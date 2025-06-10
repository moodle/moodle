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
 * @package    block_simple_restore
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Be sure no one accesses the page directly.
defined('MOODLE_INTERNAL') || die();

abstract class simple_restore_utils {
    // We don't need the includes on every request.
    public static function includes() {
        global $CFG;
        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
    }

    public static function backadel_shortname($shortname) {
        if (preg_match('/\s/', $shortname)) {
            $matchers = array('/\s/', '/\//');
            return preg_replace($matchers, '-', $shortname);
        }
        return $shortname;
    }

    public static function selected_backadel($data) {
        global $CFG;

        $backadelpath = get_config('block_backadel', 'path');

        $realpath = $CFG->dataroot . $backadelpath . $data->fileid;

        if (!file_exists($realpath)) {
            return true;
        }

        copy($realpath, $data->to_path);
        $data->filename = $data->fileid;
        return true;
    }

    public static function backadel_backups($search) {
        global $CFG;
        $backadelpath = get_config('block_backadel', 'path');
        if (empty($backadelpath)) {
            return array();
        }
        $backadelpath = $CFG->dataroot . $backadelpath;
        $bysearch = function ($file) use ($search) {
            return preg_match("/{$search}/i", $file);
        };
        $tobackup = function ($file) use ($backadelpath) {
            $backadel = new stdClass;
            $backadel->id = $file;
            $backadel->filename = $file;
            $backadel->filesize = filesize($backadelpath . $file);
            $backadel->timemodified = filemtime($backadelpath . $file);
            return $backadel;
        };
        $potentials = array_filter(scandir($backadelpath), $bysearch);
        return array_map($tobackup, $potentials);
    }

    public static function backadel_criterion($course) {
        global $USER;
        $crit = get_config('block_backadel', 'suffix');
        if (empty($crit)) {
            return "";
        }
        $search = $crit == 'username' ? '_' . $USER->username : $course->{$crit};
        return "{$search}[_\.]";
    }

    public static function backup_list($data) {
        global $DB, $OUTPUT;
        if (isset($data->shortname)) {
            $search = self::backadel_shortname($data->shortname);
        } else {
            $course = $DB->get_record('course', array('id' => $data->courseid));
            $search = self::backadel_criterion($course);
        }
        $list = new stdClass;
        $list->header = get_string('semester_backups', 'block_simple_restore');
        $list->backups = self::backadel_backups($search);
        $list->order = 10;
        $list->html = '';
        if (!empty($list->backups)) {
            $list->html = $OUTPUT->heading($list->header);
            $list->html .= self::build_table(
                $list->backups,
                'backadel',
                $data->courseid,
                $data->restore_to
            );
        }
        $data->lists[] = $list;

        return (
            self::course_backups($data) and
            self::user_backups($data)
        );
    }

    public static function course_backups($data) {
        if (isset($data->shortname)) {
            $courses = simple_restore_utils::filter_courses($data->shortname);
        } else {
            $courses = enrol_get_my_courses();
        }

        $to_html = function($in, $course) use ($data) {
            global $DB, $OUTPUT;

            $ctx = context_course::instance($course->id);

            $backups = $DB->get_records('files', array(
                'component' => 'backup',
                'contextid' => $ctx->id,
                'filearea' => 'course',
                'mimetype' => 'application/vnd.moodle.backup'
            ), 'timemodified DESC');

            if (empty($backups)) return $in;

            return $in . (
                $OUTPUT->heading($course->shortname) .
                simple_restore_utils::build_table(
                    $backups,
                    'course',
                    $data->courseid,
                    $data->restore_to
                )
            );
        };

        $list = new stdClass;
        $list->html = array_reduce($courses, $to_html, '');
        $list->backups = !empty($list->html);
        $list->order = 100;

        $data->lists[] = $list;

        return true;
    }

