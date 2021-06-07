<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Schedules\Form;

use phpDocumentor\Reflection\Types\Integer;
use Schedules\Schedules;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use Thelia\Model\Base\TemplateQuery;

/**
 * Class Configuration
 * @package Schedules\Form
 * @author Thomas Arnaud <tarnaud@openstudio.fr>
 */
class Configuration extends BaseForm
{

    protected function buildForm()
    {
        $this->formBuilder
            ->add("template_event_id", ChoiceType::class, [
                "choices" => $this->getTemplateChoices(),
                "label" => Translator::getInstance()->trans("Template : ", [], Schedules::DOMAIN_NAME),
                "label_attr" => [
                    "for" => "template_event_id",
                    "help" => Translator::getInstance()->trans('The template products must use in order to implement schedules.', [], Schedules::DOMAIN_NAME)
                ],
                "required" => false,
                "data" => Schedules::getConfigValue('template_event_id', 0)
            ])
            ->add("schedule_step", IntegerType::class, [
                'attr' => [
                    'min' => 0,
                ],
                "constraints" => array(
                    new GreaterThanOrEqual(["value" => 0]),
                ),
                "label" => Translator::getInstance()->trans("Schedule step : ", [], Schedules::DOMAIN_NAME),
                "label_attr" => [
                    "for" => "schedule_step",
                    "help" => Translator::getInstance()->trans('Duration of each step during a schedule.', [], Schedules::DOMAIN_NAME)
                ],
                "required" => false,
                "data" => Schedules::getConfigValue('schedule_step', 0)
            ])
        ;
    }

    protected function getTemplateChoices()
    {
        $choices = array();
        $locale = $this->getRequest()->getSession()->getLang()->getLocale();

        $choices[0] = $this->translator->trans('No template', [], Schedules::DOMAIN_NAME);
        foreach (TemplateQuery::create()->find() as $template) {
            $choices[$template->getId()] = $template->setLocale($locale)->getName();
        }
        return $choices;
    }

    /**
     * @return string the name of you form. This name must be unique
     */
    public function getName()
    {
        return "schedules_configuration";
    }
}
