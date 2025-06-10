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
 * Edit config page.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2020
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_intellidata\helpers\TrackingHelper;
use local_intellidata\output\forms\local_intellidata_edit_config;
use local_intellidata\services\datatypes_service;
use local_intellidata\services\config_service;
use local_intellidata\helpers\SettingsHelper;
use local_intellidata\repositories\export_log_repository;
use local_intellidata\repositories\config_repository;
use local_intellidata\task\create_index_adhoc_task;
use local_intellidata\task\delete_index_adhoc_task;

require_once('../../../config.php');

$datatype = required_param('datatype', PARAM_TEXT);
$action = optional_param('action', '', PARAM_TEXT);

require_login();

if (!empty($action)) {
    require_sesskey();
}

$context = context_system::instance();
require_capability('local/intellidata:editconfig', $context);

$returnurl = new \moodle_url('/local/intellidata/config/index.php');
$pageurl = new \moodle_url('/local/intellidata/config/edit.php', ['datatype' => $datatype]);
$PAGE->set_url($pageurl);
$PAGE->set_context($context);
$PAGE->set_pagelayout(SettingsHelper::get_page_layout());

$configrepository = new config_repository();
$record = $configrepository->get_record(['datatype' => $datatype]);

if (!$record) {
    throw new \moodle_exception('wrongdatatype', 'local_intellidata');
}

$exportlogrepository = new export_log_repository();
$configservice = new config_service();
if ($action == 'reset') {
    $configservice->reset_config_datatype($record);

    redirect($returnurl, get_string('resetmsg', 'local_intellidata'));

} else if ($action == 'createindex') {

    $record->set('tableindex', $record->get('timemodified_field'));
    $configrepository->save($record->get('datatype'), $record->to_record());

    // Cache config after deletion.
    $configrepository->cache_config();

    $createindextask = new create_index_adhoc_task();
    $createindextask->set_custom_data([
        'datatype' => $record->get('datatype'),
    ]);
    \core\task\manager::queue_adhoc_task($createindextask);

    redirect($returnurl, get_string('taskaddedforindexcreation', 'local_intellidata'));

} else if ($action == 'deleteindex') {

    $deleteindextask = new delete_index_adhoc_task();
    $deleteindextask->set_custom_data([
        'datatype' => $record->get('datatype'),
        'tableindex' => $record->get('tableindex'),
    ]);
    \core\task\manager::queue_adhoc_task($deleteindextask);

    $record->set('tableindex', '');
    $configrepository->save($record->get('datatype'), $record->to_record());

    // Cache config after deletion.
    $configrepository->cache_config();

    redirect($returnurl, get_string('taskaddedforindexdeletion', 'local_intellidata'));
}
$isrequired = $record->is_required_by_default();

$datatypeconfig = datatypes_service::get_datatype($datatype);
if (!$isrequired) {
    $datatypeconfig['timemodifiedfields'] = config_service::get_available_timemodified_fields($datatypeconfig['table']);
}

$exportlog = $exportlogrepository->get_datatype_export_log($datatype);

$title = get_string('editconfigfor', 'local_intellidata', $datatype);

$PAGE->navbar->add(get_string('configuration', 'local_intellidata'), $returnurl);
$PAGE->navbar->add($title);
$PAGE->set_title($title);
$PAGE->set_heading($title);

$recorddata = $record->to_record();
if (TrackingHelper::new_tracking_enabled()) {
    $recorddata->filterbyid = 0;
    $recorddata->timemodified_field = '';
}

$editform = new local_intellidata_edit_config(null, [
    'data' => $recorddata,
    'config' => (object)$datatypeconfig,
    'exportlog' => $exportlog,
    'is_required' => $isrequired,
]);

if ($editform->is_cancelled()) {
    redirect($returnurl);
} else if ($data = $editform->get_data()) {

    if (!empty($data->reset)) {
        $data = $configservice->create_config($datatype, $datatypeconfig);
        $returnurl = $pageurl;
    } else {
        $configservice->save_config($record, $data, $datatypeconfig);
    }

    \local_intellidata\services\datatypes_service::get_datatypes(true, true);

    redirect($returnurl, get_string('configurationsaved', 'local_intellidata'));
}

echo $OUTPUT->header();
echo $OUTPUT->heading($title);

echo $editform->display();

echo $OUTPUT->footer();