    public static function user_backups($data) {
        global $USER, $DB, $PAGE, $OUTPUT;

        $user_context = context_user::instance($USER->id);
        $context = context_course::instance($data->courseid);

        $params = array(
            'component' => 'user',
            'filearea' => 'backup',
            'contextid' => $user_context->id,
        );
        $correct_files = function($file) { return $file->filename != '.'; };
        $backup_files = $DB->get_records('files', $params);

        $params = array(
            'contextid' => $user_context->id,
            'currentcontext' => $context->id,
            'filearea' => 'backup',
            'component' => 'user',
            'returnurl' => $PAGE->url->out(false)
        );

        $str = get_string('managefiles', 'backup');
        $url = new moodle_url('/backup/backupfilesedit.php', $params);

        $list = new stdClass;
        $list->header = get_string('choosefilefromuserbackup', 'backup');
        $list->backups = array_filter($backup_files, $correct_files);
        $list->order = 200;

        $list->html = (
            $OUTPUT->heading($list->header) .
            $OUTPUT->single_button($url, $str, 'post', array('class' => 'center padded'))
        );

        if ($list->backups) {
            $list->html .= simple_restore_utils::build_table(
                $list->backups,
                'user',
                $data->courseid,
                $data->restore_to
            );
        }

        $data->lists[] = $list;

        return true;
    }


    public static function permission($cap, $context) {
        return has_capability("block/simple_restore:{$cap}", $context);
    }

    public static function _s($name, $a=null) {
        return get_string($name, 'block_simple_restore', $a);
    }

    public static function build_table($backups, $name, $courseid, $restoreto) {
        $table = new html_table();
        $table->head = array(
            get_string('name'),
            get_string('size'),
            get_string('modified')
        );

        $torow = function($backup) use ($name, $courseid, $restoreto) {
            $link = html_writer::link(
                new moodle_url('/blocks/simple_restore/list.php', array(
                    'id' => $courseid,
                    'name' => $name,
                    'action' => 'choosefile',
                    'restore_to' => $restoreto,
                    'fileid' => $backup->id
                )), $backup->filename);
                $name = new html_table_cell($link);
                $size = new html_table_cell(display_size($backup->filesize));
                $modified = new html_table_cell(date('d M Y, h:i:s A',
                                            $backup->timemodified));
                return new html_table_row(array($name, $size, $modified));
        };
        $table->data = array_map($torow, $backups);
        return html_writer::table($table);
    }

    public static function filter_courses($shortname) {
        global $DB;
        $safeshortname = addslashes($shortname);
        $select = "shortname LIKE '%{$safeshortname}%'";
        return $DB->get_records_select('course', $select);
    }

    public static function heading($restoreto) {
        switch($restoreto){
            case 0:
                return self::_s('delete_restore');
            case 1:
                return self::_s('restore_course');
            case 2:
                return self::_s('restore_course_archive');
        }
    }

    public static function prep_restore($fileid, $name, $courseid) {
        global $USER, $CFG;

        // Get the includes.
        self::includes();

        if (empty($fileid) || empty($courseid)) {
            throw new Exception(self::_s('no_arguments'));
        }

        $filename = restore_controller::get_tempdir_name($courseid, $USER->id);
        $tempdir = isset($CFG->backuptempdir) ? $CFG->backuptempdir : $CFG->tempdir;
        $tempdir = substr($tempdir, -1) === '/' ? $tempdir : $tempdir . '/';
        $pathname = $tempdir . $filename;

        $data = new stdClass;
        $data->userid = $USER->id;
        $data->courseid = $courseid;
        $data->fileid = $fileid;
        $data->to_path = $pathname;
        $data->filename = $filename;

        self::selected_backadel($data);

        simple_restore_selected_user::selected_user($data);

        if (empty($data->filename)) {
            throw new Exception(self::_s('no_file'));
        }

        return $filename;
    }

