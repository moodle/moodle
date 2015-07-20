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

// Bulk media data script from a comma separated file.
// Returns list of mediadata with their entry ids.

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/csvlib.class.php');
require_once('uploadmediadata_form.php');

$iid         = optional_param('iid', '', PARAM_INT);
$previewrows = optional_param('previewrows', 10, PARAM_INT);
$readcount   = optional_param('readcount', 0, PARAM_INT);
$uploadtype  = optional_param('uutype', 0, PARAM_INT);

$context = context_system::instance();
require_login();

$url = '/blocks/mediasearch/uploadmediadata.php';
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('uploadmediadata', 'block_mediasearch'));
$PAGE->set_url($url);
$PAGE->set_heading($SITE->fullname);

$returnurl = '/blocks/mediasearch/manage.php';


define('UU_ADDNEW', 0);
define('UU_ADDINC', 1);
define('UU_ADD_UPDATE', 2);
define('UU_UPDATE', 3);

$choices = array(UU_ADDNEW    => get_string('uuoptype_addnew', 'block_mediasearch'),
                 UU_ADDINC    => get_string('uuoptype_addinc', 'block_mediasearch'),
                 UU_ADD_UPDATE => get_string('uuoptype_addupdate', 'block_mediasearch'),
                 UU_UPDATE     => get_string('uuoptype_update', 'block_mediasearch'));

@set_time_limit(3600); // 1 hour should be enough.
raise_memory_limit(MEMORY_EXTRA);

require_login();
require_capability('block/mediasearch:uploadcsv', context_system::instance());

// Correct the navbar .
// Set the name for the page.
$linktext = get_string('mediadata_upload_title', 'block_mediasearch');

// Set the url.
$linkurl = new moodle_url('/blocks/mediasearch/uploadmediadata.php');

$errorstr                   = get_string('error');

$today = time();
$today = make_timestamp(date('Y', $today), date('m', $today), date('d', $today), 0, 0, 0);

// Array of all valid fields for validation.
$stdfields = array('id', 'course', 'title', 'description', 'link', 'keywords');

if (empty($iid)) {
    $mform = new mediasearch_uploaddata_form1();

    if ($formdata = $mform->get_data()) {
        $iid = csv_import_reader::get_new_iid('uploadmediadata');
        $cir = new csv_import_reader($iid, 'uploadmediadata');

        $content = $mform->get_file_content('userfile');
        $optype = $formdata->uutype;
        $readcount = $cir->load_csv_content($content,
                                            $formdata->encoding,
                                            $formdata->delimiter_name,
                                            'validate_mediadata_upload_columns');
        unset($content);

        if ($readcount === false) {
            // TODO: need more detailed error info.
            print_error('csvloaderror', '', $returnurl);
        } else if ($readcount == 0) {
            print_error('csvemptyfile', 'error', $returnurl);
        }
        // Continue to form2.

    } else {
        echo $OUTPUT->header();

        echo $OUTPUT->heading_with_help(get_string('uploadmediadata', 'block_mediasearch'), 'uploadmediadata', 'block_mediasearch');

        $mform->display();
        echo $OUTPUT->footer();
        die;
    }
} else {
    $cir = new csv_import_reader($iid, 'uploadmediadata');
}

if (!$columns = $cir->get_columns()) {
    print_error('cannotreadtmpfile', 'error', $returnurl);
}
$mform = new mediasearch_uploaddata_form2(null, $columns);

// Get initial date from form1.
$mform->set_data(array('iid' => $iid,
                       'previewrows' => $previewrows,
                       'readcount' => $readcount,
                       'uutypelabel' => $choices[$uploadtype],
                       'uutype' => $uploadtype));

