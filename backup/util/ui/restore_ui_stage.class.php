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
 * restore user interface stages
 *
 * This file contains the classes required to manage the stages that make up the
 * restore user interface.
 * These will be primarily operated a {@see restore_ui} instance.
 *
 * @package   moodlecore
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Abstract stage class
 *
 * This class should be extended by all restore stages (a requirement of many restore ui functions).
 * Each stage must then define two abstract methods
 *  - process : To process the stage
 *  - initialise_stage_form : To get a restore_moodleform instance for the stage
 *
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class restore_ui_stage extends base_ui_stage {
    /**
     * Constructor
     * @param restore_ui $ui
     */
    public function __construct(restore_ui $ui, array $params=null) {
        $this->ui = $ui;
        $this->params = $params;
    }
    /**
     * The restore id from the restore controller
     * @return string
     */
    final public function get_restoreid() {
        return $this->get_uniqueid();
    }
    /**
     * This is an independent stage
     * @return int
     */
    final public function is_independent() {
        return false;
    }

    /**
     * No sub stages for this stage
     * @return false
     */
    public function has_sub_stages() {
        return false;
    }

    /**
     * The name of this stage
     * @return string
     */
    final public function get_name() {
        return get_string('restorestage'.$this->stage,'backup');
    }
    /**
     * Returns true if this is the settings stage
     * @return bool
     */
    final public function is_first_stage() {
        return $this->stage == restore_ui::STAGE_SETTINGS;
    }
}

/**
 * Abstract class used to represent a restore stage that is indenependent.
 *
 * An independent stage is a judged to be so because it doesn't require, and has
 * no use for the restore controller.
 */
abstract class restore_ui_independent_stage {
    abstract public function __construct($contextid);
    abstract public function process();
    abstract public function display(core_backup_renderer $renderer);
    abstract public function get_stage();
    /**
     * Gets an array of progress bar items that can be displayed through the restore renderer.
     * @return array Array of items for the progress bar
     */
    public function get_progress_bar() {
        global $PAGE;
        $stage = restore_ui::STAGE_COMPLETE;
        $currentstage = $this->get_stage();
        $items = array();
        while ($stage > 0) {
            $classes = array('backup_stage');
            if (floor($stage/2) == $currentstage) {
                $classes[] = 'backup_stage_next';
            } else if ($stage == $currentstage) {
                $classes[] = 'backup_stage_current';
            } else if ($stage < $currentstage) {
                $classes[] = 'backup_stage_complete';
            }
            $item = array('text' => strlen(decbin($stage)).'. '.get_string('restorestage'.$stage, 'backup'),'class' => join(' ', $classes));
            if ($stage < $currentstage && $currentstage < restore_ui::STAGE_COMPLETE) {
                //$item['link'] = new moodle_url($PAGE->url, array('restore'=>$this->get_restoreid(), 'stage'=>$stage));
            }
            array_unshift($items, $item);
            $stage = floor($stage/2);
        }
        return $items;
    }
    abstract public function get_stage_name();
    /**
     * Obviously true
     * @return true
     */
    final public function is_independent() {
        return true;
    }
    public function destroy() {
        // Nothing to destroy here!
    }
}

/**
 * The confirmation stage.
 *
 * This is the first stage, it is independent.
 */
class restore_ui_stage_confirm extends restore_ui_independent_stage {
    protected $contextid;
    protected $filename = null;
    protected $filepath = null;
    protected $details;
    public function __construct($contextid) {
        $this->contextid = $contextid;
        $this->filename = required_param('filename', PARAM_FILE);
    }
    public function process() {
        global $CFG;
        if (!file_exists("$CFG->tempdir/backup/".$this->filename)) {
            throw new restore_ui_exception('invalidrestorefile');
        }
        $outcome = $this->extract_file_to_dir();
        if ($outcome) {
            fulldelete($this->filename);
        }
        return $outcome;
    }
    protected function extract_file_to_dir() {
        global $CFG, $USER;

        $this->filepath = restore_controller::get_tempdir_name($this->contextid, $USER->id);

        $fb = get_file_packer();
        return ($fb->extract_to_pathname("$CFG->tempdir/backup/".$this->filename, "$CFG->tempdir/backup/$this->filepath/"));
    }

