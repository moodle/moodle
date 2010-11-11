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
 * This file is part of the User section Moodle
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package user
 */

require_once('../config.php');
require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/resourcelib.php');

$agree = optional_param('agree', 0, PARAM_BOOL);

$PAGE->set_url('/user/policy.php');
$PAGE->set_popup_notification_allowed(false);

if (!isloggedin()) {
    require_login();
}

if (isguestuser()) {
    $sitepolicy = $CFG->sitepolicyguest;
} else {
    $sitepolicy = $CFG->sitepolicy;
}

if (!empty($SESSION->wantsurl)) {
    $return = $SESSION->wantsurl;
} else {
    $return = $CFG->wwwroot.'/';
}

if (empty($sitepolicy)) {
    // nothing to agree to, sorry, hopefully we will not get to infinite loop
    redirect($return);
}

if ($agree and confirm_sesskey()) {    // User has agreed
    if (!isguestuser()) {              // Don't remember guests
        $DB->set_field('user', 'policyagreed', 1, array('id'=>$USER->id));
    }
    $USER->policyagreed = 1;
    unset($SESSION->wantsurl);
    redirect($return);
}

$strpolicyagree = get_string('policyagree');
$strpolicyagreement = get_string('policyagreement');
$strpolicyagreementclick = get_string('policyagreementclick');

$PAGE->set_context(get_context_instance(CONTEXT_SYSTEM));
$PAGE->set_title($strpolicyagreement);
$PAGE->set_heading($SITE->fullname);
$PAGE->navbar->add($strpolicyagreement);

echo $OUTPUT->header();
echo $OUTPUT->heading($strpolicyagreement);

$mimetype = mimeinfo('type', $sitepolicy);
if ($mimetype == 'document/unknown') {
    //fallback for missing index.php, index.html
    $mimetype = 'text/html';
}

// we can not use our popups here, because the url may be arbitrary, see MDL-9823
$clicktoopen = '<a href="'.$sitepolicy.'" onclick="this.target=\'_blank\'">'.$strpolicyagreementclick.'</a>';

echo '<div class="noticebox">';
echo resourcelib_embed_general($sitepolicy, $strpolicyagreement, $clicktoopen, $mimetype);
echo '</div>';

$formcontinue = new single_button(new moodle_url('policy.php', array('agree'=>1)), get_string('yes'));
$formcancel = new single_button(new moodle_url($CFG->wwwroot.'/login/logout.php', array('agree'=>0)), get_string('no'));
echo $OUTPUT->confirm($strpolicyagree, $formcontinue, $formcancel);

echo $OUTPUT->footer();

