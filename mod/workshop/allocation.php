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
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once(dirname(__FILE__).'/allocation/lib.php');

$cmid   = required_param('cmid', PARAM_INT);                    // course module
$method = optional_param('method', 'manual', PARAM_ALPHA);      // method to use

$PAGE->set_url('mod/workshop/allocation.php', array('cmid' => $cmid));

if (!$cm = get_coursemodule_from_id('workshop', $cmid)) {
    print_error('invalidcoursemodule');
}
if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
    print_error('coursemisconf');
}
if (!$workshop = $DB->get_record('workshop', array('id' => $cm->instance))) {
    print_error('err_invalidworkshopid', 'workshop');
}

$workshop = new workshop_api($workshop, $cm);

require_login($course, false, $cm);

$context = $PAGE->context;
require_capability('mod/workshop:allocate', $context);

$PAGE->set_title($workshop->name);
$PAGE->set_heading($course->fullname);

// todo navigation will be changed yet for Moodle 2.0
$navigation = build_navigation(get_string('allocation', 'workshop'), $cm);

$allocator = workshop_allocator_instance($workshop, $method);
try {
    $allocator->init();
} 
catch (moodle_workshop_exception $e) {
    echo $OUTPUT->header($navigation);
    throw $e;
}

//
// Output starts here
//
echo $OUTPUT->header($navigation);

$allocators = workshop_installed_allocators();
$tabrow = array();
foreach ($allocators as $methodid => $methodname) {
    $tabrow[] = new tabobject($methodid, "allocation.php?cmid={$cmid}&amp;method={$methodid}", $methodname);
}
print_tabs(array($tabrow), $method);

echo $OUTPUT->container_start('allocator allocator-' . $method);
echo $allocator->ui();
echo $OUTPUT->container_end();

echo $OUTPUT->footer();