    /**
     * Renders the confirmation stage screen
     *
     * @param core_backup_renderer $renderer renderer instance to use
     * @return string HTML code
     */
    public function display(core_backup_renderer $renderer) {

        $prevstageurl = new moodle_url('/backup/restorefile.php', array('contextid' => $this->contextid));
        $nextstageurl = new moodle_url('/backup/restore.php', array(
            'contextid' => $this->contextid,
            'filepath'  => $this->filepath,
            'stage'     => restore_ui::STAGE_DESTINATION));

        $format = backup_general_helper::detect_backup_format($this->filepath);

        if ($format === backup::FORMAT_UNKNOWN) {
            // unknown format - we can't do anything here
            return $renderer->backup_details_unknown($prevstageurl);

        } else if ($format !== backup::FORMAT_MOODLE) {
            // non-standard format to be converted
            $details = array('format' => $format, 'type' => backup::TYPE_1COURSE); // todo type to be returned by a converter
            return $renderer->backup_details_nonstandard($nextstageurl, $details);

        } else {
            // standard MBZ backup, let us get information from it and display
            $this->details = backup_general_helper::get_backup_information($this->filepath);
            return $renderer->backup_details($this->details, $nextstageurl);
        }
    }

    public function get_stage_name() {
        return get_string('restorestage'.restore_ui::STAGE_CONFIRM, 'backup');
    }
    public function get_stage() {
        return restore_ui::STAGE_CONFIRM;
    }
}

/**
 * This is the destination stage.
 *
 * This stage is the second stage and is also independent
 */
class restore_ui_stage_destination extends restore_ui_independent_stage {
    protected $contextid;
    protected $filepath = null;
    protected $courseid = null;
    protected $target = backup::TARGET_NEW_COURSE;
    protected $coursesearch = null;
    protected $categorysearch = null;
    public function __construct($contextid) {
        global $PAGE;
        $this->contextid = $contextid;
        $this->filepath = required_param('filepath', PARAM_ALPHANUM);
        $url = new moodle_url($PAGE->url, array(
            'filepath'=>$this->filepath,
            'contextid'=>$this->contextid,
            'stage'=>restore_ui::STAGE_DESTINATION));
        $this->coursesearch = new restore_course_search(array('url'=>$url), context::instance_by_id($contextid)->instanceid);
        $this->categorysearch = new restore_category_search(array('url'=>$url));
    }
    public function process() {
        global $CFG, $DB;
        if (!file_exists("$CFG->tempdir/backup/".$this->filepath) || !is_dir("$CFG->tempdir/backup/".$this->filepath)) {
            throw new restore_ui_exception('invalidrestorepath');
        }
        if (optional_param('searchcourses', false, PARAM_BOOL)) {
            return false;
        }
        $this->target = optional_param('target', backup::TARGET_NEW_COURSE, PARAM_INT);
        $targetid = optional_param('targetid', null, PARAM_INT);
        if (!is_null($this->target) && !is_null($targetid) && confirm_sesskey()) {
            if ($this->target == backup::TARGET_NEW_COURSE) {
                list($fullname, $shortname) = restore_dbops::calculate_course_names(0, get_string('restoringcourse', 'backup'), get_string('restoringcourseshortname', 'backup'));
                $this->courseid = restore_dbops::create_new_course($fullname, $shortname, $targetid);
            } else {
                $this->courseid = $targetid;
            }
            return ($DB->record_exists('course', array('id'=>$this->courseid)));
        }
        return false;
    }

