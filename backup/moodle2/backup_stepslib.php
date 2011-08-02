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
 * @package moodlecore
 * @subpackage backup-moodle2
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the backup steps that will be used by common tasks in backup
 */

/**
 * create the temp dir where backup/restore will happen,
 * delete old directories and create temp ids table
 */
class create_and_clean_temp_stuff extends backup_execution_step {

    protected function define_execution() {
        backup_helper::check_and_create_backup_dir($this->get_backupid());// Create backup temp dir
        backup_helper::clear_backup_dir($this->get_backupid());           // Empty temp dir, just in case
        backup_helper::delete_old_backup_dirs(time() - (4 * 60 * 60));    // Delete > 4 hours temp dirs
        backup_controller_dbops::create_backup_ids_temp_table($this->get_backupid()); // Create ids temp table
    }
}

/**
 * delete the temp dir used by backup/restore (conditionally),
 * delete old directories and drop tem ids table. Note we delete
 * the directory but not the corresponding log file that will be
 * there for, at least, 4 hours - only delete_old_backup_dirs()
 * deletes log files (for easier access to them)
 */
class drop_and_clean_temp_stuff extends backup_execution_step {

    protected $skipcleaningtempdir = false;

    protected function define_execution() {
        global $CFG;

        backup_controller_dbops::drop_backup_ids_temp_table($this->get_backupid()); // Drop ids temp table
        backup_helper::delete_old_backup_dirs(time() - (4 * 60 * 60));              // Delete > 4 hours temp dirs
        // Delete temp dir conditionally:
        // 1) If $CFG->keeptempdirectoriesonbackup is not enabled
        // 2) If backup temp dir deletion has been marked to be avoided
        if (empty($CFG->keeptempdirectoriesonbackup) && !$this->skipcleaningtempdir) {
            backup_helper::delete_backup_dir($this->get_backupid()); // Empty backup dir
        }
    }

    public function skip_cleaning_temp_dir($skip) {
        $this->skipcleaningtempdir = $skip;
    }
}

/**
 * Create the directory where all the task (activity/block...) information will be stored
 */
class create_taskbasepath_directory extends backup_execution_step {

    protected function define_execution() {
        global $CFG;
        $basepath = $this->task->get_taskbasepath();
        if (!check_dir_exists($basepath, true, true)) {
            throw new backup_step_exception('cannot_create_taskbasepath_directory', $basepath);
        }
    }
}

/**
 * Abstract structure step, parent of all the activity structure steps. Used to wrap the
 * activity structure definition within the main <activity ...> tag. Also provides
 * subplugin support for activities (that must be properly declared)
 */
abstract class backup_activity_structure_step extends backup_structure_step {

    /**
     * Add subplugin structure to any element in the activity backup tree
     *
     * @param string $subplugintype type of subplugin as defined in activity db/subplugins.php
     * @param backup_nested_element $element element in the activity backup tree that
     *                                       we are going to add subplugin information to
     * @param bool $multiple to define if multiple subplugins can produce information
     *                       for each instance of $element (true) or no (false)
     */
    protected function add_subplugin_structure($subplugintype, $element, $multiple) {

        global $CFG;

        // Check the requested subplugintype is a valid one
        $subpluginsfile = $CFG->dirroot . '/mod/' . $this->task->get_modulename() . '/db/subplugins.php';
        if (!file_exists($subpluginsfile)) {
             throw new backup_step_exception('activity_missing_subplugins_php_file', $this->task->get_modulename());
        }
        include($subpluginsfile);
        if (!array_key_exists($subplugintype, $subplugins)) {
             throw new backup_step_exception('incorrect_subplugin_type', $subplugintype);
        }

        // Arrived here, subplugin is correct, let's create the optigroup
        $optigroupname = $subplugintype . '_' . $element->get_name() . '_subplugin';
        $optigroup = new backup_optigroup($optigroupname, null, $multiple);
        $element->add_child($optigroup); // Add optigroup to stay connected since beginning

        // Get all the optigroup_elements, looking across all the subplugin dirs
        $subpluginsdirs = get_plugin_list($subplugintype);
        foreach ($subpluginsdirs as $name => $subpluginsdir) {
            $classname = 'backup_' . $subplugintype . '_' . $name . '_subplugin';
            $backupfile = $subpluginsdir . '/backup/moodle2/' . $classname . '.class.php';
            if (file_exists($backupfile)) {
                require_once($backupfile);
                $backupsubplugin = new $classname($subplugintype, $name, $optigroup, $this);
                // Add subplugin returned structure to optigroup
                $backupsubplugin->define_subplugin_structure($element->get_name());
            }
        }
    }

    /**
     * As far as activity backup steps are implementing backup_subplugin stuff, they need to
     * have the parent task available for wrapping purposes (get course/context....)
     */
    public function get_task() {
        return $this->task;
    }

    /**
     * Wraps any activity backup structure within the common 'activity' element
     * that will include common to all activities information like id, context...
     */
    protected function prepare_activity_structure($activitystructure) {

        // Create the wrap element
        $activity = new backup_nested_element('activity', array('id', 'moduleid', 'modulename', 'contextid'), null);

        // Build the tree
        $activity->add_child($activitystructure);

        // Set the source
        $activityarr = array((object)array(
            'id'         => $this->task->get_activityid(),
            'moduleid'   => $this->task->get_moduleid(),
            'modulename' => $this->task->get_modulename(),
            'contextid'  => $this->task->get_contextid()));

        $activity->set_source_array($activityarr);

        // Return the root element (activity)
        return $activity;
    }
}

/**
 * Abstract structure step, to be used by all the activities using core questions stuff
 * (namely quiz module), supporting question plugins, states and sessions
 */
abstract class backup_questions_activity_structure_step extends backup_activity_structure_step {

    /**
     * Attach to $element (usually attempts) the needed backup structures
     * for question_usages and all the associated data.
     */
    protected function add_question_usages($element, $usageidname) {
        global $CFG;
        require_once($CFG->dirroot . '/question/engine/lib.php');

        // Check $element is one nested_backup_element
        if (! $element instanceof backup_nested_element) {
            throw new backup_step_exception('question_states_bad_parent_element', $element);
        }
        if (! $element->get_final_element($usageidname)) {
            throw new backup_step_exception('question_states_bad_question_attempt_element', $usageidname);
        }

        $quba = new backup_nested_element('question_usage', array('id'),
                array('component', 'preferredbehaviour'));

        $qas = new backup_nested_element('question_attempts');
        $qa = new backup_nested_element('question_attempt', array('id'), array(
                'slot', 'behaviour', 'questionid', 'maxmark', 'minfraction',
                'flagged', 'questionsummary', 'rightanswer', 'responsesummary',
                'timemodified'));

        $steps = new backup_nested_element('steps');
        $step = new backup_nested_element('step', array('id'), array(
                'sequencenumber', 'state', 'fraction', 'timecreated', 'userid'));

        $response = new backup_nested_element('response');
        $variable = new backup_nested_element('variable', null,  array('name', 'value'));

        // Build the tree
        $element->add_child($quba);
        $quba->add_child($qas);
        $qas->add_child($qa);
        $qa->add_child($steps);
        $steps->add_child($step);
        $step->add_child($response);
        $response->add_child($variable);

        // Set the sources
        $quba->set_source_table('question_usages',
                array('id'                => '../' . $usageidname));
        $qa->set_source_sql('
                SELECT *
                FROM {question_attempts}
                WHERE questionusageid = :questionusageid
                ORDER BY slot',
                array('questionusageid'   => backup::VAR_PARENTID));
        $step->set_source_sql('
                SELECT *
                FROM {question_attempt_steps}
                WHERE questionattemptid = :questionattemptid
                ORDER BY sequencenumber',
                array('questionattemptid' => backup::VAR_PARENTID));
        $variable->set_source_table('question_attempt_step_data',
                array('attemptstepid'     => backup::VAR_PARENTID));

        // Annotate ids
        $qa->annotate_ids('question', 'questionid');
        $step->annotate_ids('user', 'userid');

        // Annotate files
        $fileareas = question_engine::get_all_response_file_areas();
        foreach ($fileareas as $filearea) {
            $step->annotate_files('question', $filearea, 'id');
        }
    }
}


