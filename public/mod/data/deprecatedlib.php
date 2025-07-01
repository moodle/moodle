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
 * List of deprecated mod_data functions.
 *
 * @package   mod_data
 * @copyright 2021 Jun Pataleta
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @deprecated since Moodle 4.1 MDL-75146 - please do not use this function any more.
 */
#[\core\attribute\deprecated(
    'mod_data\manager::get_template and mod_data\template::parse_entries',
    since: '4.1',
    mdl: 'MDL-75146',
    final: true,
)]
function data_print_template(): void {
    \core\deprecation::emit_deprecation(__FUNCTION__);
}

/**
 * @deprecated since Moodle 4.1 MDL-75148 - please, use the preset::get_name_from_plugin() function instead.
 */
#[\core\attribute\deprecated('mod_data\preset::get_name_from_plugin()', since: '4.1', mdl: 'MDL-75148', final: true)]
function data_preset_name(): void {
    \core\deprecation::emit_deprecation(__FUNCTION__);
}

/**
 * @deprecated since Moodle 4.1 MDL-75148 - please, use the manager::get_available_presets() function instead.
 */
#[\core\attribute\deprecated('mod_data\manager::get_available_presets()', since: '4.1', mdl: 'MDL-75148', final: true)]
function data_get_available_presets(): void {
    \core\deprecation::emit_deprecation(__FUNCTION__);
}

/**
 * @deprecated since Moodle 4.1 MDL-75148 - please, use the manager::get_available_saved_presets() function instead.
 */
#[\core\attribute\deprecated('mod_data\manager::get_available_saved_presets()', since: '4.1', mdl: 'MDL-75148', final: true)]
function data_get_available_site_presets(): void {
    \core\deprecation::emit_deprecation(__FUNCTION__);
}

/**
 * @deprecated since Moodle 4.1 MDL-75187 - please, use the preset::delete() function instead.
 */
#[\core\attribute\deprecated('mod_data\preset::delete()', since: '4.1', mdl: 'MDL-75187', final: true)]
function data_delete_site_preset(): void {
    \core\deprecation::emit_deprecation(__FUNCTION__);
}

/**
 * @deprecated since Moodle 4.1 MDL-75142 - please, use the preset::save() function instead.
 */
#[\core\attribute\deprecated('mod_data\preset::save()', since: '4.1', mdl: 'MDL-75142', final: true)]
function data_presets_save(): void {
    \core\deprecation::emit_deprecation(__FUNCTION__);
}

/**
 * @deprecated since Moodle 4.1 MDL-75142 - please, use the protected preset::generate_preset_xml() function instead.
 */
#[\core\attribute\deprecated('mod_data\preset::generate_preset_xml()', since: '4.1', mdl: 'MDL-75142', final: true)]
function data_presets_generate_xml(): void {
    \core\deprecation::emit_deprecation(__FUNCTION__);
}

/**
 * @deprecated since Moodle 4.1 MDL-75142 - please, use the preset::export() function instead.
 */
#[\core\attribute\deprecated('mod_data\preset::export()', since: '4.1', mdl: 'MDL-75142', final: true)]
function data_presets_export(): void {
    \core\deprecation::emit_deprecation(__FUNCTION__);
}

/**
 * @deprecated since Moodle 4.1 MDL-75187 - please, use the preset::can_manage() function instead.
 */
#[\core\attribute\deprecated('mod_data\preset::can_manage()', since: '4.1', mdl: 'MDL-75187', final: true)]
function data_user_can_delete_preset(): void {
    \core\deprecation::emit_deprecation(__FUNCTION__);
}

/**
 * @deprecated since Moodle 4.1 MDL-75146 - please do not use this function any more.
 */
#[\core\attribute\deprecated('mod_data\manager::set_module_viewed', since: '4.1', mdl: 'MDL-75146', final: true)]
function data_view(): void {
    \core\deprecation::emit_deprecation(__FUNCTION__);
}

/**
 * @deprecated since Moodle 4.1 MDL-75148 - please, use the preset::is_directory_a_preset() function instead.
 */
#[\core\attribute\deprecated('mod_data\preset::is_directory_a_preset()', since: '4.1', mdl: 'MDL-75148', final: true)]
function is_directory_a_preset(): void {
    \core\deprecation::emit_deprecation(__FUNCTION__);
}

