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
 * @package mod
 * @package preset
 * @copyright 2012 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->libdir/filelib.php");

/**
 * Preset manager class
 */
class mod_dataform_preset_manager {

    const PRESET_COURSEAREA = 'course_presets';
    const PRESET_SITEAREA = 'site_presets';

    protected $_dataformid;

    public static function instance($dataformid) {
        if (!$instance = \mod_dataform_instance_store::instance($dataformid, 'preset_manager')) {
            $instance = new mod_dataform_preset_manager($dataformid);
            \mod_dataform_instance_store::register($dataformid, 'preset_manager', $instance);
        }

        return $instance;
    }

    /**
     * constructor
     */
    public function __construct($dataformid) {
        $this->_dataformid = $dataformid;
    }

    /**
     * Returns an array of the shared presets (in moodledata) the user is allowed to access
     * @param in $presetarea  PRESET_COURSEAREA/PRESET_SITEAREA
     */
    public function get_user_presets($presetarea) {
        global $USER, $PAGE;

        $presets = array();

        $fs = get_file_storage();
        if ($presetarea == self::PRESET_COURSEAREA) {
            $df = mod_dataform_dataform::instance($this->_dataformid);
            $context = context_course::instance($df->course->id);
            $files = $fs->get_area_files($context->id, 'mod_dataform', $presetarea);
        } else if ($presetarea == self::PRESET_SITEAREA) {
            $context = context_system::instance();
            $files = $fs->get_area_files(SYSCONTEXTID, 'mod_dataform', $presetarea);
        }
        $canviewall = has_capability('mod/dataform:presetsviewall', $context);
        if (!empty($files)) {
            foreach ($files as $file) {
                if ($file->is_directory() || ($file->get_userid() != $USER->id and !$canviewall)) {
                    continue;
                }
                $preset = new stdClass;
                $preset->contextid = $file->get_contextid();
                $preset->path = $file->get_filepath();
                $preset->name = $file->get_filename();
                $preset->shortname = pathinfo($preset->name, PATHINFO_FILENAME);
                $preset->userid = $file->get_userid();
                $preset->itemid = $file->get_itemid();
                $preset->id = $file->get_id();
                $preset->area = $presetarea;
                $presets[] = $preset;
            }
        }

        return $presets;
    }

    /**
     *
     */
    public function get_course_presets_list($presets) {
        // Labels.
        $strs = $this->get_strings();

        // Bulk download.
        $multidownload = $this->get_action_icon_multidownload();
        // Bulk delete.
        $multidelete = $this->get_action_icon_multidelete();
        // Bulk share.
        $multishare = $this->get_action_icon_multishare();
        // Select all none.
        $selectallnone = $this->get_action_icon_selectallnone();

        $multiactions = array($multishare, $multidownload, $multidelete, $selectallnone);

        $table = new html_table();
        $table->head = array($strs->name, $strs->description, $strs->screenshot, $strs->apply, implode('&nbsp;', $multiactions));
        $table->align = array('left', 'left', 'center', 'right', 'right');
        $table->wrap = array(false, false, false, false);
        $table->attributes['class'] = 'generaltable coursepresets';

        foreach ($presets as $preset) {
            $presetname = $preset->shortname;
            $presetdescription = '';
            $presetscreenshot = '';

            // Apply.
            $presetapply = $this->get_action_icon_apply($preset);
            // Share.
            $presetshare = $this->get_action_icon_share($preset);
            // Download.
            $presetdownload = $this->get_action_icon_download($preset, 'course_presets');
            // Delete.
            $presetdelete = $this->get_action_icon_delete($preset);
            // Selector.
            $presetselector = html_writer::checkbox("presetselector", $preset->id, false, null, array('class' => 'presetselector'));

            $table->data[] = array(
                $presetname,
                $presetdescription,
                $presetscreenshot,
                $presetapply,
                implode('&nbsp;', array($presetshare, $presetdownload, $presetdelete, $presetselector))
            );
        }
        return html_writer::table($table);
    }

