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

namespace Schedules;

use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Core\Template\TemplateDefinition;
use Thelia\Module\BaseModule;
use Thelia\Install\Database;
use Thelia\Model\ModuleConfigQuery;

class Schedules extends BaseModule
{
    /** @var string */
    const DOMAIN_NAME = 'schedules';

    public function postActivation(ConnectionInterface $con = null)
    {
        if (!$this->getConfigValue('is_initialized', false)) {
            $database = new Database($con);
            $database->insertSql(null, array(__DIR__ . '/Config/Sql/create.sql'));
            $this->setConfigValue('is_initialized', true);
            $this->setConfigValue('template', 0);
        }
    }

    public function destroy(ConnectionInterface $con = null, $deleteModuleData = false)
    {
        if ($deleteModuleData) {
            $database = new Database($con);
            $database->insertSql(null, array(__DIR__ . '/Config/Sql/destroy.sql'));
            ModuleConfigQuery::create()->deleteConfigValue(self::getModuleId(), 'is_initialized');
        }
    }

    public function getHooks()
    {
        return [
            [
                "active" => true,
                "block" => true,
                "code" => "config-store.tab",
                "description" => "Add tabs in the admin store configuration page",
                "title" => "Store configuration tabs",
                "type" => TemplateDefinition::BACK_OFFICE,
            ],
        ];
    }
}
