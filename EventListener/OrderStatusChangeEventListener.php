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
use Thelia\Model\Base\OrderStatusQuery;

/**
 * [Description OrderStatusChangeEventListener]
 */
class OrderStatusChangeEventListener implements EventSubscriberInterface
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param OrderEvent $event
     * @param $eventName
     * @param EventDispatcherInterface $dispatcher
     * @throws \Exception
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function updateStatus(OrderEvent $event, $eventName, EventDispatcherInterface $dispatcher)
    {
        $order = $event->getOrder();
        $newStatus = $event->getStatus();

        if ($newStatus !== $order->getStatusId()) {
            if (null !== $newStatusModel = OrderStatusQuery::create()->findPk($newStatus)) {
            // on status paid, check if there is still enough stock
                if ($order->isNotPaid(false) && $newStatusModel->isPaid(false)) {
                    $orderProducts = $order->getOrderProducts();
                    foreach ($orderProducts as $orderProduct) {
                        if (null !== $orderProductScheduleDate = $orderProduct->getOrderProductScheduleDate()) {
                            if ($orderProductScheduleDate->getScheduleDate()->getRemainingStock() < $orderProduct->getQuantity()) {
                                throw new TheliaProcessException($orderProduct->getProductRef() . " : Not enough stock");
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            THeliaEvents::ORDER_UPDATE_STATUS => array('updateStatus', 256),
        ];
    }
}