    /**
     * Renders the destination stage screen
     *
     * @param core_backup_renderer $renderer renderer instance to use
     * @return string HTML code
     */
    public function display(core_backup_renderer $renderer) {

        $format = backup_general_helper::detect_backup_format($this->filepath);

        if ($format === backup::FORMAT_MOODLE) {
            // standard Moodle 2 format, let use get the type of the backup
            $details = backup_general_helper::get_backup_information($this->filepath);
            if ($details->type === backup::TYPE_1COURSE) {
                $wholecourse = true;
            } else {
                $wholecourse = false;
            }

        } else {
            // non-standard format to be converted. We assume it contains the
            // whole course for now. However, in the future there might be a callback
            // to the installed converters
            $wholecourse = true;
        }

        $nextstageurl = new moodle_url('/backup/restore.php', array(
            'contextid' => $this->contextid,
            'filepath'  => $this->filepath,
            'stage'     => restore_ui::STAGE_SETTINGS));
        $context = context::instance_by_id($this->contextid);

        if ($context->contextlevel == CONTEXT_COURSE and has_capability('moodle/restore:restorecourse', $context)) {
            $currentcourse = $context->instanceid;
        } else {
            $currentcourse = false;
        }

        return $renderer->course_selector($nextstageurl, $wholecourse, $this->categorysearch, $this->coursesearch, $currentcourse);
    }

    public function get_stage_name() {
        return get_string('restorestage'.restore_ui::STAGE_DESTINATION, 'backup');
    }
    public function get_filepath() {
        return $this->filepath;
    }
    public function get_course_id() {
        return $this->courseid;
    }
    public function get_stage() {
        return restore_ui::STAGE_DESTINATION;
    }
    public function get_target() {
        return $this->target;
    }
}
/**
 * This stage is the settings stage.
 *
 * This stage is the third stage, it is dependent on a restore controller and
 * is the first stage as such.
 */
class restore_ui_stage_settings extends restore_ui_stage {
    /**
     * Initial restore stage constructor
     * @param restore_ui $ui
     */
    public function __construct(restore_ui $ui, array $params=null) {
        $this->stage = restore_ui::STAGE_SETTINGS;
        parent::__construct($ui, $params);
    }

    public function process(base_moodleform $form=null) {
        $form = $this->initialise_stage_form();

        if ($form->is_cancelled()) {
            $this->ui->cancel_process();
        }

        $data = $form->get_data();
        if ($data && confirm_sesskey()) {
            $tasks = $this->ui->get_tasks();
            $changes = 0;
            foreach ($tasks as &$task) {
                // We are only interesting in the backup root task for this stage
                if ($task instanceof restore_root_task || $task instanceof restore_course_task) {
                    // Get all settings into a var so we can iterate by reference
                    $settings = $task->get_settings();
                    foreach ($settings as &$setting) {
                        $name = $setting->get_ui_name();
                        if (isset($data->$name) &&  $data->$name != $setting->get_value()) {
                            $setting->set_value($data->$name);
                            $changes++;
                        } else if (!isset($data->$name) && $setting->get_ui_type() == backup_setting::UI_HTML_CHECKBOX && $setting->get_value()) {
                            $setting->set_value(0);
                            $changes++;
                        }
                    }
                }
            }
            // Return the number of changes the user made
            return $changes;
        } else {
            return false;
        }
    }

    protected function initialise_stage_form() {
        global $PAGE;
        if ($this->stageform === null) {
            $form = new restore_settings_form($this, $PAGE->url);
            // Store as a variable so we can iterate by reference
            $tasks = $this->ui->get_tasks();
            $headingprinted = false;
            // Iterate all tasks by reference
            foreach ($tasks as &$task) {
                // For the initial stage we are only interested in the root settings
                if ($task instanceof restore_root_task) {
                    if (!$headingprinted) {
                        $form->add_heading('rootsettings', get_string('restorerootsettings', 'backup'));
                        $headingprinted = true;
                    }
                    $settings = $task->get_settings();
                    // First add all settings except the filename setting
                    foreach ($settings as &$setting) {
                        if ($setting->get_name() == 'filename') {
                            continue;
                        }
                        $form->add_setting($setting, $task);
                    }
                    // Then add all dependencies
                    foreach ($settings as &$setting) {
                        if ($setting->get_name() == 'filename') {
                            continue;
                        }
                        $form->add_dependencies($setting);
                    }
                }
            }
            $this->stageform = $form;
        }
        // Return the form
        return $this->stageform;
    }
}

