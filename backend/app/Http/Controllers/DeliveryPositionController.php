<?php
namespace App\Http\Controllers;

use DB;
use Carbon\Carbon;
use App\Models\DeliveryPosition as DeliveryPositionModel;

class DeliveryPositionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function getAvgDeliveryPerDay()
    {
        $movingAvgData = $this->calculateMovingAvg();

        $currentDeliveryPositionData = DeliveryPositionModel::select(
                    'delivery_positions.symbol',
                    'delivery_positions.traded_quantity',
                    'delivery_positions.deliverable_quantity',
                    'delivery_positions.close_price'
                )
                ->where('delivery_positions.traded_at', '=', '2019-10-10


                ')
                ->where('delivery_positions.series', 'EQ')
                ->get();

        $finalStockList = [];
        foreach ($currentDeliveryPositionData as $currentDeliveryPositionValue) {
            $symbol = $currentDeliveryPositionValue->symbol;
            if (
                isset($movingAvgData[$symbol.'_20']['avg_close_price'])
            ) {
                if (
                    ($movingAvgData[$symbol.'_20']['avg_close_price'] <= $currentDeliveryPositionValue->close_price)
                ) {
                    if (
                        ($movingAvgData[$symbol.'_20']['avg_traded_quantity'] <= $currentDeliveryPositionValue->traded_quantity)
                    ) {
                        $deliverable_quantity_percentage_20 = ($movingAvgData[$symbol.'_20']['avg_deliverable_quantity']/$movingAvgData[$symbol.'_20']['avg_traded_quantity'])*100;
                        $current_deliverable_quantity_percentage = ($currentDeliveryPositionValue->deliverable_quantity/$currentDeliveryPositionValue->traded_quantity)*100;

                        if (
                            ($deliverable_quantity_percentage_20 <= $current_deliverable_quantity_percentage)
                        ) {
                            $finalStockList[$symbol] = [
                                'price' => [
                                    20 => $movingAvgData[$symbol.'_20']['avg_close_price'],
                                    'current' => $currentDeliveryPositionValue->close_price
                                ],
                                'traded_quantity' => [
                                    20 => $movingAvgData[$symbol.'_20']['avg_traded_quantity'],
                                    'current' => $currentDeliveryPositionValue->traded_quantity
                                ],
                                'deliverable_quantity_percentage' => [
                                    20 => $deliverable_quantity_percentage_20,
                                    'current' => $current_deliverable_quantity_percentage
                                ],
                            ];
                        }
                    }

                }
            }
        }
        dd($finalStockList);
    }

    private function calculateMovingAvg()
    {
        $intervalArray = [
            20
        ];
        $result = [];
        foreach ($intervalArray as $intervalValue) {
            $interval = $this->getDateRangeByInterval('2019-10-10', $intervalValue);
            $movingAvgData = DeliveryPositionModel::select(
                        'delivery_positions.symbol',
                        DB::raw('AVG(delivery_positions.traded_quantity) as avg_traded_quantity'),
                        DB::raw('AVG(delivery_positions.deliverable_quantity) as avg_deliverable_quantity'),
                        DB::raw('AVG(delivery_positions.close_price) as avg_close_price')
                    )
                    ->where('delivery_positions.series', 'EQ')
                    ->whereBetween('delivery_positions.traded_at', $interval)
                    ->groupBy('delivery_positions.symbol')
                    ->get();

            foreach ($movingAvgData as $key => $value) {
                $result[$value->symbol.'_'.$intervalValue] = [
                    'symbol' => $value->symbol,
                    'avg_traded_quantity' => $value->avg_traded_quantity,
                    'avg_deliverable_quantity' => $value->avg_deliverable_quantity,
                    'avg_close_price' => $value->avg_close_price,
                ];
            }
        }
        return $result;
    }
}
