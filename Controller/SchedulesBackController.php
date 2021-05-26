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

namespace Schedules\Controller;

use Thelia\Controller\Admin\ProductController;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Security\AccessManager;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Tools\URL;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Propel\Runtime\Propel;
use Schedules\Event\CloneScheduleEvent;
use Schedules\Event\CreateScheduleEvent;
use Schedules\Event\DeleteScheduleEvent;
use Schedules\Event\SchedulesEvent;
use Schedules\Event\SchedulesProductEvent;
use Schedules\Event\UpdateScheduleEvent;
use Schedules\Form\SchedulesForm;
use Schedules\Event\ScheduleEvent;
use Schedules\Schedules as SchedulesModule;
use Schedules\Model\ProductSchedule;
use Schedules\Model\ProductScheduleQuery;
use Schedules\Model\Schedule;
use Schedules\Model\ScheduleQuery;
use Thelia\Controller\Admin\AbstractCrudController;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Form\Definition\AdminForm;
use TheliaSmarty\Template\Plugins\Render;

/**
 * Class SchedulesProductController
 * @package Schedules\Controller
 * @author Thierry Caresmel <thierry@pixel-plurimedia.fr>
 */
class SchedulesBackController extends BaseAdminController
{
    // overrides GET config-store.html
    public function getConfigStore()
    {
        if (null !== $response = $this->checkAuth(AdminResources::STORE, array(), AccessManager::VIEW)) {
            return $response;
        }

        // The form is self-hydrated
        $configStoreForm = $this->createForm(AdminForm::CONFIG_STORE);

        $this->getParserContext()->addForm($configStoreForm);

        return $this->render('config_store_with_tabs', [
            'current_tab' => $this->getRequest()->get('current_tab', 'general')
        ]);
    }

    /**
     * Create an object
     * @return mixed|\Symfony\Component\HttpFoundation\Response
     */
    public function createScheduleAction(Request $request)
    {
        // Check current user authorization
        if (null !== $response = $this->checkAuth([], SchedulesModule::getModuleCode(), AccessManager::CREATE))
        {
            return $response;
        }

        // Error (Default: false)
        $error_msg = false;
        // Create the Creation Form
        $creationForm = $this->createForm(SchedulesModule::SCHEDULES_FORM_CREATE);

        try {
            // Check the form against constraints violations
            $form = $this->validateForm($creationForm, "POST");
            // Get the form field values
            $data = $form->getData();

            // dispatch schedule creation event with validated data
            $event = new ScheduleEvent();
            $event->setData($data);
            $event->setAction('create');

            $this->dispatch(
                ScheduleEvent::SCHEDULE_EVENT,
                $event
            );

            // Redirect to the success URL
            return $this->generateSuccessRedirect($creationForm);

        } catch (FormValidationException $ex) {
            // Form cannot be validated
            $error_msg = $this->createStandardFormValidationErrorMessage($ex);

        } catch (\Exception $ex) {
            // Any other error
            $error_msg = $ex->getMessage();
        }

        if (false !== $error_msg) {
            $this->setupFormErrorContext(
                $this->getTranslator()->trans("%obj creation", ['%obj' => SchedulesModule::DOMAIN_NAME]),
                $error_msg,
                $creationForm,
                $ex
            );

            // At this point, the form has error, and should be redisplayed.
            return $this->generateErrorRedirect($creationForm);
        }
    }

    /**
     * Save changes on a modified object, and either go back to the object list, or stay on the edition page.
     *
     * @return \Thelia\Core\HttpFoundation\Response the response
     */
    public function updateScheduleAction()
    {
        // Check current user authorization
        if (null !== $response = $this->checkAuth([], SchedulesModule::getModuleCode(), AccessManager::UPDATE))
        {
            return $response;
        }

        // Error (Default: false)
        $error_msg = false;
        // Create the Form from the request
        $changeForm = $this->createForm(SchedulesModule::SCHEDULES_FORM_UPDATE);

        try {
            // Check the form against constraints violations
            $form = $this->validateForm($changeForm, "POST");

            // Get the form field values
            $data = $form->getData();

            // dispatch schedule update event with validated data
            $event = new ScheduleEvent();
            $event->setData($data);
            $event->setAction('update');

            $this->dispatch(
                ScheduleEvent::SCHEDULE_EVENT,
                $event
            );

            // Redirect to the success URL
            return $this->generateSuccessRedirect($changeForm);

        } catch (FormValidationException $ex) {
            // Form cannot be validated
            $error_msg = $this->createStandardFormValidationErrorMessage($ex);

        } catch (\Exception $ex) {
            // Any other error
            $error_msg = $ex->getMessage();
        }

        if (false !== $error_msg) {
            // At this point, the form has errors, and should be redisplayed.
            $this->setupFormErrorContext(
                $this->getTranslator()->trans("%obj modification", ['%obj' => SchedulesModule::DOMAIN_NAME]),
                $error_msg,
                $changeForm,
                $ex
            );

            // At this point, the form has error, and should be redisplayed.
            return $this->generateErrorRedirect($changeForm);
        }
    }

