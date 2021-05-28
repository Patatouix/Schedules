<?php

namespace Schedules\EventListener;

use DateTime;
use Schedules\Model\ScheduleDateQuery;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Event\TheliaFormEvent;
use Thelia\Core\Translation\Translator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Core\HttpFoundation\Request;

/**
 * [Description CartAddFormAfterBuildEventListener]
 */
class CartAddFormAfterBuildEventListener implements EventSubscriberInterface
{
    const FORM = 'thelia_cart_add';

    //protected $request;

    /*public function __construct(Request $request)
    {
        $this->request = $request;
    }*/

    /**
     * @param TheliaFormEvent $event
     */
    public function addAntiSpamFields(TheliaFormEvent $event)
    {
        $formBuilder = $event->getForm()->getFormBuilder();

        // schedule date hidden field
        $formBuilder->add("schedule_date", ChoiceType::class, [
            'choices' => $this->getScheduleDateChoices(),
            "constraints" => [
                new NotBlank(),
            ],
            "label" => 'Schedule date',
            "label_attr" => array(
                "for" => "schedule_date",
                "help" => 'chose a date'
            ),
            "required" => true
        ]);
    }

    protected function getScheduleDateChoices()
    {
        $choices = array();

        $choices[0] = 'Aucune date sélectionnée';
        foreach (ScheduleDateQuery::create()->find() as $scheduleDate) {
            $choiceLabel = 'Du ' . $scheduleDate->getDateBegin()->format('d/m/Y');
            if (null !== $scheduleDate->getTimeBegin()) {
                $choiceLabel .= ' à ' . $scheduleDate->getTimeBegin()->format('H:i');
            }
            $choiceLabel .= ' au ' . $scheduleDate->getDateEnd()->format('d/m/Y');
            if (null !== $scheduleDate->getTimeEnd()) {
                $choiceLabel .= ' à ' . $scheduleDate->getTimeEnd()->format('H:i');
            }
            $choices[$scheduleDate->getId()] = $choiceLabel;
        }
        return $choices;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            (TheliaEvents::FORM_AFTER_BUILD.".".self::FORM) => [
                'addAntiSpamFields', 128
            ]
        ];
    }
}