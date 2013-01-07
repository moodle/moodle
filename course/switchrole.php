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
 * The purpose of this file is to allow the user to switch roles and be redirected
 * back to the page that they were on.
 *
 * This functionality is also supported in {@link /course/view.php} in order to comply
 * with backwards compatibility
 * The reason that we created this file was so that user didn't get redirected back
 * to the course view page only to be redirected again.
 *
 * @since 2.0
 * @package course
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once($CFG->dirroot.'/course/lib.php');

$id         = required_param('id', PARAM_INT);
$switchrole = optional_param('switchrole',-1, PARAM_INT);
$returnurl  = optional_param('returnurl', false, PARAM_LOCALURL);

$PAGE->set_url('/course/switchrole.php', array('id'=>$id));

if (!confirm_sesskey()) {
    print_error('confirmsesskeybad', 'error');
}

if (! ($course = $DB->get_record('course', array('id'=>$id)))) {
    print_error('invalidcourseid', 'error');
}

$context = context_course::instance($course->id);

// Remove any switched roles before checking login
if ($switchrole == 0) {
    role_switch($switchrole, $context);
}
require_login($course);

// Switchrole - sanity check in cost-order...
if ($switchrole > 0 && has_capability('moodle/role:switchroles', $context)) {
    // is this role assignable in this context?
    // inquiring minds want to know...
    $aroles = get_switchable_roles($context);
    if (is_array($aroles) && isset($aroles[$switchrole])) {
        role_switch($switchrole, $context);
        // Double check that this role is allowed here
        require_login($course);
    }
}

// TODO: Using SESSION->returnurl is deprecated and should be removed in the future.
// Till then this code remains to support any external applications calling this script.
if (!empty($returnurl) && is_numeric($returnurl)) {
    $returnurl = false;
    if (!empty($SESSION->returnurl) && strpos($SESSION->returnurl, 'moodle_url')!==false) {
        debugging('Code calling switchrole should be passing a URL as a param.', DEBUG_DEVELOPER);
        $returnurl = @unserialize($SESSION->returnurl);
        if (!($returnurl instanceof moodle_url)) {
            $returnurl = false;
        }
    }
}

if ($returnurl === false) {
    $returnurl = new moodle_url('/course/view.php', array('id' => $course->id));
}

redirect($returnurl);