/**
 * backup structure step in charge of calculating the categories to be
 * included in backup, based in the context being backuped (module/course)
 * and the already annotated questions present in backup_ids_temp
 */
class backup_calculate_question_categories extends backup_execution_step {

    protected function define_execution() {
        backup_question_dbops::calculate_question_categories($this->get_backupid(), $this->task->get_contextid());
    }
}

/**
 * backup structure step in charge of deleting all the questions annotated
 * in the backup_ids_temp table
 */
class backup_delete_temp_questions extends backup_execution_step {

    protected function define_execution() {
        backup_question_dbops::delete_temp_questions($this->get_backupid());
    }
}

/**
 * Abstract structure step, parent of all the block structure steps. Used to wrap the
 * block structure definition within the main <block ...> tag
 */
abstract class backup_block_structure_step extends backup_structure_step {

    protected function prepare_block_structure($blockstructure) {

        // Create the wrap element
        $block = new backup_nested_element('block', array('id', 'blockname', 'contextid'), null);

        // Build the tree
        $block->add_child($blockstructure);

        // Set the source
        $blockarr = array((object)array(
            'id'         => $this->task->get_blockid(),
            'blockname'  => $this->task->get_blockname(),
            'contextid'  => $this->task->get_contextid()));

        $block->set_source_array($blockarr);

        // Return the root element (block)
        return $block;
    }
}

/**
 * structure step that will generate the module.xml file for the activity,
 * accumulating various information about the activity, annotating groupings
 * and completion/avail conf
 */
class backup_module_structure_step extends backup_structure_step {

    protected function define_structure() {

        // Define each element separated

        $module = new backup_nested_element('module', array('id', 'version'), array(
            'modulename', 'sectionid', 'sectionnumber', 'idnumber',
            'added', 'score', 'indent', 'visible',
            'visibleold', 'groupmode', 'groupingid', 'groupmembersonly',
            'completion', 'completiongradeitemnumber', 'completionview', 'completionexpected',
            'availablefrom', 'availableuntil', 'showavailability'));

        $availinfo = new backup_nested_element('availability_info');
        $availability = new backup_nested_element('availability', array('id'), array(
            'sourcecmid', 'requiredcompletion', 'gradeitemid', 'grademin', 'grademax'));

        // attach format plugin structure to $module element, only one allowed
        $this->add_plugin_structure('format', $module, false);

        // attach plagiarism plugin structure to $module element, there can be potentially
        // many plagiarism plugins storing information about this course
        $this->add_plugin_structure('plagiarism', $module, true);

        // Define the tree
        $module->add_child($availinfo);
        $availinfo->add_child($availability);

        // Set the sources

        $module->set_source_sql('
            SELECT cm.*, m.version, m.name AS modulename, s.id AS sectionid, s.section AS sectionnumber
              FROM {course_modules} cm
              JOIN {modules} m ON m.id = cm.module
              JOIN {course_sections} s ON s.id = cm.section
             WHERE cm.id = ?', array(backup::VAR_MODID));

        $availability->set_source_table('course_modules_availability', array('coursemoduleid' => backup::VAR_MODID));

        // Define annotations
        $module->annotate_ids('grouping', 'groupingid');

        // Return the root element ($module)
        return $module;
    }
}

/**
 * structure step that will generate the section.xml file for the section
 * annotating files
 */
class backup_section_structure_step extends backup_structure_step {

    protected function define_structure() {

        // Define each element separated

        $section = new backup_nested_element('section', array('id'), array(
            'number', 'name', 'summary', 'summaryformat', 'sequence', 'visible'));

        // attach format plugin structure to $section element, only one allowed
        $this->add_plugin_structure('format', $section, false);

        // Define sources

        $section->set_source_table('course_sections', array('id' => backup::VAR_SECTIONID));

        // Aliases
        $section->set_source_alias('section', 'number');

        // Set annotations
        $section->annotate_files('course', 'section', 'id');

        return $section;
    }
}

/**
 * structure step that will generate the course.xml file for the course, including
 * course category reference, tags, modules restriction information
 * and some annotations (files & groupings)
 */
class backup_course_structure_step extends backup_structure_step {

    protected function define_structure() {
        global $DB;

        // Define each element separated

        $course = new backup_nested_element('course', array('id', 'contextid'), array(
            'shortname', 'fullname', 'idnumber',
            'summary', 'summaryformat', 'format', 'showgrades',
            'newsitems', 'startdate', 'numsections',
            'marker', 'maxbytes', 'legacyfiles', 'showreports',
            'visible', 'hiddensections', 'groupmode', 'groupmodeforce',
            'defaultgroupingid', 'lang', 'theme',
            'timecreated', 'timemodified',
            'requested', 'restrictmodules',
            'enablecompletion', 'completionstartonenrol', 'completionnotify'));

        $category = new backup_nested_element('category', array('id'), array(
            'name', 'description'));

        $tags = new backup_nested_element('tags');

        $tag = new backup_nested_element('tag', array('id'), array(
            'name', 'rawname'));

        $allowedmodules = new backup_nested_element('allowed_modules');

        $module = new backup_nested_element('module', array(), array('modulename'));

        // attach format plugin structure to $course element, only one allowed
        $this->add_plugin_structure('format', $course, false);

        // attach theme plugin structure to $course element; multiple themes can
        // save course data (in case of user theme, legacy theme, etc)
        $this->add_plugin_structure('theme', $course, true);

        // attach course report plugin structure to $course element; multiple
        // course reports can save course data if required
        $this->add_plugin_structure('coursereport', $course, true);

        // attach plagiarism plugin structure to $course element, there can be potentially
        // many plagiarism plugins storing information about this course
        $this->add_plugin_structure('plagiarism', $course, true);

        // Build the tree

        $course->add_child($category);

        $course->add_child($tags);
        $tags->add_child($tag);

        $course->add_child($allowedmodules);
        $allowedmodules->add_child($module);

        // Set the sources

        $courserec = $DB->get_record('course', array('id' => $this->task->get_courseid()));
        $courserec->contextid = $this->task->get_contextid();

        $course->set_source_array(array($courserec));

        $categoryrec = $DB->get_record('course_categories', array('id' => $courserec->category));

        $category->set_source_array(array($categoryrec));

        $tag->set_source_sql('SELECT t.id, t.name, t.rawname
                                FROM {tag} t
                                JOIN {tag_instance} ti ON ti.tagid = t.id
                               WHERE ti.itemtype = ?
                                 AND ti.itemid = ?', array(
                                     backup_helper::is_sqlparam('course'),
                                     backup::VAR_PARENTID));

        $module->set_source_sql('SELECT m.name AS modulename
                                   FROM {modules} m
                                   JOIN {course_allowed_modules} cam ON m.id = cam.module
                                  WHERE course = ?', array(backup::VAR_COURSEID));

        // Some annotations

        $course->annotate_ids('grouping', 'defaultgroupingid');

        $course->annotate_files('course', 'summary', null);
        $course->annotate_files('course', 'legacy', null);

        // Return root element ($course)

        return $course;
    }
}

/**
 * structure step that will generate the enrolments.xml file for the given course
 */
class backup_enrolments_structure_step extends backup_structure_step {

