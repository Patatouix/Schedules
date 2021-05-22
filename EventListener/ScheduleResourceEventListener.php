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
use Schedules\Model\ContentSchedule;
use Schedules\Model\ProductSchedule;
use Schedules\Model\Schedule;
use Schedules\Model\ScheduleQuery;
use Schedules\Model\StoreSchedule;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Class ScheduleResourceEventListener
 * @package Schedules\EventListener
 */
class ScheduleResourceEventListener implements EventSubscriberInterface
{
    public function createScheduleResource(CreateScheduleResourceEvent $event)
    {
        $schedule = $event->getSchedule();
        $data = $event->getData();

        $resourceSchedule = $this->getResourceModel($data);
        $hydratedResourceSchedule = $this->hydrateResourceSchedule($resourceSchedule, $schedule, $data);
        $hydratedResourceSchedule->save();
    }

    public function updateScheduleResource(UpdateScheduleResourceEvent $event)
    {
        $schedule = $event->getSchedule();
        $data = $event->getData();

        $resourceSchedule = $this->getExistingResourceModel($schedule, $data);
        $hydratedResourceSchedule = $this->hydrateResourceSchedule($resourceSchedule, $schedule, $data);
        $hydratedResourceSchedule->save();
    }

    protected function getResourceModel($data)
    {
        switch ($data['resource_type']) {
            case 'product':
                return new ProductSchedule();
            case 'content':
                return new ContentSchedule();
            case 'store':
                return new StoreSchedule();
        }
    }

    protected function getExistingResourceModel($schedule, $data)
    {
        switch ($data['resource_type']) {
            case 'product':
                return $schedule->getProductSchedule();
            case 'content':
                return $schedule->getContentSchedule();
            case 'store':
                return $schedule->getStoreSchedule();
        }
    }

    protected function hydrateResourceSchedule($resourceSchedule, $schedule, $data)
    {
        $resourceSchedule->setSchedule($schedule);

        if ($data['resource_type'] == 'product') {
            $resourceSchedule->setProductId($data['resource_id']);
            $resourceSchedule->setStock($data['stock']);
        }
        else if($data['resource_type'] == 'content') {
            $resourceSchedule->setContentId($data['resource_id']);
        }

        return $resourceSchedule;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return [
            CreateScheduleResourceEvent::CREATE_SCHEDULE_RESOURCE_EVENT => ['createScheduleResource', 128],
            UpdateScheduleResourceEvent::UPDATE_SCHEDULE_RESOURCE_EVENT => ['updateScheduleResource', 128],
        ];
    }
}