// If a file has been uploaded, then process it.
if ($formdata = $mform->is_cancelled()) {
    $cir->cleanup(true);
    redirect($returnurl);

} else if ($formdata = $mform->get_data()) {
    // Print the header.
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('uploadmediadataresult', 'block_mediasearch'));

    $optype = $formdata->uutype;

    $allowrenames      = (!empty($formdata->uuallowrenames) and $optype != UU_ADDNEW and $optype != UU_ADDINC);
    $allowdeletes      = (!empty($formdata->uuallowdeletes) and $optype != UU_ADDNEW and $optype != UU_ADDINC);
    $updatetype        = isset($formdata->uuupdatetype) ? $formdata->uuupdatetype : 0;
    
    // Verification moved to two places: after upload and into form2.
    $mediadatanew     = 0;
    $mediadataupdated = 0;
    $mediadataerrors  = 0;
    $deletes      = 0;
    $deleteerrors = 0;
    $renames      = 0;
    $renameerrors = 0;
    $mediadataskipped = 0;
    $weakpasswords = 0;

    $strmediadatanotdeletedoff = get_string('mediadatanotdeletedoff', 'block_mediasearch');
    $strmediadatadeleted = get_string('mediadatadeleted', 'block_mediasearch');
    $strmediadatanotdeletederror = get_string('mediadatanotdeletederror', 'block_mediasearch');
    $strmediadatanotdeletedmissing = get_string('mediadatanotdeletedmissing', 'block_mediasearch');
    $strmediadatanotadded = get_string('mediadatanotadded', 'block_mediasearch');
    $strmediadatanotaddederror = get_string('mediadatanotaddederror', 'block_mediasearch');
    $strmediadatanotupdatednotexists = get_string('mediadatanotupdatednotexists', 'block_mediasearch');
    $strmediadataupdated = get_string('mediadataupdated', 'block_mediasearch');
    $strmediadataadded = get_string('mediadataadded', 'block_mediasearch');

    // Init csv import helper.
    $cir->init();
    $linenum = 1; // Column header is first line.

    // Init upload progress tracker.
    $upt = new uu_progress_tracker();
    $upt->init(); // Start table.

    while ($line = $cir->next()) {
        $upt->flush();
        $linenum++;

        $upt->track('line', $linenum);

        $forcechangepassword = false;

        $mediadata = new stdClass();
        // Add fields to mediadata object.
        foreach ($line as $key => $value) {
            if ($value !== '') {
                $key = strtolower($columns[$key]);
                if ($key == 'course') {
                    $datacourse = $DB->get_record('course', array('shortname' => $value));
                    $mediadata->courseid = $datacourse->id;
                } else {
                    $mediadata->$key = $value;
                }
                if (in_array($key, $upt->columns)) {
                    $upt->track($key, $value);
                }
            } else {
                $key = strtolower($columns[$key]);
                $mediadata->$key = '';
            }
        }

        // Get title, link and course now.
        if ($optype == UU_UPDATE) {
            // When updating title, course and link are required.
            if (!isset($mediadata->title) || !isset($mediadata->course) || !isset($mediadata->link)) {
                $upt->track('status', get_string('missingfields', 'block_mediasearch'), 'error');
                $upt->track('mediadatatitle', $errorstr, 'error');
                $mediadataerrors++;
                continue;
            }

        } else {
            $existingmediadata = $DB->get_record_sql("SELECT id FROM {block_mediasearch_data}
                                                      WHERE ". $DB->sql_compare_text('title') . " = " . $DB->sql_compare_text(":title") .
                                                    " AND ". $DB->sql_compare_text('courseid') . " = " . $DB->sql_compare_text(":courseid") .
                                                    " AND ". $DB->sql_compare_text('link') . " = " . $DB->sql_compare_text(":link"),
                                                    array('title' => $mediadata->title,
                                                          'courseid' => $mediadata->courseid,
                                                          'link' => $mediadata->link));
            $error = false;
            // When all other ops need description and keywords.
            if (!isset($mediadata->description) or $mediadata->description === '') {
                $upt->track('status', get_string('missingfield', 'error', 'description'), 'error');
                $upt->track('description', $errorstr, 'error');
                $error = true;
            }
            if (!isset($mediadata->keywords) or $mediadata->keywords === '') {
                $upt->track('status', get_string('missingfield', 'error', 'keywords'), 'error');
                $upt->track('keywords', $errorstr, 'error');
                $error = true;
            }
            if ($error) {
                $mediadataerrors++;
                continue;
            }
            // We require title too.
            if (!isset($mediadata->title)) {
                if (!isset($formdata->title) or $formdata->title === '') {
                    $upt->track('status', get_string('missingfield', 'error', 'title'), 'error');
                    $upt->track('title', $errorstr, 'error');
                    $mediadataerrors++;
                    continue;
                }
            }
        }

        // Delete mediadata record.
        if (!empty($mediadata->deleted)) {
            if (!$allowdeletes) {
                $mediadataskipped++;
                $upt->track('status', $strmediadatanotdeletedoff, 'warning');
                continue;
            }
            if ($existingmediadata) {
                if ($DB->delete_records('block_mediasearch_data', (array) $existingmediadata)) {
                    $upt->track('status', $strmediadatadeleted);
                    $deletes++;
                } else {
                    $upt->track('status', $strmediadatanotdeletederror, 'error');
                    $deleteerrors++;
                }
            } else {
                $upt->track('status', $strmediadatanotdeletedmissing, 'error');
                $deleteerrors++;
            }
            continue;
        }
        // We do not need the deleted flag anymore.
        unset($mediadata->deleted);

        // Can we process with update or insert?
        $skip = false;
        switch ($optype) {
            case UU_ADDNEW:
                if ($existingmediadata) {
                    $mediadataskipped++;
                    $upt->track('status', $strmediadatanotadded, 'warning');
                    $skip = true;
                }
                break;

            case UU_ADDINC:
                if ($existingmediadata) {
                    // This should not happen!
                    $upt->track('status', $strmediadatanotaddederror, 'error');
                    $mediadataerrors++;
                    continue;
                }
                break;

            case UU_ADD_UPDATE:
                break;

            case UU_UPDATE:
                if (!$existingmediadata) {
                    $mediadataskipped++;
                    $upt->track('status', $strmediadatanotupdatednotexists, 'warning');
                    $skip = true;
                }
                break;
        }

        if ($skip) {
            continue;
        }

        if ($existingmediadata) {
            $mediadata->id = $existingmediadata->id;

            if (!empty($updatetype)) {

                $allowed = array();
                if ($updatetype == 1) {
                    $allowed = $columns;
                }
                foreach ($allowed as $column) {
                    $temppasswordhandler = '';
                    if ((property_exists($existingmediadata, $column) and property_exists($mediadata, $column))) {
                        if ($updatetype == 3 && $existingmediadata->$column !== '') {
                            // Missing == non-empty only!
                            continue;
                        }
                        if ($existingmediadata->$column !== $mediadata->$column) {

                            $upt->track($column, '', 'normal', false); // Clear previous.
                            if ($column != 'password' && in_array($column, $upt->columns)) {
                                $upt->track($column, $existingmediadata->$column.'-->'.$mediadata->$column, 'info');
                            }
                            $existingmediadata->$column = $mediadata->$column;

                        }
                    }
                }

                $DB->update_record('block_mediasearch_data', $existingmediadata);
                $upt->track('status', $strmediadataupdated);
                $mediadataupdated++;

                
            }

            if ($bulk == 2 or $bulk == 3) {
                if (!in_array($mediadata->id, $SESSION->bulk_mediadata)) {
                    $SESSION->bulk_mediadata[] = $mediadata->id;
                }
            }

        } else {
            // Save the mediadata to the database.
            $mediadata->id = $DB->insert_record('block_mediasearch_data', $mediadata);
            $info = ': ' . $mediadata->title .' (ID = ' . $mediadata->id . ')';
            $upt->track('status', $strmediadataadded);
            $upt->track('id', $mediadata->id, 'normal', false);
            $mediadatanew++;

        }
    }
    $upt->flush();
    $upt->close(); // Close table.

    $cir->close();
    $cir->cleanup(true);

    echo $OUTPUT->box_start('boxwidthnarrow boxaligncenter generalbox', 'uploadresults');
    echo '<p>';
    if ($optype != UU_UPDATE) {
        echo get_string('mediadatacreated', 'block_mediasearch').': '.$mediadatanew.'<br />';
    }
    if ($optype == UU_UPDATE or $optype == UU_ADD_UPDATE) {
        echo get_string('mediadataupdated', 'block_mediasearch').': '.$mediadataupdated.'<br />';
    }
    if ($allowdeletes) {
        echo get_string('mediadatadeleted', 'block_mediasearch').': '.$deletes.'<br />';
        echo get_string('deleteerrors', 'block_mediasearch').': '.$deleteerrors.'<br />';
    }
    if ($allowrenames) {
        echo get_string('mediadatarenamed', 'block_mediasearch').': '.$renames.'<br />';
        echo get_string('renameerrors', 'block_mediasearch').': '.$renameerrors.'<br />';
    }
    if ($mediadataskipped) {
        echo get_string('mediadataskipped', 'block_mediasearch').': '.$mediadataskipped.'<br />';
    }
    echo get_string('errors', 'block_mediasearch').': '.$mediadataerrors.'</p>';
    echo $OUTPUT->box_end();

    echo $OUTPUT->continue_button($returnurl);
    echo $OUTPUT->footer();
    die;
}

