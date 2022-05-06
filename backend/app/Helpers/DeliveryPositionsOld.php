<?php
namespace App\Helpers;

use App\Models\DeliveryPosition as DeliveryPositionModel;

class DeliveryPositions
{
    /**
     * @param string $date
     * @return mixed
     */
    public static function getDeliveryPositionDataByTradedAtDate(string $date)
    {
        return DeliveryPositionModel::getRecordsByTradedAtDate($date);
    }

    /**
     * @param array $intervalArray
     * @return mixed
     */
    public static function getSimpleMovingAvgByInterval(array $intervalArray)
    {
        return DeliveryPositionModel::getSimpleMovingAvgByInterval($intervalArray);
    }
}
