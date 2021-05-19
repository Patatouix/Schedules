<?php

namespace Schedules\Model;
use Schedules\Model\Map\ScheduleTableMap;
use Propel\Runtime\ActiveQuery\Criteria;

use Schedules\Model\Base\ScheduleQuery as BaseScheduleQuery;

/**
 * Skeleton subclass for performing query and update operations on the 'schedule' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class ScheduleQuery extends BaseScheduleQuery
{
    public function filterByPeriodNotNull()
    {
        return $this->where(ScheduleTableMap::PERIOD_BEGIN . " " . Criteria::ISNOTNULL . " " . Criteria::LOGICAL_AND . " " . ScheduleTableMap::PERIOD_END . " " . Criteria::ISNOTNULL);
    }

    public function filterByPeriodNull()
    {
        return $this->where(ScheduleTableMap::PERIOD_BEGIN . " " . Criteria::ISNULL . " " . Criteria::LOGICAL_AND . " " . ScheduleTableMap::PERIOD_END . " " . Criteria::ISNULL);
    }
}
