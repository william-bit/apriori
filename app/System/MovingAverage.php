<?php

namespace App\System;

class MovingAverage
{
    /**
     * It takes a list of stocks and returns the average of the list
     *
     * @param array $listStock The list of stock prices.
     *
     * @return int The moving average of the list of stocks.
     */
    public function start($listStock)
    {
        return array_sum($listStock) / count($listStock);
    }
}
