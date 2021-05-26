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

use DateInterval;
use DatePeriod;
use DateTime;
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
use Schedules\Event\ScheduleResourceEvent;
use Schedules\Model\ContentSchedule;
use Schedules\Model\ProductSchedule;
use Schedules\Model\ProductScheduleDate;
use Schedules\Model\Schedule;
use Schedules\Model\ScheduleQuery;
use Schedules\Model\StoreSchedule;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\VarDumper\Cloner\Data;

/**
 * Class ScheduleDateEventListener
 * @package Schedules\EventListener
 */
class ScheduleDateEventListener implements EventSubscriberInterface
{
    // create all dates associated to a schedule
    public function processScheduleDate(ScheduleDateEvent $event)
    {
        $schedule = $event->getSchedule();
        $day = $schedule->getDay();
        $periodBegin = $schedule->getPeriodBegin();
        $periodEnd = $schedule->getPeriodEnd();
        $begin = $schedule->getBegin();
        $end = $schedule->getEnd();
        $closed = $schedule->getClosed();

        $productSchedule = $schedule->getProductSchedule();
        $stock = $productSchedule->getStock();
        $productId = $productSchedule->getProductId();

        if (null !== $day) {
            //setTime() is a trick to get last day of DatePeriod
            $period = new DatePeriod($periodBegin, new DateInterval('P1D'), $periodEnd->setTime(0,0,1));

            // iterate on all day of period
            foreach($period as $dayPeriod) {

                if ((int)$dayPeriod->format('w') === (int)($day + 1)) {

                    $date = new ProductScheduleDate();
                    $date->setDateBegin($dayPeriod)
                        ->setDateEnd($dayPeriod)
                        ->setStock($stock)
                        ->setClosed($closed)
                        ->setProductId($productId)
                        ->setTimeBegin($begin)
                        ->setTimeEnd($end)
                        ->save()
                    ;
                }
            }
        } else {
            $date = new ProductScheduleDate();
            $date->setDateBegin($periodBegin)
                ->setDateEnd($periodEnd)
                ->setStock($stock)
                ->setClosed($closed)
                ->setProductId($productId)
                ->setTimeBegin($begin)
                ->setTimeEnd($end)
                ->save()
            ;
        }
    }

    /**
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return [
            ScheduleDateEvent::SCHEDULE_DATE_EVENT => ['processScheduleDate', 128],
        ];
    }
}