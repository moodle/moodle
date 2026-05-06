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
 * Class used for uploading of one file into file storage,
 * the file name is stored in config table.
 *
 * Please note you need to implement your own '_pluginfile' callback function,
 * this setting only stores the file, it does not deal with file serving.
 *
 * @copyright 2013 Petr Skoda {@link http://skodak.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_configstoredfile extends admin_setting {
    /** @var array file area options - should be one file only */
    protected $options;
    /** @var string name of the file area */
    protected $filearea;
    /** @var int intemid */
    protected $itemid;
    /** @var string used for detection of changes */
    protected $oldhashes;

    /**
     * Create new stored file setting.
     *
     * @param string $name low level setting name
     * @param string $visiblename human readable setting name
     * @param string $description description of setting
     * @param mixed $filearea file area for file storage
     * @param int $itemid itemid for file storage
     * @param array $options file area options
     */
    public function __construct($name, $visiblename, $description, $filearea, $itemid = 0, ?array $options = null) {
        parent::__construct($name, $visiblename, $description, '');
        $this->filearea = $filearea;
        $this->itemid   = $itemid;
        $this->options  = (array)$options;
        $this->customcontrol = true;
    }

    /**
     * Applies defaults and returns all options.
     * @return array
     */
    protected function get_options() {
        global $CFG;

        require_once("$CFG->libdir/filelib.php");
        require_once("$CFG->dirroot/repository/lib.php");
        $defaults = array(
            'mainfile' => '', 'subdirs' => 0, 'maxbytes' => -1, 'maxfiles' => 1,
            'accepted_types' => '*', 'return_types' => FILE_INTERNAL, 'areamaxbytes' => FILE_AREA_MAX_BYTES_UNLIMITED,
            'context' => context_system::instance());
        foreach($this->options as $k => $v) {
            $defaults[$k] = $v;
        }

        return $defaults;
    }

    public function get_setting() {
        return $this->config_read($this->name);
    }

    public function write_setting($data) {
        global $USER;

        // Let's not deal with validation here, this is for admins only.
        $current = $this->get_setting();
        if (empty($data) && ($current === null || $current === '')) {
            // This will be the case when applying default settings (installation).
            return ($this->config_write($this->name, '') ? '' : get_string('errorsetting', 'admin'));
        } else if (!is_number($data)) {
            // Draft item id is expected here!
            return get_string('errorsetting', 'admin');
        }

        $options = $this->get_options();
        $fs = get_file_storage();
        $component = is_null($this->plugin) ? 'core' : $this->plugin;

        $this->oldhashes = null;
        if ($current) {
            $hash = sha1('/'.$options['context']->id.'/'.$component.'/'.$this->filearea.'/'.$this->itemid.$current);
            if ($file = $fs->get_file_by_hash($hash)) {
                $this->oldhashes = $file->get_contenthash().$file->get_pathnamehash();
            }
            unset($file);
        }

        if ($fs->file_exists($options['context']->id, $component, $this->filearea, $this->itemid, '/', '.')) {
            // Make sure the settings form was not open for more than 4 days and draft areas deleted in the meantime.
            // But we can safely ignore that if the destination area is empty, so that the user is not prompt
            // with an error because the draft area does not exist, as he did not use it.
            $usercontext = context_user::instance($USER->id);
            if (!$fs->file_exists($usercontext->id, 'user', 'draft', $data, '/', '.') && $current !== '') {
                return get_string('errorsetting', 'admin');
            }
        }

        file_save_draft_area_files($data, $options['context']->id, $component, $this->filearea, $this->itemid, $options);
        $files = $fs->get_area_files($options['context']->id, $component, $this->filearea, $this->itemid, 'sortorder,filepath,filename', false);

        $filepath = '';
        if ($files) {
            /** @var stored_file $file */
            $file = reset($files);
            $filepath = $file->get_filepath().$file->get_filename();
        }

        return ($this->config_write($this->name, $filepath) ? '' : get_string('errorsetting', 'admin'));
    }

    public function post_write_settings($original) {
        $options = $this->get_options();
        $fs = get_file_storage();
        $component = is_null($this->plugin) ? 'core' : $this->plugin;

        $current = $this->get_setting();
        $newhashes = null;
        if ($current) {
            $hash = sha1('/'.$options['context']->id.'/'.$component.'/'.$this->filearea.'/'.$this->itemid.$current);
            if ($file = $fs->get_file_by_hash($hash)) {
                $newhashes = $file->get_contenthash().$file->get_pathnamehash();
            }
            unset($file);
        }

        if ($this->oldhashes === $newhashes) {
            $this->oldhashes = null;
            return false;
        }
        $this->oldhashes = null;

        $callbackfunction = $this->updatedcallback;
        if (!empty($callbackfunction) and function_exists($callbackfunction)) {
            $callbackfunction($this->get_full_name());
        }
        return true;
    }

    public function output_html($data, $query = '') {
        global $CFG;

        $options = $this->get_options();
        $id = $this->get_id();
        $elname = $this->get_full_name();
        $draftitemid = file_get_submitted_draft_itemid($elname);
        $component = is_null($this->plugin) ? 'core' : $this->plugin;
        file_prepare_draft_area($draftitemid, $options['context']->id, $component, $this->filearea, $this->itemid, $options);

        // Filemanager form element implementation is far from optimal, we need to rework this if we ever fix it...
        require_once("$CFG->dirroot/lib/form/filemanager.php");

        $fmoptions = new stdClass();
        $fmoptions->mainfile       = $options['mainfile'];
        $fmoptions->maxbytes       = $options['maxbytes'];
        $fmoptions->maxfiles       = $options['maxfiles'];
        $fmoptions->subdirs        = $options['subdirs'];
        $fmoptions->accepted_types = $options['accepted_types'];
        $fmoptions->return_types   = $options['return_types'];
        $fmoptions->context        = $options['context'];
        $fmoptions->areamaxbytes   = $options['areamaxbytes'];

        $fm = new MoodleQuickForm_filemanager($elname, $this->visiblename, ['id' => $id], $fmoptions);
        $fm->setValue($draftitemid);

        return format_admin_setting($this, $this->visiblename,
            '<div class="form-filemanager" data-fieldtype="filemanager">' . $fm->toHtml() . '</div>',
            $this->description, true, '', '', $query);
    }
}
