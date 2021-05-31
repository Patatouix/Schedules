<?php

namespace Schedules\EventListener;

use Schedules\Model\CartItemScheduleDate;
use Schedules\Model\CartItemScheduleDateQuery;
use Schedules\Model\OrderProductScheduleDate;
use Schedules\Model\ScheduleDate;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Event\Cart\CartEvent;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Model\CartItemQuery;

/**
 * [Description OrderBeforePaymentEventListener]
 */
class OrderBeforePaymentEventListener implements EventSubscriberInterface
{
    public function createOrderProductScheduleDate(OrderEvent $event)
    {
        foreach ($event->getOrder()->getOrderProducts() as $orderProduct) {
            // check if order product was added to cart with a schedule date
            if (null !== $cartItemScheduleDate = CartItemScheduleDateQuery::create()->filterByCartItemId($orderProduct->getCartItemId())->findOne()) {
                // associates order product to this schedule date
                $orderProductScheduleDate = new OrderProductScheduleDate();
                $orderProductScheduleDate
                    ->setOrderProductId($orderProduct->getId())
                    ->setScheduleDateId($cartItemScheduleDate->getScheduleDateId())
                    ->save()
                ;
            }
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            TheliaEvents::ORDER_BEFORE_PAYMENT => array('createOrderProductScheduleDate', 128)
        ];
    }
}