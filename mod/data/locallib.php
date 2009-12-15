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
 * @package   mod-data
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @package   mod-data
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class data_portfolio_caller extends portfolio_module_caller_base {

    /** @var int */
    protected $recordid;
    /** @var string */
    protected $exporttype;
    /** @var string */
    protected $delimiter_name;

    /** @var object */
    private $data;
    /**#@+ @var array */
    private $selectedfields;
    private $fields;
    private $fieldtypes;
    private $exportdata;
    /**#@-*/
    /**#@+ @var object */
    private $singlerecord;
    private $singlefield;
    /**#@-*/
    /**
     * @return array
     */
    public static function expected_callbackargs() {
        return array(
            'id'             => true,
            'recordid'       => false,
            'delimiter_name' => false,
            'exporttype'     => false,
        );
    }
    /**
     * @param array $callbackargs
     */
    public function __construct($callbackargs) {
        parent::__construct($callbackargs);
        if (empty($this->exporttype)) {
            $this->exporttype = 'csv';
        }
        $this->selectedfields = array();
        foreach ($callbackargs as $key => $value) {
            if (strpos($key, 'field_') === 0) {
                $this->selectedfields[] = substr($key, 6);
            }
        }
    }

    /**
     * @global object
     */
    public function load_data() {
        global $DB;
        if (!$this->cm = get_coursemodule_from_id('data', $this->id)) {
            throw new portfolio_caller_exception('invalidid', 'data');
        }
        $this->data = $DB->get_record('data', array('id' => $this->cm->instance));
        $fieldrecords = $DB->get_records('data_fields', array('dataid'=>$this->cm->instance), 'id');
        // populate objets for this databases fields
        $this->fields = array();
        foreach ($fieldrecords as $fieldrecord) {
            $tmp = data_get_field($fieldrecord, $this->data);
            $this->fields[] = $tmp;
            $this->fieldtypes[]  = $tmp->type;
        }

        if ($this->recordid) {
            //user has selected to export one single entry rather than the whole thing
            // which is completely different
            $this->singlerecord = $DB->get_record('data_records', array('id' => $this->recordid));
            $this->singlerecord->content = $DB->get_records('data_content', array('recordid' => $this->singlerecord->id));
            $this->exporttype = 'single';

            list($formats, $files) = self::formats($this->fields, $this->singlerecord);
            if (count($files) == 1 && count($this->fields) == 1) {
                $this->singlefile = $files[0];
                $this->exporttype = 'singlefile';
            } else if (count($files) > 0) {
                $this->multifiles = $files;
            }
        } else {
            // all records as csv or whatever
            $this->exportdata = data_get_exportdata($this->cm->instance, $this->fields, $this->selectedfields);
        }
    }

    /**
     * @todo penny  later when we suport exporting to more than just csv, we may
     * need to ask the user here if we have not already passed it
     *
     * @return bool
     */
    public function has_export_config() {
        return false;
    }

    /**
     * @uses PORTFOLIO_TIME_LOW
     * @return mixed
     */
    public function expected_time() {
        if ($this->exporttype == 'single') {
            return PORTFOLIO_TIME_LOW;
        }
        return portfolio_expected_time_db(count($this->exportdata));
    }

    /**
     * @return string
     */
    public function get_sha1() {
        if ($this->exporttype == 'singlefile') {
            return $this->singlefile->get_contenthash();
        }
        $loopdata = $this->exportdata;
        if ($this->exporttype == 'single') {
            $loopdata = $this->singlerecord;
        }
        $str = '';
        foreach ($loopdata as $data) {
            if (is_array($data) || is_object($data)) {
                $testkey = array_pop(array_keys($data));
                if (is_array($data[$testkey]) || is_object($data[$testkey])) {
                    foreach ($data as $d) {
                        $str .= implode(',', (array)$d);
                    }
                } else {
                    $str .= implode(',', (array)$data);
                }
            } else {
                $str .= $data;
            }
        }
        return sha1($str . ',' . $this->exporttype);
    }
    /**
     * @global object
     */
    public function prepare_package() {
        global $DB;
        $count = count($this->exportdata);
        $content = '';
        $filename = '';
        switch ($this->exporttype) {
            case 'singlefile':
                return $this->get('exporter')->copy_existing_file($this->singlefile);
            case 'single':
                $content = $this->exportsingle();
                $filename = clean_filename($this->cm->name . '-entry.html');
                break;
            case 'csv':
                $content = data_export_csv($this->exportdata, $this->delimiter_name, $this->cm->name, $count, true);
                $filename = clean_filename($this->cm->name . '.csv');
                break;
            case 'xls':
                throw new portfolio_caller_exception('notimplemented', 'portfolio', '', 'xls');
                $content = data_export_xls($this->exportdata, $this->cm->name, $count, true);
                break;
            case 'ods':
                throw new portfolio_caller_exception('notimplemented', 'portfolio', '', 'ods');
                $content = data_export_ods($this->exportdata, $this->cm->name, $count, true);
                break;
            default:
                throw new portfolio_caller_exception('notimplemented', 'portfolio', '', $this->exporttype);
            break;
        }
        return $this->exporter->write_new_file(
            $content,
            $filename,
            ($this->exporter->get('format') instanceof PORTFOLIO_FORMAT_RICH) // if we have associate files, this is a 'manifest'
        );
    }

    /**
     * @return bool
     */
    public function check_permissions() {
        return has_capability('mod/data:exportallentries', get_context_instance(CONTEXT_MODULE, $this->cm->id));
    }

    /**
     *  @return string
     */
    public static function display_name() {
        return get_string('modulename', 'data');
    }

    /**
     * @global object
     * @return bool|void
     */
    public function __wakeup() {
        global $CFG;
        if (empty($CFG)) {
            return true; // too early yet
        }
        foreach ($this->fieldtypes as $key => $field) {
            require_once($CFG->dirroot . '/mod/data/field/' . $field .'/field.class.php');
            $this->fields[$key] = unserialize(serialize($this->fields[$key]));
        }
    }

    /**
     * @global object
     * @return string
     */
    private function exportsingle() {
        global $DB;
    // Replacing tags
        $patterns = array();
        $replacement = array();
        $context = get_context_instance(CONTEXT_MODULE, $this->cm->id);

    // Then we generate strings to replace for normal tags
        $format = $this->get('exporter')->get('format');
        foreach ($this->fields as $field) {
            $patterns[]='[['.$field->field->name.']]';
            if (is_callable(array($field, 'get_file'))) {
                // TODO this used to be:
                // if ($field instanceof data_field_file) {
                // - see  MDL-16493
                if (!$file = $field->get_file($this->singlerecord->id)) {
                    $replacement[] = '';
                    continue; // probably left empty
                }
                $replacement[] = $format->file_output($file);
                $this->get('exporter')->copy_existing_file($file);
            } else {
                $replacement[] = $field->display_browse_field($this->singlerecord->id, 'singletemplate');
            }
        }

    // Replacing special tags (##Edit##, ##Delete##, ##More##)
        $patterns[]='##edit##';
        $patterns[]='##delete##';
        $patterns[]='##export##';
        $patterns[]='##more##';
        $patterns[]='##moreurl##';
        $patterns[]='##user##';
        $patterns[]='##approve##';
        $patterns[]='##comments##';
        $patterns[] = '##timeadded##';
        $patterns[] = '##timemodified##';
        $replacement[] = '';
        $replacement[] = '';
        $replacement[] = '';
        $replacement[] = '';
        $replacement[] = '';
        $replacement[] = '';
        $replacement[] = '';
        $replacement[] = '';
        $replacement[] = userdate($this->singlerecord->timecreated);
        $replacement[] = userdate($this->singlerecord->timemodified);

        // actual replacement of the tags
        return str_ireplace($patterns, $replacement, $this->data->singletemplate);
    }

    /**
     * @param array $fields
     * @param object $record
     * @uses PORTFOLIO_FORMAT_PLAINHTML
     * @uses PORTFOLIO_FORMAT_RICHHTML
     * @return array
     */
    public static function formats($fields, $record) {
        $formats = array(PORTFOLIO_FORMAT_PLAINHTML);
        $includedfiles = array();
        foreach ($fields as $singlefield) {
            if (is_callable(array($singlefield, 'get_file'))) {
                $includedfiles[] = $singlefield->get_file($record->id);
            }
        }
        if (count($includedfiles) == 1 && count($fields) == 1) {
            $formats= array(portfolio_format_from_mimetype($includedfiles[0]->get_mimetype()));
        } else if (count($includedfiles) > 0) {
            $formats = array(PORTFOLIO_FORMAT_RICHHTML);
        }
        return array($formats, $includedfiles);
    }

    public static function base_supported_formats() {
        return array(PORTFOLIO_FORMAT_SPREADSHEET, PORTFOLIO_FORMAT_RICHHTML, PORTFOLIO_FORMAT_PLAINHTML);
    }
}
