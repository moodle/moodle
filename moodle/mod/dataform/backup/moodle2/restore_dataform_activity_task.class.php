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
 * @copyright 2011 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') or die;

require_once("$CFG->dirroot/mod/dataform/backup/moodle2/restore_dataform_stepslib.php");

/**
 * dataform restore task that provides all the settings and steps to perform one
 * complete restore of the activity.
 */
class restore_dataform_activity_task extends restore_activity_task {

    /* @var int User id of designated owner of content. */
    protected $ownerid = 0;

    /**
     *
     */
    public function get_old_moduleid() {
        return $this->oldmoduleid;
    }

    /**
     *
     */
    public function set_ownerid($ownerid) {
        $this->ownerid = $ownerid;
    }

    /**
     *
     */
    public function get_ownerid() {
        return $this->ownerid;
    }

    /**
     * TODO Implement comment mapping itemname for non-dataformfield comments.
     * $itemname = parent::get_comment_mapping_itemname($commentarea);
     */
    public function get_comment_mapping_itemname($commentarea) {
        return 'dataform_entry';
    }

    /**
     * Override to remove the course module step if restoring a preset.
     */
    public function build() {
        parent::build();

        // If restoring into a given activity replace the restore module structure step
        // with the our specialized one, as we only need to update the current module instance.
        if ($this->get_activityid()) {
            $steps = array();
            foreach ($this->steps as $key => $step) {
                if ($step instanceof restore_module_structure_step) {
                    $step = new restore_dataform_to_module_structure_step('module_info', 'module.xml');
                    $step->set_task($this);
                    $steps[] = $step;
                } else {
                    $steps[] = $step;
                }
            }
            $this->steps = $steps;
        }
    }

    /**
     * Define (add) particular settings this activity can have.
     */
    protected function define_my_settings() {
    }