    protected function define_structure() {

        // To know if we are including users
        $users = $this->get_setting_value('users');

        // Define each element separated

        $enrolments = new backup_nested_element('enrolments');

        $enrols = new backup_nested_element('enrols');

        $enrol = new backup_nested_element('enrol', array('id'), array(
            'enrol', 'status', 'sortorder', 'name', 'enrolperiod', 'enrolstartdate',
            'enrolenddate', 'expirynotify', 'expirytreshold', 'notifyall',
            'password', 'cost', 'currency', 'roleid', 'customint1', 'customint2', 'customint3',
            'customint4', 'customchar1', 'customchar2', 'customdec1', 'customdec2',
            'customtext1', 'customtext2', 'timecreated', 'timemodified'));

        $userenrolments = new backup_nested_element('user_enrolments');

        $enrolment = new backup_nested_element('enrolment', array('id'), array(
            'status', 'userid', 'timestart', 'timeend', 'modifierid',
            'timemodified'));

        // Build the tree
        $enrolments->add_child($enrols);
        $enrols->add_child($enrol);
        $enrol->add_child($userenrolments);
        $userenrolments->add_child($enrolment);

        // Define sources

        $enrol->set_source_table('enrol', array('courseid' => backup::VAR_COURSEID));

        // User enrolments only added only if users included
        if ($users) {
            $enrolment->set_source_table('user_enrolments', array('enrolid' => backup::VAR_PARENTID));
            $enrolment->annotate_ids('user', 'userid');
        }

        $enrol->annotate_ids('role', 'roleid');

        //TODO: let plugins annotate custom fields too and add more children

        return $enrolments;
    }
}

/**
 * structure step that will generate the roles.xml file for the given context, observing
 * the role_assignments setting to know if that part needs to be included
 */
class backup_roles_structure_step extends backup_structure_step {

    protected function define_structure() {

        // To know if we are including role assignments
        $roleassignments = $this->get_setting_value('role_assignments');

        // Define each element separated

        $roles = new backup_nested_element('roles');

        $overrides = new backup_nested_element('role_overrides');

        $override = new backup_nested_element('override', array('id'), array(
            'roleid', 'capability', 'permission', 'timemodified',
            'modifierid'));

        $assignments = new backup_nested_element('role_assignments');

        $assignment = new backup_nested_element('assignment', array('id'), array(
            'roleid', 'userid', 'timemodified', 'modifierid', 'component', 'itemid',
            'sortorder'));

        // Build the tree
        $roles->add_child($overrides);
        $roles->add_child($assignments);

        $overrides->add_child($override);
        $assignments->add_child($assignment);

        // Define sources

        $override->set_source_table('role_capabilities', array('contextid' => backup::VAR_CONTEXTID));

        // Assignments only added if specified
        if ($roleassignments) {
            $assignment->set_source_table('role_assignments', array('contextid' => backup::VAR_CONTEXTID));
        }

        // Define id annotations
        $override->annotate_ids('role', 'roleid');

        $assignment->annotate_ids('role', 'roleid');

        $assignment->annotate_ids('user', 'userid');

        //TODO: how do we annotate the itemid? the meaning depends on the content of component table (skodak)

        return $roles;
    }
}

/**
 * structure step that will generate the roles.xml containing the
 * list of roles used along the whole backup process. Just raw
 * list of used roles from role table
 */
class backup_final_roles_structure_step extends backup_structure_step {

    protected function define_structure() {

        // Define elements

        $rolesdef = new backup_nested_element('roles_definition');

        $role = new backup_nested_element('role', array('id'), array(
            'name', 'shortname', 'nameincourse', 'description',
            'sortorder', 'archetype'));

        // Build the tree

        $rolesdef->add_child($role);

        // Define sources

        $role->set_source_sql("SELECT r.*, rn.name AS nameincourse
                                 FROM {role} r
                                 JOIN {backup_ids_temp} bi ON r.id = bi.itemid
                            LEFT JOIN {role_names} rn ON r.id = rn.roleid AND rn.contextid = ?
                                WHERE bi.backupid = ?
                                  AND bi.itemname = 'rolefinal'", array(backup::VAR_CONTEXTID, backup::VAR_BACKUPID));

        // Return main element (rolesdef)
        return $rolesdef;
    }
}

/**
 * structure step that will generate the scales.xml containing the
 * list of scales used along the whole backup process.
 */
class backup_final_scales_structure_step extends backup_structure_step {

    protected function define_structure() {

        // Define elements

        $scalesdef = new backup_nested_element('scales_definition');

        $scale = new backup_nested_element('scale', array('id'), array(
            'courseid', 'userid', 'name', 'scale',
            'description', 'descriptionformat', 'timemodified'));

        // Build the tree

        $scalesdef->add_child($scale);

        // Define sources

        $scale->set_source_sql("SELECT s.*
                                  FROM {scale} s
                                  JOIN {backup_ids_temp} bi ON s.id = bi.itemid
                                 WHERE bi.backupid = ?
                                   AND bi.itemname = 'scalefinal'", array(backup::VAR_BACKUPID));

        // Annotate scale files (they store files in system context, so pass it instead of default one)
        $scale->annotate_files('grade', 'scale', 'id', get_context_instance(CONTEXT_SYSTEM)->id);

        // Return main element (scalesdef)
        return $scalesdef;
    }
}

/**
 * structure step that will generate the outcomes.xml containing the
 * list of outcomes used along the whole backup process.
 */
class backup_final_outcomes_structure_step extends backup_structure_step {

    protected function define_structure() {

        // Define elements

        $outcomesdef = new backup_nested_element('outcomes_definition');

        $outcome = new backup_nested_element('outcome', array('id'), array(
            'courseid', 'userid', 'shortname', 'fullname',
            'scaleid', 'description', 'descriptionformat', 'timecreated',
            'timemodified','usermodified'));

        // Build the tree

        $outcomesdef->add_child($outcome);

        // Define sources

        $outcome->set_source_sql("SELECT o.*
                                    FROM {grade_outcomes} o
                                    JOIN {backup_ids_temp} bi ON o.id = bi.itemid
                                   WHERE bi.backupid = ?
                                     AND bi.itemname = 'outcomefinal'", array(backup::VAR_BACKUPID));

        // Annotate outcome files (they store files in system context, so pass it instead of default one)
        $outcome->annotate_files('grade', 'outcome', 'id', get_context_instance(CONTEXT_SYSTEM)->id);

        // Return main element (outcomesdef)
        return $outcomesdef;
    }
}

/**
 * structure step in charge of constructing the filters.xml file for all the filters found
 * in activity
 */
class backup_filters_structure_step extends backup_structure_step {

    protected function define_structure() {

        // Define each element separated

        $filters = new backup_nested_element('filters');

        $actives = new backup_nested_element('filter_actives');

        $active = new backup_nested_element('filter_active', null, array('filter', 'active'));

        $configs = new backup_nested_element('filter_configs');

        $config = new backup_nested_element('filter_config', null, array('filter', 'name', 'value'));

        // Build the tree

        $filters->add_child($actives);
        $filters->add_child($configs);

        $actives->add_child($active);
        $configs->add_child($config);

        // Define sources

        list($activearr, $configarr) = filter_get_all_local_settings($this->task->get_contextid());

        $active->set_source_array($activearr);
        $config->set_source_array($configarr);

        // Return the root element (filters)
        return $filters;
    }
}

/**
 * structure step in charge of constructing the comments.xml file for all the comments found
 * in a given context
 */
class backup_comments_structure_step extends backup_structure_step {

    protected function define_structure() {

        // Define each element separated

        $comments = new backup_nested_element('comments');

        $comment = new backup_nested_element('comment', array('id'), array(
            'commentarea', 'itemid', 'content', 'format',
            'userid', 'timecreated'));

        // Build the tree

        $comments->add_child($comment);

        // Define sources

        $comment->set_source_table('comments', array('contextid' => backup::VAR_CONTEXTID));

        // Define id annotations

        $comment->annotate_ids('user', 'userid');

        // Return the root element (comments)
        return $comments;
    }
}

/**
 * structure step in charge of constructing the gradebook.xml file for all the gradebook config in the course
 * NOTE: the backup of the grade items themselves is handled by backup_activity_grades_structure_step
 */
class backup_gradebook_structure_step extends backup_structure_step {

