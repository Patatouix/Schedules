<?php

namespace Schedules\Model;

use Schedules\Model\Base\ScheduleDate as BaseScheduleDate;

/**
 * Skeleton subclass for representing a row from the 'schedule_date' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class ScheduleDate extends BaseScheduleDate
{
    /**
     * Returns the remaining stock of a schedule date
     *
     * @return Integer
     */
    public function getRemainingStock()
    {
        $consumedStock = 0;
        foreach ($this->getOrderProductScheduleDates() as $orderProductScheduleDate) {
            $orderProduct = $orderProductScheduleDate->getOrderProduct();
            $order = $orderProduct->getOrder();
            if ($order->getPaymentModuleInstance()->manageStockOnCreation()) {
                $consumedStock += $orderProduct->getQuantity();
            } else {
                if ($orderProduct->getOrder()->isPaid()) {
                    $consumedStock += $orderProduct->getQuantity();
                }
            }
        }
        return ($this->getStock() - $consumedStock);
    }
}
