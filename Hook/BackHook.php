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

/**
 * Class BackHook
 * @package Schedules\Hook
 * @author Thierry CARESMEL <dev@pixel-plurimedia.fr>
 */
class BackHook extends BaseHook
{
    public function onProductTab(HookRenderBlockEvent $event)
    {
        $event->add([
            'id' => 'schedules',
            'title' => $this->trans('Schedules', [], Schedules::DOMAIN_NAME),
            'content' => $this->render('product_schedules.html', [
                'product_id' => $this->getRequest()->get('product_id'),
            ]),
        ]);
    }

    public function onContentTab(HookRenderBlockEvent $event)
    {
        $event->add([
            'id' => 'schedules',
            'title' => $this->trans('Schedules', [], Schedules::DOMAIN_NAME),
            'content' => $this->render('content_schedules.html', [
                'content_id' => $this->getRequest()->get('content_id'),
            ]),
        ]);
    }

    public function onConfigStoreTab(HookRenderBlockEvent $event)
    {
        $event->add([
            'id' => 'schedules',
            'title' => $this->trans('Schedules', [], Schedules::DOMAIN_NAME),
            'content' => $this->render('store_schedules.html')
        ]);
    }
}
