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
 * This page lets users manage default purposes and categories.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/tool/dataprivacy/lib.php');

require_login(null, false);

$url = new \moodle_url('/admin/tool/dataprivacy/defaults.php');
$title = get_string('setdefaults', 'tool_dataprivacy');

\tool_dataprivacy\page_helper::setup($url, $title, 'dataregistry');

$levels = \context_helper::get_all_levels();
// They are set through the context level site and user.
unset($levels[CONTEXT_SYSTEM]);
unset($levels[CONTEXT_USER]);

$customdata = [
    'levels' => $levels,
    'purposes' => \tool_dataprivacy\api::get_purposes(),
    'categories' => \tool_dataprivacy\api::get_categories(),
];
$form = new \tool_dataprivacy\form\defaults($PAGE->url->out(false), $customdata);

$toform = new stdClass();
foreach ($levels as $level => $classname) {
    list($purposevar, $categoryvar) = \tool_dataprivacy\data_registry::var_names_from_context($classname);
    $toform->{$purposevar} = get_config('tool_dataprivacy', $purposevar);
    $toform->{$categoryvar} = get_config('tool_dataprivacy', $categoryvar);
}
$form->set_data($toform);

$returnurl = new \moodle_url('/admin/tool/dataprivacy/dataregistry.php');
if ($form->is_cancelled()) {
    redirect($returnurl);
} else if ($data = $form->get_data()) {

    foreach ($levels as $level => $classname) {

        list($purposevar, $categoryvar) = \tool_dataprivacy\data_registry::var_names_from_context($classname);

        if (isset($data->{$purposevar})) {
            set_config($purposevar, $data->{$purposevar}, 'tool_dataprivacy');
        }
        if (isset($data->{$categoryvar})) {
            set_config($categoryvar, $data->{$categoryvar}, 'tool_dataprivacy');
        }
    }
    redirect($returnurl, get_string('defaultssaved', 'tool_dataprivacy'),
        0, \core\output\notification::NOTIFY_SUCCESS);
}

$output = $PAGE->get_renderer('tool_dataprivacy');
echo $output->header();
$form->display();
echo $output->footer();