    /**
     *
     */
    public function get_site_presets_list($presets) {
        // Labels.
        $strs = $this->get_strings();

        // Bulk download.
        $multidownload = $this->get_action_icon_multidownload();
        // Bulk delete.
        $multidelete = $this->get_action_icon_multidelete();
        // Select all none.
        $selectallnone = $this->get_action_icon_selectallnone();

        $multiactions = array($multidownload, $multidelete, $selectallnone);

        $table = new html_table();
        $table->head = array($strs->name, $strs->description, $strs->screenshot, $strs->apply, implode('&nbsp;', $multiactions));
        $table->align = array('left', 'left', 'center', 'right', 'right');
        $table->wrap = array(false, false, false, false, false);

        $table->attributes['class'] = 'generaltable sitepresets';
        foreach ($presets as $preset) {

            $presetname = $preset->shortname;
            $presetdescription = '';
            $presetscreenshot = '';

            // Apply.
            $presetapply = $this->get_action_icon_apply($preset);
            // Share.
            $presetshare = '';
            // Download.
            $presetdownload = $this->get_action_icon_download($preset, self::PRESET_SITEAREA);
            // Delete.
            $presetdelete = $this->get_action_icon_delete($preset, self::PRESET_SITEAREA);
            // Selector.
            $presetselector = html_writer::checkbox("presetselector", $preset->id, false, null, array('class' => 'presetselector'));

            $table->data[] = array(
                $presetname,
                $presetdescription,
                $presetscreenshot,
                $presetapply,
                implode('&nbsp;', array($presetdownload, $presetdelete, $presetselector))
            );
        }
        return html_writer::table($table);
    }

    /**
     *
     */
    public function print_preset_form() {
        echo html_writer::start_tag('div', array('style' => 'width:80%;margin:auto;'));
        $url = new moodle_url($this->get_base_url(), array('sesskey' => sesskey(), 'add' => 1));
        $mform = new mod_dataform\pluginbase\dataformpresetform($url, array('dataformid' => $this->_dataformid));
        $mform->set_data(null);
        $mform->display();
        echo html_writer::end_tag('div');
    }

    /**
     *
     */
    public function process_presets($params) {
        global $CFG;

        $url = new moodle_url($this->get_base_url(), array('sesskey' => sesskey(), 'add' => 1));
        $mform = new mod_dataform\pluginbase\dataformpresetform($url, array('dataformid' => $this->_dataformid));
        // Add presets.
        if ($data = $mform->get_data()) {
            if (!empty($data->preset_source) and $data->preset_source == 'current') {
                // Preset this dataform.
                $this->create_preset_from_backup($data->preset_data);

            } else {
                // Upload presets.
                $this->create_preset_from_upload($data->uploadfile);
            }
        } else if (!empty($params->apply) and confirm_sesskey()) {
            // Apply a preset.
            if ($this->apply_preset($params->apply, $params->torestorer)) {
                // Rebuild course cache to show new dataform name on the course page.
                $df = mod_dataform_dataform::instance($this->_dataformid);
                rebuild_course_cache($df->course->id);
                redirect(new moodle_url('/mod/dataform/view.php', array('d' => $this->_dataformid)));
            }

        } else if (!empty($params->download) and confirm_sesskey()) {
            // Download (bulk in zip).
            $this->download_presets($params->download);

        } else if (!empty($params->share) and confirm_sesskey()) {
            // Share presets.
            $this->share_presets($params->share);
            redirect($this->get_base_url());

        } else if (!empty($params->delete) and confirm_sesskey()) {
            // Delete presets.
            $this->delete_presets($params->delete);
            redirect($this->get_base_url());
        }
    }

