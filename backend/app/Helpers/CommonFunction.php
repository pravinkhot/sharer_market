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

        return $dateList;
    }

    public static function seedHolidays ()
    {
        $holidayList = [
            [
                'date' => Carbon::createFromDate('21-Feb-2020')->toDateTimeString(),
                'description' => 'Mahashivratri',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'date' => Carbon::createFromDate('10-Mar-2020')->toDateTimeString(),
                'description' => 'Holi',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'date' => Carbon::createFromDate('02-Apr-2020')->toDateTimeString(),
                'description' => 'Ram Navami',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'date' => Carbon::createFromDate('06-Apr-2020')->toDateTimeString(),
                'description' => 'Mahavir Jayanti',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'date' => Carbon::createFromDate('10-Apr-2020')->toDateTimeString(),
                'description' => 'Good Friday',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'date' => Carbon::createFromDate('14-Apr-2020')->toDateTimeString(),
                'description' => 'Dr.Baba Saheb Ambedkar Jayanti',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'date' => Carbon::createFromDate('01-May-2020')->toDateTimeString(),
                'description' => 'Maharashtra Day',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'date' => Carbon::createFromDate('25-May-2020')->toDateTimeString(),
                'description' => 'Id-Ul-Fitr (Ramzan ID)',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'date' => Carbon::createFromDate('02-Oct-2020')->toDateTimeString(),
                'description' => 'Mahatma Gandhi Jayanti',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'date' => Carbon::createFromDate('16-Nov-2020')->toDateTimeString(),
                'description' => 'Diwali-Balipratipada',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'date' => Carbon::createFromDate('30-Nov-2020')->toDateTimeString(),
                'description' => 'Gurunanak Jayanti',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'date' => Carbon::createFromDate('25-Dec-2020')->toDateTimeString(),
                'description' => 'Christmas',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
        ];
        HolidayModel::insert($holidayList);
    }
}
