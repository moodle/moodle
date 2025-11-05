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
 * Desc.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers\local\callbacks;

use coding_exception;
use dml_exception;
use moodle_exception;
use tool_mergeusers\hook\after_merged_all_tables;

/**
 * Callback that regrades activities where any of the merged users were involved.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class regrading_after_merged_callback {
    /**
     * Regrades activities where any of the merged users were involved.
     *
     * When any of the matching activities does not exist or its course module,
     * a proper exception is thrown to inform about that database inconsistency.
     *
     * @param after_merged_all_tables $hook
     * @return void
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public static function regrade(after_merged_all_tables $hook) {
        global $DB, $CFG;
        require_once($CFG->libdir . '/gradelib.php');

        $sql = "SELECT DISTINCT gi.id, gi.iteminstance, gi.itemmodule, gi.courseid
                FROM {grade_grades} gg
                INNER JOIN {grade_items} gi on gg.itemid = gi.id
                WHERE itemtype = :itemtype AND (gg.userid = :toid OR gg.userid = :fromid)";

        $iteminstances = $DB->get_records_sql($sql, ['itemtype' => 'mod', 'toid' => $hook->toid, 'fromid' => $hook->fromid]);

        foreach ($iteminstances as $iteminstance) {
            if (!$activity = $DB->get_record($iteminstance->itemmodule, ['id' => $iteminstance->iteminstance])) {
                throw new moodle_exception(
                    'exception:nomoduleinstance',
                    'tool_mergeusers',
                    '',
                    [
                        'module' => $iteminstance->itemmodule,
                        'activityid' => $iteminstance->iteminstance,
                    ]
                );
            }
            if (!$cm = get_coursemodule_from_instance($iteminstance->itemmodule, $activity->id, $iteminstance->courseid)) {
                throw new moodle_exception(
                    'exception:nocoursemodule',
                    'tool_mergeusers',
                    '',
                    [
                        'module' => $iteminstance->itemmodule,
                        'activityid' => $activity->id,
                        'courseid' => $iteminstance->courseid,
                    ],
                );
            }

            $activity->modname    = $iteminstance->itemmodule;
            $activity->cmidnumber = $cm->idnumber;

            ob_start();
            grade_update_mod_grades($activity, $hook->toid);
            $regradeoutput = ob_get_clean();
            $hook->add_log(sprintf(
                'Regraded grade item with id "%s" from module type "%s" and instance "%s" from course "%s".',
                $iteminstance->id,
                $iteminstance->itemmodule,
                $iteminstance->iteminstance,
                $iteminstance->courseid,
            ));
            if (!empty($regradeoutput)) {
                // Convert potential HTML returned to HTML entities to prevent formatting errors on merge logs.
                $hook->add_log(htmlspecialchars($regradeoutput));
            }
        }
    }
}
