<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/*
 * @package    moodle
 * @subpackage registration
 * @author     Jerome Mouneyrac <jerome@mouneyrac.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * Thsi page displays a hub selector or a hub URL + password. Then it will redirect to
 * the site registration form (with the selected hub as parameter)
*/

require('../../config.php');

require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/' . $CFG->admin . '/registration/forms.php');

admin_externalpage_setup('registrationselector');

$hubselectorform = new hub_selector_form();
$fromform = $hubselectorform->get_data();

//// Redirect to the registration form if an URL has been choosen ////

$selectedhuburl = optional_param('publichub', false, PARAM_URL);
$unlistedhuburl = optional_param('unlistedurl', false, PARAM_TEXT);
$password = optional_param('password', '', PARAM_RAW);

if (!empty($unlistedhuburl)) {
    if (clean_param($unlistedhuburl, PARAM_URL) !== '') {
        $huburl = $unlistedhuburl;
    }
} else if (!empty($selectedhuburl)) {
    $huburl = $selectedhuburl;
}


//redirect
if (!empty($huburl) and confirm_sesskey()) {
    $hubname = optional_param(clean_param($huburl, PARAM_ALPHANUMEXT), '', PARAM_TEXT);
    $params = array('sesskey' => sesskey(), 'huburl' => $huburl,
            'password' => $password, 'hubname' => $hubname);
    redirect(new moodle_url($CFG->wwwroot."/" . $CFG->admin . "/registration/register.php",
            $params));
}


//// OUTPUT ////

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('registeron', 'hub'), 3, 'main');
$hubselectorform->display();
echo $OUTPUT->footer();