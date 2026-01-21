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

namespace mod_subsection\task;

use core\task\adhoc_task;
use core_courseformat\formatactions;

/**
 * A task to migrate to text and media and remove existing descriptions from subsection instances.
 *
 * NOTE:
 *  - This task requires that both the label and subsection modules are enabled.
 *  - It processes subsections in batches of 100 to reduce server overload.
 *  - This task will be removed in Moodle 7.0. By then, the remaining descriptions will be removed.
 *
 * @package    mod_subsection
 * @copyright  2025 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class migrate_subsection_descriptions_task extends adhoc_task {
    /**
     * Execute the task.
     */
    public function execute(): void {
        global $CFG, $DB;

        $enabledplugins = \core\plugininfo\mod::get_enabled_plugins();
        if (!isset($enabledplugins['label']) || !isset($enabledplugins['subsection'])) {
            // The label or subsection module is not enabled, nothing to do.
            mtrace('Text and media area or Subsection module is not enabled. Skipping migration task.');
            return;
        }
        require_once($CFG->dirroot . '/course/lib.php');

        // Process subsections in batches to reduce server overload.
        $migratedcount = 0;
        $subsections = $DB->get_recordset_select(
            table: 'course_sections',
            select: 'component = :component AND summary != :empty',
            params: ['component' => 'mod_subsection', 'empty' => ''],
            limitnum: 100,
        );
        $transaction = $DB->start_delegated_transaction();
        foreach ($subsections as $subsection) {
            // Create a label with the subsection summary as intro.
            $label = [
                'modulename' => 'label',
                'course' => $subsection->course,
                'section' => $subsection->section,
                'visible' => 1,
                'introeditor' => [
                    'text' => $subsection->summary,
                    'format' => $subsection->summaryformat,
                    'itemid' => 0,
                ],
            ];
            $label = \create_module((object) $label);

            // Move the files from the subsection summary to the label intro.
            $fs = get_file_storage();
            $files = $fs->get_area_files(
                contextid: \context_course::instance($subsection->course)->id,
                component: 'course',
                filearea: 'section',
                itemid: $subsection->id,
            );
            foreach ($files as $file) {
                $filerecord = [
                    'contextid' => \context_module::instance($label->coursemodule)->id,
                    'component' => 'mod_label',
                    'filearea' => 'intro',
                    'itemid' => 0,
                    'timemodified' => time(),
                ];
                if ($fs->create_file_from_storedfile($filerecord, $file)) {
                    // Remove the file from the subsection area.
                    $file->delete();
                }
            }

            // Move the label at the beginning of the subsection.
            $section = get_fast_modinfo($subsection->course)->get_section_info($subsection->section);
            $beforemod = explode(',', trim($section->sequence))[0] ?? null;
            if ($beforemod) {
                formatactions::cm($subsection->course)->move_before($label->coursemodule, $beforemod);
            }

            // Clear the summary field.
            $DB->set_field(
                table: 'course_sections',
                newfield: 'summary',
                newvalue: '',
                conditions: ['id' => $subsection->id],
            );
            $migratedcount++;
        }
        $transaction->allow_commit();
        if ($migratedcount > 0) {
            mtrace('Subsection descriptions migration task completed. Total migrated subsections: ' . $migratedcount);
        } else {
            mtrace('No subsection descriptions found to migrate.');
        }
        $subsections->close();

        $pendingcount = $DB->count_records_select(
            table: 'course_sections',
            select: 'component = :component AND summary != :empty',
            params: ['component' => 'mod_subsection', 'empty' => ''],
        );
        if ($pendingcount > 0) {
            $task = new self();
            \core\task\manager::queue_adhoc_task($task);
            mtrace('Subsection descriptions migration task pending subsections: ' . $pendingcount . '. Scheduled new ad-hoc task.');
        }
    }
}
