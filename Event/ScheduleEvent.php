<?php

namespace Schedules\Event;

use Thelia\Core\Event\ActionEvent;

/**
 * Class ScheduleEvent
 * @package Schedules\Event
 */
class ScheduleEvent extends ActionEvent
{
    const SCHEDULE_EVENT = 'schedules.schedule';

    protected $data;
    protected $action;

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function setAction($action)
    {
        $this->action = $action;
    }
}
