<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class DeliveryPosition extends Model
{
    protected $fillable = [
        'symbol', 'series', 'traded_quantity', 'deliverable_quantity', 'deliverable_quantity_percentage', 'traded_at','open_price','high_price','low_price','close_price','last_price','prev_close_price','traded_value','total_trades','isin'
    ];

    /**
     * @param array $intervalArray
     * @return mixed
     */
    public function getDeliveryPositionDataByInterval(array $intervalArray)
    {
        return DeliveryPosition::select('*')
            ->where('delivery_positions.series', 'EQ')
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

    public function getSimpleMovingAvgByInterval(array $intervalArray)
    {
    	return DeliveryPosition::select(
                        'delivery_positions.symbol',
                        DB::raw('SUM(delivery_positions.traded_quantity) as avg_traded_quantity'),
                        DB::raw('SUM(delivery_positions.deliverable_quantity) as avg_deliverable_quantity'),
                        DB::raw('AVG(delivery_positions.close_price) as avg_close_price')
                    )
                    ->where('delivery_positions.series', 'EQ')
                    ->where('delivery_positions.symbol', 'BANKBARODA')
                    ->whereBetween('delivery_positions.traded_at', $intervalArray)
                    ->groupBy('delivery_positions.symbol')
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
}
