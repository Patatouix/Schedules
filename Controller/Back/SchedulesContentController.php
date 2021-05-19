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

namespace Schedules\Controller\Back;

use Thelia\Controller\Admin\ContentController;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Security\AccessManager;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Tools\URL;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Propel\Runtime\Propel;

use Schedules\Schedules as SchedulesModule;
use Schedules\Model\ContentSchedule;
use Schedules\Model\ContentScheduleQuery;
use Schedules\Model\Schedule;
use Schedules\Model\ScheduleQuery;

/**
 * Class SchedulesContentController
 * @package Schedules\Controller
 * @author Thierry Caresmel <thierry@pixel-plurimedia.fr>
 */
class SchedulesContentController extends ContentController
{
    protected $service;

    /**
     * Create an object
     * @return mixed|\Symfony\Component\HttpFoundation\Response
     */
    public function createScheduleAction()
    {
        // Check current user authorization
        if (null !== $response = $this->checkAuth([], SchedulesModule::getModuleCode(), AccessManager::CREATE))
        {
            return $response;
        }

        // Create the Creation Form
        $creationForm = $this->createForm('schedules.content.create');

        $con = Propel::getConnection();
        $con->beginTransaction();

        try {
            // Check the form against constraints violations
            $form = $this->validateForm($creationForm, "POST");
            // Get the form field values
            $data = $form->getData();

            if (empty($data["day"])) {
                $dataAM = $this->formatData($data);
                $dataPM = $this->formatData($data, 'PM');

                if ($this->hasNullDate($dataAM) && $this->hasNullDate($dataPM)) {
                    $this->hydrateObjectArray($dataAM);
                } else {
                    if (!$this->hasNullDate($dataAM)) {
                        $this->hydrateObjectArray($dataAM);
                    }
                    if (!$this->hasNullDate($dataPM)) {
                        $this->hydrateObjectArray($dataPM);
                    }
                }
            } else {
                foreach ($data["day"] as $day) {
                    $currentData = $data;
                    $currentData["day"] = $day;
                    $dataAM = $this->formatData($currentData);
                    $dataPM = $this->formatData($currentData, 'PM');

                    if ($this->hasNullDate($dataAM) && $this->hasNullDate($dataPM)) {
                        $this->hydrateObjectArray($dataAM);
                    } else {
                        if (!$this->hasNullDate($dataAM)) {
                            $this->hydrateObjectArray($dataAM);
                        }
                        if (!$this->hasNullDate($dataPM)) {
                            $this->hydrateObjectArray($dataPM);
                        }
                    }
                }
            }

            // Substitute _ID_ in the URL with the ID of the created object
            $successUrl = $creationForm->getSuccessUrl();

            $con->commit();

            // Redirect to the success URL
            return $this->generateRedirect($successUrl);

        } catch (FormValidationException $ex) {
            $con->rollBack();
            // Form cannot be validated
            $error_msg = $this->createStandardFormValidationErrorMessage($ex);

        } catch (\Exception $ex) {
            $con->rollBack();
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
        if (null !== $response = $this->checkAuth(AdminResources::CONTENT, SchedulesModule::getModuleCode(), AccessManager::UPDATE))
        {
            return $response;
        }

        // Error (Default: false)
        $error_msg = false;
        // Create the Form from the request
        $changeForm = $this->createForm('schedules.content.update');

        $con = Propel::getConnection();
        $con->beginTransaction();

        try {
            // Check the form against constraints violations
            $form = $this->validateForm($changeForm, "POST");

            // Get the form field values
            $data = $form->getData();

            $updatedObject = $this->hydrateObjectArray($data);

            // Check if object exist
            if (! $updatedObject) {
                throw new \LogicException(
                    $this->getTranslator()->trans("No %obj was updated.", ['%obj' => SchedulesModule::DOMAIN_NAME])
                );
            }

            $con->commit();

            // Redirect to the success URL
            return $this->generateSuccessRedirect($changeForm);

        } catch (FormValidationException $ex) {
            $con->rollBack();
            // Form cannot be validated
            $error_msg = $this->createStandardFormValidationErrorMessage($ex);

        } catch (\Exception $ex) {
            $con->rollBack();
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

        // Create the Creation Form
        $cloneForm = $this->createForm('schedules.content.clone');

        $con = Propel::getConnection();
        $con->beginTransaction();

        try {
            // Check the form against constraints violations
            $form = $this->validateForm($cloneForm, "POST");
            // Get the form field values
            $data = $form->getData();

            $this->hydrateObjectArray($data, true);

            // Substitute _ID_ in the URL with the ID of the created object
            $successUrl = $cloneForm->getSuccessUrl();

            $con->commit();

            // Redirect to the success URL
            return $this->generateRedirect($successUrl);

        } catch (FormValidationException $ex) {
            $con->rollBack();
            // Form cannot be validated
            $error_msg = $this->createStandardFormValidationErrorMessage($ex);

        } catch (\Exception $ex) {
            $con->rollBack();
            // Any other error
            $error_msg = $ex->getMessage();
        }

        if (false !== $error_msg) {
            $this->setupFormErrorContext(
                $this->getTranslator()->trans("%obj creation", ['%obj' => SchedulesModule::DOMAIN_NAME]),
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
        if (null !== $response = $this->checkAuth(AdminResources::CONTENT, SchedulesModule::getModuleCode(), AccessManager::DELETE))
        {
            return $response;
        }

        $con = Propel::getConnection();
        $con->beginTransaction();

        try {
            // Check token
            $this->getTokenProvider()->checkToken(
                $this->getRequest()->query->get("_token")
            );

            if (null != $contentSchedule = ContentScheduleQuery::create()->findPk($this->getRequest()->request->get("schedule_id"))) {
                $contentSchedule->delete();
            }

            $con->commit();

            // Redirect to the success URL
            return $this->generateRedirect($this->getRequest()->request->get("success_url"));

        } catch (\Exception $e) {
            $con->rollBack();
            return $this->renderAfterDeleteError($e);
        }
    }

    protected function formatData($data, $type = "AM")
    {
        $retour = $data;
        if (isset($data["begin" . $type]) && $data["begin" . $type] != "") {
            $retour["begin"] = $data["begin" . $type];
        } else {
            $retour["begin"] = null;
        }

        if (isset($data["end" . $type]) && $data["end" . $type] != "") {
            $retour["end"] = $data["end" . $type];
        } else {
            $retour["end"] = null;
        }

        return $retour;
    }

    protected function hasNullDate($data)
    {
        return !($data["begin"] && $data["end"]);
    }

    protected function hydrateObjectArray($data, $clone = false)
    {
        $schedule = new Schedule();
        $contentSchedule = new ContentSchedule();

        if (isset($data['schedule_id']) && (null != $existingSchedule = ScheduleQuery::create()->findPk($data['schedule_id']))) {
            $schedule = $existingSchedule;
            $contentSchedule = $schedule->getContentSchedule();

            if (true === $clone) {
                $schedule = $schedule->copy();
                $contentSchedule = $contentSchedule->copy();
            }
        }

        // set schedule
        if (array_key_exists('day', $data) && $data['day'] !== array()) {
            $schedule->setDay($data['day']);
        }
        if (isset($data['begin'])) {
            $schedule->setBegin($data['begin']);
        }
        if (isset($data['end'])) {
            $schedule->setEnd($data['end']);
        }
        if (isset($data['period_begin'])) {
            $schedule->setPeriodBegin($data['period_begin']);
        }
        if (isset($data['period_end'])) {
            $schedule->setPeriodEnd($data['period_end']);
        }
        if (isset($data['closed'])) {
            $schedule->setClosed($data['closed']);
        }
        $schedule->save();

        // set contentSchedule
        if (isset($data['content_id'])) {
            $contentSchedule->setContentId($data['content_id']);
        }
        $contentSchedule->setSchedule($schedule);
        $contentSchedule->save();

        return $contentSchedule;
    }
}
