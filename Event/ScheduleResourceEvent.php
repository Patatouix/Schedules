<?php

namespace Schedules\Event;

use Schedules\Event\ScheduleEvent;

/**
 * Class ScheduleResourceEvent
 * @package Schedules\Event
 */
class ScheduleResourceEvent extends ScheduleEvent
{
    const SCHEDULE_RESOURCE_EVENT = 'schedules.schedule_resource';

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
