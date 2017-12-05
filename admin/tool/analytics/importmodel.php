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
 * Import models tool frontend.
 *
 * @package tool_analytics
 * @copyright 2017 onwards Ankit Agarwal
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('analyticsmodelimport', '', null, '', array('pagelayout' => 'report'));
echo $OUTPUT->header();

$form = new tool_analytics\import_model_form();
if ($data = $form->get_data()) {
    $content = json_decode($form->get_file_content('modelfile'));
    if (empty($content->moodleversion)) {
        // Should never happen.
        echo $OUTPUT->notification(get_string('missingmoodleversion', 'tool_analytics'), 'error');
    } else {
        if ($content->moodleversion != $CFG->version) {
            $a = new stdClass();
            $a->importedversion = $content->moodleversion;
            $a->version = $CFG->version;
            echo $OUTPUT->notification(get_string('versionnotsame', 'tool_analytics', $a), 'warning');
        }
        $model = \core_analytics\model::create_from_json($content);
        if ($model) {
            echo $OUTPUT->notification(get_string('success'), 'notifysuccess');
        } else {
            echo $OUTPUT->notification(get_string('error'), 'error');
        }
    }
    echo $OUTPUT->single_button(new moodle_url("$CFG->wwwroot/$CFG->admin/tool/analytics/index.php"),
        get_string('continue'), 'get');
} else {
    $form->display();
}

echo $OUTPUT->footer();