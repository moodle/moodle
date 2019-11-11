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

require_login();
\core_analytics\manager::check_can_manage_models();

if (!\core_analytics\manager::is_analytics_enabled()) {
    $PAGE->set_context(\context_system::instance());
    $renderer = $PAGE->get_renderer('tool_analytics');
    echo $renderer->render_analytics_disabled();
    exit(0);
}

$returnurl = new \moodle_url('/admin/tool/analytics/index.php');
$url = new \moodle_url('/admin/tool/analytics/importmodel.php');
$title = get_string('importmodel', 'tool_analytics');

\tool_analytics\output\helper::set_navbar($title, $url);

$form = new \tool_analytics\output\form\import_model();
if ($form->is_cancelled()) {
    redirect($returnurl);
} else if ($data = $form->get_data()) {

    $modelconfig = new \core_analytics\model_config();

    $zipfilepath = $form->save_temp_file('modelfile');

    list ($modeldata, $unused) = $modelconfig->extract_import_contents($zipfilepath);

    if ($error = $modelconfig->check_dependencies($modeldata, $data->ignoreversionmismatches)) {
        // The file is not available until the form is validated so we need an alternative method to show errors.
        redirect($url, $error, 0, \core\output\notification::NOTIFY_ERROR);
    }
    \core_analytics\model::import_model($zipfilepath);

    redirect($returnurl, get_string('importedsuccessfully', 'tool_analytics'), 0,
        \core\output\notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();
$form->display();
echo $OUTPUT->footer();
