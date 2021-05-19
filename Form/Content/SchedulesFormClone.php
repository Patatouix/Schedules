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

namespace Schedules\Form\Content;

use Schedules\Schedules;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Form\BaseForm;

/**
 * Class SchedulesFormClone
 * @package Schedules\Form
 * @author Thierry Caresmel <thierry@pixel-plurimedia.fr>
 */
class SchedulesFormClone extends BaseForm
{
    /**
     * @inheritDoc
     */
    protected function buildForm()
    {
        $this->formBuilder
            ->add('schedule_id', 'integer', [
                  'attr' => [],
                  'constraints' => array(new NotBlank(), ),
                  'label' => $this->translator->trans('Id', [], Schedules::DOMAIN_NAME),
                  'label_attr' => ['for' => 'attr-schedules-clone-schedule-id'],
                  'required' => true,
            ])
            ->add('day', 'choice', [
                  'attr' => [],
                  'choices' => $this->getDay(),
                  'label' => $this->translator->trans('Day', [], Schedules::DOMAIN_NAME),
                  'label_attr' => ['for' => 'attr-schedules-clone-day'],
                  'required' => true,
            ]);
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
        return "schedules_clone";
    }
}
