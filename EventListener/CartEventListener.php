<?php

namespace Schedules\EventListener;

use Schedules\Model\CartItemScheduleDate;
use Schedules\Model\CartItemScheduleDateQuery;
use Schedules\Model\ScheduleDateQuery;
use Schedules\Schedules;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Event\Cart\CartEvent;
use Thelia\Core\Event\TheliaFormEvent;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Translation\Translator;
use Thelia\Model\CartItemQuery;
use Thelia\Model\ProductQuery;

/**
 * [Description CartEventListener]
 */
class CartEventListener implements EventSubscriberInterface
{
    const FORM = 'thelia_cart_add';

    protected $request;
    protected $dispatcher;

    public function __construct(Request $request, EventDispatcherInterface $dispatcher)
    {
        $this->request = $request;
        $this->dispatcher = $dispatcher;
    }

    public function createCartItemScheduleDate(CartEvent $event)
    {
        // only create cartItemScheduleDate if cart_item does not already exist
        if (null === CartItemScheduleDateQuery::create()->filterByCartItemId($event->getCartItem()->getId())->findOne()) {
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

    public function changeItem(CartEvent $event, $eventName)
    {
        if ((null !== $cartItemId = $event->getCartItemId()) && (null !== $quantity = $event->getQuantity())) {
            $cart = $event->getCart();

            $cartItem = CartItemQuery::create()
                ->filterByCartId($cart->getId())
                ->filterById($cartItemId)
                ->findOne();

            if ($cartItem) {
                if (null !== $cartItemScheduleDate = $cartItem->getCartItemScheduleDate()) {
                    if ($cartItemScheduleDate->getScheduleDate()->getRemainingStock() < $quantity) {
                        $cartItem->setQuantity($cartItemScheduleDate->getScheduleDate()->getRemainingStock());
                        $cartItem->save();
                        $event->setCartItem($cartItem);
                    }
                }
            }
        }
    }

    /**
     * @param TheliaFormEvent $event
     */
    public function addScheduleDateField(TheliaFormEvent $event)
    {
        $productId = null;
        if ($this->request->isMethod('get')) {
            $productId = $this->request->query->get('product_id');
        } else if ($this->request->isMethod('post')) {
            $productId = $this->request->request->get('product_id');
        }

        if (null !== $product = ProductQuery::create()->findPk($productId)) {
            if ($product->getTemplateId() == Schedules::getConfigValue('template')) {

                $formBuilder = $event->getForm()->getFormBuilder();
                $formBuilder->add("schedule_date", ChoiceType::class, [
                    "choices" => $this->getScheduleDateIds($productId),
                    "constraints" => [
                        new NotBlank(),
                        new Callback(array("methods" => array(
                            array($this, "checkStock")
                        )))
                    ],
                    "label" => 'Schedule date',
                    "label_attr" => array(
                        "for" => "schedule_date",
                        "help" => 'chose a date'
                    ),
                    "required" => true
                ]);
            }
        }
    }

    protected function getScheduleDateIds($productId)
    {
        $scheduleDateIds = [];
        $scheduleDates = ScheduleDateQuery::create()
            ->useScheduleQuery()->useProductScheduleQuery()
            ->filterByProductId($productId)->endUse()->endUse()
            ->find()
        ;
        // keep only as valid schedule dates those that have not depleted stock
        foreach ($scheduleDates as $scheduleDate) {
            if ($scheduleDate->getRemainingStock() > 0) {
                $scheduleDateIds[$scheduleDate->getId()] = $scheduleDate->getId();
            }
        }
        return $scheduleDateIds;
    }

    public function checkStock($value, ExecutionContextInterface $context)
    {
        $data = $context->getRoot()->getData();

        if (null !== $scheduleDate = ScheduleDateQuery::create()->findPk($data["schedule_date"])) {

            $consumedStock = 0;

            // check cart (do not allow customer to add to cart more than stock)
            foreach ($this->request->getSession()->getSessionCart($this->dispatcher)->getCartItems() as $cartItem) {
                if ($cartItem->getProductId() === (int)$data['product'] && $cartItem->getCartItemScheduleDate()->getScheduleDateId() === (int)$data["schedule_date"]) {
                    $consumedStock += $cartItem->getQuantity();
                }
            }

            // remove cart consumed stock from remaining stock, then compare to quantity of cart addition
            if (($scheduleDate->getRemainingStock() - $consumedStock) < $data["quantity"]) {
                $context->addViolation(Translator::getInstance()->trans("quantity value is not valid"));
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
            TheliaEvents::CART_UPDATEITEM => array("changeItem", 64),
            (TheliaEvents::FORM_AFTER_BUILD.".".self::FORM) => array('addScheduleDateField', 128,'setMaxQuantity')
        ];
    }
}