    /**
     *
     */
    public function create_preset_from_backup($userdata) {
        global $CFG, $USER, $SESSION;

        require_once("$CFG->dirroot/backup/util/includes/backup_includes.php");

        $df = mod_dataform_dataform::instance($this->_dataformid);

        $users = 0;
        $anon = 0;
        switch ($userdata) {
            case 'dataanon':
                $anon = 1;
            case 'data':
                $users = 1;
        }

        // Store preset settings in $SESSION.
        $SESSION->{"dataform_{$df->cm->id}_preset"} = "$users $anon";

        $bc = new backup_controller(
            backup::TYPE_1ACTIVITY,
            $df->cm->id,
            backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO,
            backup::MODE_GENERAL,
            $USER->id
        );

        // Clear preset settings from $SESSION.
        unset($SESSION->{"dataform_{$df->cm->id}_preset"});

        // Set users and anon in plan.
        $bc->get_plan()->get_setting('users')->set_value($users);
        $bc->get_plan()->get_setting('anonymize')->set_value($anon);
        $bc->set_status(backup::STATUS_AWAITING);

        $bc->execute_plan();

        $results = $bc->get_results();
        $bc->destroy();
        unset($bc);

        if ($file = $results['backup_destination']) {
            $fs = get_file_storage();

            $coursecontext = context_course::instance($df->course->id);
            $presetname = clean_filename(
                str_replace(' ', '_', $df->name).
                '-dataform-preset-'.
                gmdate("Ymd_Hi"). '-'.
                str_replace(' ', '-', get_string("preset$userdata", 'dataform')). '.mbz'
            );


            $preset = new \stdClass;
            $preset->contextid = $coursecontext->id;
            $preset->component = 'mod_dataform';
            $preset->filearea = self::PRESET_COURSEAREA;
            $preset->filepath = '/';
            $preset->filename = $presetname;

            $fs->create_file_from_storedfile($preset, $file);
            $file->delete();
            return true;
        }
        return false;
    }

