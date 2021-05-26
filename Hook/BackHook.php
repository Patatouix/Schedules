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
use Thelia\Model\Base\ProductQuery;
use Thelia\Model\Base\TemplateQuery;

/**
 * Class BackHook
 * @package Schedules\Hook
 * @author Thierry CARESMEL <dev@pixel-plurimedia.fr>
 */
class BackHook extends BaseHook
{
    public function onProductTab(HookRenderBlockEvent $event)
    {
        if (null !== $product = ProductQuery::create()->findPk($this->getRequest()->get('product_id'))) {
            // check if product template is the one required in module config
            if ($product->getTemplateId() == Schedules::getConfigValue('template')) {

                $event->add([
                    'id' => 'schedules',
                    'title' => $this->trans('Schedules', [], Schedules::DOMAIN_NAME),
                    'content' => $this->render('schedules_product.html', [
                        'resource_type' => 'product',
                        'resource_auth' => 'admin.product',
                        'resource_id' => $this->getRequest()->get('product_id'),
                    ]),
                ]);
            }
        }
    }

    public function onContentTab(HookRenderBlockEvent $event)
    {
        $event->add([
            'id' => 'schedules',
            'title' => $this->trans('Schedules', [], Schedules::DOMAIN_NAME),
            'content' => $this->render('schedules.html', [
                'resource_type' => 'content',
                'resource_auth' => 'admin.content',
                'resource_id' => $this->getRequest()->get('content_id'),
            ]),
        ]);
    }

    public function onConfigStoreTab(HookRenderBlockEvent $event)
    {
        $event->add([
            'id' => 'schedules',
            'title' => $this->trans('Schedules', [], Schedules::DOMAIN_NAME),
            'content' => $this->render('schedules.html', [
                'resource_type' => 'store',
                'resource_auth' => 'admin.configuration.store',
                'resource_id' => null
            ])
        ]);
    }
}
