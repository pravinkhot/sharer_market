<?php
namespace App\Helpers;

use Carbon\Carbon;
use App\Models\Holiday as HolidayModel;

class CommonFunction
{
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
        $inputDateObject = self::getLastWorkingDayByDate($inputDateObject->subDay()->toDateString());

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
     * @return mixed
     */
    public static function getHolidayListByYear(int $year)
    {
        return HolidayModel::getRecordsByYear($year);
    }

    private static function seedHolidays()
    {
        $holidayList = [
            [
                'date' => Carbon::createFromDate('21-Feb-2020')->toDateTimeString(),
                'description' => 'Mahashivratri',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('10-Mar-2020')->toDateTimeString(),
                'description' => 'Holi',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('02-Apr-2020')->toDateTimeString(),
                'description' => 'Ram Navami',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('06-Apr-2020')->toDateTimeString(),
                'description' => 'Mahavir Jayanti',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('10-Apr-2020')->toDateTimeString(),
                'description' => 'Good Friday',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('14-Apr-2020')->toDateTimeString(),
                'description' => 'Dr.Baba Saheb Ambedkar Jayanti',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('01-May-2020')->toDateTimeString(),
                'description' => 'Maharashtra Day',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('25-May-2020')->toDateTimeString(),
                'description' => 'Id-Ul-Fitr (Ramzan ID)',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('02-Oct-2020')->toDateTimeString(),
                'description' => 'Mahatma Gandhi Jayanti',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('16-Nov-2020')->toDateTimeString(),
                'description' => 'Diwali-Balipratipada',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('30-Nov-2020')->toDateTimeString(),
                'description' => 'Gurunanak Jayanti',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('25-Dec-2020')->toDateTimeString(),
                'description' => 'Christmas',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('26-Jan-2021')->toDateTimeString(),
                'description' => 'Republic Day',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('11-Mar-2021')->toDateTimeString(),
                'description' => 'Mahashivratri',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('29-Mar-2021')->toDateTimeString(),
                'description' => 'Holi',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('02-Apr-2021')->toDateTimeString(),
                'description' => 'Good Friday',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('14-Apr-2021')->toDateTimeString(),
                'description' => 'Dr.Baba Saheb Ambedkar Jayanti',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('21-Apr-2021')->toDateTimeString(),
                'description' => 'Ram Navami',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('13-May-2021')->toDateTimeString(),
                'description' => 'Id-Ul-Fitr (Ramzan ID)',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('21-Jul-2021')->toDateTimeString(),
                'description' => 'Bakri Id',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('19-Aug-2021')->toDateTimeString(),
                'description' => 'Muharram',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('10-Sep-2021')->toDateTimeString(),
                'description' => 'Ganesh Chaturthi',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('15-Oct-2021')->toDateTimeString(),
                'description' => 'Dussehra',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('04-Nov-2021')->toDateTimeString(),
                'description' => 'Diwali * Laxmi Pujan',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('05-Nov-2021')->toDateTimeString(),
                'description' => 'Diwali Balipratipada',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('19-Nov-2021')->toDateTimeString(),
                'description' => 'Gurunanak Jayanti',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('26-Jan-2022')->toDateTimeString(),
                'description' => 'Republic Day',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('01-Mar-2022')->toDateTimeString(),
                'description' => 'Mahashivratri',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('18-Mar-2022')->toDateTimeString(),
                'description' => 'Holi',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('14-Apr-2022')->toDateTimeString(),
                'description' => 'Dr.Baba Saheb Ambedkar Jayanti',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('15-Apr-2022')->toDateTimeString(),
                'description' => 'Good Friday',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('03-May-2022')->toDateTimeString(),
                'description' => 'Id-Ul-Fitr (Ramzan ID)',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('09-Aug-2022')->toDateTimeString(),
                'description' => 'Muharram',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('15-Aug-2022')->toDateTimeString(),
                'description' => 'Independence Day',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('31-Aug-2022')->toDateTimeString(),
                'description' => 'Ganesh Chaturthi',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('05-Oct-2022')->toDateTimeString(),
                'description' => 'Dussehra',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('24-Oct-2022')->toDateTimeString(),
                'description' => 'Diwali * Laxmi Pujan',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('26-Oct-2022')->toDateTimeString(),
                'description' => 'Diwali Balipratipada',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ], [
                'date' => Carbon::createFromDate('08-Nov-2022')->toDateTimeString(),
                'description' => 'Gurunanak Jayanti',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
        ];
        HolidayModel::insert($holidayList);
    }
}