    /**
     * Simple Restore post restore fixes
     * NOT DONE.
     * REQUIRES CPS AND UES TO FUNCTION.
     * DO NOT CALL!!!
     *
     * @param  $data
     * @param  int  other['userid']
     * @param  int  other['restore_to'] 0,1,2
     * @param  int  other['courseid']
     */
    public static function simple_restore_complete($data) {
        try {
            global $DB, $CFG, $USER;
            require_once($CFG->dirroot . '/blocks/cps/classes/lib.php');

            $sectionid = $data->other['ues_section_id'];
            $restoreto = $data->other['restore_to'];
            $oldcourse = get_course($data->other['courseid']);

            $skip = array(
                'id', 'category', 'sortorder',
                'sectioncache', 'modinfo', 'newsitems'
            );

            $course = $DB->get_record('course', array('id' => $oldcourse->id));

            $resetgrades = cps_setting::get(array(
                'name' => 'user_grade_restore',
                'userid' => $USER->id
            ));

            // Defaults to reset grade items.
            if (empty($resetgrades)) {
                $resetgrades = new stdClass;
                $resetgrades->value = 1;
            }

            // Maintain the correct config.
            foreach (get_object_vars($oldcourse) as $key => $value) {
                if (in_array($key, $skip)) {
                    continue;
                }

                $course->$key = $value;
            }

            $DB->update_record('course', $course);

            if ($resetgrades->value == 1) {
                require_once($CFG->libdir . '/gradelib.php');

                $items = grade_item::fetch_all(array('courseid' => $course->id));
                foreach ($items as $item) {
                    $item->plusfactor = 0.00000;
                    $item->multfactor = 1.00000;
                    $item->update();
                }

                grade_regrade_final_grades($course->id);
            }

            // This is an import, ignore.
            if ($restoreto == 1) {
                return true;
            }

            $keepenrollments = (bool) get_config('simple_restore', 'keep_roles_and_enrolments');
            $keepgroups = (bool) get_config('simple_restore', 'keep_groups_and_groupings');

            // No need to re-enroll.
            if ($keepgroups and $keepenrollments) {
                $enrolinstances = $DB->get_records('enrol', array(
                    'courseid' => $oldcourse->id,
                    'enrol' => 'ues'
                ));

                // Cleanup old instances.
                $ues = enrol_get_plugin('ues');

                foreach (array_slice($enrolinstances, 1) as $instance) {
                    $ues->delete_instance($instance);
                }

            } else {
                $sections = ues_section::from_course($course);

                // Nothing to do.
                if (empty($sections)) {
                    return true;
                }

                // Rebuild enrollment.
                ues::enrollUsers(ues_section::from_course($course));
            }

            return true;

        } catch (Exception $e) {
            return false;
        }
    }
}

class archive_restore_utils extends simple_restore_utils {
    /**
     * Get course name and category from filename.
     *
     * NB: this function expects files from backadel whose names
     * begin as 'backadel-', for example:
     * backup-moodle2-course-2-2014_spring_tst2_2011_for_instructor_four-20140407-1539.mbz
     *
     * @param string $filename
     */
    public static function coursedata_from_filename($filename) {
        $prefix = 'backadel';
        if (substr($filename, 0, strlen($prefix)) == $prefix) {
            $filename = substr($filename, strlen($prefix) + 1);
        } else {
            // TODO - do something better than throw an error if it isn't a backadel file.
            // Consider restricting the choice of files in the first place!
            throw new exception("Archive Restore does not support filenames other than 'backadel-*'");
        }

        $chunks     = explode('_', $filename);
        $meta       = $chunks[0];
        $metachunks = explode('-', $meta);
        $fullname   = implode(' ', $metachunks);
        $category   = $metachunks[2];

        return array($fullname, $category);
    }
}

class simple_restore {
    public $userid;
    public $course;
    public $filename;
    public $restoreto;

    public function __construct($course, $filename, $restoreto = 0) {
        if (empty($course)) {
            throw new Exception(simple_restore_utils::_s('no_context'));
        }
        if (empty($filename)) {
            throw new Exception(simple_restore_utils::_s('no_file'));
        }

        global $USER;

        $this->userid = $USER->id;
        $this->course = $course;
        $this->context = context_course::instance($course->id);
        $this->filename = $filename;
        $this->restore_to = $restoreto;
    }

    private function process_confirm() {
        $restore = restore_ui::engage_independent_stage(
            restore_ui::STAGE_CONFIRM, $this->context->id
        );
        $restore->process();
        return $restore;
    }

