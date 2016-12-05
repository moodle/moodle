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
 * This file keeps track of upgrades to the quiz_results block
 *
 * Sometimes, changes between versions involve alterations to database structures
 * and other major things that may break installations.
 *
 * The upgrade function in this file will attempt to perform all the necessary
 * actions to upgrade your older installation to the current version.
 *
 * If there's something it cannot do itself, it will tell you what you need to do.
 *
 * The commands in here will all be database-neutral, using the methods of
 * database_manager class
 *
 * Please do not forget to use upgrade_set_timeout()
 * before any action that may take longer time to finish.
 *
 * @since Moodle 2.9
 * @package block_quiz_results
 * @copyright 2015 Stephen Bourget
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade the quiz_results block
 * @param int $oldversion
 * @param object $block
 */
function xmldb_block_quiz_results_upgrade($oldversion, $block) {
    global $DB, $CFG;

    if ($oldversion < 2015022200) {
        // Only migrate if the block_activity_results is installed.
        if (is_dir($CFG->dirroot . '/blocks/activity_results')) {

            // Migrate all instances of block_quiz_results to block_activity_results.
            $records = $DB->get_records('block_instances', array('blockname' => 'quiz_results'));
            foreach ($records as $record) {
                $configdata = '';

                // The block was configured.
                if (!empty($record->configdata)) {

                    $config = unserialize(base64_decode($record->configdata));
                    $config->activityparent = 'quiz';
                    $config->activityparentid = isset($config->quizid) ? $config->quizid : 0;
                    $config->gradeformat = isset($config->gradeformat) ? $config->gradeformat : 1;

                    // Set the decimal valuue as appropriate.
                    if ($config->gradeformat == 1) {
                        // This block is using percentages, do not display any decimal places.
                        $config->decimalpoints = 0;
                    } else {
                        // Get the decimal value from the corresponding quiz.
                        $config->decimalpoints = $DB->get_field('quiz', 'decimalpoints', array('id' => $config->activityparentid));
                    }

                    // Get the grade_items record to set the activitygradeitemid.
                    $info = $DB->get_record('grade_items',
                            array('iteminstance' => $config->activityparentid, 'itemmodule' => $config->activityparent));
                    $config->activitygradeitemid = 0;
                    if ($info) {
                        $config->activitygradeitemid = $info->id;
                    }

                    unset($config->quizid);
                    $configdata = base64_encode(serialize($config));
                }

                // Save the new configuration and update the record.
                $record->configdata = $configdata;
                $record->blockname = 'activity_results';
                $DB->update_record('block_instances', $record);
            }

            // Disable the Quiz_results block.
            if ($block = $DB->get_record("block", array("name" => "quiz_results"))) {
                $DB->set_field("block", "visible", "0", array("id" => $block->id));
            }

        }
        upgrade_block_savepoint(true, 2015022200, 'quiz_results');
    }

    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.9.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v3.0.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v3.1.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.2.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}