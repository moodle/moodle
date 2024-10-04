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
 * View all BigBlueButton instances in this course.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @author    Fred Dixon  (ffdixon [at] blindsidenetworks [dt] com)
 */

use core\notification;
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\output\index;
use mod_bigbluebuttonbn\plugin;

require(__DIR__.'/../../config.php');
global $PAGE, $OUTPUT;
$id = required_param('id', PARAM_INT);
$course = get_course($id);
require_login($course, true);

$PAGE->set_url('/mod/bigbluebuttonbn/index.php', ['id' => $id]);
$PAGE->set_title(get_string('modulename', plugin::COMPONENT));
$PAGE->set_heading($course->fullname);
$PAGE->set_cacheable(false);
$PAGE->set_pagelayout('incourse');

$PAGE->navbar->add($PAGE->title, $PAGE->url);

$instances = instance::get_all_instances_in_course($course->id);
if (empty($instances)) {
    notification::add(
        get_string('index_error_noinstances', plugin::COMPONENT),
        notification::ERROR
    );
    redirect(new moodle_url('/course/view.php', ['id' => $course->id]));
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('index_heading', plugin::COMPONENT));
$renderer = $PAGE->get_renderer(plugin::COMPONENT);
echo $renderer->render(new index($course, $instances));
echo $OUTPUT->footer();
