<?php

namespace App\System;

class EOQ
{
    /**
     * This function returns the square root of the product of two times the booking fee, the sum of
     * the ingredients, and the cost, divided by the upkeep and the cost.
     *
     * @param int bookingFee The amount of money you make per booking.
     * @param int sumOfIngredient The sum of all the ingredients in the recipe.
     * @param int cost the cost of the ingredients for the recipe
     * @param int upkeep The cost of maintaining the kitchen for a day.
     *
     * @return int The square root of the sum of the booking fee, sum of the ingredients, upkeep, and cost.
     */
    public function start($bookingFee, $sumOfIngredient, $cost, $upkeep)
    {
        return sqrt((2 * $bookingFee * $sumOfIngredient) / $upkeep * $cost);
    }
}
