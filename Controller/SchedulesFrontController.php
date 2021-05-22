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

use LocalPickup\Listener\Schedules;
use Thelia\Controller\Admin\ProductController;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Security\AccessManager;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Tools\URL;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Schedules\Schedules as ModuleSchedules;
use Propel\Runtime\Propel;
use Schedules\Model\ScheduleQuery;
use Thelia\Controller\Front\BaseFrontController;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\HttpFoundation\Response;
use Thelia\Form\Definition\AdminForm;
use Thelia\Model\Base\ProductQuery;
use TheliaSmarty\Template\Plugins\Render;

/**
 * Class SchedulesProductController
 * @package Schedules\Controller
 * @author Thierry Caresmel <thierry@pixel-plurimedia.fr>
 */
class SchedulesFrontController extends BaseFrontController
{
    public function getAgenda()
    {
        $query = ProductQuery::create();

        $products = $query->filterByTemplateId(ModuleSchedules::getConfigValue('template'))->find();

        return $this->render('agenda.ics');
    }
}