    private function process_destination($restore) {
        $_POST['sesskey']   = sesskey();
        $_POST['filepath']  = $this->rip_value($restore, 'filepath');
        $_POST['target']    = $this->restore_to;
        $_POST['targetid']  = $this->course->id;

        $rtn = restore_ui::engage_independent_stage(
            restore_ui::STAGE_DESTINATION, $this->context->id
        );
        $rtn->process();
        return $rtn;
    }

    private function process_schema($rc) {
        // File dependencies.
        $filedependencies = array(
            'block' => 1, 'comments' => 1, 'filters' => 1
        );

        $_POST['stage'] = restore_ui::STAGE_SCHEMA;
        $restore = new restore_ui($rc, array('contextid' => $this->context->id));

        // Forge posts.
        $_POST['restore'] = $restore->get_restoreid();

        // Get all tasks from the UI object through reflection.
        $tasks = $this->rip_ui($restore)->get_tasks();
        foreach ($tasks as $task) {
            $settings = $task->get_settings();
            foreach ($settings as $setting) {
                $settingname = $setting->get_name();

                if (preg_match('/(.+)_(\d+)_(.+)/', $settingname, $matches)) {
                    $module = $matches[1];
                    $type = $matches[3];
                    $adminsettingkey = $module.'_'.$type;
                } else {
                    $adminsettingkey = $settingname;
                }
                $adminsetting = get_config('simple_restore', $adminsettingkey);
                if (!is_numeric($adminsetting)) {
                    continue;
                }

                if ($adminsetting and isset($filedependencies[$settingname])) {
                    $basepath = $task->get_taskbasepath();
                    if (!file_exists("$basepath/$settingname.xml")) {
                        continue;
                    }
                }
                // Set admin value.
                // Some settings may be locked by permission.
                if ($setting->get_status() == base_setting::NOT_LOCKED) {
                    $setting->set_value($adminsetting);
                }
            }
        }

        $restore->process();
        $restore->save_controller();
        return $restore;
    }

    private function rip_value($restore, $property) {
        $reflector = new ReflectionObject($restore);
        $prop = $reflector->getProperty($property);
        $prop->setAccessible(true);
        return $prop->getValue($restore);
    }

    private function rip_stage($restore) {
        return $this->rip_value($restore, 'stage');
    }

    private function rip_ui($restore) {
        return $this->rip_value($this->rip_stage($restore), 'ui');
    }

    private function process_final($restore) {
        $_POST['stage'] = restore_ui::STAGE_PROCESS;
        $rc = restore_ui::load_controller($restore->get_restoreid());
        $final = new restore_ui($rc, array('contextid' => $this->context->id));
        $final->process();
        $final->execute();
        $final->destroy();
        unset($final);
    }