    /**
     * We need to decide conditionally, based on dynamic information
     * about the execution of this step. Only will be executed if all
     * the module gradeitems have been already included in backup
     */
    protected function execute_condition() {
        return backup_plan_dbops::require_gradebook_backup($this->get_courseid(), $this->get_backupid());
    }

    protected function define_structure() {

        // are we including user info?
        $userinfo = $this->get_setting_value('users');

        $gradebook = new backup_nested_element('gradebook');

        //grade_letters are done in backup_activity_grades_structure_step()

        //calculated grade items
        $grade_items = new backup_nested_element('grade_items');
        $grade_item = new backup_nested_element('grade_item', array('id'), array(
            'categoryid', 'itemname', 'itemtype', 'itemmodule',
            'iteminstance', 'itemnumber', 'iteminfo', 'idnumber',
            'calculation', 'gradetype', 'grademax', 'grademin',
            'scaleid', 'outcomeid', 'gradepass', 'multfactor',
            'plusfactor', 'aggregationcoef', 'sortorder', 'display',
            'decimals', 'hidden', 'locked', 'locktime',
            'needsupdate', 'timecreated', 'timemodified'));

        $grade_grades = new backup_nested_element('grade_grades');
        $grade_grade = new backup_nested_element('grade_grade', array('id'), array(
            'userid', 'rawgrade', 'rawgrademax', 'rawgrademin',
            'rawscaleid', 'usermodified', 'finalgrade', 'hidden',
            'locked', 'locktime', 'exported', 'overridden',
            'excluded', 'feedback', 'feedbackformat', 'information',
            'informationformat', 'timecreated', 'timemodified'));

        //grade_categories
        $grade_categories = new backup_nested_element('grade_categories');
        $grade_category   = new backup_nested_element('grade_category', array('id'), array(
                //'courseid', 
                'parent', 'depth', 'path', 'fullname', 'aggregation', 'keephigh',
                'dropload', 'aggregateonlygraded', 'aggregateoutcomes', 'aggregatesubcats',
                'timecreated', 'timemodified', 'hidden'));

        $letters = new backup_nested_element('grade_letters');
        $letter = new backup_nested_element('grade_letter', 'id', array(
            'lowerboundary', 'letter'));

        $grade_settings = new backup_nested_element('grade_settings');
        $grade_setting = new backup_nested_element('grade_setting', 'id', array(
            'name', 'value'));


        // Build the tree
        $gradebook->add_child($grade_categories);
        $grade_categories->add_child($grade_category);

        $gradebook->add_child($grade_items);
        $grade_items->add_child($grade_item);
        $grade_item->add_child($grade_grades);
        $grade_grades->add_child($grade_grade);

        $gradebook->add_child($letters);
        $letters->add_child($letter);

        $gradebook->add_child($grade_settings);
        $grade_settings->add_child($grade_setting);

        // Define sources

        //Include manual, category and the course grade item
        $grade_items_sql ="SELECT * FROM {grade_items}
                           WHERE courseid = :courseid
                           AND (itemtype='manual' OR itemtype='course' OR itemtype='category')";
        $grade_items_params = array('courseid'=>backup::VAR_COURSEID);
        $grade_item->set_source_sql($grade_items_sql, $grade_items_params);

        if ($userinfo) {
            $grade_grade->set_source_table('grade_grades', array('itemid' => backup::VAR_PARENTID));
        }

        $grade_category_sql = "SELECT gc.*, gi.sortorder
                               FROM {grade_categories} gc
                               JOIN {grade_items} gi ON (gi.iteminstance = gc.id)
                               WHERE gc.courseid = :courseid
                               AND (gi.itemtype='course' OR gi.itemtype='category')
                               ORDER BY gc.parent ASC";//need parent categories before their children
        $grade_category_params = array('courseid'=>backup::VAR_COURSEID);
        $grade_category->set_source_sql($grade_category_sql, $grade_category_params);

        $letter->set_source_table('grade_letters', array('contextid' => backup::VAR_CONTEXTID));

        $grade_setting->set_source_table('grade_settings', array('courseid' => backup::VAR_COURSEID));

        // Annotations (both as final as far as they are going to be exported in next steps)
        $grade_item->annotate_ids('scalefinal', 'scaleid'); // Straight as scalefinal because it's > 0
        $grade_item->annotate_ids('outcomefinal', 'outcomeid');

        //just in case there are any users not already annotated by the activities
        $grade_grade->annotate_ids('userfinal', 'userid');

        // Return the root element
        return $gradebook;
    }
}

/**
 * structure step in charge if constructing the completion.xml file for all the users completion
 * information in a given activity
 */
class backup_userscompletion_structure_step extends backup_structure_step {

    protected function define_structure() {

        // Define each element separated

        $completions = new backup_nested_element('completions');

        $completion = new backup_nested_element('completion', array('id'), array(
            'userid', 'completionstate', 'viewed', 'timemodified'));

        // Build the tree

        $completions->add_child($completion);

        // Define sources

        $completion->set_source_table('course_modules_completion', array('coursemoduleid' => backup::VAR_MODID));

        // Define id annotations

        $completion->annotate_ids('user', 'userid');

        // Return the root element (completions)
        return $completions;
    }
}

/**
 * structure step in charge of constructing the main groups.xml file for all the groups and
 * groupings information already annotated
 */
class backup_groups_structure_step extends backup_structure_step {

