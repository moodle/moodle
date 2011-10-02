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
 * course task that provides all the properties and common steps to be performed
 * when one course is being backup
 *
 * TODO: Finish phpdocs
 */
class backup_course_task extends backup_task {

    protected $courseid;
    protected $contextid;

    /**
     * Constructor - instantiates one object of this class
     */
    public function __construct($name, $courseid, $plan = null) {

        $this->courseid   = $courseid;
        $this->contextid  = get_context_instance(CONTEXT_COURSE, $this->courseid)->id;

        parent::__construct($name, $plan);
    }

    public function get_contextid() {
        return $this->contextid;
    }

    /**
     * Course tasks have their own directory to write files
     */
    public function get_taskbasepath() {

        return $this->get_basepath() . '/course';
    }

    /**
     * Create all the steps that will be part of this task
     */
    public function build() {

        // Add some extra settings that related processors are going to need
        $this->add_setting(new backup_activity_generic_setting(backup::VAR_COURSEID, base_setting::IS_INTEGER, $this->get_courseid()));
        $this->add_setting(new backup_activity_generic_setting(backup::VAR_CONTEXTID, base_setting::IS_INTEGER, $this->contextid));

        // Create the course directory
        $this->add_step(new create_taskbasepath_directory('create_course_directory'));

        // Create the course.xml file with course & category information
        // annotating some bits, tags and module restrictions
        $this->add_step(new backup_course_structure_step('course_info', 'course.xml'));

        // Generate the enrolment file (conditionally, prevent it in any IMPORT/HUB operation)
        if ($this->plan->get_mode() != backup::MODE_IMPORT && $this->plan->get_mode() != backup::MODE_HUB) {
            $this->add_step(new backup_enrolments_structure_step('course_enrolments', 'enrolments.xml'));
        }

        // Annotate all the groups and groupings belonging to the course
        $this->add_step(new backup_annotate_course_groups_and_groupings('annotate_course_groups'));

        // Annotate the groups used in already annotated groupings (note this may be
        // unnecessary now that we are annotating all the course groups and groupings in the
        // step above. But we keep it working in case we decide, someday, to introduce one
        // setting to transform the step above into an optional one. This is here to support
        // course->defaultgroupingid
        $this->add_step(new backup_annotate_groups_from_groupings('annotate_groups_from_groupings'));

        // Annotate the question_categories belonging to the course context
        $this->add_step(new backup_calculate_question_categories('course_question_categories'));

        // Generate the roles file (optionally role assignments and always role overrides)
        $this->add_step(new backup_roles_structure_step('course_roles', 'roles.xml'));

        // Generate the filter file (conditionally)
        if ($this->get_setting_value('filters')) {
            $this->add_step(new backup_filters_structure_step('course_filters', 'filters.xml'));
        }

        // Generate the comments file (conditionally)
        if ($this->get_setting_value('comments')) {
            $this->add_step(new backup_comments_structure_step('course_comments', 'comments.xml'));
        }

        // Generate the logs file (conditionally)
        if ($this->get_setting_value('logs')) {
            $this->add_step(new backup_course_logs_structure_step('course_logs', 'logs.xml'));
        }

        // Generate the inforef file (must be after ALL steps gathering annotations of ANY type)
        $this->add_step(new backup_inforef_structure_step('course', 'inforef.xml'));

        // Migrate the already exported inforef entries to final ones
        $this->add_step(new move_inforef_annotations_to_final('migrate_inforef'));

        // At the end, mark it as built
        $this->built = true;
    }

    /**
     * Code the transformations to perform in the course in
     * order to get transportable (encoded) links
     */
    static public function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot, '/');

        // Link to the course main page (it also covers "&topic=xx" and "&week=xx"
        // because they don't become transformed (section number) in backup/restore
        $search = '/(' . $base . '\/course\/view.php\?id\=)([0-9]+)/';
        $content= preg_replace($search, '$@COURSEVIEWBYID*$2@$', $content);

        return $content;
    }

// Protected API starts here

    /**
     * Define the common setting that any backup section will have
     */
    protected function define_settings() {

        // Nothing to add, sections doesn't have common settings (for now)

    }
}
