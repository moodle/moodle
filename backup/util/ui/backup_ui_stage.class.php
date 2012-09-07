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
 * Backup user interface stages
 *
 * This file contains the classes required to manage the stages that make up the
 * backup user interface.
 * These will be primarily operated a {@see backup_ui} instance.
 *
 * @package   moodlecore
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Abstract stage class
 *
 * This class should be extended by all backup stages (a requirement of many backup ui functions).
 * Each stage must then define two abstract methods
 *  - process : To process the stage
 *  - initialise_stage_form : To get a backup_moodleform instance for the stage
 *
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class backup_ui_stage extends base_ui_stage {

    public function __construct(backup_ui $ui, array $params = null) {
       parent::__construct($ui, $params);
    }
    /**
     * The backup id from the backup controller
     * @return string
     */
    final public function get_backupid() {
        return $this->get_uniqueid();
    }
}

/**
 * Class representing the initial stage of a backup.
 *
 * In this stage the user is required to set the root level settings.
 *
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_ui_stage_initial extends backup_ui_stage {

    /**
     * Initial backup stage constructor
     * @param backup_ui $ui
     */
    public function __construct(backup_ui $ui, array $params=null) {
        $this->stage = backup_ui::STAGE_INITIAL;
        parent::__construct($ui, $params);
    }

    /**
     * Processes the initial backup stage
     * @param backup_moodleform $form
     * @return int The number of changes
     */
    public function process(base_moodleform $m = null) {

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
                if ($task instanceof backup_root_task) {
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

    /**
     * Initialises the backup_moodleform instance for this stage
     *
     * @return backup_initial_form
     */
    protected function initialise_stage_form() {
        global $PAGE;
        if ($this->stageform === null) {
            $form = new backup_initial_form($this, $PAGE->url);
            // Store as a variable so we can iterate by reference
            $tasks = $this->ui->get_tasks();
            // Iterate all tasks by reference
            $add_settings = array();
            $dependencies = array();
            foreach ($tasks as &$task) {
                // For the initial stage we are only interested in the root settings
                if ($task instanceof backup_root_task) {
                    $form->add_heading('rootsettings', get_string('rootsettings', 'backup'));
                    $settings = $task->get_settings();
                    // First add all settings except the filename setting
                    foreach ($settings as &$setting) {
                        if ($setting->get_name() == 'filename') {
                            continue;
                        }
                        $add_settings[] = array($setting, $task);
                    }
                    // Then add all dependencies
                    foreach ($settings as &$setting) {
                        if ($setting->get_name() == 'filename') {
                            continue;
                        }
                        $dependencies[] = $setting;
                    }
                }
            }
            // Add all settings at once.
            $form->add_settings($add_settings);
            // Add dependencies.
            foreach ($dependencies as $depsetting) {
                $form->add_dependencies($depsetting);
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
class backup_ui_stage_schema extends backup_ui_stage {
    /**
     * Schema stage constructor
     * @param backup_moodleform $ui
     */
    public function __construct(backup_ui $ui, array $params=null) {
        $this->stage = backup_ui::STAGE_SCHEMA;
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
                if (!($task instanceof backup_root_task)) {
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
            $form = new backup_schema_form($this, $PAGE->url);
            $tasks = $this->ui->get_tasks();
            $content = '';
            $courseheading = false;
            $add_settings = array();
            $dependencies = array();
            foreach ($tasks as $task) {
                if (!($task instanceof backup_root_task)) {
                    if (!$courseheading) {
                        // If we havn't already display a course heading to group nicely
                        $form->add_heading('coursesettings', get_string('includeactivities', 'backup'));
                        $courseheading = true;
                    }
                    // First add each setting
                    foreach ($task->get_settings() as $setting) {
                        $add_settings[] = array($setting, $task);
                    }
                    // The add all the dependencies
                    foreach ($task->get_settings() as $setting) {
                        $dependencies[] = $setting;
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
            $form->add_settings($add_settings);
            foreach ($dependencies as $depsetting) {
                $form->add_dependencies($depsetting);
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
class backup_ui_stage_confirmation extends backup_ui_stage {
    /**
     * Constructs the stage
     * @param backup_ui $ui
     */
    public function __construct($ui, array $params=null) {
        $this->stage = backup_ui::STAGE_CONFIRMATION;
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
            // Collect into a variable so we can iterate by reference
            $tasks = $this->ui->get_tasks();
            $changes = 0;
            // Iterate each task by reference
            foreach ($tasks as &$task) {
                if ($task instanceof backup_root_task) {
                    // At this stage all we are interested in is the filename setting
                    $setting = $task->get_setting('filename');
                    $name = $setting->get_ui_name();
                    if (isset($data->$name) &&  $data->$name != $setting->get_value()) {
                        $setting->set_value($data->$name);
                        $changes++;
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
     * Creates the backup_confirmation_form instance this stage requires
     *
     * @return backup_confirmation_form
     */
    protected function initialise_stage_form() {
        global $PAGE;
        if ($this->stageform === null) {
            // Get the form
            $form = new backup_confirmation_form($this, $PAGE->url);
            $content = '';
            $courseheading = false;

            foreach ($this->ui->get_tasks() as $task) {
                if ($setting = $task->get_setting('filename')) {
                    $form->add_heading('filenamesetting', get_string('filename', 'backup'));
                    if ($setting->get_value() == 'backup.mbz') {
                        $format = $this->ui->get_format();
                        $type = $this->ui->get_type();
                        $id = $this->ui->get_controller_id();
                        $users = $this->ui->get_setting_value('users');
                        $anonymised = $this->ui->get_setting_value('anonymize');
                        $setting->set_value(backup_plan_dbops::get_default_backup_filename($format, $type, $id, $users, $anonymised));
                    }
                    $form->add_setting($setting, $task);
                    break;
                }
            }

            foreach ($this->ui->get_tasks() as $task) {
                if ($task instanceof backup_root_task) {
                    // If its a backup root add a root settings heading to group nicely
                    $form->add_heading('rootsettings', get_string('rootsettings', 'backup'));
                } else if (!$courseheading) {
                    // we havn't already add a course heading
                    $form->add_heading('coursesettings', get_string('includeditems', 'backup'));
                    $courseheading = true;
                }
                // Iterate all settings, doesnt need to happen by reference
                foreach ($task->get_settings() as $setting) {
                    // For this stage only the filename setting should be editable
                    if ($setting->get_name() != 'filename') {
                        $form->add_fixed_setting($setting, $task);
                    }
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
class backup_ui_stage_final extends backup_ui_stage {
    /**
     * Constructs the final stage
     * @param backup_ui $ui
     */
    public function __construct(backup_ui $ui, array $params=null) {
        $this->stage = backup_ui::STAGE_FINAL;
        parent::__construct($ui, $params);
    }
    /**
     * Processes the final stage.
     *
     * In this case it ALWAYS passes processing to the previous stage (confirmation)
     */
    public function process(base_moodleform $form=null) {
        return true;
    }
    /**
     * should NEVER be called... throws an exception
     */
    protected function initialise_stage_form() {
        throw new backup_ui_exception('backup_ui_must_execute_first');
    }
    /**
     * should NEVER be called... throws an exception
     */
    public function display(core_backup_renderer $renderer) {
        throw new backup_ui_exception('backup_ui_must_execute_first');
    }
}

/**
 * The completed backup stage
 *
 * At this stage everything is done and the user will be redirected to view the
 * backup file in the file browser.
 *
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_ui_stage_complete extends backup_ui_stage_final {
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
    public function __construct(backup_ui $ui, array $params=null, array $results=null) {
        $this->results = $results;
        parent::__construct($ui, $params);
        $this->stage = backup_ui::STAGE_COMPLETE;
    }
    /**
     * Displays the completed backup stage.
     *
     * Currently this just involves redirecting to the file browser with an
     * appropriate message.
     *
     * @param core_backup_renderer $renderer
     * @return string HTML code to echo
     */
    public function display(core_backup_renderer $renderer) {

        // Get the resulting stored_file record
        $type = $this->get_ui()->get_controller()->get_type();
        $courseid = $this->get_ui()->get_controller()->get_courseid();
        switch ($type) {
        case 'activity':
            $cmid = $this->get_ui()->get_controller()->get_id();
            $cm = get_coursemodule_from_id(null, $cmid, $courseid);
            $modcontext = context_module::instance($cm->id);
            $restorerul = new moodle_url('/backup/restorefile.php', array('contextid'=>$modcontext->id));
            break;
        case 'course':
        default:
            $coursecontext = context_course::instance($courseid);
            $restorerul = new moodle_url('/backup/restorefile.php', array('contextid'=>$coursecontext->id));
        }

        $output = '';
        $output .= $renderer->box_start();
        if (!empty($this->results['include_file_references_to_external_content'])) {
            $output .= $renderer->notification(get_string('filereferencesincluded', 'backup'), 'notifyproblem');
        }
        if (!empty($this->results['missing_files_in_pool'])) {
            $output .= $renderer->notification(get_string('missingfilesinpool', 'backup'), 'notifyproblem');
        }
        $output .= $renderer->notification(get_string('executionsuccess', 'backup'), 'notifysuccess');
        $output .= $renderer->continue_button($restorerul);
        $output .= $renderer->box_end();

        return $output;
    }
}