    /**
     *
     */
    public function create_preset_from_upload($draftid) {
        global $USER;

        // Outside a Dataform, presets are uploaded directly to site presets.
        if (!$this->_dataformid) {
            // User must have proper permissions.
            if (!is_siteadmin()) {
                return false;
            }
            $contextid = SYSCONTEXTID;
            $filearea = self::PRESET_SITEAREA;
        } else {
            $df = mod_dataform_dataform::instance($this->_dataformid);
            $context = context_course::instance($df->course->id);
            $contextid = $context->id;
            $filearea = self::PRESET_COURSEAREA;
        }

        $usercontext = context_user::instance($USER->id);
        $fs = get_file_storage();
        if ($files = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftid, 'sortorder', false)) {
            $file = reset($files);
            $preset = new stdClass;
            $preset->contextid = $contextid;
            $preset->component = 'mod_dataform';
            $preset->filearea = $filearea;
            $preset->filepath = '/';

            $ext = pathinfo($file->get_filename(), PATHINFO_EXTENSION);
            if ($ext == 'mbz') {
                $preset->filename = $file->get_filename();
                $fs->create_file_from_storedfile($preset, $file);
            } else if ($ext == 'zip') {
                // Extract files to the draft area.
                $zipper = get_file_packer('application/zip');
                $file->extract_to_storage($zipper, $usercontext->id, 'user', 'draft', $draftid, '/');
                $file->delete();

                if ($files = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftid, 'sortorder', false)) {
                    foreach ($files as $file) {
                        $ext = pathinfo($file->get_filename(), PATHINFO_EXTENSION);
                        if ($ext == 'mbz') {
                            $preset->filename = $file->get_filename();
                            $fs->create_file_from_storedfile($preset, $file);
                        }
                    }
                }
            }
            $fs->delete_area_files($usercontext->id, 'user', 'draft', $draftid);
            return true;
        }
        return false;
    }

    /**
     *
     */
    public function apply_preset($userpreset, $torestorer = true) {
        global $DB, $CFG, $USER;

        $df = mod_dataform_dataform::instance($this->_dataformid);
        // Extract the backup file to the temp folder.
        $folder = 'tmp-'. $df->context->id. '-'. time();
        $backuptempdir = make_temp_directory("backup/$folder");
        $fs = get_file_storage();
        $file = $fs->get_file_by_id($userpreset);
        $zipper = get_file_packer($file->get_mimetype());
        $file->extract_to_pathname($zipper, $backuptempdir);

        require_once("$CFG->dirroot/backup/util/includes/restore_includes.php");

        // Required preparation due to restorer assumption that this should be a new activity
        // Anonymous users cleanup.
        $DB->delete_records_select('user', $DB->sql_like('firstname', '?'), array('%anonfirstname%'));
        // Grading area removal.
        $DB->delete_records('grading_areas', array('contextid' => $df->context->id));

        $transaction = $DB->start_delegated_transaction();
        $rc = new restore_controller(
            $folder,
            $df->course->id,
            backup::INTERACTIVE_NO,
            backup::MODE_GENERAL,
            $USER->id,
            backup::TARGET_CURRENT_ADDING
        );

        if (!$rc->execute_precheck()) {
            $precheckresults = $rc->get_precheck_results();
            if (is_array($precheckresults) && !empty($precheckresults['errors'])) {
                if (empty($CFG->keeptempdirectoriesonbackup)) {
                    fulldelete($backuptempdir);
                }
            }
        }

        // Get the dataform restore activity task.
        $tasks = $rc->get_plan()->get_tasks();
        $dataformtask = null;
        foreach ($tasks as &$task) {
            if ($task instanceof restore_dataform_activity_task) {
                $dataformtask = &$task;
                break;
            }
        }

        if ($dataformtask) {
            $dataformtask->set_activityid($df->id);
            $dataformtask->set_moduleid($df->cm->id);
            $dataformtask->set_contextid($df->context->id);

            if ($torestorer) {
                $dataformtask->set_ownerid($USER->id);
            }

            $rc->set_status(backup::STATUS_AWAITING);
            $rc->execute_plan();

            $transaction->allow_commit();
            // Rc cleanup.
            $rc->destroy();
            // Anonymous users cleanup.
            $DB->delete_records_select('user', $DB->sql_like('firstname', '?'), array('%anonfirstname%'));
            return true;
        } else {
            $rc->destroy();
        }
        return false;
    }

    /**
     *
     */
    public function download_presets($presetids) {
        global $CFG;

        if (headers_sent()) {
            throw new moodle_exception('headerssent');
        }

        if (!$pids = explode(',', $presetids)) {
            return false;
        }

        $presets = array();
        $fs = get_file_storage();

        // Try first course area
        // Must be in Dataform.
        if ($this->_dataformid) {
            $df = mod_dataform_dataform::instance($this->_dataformid);
            $coursecontext = context_course::instance($df->course->id);
            $contextid = $coursecontext->id;

            if ($files = $fs->get_area_files($contextid, 'mod_dataform', self::PRESET_COURSEAREA)) {
                foreach ($files as $file) {
                    if (empty($pids)) {
                        break;
                    }

                    if (!$file->is_directory()) {
                        $key = array_search($file->get_id(), $pids);
                        if ($key !== false) {
                            $presets[$file->get_filename()] = $file;
                            unset($pids[$key]);
                        }
                    }
                }
            }
        }

        // Try site area.
        if (!empty($pids)) {
            if ($files = $fs->get_area_files(SYSCONTEXTID, 'mod_dataform', self::PRESET_SITEAREA)) {
                foreach ($files as $file) {
                    if (empty($pids)) {
                        break;
                    }

                    if (!$file->is_directory()) {
                        $key = array_search($file->get_id(), $pids);
                        if ($key !== false) {
                            $presets[$file->get_filename()] = $file;
                            unset($pids[$key]);
                        }
                    }
                }
            }
        }

        $downloaddir = make_temp_directory('download');
        $filename = 'presets.zip';
        $downloadfile = "$downloaddir/$filename";

        $zipper = get_file_packer('application/zip');
        $zipper->archive_to_pathname($presets, $downloadfile);

        header("Content-Type: application/download\n");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Expires: 0');
        header('Cache-Control: must-revalidate,post-check=0,pre-check=0');
        header('Pragma: public');
        $downloadhandler = fopen($downloadfile, 'rb');
        print fread($downloadhandler, filesize($downloadfile));
        fclose($downloadhandler);
        unlink($downloadfile);
        exit(0);
    }

    /**
     *
     */
    public function share_presets($presetids) {
        global $CFG, $USER;

        $df = mod_dataform_dataform::instance($this->_dataformid);
        if (!has_capability('mod/dataform:presetsviewall', $df->context)) {
            return false;
        }

        $fs = get_file_storage();
        $filerecord = new \stdClass;
        $filerecord->contextid = SYSCONTEXTID;
        $filerecord->component = 'mod_dataform';
        $filerecord->filearea = self::PRESET_SITEAREA;
        $filerecord->filepath = '/';

        foreach (explode(',', $presetids) as $pid) {
            $fs->create_file_from_storedfile($filerecord, $pid);
        }
        return true;
    }

    /**
     *
     */
    public function delete_presets($presetids) {
        if (!$pids = explode(',', $presetids)) {
            return false;
        }

        $fs = get_file_storage();

        // Try first course area
        // Must be in Dataform.
        if ($this->_dataformid) {
            $df = mod_dataform_dataform::instance($this->_dataformid);
            if (!has_capability('mod/dataform:managepresets', $df->context)) {
                return false;
            }

            $coursecontext = context_course::instance($df->course->id);
            $contextid = $coursecontext->id;

            if ($files = $fs->get_area_files($contextid, 'mod_dataform', self::PRESET_COURSEAREA)) {
                foreach ($files as $file) {
                    if (empty($pids)) {
                        break;
                    }

                    if (!$file->is_directory()) {
                        $key = array_search($file->get_id(), $pids);
                        if ($key !== false) {
                            $file->delete();
                            unset($pids[$key]);
                        }
                    }
                }
            }
        }

        // Try site area.
        if (!empty($pids)) {
            if ($files = $fs->get_area_files(SYSCONTEXTID, 'mod_dataform', self::PRESET_SITEAREA)) {
                foreach ($files as $file) {
                    if (empty($pids)) {
                        break;
                    }

                    if (!$file->is_directory()) {
                        $key = array_search($file->get_id(), $pids);
                        if ($key !== false) {
                            $file->delete();
                            unset($pids[$key]);
                        }
                    }
                }
            }
        }
        return true;
    }

    /**
     *
     */
    protected function get_base_url() {
        global $PAGE;

        static $baseurl;

        if (!$baseurl) {
            $params = array();
            if ($this->_dataformid) {
                $params['d'] = $this->_dataformid;
            }
            $baseurl = new moodle_url($PAGE->url->out_omit_querystring(), $params);
        }
        return $baseurl;
    }

    /**
     *
     */
    protected function get_action_icon_apply($preset) {
        global $OUTPUT;

        // Can apply only inside a Dataform.
        if (!$this->_dataformid) {
            return null;
        }

        static $icon;

        if (!$icon) {
            $icon = new pix_icon('t/switch_whole', $this->get_strings()->apply);
        }
        $url = new moodle_url($this->get_base_url(), array('apply' => $preset->id, 'sesskey' => sesskey()));
        return $OUTPUT->action_icon($url, $icon);
    }

    /**
     *
     */
    protected function get_action_icon_share($preset, $contextid = SYSCONTEXTID) {
        global $OUTPUT;

        static $icon;

        $str = '';
        $context = context::instance_by_id($contextid);
        if (has_capability('mod/dataform:presetsviewall', $context)) {
            $icon = new pix_icon('i/group', $this->get_strings()->share);
            $url = new moodle_url($this->get_base_url(), array('share' => $preset->id, 'sesskey' => sesskey()));
            $str = $OUTPUT->action_icon($url, $icon);
        }
        return $str;
    }

    /**
     *
     */
    protected function get_action_icon_download($preset, $area) {
        global $OUTPUT;

        static $icon;

        if (!$icon) {
            $icon = new pix_icon('t/download', $this->get_strings()->download);
        }
        $url = moodle_url::make_file_url("/pluginfile.php", "/$preset->contextid/mod_dataform/$area/$preset->itemid/$preset->name");
        return $OUTPUT->action_icon($url, $icon);
    }

    /**
     *
     */
    protected function get_action_icon_delete($preset, $area = null) {
        global $OUTPUT, $PAGE;

        static $icon;

        $str = '';
        $context = $PAGE->context;
        if (has_capability('mod/dataform:managepresets', $context)) {
            if (!$icon) {
                $icon = new pix_icon('t/delete', $this->get_strings()->delete);
            }
            $params = array('delete' => $preset->id, 'sesskey' => sesskey());
            if ($area) {
                $params['area'] = $area;
            }
            $url = new moodle_url($this->get_base_url(), $params);
            $str = $OUTPUT->action_icon($url, $icon);
        }
        return $str;
    }

    /**
     *
     */
    protected function get_action_icon_multidownload() {
        global $OUTPUT, $PAGE;

        $baseurl = $this->get_base_url();
        $actionurl = new moodle_url($baseurl, array('sesskey' => sesskey()));
        $icon = new pix_icon('t/download', $this->get_strings()->multidownload);
        $PAGE->requires->js_init_call('M.mod_dataform.util.init_bulk_action', array('preset', 'download', $actionurl->out(false)));
        return $OUTPUT->action_icon($baseurl, $icon, null, array('id' => 'id_preset_bulkaction_download'));
    }

    /**
     *
     */
    protected function get_action_icon_multidelete() {
        global $OUTPUT, $PAGE;

        $baseurl = $this->get_base_url();
        $actionurl = new moodle_url($baseurl, array('sesskey' => sesskey()));
        $icon = new pix_icon('t/delete', $this->get_strings()->multidelete);
        $PAGE->requires->js_init_call('M.mod_dataform.util.init_bulk_action', array('preset', 'delete', $actionurl->out(false)));
        return $OUTPUT->action_icon($baseurl, $icon, null, array('id' => 'id_preset_bulkaction_delete'));
    }

    /**
     *
     */
    protected function get_action_icon_multishare() {
        global $OUTPUT, $PAGE;

        $baseurl = $this->get_base_url();
        $actionurl = new moodle_url($baseurl, array('sesskey' => sesskey()));
        $icon = new pix_icon('i/group', $this->get_strings()->multishare);
        $PAGE->requires->js_init_call('M.mod_dataform.util.init_bulk_action', array('preset', 'share', $actionurl->out(false)));
        return $OUTPUT->action_icon($baseurl, $icon, null, array('id' => 'id_preset_bulkaction_share'));
    }

    /**
     *
     */
    protected function get_action_icon_selectallnone() {
        global $PAGE;

        $PAGE->requires->js_init_call('M.mod_dataform.util.init_select_allnone', array('preset'));
        return html_writer::checkbox('presetselectallnone', null, false, null, array('id' => 'id_presetselectallnone'));
    }

    /**
     *
     */
    protected function get_strings() {
        static $strings;

        if (!$strings) {
            $strings = new stdClass;
            $strings->name = get_string('name');
            $strings->description = get_string('description');
            $strings->screenshot = get_string('screenshot');
            $strings->apply = get_string('presetapply', 'dataform');
            $strings->map = get_string('presetmap', 'dataform');
            $strings->download = get_string('download', 'dataform');
            $strings->delete = get_string('delete');
            $strings->share = get_string('presetshare', 'dataform');
            $strings->multishare = get_string('multishare', 'dataform');
            $strings->multidownload = get_string('multidownload', 'dataform');
            $strings->multidelete = get_string('multidelete', 'dataform');
        }
        return $strings;
    }
}
