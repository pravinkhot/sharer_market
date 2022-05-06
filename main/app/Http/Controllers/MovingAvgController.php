<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Helpers\CommonFunction as CommonFunctionHelper;
use App\Helpers\DeliveryPositions as DeliveryPositionsHelper;

class MovingAvgController extends Controller
{
    public function getSuperBreakoutStockList()
    {
        $startDate = Carbon::now()->toDateString();
        $lastWorkingDay = CommonFunctionHelper::getLastWorkingDayByDate($startDate);
        $currentDateDpData = DeliveryPositionsHelper::getDeliveryPositionDataByTradedAtDate($lastWorkingDay->toDateString());

        $smaIntervalList = [5, 10, 15, 20, 50, 100, 200,];
        $finalStockList = [];

        foreach ($smaIntervalList as $key => $interval) {
            $dateList = CommonFunctionHelper::getDateRangeByInterval($startDate, $interval);
            $movingAvgData = DeliveryPositionsHelper::getSimpleMovingAvgByInterval($dateList, array_keys($finalStockList));

            foreach ($movingAvgData as $movingAvg) {
                $symbol = $movingAvg->symbol;
                $currentData = $currentDateDpData->where('symbol', $symbol)->first();

                if (!empty($currentData)) {
                    $closePrice = $currentData->close_price;
                    $avgPrice = round($movingAvg->sum_close_price/$interval, 2);

                    $unsetFlag = true;
                    if (200 === $interval) {
                        $perPerAvgPrice =  round(($avgPrice*1)/100, 2);
                        $diffPercentage = round((($closePrice-$avgPrice)*100)/$avgPrice, 2);

                        if ((($avgPrice-($perPerAvgPrice*3)) < $closePrice) &&
                            (($avgPrice+$perPerAvgPrice) > $closePrice)
                        ) {
                            $unsetFlag = false;
                            $finalStockList[$symbol]['diffPercentage'] = $diffPercentage;
                        }
                    } elseif ($avgPrice < $closePrice) {
                        $unsetFlag = false;
                    }

                    if (false === $unsetFlag) {
                        $finalStockList[$symbol]['symbol'] = $symbol;
                        $finalStockList[$symbol]['cmp'] = $closePrice;
                        $finalStockList[$symbol]['sma_'.$interval] = $avgPrice;
                    } else {
                        unset($finalStockList[$symbol]);
                    }
                }
            }
        }

        return view('super_breakout_screener', [
            'stockList' => collect($finalStockList)->sortByDesc('diffPercentage'),
        ]);
    }
}