/**
 * Schema stage of backup process
 *
 * During the schema stage the user is required to set the settings that relate
 * to the area that they are backing up as well as its children.
 *
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_ui_stage_schema extends restore_ui_stage {
    /**
     * Schema stage constructor
     * @param backup_moodleform $ui
     */
    public function __construct(restore_ui $ui, array $params=null) {
        $this->stage = restore_ui::STAGE_SCHEMA;
        parent::__construct($ui, $params);
    }
    /**
     * Processes the schema stage
     *
     * @param backup_moodleform|null $form
     * @return int The number of changes the user made
     */
    public function process(base_moodleform $form = null) {
        $form = $this->initialise_stage_form();
        // Check it wasn't cancelled
        if ($form->is_cancelled()) {
            $this->ui->cancel_process();
        }

        // Check it has been submit
        $data = $form->get_data();
        if ($data && confirm_sesskey()) {
            // Get the tasks into a var so we can iterate by reference
            $tasks = $this->ui->get_tasks();
            $changes = 0;
            // Iterate all tasks by reference
            foreach ($tasks as &$task) {
                // We are only interested in schema settings
                if (!($task instanceof restore_root_task)) {
                    // Store as a variable so we can iterate by reference
                    $settings = $task->get_settings();
                    // Iterate by reference
                    foreach ($settings as &$setting) {
                        $name = $setting->get_ui_name();
                        if (isset($data->$name) &&  $data->$name != $setting->get_value()) {
                            $setting->set_value($data->$name);
                            $changes++;
                        } else if (!isset($data->$name) && $setting->get_ui_type() == backup_setting::UI_HTML_CHECKBOX && $setting->get_value()) {
                            $setting->set_value(0);
                            $changes++;
                        }
                    }
                }
            }
            // Return the number of changes the user made
            return $changes;
        } else {
            return false;
        }
    }
    /**
     * Creates the backup_schema_form instance for this stage
     *
     * @return backup_schema_form
     */
    protected function initialise_stage_form() {
        global $PAGE;
        if ($this->stageform === null) {
            $form = new restore_schema_form($this, $PAGE->url);
            $tasks = $this->ui->get_tasks();
            $courseheading = false;

            $allsettings = array();
            foreach ($tasks as $task) {
                if (!($task instanceof restore_root_task)) {
                    if (!$courseheading) {
                        // If we havn't already display a course heading to group nicely
                        $form->add_heading('coursesettings', get_string('coursesettings', 'backup'));
                        $courseheading = true;
                    }
                    // Put each setting into an array of settings to add. Adding
                    // a setting individually is a very slow operation, so we add
                    // them all in a batch later on.
                    foreach ($task->get_settings() as $setting) {
                        $allsettings[] = array($setting, $task);
                    }
                } else if ($this->ui->enforce_changed_dependencies()) {
                    // Only show these settings if dependencies changed them.
                    // Add a root settings heading to group nicely
                    $form->add_heading('rootsettings', get_string('rootsettings', 'backup'));
                    // Iterate all settings and add them to the form as a fixed
                    // setting. We only want schema settings to be editable
                    foreach ($task->get_settings() as $setting) {
                        if ($setting->get_name() != 'filename') {
                            $form->add_fixed_setting($setting, $task);
                        }
                    }
                }
            }

            // Actually add all the settings that we put in the array.
            $form->add_settings($allsettings);

            // Add the dependencies for all the settings.
            foreach ($allsettings as $settingtask) {
                $form->add_dependencies($settingtask[0]);
            }

            $this->stageform = $form;
        }
        return $this->stageform;
    }
}

