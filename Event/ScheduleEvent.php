<?php

namespace Schedules\Event;

use Thelia\Core\Event\ActionEvent;

/**
 * Class ScheduleEvent
 * @package Schedules\Event
 */
class ScheduleEvent extends ActionEvent
{
    protected $data;

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
    }
}
