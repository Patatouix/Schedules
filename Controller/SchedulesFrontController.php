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

use Symfony\Component\HttpFoundation\Response;
use Thelia\Controller\Front\BaseFrontController;

/**
 * Class SchedulesProductController
 * @package Schedules\Controller
 * @author Thierry Caresmel <thierry@pixel-plurimedia.fr>
 */
class SchedulesFrontController extends BaseFrontController
{
    public function getAgenda()
    {
        return new Response($this->renderRaw('agenda.ics'), 200, [
            'Content-type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename=agenda.ics'
        ]);
    }
}
