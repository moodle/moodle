<?php
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// This file is part of Moodle - http://moodle.org/                      //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//                                                                       //
// Moodle is free software: you can redistribute it and/or modify        //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation, either version 3 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// Moodle is distributed in the hope that it will be useful,             //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details.                          //
//                                                                       //
// You should have received a copy of the GNU General Public License     //
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.       //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/*
 * @package    moodle
 * @subpackage registration
 * @author     Jerome Mouneyrac <jerome@mouneyrac.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * The administrator is redirect to this page from the hub to confirm that the
 * site has been registered. It is an administration page. The administrator
 * should be using the same browser during all the registration process.
 * This page save the token that the hub gave us, in order to call the hub
 * directory later by web service.
*/

require('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/lib/hublib.php');

admin_externalpage_setup('siteregistrationconfirmed');

$newtoken        = optional_param('newtoken', '', PARAM_ALPHANUM);
$url             = optional_param('url', '', PARAM_URL);
$token           = optional_param('token', '', PARAM_ALPHANUM);

$hub = new hub();

//check that the token/url couple exist and is not confirmed
$registeredhub = $hub->get_registeredhub($url);
if (!empty($registeredhub) and  $registeredhub->confirmed == 0
        and $registeredhub->token == $token) {

    $registeredhub->token = $newtoken;
    $registeredhub->confirmed = 1;
    $hub->update_registeredhub($registeredhub);

    echo $OUTPUT->header();
    echo $OUTPUT->notification(get_string('registrationconfirmed', 'hub'), 'notifysuccess');
    echo $OUTPUT->footer();
} else {
    throw new moodle_exception('wrongtoken');
}


