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

use Schedules\Schedules;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EntityType;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\GreaterThan;
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
            ->add(
                "template",
                ChoiceType::class,
                array(
                    "choices" => $this->getTemplateChoices(),
                    "label" => Translator::getInstance()->trans("Template : ", [], 'antispam'),
                    "label_attr" => [
                        "for" => "template",
                        "help" => Translator::getInstance()->trans('The name of the template that products must use in order to implement schedules', [], 'antispam')
                    ],
                    "required" => false,
                    "data" => Schedules::getConfigValue('template')
                )
            )
        ;
    }

    protected function getTemplateChoices()
    {
        $choices = array();
        $locale = $this->getRequest()->getSession()->getLang()->getLocale();

        $choices[0] = 'Aucun template';
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
