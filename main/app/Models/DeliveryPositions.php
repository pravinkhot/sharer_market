<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DeliveryPositions extends Model
{
    private static $tableName = 'delivery_positions';

    /**
     * @param array $intervalArray
     * @param array $symbolList
     * @return Collection
     */
    public static function getSimpleMovingAvgByInterval(array $intervalArray, array $symbolList = []): Collection
    {
        $sql = DB::table(self::$tableName.' as dp')
            ->select([
                'dp.symbol',
                DB::raw('SUM(dp.close_price) as sum_close_price')
            ])
            ->where([
                'dp.series' => 'EQ',
            ])
            ->whereBetween('dp.traded_at', $intervalArray);

        if (!empty($symbolList)) {
            $sql = $sql->whereIn('dp.symbol', $symbolList);
        }

        return $sql->groupBy('dp.symbol')
            ->get();
    }

    /**
     * @param string $date
     * @return Collection
     */
    public static function getRecordsByTradedAtDate(string $date): Collection
    {
        return DB::table(self::$tableName.' as dp')
            ->where([
                'dp.series' => 'EQ',
                'dp.traded_at' => $date,
            ])
            ->get();
    }
}