    public function execute() {
        global $PAGE;

        simple_restore_utils::includes();

        $useasync = (bool)get_config('simple_restore', 'async_toggle');

        // if (isset($useasync) && $useasync == "1") {
        if ($useasync) {
            // Prepare a progress bar which can display optionally during long-running
            // operations while setting up the UI.
            $slowprogress = new \core\progress\display_if_slow(get_string('preparingui', 'backup'));

            // Overall, allow 10 units of progress.
            $slowprogress->start_progress('', 10);

            // This progress section counts for loading the restore controller.
            $slowprogress->start_progress('', 1, 1);

            $backupmode = backup::MODE_ASYNC;
            // Prefer to use bool.
            $useasync = true;
        } else {
            $backupmode = backup::MODE_GENERAL;
            // Prefer to use bool.
            $useasync = false;
        }

        // Archive mode.
        if ($this->restore_to == 2 && get_config('simple_restore', 'is_archive_server')) {
            return $this->archive_mode_execute();
        }

        // Confirmed ... process destination.
        $confirmed = $this->process_destination($this->process_confirm());

        // Setting up controller ... tmp tables.
        $rc = new restore_controller(
            $confirmed->get_filepath(),
            $confirmed->get_course_id(),
            backup::INTERACTIVE_YES,
            $backupmode,
            $this->userid,
            $confirmed->get_target()
        );

        if ($rc->get_status() == backup::STATUS_REQUIRE_CONV) {
            $rc->convert();
        }

        if ($useasync) {

            // Get the renderer so we can use the backup status template.
            $renderer = $PAGE->get_renderer('core','backup');

            $restore = new restore_ui($rc, array('contextid'=>$this->context->id));
            $restore->set_progress_reporter($slowprogress);

            if (!$restore->is_independent()) {
                // Use a temporary (disappearing) progress bar to show the precheck progress if any.
                $precheckprogress = new \core\progress\display_if_slow(get_string('preparingdata', 'backup'));
                $restore->get_controller()->set_progress($precheckprogress);

                if ($rc->get_status() == backup::STATUS_SETTING_UI) {
                    $rc->finish_ui();
                }
                if ($rc->get_status() == backup::STATUS_NEED_PRECHECK) {
                    if (!$rc->precheck_executed()) {
                        $rc->execute_precheck(true);
                    }
                    $precheckresults = $rc->get_precheck_results();
                    if (!empty($results)) {
                        echo $renderer->precheck_notices($precheckresults);
                        echo $OUTPUT->continue_button(new moodle_url('/course/view.php', array('id' => $this->course->id)));
                        echo $OUTPUT->footer();
                        die();
                    }
                }
                $restore->save_controller();
            }

            echo $renderer->progress_bar($restore->get_progress_bar());

            // Asynchronous restore.
            // Create adhoc task for restore.
            $restoreid = $restore->get_restoreid();
            $asynctask = new \core\task\asynchronous_restore_task();
            $asynctask->set_blocking(false);
            $asynctask->set_userid($this->userid);
            $asynctask->set_custom_data(array('backupid' => $restoreid));
            \core\task\manager::queue_adhoc_task($asynctask);

            // Add ajax progress bar and initiate ajax via a template.
            $restoreurl = new moodle_url('/backup/restorefile.php', array('contextid' => $this->context->id));
            $courseurl = course_get_url($this->course->id);
            $progresssetup = array(
                    'backupid' => $restoreid,
                    'contextid' => $this->context->id,
                    'courseurl' => $courseurl,
                    'restoreurl' => $restoreurl->out()
            );
            echo $renderer->render_from_template('core/async_backup_status', $progresssetup);

            $restore->destroy();
            unset($restore);
        } else {
            // The old way.
            $this->process_final($this->process_schema($rc));
        }

        // Probably good to do this.
        unset($confirmed);

        // Restore blocks.
        if ($this->restore_to == 0) {
            blocks_delete_all_for_context($this->context->id);
            blocks_add_default_course_blocks($this->course);
        }

        // It's important to pass the previous course's config.
        $coursesettings = array(
            'restore_to' => $this->restore_to,
            'course' => $this->course
        );

        return true;
    }

