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

/**
 * This file launches LTI-tools enabled to be launched from a rich text editor
 *
 * @package    atto_panoptoltibutton
 * @copyright  2020 Panopto
 * @author     Panopto with contributions from David Shepard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__) . '/lib/panopto_lti_utility.php');

function init_panoptoltibutton_view() {
    global $CFG;
    if (empty($CFG)) {
        require_once(dirname(__FILE__) . '/../../../../../config.php');
    }
    require_once($CFG->dirroot . '/mod/lti/lib.php');
    require_once($CFG->dirroot . '/mod/lti/locallib.php');

    $courseid  = required_param('course', PARAM_INT);
    $resourcelinkid = required_param('resourcelinkid', PARAM_ALPHANUMEXT);
    $ltitypeid = required_param('ltitypeid', PARAM_INT);
    $contenturl = optional_param('contenturl', '', PARAM_URL);
    $customdata = optional_param('custom', '', PARAM_RAW_TRIMMED);

    $course = get_course($courseid);

    $context = context_course::instance($courseid);

    // Make sure $ltitypeid is valid.
    $ltitype = $DB->get_record('lti_types', ['id' => $ltitypeid], '*', MUST_EXIST);

    require_login($course);
    require_capability('mod/lti:view', $context);

    $lti = new stdClass();

    $lti->id = $resourcelinkid;
    $lti->typeid = $ltitypeid;
    $lti->launchcontainer = LTI_LAUNCH_CONTAINER_WINDOW;
    $lti->toolurl = $contenturl;
    $lti->custom = new stdClass();
    $lti->instructorcustomparameters = [];
    $lti->debuglaunch = false;
    if ($customdata) {
        $decoded = json_decode($customdata, true);
        
        foreach ($decoded as $key => $value) {
            $lti->custom->$key = $value;
        }
    }
    
    \panopto_lti_utility::panoptoltibutton_launch_tool($lti);
}

init_panoptoltibutton_view();