    /**
     * Define (add) particular steps this activity can have.
     */
    protected function define_my_steps() {
        // Dataform only has one structure step.
        $this->add_step(new restore_dataform_activity_structure_step('dataform_structure', 'dataform.xml'));
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder.
     */
    static public function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('dataform', array('intro'), 'dataform');
        $contents[] = new restore_decode_content('dataform_fields', array(
                              'description',
                              'param1', 'param2', 'param3', 'param4', 'param5',
                              'param6', 'param7', 'param8', 'param9', 'param10'), 'dataform_field');
        $contents[] = new restore_decode_content('dataform_views', array(
                              'description', 'section',
                              'param1', 'param2', 'param3', 'param4', 'param5',
                              'param6', 'param7', 'param8', 'param9', 'param10'), 'dataform_view');
        $contents[] = new restore_decode_content('dataform_contents', array(
                              'content', 'content1', 'content2', 'content3', 'content4'), 'dataform_content');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder.
     */
    static public function define_decode_rules() {
        $rules = array();

        $rules[] = new restore_decode_rule('DFINDEX', '/mod/dataform/index.php?id=$1', 'course');

        $rules[] = new restore_decode_rule('DFVIEWBYID', '/mod/dataform/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('DFEMBEDBYID', '/mod/dataform/embed.php?id=$1', 'course_module');

        $rules[] = new restore_decode_rule('DFVIEWBYD', '/mod/dataform/view.php?d=$1', 'dataform');
        $rules[] = new restore_decode_rule('DFEMBEDBYD', '/mod/dataform/embed.php?d=$1', 'dataform');

        $pattern = '/mod/dataform/view.php?d=$1&amp;view=$2';
        $rules[] = new restore_decode_rule('DFVIEWVIEW', $pattern, array('dataform', 'dataform_view'));
        $pattern = '/mod/dataform/embed.php?d=$1&amp;view=$2';
        $rules[] = new restore_decode_rule('DFEMBEDVIEW', $pattern, array('dataform', 'dataform_view'));

        $pattern = '/mod/dataform/view.php?d=$1&amp;view=$2&amp;filter=$3';
        $rules[] = new restore_decode_rule('DFVIEWVIEWFILTER', $pattern, array('dataform', 'dataform_view', 'dataform_filter'));
        $pattern = '/mod/dataform/embed.php?d=$1&amp;view=$2&amp;filter=$3';
        $rules[] = new restore_decode_rule('DFEMBEDVIEWFILTER', $pattern, array('dataform', 'dataform_view', 'dataform_filter'));

        $pattern = '/mod/dataform/view.php?d=$1&amp;eid=$2';
        $rules[] = new restore_decode_rule('DFVIEWENTRY', $pattern, array('dataform', 'dataform_entry'));
        $pattern = '/mod/dataform/embed.php?d=$1&amp;eid=$2';
        $rules[] = new restore_decode_rule('DFEMBEDENTRY', $pattern, array('dataform', 'dataform_entry'));

        return $rules;

    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * data logs. It must return one array
     * of {@link restore_log_rule} objects
     */
    static public function define_restore_log_rules() {
        $rules = array();

        $pattern = 'view.php?d={dataform}&eid={dataform_entry}';
        $rules[] = new restore_log_rule('dataform', 'add', $pattern, '{dataform}');

        $pattern = 'view.php?d={dataform}&eid={dataform_entry}';
        $rules[] = new restore_log_rule('dataform', 'update', $pattern, '{dataform}');

        $pattern = 'view.php?id={course_module}';
        $rules[] = new restore_log_rule('dataform', 'view', $pattern, '{dataform}');

        $pattern = 'view.php?id={course_module}';
        $rules[] = new restore_log_rule('dataform', 'entry delete', $pattern, '{dataform}');

        $pattern = 'field/index.php?d={dataform}&fid={dataform_field}';
        $rules[] = new restore_log_rule('dataform', 'fields add', $pattern, '{dataform_field}');

        $pattern = 'field/index.php?d={dataform}&fid={dataform_field}';
        $rules[] = new restore_log_rule('dataform', 'fields update', $pattern, '{dataform_field}');

        $pattern = 'field/index.php?d={dataform}';
        $rules[] = new restore_log_rule('dataform', 'fields delete', $pattern, '[name]');

        $pattern = 'view/index.php?d={dataform}&vid={dataform_view}';
        $rules[] = new restore_log_rule('dataform', 'views add', $pattern, '{dataform_view}');

        $pattern = 'view/index.php?d={dataform}&vid={dataform_view}';
        $rules[] = new restore_log_rule('dataform', 'views update', $pattern, '{dataform_view}');

        $pattern = 'view/index.php?d={dataform}';
        $rules[] = new restore_log_rule('dataform', 'views delete', $pattern, '[name]');

        $pattern = 'filter/index.php?d={dataform}&fid={dataform_filter}';
        $rules[] = new restore_log_rule('dataform', 'filters add', $pattern, '{dataform_filter}');

        $pattern = 'filter/index.php?d={dataform}&fid={dataform_filter}';
        $rules[] = new restore_log_rule('dataform', 'filters update', $pattern, '{dataform_filter}');

        $pattern = 'filter/index.php?d={dataform}';
        $rules[] = new restore_log_rule('dataform', 'filters delete', $pattern, '[name]');

        $pattern = 'rule/index.php?d={dataform}&rid={dataform_rule}';
        $rules[] = new restore_log_rule('dataform', 'rules add', $pattern, '{dataform_rule}');

        $pattern = 'rule/index.php?d={dataform}&rid={dataform_rule}';
        $rules[] = new restore_log_rule('dataform', 'rules update', $pattern, '{dataform_rule}');

        $pattern = 'rule/index.php?d={dataform}';
        $rules[] = new restore_log_rule('dataform', 'rules delete', $pattern, '[name]');

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * course logs. It must return one array
     * of {@link restore_log_rule} objects
     *
     * Note this rules are applied when restoring course logs
     * by the restore final task, but are defined here at
     * activity level. All them are rules not linked to any module instance (cmid = 0)
     */
    static public function define_restore_log_rules_for_course() {
        $rules = array();

        $rules[] = new restore_log_rule('dataform', 'view all', 'index.php?id={course}', null);

        return $rules;
    }
}


/**
 * Structure step to restore common course_module information into an existing module.
 *
 * This step will process the module.xml file for one activity, in order to restore
 * the corresponding information into the course module within which the restore is
 * executed, skipping various bits
 * of information based on CFG settings (groupings, completion...) in order to fullfill
 * all the reqs to be able to use the context by all the rest of steps
 * in the activity restore task
 */
class restore_dataform_to_module_structure_step extends restore_module_structure_step {

    protected function process_module($data) {
        global $CFG, $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $this->task->set_old_moduleversion($data->version);

        // Get the current course module data.
        $newitemid = $this->task->get_moduleid();
        $params = array('id' => $newitemid);
        $cmdata = $DB->get_record('course_modules', $params, '*', MUST_EXIST);

        // Group mode and Grouping.
        $cmdata->groupmode = $data->groupmode;
        $cmdata->groupingid = $this->get_mappingid('grouping', $data->groupingid);

        // Idnumber uniqueness.
        if (!grade_verify_idnumber($data->idnumber, $this->get_courseid())) {
            $data->idnumber = '';
        }
        $cmdata->idnumber = $data->idnumber;

        // Completion.
        if (!empty($CFG->enablecompletion)) {
            $cmdata->completion = $data->completion;
            $cmdata->completiongradeitemnumber = $data->completiongradeitemnumber;
            $cmdata->completionview = $data->completionview;
            $cmdata->completionexpected = $this->apply_date_offset($data->completionexpected);
        }

        // Availability.
        if (empty($CFG->enableavailability)) {
            $data->availability = null;
        }
        if (empty($data->availability)) {
            // If there are legacy availablility data fields (and no new format data),
            // convert the old fields.
            $data->availability = \core_availability\info::convert_legacy_fields(
                    $data, false);
        } else if (!empty($data->groupmembersonly)) {
            // There is current availability data, but it still has groupmembersonly
            // as well (2.7 backups), convert just that part.
            require_once($CFG->dirroot . '/lib/db/upgradelib.php');
            $data->availability = upgrade_group_members_only($data->groupingid, $data->availability);
        }
        $cmdata->availability = $data->availability;

        // Backups that did not include showdescription, set it to default 0
        // (this is not totally necessary as it has a db default, but just to
        // be explicit).
        if (!isset($data->showdescription)) {
            $data->showdescription = 0;
        }
        $cmdata->showdescription = $data->showdescription;

        // Course_module record ready, update it.
        $DB->update_record('course_modules', $cmdata);
        // Save mapping.
        $this->set_mapping('course_module', $oldid, $newitemid);
        // Set the new course_module id in the task.
        $this->task->set_moduleid($newitemid);
        // We can now create the context safely.
        $ctxid = context_module::instance($newitemid)->id;
        // Set the new context id in the task.
        $this->task->set_contextid($ctxid);

        // If there is the legacy showavailability data, store this for later use.
        // (This data is not present when restoring 'new' backups.)
        if (isset($cmdata->showavailability)) {
            // Cache the showavailability flag using the backup_ids data field.
            restore_dbops::set_backup_ids_record($this->get_restoreid(),
                    'module_showavailability', $newitemid, 0, null,
                    (object)array('showavailability' => $cmdata->showavailability));
        }
    }

}