    /**
     * Create a new course from the selected backup file.
     *
     * This method is inspired by @see core_course_external::duplicate_course.
     * found in /course/externallib.php.
     *
     * @global type $CFG
     * @global type $DB
     * @global type $USER
     * @return boolean
     * @throws moodle_exception
     */
    public function archive_mode_execute() {
        global $CFG, $DB, $USER;
        require_once($CFG->dirroot.'/enrol/manual/lib.php');
        simple_restore_utils::includes();

        // Enrol the current user as teacher.
        $plugin       = new enrol_manual_plugin();
        $plugin->add_instance($this->course);

        $instances    = enrol_get_instances($this->course->id, true);
        $isntance     = null;
        foreach ($instances as $enrolinstance) {
            if ($enrolinstance->enrol == 'manual') {
                $instance = $enrolinstance;
                break;
            }
        }

        $roleid = $DB->get_field('role', 'id', array('shortname' => 'editingteacher'));
        $plugin->enrol_user($instance, $USER->id, $roleid);

        // Setup tempdir for the restore process.
        $tempdir = isset($CFG->backuptempdir) ? $CFG->backuptempdir : $CFG->tempdir;
        $tempdir = substr($tempdir, -1) === '/' ? $tempdir : $tempdir . '/';
        $extractname = restore_controller::get_tempdir_name($this->course->id, $USER->id);
        $extractpath = $tempdir . $extractname;
        $filepath    = $tempdir . $this->filename;

        if (!has_capability('moodle/restore:userinfo', $this->context, $USER->id)) {
            // Delete, abort, etc.
            echo "deleting temporary course files and materials.";
            fulldelete($filepath);
            delete_course($this->course);

            // Update course count in catagories.
            fix_course_sortorder();

            // In order to restore Archived courses,
            // this role must be granted the capability moodle/restore:userinfo - Ask your administrator.
            throw new restore_controller_exception("no userinfo cap");
        }

        // Zip file needs to be unzipped.
        if (!file_exists($filepath. "/moodle_backup.xml")) {
            $fb = get_file_packer('application/vnd.moodle.backup');
            $fb->extract_to_pathname("$tempdir" . $this->filename, $extractpath);
        }

        $rc = new restore_controller($extractname, $this->course->id,
                backup::INTERACTIVE_NO, backup::MODE_GENERAL, $USER->id, backup::TARGET_NEW_COURSE);

        // Iterate through our settings and make sure they are reflected in the restore plan.
        $configsettings = array_values($this->get_settings());

        foreach ($configsettings as $config) {
            if ($rc->get_plan()->setting_exists($config->name)) {
                $setting = $rc->get_plan()->get_setting($config->name);
                if ($setting->get_status() == backup_setting::NOT_LOCKED) {
                    $setting->set_value($config->value);
                }
            }
        }

        // Setup restore process and ensure there are no errors.
        if (!$rc->execute_precheck()) {
            $precheckresults = $rc->get_precheck_results();
            if (is_array($precheckresults) && !empty($precheckresults['errors'])) {
                if (empty($CFG->keeptempdirectoriesonbackup)) {
                    fulldelete($filepath);
                }

                $errorinfo = '';

                foreach ($precheckresults['errors'] as $error) {
                    $errorinfo .= $error;
                }

                if (array_key_exists('warnings', $precheckresults)) {
                    foreach ($precheckresults['warnings'] as $warning) {
                        $errorinfo .= $warning;
                    }
                }
                throw new moodle_exception('backupprecheckerrors', 'webservice', '', $errorinfo);
            }
        }

        // Get the correct course name - prevents dupe names.
        list($this->course->fullname, $this->course->shortname) =
                restore_dbops::calculate_course_names(
                        $this->course->id,
                        $this->course->fullname,
                        $this->course->shortname
                        );

        $rc->execute_plan();
        $rc->destroy();

        // Set shortname and fullname back, ensure visibility.
        $this->course->visible = 1;
        $DB->update_record('course', $this->course);

        // Clean up after ourselves.
        if (empty($CFG->keeptempdirectoriesonbackup)) {
            fulldelete($filepath);
        }

        return true;
    }

    private function get_settings() {
        global $DB;
        $settings = $DB->get_records('config_plugins', array('plugin' => 'simple_restore'), null, 'id,name,value');
        return $settings;
    }
}

class simple_restore_selected_user {
    public static function selected_user($data) {
        return self::selected($data);
    }

    private static function selected($data) {
        global $DB, $CFG;
        $backup = $DB->get_record('files', array('id' => $data->fileid));
        if (empty($backup)) {
            return true;
        }
        $fs = get_file_storage();
        $browser = get_file_browser();
        $filecontext = context::instance_by_id($backup->contextid);
        $storedfile = $fs->get_file(
            $filecontext->id,
            $backup->component,
            $backup->filearea,
            $backup->itemid,
            $backup->filepath,
            $backup->filename
        );
        $fileinfo = new file_info_stored(
            $browser,
            $filecontext,
            $storedfile,
            $CFG->wwwroot.'/pluginfile.php',
            '',
            false,
            simple_restore_utils::permission(
                'canrestore',
                context_course::instance($data->courseid)
            ),
            false,
            true
        );
        $fileinfo->copy_to_pathname($data->to_path);
        $data->filename = $backup->filename;
        return true;
    }
}
