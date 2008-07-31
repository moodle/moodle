<?php  // $Id$

require_once('../../config.php');
require_once('lib.php');
require_once($CFG->libdir . '/csvlib.class.php');
require_once('export_form.php');

$d = required_param('d', PARAM_INT);
// database ID

if (! $data = $DB->get_record('data', array('id'=>$d))) {
    print_error('wrongdataid', 'data');
}

if (! $cm = get_coursemodule_from_instance('data', $data->id, $data->course)) {
    print_error('invalidcoursemodule');
}

if(! $course = $DB->get_record('course', array('id'=>$cm->course))) {
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
$fieldrecords = $DB->get_records('data_fields', array('dataid'=>$data->id), 'id');

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

$datarecords = $DB->get_records('data_records', array('dataid'=>$data->id));
ksort($datarecords);
$line = 1;
foreach($datarecords as $record) {
    // get content indexed by fieldid
    if( $content = $DB->get_records('data_content', array('recordid'=>$record->id), 'fieldid', 'fieldid, content, content1, content2, content3, content4') ) {
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

die();
?>
