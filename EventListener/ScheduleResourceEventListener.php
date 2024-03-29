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
use Schedules\Event\ScheduleResourceEvent;
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
    public function processScheduleResource(ScheduleResourceEvent $event)
    {
        $schedule = $event->getSchedule();
        $data = $event->getData();

        $resourceSchedule = $this->getResourceModel($schedule, $data);
        $hydratedResourceSchedule = $this->hydrateResourceSchedule($resourceSchedule, $schedule, $data);
        $hydratedResourceSchedule->save();
    }

    protected function getResourceModel($schedule, $data)
    {
        switch ($data['resource_type']) {
            case 'product':
                return null !== $schedule->getProductSchedule() ? $schedule->getProductSchedule() : new ProductSchedule();
            case 'content':
                return null !== $schedule->getContentSchedule() ? $schedule->getContentSchedule() : new ContentSchedule();
            case 'store':
                return null !== $schedule->getStoreSchedule() ? $schedule->getStoreSchedule() : new StoreSchedule();
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
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return [
            ScheduleResourceEvent::SCHEDULE_RESOURCE_EVENT => ['processScheduleResource', 128],
        ];
    }
}