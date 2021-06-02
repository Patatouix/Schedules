<?php
/*************************************************************************************/
/*      This file is part of the Schedules module.                                   */
/*                                                                                   */
/*      Copyright (c) Pixel Plurimedia                                               */
/*      email : dev@pixel-plurimedia.fr                                              */
/*      web : https://pixel-plurimedia.fr                                            */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Schedules\Hook;

use Thelia\Core\Hook\BaseHook;
use Thelia\Core\Event\Hook\HookRenderBlockEvent;

use Schedules\Schedules;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Model\ProductQuery;

/**
 * Class FrontHook
 * @package Schedules\Hook
 * @author Thierry CARESMEL <dev@pixel-plurimedia.fr>
 */
class FrontHook extends BaseHook
{
    public function onProductAdditional(HookRenderBlockEvent $event)
    {
        if ($this->isProductSchedulable()) {
            $event->add([
                'id' => 'schedules',
                'title' => $this->trans('Schedules', [], Schedules::DOMAIN_NAME),
                'content' => $this->render('product_schedules.html', [
                    'product_id' => $this->getRequest()->get('product_id'),
                ]),
            ]);
        }
    }

    public function onProductJavascriptInitialization(HookRenderEvent $event)
    {
        if ($this->isProductSchedulable()) {
            $event->add($this->addJS("assets/js/fullcalendar.js"));
            $event->add($this->addJS("assets/js/schedules.js"));
        }
    }

    public function onProductStylesheet(HookRenderEvent $event)
    {
        if ($this->isProductSchedulable()) {
            $event->add($this->addCSS("assets/css/fullcalendar.css"));
        }
    }

    protected function isProductSchedulable()
    {
        if (null !== $product = ProductQuery::create()->findPk($this->getRequest()->get('product_id'))) {
            // check if product template is the one required in module config
            return $product->getTemplateId() == Schedules::getConfigValue('template');
        } else {
            return false;
        }
    }
}