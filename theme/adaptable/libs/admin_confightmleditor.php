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
 * Version details
 *
 * @package    theme_adaptable
 * @copyright 2015 Jeremy Hopkins (Coventry University)
 * @copyright 2015 Fernando Acedo (3-bits.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

/**
 * @copyright 2015 Jeremy Hopkins (Coventry University)
 * @copyright 2015 Fernando Acedo (3-bits.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * Class to configure html editor for admin settings allowing use of repositories
 *
 * Special thanks to Iban Cardona i Subiela (http://icsbcn.blogspot.com.es/2015/03/use-image-repository-in-theme-settings.html)
 * This post laid the ground work for most of the code featured in this file.
 *
 */

defined('MOODLE_INTERNAL') || die;

class adaptable_setting_confightmleditor extends admin_setting_configtext {

    /** @var int number of rows */
    private $rows;

    /** @var int number of columns */
    private $cols;

    /** @var string options - looks like this unused and should be removed */
    private $options;

    /** @var string filearea - filearea within Moodle repository API */
    private $filearea;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $visiblename
     * @param string $description
     * @param mixed $defaultsetting string or array
     * @param mixed $paramtype
     * @param int $cols
     * @param int $rows
     * @param string $filearea
     */
    public function __construct($name, $visiblename, $description, $defaultsetting,
                                $paramtype=PARAM_RAW, $cols='60', $rows='8',
                                $filearea = 'adaptablemarketingimages') {
        $this->rows = $rows;
        $this->cols = $cols;
        $this->filearea = $filearea;
        parent::__construct($name, $visiblename, $description, $defaultsetting, $paramtype);
        editors_head_setup();
    }

    /**
     * get options
     */
    private function get_options() {
        global $USER;

        $default = array();
        $default['noclean'] = false;
        $default['context'] = context_user::instance($USER->id);
        $default['maxbytes'] = 0;
        $default['maxfiles'] = -1;
        $default['forcehttps'] = false;
        $default['subdirs'] = false;
        $default['changeformat'] = 0;
        $default['areamaxbytes'] = FILE_AREA_MAX_BYTES_UNLIMITED;
        $default['return_types'] = (FILE_INTERNAL | FILE_EXTERNAL);

        return $default;
    }

    /**
     * Returns an XHTML string for the editor
     *
     * @param string $data
     * @param string $query
     * @return string XHTML string for the editor
     */
    public function output_html($data, $query='') {
        global $USER;

        $default = $this->get_defaultsetting();

        $defaultinfo = $default;
        if (!is_null($default) and $default !== '') {
            $defaultinfo = "\n".$default;
        }

        $ctx = context_user::instance($USER->id);
        $editor = editors_get_preferred_editor(FORMAT_HTML);
        $options = $this->get_options();
        $draftitemid = file_get_unused_draft_itemid();
        $component = is_null($this->plugin) ? 'core' : $this->plugin;
        $data = file_prepare_draft_area($draftitemid, $options['context']->id,
                                        $component, $this->get_full_name().'_draftitemid',
                                        $draftitemid, $options, $data);

        $fpoptions = array();
        $args = new stdClass();

        // Need these three to filter repositories list.
        $args->accepted_types = array('web_image');
        $args->return_types = $options['return_types'];
        $args->context = $ctx;
        $args->env = 'filepicker';

        // Advimage plugin.
        $imageoptions = initialise_filepicker($args);
        $imageoptions->context = $ctx;
        $imageoptions->client_id = uniqid();
        $imageoptions->maxbytes = $options['maxbytes'];
        $imageoptions->areamaxbytes = $options['areamaxbytes'];
        $imageoptions->env = 'editor';
        $imageoptions->itemid = $draftitemid;

        // Moodlemedia plugin.
        $args->accepted_types = array('video', 'audio');
        $mediaoptions = initialise_filepicker($args);
        $mediaoptions->context = $ctx;
        $mediaoptions->client_id = uniqid();
        $mediaoptions->maxbytes  = $options['maxbytes'];
        $mediaoptions->areamaxbytes  = $options['areamaxbytes'];
        $mediaoptions->env = 'editor';
        $mediaoptions->itemid = $draftitemid;

        // Advlink plugin.
        $args->accepted_types = '*';
        $linkoptions = initialise_filepicker($args);
        $linkoptions->context = $ctx;
        $linkoptions->client_id = uniqid();
        $linkoptions->maxbytes  = $options['maxbytes'];
        $linkoptions->areamaxbytes  = $options['areamaxbytes'];
        $linkoptions->env = 'editor';
        $linkoptions->itemid = $draftitemid;

        $fpoptions['image'] = $imageoptions;
        $fpoptions['media'] = $mediaoptions;
        $fpoptions['link'] = $linkoptions;

        $editor->use_editor($this->get_id(), $options, $fpoptions);

        return format_admin_setting($this, $this->visiblename,
        '<div class="form-textarea">
         <textarea rows="'. $this->rows .'" cols="'. $this->cols .'" id="'. $this->get_id() .'" name="'.$this->get_full_name()
         .'"spellcheck="true">'. s($data) .'
         </textarea>
         </div>
        <input value="'.$draftitemid.'" name="'.$this->get_full_name().'_draftitemid" type="hidden" />',
        $this->description, true, '', $defaultinfo, $query);
    }

    /**
     * Handle file writes to repository
     *
     * @param string $data
     */
    public function write_setting($data) {
        global $CFG;

        if ($this->paramtype === PARAM_INT and $data === '') {
            // ... do not complain if '' used instead of 0 !
            $data = 0;
        }
        // ... $data is a string.
        $validated = $this->validate($data);
        if ($validated !== true) {
            return $validated;
        }

        $options = $this->get_options();
        $fs = get_file_storage();
        $component = is_null($this->plugin) ? 'core' : $this->plugin;
        $wwwroot = $CFG->wwwroot;
        if ($options['forcehttps']) {
            $wwwroot = str_replace('http://', 'https://', $wwwroot);
        }

        $draftitemid = $_REQUEST[$this->get_full_name().'_draftitemid'];
        $draftfiles = $fs->get_area_files($options['context']->id, 'user', 'draft', $draftitemid, 'id');
        foreach ($draftfiles as $file) {
            if (!$file->is_directory()) {
                $strtosearch = "$wwwroot/draftfile.php/".$options['context']->id."/user/draft/$draftitemid/".$file->get_filename();
                if (stripos($data, $strtosearch) !== false) {
                    $filerecord = array(
                        'contextid' => context_system::instance()->id,
                        'component' => $component,
                        'filearea' => $this->filearea,
                        'filename' => $file->get_filename(),
                        'filepath' => '/',
                        'itemid' => 0,
                        'timemodified' => time()
                    );
                    if (!$filerec = $fs->get_file($filerecord['contextid'],
                                                  $filerecord['component'],
                                                  $filerecord['filearea'],
                                                  $filerecord['itemid'],
                                                  $filerecord['filepath'],
                                                  $filerecord['filename'])) {
                        $filerec = $fs->create_file_from_storedfile($filerecord, $file);
                    }
                    $url = moodle_url::make_pluginfile_url($filerec->get_contextid(),
                                                           $filerec->get_component(),
                                                           $filerec->get_filearea(),
                                                           $filerec->get_itemid(),
                                                           $filerec->get_filepath(),
                                                           $filerec->get_filename());
                    $data = str_ireplace($strtosearch, $url, $data);
                }
            }
        }

        return ($this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin'));
    }
}
