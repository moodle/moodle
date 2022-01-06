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
 * A form for cohort upload.
 *
 * @package    core_cohort
 * @copyright  2014 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

/**
 * Cohort upload form class
 *
 * @package    core_cohort
 * @copyright  2014 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cohort_upload_form extends moodleform {
    /** @var array new cohorts that need to be created */
    public $processeddata = null;
    /** @var array cached list of available contexts */
    protected $contextoptions = null;
    /** @var array temporary cache for retrieved categories */
    protected $categoriescache = array();

    /**
     * Form definition
     */
    public function definition() {
        $mform = $this->_form;
        $data  = (object)$this->_customdata;

        $mform->addElement('header', 'cohortfileuploadform', get_string('uploadafile'));

        $filepickeroptions = array();
        $filepickeroptions['filetypes'] = '*';
        $filepickeroptions['maxbytes'] = get_max_upload_file_size();
        $mform->addElement('filepicker', 'cohortfile', get_string('file'), null, $filepickeroptions);

        $choices = csv_import_reader::get_delimiter_list();
        $mform->addElement('select', 'delimiter', get_string('csvdelimiter', 'tool_uploadcourse'), $choices);
        if (array_key_exists('cfg', $choices)) {
            $mform->setDefault('delimiter', 'cfg');
        } else if (get_string('listsep', 'langconfig') == ';') {
            $mform->setDefault('delimiter', 'semicolon');
        } else {
            $mform->setDefault('delimiter', 'comma');
        }
        $mform->addHelpButton('delimiter', 'csvdelimiter', 'tool_uploadcourse');

        $choices = core_text::get_encodings();
        $mform->addElement('select', 'encoding', get_string('encoding', 'tool_uploadcourse'), $choices);
        $mform->setDefault('encoding', 'UTF-8');
        $mform->addHelpButton('encoding', 'encoding', 'tool_uploadcourse');

        $options = $this->get_context_options();
        $mform->addElement('select', 'contextid', get_string('defaultcontext', 'cohort'), $options);

        $this->add_cohort_upload_buttons(true);
        $this->set_data($data);
    }

    /**
     * Add buttons to the form ("Upload cohorts", "Preview", "Cancel")
     */
    protected function add_cohort_upload_buttons() {
        $mform = $this->_form;

        $buttonarray = array();

        $submitlabel = get_string('uploadcohorts', 'cohort');
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', $submitlabel);

        $previewlabel = get_string('preview', 'cohort');
        $buttonarray[] = $mform->createElement('submit', 'previewbutton', $previewlabel);
        $mform->registerNoSubmitButton('previewbutton');

        $buttonarray[] = $mform->createElement('cancel');

        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }

    /**
     * Process the uploaded file and allow the submit button only if it doest not have errors.
     */
    public function definition_after_data() {
        $mform = $this->_form;
        $cohortfile = $mform->getElementValue('cohortfile');
        $allowsubmitform = false;
        if ($cohortfile && ($file = $this->get_cohort_file($cohortfile))) {
            // File was uploaded. Parse it.
            $encoding = $mform->getElementValue('encoding')[0];
            $delimiter = $mform->getElementValue('delimiter')[0];
            $contextid = $mform->getElementValue('contextid')[0];
            if (!empty($contextid) && ($context = context::instance_by_id($contextid, IGNORE_MISSING))) {
                $this->processeddata = $this->process_upload_file($file, $encoding, $delimiter, $context);
                if ($this->processeddata && count($this->processeddata) > 1 && !$this->processeddata[0]['errors']) {
                    $allowsubmitform = true;
                }
            }
        }
        if (!$allowsubmitform) {
            // Hide submit button.
            $el = $mform->getElement('buttonar')->getElements()[0];
            $el->setValue('');
            $el->freeze();
        } else {
            $mform->setExpanded('cohortfileuploadform', false);
        }

    }

    /**
     * Returns the list of contexts where current user can create cohorts.
     *
     * @return array
     */
    protected function get_context_options() {
        if ($this->contextoptions === null) {
            $this->contextoptions = array();
            $displaylist = core_course_category::make_categories_list('moodle/cohort:manage');
            // We need to index the options array by context id instead of category id and add option for system context.
            $syscontext = context_system::instance();
            if (has_capability('moodle/cohort:manage', $syscontext)) {
                $this->contextoptions[$syscontext->id] = $syscontext->get_context_name();
            }
            foreach ($displaylist as $cid => $name) {
                $context = context_coursecat::instance($cid);
                $this->contextoptions[$context->id] = $name;
            }
        }
        return $this->contextoptions;
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if (empty($errors)) {
            if (empty($data['cohortfile']) || !($file = $this->get_cohort_file($data['cohortfile']))) {
                $errors['cohortfile'] = get_string('required');
            } else {
                if (!empty($this->processeddata[0]['errors'])) {
                    // Any value in $errors will notify that validation did not pass. The detailed errors will be shown in preview.
                    $errors['dummy'] = '';
                }
            }
        }
        return $errors;
    }

    /**
     * Returns the uploaded file if it is present.
     *
     * @param int $draftid
     * @return stored_file|null
     */
    protected function get_cohort_file($draftid) {
        global $USER;
        // We can not use moodleform::get_file_content() method because we need the content before the form is validated.
        if (!$draftid) {
            return null;
        }
        $fs = get_file_storage();
        $context = context_user::instance($USER->id);
        if (!$files = $fs->get_area_files($context->id, 'user', 'draft', $draftid, 'id DESC', false)) {
            return null;
        }
        $file = reset($files);

        return $file;

    }

    /**
     * Returns the list of prepared objects to be added as cohorts
     *
     * @return array of stdClass objects, each can be passed to {@link cohort_add_cohort()}
     */
    public function get_cohorts_data() {
        $cohorts = array();
        if ($this->processeddata) {
            foreach ($this->processeddata as $idx => $line) {
                if ($idx && !empty($line['data'])) {
                    $cohorts[] = (object)$line['data'];
                }
            }
        }
        return $cohorts;
    }

    /**
     * Displays the preview of the uploaded file
     */
    protected function preview_uploaded_cohorts() {
        global $OUTPUT;
        if (empty($this->processeddata)) {
            return;
        }
        foreach ($this->processeddata[0]['errors'] as $error) {
            echo $OUTPUT->notification($error);
        }
        foreach ($this->processeddata[0]['warnings'] as $warning) {
            echo $OUTPUT->notification($warning, 'notifymessage');
        }
        $table = new html_table();
        $table->id = 'previewuploadedcohorts';
        $columns = $this->processeddata[0]['data'];
        $columns['contextid'] = get_string('context', 'role');

        // Add column names to the preview table.
        $table->head = array('');
        foreach ($columns as $key => $value) {
            $table->head[] = $value;
        }
        $table->head[] = get_string('status');

        // Add (some) rows to the preview table.
        $previewdrows = $this->get_previewed_rows();
        foreach ($previewdrows as $idx) {
            $line = $this->processeddata[$idx];
            $cells = array(new html_table_cell($idx));
            $context = context::instance_by_id($line['data']['contextid']);
            foreach ($columns as $key => $value) {
                if ($key === 'contextid') {
                    $text = html_writer::link(new moodle_url('/cohort/index.php', array('contextid' => $context->id)),
                        $context->get_context_name(false));
                } else {
                    $text = s($line['data'][$key]);
                }
                $cells[] = new html_table_cell($text);
            }
            $text = '';
            if ($line['errors']) {
                $text .= html_writer::div(join('<br>', $line['errors']), 'notifyproblem');
            }
            if ($line['warnings']) {
                $text .= html_writer::div(join('<br>', $line['warnings']));
            }
            $cells[] = new html_table_cell($text);
            $table->data[] = new html_table_row($cells);
        }
        if ($notdisplayed = count($this->processeddata) - count($previewdrows) - 1) {
            $cell = new html_table_cell(get_string('displayedrows', 'cohort',
                (object)array('displayed' => count($previewdrows), 'total' => count($this->processeddata) - 1)));
            $cell->colspan = count($columns) + 2;
            $table->data[] = new html_table_row(array($cell));
        }
        echo html_writer::table($table);
    }

    /**
     * Find up rows to show in preview
     *
     * Number of previewed rows is limited but rows with errors and warnings have priority.
     *
     * @return array
     */
    protected function get_previewed_rows() {
        $previewlimit = 10;
        if (count($this->processeddata) <= 1) {
            $rows = array();
        } else if (count($this->processeddata) < $previewlimit + 1) {
            // Return all rows.
            $rows = range(1, count($this->processeddata) - 1);
        } else {
            // First find rows with errors and warnings (no more than 10 of each).
            $errorrows = $warningrows = array();
            foreach ($this->processeddata as $rownum => $line) {
                if ($rownum && $line['errors']) {
                    $errorrows[] = $rownum;
                    if (count($errorrows) >= $previewlimit) {
                        return $errorrows;
                    }
                } else if ($rownum && $line['warnings']) {
                    if (count($warningrows) + count($errorrows) < $previewlimit) {
                        $warningrows[] = $rownum;
                    }
                }
            }
            // Include as many error rows as possible and top them up with warning rows.
            $rows = array_merge($errorrows, array_slice($warningrows, 0, $previewlimit - count($errorrows)));
            // Keep adding good rows until we reach limit.
            for ($rownum = 1; count($rows) < $previewlimit; $rownum++) {
                if (!in_array($rownum, $rows)) {
                    $rows[] = $rownum;
                }
            }
            asort($rows);
        }
        return $rows;
    }

    public function display() {
        // Finalize the form definition if not yet done.
        if (!$this->_definition_finalized) {
            $this->_definition_finalized = true;
            $this->definition_after_data();
        }

        // Difference from the parent display() method is that we want to show preview above the form if applicable.
        $this->preview_uploaded_cohorts();

        $this->_form->display();
    }

    /**
     * @param stored_file $file
     * @param string $encoding
     * @param string $delimiter
     * @param context $defaultcontext
     * @return array
     */
    protected function process_upload_file($file, $encoding, $delimiter, $defaultcontext) {
        global $CFG, $DB;
        require_once($CFG->libdir . '/csvlib.class.php');

        $cohorts = array(
            0 => array('errors' => array(), 'warnings' => array(), 'data' => array())
        );

        // Read and parse the CSV file using csv library.
        $content = $file->get_content();
        if (!$content) {
            $cohorts[0]['errors'][] = new lang_string('csvemptyfile', 'error');
            return $cohorts;
        }

        $uploadid = csv_import_reader::get_new_iid('uploadcohort');
        $cir = new csv_import_reader($uploadid, 'uploadcohort');
        $readcount = $cir->load_csv_content($content, $encoding, $delimiter);
        unset($content);
        if (!$readcount) {
            $cohorts[0]['errors'][] = get_string('csvloaderror', 'error', $cir->get_error());
            return $cohorts;
        }
        $columns = $cir->get_columns();

        // Check that columns include 'name' and warn about extra columns.
        $allowedcolumns = array('contextid', 'name', 'idnumber', 'description', 'descriptionformat', 'visible', 'theme');
        $additionalcolumns = array('context', 'category', 'category_id', 'category_idnumber', 'category_path');
        $displaycolumns = array();
        $extracolumns = array();
        $columnsmapping = array();
        foreach ($columns as $i => $columnname) {
            $columnnamelower = preg_replace('/ /', '', core_text::strtolower($columnname));
            $columnsmapping[$i] = null;
            if (in_array($columnnamelower, $allowedcolumns)) {
                $displaycolumns[$columnnamelower] = $columnname;
                $columnsmapping[$i] = $columnnamelower;
            } else if (in_array($columnnamelower, $additionalcolumns)) {
                $columnsmapping[$i] = $columnnamelower;
            } else {
                $extracolumns[] = $columnname;
            }
        }
        if (!in_array('name', $columnsmapping)) {
            $cohorts[0]['errors'][] = new lang_string('namecolumnmissing', 'cohort');
            return $cohorts;
        }
        if ($extracolumns) {
            $cohorts[0]['warnings'][] = new lang_string('csvextracolumns', 'cohort', s(join(', ', $extracolumns)));
        }

        if (!isset($displaycolumns['contextid'])) {
            $displaycolumns['contextid'] = 'contextid';
        }
        $cohorts[0]['data'] = $displaycolumns;

        // Parse data rows.
        $cir->init();
        $rownum = 0;
        $idnumbers = array();
        $haserrors = false;
        $haswarnings = false;
        while ($row = $cir->next()) {
            $rownum++;
            $cohorts[$rownum] = array(
                'errors' => array(),
                'warnings' => array(),
                'data' => array(),
            );
            $hash = array();
            foreach ($row as $i => $value) {
                if ($columnsmapping[$i]) {
                    $hash[$columnsmapping[$i]] = $value;
                }
            }
            $this->clean_cohort_data($hash);

            $warnings = $this->resolve_context($hash, $defaultcontext);
            $cohorts[$rownum]['warnings'] = array_merge($cohorts[$rownum]['warnings'], $warnings);

            if (!empty($hash['idnumber'])) {
                if (isset($idnumbers[$hash['idnumber']]) || $DB->record_exists('cohort', array('idnumber' => $hash['idnumber']))) {
                    $cohorts[$rownum]['errors'][] = new lang_string('duplicateidnumber', 'cohort');
                }
                $idnumbers[$hash['idnumber']] = true;
            }

            if (empty($hash['name'])) {
                $cohorts[$rownum]['errors'][] = new lang_string('namefieldempty', 'cohort');
            }

            if (!empty($hash['theme']) && !empty($CFG->allowcohortthemes)) {
                $availablethemes = cohort_get_list_of_themes();
                if (empty($availablethemes[$hash['theme']])) {
                    $cohorts[$rownum]['errors'][] = new lang_string('invalidtheme', 'cohort');
                }
            }

            $cohorts[$rownum]['data'] = array_intersect_key($hash, $cohorts[0]['data']);
            $haserrors = $haserrors || !empty($cohorts[$rownum]['errors']);
            $haswarnings = $haswarnings || !empty($cohorts[$rownum]['warnings']);
        }

        if ($haserrors) {
            $cohorts[0]['errors'][] = new lang_string('csvcontainserrors', 'cohort');
        }

        if ($haswarnings) {
            $cohorts[0]['warnings'][] = new lang_string('csvcontainswarnings', 'cohort');
        }

        return $cohorts;
    }

    /**
     * Cleans input data about one cohort.
     *
     * @param array $hash
     */
    protected function clean_cohort_data(&$hash) {
        foreach ($hash as $key => $value) {
            switch ($key) {
                case 'contextid': $hash[$key] = clean_param($value, PARAM_INT); break;
                case 'name': $hash[$key] = core_text::substr(clean_param($value, PARAM_TEXT), 0, 254); break;
                case 'idnumber': $hash[$key] = core_text::substr(clean_param($value, PARAM_RAW), 0, 254); break;
                case 'description': $hash[$key] = clean_param($value, PARAM_RAW); break;
                case 'descriptionformat': $hash[$key] = clean_param($value, PARAM_INT); break;
                case 'visible':
                    $tempstr = trim(core_text::strtolower($value));
                    if ($tempstr === '') {
                        // Empty string is treated as "YES" (the default value for cohort visibility).
                        $hash[$key] = 1;
                    } else {
                        if ($tempstr === core_text::strtolower(get_string('no')) || $tempstr === 'n') {
                            // Special treatment for 'no' string that is not included in clean_param().
                            $value = 0;
                        }
                        $hash[$key] = clean_param($value, PARAM_BOOL) ? 1 : 0;
                    }
                    break;
                case 'theme':
                    $hash[$key] = core_text::substr(clean_param($value, PARAM_TEXT), 0, 50);
                    break;
            }
        }
    }

    /**
     * Determines in which context the particular cohort will be created
     *
     * @param array $hash
     * @param context $defaultcontext
     * @return array array of warning strings
     */
    protected function resolve_context(&$hash, $defaultcontext) {
        global $DB;

        $warnings = array();

        if (!empty($hash['contextid'])) {
            // Contextid was specified, verify we can post there.
            $contextoptions = $this->get_context_options();
            if (!isset($contextoptions[$hash['contextid']])) {
                $warnings[] = new lang_string('contextnotfound', 'cohort', $hash['contextid']);
                $hash['contextid'] = $defaultcontext->id;
            }
            return $warnings;
        }

        if (!empty($hash['context'])) {
            $systemcontext = context_system::instance();
            if ((core_text::strtolower(trim($hash['context'])) ===
                    core_text::strtolower($systemcontext->get_context_name())) ||
                    ('' . $hash['context'] === '' . $systemcontext->id)) {
                // User meant system context.
                $hash['contextid'] = $systemcontext->id;
                $contextoptions = $this->get_context_options();
                if (!isset($contextoptions[$hash['contextid']])) {
                    $warnings[] = new lang_string('contextnotfound', 'cohort', $hash['context']);
                    $hash['contextid'] = $defaultcontext->id;
                }
            } else {
                // Assume it is a category.
                $hash['category'] = trim($hash['context']);
            }
        }

        if (!empty($hash['category_path'])) {
            // We already have array with available categories, look up the value.
            $contextoptions = $this->get_context_options();
            if (!$hash['contextid'] = array_search($hash['category_path'], $contextoptions)) {
                $warnings[] = new lang_string('categorynotfound', 'cohort', s($hash['category_path']));
                $hash['contextid'] = $defaultcontext->id;
            }
            return $warnings;
        }

        if (!empty($hash['category'])) {
            // Quick search by category path first.
            // Do not issue warnings or return here, further we'll try to search by id or idnumber.
            $contextoptions = $this->get_context_options();
            if ($hash['contextid'] = array_search($hash['category'], $contextoptions)) {
                return $warnings;
            }
        }

        // Now search by category id or category idnumber.
        if (!empty($hash['category_id'])) {
            $field = 'id';
            $value = clean_param($hash['category_id'], PARAM_INT);
        } else if (!empty($hash['category_idnumber'])) {
            $field = 'idnumber';
            $value = $hash['category_idnumber'];
        } else if (!empty($hash['category'])) {
            $field = is_numeric($hash['category']) ? 'id' : 'idnumber';
            $value = $hash['category'];
        } else {
            // No category field was specified, assume default category.
            $hash['contextid'] = $defaultcontext->id;
            return $warnings;
        }

        if (empty($this->categoriescache[$field][$value])) {
            $record = $DB->get_record_sql("SELECT c.id, ctx.id contextid
                FROM {context} ctx JOIN {course_categories} c ON ctx.contextlevel = ? AND ctx.instanceid = c.id
                WHERE c.$field = ?", array(CONTEXT_COURSECAT, $value));
            if ($record && ($contextoptions = $this->get_context_options()) && isset($contextoptions[$record->contextid])) {
                $contextid = $record->contextid;
            } else {
                $warnings[] = new lang_string('categorynotfound', 'cohort', s($value));
                $contextid = $defaultcontext->id;
            }
            // Next time when we can look up and don't search by this value again.
            $this->categoriescache[$field][$value] = $contextid;
        }
        $hash['contextid'] = $this->categoriescache[$field][$value];

        return $warnings;
    }
}
