<?php

namespace Schedules\Event;

use Schedules\Event\ScheduleEvent;

/**
 * Class ScheduleResourceEvent
 * @package Schedules\Event
 */
class ScheduleDateEvent extends ScheduleEvent
{
    const SCHEDULE_DATE_EVENT = 'schedules.schedule_date';

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