/**
 * Confirmation stage
 *
 * On this stage the user reviews the setting for the backup and can change the filename
 * of the file that will be generated.
 *
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_ui_stage_review extends restore_ui_stage {
    /**
     * Constructs the stage
     * @param backup_ui $ui
     */
    public function __construct($ui, array $params=null) {
        $this->stage = restore_ui::STAGE_REVIEW;
        parent::__construct($ui, $params);
    }
    /**
     * Processes the confirmation stage
     *
     * @param backup_moodleform $form
     * @return int The number of changes the user made
     */
    public function process(base_moodleform $form = null) {
        $form = $this->initialise_stage_form();
        // Check it hasn't been cancelled
        if ($form->is_cancelled()) {
            $this->ui->cancel_process();
        }

        $data = $form->get_data();
        if ($data && confirm_sesskey()) {
            return 0;
        } else {
            return false;
        }
    }
    /**
     * Creates the backup_confirmation_form instance this stage requires
     *
     * @return backup_confirmation_form
     */
    protected function initialise_stage_form() {
        global $PAGE;
        if ($this->stageform === null) {
            // Get the form
            $form = new restore_review_form($this, $PAGE->url);
            $content = '';
            $courseheading = false;

            foreach ($this->ui->get_tasks() as $task) {
                if ($task instanceof restore_root_task) {
                    // If its a backup root add a root settings heading to group nicely
                    $form->add_heading('rootsettings', get_string('rootsettings', 'backup'));
                } else if (!$courseheading) {
                    // we havn't already add a course heading
                    $form->add_heading('coursesettings', get_string('coursesettings', 'backup'));
                    $courseheading = true;
                }
                // Iterate all settings, doesnt need to happen by reference
                foreach ($task->get_settings() as $setting) {
                    $form->add_fixed_setting($setting, $task);
                }
            }
            $this->stageform = $form;
        }
        return $this->stageform;
    }
}

/**
 * Final stage of backup
 *
 * This stage is special in that it is does not make use of a form. The reason for
 * this is the order of procession of backup at this stage.
 * The processesion is:
 * 1. The final stage will be intialise.
 * 2. The confirmation stage will be processed.
 * 3. The backup will be executed
 * 4. The complete stage will be loaded by execution
 * 5. The complete stage will be displayed
 *
 * This highlights that we neither need a form nor a display method for this stage
 * we simply need to process.
 *
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_ui_stage_process extends restore_ui_stage {

    const SUBSTAGE_NONE = 0;
    const SUBSTAGE_PRECHECKS = 2;

    protected $substage = 0;

    /**
     * Constructs the final stage
     * @param backup_ui $ui
     */
    public function __construct(base_ui $ui, array $params=null) {
        $this->stage = restore_ui::STAGE_PROCESS;
        parent::__construct($ui, $params);
    }
    /**
     * Processes the final stage.
     *
     * In this case it checks to see if there is a sub stage that we need to display
     * before execution, if there is we gear up to display the subpage, otherwise
     * we return true which will lead to execution of the restore and the loading
     * of the completed stage.
     */
    public function process(base_moodleform $form=null) {
        if (optional_param('cancel', false, PARAM_BOOL)) {
            redirect(new moodle_url('/course/view.php', array('id'=>$this->get_ui()->get_controller()->get_courseid())));
        }

        // First decide whether a substage is needed
        $rc = $this->ui->get_controller();
        if ($rc->get_status() == backup::STATUS_SETTING_UI) {
            $rc->finish_ui();
        }
        if ($rc->get_status() == backup::STATUS_NEED_PRECHECK) {
            if (!$rc->precheck_executed()) {
                $rc->execute_precheck(true);
            }
            $results = $rc->get_precheck_results();
            if (!empty($results)) {
                $this->substage = self::SUBSTAGE_PRECHECKS;
            }
        }

        $substage = optional_param('substage', null, PARAM_INT);
        if (empty($this->substage) && !empty($substage)) {
            $this->substage = $substage;
            // Now check whether that substage has already been submit
            if ($this->substage == self::SUBSTAGE_PRECHECKS && optional_param('sesskey', null, PARAM_RAW) == sesskey()) {
                $info = $rc->get_info();
                if (!empty($info->role_mappings->mappings)) {
                    foreach ($info->role_mappings->mappings as $key=>&$mapping) {
                        $mapping->targetroleid = optional_param('mapping'.$key, $mapping->targetroleid, PARAM_INT);
                    }
                    $info->role_mappings->modified = true;
                }
                // We've processed the substage now setting it back to none so we
                // can move to the next stage.
                $this->substage = self::SUBSTAGE_NONE;
            }
        }

        return empty($this->substage);
    }
    /**
     * should NEVER be called... throws an exception
     */
    protected function initialise_stage_form() {
        throw new backup_ui_exception('backup_ui_must_execute_first');
    }

    /**
     * Renders the process stage screen
     *
     * @param core_backup_renderer $renderer renderer instance to use
     * @return string HTML code
     */
    public function display(core_backup_renderer $renderer) {
        global $PAGE;

        $html = '';
        $haserrors = false;
        $url = new moodle_url($PAGE->url, array(
            'restore'   => $this->get_uniqueid(),
            'stage'     => restore_ui::STAGE_PROCESS,
            'substage'  => $this->substage,
            'sesskey'   => sesskey()));
        $html .= html_writer::start_tag('form', array(
            'action'    => $url->out_omit_querystring(),
            'class'     => 'backup-restore',
            'method'    => 'post'));
        foreach ($url->params() as $name => $value) {
            $html .= html_writer::empty_tag('input', array(
                'type'  => 'hidden',
                'name'  => $name,
                'value' => $value));
        }
        switch ($this->substage) {
            case self::SUBSTAGE_PRECHECKS :
                $results = $this->ui->get_controller()->get_precheck_results();
                $info = $this->ui->get_controller()->get_info();
                $haserrors = (!empty($results['errors']));
                $html .= $renderer->precheck_notices($results);
                if (!empty($info->role_mappings->mappings)) {
                    $context = context_course::instance($this->ui->get_controller()->get_courseid());
                    $assignableroles = get_assignable_roles($context, ROLENAME_ALIAS, false);
                    $html .= $renderer->role_mappings($info->role_mappings->mappings, $assignableroles);
                }
                break;
            default:
                throw new restore_ui_exception('backup_ui_must_execute_first');
        }
        $html .= $renderer->substage_buttons($haserrors);
        $html .= html_writer::end_tag('form');

        return $html;
    }

    public function has_sub_stages() {
        return true;
    }
}

