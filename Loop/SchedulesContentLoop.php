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

namespace Schedules\Loop;

use Schedules\Schedules;
use Schedules\Model\ContentSchedule;
use Schedules\Model\ContentScheduleQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

/**
 * Class SchedulesContentLoop
 * @package Schedules\Loop
 */
class SchedulesContentLoop extends BaseLoop implements PropelSearchLoopInterface
{
    /**
     * Definition of loop arguments
     *
     * @return \Thelia\Core\Template\Loop\Argument\ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(

            Argument::createIntListTypeArgument('schedule_id'),
            Argument::createIntListTypeArgument('content_id'),
            Argument::createBooleanTypeArgument('default_period'),
            Argument::createBooleanTypeArgument('hide_past', false),
            Argument::createBooleanTypeArgument('closed', false),
            Argument::createIntListTypeArgument('day'),
            Argument::createEnumListTypeArgument('order', [
                'schedule_id',
                'schedule_id_reverse',
                'day',
                'day_reverse',
                'begin',
                'begin_reverse',
                'period_begin',
                'period_begin_reverse'
            ], 'schedule_id')

        );
    }

    /**
     * this method returns a Propel ModelCriteria
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildModelCriteria()
    {
        $query = ContentScheduleQuery::create();

        if ($scheduleId = $this->getScheduleId()) {
            $query->filterByScheduleId($scheduleId);
        }

        if ($day = $this->getDay()) {
            $query->useScheduleQuery()->filterByDay($day)->endUse();
        }

        if ($contentId = $this->getContentId()) {
            $query->filterByContentId($contentId);
        }

        if (true == $this->getDefaultPeriod()) {
            $query->useScheduleQuery()->filterByPeriodNull()->endUse();
        } elseif (false == $this->getDefaultPeriod() && !is_null($this->getDefaultPeriod())) {
            $query->useScheduleQuery()->filterByPeriodNotNull()->endUse();
            if ($this->getHidePast()) {
                $query->useScheduleQuery()->filterByPeriodEnd(new \DateTime(), Criteria::GREATER_THAN)->endUse();
            }
        }

        $query->useScheduleQuery()->filterByClosed($this->getClosed())->endUse();

        foreach ($this->getOrder() as $order) {
            switch ($order) {
                case 'schedule_id':
                    $query->useScheduleQuery()->orderById()->endUse();
                    break;
                case 'schedule_id_reverse':
                    $query->useScheduleQuery()->orderById(Criteria::DESC)->endUse();
                    break;
                case 'day':
                    $query->useScheduleQuery()->orderByDay()->endUse();
                    break;
                case 'day_reverse':
                    $query->useScheduleQuery()->orderByDay(Criteria::DESC)->endUse();
                    break;
                case 'begin':
                    $query->useScheduleQuery()->orderByBegin()->endUse();
                    break;
                case 'begin_reverse':
                    $query->useScheduleQuery()->orderByBegin(Criteria::DESC)->endUse();
                    break;
                case 'period_begin':
                    $query->useScheduleQuery()->orderByPeriodBegin()->endUse();
                    break;
                case 'period_begin_reverse':
                    $query->useScheduleQuery()->orderByPeriodBegin(Criteria::DESC)->endUse();
                    break;
                default:
                    break;
            }
        }

        return $query;
    }

    /**
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        /** @var ContentSchedule $schedules */
        foreach ($loopResult->getResultDataCollection() as $schedules) {
            $loopResultRow = new LoopResultRow($schedules);

            $loopResultRow
                ->set('SCHEDULE_ID', $schedules->getScheduleId())
                ->set('CONTENT_ID', $schedules->getContentId())
                ->set('DAY', $schedules->getSchedule()->getDay())
                ->set('DAY_LABEL', $this->getDayLabel($schedules->getSchedule()->getDay()))
                ->set('BEGIN', $schedules->getSchedule()->getBegin())
                ->set('END', $schedules->getSchedule()->getEnd())
                ->set('PERIOD_BEGIN', $schedules->getSchedule()->getPeriodBegin())
                ->set('PERIOD_END', $schedules->getSchedule()->getPeriodEnd());


            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }

    protected function getDayLabel($int = 0)
    {
        return [
            $this->translator->trans("Monday", [], Schedules::DOMAIN_NAME),
            $this->translator->trans("Tuesday", [], Schedules::DOMAIN_NAME),
            $this->translator->trans("Wednesday", [], Schedules::DOMAIN_NAME),
            $this->translator->trans("Thursday", [], Schedules::DOMAIN_NAME),
            $this->translator->trans("Friday", [], Schedules::DOMAIN_NAME),
            $this->translator->trans("Saturday", [], Schedules::DOMAIN_NAME),
            $this->translator->trans("Sunday", [], Schedules::DOMAIN_NAME)
        ][$int];
    }
}
