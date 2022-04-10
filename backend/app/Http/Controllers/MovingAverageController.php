<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Helpers\CommonFunction;
use App\Helpers\DeliveryPositions as DeliveryPositionsHelper;
use App\Models\DeliveryPosition as DeliveryPositionModel;

class MovingAverageController extends Controller
{
    /**
     * @param Request $request
     */
    public function getSuperBreakoutStockList(Request $request)
    {
        $startDate = Carbon::now()->toDateString();
        $lastWorkingDay = CommonFunction::getLastWorkingDayByDate($startDate);
        $currentDateDpData = DeliveryPositionsHelper::getDeliveryPositionDataByTradedAtDate($lastWorkingDay->toDateString());


        $smaIntervalList = [5, 10, 15, 50, 100, 200,];
        foreach ($smaIntervalList as $interval) {
            $dateList = CommonFunction::getDateRangeByInterval($startDate, $interval);

            $movingAvgData = DeliveryPositionsHelper::getSimpleMovingAvgByInterval($dateList);


            echo '<pre>'; print_r($movingAvgData); echo '</pre>'; exit;
        }

        dd($startDate, 'hi');





        $deliveryPositionModelObject = new DeliveryPositionModel();
        $currentDateDeliveryPositionData = $deliveryPositionModelObject->getCurrentDateDeliveryPositionData($startDate);

        $prevCloseData = [];
        foreach ($currentDateDeliveryPositionData as $deliveryPosition) {
            $prevCloseData[$deliveryPosition->symbol] = [
                'close_price' => $deliveryPosition->close_price
            ];
        }

        $resultArray = [];
        $superBreakoutStockList = [];
        $finalResultArray = [];
        foreach ($smaIntervalList as $interval) {
            $dateList = CommonFunction::getDateRangeByInterval($startDate, $interval);
            dd($dateList, $interval);

            $intervalArray = [
                'startDate' => end($dateList),
                'endDate' => reset($dateList)
            ];

            $movingAvgData = $deliveryPositionModelObject->getSimpleMovingAvgByInterval($intervalArray);

            foreach ($movingAvgData as $data) {
                $finalResultArray[$interval] = [
                    'closePrice' => $data->avg_close_price,
                    'deliveryPercentage' => $data->avg_deliverable_quantity*100/$data->avg_traded_quantity
                ];
                // if (200 === $interval && isset($prevCloseData[$data->symbol])) {
                //     $prevClosePrice = $prevCloseData[$data->symbol]['close_price'];

                //     if (isset($superBreakoutStockList[$data->symbol]) && 7 === count($superBreakoutStockList[$data->symbol]) &&
                //         ($data->avg_close_price >= $prevClosePrice)) {
                //         $lowPoint = (95*$data->avg_close_price)/100;

                //         if (($prevClosePrice >= $lowPoint) && ($prevClosePrice <= $data->avg_close_price)) {
                //             $finalResultArray[$data->symbol] = array_merge(
                //                 $superBreakoutStockList[$data->symbol],
                //                 [
                //                     $interval.'_days_avg_close_price' => $data->avg_close_price
                //                 ]
                //             );
                //         }
                //     }
                // }

                // if (isset($prevCloseData[$data->symbol]) &&
                //     ($prevCloseData[$data->symbol]['close_price'] > $data->avg_close_price)) {
                //     $superBreakoutStockList[$data->symbol][$interval.'_days_avg_close_price'] = $data->avg_close_price;

                //     $tempData[$data->symbol] = [
                //         'avg_close_price' => $data->avg_close_price,
                //     ];

                //     if (isset($superBreakoutStockList[$data->symbol])) {
                //         $superBreakoutStockList[$data->symbol][$interval.'_days_avg_close_price'] = $data->avg_close_price;
                //         $superBreakoutStockList[$data->symbol]['close_price'] = $data->avg_close_price;
                //     } else {
                //         $superBreakoutStockList[$data->symbol][$interval.'_days_avg_close_price'] = $data->avg_close_price;
                //     }
                // }
            }

            $resultArray[$interval] = $tempData ?? [];
        }

        echo '<pre>'; dd($finalResultArray);
        exit;
    }

    public function getStockListByVolAndDP()
    {
        $startDate = '2020-11-07';

        $dateList = CommonFunction::getDateRangeByInterval($startDate, 3);

        $intervalArray = [
            'startDate' => end($dateList),
            'endDate' => reset($dateList)
        ];

        $deliveryPositionModelObject = new DeliveryPositionModel();
        $deliveryPositionData = $deliveryPositionModelObject->getDeliveryPositionDataByInterval($intervalArray);

        $data = [];
        foreach ($deliveryPositionData as $deliveryPositionKey => $deliveryPosition) {
            $data[$deliveryPosition->symbol][$deliveryPosition->traded_at] = [
                'deliverable_quantity_percentage' => $deliveryPosition->deliverable_quantity_percentage,
                'close_price' => $deliveryPosition->close_price,
                'traded_at' => $deliveryPosition->traded_at,
                'traded_quantity' => $deliveryPosition->traded_quantity,
            ];
        }

        $result = [];
        foreach ($data as $key => $value) {
            if (!empty($value[$dateList[2]]) && !empty($value[$dateList[1]]) && !empty($value[$dateList[0]])) {
                if (($value[$dateList[0]]['close_price'] > $value[$dateList[1]]['close_price']) &&
                ($value[$dateList[1]]['close_price'] > $value[$dateList[2]]['close_price'])) {
                    if (($value[$dateList[0]]['deliverable_quantity_percentage'] > $value[$dateList[1]]['deliverable_quantity_percentage']) &&
                        ($value[$dateList[1]]['deliverable_quantity_percentage'] > $value[$dateList[2]]['deliverable_quantity_percentage'])) {
                        if (($value[$dateList[0]]['traded_quantity'] > $value[$dateList[1]]['traded_quantity']) &&
                            ($value[$dateList[1]]['traded_quantity'] > $value[$dateList[2]]['traded_quantity'])) {
                            $result[$key] = $value;
                        }
                    }
                }
            }
        }

        dd($result);
    }

    public function getStockListBySingleMovingAvg(Request $request, int $interval)
    {
        $allowedIntervalList = [50];
        if (in_array($interval, $allowedIntervalList)) {
            $intervalArray = CommonFunction::getDateRangeByInterval('2020-11-03', $interval);
            $movingAvgData = $this->calculateMovingAvg($intervalArray);
            $deliveryPositionModelObject = new DeliveryPositionModel();
            $currentDeliveryPositionData = $deliveryPositionModelObject->getDeliveryPositionByDate('2020-11-03');

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
