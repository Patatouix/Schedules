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

/**
 * Class FrontHook
 * @package Schedules\Hook
 * @author Thierry CARESMEL <dev@pixel-plurimedia.fr>
 */
class FrontHook extends BaseHook
{
    public function onProductAdditional(HookRenderBlockEvent $event)
    {
        $event->add([
            'id' => 'schedules',
            'title' => $this->trans('Schedules', [], Schedules::DOMAIN_NAME),
            'content' => $this->render('product_schedules.html', [
                'product_id' => $this->getRequest()->get('product_id'),
            ]),
        ]);
    }
}