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
 * Site recommendations for the activity chooser.
 *
 * @package    core_course
 * @copyright  2020 Adrian Greeve
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../config.php");

$search = optional_param('search', '', PARAM_TEXT);

$context = context_system::instance();
$url = new moodle_url('/course/recommendations.php');

$pageheading = format_string($SITE->fullname, true, ['context' => $context]);

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');

$PAGE->set_title(get_string('activitychooserrecommendations', 'course'));
$PAGE->set_heading($pageheading);

require_login();
require_capability('moodle/course:recommendactivity', $context);

$renderer = $PAGE->get_renderer('core_course', 'recommendations');

echo $renderer->header();
echo $renderer->heading(get_string('activitychooserrecommendations', 'course'));

$manager = \core_course\local\factory\content_item_service_factory::get_content_item_service();
if (!empty($search)) {
    $modules = $manager->get_content_items_by_name_pattern($USER, $search);
} else {
    $modules = $manager->get_all_content_items($USER);
}

$activitylist = new \core_course\output\recommendations\activity_list($modules, $search);

echo $renderer->render_activity_list($activitylist);

echo $renderer->footer();