    public function cloneScheduleAction()
    {
        // Check current user authorization
        if (null !== $response = $this->checkAuth([], SchedulesModule::getModuleCode(), AccessManager::CREATE))
        {
            return $response;
        }

        // Error (Default: false)
        $error_msg = false;
        // Create the Creation Form
        $cloneForm = $this->createForm(SchedulesModule::SCHEDULES_FORM_CLONE);

        try {
            // Check the form against constraints violations
            $form = $this->validateForm($cloneForm, "POST");
            // Get the form field values
            $data = $form->getData();

            // dispatch schedule clone event with validated data
            $event = new ScheduleEvent();
            $event->setData($data);
            $event->setAction('clone');

            $this->dispatch(
                ScheduleEvent::SCHEDULE_EVENT,
                $event
            );

            // Redirect to the success URL
            return $this->generateSuccessRedirect($cloneForm);

        } catch (FormValidationException $ex) {
            // Form cannot be validated
            $error_msg = $this->createStandardFormValidationErrorMessage($ex);

        } catch (\Exception $ex) {
            // Any other error
            $error_msg = $ex->getMessage();
        }

        if (false !== $error_msg) {
            $this->setupFormErrorContext(
                $this->getTranslator()->trans("%obj cloning", ['%obj' => SchedulesModule::DOMAIN_NAME]),
                $error_msg,
                $cloneForm,
                $ex
            );

            // At this point, the form has error, and should be redisplayed.
            return $this->generateErrorRedirect($cloneForm);
        }
    }

    /**
     * Delete an object
     *
     * @return \Thelia\Core\HttpFoundation\Response the response
     */
    public function deleteScheduleAction()
    {
        // Check current user authorization
        if (null !== $response = $this->checkAuth([], SchedulesModule::getModuleCode(), AccessManager::DELETE))
        {
            return $response;
        }

        // Error (Default: false)
        $error_msg = false;
        // Create the Creation Form
        $deleteForm = $this->createForm(SchedulesModule::SCHEDULES_FORM_DELETE);

        try {
            // Check the form against constraints violations
            $form = $this->validateForm($deleteForm, "POST");
            // Get the form field values
            $data = $form->getData();

            // dispatch schedule delete event
            $event = new ScheduleEvent();
            $event->setData($data);
            $event->setAction('delete');

            $this->dispatch(
                ScheduleEvent::SCHEDULE_EVENT,
                $event
            );

            // Redirect to the success URL
            return $this->generateSuccessRedirect($deleteForm);

        } catch (FormValidationException $ex) {
            // Form cannot be validated
            $error_msg = $this->createStandardFormValidationErrorMessage($ex);

        } catch (\Exception $ex) {
            // Any other error
            $error_msg = $ex->getMessage();
        }

        if (false !== $error_msg) {
            $this->setupFormErrorContext(
                $this->getTranslator()->trans("%obj deletion", ['%obj' => SchedulesModule::DOMAIN_NAME]),
                $error_msg,
                $deleteForm,
                $ex
            );

            // At this point, the form has error, and should be redisplayed.
            return $this->generateErrorRedirect($deleteForm);
        }
    }

    public function editConfiguration()
    {
        if (null !== $response = $this->checkAuth(
            AdminResources::MODULE,
            [SchedulesModule::DOMAIN_NAME],
            AccessManager::UPDATE
        )) {
            return $response;
        }

        $form = $this->createForm(SchedulesModule::SCHEDULES_FORM_CONFIGURATION);
        $error_message = null;

        try {
            $validateForm = $this->validateForm($form);
            $data = $validateForm->getData();

            SchedulesModule::setConfigValue('template', $data['template']);

            return $this->redirectToConfigurationPage();

        } catch (FormValidationException $e) {
            $error_message = $this->createStandardFormValidationErrorMessage($e);
        }

        if (null !== $error_message) {
            $this->setupFormErrorContext(
                'configuration',
                $error_message,
                $form
            );
            $response = $this->render("module-configure", ['module_code' => 'Schedules']);
        }
        return $response;
    }

    /**
     * Redirect to the configuration page
     */
    protected function redirectToConfigurationPage()
    {
        return RedirectResponse::create(URL::getInstance()->absoluteUrl('/admin/module/Schedules'));
    }
}