/**
 * @deprecated since Moodle 4.3.
 * @global object
 * @param array $export
 * @param string $dataname
 * @param int $count
 * @return string
 */
function data_export_xls($export, $dataname, $count) {
    global $CFG;

    debugging('Function data_export_xls() has been deprecated, because xls export has been dropped.',
        DEBUG_DEVELOPER);
    require_once("$CFG->libdir/excellib.class.php");
    $filename = clean_filename("{$dataname}-{$count}_record");
    if ($count > 1) {
        $filename .= 's';
    }
    $filename .= clean_filename('-' . gmdate("Ymd_Hi"));
    $filename .= '.xls';

    $filearg = '-';
    $workbook = new MoodleExcelWorkbook($filearg);
    $workbook->send($filename);
    $worksheet = array();
    $worksheet[0] = $workbook->add_worksheet('');
    $rowno = 0;
    foreach ($export as $row) {
        $colno = 0;
        foreach($row as $col) {
            $worksheet[0]->write($rowno, $colno, $col);
            $colno++;
        }
        $rowno++;
    }
    $workbook->close();
    return $filename;
}

/**
 * @deprecated since Moodle 4.3, exporting is now being done by \mod_data\local\exporter\csv_entries_exporter
 * @global object
 * @param array $export
 * @param string $delimiter_name
 * @param object $database
 * @param int $count
 * @param bool $return
 * @return string|void
 */
function data_export_csv($export, $delimiter_name, $database, $count, $return=false) {
    global $CFG;

    debugging('Function data_export_csv has been deprecated. Exporting is now being done by '
        . '\mod_data\local\csv_exporter.', DEBUG_DEVELOPER);
    require_once($CFG->libdir . '/csvlib.class.php');

    $filename = $database . '-' . $count . '-record';
    if ($count > 1) {
        $filename .= 's';
    }
    if ($return) {
        return csv_export_writer::print_array($export, $delimiter_name, '"', true);
    } else {
        csv_export_writer::download_array($filename, $export, $delimiter_name);
    }
}

/**
 * @deprecated since Moodle 4.3, exporting is now being done by \mod_data\local\exporter\ods_entries_exporter
 * @global object
 * @param array $export
 * @param string $dataname
 * @param int $count
 * @param string
 */
function data_export_ods($export, $dataname, $count) {
    global $CFG;

    debugging('Function data_export_ods has been deprecated. Exporting is now being done by '
        . '\mod_data\local\ods_exporter.', DEBUG_DEVELOPER);
    require_once("$CFG->libdir/odslib.class.php");
    $filename = clean_filename("{$dataname}-{$count}_record");
    if ($count > 1) {
        $filename .= 's';
    }
    $filename .= clean_filename('-' . gmdate("Ymd_Hi"));
    $filename .= '.ods';
    $filearg = '-';
    $workbook = new MoodleODSWorkbook($filearg);
    $workbook->send($filename);
    $worksheet = array();
    $worksheet[0] = $workbook->add_worksheet('');
    $rowno = 0;
    foreach ($export as $row) {
        $colno = 0;
        foreach($row as $col) {
            $worksheet[0]->write($rowno, $colno, $col);
            $colno++;
        }
        $rowno++;
    }
    $workbook->close();
    return $filename;
}

/**
 * @deprecated since Moodle 4.3, use \mod_data\local\exporter\utils::data_exportdata with a \mod_data\local\exporter\entries_exporter object
 * @global object
 * @param int $dataid
 * @param array $fields
 * @param array $selectedfields
 * @param int $currentgroup group ID of the current group. This is used for
 * exporting data while maintaining group divisions.
 * @param object $context the context in which the operation is performed (for capability checks)
 * @param bool $userdetails whether to include the details of the record author
 * @param bool $time whether to include time created/modified
 * @param bool $approval whether to include approval status
 * @param bool $tags whether to include tags
 * @return array
 */
