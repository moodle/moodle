<?php  // $Id$

require_once('../../config.php');
require_once('lib.php');
require_once($CFG->libdir . '/csvlib.class.php');
require_once('export_form.php');

$d = required_param('d', PARAM_INT);
// database ID

if (! $data = get_record('data', 'id', $d)) {
    print_error('wrongdataid', 'data');
}

if (! $cm = get_coursemodule_from_instance('data', $data->id, $data->course)) {
    print_error('invalidcoursemodule');
}

if(! $course = get_record('course', 'id', $cm->course)) {
    print_error('invalidcourseid', '', '', $cm->course);
}

// fill in missing properties needed for updating of instance
$data->course     = $cm->course;
$data->cmidnumber = $cm->idnumber;
$data->instance   = $cm->instance;

if (! $context = get_context_instance(CONTEXT_MODULE, $cm->id)) {
    print_error('invalidcontext', '');
}

require_login($course->id, false, $cm);
require_capability(DATA_CAP_EXPORT, $context);

// get fields for this database
$fieldrecords = get_records('data_fields','dataid', $data->id, 'id');

if(empty($fieldrecords)) {
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    if (has_capability('mod/data:managetemplates', $context)) {
        redirect($CFG->wwwroot.'/mod/data/field.php?d='.$data->id);
    } else {
        print_error('nofieldindatabase', 'data');
    }
}

// populate objets for this databases fields
$fields = array();
foreach ($fieldrecords as $fieldrecord) {
    $fields[]= data_get_field($fieldrecord, $data);
}

$mform = new mod_data_export_form('export.php?d='.$data->id, $fields);

if($mform->is_cancelled()) {
    redirect('view.php?d='.$data->id);
} elseif (!$formdata = (array) $mform->get_data()) {
    // build header to match the rest of the UI
    $nav = build_navigation('', $cm);
    print_header_simple($data->name, '', $nav,
        '', '', true, update_module_button($cm->id, $course->id, get_string('modulename', 'data')),
        navmenu($course, $cm), '', '');
    print_heading(format_string($data->name));

    // these are for the tab display
    $currentgroup = groups_get_activity_group($cm);
    $groupmode = groups_get_activity_groupmode($cm);
    $currenttab = 'export';
    include('tabs.php');
    $mform->display();
    print_footer();
    die;
}

$exportdata = array();

// populate the header in first row of export
foreach($fields as $key => $field) {
    if(empty($formdata['field_'.$field->field->id])) {
        // ignore values we aren't exporting
        unset($fields[$key]);
    } else {
        $exportdata[0][] = $field->field->name;
    }
}

$datarecords = get_records('data_records', 'dataid', $data->id);
ksort($datarecords);
$line = 1;
foreach($datarecords as $record) {
    // get content indexed by fieldid
    if( $content = get_records('data_content', 'recordid', $record->id, 'fieldid', 'fieldid, content, content1, content2, content3, content4') ) {
        foreach($fields as $field) {
            $contents = '';
            if(isset($content[$field->field->id])) {
                $contents = $field->export_text_value($content[$field->field->id]);
            }
            $exportdata[$line][] = $contents;
        }
    }
    $line++;
}
$line--;

switch ($formdata['exporttype']) {
    case 'csv':
        data_export_csv($exportdata, $formdata['delimiter_name'], $data->name, $line);
        break;
    case 'xls':
        data_export_xls($exportdata, $data->name, $line);
        break;
    case 'ods':
        data_export_ods($exportdata, $data->name, $line);
        break;
}


function data_export_csv($export, $delimiter_name, $dataname, $count) {
    $delimiter = csv_import_reader::get_delimiter($delimiter_name);
    $filename = clean_filename("${dataname}-${count}_record");
    if ($count > 1) {
        $filename .= 's';
    }
    $filename .= clean_filename('-' . gmdate("Ymd_Hi"));
    $filename .= clean_filename("-${delimiter_name}_separated");
    $filename .= '.csv';
    header("Content-Type: application/download\n");
    header("Content-Disposition: attachment; filename=$filename");
    header('Expires: 0');
    header('Cache-Control: must-revalidate,post-check=0,pre-check=0');
    header('Pragma: public');
    $encdelim = '&#' . ord($delimiter) . ';';
    foreach($export as $row) {
        foreach($row as $key => $column) {
            $row[$key] = str_replace($delimiter, $encdelim, $column);
        }
        echo implode($delimiter, $row) . "\n";
    }
    die;
}


function data_export_xls($export, $dataname, $count) {
    global $CFG;
    require_once("$CFG->libdir/excellib.class.php");
    $filename = clean_filename("${dataname}-${count}_record");
    if ($count > 1) {
        $filename .= 's';
    }
    $filename .= clean_filename('-' . gmdate("Ymd_Hi"));
    $filename .= '.xls';
    $workbook = new MoodleExcelWorkbook('-');
    $workbook->send($filename);
    $worksheet = array();
    $worksheet[0] =& $workbook->add_worksheet('');
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
    die;
}


function data_export_ods($export, $dataname, $count) {
    global $CFG;
    require_once("$CFG->libdir/odslib.class.php");
    $filename = clean_filename("${dataname}-${count}_record");
    if ($count > 1) {
        $filename .= 's';
    }
    $filename .= clean_filename('-' . gmdate("Ymd_Hi"));
    $filename .= '.ods';
    $workbook = new MoodleODSWorkbook('-');
    $workbook->send($filename);
    $worksheet = array();
    $worksheet[0] =& $workbook->add_worksheet('');
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
    die;
}
