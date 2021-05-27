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
use Schedules\Event\ScheduleDateEvent;
use Schedules\Event\ScheduleEvent;
use Schedules\Event\ScheduleResourceEvent;
use Schedules\Model\Event\ProductScheduleDateEvent;
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

    public function processSchedule(ScheduleEvent $event)
    {
        $data = $event->getData();
        $action = $event->getAction();

        $con = Propel::getConnection();
        $con->beginTransaction();

        try {
            switch ($action) {
                case 'create':
                    $this->createSchedule($data);
                    break;
                case 'update':
                    $this->updateSchedule($data);
                    break;
                case 'clone':
                    $this->cloneSchedule($data);
                    break;
                case 'delete':
                    $this->deleteSchedule($data);
                    break;
                default:
                    break;
            }
            $con->commit();
        } catch (\Exception $ex) {
            // if any error happened, rollback transaction
            $con->rollBack();
        }
    }

    public function createSchedule($data)
    {
        if (empty($data["day"])) {
            $dataAM = $this->formatData($data);
            $dataPM = $this->formatData($data, 'PM');

            if ($this->hasNullDate($dataAM) && $this->hasNullDate($dataPM)) {
                $this->hydrateScheduleAndDispatch(new Schedule(), $dataAM);
            } else {
                if (!$this->hasNullDate($dataAM)) $this->hydrateScheduleAndDispatch(new Schedule(), $dataAM);
                if (!$this->hasNullDate($dataPM)) $this->hydrateScheduleAndDispatch(new Schedule(), $dataPM);
            }
        } else {
            foreach ($data["day"] as $day) {
                $currentData = $data;
                $currentData["day"] = $day;
                $dataAM = $this->formatData($currentData);
                $dataPM = $this->formatData($currentData, 'PM');

                if ($this->hasNullDate($dataAM) && $this->hasNullDate($dataPM)) {
                    $this->hydrateScheduleAndDispatch(new Schedule(), $dataAM);
                } else {
                    if (!$this->hasNullDate($dataAM)) $this->hydrateScheduleAndDispatch(new Schedule(), $dataAM);
                    if (!$this->hasNullDate($dataPM)) $this->hydrateScheduleAndDispatch(new Schedule(), $dataPM);
                }
            }
        }
    }

    public function updateSchedule($data)
    {
        if (isset($data['schedule_id']) && (null != $existingSchedule = ScheduleQuery::create()->findPk($data['schedule_id']))) {
            $this->hydrateScheduleAndDispatch($existingSchedule, $data);
        }
    }

    public function cloneSchedule($data)
    {
        if (isset($data['schedule_id']) && (null != $existingSchedule = ScheduleQuery::create()->findPk($data['schedule_id']))) {
            $copySchedule = $existingSchedule->copy();
            $this->hydrateScheduleAndDispatch($copySchedule, $data);
        }
    }

    public function deleteSchedule($data)
    {
        if (isset($data['schedule_id']) && (null != $existingSchedule = ScheduleQuery::create()->findPk($data['schedule_id']))) {
            $existingSchedule->delete();
            // also delete associated schedule resources + schedule dates (due to CASCADE)
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

    protected function hydrateScheduleAndDispatch($schedule, $data)
    {
        $hydratedSchedule = $this->hydrateSchedule($schedule, $data);

        // dispatch resource event that process schedule resource (ProductSchedule, ContentSchedule, StoreSchedule)
        $event = new ScheduleResourceEvent();
        $event->setData($data);
        $event->setSchedule($hydratedSchedule);

        $this->dispatcher->dispatch(
            ScheduleResourceEvent::SCHEDULE_RESOURCE_EVENT,
            $event
        );

        // if our schedule resource is a product, process ScheduleProductDate
        if ('product' === $data['resource_type']) {
            $event = new ScheduleDateEvent();
            $event->setSchedule($hydratedSchedule);

            $this->dispatcher->dispatch(
                ScheduleDateEvent::SCHEDULE_DATE_EVENT,
                $event
            );
        }
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
            ScheduleEvent::SCHEDULE_EVENT => ['processSchedule', 128],
        ];
    }
}