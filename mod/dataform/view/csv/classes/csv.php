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
 * @package dataformview
 * @subpackage csv
 * @copyright 2012 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class dataformview_csv_csv extends dataformview_aligned_aligned {

    const EXPORT_ALL = 'all';
    const EXPORT_PAGE = 'page';

    protected $_delimiter = 'comma';
    protected $_enclosure = '';
    protected $_encoding = 'UTF-8';

    protected $_results;
    protected $_showimportform = false;

    /**
     *
     */
    public function __construct($view) {
        parent::__construct($view);
        // Set user define csv settings.
        if ($this->param1) {
            list($this->_delimiter, $this->_enclosure, $this->_encoding) = explode(',', $this->param1);
        }
    }

    /**
     * process any view specific actions.
     *
     * @return void
     */
    public function process_data() {
        global $CFG;

        // Proces csv export request.
        $exportcsv = optional_param('exportcsv', '', PARAM_ALPHA);
        if ($exportcsv and $this->param4 and confirm_sesskey()) {
            $this->process_export($exportcsv);
            return;
        }

        // Proces csv import request.
        $importcsv = optional_param('importcsv', 0, PARAM_INT);
        if ($importcsv and $this->param5 and confirm_sesskey()) {
            $this->process_import();
            return;
        }

        parent::process_data();
    }

    /**
     * Overridden to show import form without entries
     */
    public function display(array $params = array()) {
        global $OUTPUT;

        if ($this->_showimportform and $this->param5) {
            // Print import form.
            $mform = $this->get_import_form();
            $mform->set_data($this->data);

            $dataformviewtype = "dataformview-$this->type";
            $viewname = str_replace(' ', '_', $this->name);
            $report = $this->print_report();
            return html_writer::tag('div', $mform->render(). $report, array('class' => "$dataformviewtype $viewname"));

        } else {
            // Print the view.
            return parent::display($params);
        }
    }

    /**
     *
     */
    public function process_export($range = self::EXPORT_PAGE) {
        global $CFG;

        require_once($CFG->libdir . '/csvlib.class.php');

        if (!$csvcontent = $this->get_csv_content($range)) {
            return;
        }
        $dataformname = $this->df->name;
        $delimiter = \csv_import_reader::get_delimiter($this->_delimiter);
        $filename = clean_filename("{$dataformname}-export");
        $filename .= clean_filename('-' . gmdate("Ymd_Hi"));
        $filename .= clean_filename("-{$this->_delimiter}_separated");
        $filename .= '.csv';

        $patterns = array("\n");
        $adjustments = array('');
        if ($this->_enclosure) {
            $patterns[] = $this->_enclosure;
            $adjustments[] = '&#' . ord($this->_enclosure) . ';';
        } else {
            $patterns[] = $delimiter;
            $adjustments[] = '&#' . ord($delimiter) . ';';
        }
        $returnstr = '';
        foreach ($csvcontent as $row) {
            foreach ($row as $key => $column) {
                $value = str_replace($patterns, $adjustments, $column);
                $row[$key] = $this->_enclosure. $value. $this->_enclosure;
            }
            $returnstr .= implode($delimiter, $row) . "\n";
        }

        // Convert encoding.
        $returnstr = mb_convert_encoding($returnstr, $this->_encoding, 'UTF-8');

        header("Content-Type: application/download\n");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Expires: 0');
        header('Cache-Control: must-revalidate,post-check=0,pre-check=0');
        header('Pragma: public');

        echo $returnstr;
        exit;
    }

    /**
     *
     */
    public function process_import() {
        global $CFG;

        $this->results = array();

        $mform = $this->get_import_form();

        if ($mform->is_cancelled()) {
            return null;
        }

        $this->_showimportform = true;
        if ($formdata = $mform->get_data()) {

            $data = new stdClass;
            $data->eids = array();
            $data->errors = array();

            $fieldsettings = array();
            // Collect field import settings from formdata by field, pattern and element.
            foreach ($formdata as $name => $value) {
                if (strpos($name, 'f_') !== false) {
                    list(, $fieldid, $pattern, $setting) = explode('_', $name);
                    if (!array_key_exists($fieldid, $fieldsettings)) {
                        $fieldsettings[$fieldid] = array();
                    } else if (!array_key_exists($pattern, $fieldsettings[$fieldid])) {
                        $fieldsettings[$fieldid][$pattern] = array();
                    }
                    $fieldsettings[$fieldid][$pattern][$setting] = $value;
                }
            }

            // If no field settings, there is nothing to do.
            if (!$fieldsettings) {
                return;
            }

            // Get the csv content.
            if (!empty($formdata->csvtext)) {
                // Upload from text.
                $csvcontent = $formdata->csvtext;
            } else {
                // Upload from file.
                $csvcontent = $mform->get_file_content('importfile');
            }

            // If no csv content, nothing to do.
            if (empty($csvcontent)) {
                return;
            }

            // Process the csv content.
            $options = array(
                'delimiter' => $formdata->delimiter,
                'enclosure' => ($formdata->enclosure ? $formdata->enclosure : ''),
                'encoding' => $formdata->encoding,
                'settings' => $fieldsettings,
                'updateexisting' => !empty($formdata->updateexisting),
                'addperparticipant' => !empty($formdata->addperparticipant),
            );
            $data = $this->process_csv($data, $csvcontent, $options);

            if ($data->errors) {
                foreach ($data->errors as $error) {
                    $result = array($error, 'problem');
                    $this->results = $this->results + array($result);
                }
                return;
            }

            // Test only, no errors.
            if (!empty($formdata->submitbutton_test)) {
                $result = array(get_string('noerrorsfound', 'dataformview_csv'), 'success');
                $this->results = $this->results + array($result);
            }

            // Execute.
            if (empty($formdata->submitbutton_test)) {
                $result = $this->execute_import($data);
                $this->results = $this->results + array($result);
            }
        }
    }

    /**
     * @return array
     */
    public function execute_import($data) {
        if ($data->eids) {
            list($strnotify, $processedeids) = $this->entry_manager->process_entries('update', $data->eids, $data, true);
            if ($processedeids) {
                $result = array($strnotify, 'success');
            } else if ($strnotify) {
                // Nothing processed but there may still be some notifications.
                $result = array($strnotify, 'problem');
            }
        } else {
            $result = array(get_string('nothingimported', 'dataformview_csv'), 'problem');
        }
        return $result;
    }

    /**
     *
     */
    public function get_csv_content($range = self::EXPORT_PAGE) {
        // Set content.
        $filter = $this->filter->clone;
        if ($range == self::EXPORT_ALL) {
            $filter->perpage = 0;
        }

        // Get the entries.
        $options = array('filter' => $filter);
        $entries = $this->entry_manager->fetch_entries($options);
        if (!$exportentries = $entries->entries) {
            return null;
        }

        // Get the field definitions
        // array(array(pattern => value,...)...).
        $entryvalues = array();
        foreach ($exportentries as $entryid => $entry) {
            $patternvalues = array();
            $definitions = $this->get_field_definitions($entry, array());
            foreach ($definitions as $pattern => $value) {
                if (is_array($value)) {
                    continue;
                }
                $patternvalues[$pattern] = $value;
            }
            $entryvalues[$entryid] = $patternvalues;
        }

        // Get csv headers from view columns.
        $columnpatterns = array();
        $csvheader = array();
        $columns = $this->get_columns();
        foreach ($columns as $column) {
            list($pattern, $header, ) = $column;
            $columnpatterns[] = $pattern;
            $csvheader[] = $header ? $header : trim($pattern, '[#]');
        }

        $csvcontent = array();
        $csvcontent[] = $csvheader;

        // Get the field definitions
        // array(array(pattern => value,...)...).
        foreach ($entryvalues as $entryid => $patternvalues) {
            $row = array();
            foreach ($columnpatterns as $pattern) {
                if (isset($patternvalues[$pattern])) {
                    $row[] = $patternvalues[$pattern];
                } else {
                    $row[] = $pattern;
                }
            }
            $csvcontent[] = $row;
        }

        return $csvcontent;
    }

    /**
     * @param array  $options associative delimiter,enclosure,encoding,updateexisting,settings
     */
    public function process_csv($data, $csvcontent, $options = null) {
        global $CFG, $DB;

        require_once("$CFG->libdir/csvlib.class.php");

        @set_time_limit(0);
        raise_memory_limit(MEMORY_EXTRA);

        $iid = \csv_import_reader::get_new_iid('moddataform');
        $cir = new \csv_import_reader($iid, 'moddataform');

        $delimiter = !empty($options['delimiter']) ? $options['delimiter'] : $this->_delimiter;
        $enclosure = !empty($options['enclosure']) ? $options['enclosure'] : $this->_enclosure;
        $encoding = !empty($options['encoding']) ? $options['encoding'] : $this->_encoding;
        $fieldsettings = !empty($options['settings']) ? $options['settings'] : array();

        $readcount = $cir->load_csv_content($csvcontent, $encoding, $delimiter);

        if (empty($readcount)) {
            $data->errors[] = $cir->get_error();
            return $data;
        }

        // Csv column headers.
        if (!$fieldnames = $cir->get_columns()) {
            $data->errors[] = $cir->get_error();
            return $data;
        }

        // Are we updating existing entries?
        $existingkeys = array();
        $keyname = null;
        if ($updateexisting = !empty($options['updateexisting'])) {
            if (isset($fieldnames['entryid'])) {
                $keyname = 'entryid';
            } else {
                $keyname = reset($fieldnames);
                if ($field = $this->df->field_manager->get_field_by_name($keyname)) {
                    $params = array('fieldid' => $field->id);
                    if ($recs = $DB->get_records('dataform_contents', $params, '', 'id,content,entryid')) {
                        foreach ($recs as $rec) {
                            $existingkeys[$rec->content] = $rec->entryid;
                        }
                    }
                }
            }
        }

        // Are we adding the imported entries to every participant?
        $addperparticipant = (!empty($options['addperparticipant']) and $users = $this->df->grade_manager->get_gradebook_users());

        $i = 0;
        $cir->init();

        while ($csvrecord = $cir->next()) {
            $csvrecord = array_combine($fieldnames, $csvrecord);

            // Add the entry for every participant.
            if ($addperparticipant) {
                foreach ($users as $userid => $unused) {

                    // Set the entry id.
                    $i++;
                    $entryid = -$i;
                    $data->eids[$entryid] = $entryid;
                    $data->{"entry_{$entryid}_userid"} = $userid;

                    // Iterate the fields and collate their entry content.
                    foreach ($fieldsettings as $fieldid => $importsettings) {
                        $field = $this->df->field_manager->get_field_by_id($fieldid);
                        $data = $field->prepare_import_content($data, $importsettings, $csvrecord, $entryid);
                    }
                }
                continue;
            }

            // Get the entry id.
            $entryid = 0;
            if ($updateexisting and $keyname) {
                if ($keyname == 'entryid') {
                    if (!empty($csvrecord['entryid'])) {
                        $entryid = $csvrecord['entryid'];
                    }
                } else if ($existingkeys and !empty($csvrecord[$keyname])) {
                    $entryid = $existingkeys[$csvrecord[$keyname]];
                }
            }

            if (!$entryid) {
                $i++;
                $entryid = -$i;
            }

            $data->eids[$entryid] = $entryid;

            // Iterate the fields and collate their entry content.
            foreach ($fieldsettings as $fieldid => $importsettings) {
                $field = $this->df->field_manager->get_field_by_id($fieldid);
                $data = $field->prepare_import_content($data, $importsettings, $csvrecord, $entryid);
            }
        }
        $cir->cleanup(true);
        $cir->close();

        return $data;
    }

    /**
     *
     */
    public function get_import_form() {
        global $CFG;

        $actionurl = new \moodle_url($this->get_baseurl(), array('importcsv' => 1));
        return new dataformview_csv_importform($this, $actionurl);
    }

    /**
     * Generates the default view template for a new view instance or when reseting an existing instance.
     * If content is specified, sets the template to the content.
     *
     * @param string $content HTML fragment.
     * @return void
     */
    public function set_default_view_template($content = null) {
        if ($content === null) {
            // Notifications.
            $notifications = \html_writer::tag('div', '##notifications##', array('class' => ''));

            // Export/import.
            $expimp = \html_writer::tag('div', '##exportall## | ##exportpage## | ##import##', array('class' => ''));

            // Add new entry.
            $addnewentry = \html_writer::tag('div', '##addnewentry##', array('class' => 'addnewentry-wrapper'));

            // Filtering.
            $quickfilters = \html_writer::tag('div', $this->get_default_filtering_template(), array('class' => 'quickfilters-wrapper'));

            // Paging bar.
            $pagingbar = \html_writer::tag('div', '##paging:bar##', array('class' => ''));
            // Entries.
            $entries = \html_writer::tag('div', '##entries##', array('class' => ''));

            // Set the view template.
            $exporthide = \html_writer::tag('div', $expimp. $addnewentry. $quickfilters. $pagingbar, array('class' => 'exporthide'));
            $content = \html_writer::tag('div', $exporthide. $entries);
        }
        $this->section = $content;
    }

    // GETTERS.
    /**
     *
     */
    public function get_delimiter() {
        return $this->_delimiter;
    }

    /**
     *
     */
    public function get_enclosure() {
        return $this->_enclosure;
    }

    /**
     *
     */
    public function get_encoding() {
        return $this->_encoding;
    }

    /**
     *
     */
    public function get_results() {
        return $this->_results;
    }

    /**
     *
     */
    public function set_results($value) {
        $this->_results = $value;
    }

    // HELPERS.
    /**
     * Comma delimited list of default csv settings.
     * This allows for not storing settings in DB unless different from default.
     *
     * @return string
     */
    public function get_default_csv_settings() {
        return 'comma, ,UTF-8';
    }

    /**
     * Returns display list of import results if any.
     *
     * @return string HTML fragment
     */
    public function print_report() {
        global $OUTPUT;

        $html = '';

        if ($this->results) {
            $html .= \html_writer::tag('h4', get_string('report'));
            foreach ($this->results as $result) {
                list($notification, $outcome) = $result;
                $html .= $OUTPUT->notification($notification, "notify$outcome");
            }
        }
        return $html;
    }

}