    protected function define_structure() {

        // To know if we are including users
        $users = $this->get_setting_value('users');

        // Define each element separated

        $groups = new backup_nested_element('groups');

        $group = new backup_nested_element('group', array('id'), array(
            'name', 'description', 'descriptionformat', 'enrolmentkey',
            'picture', 'hidepicture', 'timecreated', 'timemodified'));

        $members = new backup_nested_element('group_members');

        $member = new backup_nested_element('group_member', array('id'), array(
            'userid', 'timeadded'));

        $groupings = new backup_nested_element('groupings');

        $grouping = new backup_nested_element('grouping', 'id', array(
            'name', 'description', 'descriptionformat', 'configdata',
            'timecreated', 'timemodified'));

        $groupinggroups = new backup_nested_element('grouping_groups');

        $groupinggroup = new backup_nested_element('grouping_group', array('id'), array(
            'groupid', 'timeadded'));

        // Build the tree

        $groups->add_child($group);
        $groups->add_child($groupings);

        $group->add_child($members);
        $members->add_child($member);

        $groupings->add_child($grouping);
        $grouping->add_child($groupinggroups);
        $groupinggroups->add_child($groupinggroup);

        // Define sources

        $group->set_source_sql("
            SELECT g.*
              FROM {groups} g
              JOIN {backup_ids_temp} bi ON g.id = bi.itemid
             WHERE bi.backupid = ?
               AND bi.itemname = 'groupfinal'", array(backup::VAR_BACKUPID));

        // This only happens if we are including users
        if ($users) {
            $member->set_source_table('groups_members', array('groupid' => backup::VAR_PARENTID));
        }

        $grouping->set_source_sql("
            SELECT g.*
              FROM {groupings} g
              JOIN {backup_ids_temp} bi ON g.id = bi.itemid
             WHERE bi.backupid = ?
               AND bi.itemname = 'groupingfinal'", array(backup::VAR_BACKUPID));

        $groupinggroup->set_source_table('groupings_groups', array('groupingid' => backup::VAR_PARENTID));

        // Define id annotations (as final)

        $member->annotate_ids('userfinal', 'userid');

        // Define file annotations

        $group->annotate_files('group', 'description', 'id');
        $group->annotate_files('group', 'icon', 'id');
        $grouping->annotate_files('grouping', 'description', 'id');

        // Return the root element (groups)
        return $groups;
    }
}

/**
 * structure step in charge of constructing the main users.xml file for all the users already
 * annotated (final). Includes custom profile fields, preferences, tags, role assignments and
 * overrides.
 */
class backup_users_structure_step extends backup_structure_step {

    protected function define_structure() {
        global $CFG;

        // To know if we are anonymizing users
        $anonymize = $this->get_setting_value('anonymize');
        // To know if we are including role assignments
        $roleassignments = $this->get_setting_value('role_assignments');

        // Define each element separated

        $users = new backup_nested_element('users');

        // Create the array of user fields by hand, as far as we have various bits to control
        // anonymize option, password backup, mnethostid...

        // First, the fields not needing anonymization nor special handling
        $normalfields = array(
            'confirmed', 'policyagreed', 'deleted',
            'lang', 'theme', 'timezone', 'firstaccess',
            'lastaccess', 'lastlogin', 'currentlogin',
            'mailformat', 'maildigest', 'maildisplay', 'htmleditor',
            'ajax', 'autosubscribe', 'trackforums', 'timecreated',
            'timemodified', 'trustbitmask', 'screenreader');

        // Then, the fields potentially needing anonymization
        $anonfields = array(
            'username', 'idnumber', 'firstname', 'lastname',
            'email', 'icq', 'skype',
            'yahoo', 'aim', 'msn', 'phone1',
            'phone2', 'institution', 'department', 'address',
            'city', 'country', 'lastip', 'picture',
            'url', 'description', 'descriptionformat', 'imagealt', 'auth');

        // Add anonymized fields to $userfields with custom final element
        foreach ($anonfields as $field) {
            if ($anonymize) {
                $userfields[] = new anonymizer_final_element($field);
            } else {
                $userfields[] = $field; // No anonymization, normally added
            }
        }

        // mnethosturl requires special handling (custom final element)
        $userfields[] = new mnethosturl_final_element('mnethosturl');

        // password added conditionally
        if (!empty($CFG->includeuserpasswordsinbackup)) {
            $userfields[] = 'password';
        }

        // Merge all the fields
        $userfields = array_merge($userfields, $normalfields);

        $user = new backup_nested_element('user', array('id', 'contextid'), $userfields);

        $customfields = new backup_nested_element('custom_fields');

        $customfield = new backup_nested_element('custom_field', array('id'), array(
            'field_name', 'field_type', 'field_data'));

        $tags = new backup_nested_element('tags');

        $tag = new backup_nested_element('tag', array('id'), array(
            'name', 'rawname'));

        $preferences = new backup_nested_element('preferences');

        $preference = new backup_nested_element('preference', array('id'), array(
            'name', 'value'));

        $roles = new backup_nested_element('roles');

        $overrides = new backup_nested_element('role_overrides');

        $override = new backup_nested_element('override', array('id'), array(
            'roleid', 'capability', 'permission', 'timemodified',
            'modifierid'));

        $assignments = new backup_nested_element('role_assignments');

        $assignment = new backup_nested_element('assignment', array('id'), array(
            'roleid', 'userid', 'timemodified', 'modifierid', 'component', //TODO: MDL-22793 add itemid here
            'sortorder'));

        // Build the tree

        $users->add_child($user);

        $user->add_child($customfields);
        $customfields->add_child($customfield);

        $user->add_child($tags);
        $tags->add_child($tag);

        $user->add_child($preferences);
        $preferences->add_child($preference);

        $user->add_child($roles);

        $roles->add_child($overrides);
        $roles->add_child($assignments);

        $overrides->add_child($override);
        $assignments->add_child($assignment);

        // Define sources

        $user->set_source_sql('SELECT u.*, c.id AS contextid, m.wwwroot AS mnethosturl
                                 FROM {user} u
                                 JOIN {backup_ids_temp} bi ON bi.itemid = u.id
                                 JOIN {context} c ON c.instanceid = u.id
                            LEFT JOIN {mnet_host} m ON m.id = u.mnethostid
                                WHERE bi.backupid = ?
                                  AND bi.itemname = ?
                                  AND c.contextlevel = ?', array(
                                      backup_helper::is_sqlparam($this->get_backupid()),
                                      backup_helper::is_sqlparam('userfinal'),
                                      backup_helper::is_sqlparam(CONTEXT_USER)));

        // All the rest on information is only added if we arent
        // in an anonymized backup
        if (!$anonymize) {
            $customfield->set_source_sql('SELECT f.id, f.shortname, f.datatype, d.data
                                            FROM {user_info_field} f
                                            JOIN {user_info_data} d ON d.fieldid = f.id
                                           WHERE d.userid = ?', array(backup::VAR_PARENTID));

            $customfield->set_source_alias('shortname', 'field_name');
            $customfield->set_source_alias('datatype',  'field_type');
            $customfield->set_source_alias('data',      'field_data');

            $tag->set_source_sql('SELECT t.id, t.name, t.rawname
                                    FROM {tag} t
                                    JOIN {tag_instance} ti ON ti.tagid = t.id
                                   WHERE ti.itemtype = ?
                                     AND ti.itemid = ?', array(
                                         backup_helper::is_sqlparam('user'),
                                         backup::VAR_PARENTID));

            $preference->set_source_table('user_preferences', array('userid' => backup::VAR_PARENTID));

            $override->set_source_table('role_capabilities', array('contextid' => '/users/user/contextid'));

            // Assignments only added if specified
            if ($roleassignments) {
                $assignment->set_source_table('role_assignments', array('contextid' => '/users/user/contextid'));
            }

            // Define id annotations (as final)
            $override->annotate_ids('rolefinal', 'roleid');
        }

        // Return root element (users)
        return $users;
    }
}

/**
 * structure step in charge of constructing the block.xml file for one
 * given block (instance and positions). If the block has custom DB structure
 * that will go to a separate file (different step defined in block class)
 */
class backup_block_instance_structure_step extends backup_structure_step {

    protected function define_structure() {
        global $DB;

        // Define each element separated

        $block = new backup_nested_element('block', array('id', 'contextid', 'version'), array(
            'blockname', 'parentcontextid', 'showinsubcontexts', 'pagetypepattern',
            'subpagepattern', 'defaultregion', 'defaultweight', 'configdata'));

        $positions = new backup_nested_element('block_positions');

        $position = new backup_nested_element('block_position', array('id'), array(
            'contextid', 'pagetype', 'subpage', 'visible',
            'region', 'weight'));

        // Build the tree

        $block->add_child($positions);
        $positions->add_child($position);

        // Transform configdata information if needed (process links and friends)
        $blockrec = $DB->get_record('block_instances', array('id' => $this->task->get_blockid()));
        if ($attrstotransform = $this->task->get_configdata_encoded_attributes()) {
            $configdata = (array)unserialize(base64_decode($blockrec->configdata));
            foreach ($configdata as $attribute => $value) {
                if (in_array($attribute, $attrstotransform)) {
                    $configdata[$attribute] = $this->contenttransformer->process($value);
                }
            }
            $blockrec->configdata = base64_encode(serialize((object)$configdata));
        }
        $blockrec->contextid = $this->task->get_contextid();
        // Get the version of the block
        $blockrec->version = $DB->get_field('block', 'version', array('name' => $this->task->get_blockname()));

        // Define sources

        $block->set_source_array(array($blockrec));

        $position->set_source_table('block_positions', array('blockinstanceid' => backup::VAR_PARENTID));

        // File anotations (for fileareas specified on each block)
        foreach ($this->task->get_fileareas() as $filearea) {
            $block->annotate_files('block_' . $this->task->get_blockname(), $filearea, null);
        }

        // Return the root element (block)
        return $block;
    }
}

/**
 * structure step in charge of constructing the logs.xml file for all the log records found
 * in course. Note that we are sending to backup ALL the log records having cmid = 0. That
 * includes some records that won't be restoreable (like 'upload', 'calendar'...) but we do
 * that just in case they become restored some day in the future
 */
class backup_course_logs_structure_step extends backup_structure_step {