/**
 * This is the completed stage.
 *
 * Once this is displayed there is nothing more to do.
 */
class restore_ui_stage_complete extends restore_ui_stage_process {
    /**
     * The results of the backup execution
     * @var array
     */
    protected $results;
    /**
     * Constructs the complete backup stage
     * @param backup_ui $ui
     * @param array|null $params
     * @param array $results
     */
    public function __construct(restore_ui $ui, array $params=null, array $results=null) {
        $this->results = $results;
        parent::__construct($ui, $params);
        $this->stage = restore_ui::STAGE_COMPLETE;
    }

    /**
     * Displays the completed backup stage.
     *
     * Currently this just envolves redirecting to the file browser with an
     * appropriate message.
     *
     * @param core_backup_renderer $renderer
     * @return string HTML code to echo
     */
    public function display(core_backup_renderer $renderer) {

        $html  = '';
        if (!empty($this->results['file_aliases_restore_failures'])) {
            $html .= $renderer->box_start('generalbox filealiasesfailures');
            $html .= $renderer->heading_with_help(get_string('filealiasesrestorefailures', 'core_backup'),
                'filealiasesrestorefailures', 'core_backup');
            $html .= $renderer->container(get_string('filealiasesrestorefailuresinfo', 'core_backup'));
            $html .= $renderer->container_start('aliaseslist');
            $html .= html_writer::start_tag('ul');
            foreach ($this->results['file_aliases_restore_failures'] as $alias) {
                $html .= html_writer::tag('li', s($alias));
            }
            $html .= html_writer::end_tag('ul');
            $html .= $renderer->container_end();
            $html .= $renderer->box_end();
        }
        $html .= $renderer->box_start();
        if (array_key_exists('file_missing_in_backup', $this->results)) {
            $html .= $renderer->notification(get_string('restorefileweremissing', 'backup'), 'notifyproblem');
        }
        $html .= $renderer->notification(get_string('restoreexecutionsuccess', 'backup'), 'notifysuccess');
        $html .= $renderer->continue_button(new moodle_url('/course/view.php', array(
            'id' => $this->get_ui()->get_controller()->get_courseid())), 'get');
        $html .= $renderer->box_end();

        return $html;
    }
}
