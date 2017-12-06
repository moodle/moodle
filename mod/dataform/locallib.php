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
 * @package mod_dataform
 * @copyright 2012 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * The Dataform has been developed as an enhanced counterpart
 * of Moodle's Database activity module (1.9.11+ (20110323)).
 * To the extent that Dataform code corresponds to Database code,
 * certain copyrights on the Database module may obtain.
 */

require_once("$CFG->libdir/portfolio/caller.php");
require_once("$CFG->libdir/filelib.php");

/**
 * The class to handle entry exports of a dataform module
 */
class dataform_portfolio_caller extends portfolio_module_caller_base {

    const CONTENT_NOFILES = 0;
    const CONTENT_WITHFILES = 1;
    const CONTENT_FILESONLY = 2;

    /**
     * The required callback arguments for export.
     *
     * @return array
     */
    public static function expected_callbackargs() {
        return array(
            'id' => true,
            'vid' => true,
            'fid' => true,
            'eids' => false,
            'ecount' => false,  // Number of entries for full exports.
        );
    }

    /**
     * @return string
     */
    public static function display_name() {
        return get_string('modulename', 'dataform');
    }

    /**
     * Base supported formats before we know anything about the export.
     */
    public static function base_supported_formats() {
        return array(
            // PORTFOLIO_FORMAT_SPREADSHEET,
            // PORTFOLIO_FORMAT_RICHHTML,
            // PORTFOLIO_FORMAT_DOCUMENT,
            // PORTFOLIO_FORMAT_LEAP2A.
        );
    }

    /**
     * Get files to export if any.
     *
     * @global object $DB
     */
    public function load_data() {
        if (!$this->cm = get_coursemodule_from_id('dataform', $this->id)) {
            throw new portfolio_caller_exception('invalidid', 'dataform');
        }
    }

    /**
     * How long we think the export will take.
     *
     * @return one of PORTFOLIO_TIME_XX constants.
     */
    public function expected_time() {
        // By number of exported entries.
        if (!empty($this->eids)) {
            $dbtime = portfolio_expected_time_db(count(explode(',', $this->eids)));
        } else if (!empty($this->ecount)) {
            $dbtime = portfolio_expected_time_db($this->ecount);
        } else {
            $dbtime = PORTFOLIO_TIME_HIGH;
        }

        // Only if export includes embedded files but this is in config and not
        // yet accessible here ....
        $filetime = PORTFOLIO_TIME_HIGH;

        return ($filetime > $dbtime) ? $filetime : $dbtime;
    }

    /**
     * Calculate the sha1 of this export
     * Dependent on the export format.
     * @return string
     */
    public function get_sha1() {
        return sha1(serialize(array($this->id, $this->vid, $this->fid, $this->eids)));
    }

    /**
     * Prepare the package for export.
     *
     * @return stored_file object
     */
    public function prepare_package() {
        // Set the exported view content.
        $df = mod_dataform_dataform::instance(null, $this->id);
        $viewman = mod_dataform_view_manager::instance($df->id);
        $view = $viewman->get_view_by_id($this->vid);
        $view->set_viewfilter(array('filterid' => $this->fid, 'eids' => $this->eids));

        // Export to spreadsheet.
        if ($this->exporter->get('formatclass') == PORTFOLIO_FORMAT_SPREADSHEET) {
            $content = $view->display(array('controls' => false));
            $filename = clean_filename($view->name. '-full.'. $this->get_export_config('spreadsheettype'));
            $this->exporter->write_new_file($content, $filename);
            return;
        }

        // Export to html.
        if ($this->exporter->get('formatclass') == PORTFOLIO_FORMAT_RICHHTML) {
            $exportfiles = $this->get_export_config('contentformat');

            // Collate embedded files (view and field).
            if ($exportfiles) {
                if ($files = $view->get_embedded_files()) {
                    foreach ($files as $file) {
                        $this->exporter->copy_existing_file($file);
                    }
                }
            }

            // Export content.
            if ($exportfiles != self::CONTENT_FILESONLY) {
                $content = $view->display(array('controls' => false,
                                                'pluginfileurl' => $this->exporter->get('format')->get_file_directory()));
                $filename = clean_filename($view->name. '-full.htm');
                $this->exporter->write_new_file($content, $filename);
            }
            return;
        }

    }

    /**
     * verify the user can export the requested entries
     *
     * @return bool
     */
    public function check_permissions() {
        // Verification is done in the view so just return true.
        return true;
    }

    /**
     * @return bool
     */
    public function has_export_config() {
        return true;
    }

