<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/


namespace Schedules\EventListener;

use Propel\Runtime\Propel;
use Schedules\Event\CloneScheduleEvent;
use Schedules\Event\CreateScheduleEvent;
use Schedules\Event\Resource\ScheduleContentEvent;
use Schedules\Event\Resource\ScheduleProductEvent;
use Schedules\Event\SchedulesEvent;
use Schedules\Event\Resource\ScheduleStoreEvent;
use Schedules\Event\UpdateScheduleEvent;
use Schedules\Event\DeleteScheduleEvent;
use Schedules\Event\Resource\CloneScheduleResourceEvent;
use Schedules\Event\Resource\CreateScheduleResourceEvent;
use Schedules\Event\Resource\UpdateScheduleResourceEvent;
use Schedules\Model\Schedule;
use Schedules\Model\ScheduleQuery;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Class ScheduleEventListener
 * @package Schedules\EventListener
 * @author Julien Chanséaume <jchanseaume@openstudio.fr>
 */
class ScheduleEventListener implements EventSubscriberInterface
{
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function createSchedule(CreateScheduleEvent $event)
    {
        $data = $event->getData();

        $con = Propel::getConnection();
        $con->beginTransaction();

        try {
            if (empty($data["day"])) {
                $dataAM = $this->formatData($data);
                $dataPM = $this->formatData($data, 'PM');

                if ($this->hasNullDate($dataAM) && $this->hasNullDate($dataPM)) {
                    $schedule = new Schedule();
                    $this->hydrateScheduleAndDispatch($schedule, $dataAM, 'create');
                } else {
                    if (!$this->hasNullDate($dataAM)) {
                        $schedule = new Schedule();
                        $this->hydrateScheduleAndDispatch($schedule, $dataAM, 'create');
                    }
                    if (!$this->hasNullDate($dataPM)) {
                        $schedule = new Schedule();
                        $this->hydrateScheduleAndDispatch($schedule, $dataPM, 'create');
                    }
                }
            } else {
                foreach ($data["day"] as $day) {
                    $currentData = $data;
                    $currentData["day"] = $day;
                    $dataAM = $this->formatData($currentData);
                    $dataPM = $this->formatData($currentData, 'PM');

                    if ($this->hasNullDate($dataAM) && $this->hasNullDate($dataPM)) {
                        $schedule = new Schedule();
                        $this->hydrateScheduleAndDispatch($schedule, $dataAM, 'create');
                    } else {
                        if (!$this->hasNullDate($dataAM)) {
                            $schedule = new Schedule();
                            $this->hydrateScheduleAndDispatch($schedule, $dataAM, 'create');
                        }
                        if (!$this->hasNullDate($dataPM)) {
                            $schedule = new Schedule();
                            $this->hydrateScheduleAndDispatch($schedule, $dataPM, 'create');
                        }
                    }
                }
            }

            $con->commit();

        } catch (\Exception $ex) {
            // if any error happened
            $con->rollBack();
        }
    }

    public function updateSchedule(UpdateScheduleEvent $event)
    {
        $data = $event->getData();

        if (isset($data['schedule_id']) && (null != $existingSchedule = ScheduleQuery::create()->findPk($data['schedule_id']))) {

            $con = Propel::getConnection();
            $con->beginTransaction();

            try {
                $this->hydrateScheduleAndDispatch($existingSchedule, $data, 'update');
                $con->commit();
            } catch (\Exception $ex) {
                // if any error happened
                $con->rollBack();
            }
        }
    }

    public function cloneSchedule(CloneScheduleEvent $event)
    {
        $data = $event->getData();

        if (isset($data['schedule_id']) && (null != $existingSchedule = ScheduleQuery::create()->findPk($data['schedule_id']))) {

            $con = Propel::getConnection();
            $con->beginTransaction();

            $copySchedule = $existingSchedule->copy();

            try {
                $this->hydrateScheduleAndDispatch($copySchedule, $data, 'create');
                $con->commit();
            } catch (\Exception $ex) {
                // if any error happened
                $con->rollBack();
            }
        }
    }

    public function deleteSchedule(DeleteScheduleEvent $event)
    {
        $data = $event->getData();

        if (isset($data['schedule_id']) && (null != $existingSchedule = ScheduleQuery::create()->findPk($data['schedule_id']))) {

            $con = Propel::getConnection();
            $con->beginTransaction();

            try {
                $existingSchedule->delete();
                // due to CASCADE, resource schedules (productSchedule, contentSchedule...) are also deleted
                $con->commit();
            } catch (\Exception $ex) {
                // if any error happened
                $con->rollBack();
            }
        }
    }

    protected function formatData($data, $type = "AM")
    {
        $retour = $data;
        if (isset($data["begin" . $type]) && $data["begin" . $type] != "") {
            $retour["begin"] = $data["begin" . $type];
        } else {
            $retour["begin"] = null;
        }

        if (isset($data["end" . $type]) && $data["end" . $type] != "") {
            $retour["end"] = $data["end" . $type];
        } else {
            $retour["end"] = null;
        }

        return $retour;
    }

    protected function hasNullDate($data)
    {
        return !($data["begin"] && $data["end"]);
    }

    protected function hydrateScheduleAndDispatch($schedule, $data, $eventType)
    {
        $hydratedSchedule = $this->hydrateSchedule($schedule, $data);

        $eventClass = 'Schedules\Event\Resource\\' . ucfirst($eventType) . 'ScheduleResourceEvent';
        $eventConstant = strtoupper($eventType) . '_SCHEDULE_RESOURCE_EVENT';

        //dispatch event
        $event = new $eventClass();
        $event->setData($data);
        $event->setSchedule($hydratedSchedule);

        $this->dispatcher->dispatch(
            constant($eventClass . '::' . $eventConstant),
            $event
        );
    }

    protected function hydrateSchedule($schedule, $data)
    {
        if (array_key_exists('day', $data) && $data['day'] !== array()) {
            $schedule->setDay($data['day']);
        }
        if (isset($data['begin'])) {
            $schedule->setBegin($data['begin']);
        }
        if (isset($data['end'])) {
            $schedule->setEnd($data['end']);
        }
        if (isset($data['period_begin'])) {
            $schedule->setPeriodBegin($data['period_begin']);
        }
        if (isset($data['period_end'])) {
            $schedule->setPeriodEnd($data['period_end']);
        }
        if (isset($data['closed'])) {
            $schedule->setClosed($data['closed']);
        }

        return $schedule;
    }

    public static function getSubscribedEvents()
    {
        return [
            CreateScheduleEvent::CREATE_SCHEDULE_EVENT => ['createSchedule', 128],
            UpdateScheduleEvent::UPDATE_SCHEDULE_EVENT => ['updateSchedule', 128],
            CloneScheduleEvent::CLONE_SCHEDULE_EVENT => ['cloneSchedule', 128],
            DeleteScheduleEvent::DELETE_SCHEDULE_EVENT => ['deleteSchedule', 128],
        ];
    }
}