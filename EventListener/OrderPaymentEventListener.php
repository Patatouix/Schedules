<?php

namespace Schedules\EventListener;

use Exception;
use Schedules\Model\CartItemScheduleDate;
use Schedules\Model\CartItemScheduleDateQuery;
use Schedules\Model\OrderProductScheduleDate;
use Schedules\Model\ScheduleDate;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Event\Cart\CartEvent;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Exception\OrderException;
use Thelia\Exception\TheliaProcessException;

/**
 * [Description OrderPaymentEventListener]
 */
class OrderPaymentEventListener implements EventSubscriberInterface
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    // checks if cart items have valid schedule date stock before Thelia creates order products
    public function checkstockScheduleDate(OrderEvent $event)
    {
        foreach ($this->request->getSession()->getSessionCart()->getCartItems() as $cartItem) {
            if (null !== $cartItemScheduleDate = $cartItem->getCartItemScheduleDate()) {
                if ($cartItemScheduleDate->getScheduleDate()->getRemainingStock() < $cartItem->getQuantity()) {
                    $event->stopPropagation();
                    throw new OrderException('Un article de votre panier n\'est plus suffisamment disponible', TheliaProcessException::CART_ITEM_NOT_ENOUGH_STOCK, $cartItem);
                }
            }
        }
    }

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
            THeliaEvents::ORDER_PAY => array('checkstockScheduleDate', 256),
            TheliaEvents::ORDER_BEFORE_PAYMENT => array('createOrderProductScheduleDate', 128)
        ];
    }
}