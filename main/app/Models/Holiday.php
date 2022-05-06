<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Holiday extends Model
{
    private static $tableName = 'holidays';

    /**
     * @param int $year
     * @return Collection
     */
    public static function getRecordsByYear(int $year): Collection
    {
        return DB::table(self::$tableName)
            ->whereBetween('date', [
                $year.'-01-01',
                $year.'-12-31'
            ])
            ->get();
    }
}