// Print the header.
echo $OUTPUT->header();

// Print the form.

echo $OUTPUT->heading(get_string('uploadmediadatapreview', 'block_mediasearch'));

$cir->init();
$contents = array();
while ($fields = $cir->next()) {
    $errormsg = array();
    $rowcols = array();
    foreach ($fields as $key => $field) {
        if (strtolower($columns[$key]) == 'course') {
            // Get the course id.
            $courserecord = $DB->get_record('course', array('shortname' => $field));
            $rowcols[strtolower($columns[$key])] = $courserecord->id;
        } else {
            $rowcols[strtolower($columns[$key])] = $field;
        }
    }
    if ($DB->get_record_sql("SELECT id FROM {block_mediasearch_data}
                             WHERE ". $DB->sql_compare_text('title') . " = " . $DB->sql_compare_text(":title") .
                             " AND ". $DB->sql_compare_text('courseid') . " = " . $DB->sql_compare_text(":courseid") .
                             " AND ". $DB->sql_compare_text('link') . " = " . $DB->sql_compare_text(":link"),
                              array('title' => $rowcols['title'],
                                    'courseid' => $rowcols['course'],
                                    'link' => $rowcols['link']))) {
        $mediadataexist = true;
    } else {
        $mediadataexist = false;
    }

    if (empty($optype) ) {
        $optype = $uploadtype;
    }

    switch($optype) {
        case UU_ADDNEW:
            if ($mediadataexist) {
                $rowcols['action'] = 'skipped';
            } else {
                $rowcols['action'] = 'create';
            }
            break;

        case UU_ADDINC:
            if (!$mediadataexist) {
                $rowcols['action'] = 'create';
            } else {
                $rowcols['action'] = 'skipped';
            }
            break;

        case UU_ADD_UPDATE:
            if ($mediadataexist) {
                $rowcols['action'] = 'update';
            } else {
                $rowcols['action'] = 'create';
            }
            break;

        case UU_UPDATE:
            if ($mediadataexist) {
                $rowcols['action'] = 'update';
            } else {
                $rowcols['action'] = "skipped";
            }
            break;
    }
    if (!empty($errormsg)) {
        $rowcols['error'] = array();
        $rowcols['error'] = $errormsg;
    }
    if ($rowcols['action'] != 'skipped') {
        $contents[] = $rowcols;
    }
}
$cir->close();

