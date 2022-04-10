<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class DeliveryPosition extends Model
{
    private static $tableName = 'delivery_positions';

    protected $fillable = [
        'symbol', 'series', 'traded_quantity', 'deliverable_quantity', 'deliverable_quantity_percentage', 'traded_at','open_price','high_price','low_price','close_price','last_price','prev_close_price','traded_value','total_trades','isin'
    ];

    /**
     * @param array $intervalArray
     * @return mixed
     */
    public function getDeliveryPositionDataByInterval(array $intervalArray)
    {
        return DeliveryPosition::where('delivery_positions.series', 'EQ')
            ->whereBetween('delivery_positions.traded_at', $intervalArray)
            ->get();
    }

    /**
     * @param string $date
     * @return mixed
     */
    public function getCurrentDateDeliveryPositionData(string $date)
    {
        return DeliveryPosition::select('*')
            ->where('delivery_positions.series', 'EQ')
            ->where('delivery_positions.traded_at', '=', $date)
            ->get();
    }

    /**
     * @param array $intervalArray
     * @return mixed
     */
    public static function getSimpleMovingAvgByInterval(array $intervalArray)
    {
    	return DB::table(self::$tableName.' as dp')
            ->select([
                'dp.symbol',
                DB::raw('SUM(dp.close_price) as sum_close_price')
            ])
            ->where([
                'dp.series' => 'EQ',
            ])
            ->whereBetween('dp.traded_at', $intervalArray)
            ->groupBy('dp.symbol')
            ->get();
    }

    public function getDeliveryPositionByDate(string $date)
    {
    	return DeliveryPosition::select(
                    'delivery_positions.symbol',
                    'delivery_positions.traded_quantity',
                    'delivery_positions.deliverable_quantity',
                    'delivery_positions.close_price'
                )
                ->where('delivery_positions.traded_at', '=', $date)
                ->where('delivery_positions.series', 'EQ')
                ->get();
    }

    public static function getRecordsByTradedAtDate(string $date)
    {
        return DB::table(self::$tableName.' as dp')
            ->where('dp.series', 'EQ')
            ->where('dp.traded_at', '=', $date)
            ->get();
    }
}
