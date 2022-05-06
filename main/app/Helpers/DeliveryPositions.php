<?php
namespace App\Helpers;

use Illuminate\Support\Collection;

use App\Models\DeliveryPositions as DeliveryPositionsModel;

class DeliveryPositions
{
    /**
     * @param string $date
     * @return mixed
     */
    public static function getDeliveryPositionDataByTradedAtDate(string $date)
    {
        return DeliveryPositionsModel::getRecordsByTradedAtDate($date);
    }

    /**
     * @param array $intervalArray
     * @param array $symbolList
     * @return Collection
     */
    public static function getSimpleMovingAvgByInterval(array $intervalArray, array $symbolList = []): Collection
    {
        return DeliveryPositionsModel::getSimpleMovingAvgByInterval($intervalArray, $symbolList);
    }
}