// Get heading.
$headings = array();
foreach ($contents as $content) {
    foreach ($content as $key => $value) {
        if (!in_array($key, $headings)) {
            $headings[] = $key;
        }
    }
}

$table = new html_table();
$table->id = "uupreview";
$table->attributes['class'] = 'generaltable';
$table->tablealign = 'center';
$table->summary = get_string('uploadmediadatapreview', 'block_mediasearch');
$table->head = array();
$table->data = array();

// Print heading.
foreach ($headings as $heading) {
    $table->head[] = s($heading);
}

$haserror = false;
$countcontent = 0;
if (in_array('error', $headings)) {
    // Print error.
    $haserror = true;

    foreach ($contents as $content) {
        if (array_key_exists('error', $content)) {
            $rows = new html_table_row();
            foreach ($content as $key => $value) {
                $cells = new html_table_cell();
                $errclass = '';
                if (array_key_exists($key, $content['error'])) {
                    $errclass = 'uuerror';
                }
                if ($key == 'error') {
                    $value = join('<br />', $content['error']);
                }
                if ($key == 'action') {
                    $value = get_string($content[$key]);
                }
                $cells->text = $value;
                $cells->attributes['class'] = $errclass;
                $rows->cells[] = $cells;
            }
            $countcontent++;
            $table->data[] = $rows;
        }
    }
    $mform = new mediasearch_uploaddata_form3();
    $mform->set_data(array('uutype' => $uploadtype));
} else if (empty($contents)) {
    $mform = new mediasearch_uploaddata_form3();
    $mform->set_data(array('uutype' => $uploadtype));
} else {
    // Print content.
    foreach ($contents as $content) {
        $rows = new html_table_row();
        if ($countcontent >= $previewrows) {
            foreach ($content as $con) {
                $cells = new html_table_cell();
                $cells->text = '...';
            }
            $rows->cells[] = $cells;
            $table->data[] = $rows;
            break;
        }
        foreach ($headings as $heading) {
            $cells = new html_table_cell();
            if (array_key_exists($heading, $content)) {
                if ($heading == 'action') {
                    $content[$heading] = get_string($content[$heading]);
                }
                $cells->text = $content[$heading];
            } else {
                $cells->text = '';
            }
            $rows->cells[] = $cells;
        }
        $table->data[] = $rows;
        $countcontent++;
    }
}
echo html_writer::tag('div', html_writer::table($table), array('class' => 'flexible-wrap'));

