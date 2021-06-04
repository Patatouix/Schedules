<?php

namespace Schedules\Event;

use Schedules\Event\ScheduleEvent;
use Thelia\Core\Event\ActionEvent;

/**
 * Class ScheduleResourceEvent
 * @package Schedules\Event
 */
class ScheduleDateStockEvent extends ActionEvent
{
    const SCHEDULE_DATE_STOCK_EVENT = 'schedules.schedule_date_stock';

    protected $schedule_date;
    protected $remainingStock;

    public function getScheduleDate()
    {
        return $this->schedule_date;
    }

    public function setScheduleDate($schedule_date)
    {
        $this->schedule_date = $schedule_date;
    }

    public function getRemainingStock()
    {
        return $this->remainingStock;
    }

    public function setRemainingStock($remainingStock)
    {
        $this->remainingStock = $remainingStock;
    }
}
