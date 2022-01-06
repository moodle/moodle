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
 * This file is used to display a categories sub categories, external pages, and settings.
 *
 * @package    admin
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once("{$CFG->libdir}/adminlib.php");

$contextid = required_param('id', PARAM_INT);
$confirm = optional_param('confirm', null, PARAM_INT);
$returnurl = optional_param('returnurl', null, PARAM_LOCALURL);

$PAGE->set_url('/admin/lock.php', ['id' => $contextid]);

list($context, $course, $cm) = get_context_info_array($contextid);

$parentcontext = $context->get_parent_context();
if ($parentcontext && !empty($parentcontext->locked)) {
    // Can't make changes to a context whose parent is locked.
    throw new \coding_exception('Not sure how you got here');
}

if ($course) {
    $isfrontpage = ($course->id == SITEID);
} else {
    $isfrontpage = false;
    $course = $SITE;
}

require_login($course, false, $cm);
require_capability('moodle/site:managecontextlocks', $context);

$PAGE->set_pagelayout('admin');
$PAGE->navigation->clear_cache();

$a = (object) [
    'contextname' => $context->get_context_name(),
];

if (null !== $confirm && confirm_sesskey()) {
    $context->set_locked(!empty($confirm));

    if ($context->locked) {
        $lockmessage = get_string('managecontextlocklocked', 'admin', $a);
    } else {
        $lockmessage = get_string('managecontextlockunlocked', 'admin', $a);
    }

    if (empty($returnurl)) {
        $returnurl = $context->get_url();
    } else {
        $returnurl = new moodle_url($returnurl);
    }
    redirect($returnurl, $lockmessage);
}

$heading = get_string('managecontextlock', 'admin');
$PAGE->set_title($heading);
$PAGE->set_heading($heading);

echo $OUTPUT->header();

if ($context->locked) {
    $confirmstring = get_string('confirmcontextunlock', 'admin', $a);
    $target = 0;
} else {
    $confirmstring = get_string('confirmcontextlock', 'admin', $a);
    $target = 1;
}

$confirmurl = new \moodle_url($PAGE->url, ['confirm' => $target]);
if (!empty($returnurl)) {
    $confirmurl->param('returnurl', $returnurl);
}

echo $OUTPUT->confirm($confirmstring, $confirmurl, $context->get_url());
echo $OUTPUT->footer();
