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
//
// this file contains all the functions that aren't needed by core moodle
// but start becoming required once we're actually inside the assignment module.

/**
 * This file contains the callback class required by the portfolio api.
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/** Include assign locallib.php */
require_once($CFG->dirroot . '/mod/assign/locallib.php');
/** Include portfolio caller.php */
require_once($CFG->libdir . '/portfolio/caller.php');

/**
 * portfolio caller class for mod_assign.
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_portfolio_caller extends portfolio_module_caller_base {


    /** @var int callback arg - the id of submission we export */
    protected $sid;

    /** @var string component of the submission files we export*/
    protected $component;

    /** @var string callback arg - the area of submission files we export */
    protected $area;

    /** @var int callback arg - the id of file we export */
    protected $fileid;

    /** @var int callback arg - the cmid of the assignment we export */
    protected $cmid;

    /** @var string callback arg - the plugintype of the editor we export */
    protected $plugin;

    /** @var string callback arg - the name of the editor field we export */
    protected $editor;

   /**
    * callback arg for a single file export
    */
    public static function expected_callbackargs() {
        return array(
            'cmid' => true,
            'sid' => false,
            'area' => false,
            'component' => false,
            'fileid' => false,
            'plugin' => false,
            'editor' => false,
       );
    }

    /**
     * the constructor
     * @param array $callbackargs
     */
    function __construct($callbackargs) {
        parent::__construct($callbackargs);
        $this->cm = get_coursemodule_from_id('assign', $this->cmid, 0, false, MUST_EXIST);
    }

    /**
     *
     * Load data needed for the portfolio export
     *
     * If the assignment type implements portfolio_load_data(), the processing is delegated
     * to it. Otherwise, the caller must provide either fileid (to export single file) or
     * submissionid and filearea (to export all data attached to the given submission file area) via callback arguments.
     *
     * @throws     portfolio_caller_exception
     */
    public function load_data() {

        $context = context_module::instance($this->cmid);

        if (empty($this->fileid)) {
            if (empty($this->sid) || empty($this->area)) {
                throw new portfolio_caller_exception('invalidfileandsubmissionid', 'mod_assign');
            }

        }

        // export either an area of files or a single file (see function for more detail)
        // the first arg is an id or null. If it is an id, the rest of the args are ignored
        // if it is null, the rest of the args are used to load a list of files from get_areafiles
        $this->set_file_and_format_data($this->fileid, $context->id, $this->component, $this->area, $this->sid, 'timemodified', false);

    }

    /**
     * prepares the package up before control is passed to the portfolio plugin.
     *
     * @throws portfolio_caller_exception
     * @return mixed
     */
    public function prepare_package() {

        if ($this->plugin && $this->editor) {
            $options = portfolio_format_text_options();
            $context = context_module::instance($this->cmid);
            $options->context = $context;

            $plugin = $this->get_submission_plugin();

            $text = $plugin->get_editor_text($this->editor, $this->sid);
            $format = $plugin->get_editor_format($this->editor, $this->sid);

            $html = format_text($text, $format, $options);
            $html = portfolio_rewrite_pluginfile_urls($html, $context->id, 'mod_assign', $this->area, $this->sid, $this->exporter->get('format'));

            if (in_array($this->exporter->get('formatclass'), array(PORTFOLIO_FORMAT_PLAINHTML, PORTFOLIO_FORMAT_RICHHTML))) {
                if ($files = $this->exporter->get('caller')->get('multifiles')) {
                    foreach ($files as $file) {
                        $this->exporter->copy_existing_file($file);
                    }
                }
                return $this->exporter->write_new_file($html, 'assignment.html', !empty($files));
            } else if ($this->exporter->get('formatclass') == PORTFOLIO_FORMAT_LEAP2A) {
                $leapwriter = $this->exporter->get('format')->leap2a_writer();
                $entry = new portfolio_format_leap2a_entry($this->area . $this->cmid, print_context_name($context), 'resource', $html);

                $entry->add_category('web', 'resource_type');
                $entry->author = $this->user;
                $leapwriter->add_entry($entry);
                if ($files = $this->exporter->get('caller')->get('multifiles')) {
                    $leapwriter->link_files($entry, $files, $this->area . $this->cmid . 'file');
                    foreach ($files as $file) {
                        $this->exporter->copy_existing_file($file);
                    }
                }
                return $this->exporter->write_new_file($leapwriter->to_xml(), $this->exporter->get('format')->manifest_name(), true);
            } else {
                debugging('invalid format class: ' . $this->exporter->get('formatclass'));
            }

        }


        if ($this->exporter->get('formatclass') == PORTFOLIO_FORMAT_LEAP2A) {
            $leapwriter = $this->exporter->get('format')->leap2a_writer();
            $files = array();
            if ($this->singlefile) {
                $files[] = $this->singlefile;
            } elseif ($this->multifiles) {
                $files = $this->multifiles;
            } else {
                throw new portfolio_caller_exception('invalidpreparepackagefile', 'portfolio', $this->get_return_url());
            }

            $entryids = array();
            foreach ($files as $file) {
                $entry = new portfolio_format_leap2a_file($file->get_filename(), $file);
                $entry->author = $this->user;
                $leapwriter->add_entry($entry);
                $this->exporter->copy_existing_file($file);
                $entryids[] = $entry->id;
            }
            if (count($files) > 1) {
                $baseid = 'assign' . $this->cmid . $this->area;
                $context = context_module::instance($this->cmid);

                // if we have multiple files, they should be grouped together into a folder
                $entry = new portfolio_format_leap2a_entry($baseid . 'group', print_context_name($context), 'selection');
                $leapwriter->add_entry($entry);
                $leapwriter->make_selection($entry, $entryids, 'Folder');
            }
            return $this->exporter->write_new_file($leapwriter->to_xml(), $this->exporter->get('format')->manifest_name(), true);
        }
        return $this->prepare_package_file();
    }

    /**
     * fetch the plugin by its type
     *
     * @return assign_submission_plugin
     */
    private function get_submission_plugin() {
        global $CFG;
        if (!$this->plugin || !$this->cmid) {
            return null;
        }

        require_once($CFG->dirroot . '/mod/assign/locallib.php');

        $context = context_module::instance($this->cmid);

        $assignment = new assign($context, null, null);
        return $assignment->get_submission_plugin_by_type($this->plugin);
    }

    /**
     * get sha1 file
     * calculate a sha1 has of either a single file or a list
     * of files based on the data set by load_data
     *
     * @return string
     */
    public function get_sha1() {

        if ($this->plugin && $this->editor) {
            $plugin = $this->get_submission_plugin();
            $options = portfolio_format_text_options();
            $options->context = context_module::instance($this->cmid);

            $textsha1 = sha1(format_text($plugin->get_editor_text($this->editor, $this->sid),
                                         $plugin->get_editor_format($this->editor, $this->sid), $options));
            $filesha1 = '';
            try {
                $filesha1 = $this->get_sha1_file();
            } catch (portfolio_caller_exception $e) {} // no files
            return sha1($textsha1 . $filesha1);
        }
        return $this->get_sha1_file();
    }

    /**
     * calculate the time to transfer either a single file or a list
     * of files based on the data set by load_data
     *
     * @return int
     */
    public function expected_time() {
        return $this->expected_time_file();
    }

    /**
     * checking the permissions
     *
     * @return bool
     */
    public function check_permissions() {
        $context = context_module::instance($this->cmid);
        return has_capability('mod/assign:exportownsubmission', $context);
    }

    /**
     * display a module name
     *
     * @return string
     */
    public static function display_name() {
        return get_string('modulename', 'assign');
    }

    /**
     * return array of formats supported by this portfolio call back
     * @return array
     */
    public static function base_supported_formats() {

        return array(PORTFOLIO_FORMAT_FILE, PORTFOLIO_FORMAT_LEAP2A);

    }
}