    /**
     *
     */
    public function export_config_form(&$mform, $instance) {
        if (!$this->has_export_config()) {
            return;
        }

        // Spreadsheet selection.
        $types = array('csv', 'ods', 'xls');
        $options = array_combine($types, $types);
        $mform->addElement('select', 'caller_spreadsheettype', get_string('spreadsheettype', 'dataform'), $options);
        $mform->setDefault('caller_spreadsheettype', 'csv');
        $mform->disabledIf('caller_spreadsheettype', 'format', 'neq', PORTFOLIO_FORMAT_SPREADSHEET);

        // Export content.
        $options = array(self::CONTENT_NOFILES => 'Exclude embedded files',
                        self::CONTENT_WITHFILES => 'Include embedded files',
                        self::CONTENT_FILESONLY => 'embedded files only');
        $mform->addElement('select', 'caller_contentformat', get_string('exportcontent', 'dataform'), $options);
        $mform->setDefault('caller_contentformat', self::CONTENT_NOFILES);
        $mform->disabledIf('caller_contentformat', 'format', 'neq', PORTFOLIO_FORMAT_RICHHTML);

        /*
        // Document selection.
        $types = array('htm', 'txt');
        $options = array_combine($types, $types);
        $mform->addElement('select', 'caller_documenttype', get_string('documenttype', 'dataform'), $options);
        $mform->setDefault('caller_documenttype', 'htm');
        $mform->disabledIf('caller_documenttype', 'format', 'neq', PORTFOLIO_FORMAT_DOCUMENT);
        $mform->disabledIf('caller_documenttype', 'caller_content', 'eq', self::CONTENT_FILESONLY);
        */

        // Each entry in a separate file.
        $mform->addElement('selectyesno', 'caller_separateentries', get_string('separateentries', 'dataform'));

    }

    /**
     *
     */
    public function get_allowed_export_config() {
        return array('spreadsheettype', 'documenttype', 'contentformat', 'separateentries');
    }

    /**
     *
     */
    public function get_return_url() {
        global $CFG;

        $returnurl = new moodle_url('/mod/dataform/view.php', array('id'  => $this->id, 'view' => $this->vid, 'filter' => $this->fid));;
        return $returnurl->out(false);
    }
}

/**
 * Class representing the virtual node with all itemids in the file browser
 *
 * @category  files
 * @copyright 2012 Itamar Tzadok
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dataform_file_info_container extends file_info {
    /** @var file_browser */
    protected $browser;
    /** @var stdClass */
    protected $course;
    /** @var stdClass */
    protected $cm;
    /** @var string */
    protected $component;
    /** @var stdClass */
    protected $context;
    /** @var array */
    protected $areas;
    /** @var string */
    protected $filearea;

    /**
     * Constructor (in case you did not realize it ;-)
     *
     * @param file_browser $browser
     * @param stdClass $course
     * @param stdClass $cm
     * @param stdClass $context
     * @param array $areas
     * @param string $filearea
     */
    public function __construct($browser, $course, $cm, $context, $areas, $filearea) {
        parent::__construct($browser, $context);
        $this->browser = $browser;
        $this->course = $course;
        $this->cm = $cm;
        $this->component = 'mod_dataform';
        $this->context = $context;
        $this->areas = $areas;
        $this->filearea = $filearea;
    }

    /**
     * @return array with keys contextid, filearea, itemid, filepath and filename
     */
    public function get_params() {
        return array(
            'contextid' => $this->context->id,
            'component' => $this->component,
            'filearea' => $this->filearea,
            'itemid' => null,
            'filepath' => null,
            'filename' => null,
        );
    }

    /**
     * Can new files or directories be added via the file browser
     *
     * @return bool
     */
    public function is_writable() {
        return false;
    }

    /**
     * Should this node be considered as a folder in the file browser
     *
     * @return bool
     */
    public function is_directory() {
        return true;
    }

    /**
     * Returns localised visible name of this node
     *
     * @return string
     */
    public function get_visible_name() {
        return $this->areas[$this->filearea];
    }

    /**
     * Returns list of children nodes
     *
     * @return array of file_info instances
     */
    public function get_children() {
        global $DB;

        $children = array();
        $itemids = $DB->get_records('files', array('contextid' => $this->context->id, 'component' => $this->component,
            'filearea' => $this->filearea), 'itemid DESC', "DISTINCT itemid");
        foreach ($itemids as $itemid => $unused) {
            if ($child = $this->browser->get_file_info($this->context, 'mod_dataform', $this->filearea, $itemid)) {
                $children[] = $child;
            }
        }

        return $children;
    }

    /**
     * Returns parent file_info instance
     *
     * @return file_info or null for root
     */
    public function get_parent() {
        return $this->browser->get_file_info($this->context);
    }
}