    protected function define_structure() {

        // Define each element separated

        $logs = new backup_nested_element('logs');

        $log = new backup_nested_element('log', array('id'), array(
            'time', 'userid', 'ip', 'module',
            'action', 'url', 'info'));

        // Build the tree

        $logs->add_child($log);

        // Define sources (all the records belonging to the course, having cmid = 0)

        $log->set_source_table('log', array('course' => backup::VAR_COURSEID, 'cmid' => backup_helper::is_sqlparam(0)));

        // Annotations
        // NOTE: We don't annotate users from logs as far as they MUST be
        //       always annotated by the course (enrol, ras... whatever)

        // Return the root element (logs)

        return $logs;
    }
}

/**
 * structure step in charge of constructing the logs.xml file for all the log records found
 * in activity
 */
class backup_activity_logs_structure_step extends backup_structure_step {

    protected function define_structure() {

        // Define each element separated

        $logs = new backup_nested_element('logs');

        $log = new backup_nested_element('log', array('id'), array(
            'time', 'userid', 'ip', 'module',
            'action', 'url', 'info'));

        // Build the tree

        $logs->add_child($log);

        // Define sources

        $log->set_source_table('log', array('cmid' => backup::VAR_MODID));

        // Annotations
        // NOTE: We don't annotate users from logs as far as they MUST be
        //       always annotated by the activity (true participants).

        // Return the root element (logs)

        return $logs;
    }
}

/**
 * structure in charge of constructing the inforef.xml file for all the items we want
 * to have referenced there (users, roles, files...)
 */
class backup_inforef_structure_step extends backup_structure_step {

    protected function define_structure() {

        // Items we want to include in the inforef file.
        $items = backup_helper::get_inforef_itemnames();

        // Build the tree

        $inforef = new backup_nested_element('inforef');

        // For each item, conditionally, if there are already records, build element
        foreach ($items as $itemname) {
            if (backup_structure_dbops::annotations_exist($this->get_backupid(), $itemname)) {
                $elementroot = new backup_nested_element($itemname . 'ref');
                $element = new backup_nested_element($itemname, array(), array('id'));
                $inforef->add_child($elementroot);
                $elementroot->add_child($element);
                $element->set_source_sql("
                    SELECT itemid AS id
                     FROM {backup_ids_temp}
                    WHERE backupid = ?
                      AND itemname = ?",
                   array(backup::VAR_BACKUPID, backup_helper::is_sqlparam($itemname)));
            }
        }

        // We don't annotate anything there, but rely in the next step
        // (move_inforef_annotations_to_final) that will change all the
        // already saved 'inforref' entries to their 'final' annotations.
        return $inforef;
    }
}

/**
 * This step will get all the annotations already processed to inforef.xml file and
 * transform them into 'final' annotations.
 */
class move_inforef_annotations_to_final extends backup_execution_step {

    protected function define_execution() {

        // Items we want to include in the inforef file
        $items = backup_helper::get_inforef_itemnames();
        foreach ($items as $itemname) {
            // Delegate to dbops
            backup_structure_dbops::move_annotations_to_final($this->get_backupid(), $itemname);
        }
    }
}

/**
 * structure in charge of constructing the files.xml file with all the
 * annotated (final) files along the process. At, the same time, and
 * using one specialised nested_element, will copy them form moodle storage
 * to backup storage
 */
class backup_final_files_structure_step extends backup_structure_step {

    protected function define_structure() {

        // Define elements

        $files = new backup_nested_element('files');

        $file = new file_nested_element('file', array('id'), array(
            'contenthash', 'contextid', 'component', 'filearea', 'itemid',
            'filepath', 'filename', 'userid', 'filesize',
            'mimetype', 'status', 'timecreated', 'timemodified',
            'source', 'author', 'license', 'sortorder'));

        // Build the tree

        $files->add_child($file);

        // Define sources

        $file->set_source_sql("SELECT f.*
                                 FROM {files} f
                                 JOIN {backup_ids_temp} bi ON f.id = bi.itemid
                                WHERE bi.backupid = ?
                                  AND bi.itemname = 'filefinal'", array(backup::VAR_BACKUPID));

        return $files;
    }
}

/**
 * Structure step in charge of creating the main moodle_backup.xml file
 * where all the information related to the backup, settings, license and
 * other information needed on restore is added*/
class backup_main_structure_step extends backup_structure_step {

    protected function define_structure() {

        global $CFG;

        $info = array();

        $info['name'] = $this->get_setting_value('filename');
        $info['moodle_version'] = $CFG->version;
        $info['moodle_release'] = $CFG->release;
        $info['backup_version'] = $CFG->backup_version;
        $info['backup_release'] = $CFG->backup_release;
        $info['backup_date']    = time();
        $info['backup_uniqueid']= $this->get_backupid();
        $info['mnet_remoteusers']=backup_controller_dbops::backup_includes_mnet_remote_users($this->get_backupid());
        $info['original_wwwroot']=$CFG->wwwroot;
        $info['original_site_identifier_hash'] = md5(get_site_identifier());
        $info['original_course_id'] = $this->get_courseid();
        $originalcourseinfo = backup_controller_dbops::backup_get_original_course_info($this->get_courseid());
        $info['original_course_fullname']  = $originalcourseinfo->fullname;
        $info['original_course_shortname'] = $originalcourseinfo->shortname;
        $info['original_course_startdate'] = $originalcourseinfo->startdate;
        $info['original_course_contextid'] = get_context_instance(CONTEXT_COURSE, $this->get_courseid())->id;
        $info['original_system_contextid'] = get_context_instance(CONTEXT_SYSTEM)->id;

        // Get more information from controller
        list($dinfo, $cinfo, $sinfo) = backup_controller_dbops::get_moodle_backup_information($this->get_backupid());

        // Define elements

        $moodle_backup = new backup_nested_element('moodle_backup');

        $information = new backup_nested_element('information', null, array(
            'name', 'moodle_version', 'moodle_release', 'backup_version',
            'backup_release', 'backup_date', 'mnet_remoteusers', 'original_wwwroot',
            'original_site_identifier_hash', 'original_course_id',
            'original_course_fullname', 'original_course_shortname', 'original_course_startdate',
            'original_course_contextid', 'original_system_contextid'));

        $details = new backup_nested_element('details');

        $detail = new backup_nested_element('detail', array('backup_id'), array(
            'type', 'format', 'interactive', 'mode',
            'execution', 'executiontime'));

        $contents = new backup_nested_element('contents');

        $activities = new backup_nested_element('activities');

        $activity = new backup_nested_element('activity', null, array(
            'moduleid', 'sectionid', 'modulename', 'title',
            'directory'));

        $sections = new backup_nested_element('sections');

        $section = new backup_nested_element('section', null, array(
            'sectionid', 'title', 'directory'));

        $course = new backup_nested_element('course', null, array(
            'courseid', 'title', 'directory'));

        $settings = new backup_nested_element('settings');

        $setting = new backup_nested_element('setting', null, array(
            'level', 'section', 'activity', 'name', 'value'));

        // Build the tree

        $moodle_backup->add_child($information);

        $information->add_child($details);
        $details->add_child($detail);

        $information->add_child($contents);
        if (!empty($cinfo['activities'])) {
            $contents->add_child($activities);
            $activities->add_child($activity);
        }
        if (!empty($cinfo['sections'])) {
            $contents->add_child($sections);
            $sections->add_child($section);
        }
        if (!empty($cinfo['course'])) {
            $contents->add_child($course);
        }

        $information->add_child($settings);
        $settings->add_child($setting);


        // Set the sources

        $information->set_source_array(array((object)$info));

        $detail->set_source_array($dinfo);

        $activity->set_source_array($cinfo['activities']);

        $section->set_source_array($cinfo['sections']);

        $course->set_source_array($cinfo['course']);

        $setting->set_source_array($sinfo);

        // Prepare some information to be sent to main moodle_backup.xml file
        return $moodle_backup;
    }

}

/**
 * Execution step that will generate the final zip (.mbz) file with all the contents
 */
class backup_zip_contents extends backup_execution_step {

