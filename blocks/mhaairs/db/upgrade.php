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
 * This file keeps track of upgrades to the mhaairs block
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
 * @since Moodle 2.8
 * @package    block_mhaairs
 * @copyright  2016 Itamar Tzadok
 * @copyright  2013 Moodlerooms inc.
 * @author     Teresa Hardy <thardy@moodlerooms.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/upgradelib.php');

/**
 * Upgrade the mhaairs block
 * @param int $oldversion
 * @param object $block
 */
function xmldb_block_mhaairs_upgrade($oldversion, $block) {
    global $DB;

    $blockname = 'mhaairs';

    $savepoint = 2011091314;
    if ($oldversion < $savepoint) {
        // Check for multiple instances of mhaairs blocks in all courses.
        $blockname = 'mhaairs';
        $tbl = 'block_instances';
        $sql = 'SELECT distinct parentcontextid FROM {block_instances} WHERE blockname = :blockname';
        $instances = $DB->get_records_sql($sql, array('blockname' => $blockname));
        if (!empty($instances)) {
            $deletearr = array();
            foreach ($instances as $instance) {
                $params = array('parentcontextid' => $instance->parentcontextid, 'blockname' => $blockname);
                $recs = $DB->get_records($tbl, $params, '', 'id');

                $inst = 1;  // Helps mark first instance, which we will always keep.

                foreach ($recs as $record) {
                    $id = $record->id;
                    $newvalue = "";  // Set configdata to empty string.

                    if ($inst == 1) {
                        $DB->set_field($tbl, 'configdata', $newvalue, array('blockname' => $blockname));
                        $inst++;
                    } else {
                        // Delete list.
                        $deletearr[] = $id;
                    }
                }
                try {
                    try {
                        $transaction = $DB->start_delegated_transaction();
                        $DB->delete_records_list($tbl, 'id', $deletearr);
                        $transaction->allow_commit();
                        upgrade_block_savepoint(true, $savepoint, $blockname);
                    } catch (Exception $e) {
                        if (!empty($transaction) && !$transaction->is_disposed()) {
                            $transaction->rollback($e);
                        }
                    }
                } catch (Exception $e) {
                    return false;
                }
            }
        }
    }

    $savepoint = 2015111603.02;
    if ($oldversion < $savepoint) {
        block_mhaairs_remove_locktype_setting($block);

        upgrade_block_savepoint(true, $savepoint, $blockname);
    }

    return true;
}
