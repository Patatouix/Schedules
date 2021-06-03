<?php
/*************************************************************************************/
/*      This file is part of the module Schedules                                    */
/*                                                                                   */
/*      Copyright (c) Pixel Plurimedia                                               */
/*      email : dev@pixel-plurimedia.fr                                              */
/*      web : https://pixel-plurimedia.fr                                            */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Schedules\Controller;

use DateTime;
use Schedules\Model\ScheduleDate;
use Schedules\Model\ScheduleDateQuery;
use Schedules\Schedules;
use Symfony\Component\HttpFoundation\Response;
use Thelia\Controller\Front\BaseFrontController;
use Thelia\Core\HttpFoundation\JsonResponse;

/**
 * Class SchedulesProductController
 * @package Schedules\Controller
 * @author Thierry Caresmel <thierry@pixel-plurimedia.fr>
 */
class SchedulesFrontController extends BaseFrontController
{
    public function getAgenda()
    {
        return new Response($this->renderRaw('agenda.ics'), 200, [
            'Content-type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename=agenda.ics'
        ]);
    }

    public function getCalendarEvents()
    {
        $query = $this->getRequest()->query;

        if (null !== $productId = $query->get('productId')) {

            $scheduledDates = ScheduleDateQuery::create()
                ->join('ScheduleDate.Schedule')
                ->join('Schedule.ProductSchedule')
                ->join('ProductSchedule.Product')
                // filter by products that have good config template
                ->where('Product.TemplateId = ?', Schedules::getConfigValue('template', 0))
                ->where('Product.Id = ?', $productId)
                ->find()
            ;

            $calendarData = [];

            foreach ($scheduledDates as $scheduleDate) {

                $dateTimeBegin = $query->get('start');
                if (null !== $dateTimeBegin && $this->getDateTimeBegin($scheduleDate) < new DateTime($dateTimeBegin)) {
                    break;
                }
                $dateTimeEnd = $dateTimeEnd = $query->get('end');
                if (null !== $dateTimeEnd && $this->getDateTimeEnd($scheduleDate) > new Datetime($dateTimeEnd)) {
                    break;
                }

                $calendarEvent = [
                    'start' => $this->getDateTimeBegin($scheduleDate)->format('Y-m-d H:i:s'),
                    'end' => $this->getDateTimeEnd($scheduleDate)->format('Y-m-d H:i:s'),
                    //'title' => $scheduleDate->getSchedule()->getProductSchedule()->getProduct()->getRef(),
                    'color' => $scheduleDate->getStock() ? 'green' : 'red',
                    'selectable' => $scheduleDate->getStock() ? true : false
                ];
                array_push($calendarData, $calendarEvent);
            }

            return JsonResponse::create($calendarData);
        }

        return $this->nullResponse();
    }

    protected function getDateTimeBegin(ScheduleDate $date)
    {
        $dateBegin = $date->getDateBegin();
        if (null !== $timeBegin = $date->getTimeBegin()) {
            $dateBegin->setTime($timeBegin->format('H'), $timeBegin->format('i'), $timeBegin->format('s'));
        }
        return $dateBegin;
    }

    protected function getDateTimeEnd(ScheduleDate $date)
    {
        $dateEnd = $date->getDateEnd();
        if (null !== $timeEnd = $date->getTimeEnd()) {
            $dateEnd->setTime($timeEnd->format('H'), $timeEnd->format('i'), $timeEnd->format('s'));
        }
        return $dateEnd;
    }
}