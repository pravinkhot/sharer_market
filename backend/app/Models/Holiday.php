<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use DB;
use Carbon\Carbon;

class Holiday extends Model
{
    private static $tableName = 'holidays';

    /**
     * @param int $year
     * @return mixed
     */
    public static function getRecordsByYear(int $year)
    {
        return DB::table(self::$tableName)
            ->whereBetween('date', [
                $year.'-01-01',
                $year.'-12-31'
            ])
            ->get();
    }
}
