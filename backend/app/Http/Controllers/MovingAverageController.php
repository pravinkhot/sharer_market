<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Helpers\CommonFunction;
use App\Models\DeliveryPosition as DeliveryPositionModel;

class MovingAverageController extends Controller
{
    public function getStockListBySingleMovingAvg(Request $request, int $interval)
    {
        $allowedIntervalList = [50];
        if (in_array($interval, $allowedIntervalList)) {
            $intervalArray = CommonFunction::getDateRangeByInterval('2020-01-08', $interval);
            $movingAvgData = $this->calculateMovingAvg($intervalArray);
            $deliveryPositionModelObject = new DeliveryPositionModel();
            $currentDeliveryPositionData = $deliveryPositionModelObject->getDeliveryPositionByDate('2020-01-08');

            $finalStockList = [];
            foreach ($currentDeliveryPositionData as $currentDeliveryPositionValue) {
                $symbol = $currentDeliveryPositionValue->symbol;
                if (isset($movingAvgData[$symbol]['avg_close_price'])) {
                    $avg_deliverable_quantity_percentage = ($movingAvgData[$symbol]['avg_deliverable_quantity']/$movingAvgData[$symbol]['avg_traded_quantity'])*100;
                    $current_deliverable_quantity_percentage = ($currentDeliveryPositionValue->deliverable_quantity/$currentDeliveryPositionValue->traded_quantity)*100;
                    if (
                        $movingAvgData[$symbol]['avg_close_price'] <= $currentDeliveryPositionValue->close_price &&
                        $movingAvgData[$symbol]['avg_traded_quantity'] <= $currentDeliveryPositionValue->traded_quantity &&
                        $avg_deliverable_quantity_percentage <= $current_deliverable_quantity_percentage
                    ) {
                        $finalStockList[$symbol] = [
                            'price' => [
                                'avg_close_price' => $movingAvgData[$symbol]['avg_close_price'],
                                'current_close_price' => $currentDeliveryPositionValue->close_price
                            ],
                            'traded_quantity' => [
                                'avg_traded_quantity' => $movingAvgData[$symbol]['avg_traded_quantity'],
                                'current_traded_quantity' => $currentDeliveryPositionValue->traded_quantity
                            ],
                            'deliverable_quantity_percentage' => [
                                'avg_deliverable_quantity_percentage' => $avg_deliverable_quantity_percentage,
                                'current_deliverable_quantity_percentage' => $current_deliverable_quantity_percentage
                            ],
                        ];
                    }
                }
            }
            dd($finalStockList);
        }








        // $finalStockList = [];
        // foreach ($currentDeliveryPositionData as $currentDeliveryPositionValue) {
        //     $symbol = $currentDeliveryPositionValue->symbol;



        //         }
        //     }
        // }

        $currentDeliveryPositionData = DeliveryPositionModel::select(
                    'delivery_positions.symbol',
                    'delivery_positions.traded_quantity',
                    'delivery_positions.deliverable_quantity',
                    'delivery_positions.close_price'
                )
                ->where('delivery_positions.traded_at', '=', '2020-01-08')
                ->where('delivery_positions.series', 'EQ')
                ->get();
        
    }

    private function calculateMovingAvg(array $intervalArray):array
    {
        $deliveryPositionModelObject = new DeliveryPositionModel();
        $data = $deliveryPositionModelObject->getSimpleMovingAvgByInterval($intervalArray);
        $result = [];
        foreach ($data as $key => $value) {
            $result[$value->symbol] = [
                'symbol' => $value->symbol,
                'avg_traded_quantity' => $value->avg_traded_quantity,
                'avg_deliverable_quantity' => $value->avg_deliverable_quantity,
                'avg_close_price' => $value->avg_close_price,
            ];
        }
        return $result;
    }
}
