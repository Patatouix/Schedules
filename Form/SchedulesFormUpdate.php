<?php
/*************************************************************************************/
/*      This file is part of the module Schedules                             */
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
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class SchedulesFormUpdate
 * @package Schedules\Form
 * @author Thierry Caresmel <thierry@pixel-plurimedia.fr>
 */
class SchedulesFormUpdate extends SchedulesForm
{
    /**
     * @inheritDoc
     */
    protected function buildForm()
    {
        parent::buildForm();

        $this->formBuilder
            ->add('schedule_id', 'integer', [
                  'attr' => [],
                  'constraints' => array(new NotBlank(), ),
                  'label' => $this->translator->trans('Schedule Id', [], Schedules::DOMAIN_NAME),
                  'label_attr' => ['for' => 'attr-schedules-schedule-id'],
                  'required' => true,
            ])
            ->remove('beginPM')
            ->remove('endPM')
            ->remove('beginAM')
            ->remove('endAM')
            ->remove('day')
            ->add('day', 'choice', [
                  'attr' => [],
                  'choices' => $this->getDay(),
                  'label' => $this->translator->trans("Day", [], Schedules::DOMAIN_NAME),
                  'label_attr' => ['for' => 'attr-schedules-day'],
                  'required' => true,
            ])
            ->add('begin', 'time', [
                  'attr' => [],
                  'input' => 'string',
                  'label' => $this->translator->trans("Begin", [], Schedules::DOMAIN_NAME),
                  'label_attr' => ['for' => 'attr-schedules-begin'],
                  'required' => false,
                  'widget' => 'single_text',
            ])
            ->add('end', 'time', [
                  'attr' => [],
                  'input' => 'string',
                  'label' => $this->translator->trans("End", [], Schedules::DOMAIN_NAME),
                  'label_attr' => ['for' => 'attr-schedules-end'],
                  'required' => false,
                  'widget' => 'single_text',
            ])
        ;
    }

    public function getName()
    {
        return "schedules_update";
    }
}
