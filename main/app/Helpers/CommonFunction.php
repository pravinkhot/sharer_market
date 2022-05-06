<?php
namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Collection;

use App\Models\Holiday as HolidayModel;

class CommonFunction
{
    private static $holidayList;

    /**
     * @param string $date
     * @return mixed
     */
    public static function getLastWorkingDayByDate(string $date)
    {
        $inputDateObj = Carbon::parse($date);
        $holidayList = self::getHolidayListByYear($inputDateObj->format('Y'));

        if ($inputDateObj->isSunday()) {
            $inputDateObj->subDay(2);
        } elseif ($inputDateObj->isSaturday()) {
            $inputDateObj->subDay();
        }

        for($i=1; $i <= 365; $i++) {
            $isHoliday = $holidayList->where('date', $inputDateObj->toDateString())->first();
            if (!empty($isHoliday)) {
                $inputDateObj->subDay();
            } else {
                break;
            }
        }

        return $inputDateObj;
    }

    /**
     * @param string $startDate
     * @param int $interval
     * @return array
     */
    public static function getDateRangeByInterval(string $startDate, int $interval): array
    {
        $inputDateObject = Carbon::parse($startDate);

        for ($i=$interval; $i >= 1; $i--) {
            $inputDateObject = self::getLastWorkingDayByDate($inputDateObject->subDay()->toDateString());
        }

        return [
            'startDate' => $inputDateObject->toDateString(),
            'endDate' => $startDate,
        ];
    }

    /**
     * @param int $year
     * @return Collection
     */
    public static function getHolidayListByYear(int $year): Collection
    {
        if (empty(self::$holidayList)) {
            self::$holidayList = HolidayModel::getRecordsByYear($year);
        }

        return self::$holidayList;
    }
}
