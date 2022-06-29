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
 * At this page, teachers allocate submissions to students for a review
 *
 * The allocation logic itself is delegated to allocators - subplugins in ./allocation
 * folder.
 *
 * @package    mod_workshop
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/locallib.php');
require_once(__DIR__.'/allocation/lib.php');

$cmid       = required_param('cmid', PARAM_INT);                    // course module
$method     = optional_param('method', 'manual', PARAM_ALPHA);      // method to use

$cm         = get_coursemodule_from_id('workshop', $cmid, 0, false, MUST_EXIST);
$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$workshop   = $DB->get_record('workshop', array('id' => $cm->instance), '*', MUST_EXIST);
$workshop   = new workshop($workshop, $cm, $course);

$url = $workshop->allocation_url($method);
$PAGE->set_url($url);

require_login($course, false, $cm);
$context = $PAGE->context;
require_capability('mod/workshop:allocate', $context);

$PAGE->set_title($workshop->name);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add(get_string('allocation', 'workshop'), $workshop->allocation_url($method));
$PAGE->activityheader->set_attrs([
    'hidecompletion' => true,
    'description' => ''
]);

$allocator  = $workshop->allocator_instance($method);
$initresult = $allocator->init();

//
// Output starts here
//
$actionbar = new \mod_workshop\output\actionbar($url, $workshop);

$output = $PAGE->get_renderer('mod_workshop');
echo $output->header();
echo $output->render_allocation_menu($actionbar);

if (is_null($initresult->get_status()) or $initresult->get_status() == workshop_allocation_result::STATUS_VOID) {
    echo $output->container_start('allocator-ui');
    echo $allocator->ui();
    echo $output->container_end();
} else {
    echo $output->container_start('allocator-init-results');
    echo $output->render($initresult);
    echo $output->continue_button($workshop->allocation_url($method));
    echo $output->container_end();
}
echo $output->footer();
