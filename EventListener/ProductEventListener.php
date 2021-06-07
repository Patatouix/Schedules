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


namespace Schedules\EventListener;

use Schedules\Schedules;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\Product\ProductCreateEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Translation\Translator;

/**
 * Class ProductEventListener
 * @package Schedules\EventListener
 */
class ProductEventListener implements EventSubscriberInterface
{
    public function checkEventTemplateProductisVirtual(ProductCreateEvent $event)
    {
        if ($event->getTemplateId() === (int)Schedules::getConfigValue('template_event_id', 0) && $event->getVirtual() !== 1) {
            $event->stopPropagation();
            throw new \LogicException(
                Translator::getInstance()->trans("A product that uses Schedules module gabarit should be virtual.", [], Schedules::DOMAIN_NAME)
            );
        }
    }

    /**
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return [
            TheliaEvents::PRODUCT_CREATE => ['checkEventTemplateProductisVirtual', 256],
        ];
    }
}