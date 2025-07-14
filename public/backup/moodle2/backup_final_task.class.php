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
 * Defines backup_final_task class
 *
 * @package     core_backup
 * @subpackage  moodle2
 * @category    backup
 * @copyright   2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Final task that provides all the final steps necessary in order to finish one
 * backup (mainly gathering references and creating the main xml) apart from
 * some final cleaning
 *
 * TODO: Finish phpdocs
 */
class backup_final_task extends backup_task {

    /**
     * Create all the steps that will be part of this task
     */
    public function build() {
        global $CFG;

        // Set the backup::VAR_CONTEXTID setting to course context as far as next steps require that
        $coursectxid = context_course::instance($this->get_courseid())->id;
        $this->add_setting(new backup_activity_generic_setting(backup::VAR_CONTEXTID, base_setting::IS_INTEGER, $coursectxid));

        // Set the backup::VAR_COURSEID setting to course, we'll need that in some steps
        $courseid = $this->get_courseid();
        $this->add_setting(new backup_activity_generic_setting(backup::VAR_COURSEID, base_setting::IS_INTEGER, $courseid));

        // Generate the groups file with the final annotated groups and groupings
        // including membership based on setting
        $this->add_step(new backup_groups_structure_step('groups', 'groups.xml'));

        // Generate the questions file with the final annotated question_categories
        $this->add_step(new backup_questions_structure_step('questions', 'questions.xml'));

        // Annotate all the question files for the already annotated question
        // categories (this is performed here and not in the structure step because
        // it involves multiple contexts and as far as we are always backup-ing
        // complete question banks we don't need to restrict at all and can be
        // done in a single pass
        $this->add_step(new backup_annotate_all_question_files('question_files'));

        // Annotate all the user files (conditionally) (profile and icon files)
        // Because each user has its own context, we need a separate/specialised step here
        // This step also ensures that the contexts for all the users exist, so next
        // step can be safely executed (join between users and contexts)
        // Not executed if backup is without users of anonymized
        if (($this->get_setting_value('users') || !empty($this->get_kept_roles())) && !$this->get_setting_value('anonymize')) {
            $this->add_step(new backup_annotate_all_user_files('user_files'));
        }

        // Generate the users file (conditionally) with the final annotated users
        // including custom profile fields, preferences, tags, role assignments and
        // overrides
        if ($this->get_setting_value('users') || !empty($this->get_kept_roles())) {
            $this->add_step(new backup_users_structure_step('users', 'users.xml'));
        }

        // Generate the top roles file with all the final annotated roles
        // that have been detected along the whole process. It's just
        // the list of role definitions (no assignments nor permissions)
        $this->add_step(new backup_final_roles_structure_step('roleslist', 'roles.xml'));

        // Generate the gradebook file with categories and course grade items. Do it conditionally, using
        // execute_condition() so only will be excuted if ALL module grade_items in course have been exported
        $this->add_step(new backup_gradebook_structure_step('course_gradebook','gradebook.xml'));

        // Generate the grade history file, conditionally.
        $this->add_step(new backup_grade_history_structure_step('course_grade_history','grade_history.xml'));

        // Generate the course completion
        $this->add_step(new backup_course_completion_structure_step('course_completion', 'completion.xml'));

        // Conditionally generate the badges file.
        if ($this->get_setting_value('badges')) {
            $this->add_step(new backup_badges_structure_step('course_badges', 'badges.xml'));
        }

        // Generate the scales file with all the (final) annotated scales
        $this->add_step(new backup_final_scales_structure_step('scaleslist', 'scales.xml'));

        // Generate the outcomes file with all the (final) annotated outcomes
        $this->add_step(new backup_final_outcomes_structure_step('outcomeslist', 'outcomes.xml'));

        // Migrate the pending annotations to final (prev steps may have added some files)
        // This must be executed before backup files
        $this->add_step(new move_inforef_annotations_to_final('migrate_inforef'));

        // Generate the files.xml file with all the (final) annotated files. At the same
        // time copy all the files from moodle storage to backup storage (uses custom
        // backup_nested_element for that)
        $this->add_step(new backup_final_files_structure_step('fileslist', 'files.xml'));

        // Write the main moodle_backup.xml file, with all the information related
        // to the backup, settings, license, versions and other useful information
        $this->add_step(new backup_main_structure_step('mainfile', 'moodle_backup.xml'));

        require_once($CFG->dirroot . '/backup/util/helper/convert_helper.class.php');

        // Look for converter steps only in type course and mode general backup operations.
        $conversion = false;
        if ($this->plan->get_type() == backup::TYPE_1COURSE and $this->plan->get_mode() == backup::MODE_GENERAL) {
            $converters = convert_helper::available_converters(false);
            foreach ($converters as $value) {
                if ($this->get_setting_value($value)) {
                    // Zip class.
                    $zip_contents      = "{$value}_zip_contents";
                    $store_backup_file = "{$value}_store_backup_file";
                    $convert           = "{$value}_backup_convert";

                    $this->add_step(new $convert("package_convert_{$value}"));
                    $this->add_step(new $zip_contents("zip_contents_{$value}"));
                    $this->add_step(new $store_backup_file("save_backupfile_{$value}"));
                    if (!$conversion) {
                        $conversion = true;
                    }
                }
            }
        }

        // On backup::MODE_IMPORT, we don't have to zip nor store the the file, skip these steps
        if (($this->plan->get_mode() != backup::MODE_IMPORT) && !$conversion) {
            // Generate the zip file (mbz extension)
            $this->add_step(new backup_zip_contents('zip_contents'));

            // Copy the generated zip (.mbz) file to final destination
            $this->add_step(new backup_store_backup_file('save_backupfile'));
        }

        // Clean the temp dir (conditionally) and drop temp tables
        $cleanstep = new drop_and_clean_temp_stuff('drop_and_clean_temp_stuff');
        // Decide about to delete the temp dir (based on backup::MODE_IMPORT)
        $cleanstep->skip_cleaning_temp_dir($this->plan->get_mode() == backup::MODE_IMPORT);
        $this->add_step($cleanstep);

        $this->built = true;
    }

    public function get_weight() {
        // The final task takes ages, so give it 20 times the weight of a normal task.
        return 20;
    }

// Protected API starts here

    /**
     * Define the common setting that any backup type will have
     */
    protected function define_settings() {
        // This task has not settings (could have them, like destination or so in the future, let's see)
    }
}
