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

use DateTime;
use Schedules\Schedules;
use Schedules\Model\ProductScheduleDateQuery;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

/**
 * Class AgendaLoop
 * @package Schedules\Loop
 */
class AgendaLoop extends BaseLoop implements PropelSearchLoopInterface
{
    /**
     * Definition of loop arguments
     *
     * @return \Thelia\Core\Template\Loop\Argument\ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            //
        );
    }

    /**
     * this method returns a Propel ModelCriteria
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildModelCriteria()
    {
        $query = ProductScheduleDateQuery::create();
        // filter by products that have good config template

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
                ->set('DATE_BEGIN', $date->getDateBegin()->format('Ymd\THis\Z'))
                ->set('DATE_END', $date->getDateEnd()->format('Ymd\THis\Z'))
                ->set('TIME_BEGIN', $date->getTimeBegin())
                ->set('TIME_END', $date->getTimeEnd())
                ->set('STOCK', $date->getStock())
                ->set('CLOSED', $date->getClosed())
                ->set('PRODUCT_ID', $date->getProductId())
            ;

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }
}
