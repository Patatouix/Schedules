<?php

namespace Schedules\EventListener;

use Schedules\Model\CartItemScheduleDate;
use Schedules\Model\CartItemScheduleDateQuery;
use Schedules\Model\ScheduleDate;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Event\Cart\CartEvent;
use Thelia\Model\CartItemQuery;

/**
 * [Description CartAddItemEventListener]
 */
class CartAddItemEventListener implements EventSubscriberInterface
{
    public function createCartItemScheduleDate(CartEvent $event)
    {
        if (null === CartItemScheduleDateQuery::create()->filterByScheduleDateId($event->schedule_date)->findOne()) {
            $cartItemScheduleDate = new CartItemScheduleDate();
            $cartItemScheduleDate
                ->setScheduleDateId($event->schedule_date)
                ->setCartItemId($event->getCartItem()->getId())
                ->save()
            ;
        }
    }

    public function findCartItem(CartEvent $event)
    {
        // let thelia filter, then filter by CartItemScheduleDate so we don't stack cart items with different schedule date
        if (null !== $event->getCartItem()) {
            if (null !== $foundItem = CartItemQuery::create()
                ->useCartItemScheduleDateQuery()
                ->filterByScheduleDateId($event->schedule_date)
                ->endUse()
                ->findOne()) {
                $event->setCartItem($foundItem);
            } else {
                $event->clearCartItem();
            }
        }
    }
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            TheliaEvents::CART_ADDITEM => array('createCartItemScheduleDate', 64),
            TheliaEvents::CART_FINDITEM => array("findCartItem", 64),
        ];
    }
}