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

use Schedules\Model\ScheduleDate;
use Schedules\Model\ScheduleDateQuery;
use Schedules\Schedules;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Core\Template\Loop\Argument\Argument;

/**
 * Class ScheduleDateLoop
 * @package Schedules\Loop
 */
class ScheduleDateLoop extends BaseLoop implements PropelSearchLoopInterface
{
    /**
     * Definition of loop arguments
     *
     * @return \Thelia\Core\Template\Loop\Argument\ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntTypeArgument('product_id', null),
            Argument::createBooleanTypeArgument('agenda', false)
        );
    }

    /**
     * this method returns a Propel ModelCriteria
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildModelCriteria()
    {
        $query = ScheduleDateQuery::create()
            //TODO : voir si on ne pourrait pas mettre un product_id dans ScheduleDate pour éviter 2 jointures
            ->join('ScheduleDate.Schedule')
            ->join('Schedule.ProductSchedule')
            ->join('ProductSchedule.Product')
            // filter by products that have good config template
            ->where('Product.TemplateId = ?', Schedules::getConfigValue('template', 0))
        ;

        if ($productId = $this->getProductId()) {
            $query->where('Product.Id = ?', $productId);
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
        foreach ($loopResult->getResultDataCollection() as $date) {
            $loopResultRow = new LoopResultRow($date);

            $loopResultRow
                ->set('ID', $date->getId())
                ->set('BEGIN', $this->getDateTimeBegin($date))
                ->set('END', $this->getDateTimeEnd($date))
                ->set('LABEL', $this->getLabel($date))
                // TODO : voir si on ne pourrait pas mettre un product_id dans ScheduleDate pour éviter 2 jointures
                ->set('REF', $date->getSchedule()->getProductSchedule()->getProduct()->getRef())
                ->set('STOCK', $date->getRemainingStock())
            ;

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }

    protected function getDateTimeBegin(ScheduleDate $date)
    {
        $dateBegin = $date->getDateBegin();
        if (null !== $timeBegin = $date->getTimeBegin()) {
            $dateBegin->setTime($timeBegin->format('H'), $timeBegin->format('i'), $timeBegin->format('s'));
        }
        if (true === $this->getAgenda()) {
            $dateBegin->format('Ymd\THis\Z');
        }
        return $dateBegin;
    }

    protected function getDateTimeEnd(ScheduleDate $date)
    {
        $dateEnd = $date->getDateEnd();
        if (null !== $timeEnd = $date->getTimeEnd()) {
            $dateEnd->setTime($timeEnd->format('H'), $timeEnd->format('i'), $timeEnd->format('s'));
        }
        if (true === $this->getAgenda()) {
            $dateEnd->format('Ymd\THis\Z');
        }
        return $dateEnd;
    }

    protected function getLabel(ScheduleDate $date)
    {
        $label = 'Du ' . $date->getDateBegin()->format('d/m/Y');
        if (null !== $date->getTimeBegin()) {
            $label .= ' à ' . $date->getTimeBegin()->format('H:i');
        }
        $label .= ' au ' . $date->getDateEnd()->format('d/m/Y');
        if (null !== $date->getTimeEnd()) {
            $label .= ' à ' . $date->getTimeEnd()->format('H:i');
        }
        return $label;
    }
}
