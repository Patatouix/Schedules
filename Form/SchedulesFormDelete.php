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
use Thelia\Form\BaseForm;

/**
 * Class SchedulesFormDelete
 * @package Schedules\Form
 * @author Thierry Caresmel <thierry@pixel-plurimedia.fr>
 */
class SchedulesFormDelete extends BaseForm
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
                  'label' => $this->translator->trans('Schedule Id', [], Schedules::DOMAIN_NAME),
                  'label_attr' => ['for' => 'schedules_delete_schedule_id'],
                  'required' => true,
            ])
        ;
    }

    public function getName()
    {
        return "schedules_delete";
    }
}
