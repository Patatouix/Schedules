<?php

namespace Schedules\EventListener;

use DateTime;
use Schedules\Model\ScheduleDateQuery;
use Schedules\Schedules;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Event\TheliaFormEvent;
use Thelia\Core\Translation\Translator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Model\ProductQuery;

/**
 * [Description CartAddFormAfterBuildEventListener]
 */
class CartAddFormAfterBuildEventListener implements EventSubscriberInterface
{
    const FORM = 'thelia_cart_add';

    protected $request;
    protected $dispatcher;

    public function __construct(Request $request, EventDispatcherInterface $dispatcher)
    {
        $this->request = $request;
        $this->dispatcher = $dispatcher;
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
            (TheliaEvents::FORM_AFTER_BUILD.".".self::FORM) => [
                'addScheduleDateField', 128,
                'setMaxQuantity',
            ]
        ];
    }
}