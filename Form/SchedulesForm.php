<?php
/*************************************************************************************/
/*      This file is part of the module Schedules                                    */
/*                                                                                   */
/*      Copyright (c) Pixel Plurimedia                                               */
/*      email : dev@pixel-plurimedia.fr                                              */
/*      web : https://pixel-plurimedia.fr                                            */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Schedules\Form;

use Schedules\Schedules;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Form\BaseForm;

/**
 * Class SchedulesForm
 * @package Schedules\Form
 * @author Thierry Caresmel <thierry@pixel-plurimedia.fr>
 */
class SchedulesForm extends BaseForm
{
    /**
     * @inheritDoc
     */
    protected function buildForm()
    {
        $this->formBuilder
            ->add('day', ChoiceType::class, [
                  'attr' => [],
                  'choices' => $this->getDay(),
                  'label' => $this->translator->trans("Day", [], Schedules::DOMAIN_NAME),
                  'label_attr' => ['for' => 'attr-schedules-day'],
                  'multiple' => true,
                  'required' => false,
            ])
            ->add('beginAM', TimeType::class, [
                  'attr' => [],
                  'input' => 'string',
                  'label' => $this->translator->trans("Begin", [], Schedules::DOMAIN_NAME),
                  'label_attr' => ['for' => 'attr-schedules-begin'],
                  'required' => false,
                  'widget' => 'single_text',
            ])
            ->add('endAM', TimeType::class, [
                  'attr' => [],
                  'input' => 'string',
                  'label' => $this->translator->trans("End", [], Schedules::DOMAIN_NAME),
                  'label_attr' => ['for' => 'attr-schedules-end'],
                  'required' => false,
                  'widget' => 'single_text',
            ])
            ->add('beginPM', TimeType::class, [
                  'attr' => [],
                  'input' => 'string',
                  'label' => $this->translator->trans("Begin", [], Schedules::DOMAIN_NAME),
                  'label_attr' => ['for' => 'attr-schedules-beginPM'],
                  'required' => false,
                  'widget' => 'single_text',
            ])
            ->add('endPM', TimeType::class, [
                  'attr' => [],
                  'input' => 'string',
                  'label' => $this->translator->trans("End", [], Schedules::DOMAIN_NAME),
                  'label_attr' => ['for' => 'attr-schedules-endPM'],
                  'required' => false,
                  'widget' => 'single_text',
            ])
            ->add('period_begin', DateType::class, [
                  'attr' => [],
                  'input' => 'string',
                  'label' => $this->translator->trans("Period Begin", [], Schedules::DOMAIN_NAME),
                  'label_attr' => ['for' => 'attr-schedules-period-begin'],
                  'required' => false,
                  'widget' => 'single_text',
            ])
            ->add('period_end', DateType::class, [
                  'attr' => [],
                  'input' => 'string',
                  'label' => $this->translator->trans("Period End", [], Schedules::DOMAIN_NAME),
                  'label_attr' => ['for' => 'attr-schedules-period-end'],
                  'required' => false,
                  'widget' => 'single_text',
            ])
            ->add('closed', IntegerType::class, [
                  'attr' => [],
                  'label' => $this->translator->trans("Closed", [], Schedules::DOMAIN_NAME),
                  'label_attr' => ['for' => 'attr-schedules-closed'],
                  'required' => true,
            ])
            ->add('resource_type', TextType::class, [
                'attr' => [],
                'constraints' => [
                    new Choice(['product', 'content', 'store']),
                ],
                'label' => $this->translator->trans("Product", [], Schedules::DOMAIN_NAME),
                'label_attr' => ['for' => 'attr-schedules-product-id'],
                'required' => true,
            ])
            ->add('resource_id', IntegerType::class, [
                'attr' => [],
                'constraints' => array(),
                'label' => $this->translator->trans("Product", [], Schedules::DOMAIN_NAME),
                'label_attr' => ['for' => 'attr-schedules-product-id'],
                'required' => true,
            ])
            ->add('stock', IntegerType::class, [
                'attr' => [
                    'min' => 0,
                ],
                "constraints" => array(
                    new GreaterThanOrEqual(["value" => 0]),
                ),
                'label' => $this->translator->trans("Stock", [], Schedules::DOMAIN_NAME),
                'label_attr' => ['for' => 'attr-schedules-stock'],
                'required' => false,
            ])
        ;
    }

    protected function getDay()
    {
        return [
            $this->translator->trans("Monday", [], Schedules::DOMAIN_NAME),
            $this->translator->trans("Tuesday", [], Schedules::DOMAIN_NAME),
            $this->translator->trans("Wednesday", [], Schedules::DOMAIN_NAME),
            $this->translator->trans("Thursday", [], Schedules::DOMAIN_NAME),
            $this->translator->trans("Friday", [], Schedules::DOMAIN_NAME),
            $this->translator->trans("Saturday", [], Schedules::DOMAIN_NAME),
            $this->translator->trans("Sunday", [], Schedules::DOMAIN_NAME)
        ];
    }

    public function getName()
    {
        return "schedules_create";
    }
}