    protected function define_execution() {

        // Get basepath
        $basepath = $this->get_basepath();

        // Get the list of files in directory
        $filestemp = get_directory_list($basepath, '', false, true, true);
        $files = array();
        foreach ($filestemp as $file) { // Add zip paths and fs paths to all them
            $files[$file] = $basepath . '/' . $file;
        }

        // Add the log file if exists
        $logfilepath = $basepath . '.log';
        if (file_exists($logfilepath)) {
             $files['moodle_backup.log'] = $logfilepath;
        }

        // Calculate the zip fullpath (in OS temp area it's always backup.mbz)
        $zipfile = $basepath . '/backup.mbz';

        // Get the zip packer
        $zippacker = get_file_packer('application/zip');

        // Zip files
        $zippacker->archive_to_pathname($files, $zipfile);
    }
}

/**
 * This step will send the generated backup file to its final destination
 */
class backup_store_backup_file extends backup_execution_step {

    protected function define_execution() {

        // Get basepath
        $basepath = $this->get_basepath();

        // Calculate the zip fullpath (in OS temp area it's always backup.mbz)
        $zipfile = $basepath . '/backup.mbz';

        // Perform storage and return it (TODO: shouldn't be array but proper result object)
        return array('backup_destination' => backup_helper::store_backup_file($this->get_backupid(), $zipfile));
    }
}


/**
 * This step will search for all the activity (not calculations, categories nor aggregations) grade items
 * and put them to the backup_ids tables, to be used later as base to backup them
 */
class backup_activity_grade_items_to_ids extends backup_execution_step {

    protected function define_execution() {

        // Fetch all activity grade items
        if ($items = grade_item::fetch_all(array(
                         'itemtype' => 'mod', 'itemmodule' => $this->task->get_modulename(),
                         'iteminstance' => $this->task->get_activityid(), 'courseid' => $this->task->get_courseid()))) {
            // Annotate them in backup_ids
            foreach ($items as $item) {
                backup_structure_dbops::insert_backup_ids_record($this->get_backupid(), 'grade_item', $item->id);
            }
        }
    }
}

/**
 * This step will annotate all the groups and groupings belonging to the course
 */
class backup_annotate_course_groups_and_groupings extends backup_execution_step {

    protected function define_execution() {
        global $DB;

        // Get all the course groups
        if ($groups = $DB->get_records('groups', array(
                'courseid' => $this->task->get_courseid()))) {
            foreach ($groups as $group) {
                backup_structure_dbops::insert_backup_ids_record($this->get_backupid(), 'group', $group->id);
            }
        }

        // Get all the course groupings
        if ($groupings = $DB->get_records('groupings', array(
                'courseid' => $this->task->get_courseid()))) {
            foreach ($groupings as $grouping) {
                backup_structure_dbops::insert_backup_ids_record($this->get_backupid(), 'grouping', $grouping->id);
            }
        }
    }
}

/**
 * This step will annotate all the groups belonging to already annotated groupings
 */
class backup_annotate_groups_from_groupings extends backup_execution_step {

    protected function define_execution() {
        global $DB;

        // Fetch all the annotated groupings
        if ($groupings = $DB->get_records('backup_ids_temp', array(
                'backupid' => $this->get_backupid(), 'itemname' => 'grouping'))) {
            foreach ($groupings as $grouping) {
                if ($groups = $DB->get_records('groupings_groups', array(
                        'groupingid' => $grouping->itemid))) {
                    foreach ($groups as $group) {
                        backup_structure_dbops::insert_backup_ids_record($this->get_backupid(), 'group', $group->groupid);
                    }
                }
            }
        }
    }
}

/**
 * This step will annotate all the scales belonging to already annotated outcomes
 */
class backup_annotate_scales_from_outcomes extends backup_execution_step {

    protected function define_execution() {
        global $DB;

        // Fetch all the annotated outcomes
        if ($outcomes = $DB->get_records('backup_ids_temp', array(
                'backupid' => $this->get_backupid(), 'itemname' => 'outcome'))) {
            foreach ($outcomes as $outcome) {
                if ($scale = $DB->get_record('grade_outcomes', array(
                        'id' => $outcome->itemid))) {
                    // Annotate as scalefinal because it's > 0
                    backup_structure_dbops::insert_backup_ids_record($this->get_backupid(), 'scalefinal', $scale->scaleid);
                }
            }
        }
    }
}

/**
 * This step will generate all the file annotations for the already
 * annotated (final) question_categories. It calculates the different
 * contexts that are being backup and, annotates all the files
 * on every context belonging to the "question" component. As far as
 * we are always including *complete* question banks it is safe and
 * optimal to do that in this (one pass) way
 */
class backup_annotate_all_question_files extends backup_execution_step {

    protected function define_execution() {
        global $DB;

        // Get all the different contexts for the final question_categories
        // annotated along the whole backup
        $rs = $DB->get_recordset_sql("SELECT DISTINCT qc.contextid
                                        FROM {question_categories} qc
                                        JOIN {backup_ids_temp} bi ON bi.itemid = qc.id
                                       WHERE bi.backupid = ?
                                         AND bi.itemname = 'question_categoryfinal'", array($this->get_backupid()));
        // To know about qtype specific components/fileareas
        $components = backup_qtype_plugin::get_components_and_fileareas();
        // Let's loop
        foreach($rs as $record) {
            // We don't need to specify filearea nor itemid as far as by
            // component and context it's enough to annotate the whole bank files
            // This backups "questiontext", "generalfeedback" and "answerfeedback" fileareas (all them
            // belonging to the "question" component
            backup_structure_dbops::annotate_files($this->get_backupid(), $record->contextid, 'question', null, null);
            // Again, it is enough to pick files only by context and component
            // Do it for qtype specific components
            foreach ($components as $component => $fileareas) {
                backup_structure_dbops::annotate_files($this->get_backupid(), $record->contextid, $component, null, null);
            }
        }
        $rs->close();
    }
}

/**
 * structure step in charge of constructing the questions.xml file for all the
 * question categories and questions required by the backup
 * and letters related to one activity
 */
class backup_questions_structure_step extends backup_structure_step {

