<?php

namespace Schedules\Event\Resource;

use Schedules\Event\ScheduleEvent;

/**
 * Class ScheduleResourceEvent
 * @package Schedules\Event
 */
class ScheduleResourceEvent extends ScheduleEvent
{
    protected $schedule;

    public function getSchedule()
    {
        return $this->schedule;
    }

    public function setSchedule($schedule)
    {
        $this->schedule = $schedule;
    }
}