function data_get_exportdata($dataid, $fields, $selectedfields, $currentgroup=0, $context=null,
    $userdetails=false, $time=false, $approval=false, $tags = false) {
    global $DB;

    debugging('Function data_get_exportdata has been deprecated. Use '
        . '\mod_data\local\exporter_utils::data_exportdata with a \mod_data\local\exporter object instead',
        DEBUG_DEVELOPER);

    if (is_null($context)) {
        $context = context_system::instance();
    }
    // exporting user data needs special permission
    $userdetails = $userdetails && has_capability('mod/data:exportuserinfo', $context);

    $exportdata = array();

    // populate the header in first row of export
    foreach($fields as $key => $field) {
        if (!in_array($field->field->id, $selectedfields)) {
            // ignore values we aren't exporting
            unset($fields[$key]);
        } else {
            $exportdata[0][] = $field->field->name;
        }
    }
    if ($tags) {
        $exportdata[0][] = get_string('tags', 'data');
    }
    if ($userdetails) {
        $exportdata[0][] = get_string('user');
        $exportdata[0][] = get_string('username');
        $exportdata[0][] = get_string('email');
    }
    if ($time) {
        $exportdata[0][] = get_string('timeadded', 'data');
        $exportdata[0][] = get_string('timemodified', 'data');
    }
    if ($approval) {
        $exportdata[0][] = get_string('approved', 'data');
    }

    $datarecords = $DB->get_records('data_records', array('dataid'=>$dataid));
    ksort($datarecords);
    $line = 1;
    foreach($datarecords as $record) {
        // get content indexed by fieldid
        if ($currentgroup) {
            $select = 'SELECT c.fieldid, c.content, c.content1, c.content2, c.content3, c.content4 FROM {data_content} c, {data_records} r WHERE c.recordid = ? AND r.id = c.recordid AND r.groupid = ?';
            $where = array($record->id, $currentgroup);
        } else {
            $select = 'SELECT fieldid, content, content1, content2, content3, content4 FROM {data_content} WHERE recordid = ?';
            $where = array($record->id);
        }

        if( $content = $DB->get_records_sql($select, $where) ) {
            foreach($fields as $field) {
                $contents = '';
                if(isset($content[$field->field->id])) {
                    $contents = $field->export_text_value($content[$field->field->id]);
                }
                $exportdata[$line][] = $contents;
            }
            if ($tags) {
                $itemtags = \core_tag_tag::get_item_tags_array('mod_data', 'data_records', $record->id);
                $exportdata[$line][] = implode(', ', $itemtags);
            }
            if ($userdetails) { // Add user details to the export data
                $userdata = get_complete_user_data('id', $record->userid);
                $exportdata[$line][] = fullname($userdata);
                $exportdata[$line][] = $userdata->username;
                $exportdata[$line][] = $userdata->email;
            }
            if ($time) { // Add time added / modified
                $exportdata[$line][] = userdate($record->timecreated);
                $exportdata[$line][] = userdate($record->timemodified);
            }
            if ($approval) { // Add approval status
                $exportdata[$line][] = (int) $record->approved;
            }
        }
        $line++;
    }
    $line--;
    return $exportdata;
}

/**
 * @deprecated since Moodle 4.3, importing is now being done by \mod_data\local\importer\csv_importer::import_csv
 * Import records for a data instance from csv data.
 *
 * @param object $cm Course module of the data instance.
 * @param object $data The data instance.
 * @param string $csvdata The csv data to be imported.
 * @param string $encoding The encoding of csv data.
 * @param string $fielddelimiter The delimiter of the csv data.
 * @return int Number of records added.
 */
function data_import_csv($cm, $data, &$csvdata, $encoding, $fielddelimiter) {
    debugging('Function data_import_csv has been deprecated. '
        . 'Importing is now being done by \mod_data\local\csv_importer::import_csv.',
        DEBUG_DEVELOPER);

    // New function needs a file, not the file content, so we have to temporarily put the content into a file.
    $tmpdir = make_request_directory();
    $tmpfilename = 'tmpfile.csv';
    $tmpfilepath = $tmpdir . '/tmpfile.csv';
    file_put_contents($tmpfilepath, $csvdata);

    $importer = new \mod_data\local\importer\csv_entries_importer($tmpfilepath, $tmpfilename);
    $importer->import_csv($cm, $data, $encoding, $fielddelimiter);
    return 0;
}