    protected function define_structure() {

        // Define each element separated

        $qcategories = new backup_nested_element('question_categories');

        $qcategory = new backup_nested_element('question_category', array('id'), array(
            'name', 'contextid', 'contextlevel', 'contextinstanceid',
            'info', 'infoformat', 'stamp', 'parent',
            'sortorder'));

        $questions = new backup_nested_element('questions');

        $question = new backup_nested_element('question', array('id'), array(
            'parent', 'name', 'questiontext', 'questiontextformat',
            'generalfeedback', 'generalfeedbackformat', 'defaultmark', 'penalty',
            'qtype', 'length', 'stamp', 'version',
            'hidden', 'timecreated', 'timemodified', 'createdby', 'modifiedby'));

        // attach qtype plugin structure to $question element, only one allowed
        $this->add_plugin_structure('qtype', $question, false);

        $qhints = new backup_nested_element('question_hints');

        $qhint = new backup_nested_element('question_hint', array('id'), array(
            'hint', 'hintformat', 'shownumcorrect', 'clearwrong', 'options'));

        // Build the tree

        $qcategories->add_child($qcategory);
        $qcategory->add_child($questions);
        $questions->add_child($question);
        $question->add_child($qhints);
        $qhints->add_child($qhint);

        // Define the sources

        $qcategory->set_source_sql("
            SELECT gc.*, contextlevel, instanceid AS contextinstanceid
              FROM {question_categories} gc
              JOIN {backup_ids_temp} bi ON bi.itemid = gc.id
              JOIN {context} co ON co.id = gc.contextid
             WHERE bi.backupid = ?
               AND bi.itemname = 'question_categoryfinal'", array(backup::VAR_BACKUPID));

        $question->set_source_table('question', array('category' => backup::VAR_PARENTID));

        $qhint->set_source_sql('
                SELECT *
                FROM {question_hints}
                WHERE questionid = :questionid
                ORDER BY id',
                array('questionid' => backup::VAR_PARENTID));

        // don't need to annotate ids nor files
        // (already done by {@link backup_annotate_all_question_files}

        return $qcategories;
    }
}



/**
 * This step will generate all the file  annotations for the already
 * annotated (final) users. Need to do this here because each user
 * has its own context and structure tasks only are able to handle
 * one context. Also, this step will guarantee that every user has
 * its context created (req for other steps)
 */
class backup_annotate_all_user_files extends backup_execution_step {

    protected function define_execution() {
        global $DB;

        // List of fileareas we are going to annotate
        $fileareas = array('profile', 'icon');

        if ($this->get_setting_value('user_files')) { // private files only if enabled in settings
            $fileareas[] = 'private';
        }

        // Fetch all annotated (final) users
        $rs = $DB->get_recordset('backup_ids_temp', array(
            'backupid' => $this->get_backupid(), 'itemname' => 'userfinal'));
        foreach ($rs as $record) {
            $userid = $record->itemid;
            $userctxid = get_context_instance(CONTEXT_USER, $userid)->id;
            // Proceed with every user filearea
            foreach ($fileareas as $filearea) {
                // We don't need to specify itemid ($userid - 5th param) as far as by
                // context we can get all the associated files. See MDL-22092
                backup_structure_dbops::annotate_files($this->get_backupid(), $userctxid, 'user', $filearea, null);
            }
        }
        $rs->close();
    }
}

/**
 * structure step in charge of constructing the grades.xml file for all the grade items
 * and letters related to one activity
 */
class backup_activity_grades_structure_step extends backup_structure_step {

    protected function define_structure() {

        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated

        $book = new backup_nested_element('activity_gradebook');

        $items = new backup_nested_element('grade_items');

        $item = new backup_nested_element('grade_item', array('id'), array(
            'categoryid', 'itemname', 'itemtype', 'itemmodule',
            'iteminstance', 'itemnumber', 'iteminfo', 'idnumber',
            'calculation', 'gradetype', 'grademax', 'grademin',
            'scaleid', 'outcomeid', 'gradepass', 'multfactor',
            'plusfactor', 'aggregationcoef', 'sortorder', 'display',
            'decimals', 'hidden', 'locked', 'locktime',
            'needsupdate', 'timecreated', 'timemodified'));

        $grades = new backup_nested_element('grade_grades');

        $grade = new backup_nested_element('grade_grade', array('id'), array(
            'userid', 'rawgrade', 'rawgrademax', 'rawgrademin',
            'rawscaleid', 'usermodified', 'finalgrade', 'hidden',
            'locked', 'locktime', 'exported', 'overridden',
            'excluded', 'feedback', 'feedbackformat', 'information',
            'informationformat', 'timecreated', 'timemodified'));

        $letters = new backup_nested_element('grade_letters');

        $letter = new backup_nested_element('grade_letter', 'id', array(
            'lowerboundary', 'letter'));

        // Build the tree

        $book->add_child($items);
        $items->add_child($item);

        $item->add_child($grades);
        $grades->add_child($grade);

        $book->add_child($letters);
        $letters->add_child($letter);

        // Define sources

        $item->set_source_sql("SELECT gi.*
                               FROM {grade_items} gi
                               JOIN {backup_ids_temp} bi ON gi.id = bi.itemid
                               WHERE bi.backupid = ?
                               AND bi.itemname = 'grade_item'", array(backup::VAR_BACKUPID));

        // This only happens if we are including user info
        if ($userinfo) {
            $grade->set_source_table('grade_grades', array('itemid' => backup::VAR_PARENTID));
        }

        $letter->set_source_table('grade_letters', array('contextid' => backup::VAR_CONTEXTID));

        // Annotations

        $item->annotate_ids('scalefinal', 'scaleid'); // Straight as scalefinal because it's > 0
        $item->annotate_ids('outcome', 'outcomeid');

        $grade->annotate_ids('user', 'userid');
        $grade->annotate_ids('user', 'usermodified');

        // Return the root element (book)

        return $book;
    }
}

/**
 * Backups up the course completion information for the course.
 */
class backup_course_completion_structure_step extends backup_structure_step {

    protected function execute_condition() {
        // Check that all activities have been included
        if ($this->task->is_excluding_activities()) {
            return false;
        }
        return true;
    }

    /**
     * The structure of the course completion backup
     *
     * @return backup_nested_element
     */
    protected function define_structure() {

        // To know if we are including user completion info
        $userinfo = $this->get_setting_value('userscompletion');

        $cc = new backup_nested_element('course_completion');

        $criteria = new backup_nested_element('course_completion_criteria', array('id'), array(
            'course','criteriatype', 'module', 'moduleinstance', 'courseinstanceshortname', 'enrolperiod', 'timeend', 'gradepass', 'role'
        ));

        $criteriacompletions = new backup_nested_element('course_completion_crit_completions');

        $criteriacomplete = new backup_nested_element('course_completion_crit_compl', array('id'), array(
            'criteriaid', 'userid','gradefinal','unenrolled','deleted','timecompleted'
        ));

        $coursecompletions = new backup_nested_element('course_completions', array('id'), array(
            'userid', 'course', 'deleted', 'timenotified', 'timeenrolled','timestarted','timecompleted','reaggregate'
        ));

        $notify = new backup_nested_element('course_completion_notify', array('id'), array(
            'course','role','message','timesent'
        ));

        $aggregatemethod = new backup_nested_element('course_completion_aggr_methd', array('id'), array(
            'course','criteriatype','method','value'
        ));

        $cc->add_child($criteria);
            $criteria->add_child($criteriacompletions);
                $criteriacompletions->add_child($criteriacomplete);
        $cc->add_child($coursecompletions);
        $cc->add_child($notify);
        $cc->add_child($aggregatemethod);

        // We need to get the courseinstances shortname rather than an ID for restore
        $criteria->set_source_sql("SELECT ccc.*, c.shortname AS courseinstanceshortname
                                   FROM {course_completion_criteria} ccc
                                   LEFT JOIN {course} c ON c.id = ccc.courseinstance
                                   WHERE ccc.course = ?", array(backup::VAR_COURSEID));


        $notify->set_source_table('course_completion_notify', array('course' => backup::VAR_COURSEID));
        $aggregatemethod->set_source_table('course_completion_aggr_methd', array('course' => backup::VAR_COURSEID));

        if ($userinfo) {
            $criteriacomplete->set_source_table('course_completion_crit_compl', array('criteriaid' => backup::VAR_PARENTID));
            $coursecompletions->set_source_table('course_completions', array('course' => backup::VAR_COURSEID));
        }

        $criteria->annotate_ids('role', 'role');
        $criteriacomplete->annotate_ids('user', 'userid');
        $coursecompletions->annotate_ids('user', 'userid');
        $notify->annotate_ids('role', 'role');

        return $cc;

    }
}
