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
 * Edit log entity page.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2020
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_intellidata\output\forms\local_intellidata_editlogsentity_config;
use local_intellidata\persistent\datatypeconfig;
use local_intellidata\helpers\SettingsHelper;
use local_intellidata\repositories\export_log_repository;
use local_intellidata\repositories\config_repository;
use local_intellidata\services\export_service;
use local_intellidata\task\export_adhoc_task;

require('../../../config.php');

$id = optional_param('id', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_TEXT);

require_login();

if (!empty($action)) {
    require_sesskey();
}

$context = context_system::instance();
require_capability('local/intellidata:editconfig', $context);

$returnurl = new \moodle_url('/local/intellidata/config/index.php');
$pageurl = new \moodle_url('/local/intellidata/config/editlogsentity.php', ['id' => $id]);
$PAGE->set_url($pageurl);
$PAGE->set_context($context);
$PAGE->set_pagelayout(SettingsHelper::get_page_layout());

$configrepository = new config_repository();
$record = $configrepository->get_record(['id' => $id]);

if ($record && $record->get('tabletype') != datatypeconfig::TABLETYPE_LOGS) {
    throw new \moodle_exception('wrongdatatype', 'local_intellidata');
} else if (!$record) {
    $record = new datatypeconfig();
}

$exportlogrepository = new export_log_repository();
$exportlog = $exportlogrepository->get_datatype_export_log($record->get('datatype'));

$title = ($record->get('datatype'))
    ? get_string('editconfigfor', 'local_intellidata', $record->get('datatype'))
    : get_string('createlogsdatatype', 'local_intellidata');


$PAGE->navbar->add(get_string('configuration', 'local_intellidata'), $returnurl);
$PAGE->navbar->add($title);
$PAGE->set_title($title);
$PAGE->set_heading($title);

$recorddata = $record->to_record();
$recorddata->params = (array)$record->get('params');

if ($action == 'reset' && $record->get('datatype')) {
    // Reset export logs.
    $exportlogrepository->reset_datatype($record->get('datatype'), datatypeconfig::TABLETYPE_LOGS);

    // Delete old export files.
    $exportservice = new export_service();
    $exportservice->delete_files([
        'datatype' => $record->get('datatype'),
        'timemodified' => time(),
    ]);

    // Add task to migrate records.
    $exporttask = new export_adhoc_task();
    $exporttask->set_custom_data([
        'datatypes' => [$record->get('datatype')],
    ]);
    \core\task\manager::queue_adhoc_task($exporttask);

    redirect($returnurl, get_string('resetmsg', 'local_intellidata'));

} else if ($action == 'delete' && $record->get('datatype')) {

    // Delete old export files.
    $exportservice = new export_service();
    $exportservice->delete_files([
        'datatype' => $record->get('datatype'),
        'timemodified' => time(),
    ]);

    if (!empty($exportlog)) {
        $exportlogrepository->remove_datatype($record->get('datatype'));
    }

    // Delete config record.
    $configrepository->delete($record->get('datatype'));

    // Cache config after deletion.
    $configrepository->cache_config();

    redirect($returnurl, get_string('deletemsg', 'local_intellidata'));
}

$editform = new local_intellidata_editlogsentity_config(null, [
    'data' => $recorddata,
    'exportlog' => $exportlog,
]);

if ($editform->is_cancelled()) {
    redirect($returnurl);
} else if ($data = $editform->get_data()) {

    $data->timemodified_field = 'timecreated';
    $data->rewritable = datatypeconfig::STATUS_DISABLED;
    $data->filterbyid = datatypeconfig::STATUS_DISABLED;

    $datatype = strtolower($data->datatype);

    $record->set('datatype', $datatype);
    $record->set('tabletype', datatypeconfig::TABLETYPE_LOGS);
    $record->set('events_tracking', datatypeconfig::STATUS_ENABLED);
    $record->set('timemodified_field', $data->timemodified_field);
    $record->set('filterbyid', $data->filterbyid);
    $record->set('rewritable', $data->rewritable);
    $record->set('status', $data->status);
    $record->set('params', json_encode($data->params));

    // Save config.
    $configrepository->save($datatype, $record->to_record());

    // Cache config after updates.
    $configrepository->cache_config();

    // Process export log.
    if ((!$data->enableexport || !$data->status) && !empty($exportlog)) {
        // Remove datatype from the export logs table.
        $exportlogrepository->remove_datatype($datatype);
    } else if (empty($exportlog) && $data->enableexport) {
        // Add datatype to the export logs table.
        $exportlogrepository->reset_datatype($datatype, datatypeconfig::TABLETYPE_LOGS);

        // Add task to migrate records.
        $exporttask = new export_adhoc_task();
        $exporttask->set_custom_data([
            'datatypes' => [$record->get('datatype')],
        ]);
        \core\task\manager::queue_adhoc_task($exporttask);
    }

    redirect($returnurl, get_string('configurationsaved', 'local_intellidata'));
}

echo $OUTPUT->header();
echo $OUTPUT->heading($title);

echo $editform->display();

echo $OUTPUT->footer();
