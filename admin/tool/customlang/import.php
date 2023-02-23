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
 * Import custom lang files.
 *
 * @package    tool_customlang
 * @subpackage customlang
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_customlang\form\import;
use tool_customlang\local\importer;

require(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot.'/'.$CFG->admin.'/tool/customlang/locallib.php');
require_once($CFG->libdir.'/adminlib.php');

require_login(SITEID, false);
require_capability('tool/customlang:edit', context_system::instance());

$lng = required_param('lng', PARAM_LANG);

admin_externalpage_setup('toolcustomlang', '', null,
    new moodle_url('/admin/tool/customlang/import.php', ['lng' => $lng]));

$PAGE->set_context(context_system::instance());

$PAGE->set_secondary_active_tab('siteadminnode');
$PAGE->set_primary_active_tab('siteadminnode');
$PAGE->navbar->add(get_string('import', 'tool_customlang'), $PAGE->url);

$output = $PAGE->get_renderer('tool_customlang');

$form = new import(null, ['lng' => $lng]);
if ($data = $form->get_data()) {
    require_sesskey();

    // Get the file from the users draft area.
    $usercontext = context_user::instance($USER->id);
    $fs = get_file_storage();
    $files = $fs->get_area_files($usercontext->id, 'user', 'draft', $data->pack, 'id',
        false);

    // Send files to the importer.
    $importer = new importer($data->lng, $data->importmode);
    $importer->import($files);

    echo $output->header();

    // Display logs.
    $log = $importer->get_log();
    foreach ($log as $message) {
        echo $output->notification($message->get_message(), $message->errorlevel);
    }

    // Show continue button.
    echo $output->continue_button(new moodle_url('index.php', array('lng' => $lng)));

} else {
    echo $output->header();

    $form->display();
}

echo $OUTPUT->footer();
