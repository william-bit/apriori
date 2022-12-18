<?php

namespace App\System;

use Illuminate\Database\Eloquent\Collection;

class MovingAverage
{
    /**
     * It takes a list of stocks and returns the average of the list
     *
     * @param Collection $listStock The list of stock prices.
     *
     * @return array The moving average of the list of stocks.
     */
    public function start($listStock, $perMonth = 4)
    {
        $arr = $listStock->toArray();
        $arr = $this->reformData($arr);
        foreach ($arr as $productId => &$stock) {
            foreach ($stock as $month => $item) {
                $movingAverage = $this->makeMovingData($stock, $month);
                $moving = array_slice($movingAverage, 0, $perMonth, true);
                if (count($moving) == $perMonth && $perMonth) {
                    $stock[$month]['moving'] =  $moving;
                    $stock[$month]['moving_average'] = array_sum($moving) / count($moving);
                }
            }
            $stock[$month + 1] = $this->makeLastMonthPrediction($month, $stock, $perMonth, $productId);
        }
        unset($stock);
        return $arr;
    }
    public function makeLastMonthPrediction($month, $stock, $perMonth, $productId)
    {
        $movingAverage = $this->makeMovingData($stock, $month + 1);
        $moving = array_slice($movingAverage, 0, $perMonth, true);
        $newStock = [];
        $newStock['moving'] =  $moving;
        $newStock['product_sum'] = 0;
        $newStock['month'] =  $month + 1;
        $newStock['product_id'] =  $productId;
        $newStock['moving_average'] = array_sum($moving) / count($moving);
        return $newStock;
    }
    public function makeMovingData($arr, $month)
    {
        $movingAverage = [];
        foreach (array_reverse($arr, true) as $monthStock => $stock) {
            if ($month > $monthStock) {
                $movingAverage[$stock['month']] = $stock['product_sum'];
            }
        }
        return $movingAverage;
    }
    public function reformData($arr)
    {
        $newArr = [];
        foreach ($arr as $value) {
            $newArr[$value['product_id']][$value['month']] = $value;
        }
        return $newArr;
    }
    public function getData()
    {
    }
}
