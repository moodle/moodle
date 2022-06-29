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
 * Page to migrate frameworks.
 *
 * @package    tool_lpmigrate
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

$context = context_system::instance();
require_login(null, false);
require_capability('tool/lpmigrate:frameworksmigrate', $context);

$url = new moodle_url('/admin/tool/lpmigrate/frameworks.php');
$title = get_string('migrateframeworks', 'tool_lpmigrate');
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading(get_string('pluginname', 'tool_lpmigrate'));

$form = new \tool_lpmigrate\form\migrate_framework($context);
if ($form->is_cancelled()) {
    redirect($url);
}

$output = $PAGE->get_renderer('tool_lpmigrate');
echo $output->header();
echo $output->heading($title);

if ($data = $form->get_data()) {

    // Map competencies from both framework.
    $mapper = new \tool_lpmigrate\framework_mapper($data->from, $data->to);
    $mapper->automap();

    $progress = new \core\progress\display();
    $progress->set_display_names(true);

    $processor = new \tool_lpmigrate\framework_processor($mapper, $progress);
    if (!empty($data->allowedcourses)) {
        $processor->set_allowedcourses($data->allowedcourses);
    }
    if (!empty($data->disallowedcourses)) {
        $processor->set_disallowedcourses($data->disallowedcourses);
    }
    $processor->set_course_start_date_from($data->coursestartdate);
    $processor->proceed();

    $unmappedfrom = $mapper->get_unmapped_objects_from();
    $unmappedto = $mapper->get_unmapped_objects_to();
    $renderable = new \tool_lpmigrate\output\migrate_framework_results($context, $processor,
        \core_competency\api::read_framework($data->from), \core_competency\api::read_framework($data->to),
        $unmappedfrom, $unmappedto);
    echo $output->render($renderable);

} else {
    echo html_writer::tag('p', get_string('explanation', 'tool_lpmigrate'));
    $form->display();
}

echo $output->footer();
