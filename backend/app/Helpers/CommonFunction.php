<?php
namespace App\Helpers;

use Carbon\Carbon;
use App\Models\Holiday as HolidayModel;

class CommonFunction
{
    public static function getDateRangeByInterval(string $startDate,int $interval): array
    {
        if (!empty($startDate)) {
            $startDateObject = Carbon::parse($startDate);
        } else {
            $startDateObject = Carbon::now();
        }

        $holidayModel = new HolidayModel();
        $holidayList = $holidayModel->getList($startDateObject);

        for ($i=$interval; $i >= 1; $i--) {
            $startDateObject->subDay();
            if ($startDateObject->isSunday()) {
                $startDateObject->subDay(2); 
            } elseif ($startDateObject->isSaturday()) {
                $startDateObject->subDay();
            } elseif (in_array($startDateObject->toDateString(), $holidayList)) {
                $startDateObject->subDay();
            }
            $dateList[] = $startDateObject->toDateString();
        }
        return [
            'startDate' => end($dateList),
            'endDate' => reset($dateList)
        ];
    }
}