if ($haserror) {
    echo $OUTPUT->container(get_string('useruploadtype', 'moodle', $choices[$uploadtype]), 'block_iomad_company_admin');
    echo $OUTPUT->container(get_string('uploadinvalidpreprocessedcount', 'moodle', $countcontent), 'block_iomad_company_admin');
    echo $OUTPUT->container(get_string('invalidusername', 'moodle'), 'block_iomad_company_admin');
    echo $OUTPUT->container(get_string('uploadfilecontainerror', 'block_iomad_company_admin'), 'block_iomad_company_admin');
} else if (empty($contents)) {
    echo $OUTPUT->container(get_string('uupreprocessedcount', 'block_iomad_company_admin', $countcontent),
                            'block_iomad_company_admin');
    echo $OUTPUT->container(get_string('uploadfilecontentsnovaliddata', 'block_iomad_company_admin'));
} else {
    echo $OUTPUT->container(get_string('uupreprocessedcount', 'block_iomad_company_admin', $countcontent),
                            'block_iomad_company_admin');
}

$mform->display();
echo $OUTPUT->footer();
die;

/*
* Utility functions and classes
*/

class uu_progress_tracker {
    public $_row;
    public $columns = array('status',
                            'line',
                            'id',
                            'title',
                            'course',
                            'description',
                            'link',
                            'keywords',
                            'delete');

    public function __construct() {
    }

    public function init() {
        $ci = 0;
        echo '<table id="uuresults" class="generaltable boxaligncenter flexible-wrap" summary="'.
               get_string('uploadmediadataresult', 'block_mediasearch').'">';
        echo '<tr class="heading r0">';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('status').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('uucsvline', 'block_mediasearch').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">ID</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('title', 'block_mediasearch').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('course').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('description', 'block_mediasearch').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('link', 'block_mediasearch').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('keywords', 'block_mediasearch').'</th>';
        echo '<th class="header c'.$ci++.'" scope="col">'.get_string('delete').'</th>';
        echo '</tr>';
        $this->_row = null;
    }

    public function flush() {
        if (empty($this->_row) or empty($this->_row['line']['normal'])) {
            $this->_row = array();
            foreach ($this->columns as $col) {
                $this->_row[$col] = array('normal' => '', 'info' => '', 'warning' => '', 'error' => '');
            }
            return;
        }
        $ci = 0;
        $ri = 1;
        echo '<tr class="r'.$ri.'">';
        foreach ($this->_row as $key => $field) {
            foreach ($field as $type => $content) {
                if ($field[$type] !== '') {
                    $field[$type] = '<span class="uu'.$type.'">'.$field[$type].'</span>';
                } else {
                    unset($field[$type]);
                }
            }
            echo '<td class="cell c'.$ci++.'">';
            if (!empty($field)) {
                echo implode('<br />', $field);
            } else {
                echo '&nbsp;';
            }
            echo '</td>';
        }
        echo '</tr>';
        foreach ($this->columns as $col) {
            $this->_row[$col] = array('normal' => '', 'info' => '', 'warning' => '', 'error' => '');
        }
    }

    public function track($col, $msg, $level= 'normal', $merge=true) {
        if (empty($this->_row)) {
            $this->flush(); // Init arrays.
        }
        if (!in_array($col, $this->columns)) {
            debugging('Incorrect column:'.$col);
            return;
        }
        if ($merge) {
            if ($this->_row[$col][$level] != '') {
                $this->_row[$col][$level] .= '<br />';
            }
            $this->_row[$col][$level] .= s($msg);
        } else {
            $this->_row[$col][$level] = s($msg);
        }
    }

    public function close() {
        echo '</table>';
    }
}

/**
 * Validation callback function - verified the column line of csv file.
 * Converts column names to lowercase too.
 */
function validate_mediadata_upload_columns(&$columns) {
    global $stdfields;
    if (count($columns) < 2) {
        return get_string('csvfewcolumns', 'error');
    }
    // Test columns.
    $processed = array();
    foreach ($columns as $key => $unused) {
        $field = strtolower($columns[$key]);
        if (!in_array($field, $stdfields)) {
            // If not a standard field and not an enrolment field, then we have an error!
            return get_string('invalidfieldname', 'error', $field);
        }
        if (in_array($field, $processed)) {
            return get_string('csvcolumnduplicates', 'error');
        }
        $processed[] = $field;
    }
    return true;
}

