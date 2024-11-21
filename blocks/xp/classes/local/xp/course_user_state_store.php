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
 * User state course store.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\xp;

use context_helper;
use moodle_database;
use stdClass;
use block_xp\local\logger\collection_logger_with_group_reset;
use block_xp\local\logger\collection_logger_with_id_reset;
use block_xp\local\logger\reason_collection_logger;
use block_xp\local\observer\level_up_state_store_observer;
use block_xp\local\observer\points_increased_state_store_observer;
use block_xp\local\reason\reason;
use block_xp\local\utils\user_utils;

/**
 * User state course store.
 *
 * This is a repository of XP of each user.
 *
 * It also used to store the level of each user in the 'lvl' column, for ordering purposes,
 * but no longer does. When levels_info were changed, the levels had to be updated.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_user_state_store implements course_state_store,
        state_store_with_reason, state_store_with_delete {

    /** @var moodle_database The database. */
    protected $db;
    /** @var int The course ID. */
    protected $courseid;
    /** @var levels_info The levels info. */
    protected $levelsinfo;
    /** @var string The DB table. */
    protected $table = 'block_xp';
    /** @var reason_collection_logger The logger. */
    protected $logger;
    /** @var level_up_state_store_observer The observer. */
    protected $observer;
    /** @var points_increased_state_store_observer The observer. */
    protected $pointsobserver;

    /**
     * Constructor.
     *
     * @param moodle_database $db The DB.
     * @param levels_info $levelsinfo The levels info.
     * @param int $courseid The course ID.
     * @param reason_collection_logger $logger The reason logger.
     * @param level_up_state_store_observer $observer The observer.
     * @param points_increased_state_store_observer $pointsobserver The observer.
     */
    public function __construct(moodle_database $db, levels_info $levelsinfo, $courseid,
            reason_collection_logger $logger, level_up_state_store_observer $observer = null,
            points_increased_state_store_observer $pointsobserver = null) {
        $this->db = $db;
        $this->levelsinfo = $levelsinfo;
        $this->courseid = $courseid;
        $this->logger = $logger;
        $this->observer = $observer;
        $this->pointsobserver = $pointsobserver;
    }

    /**
     * Get a state.
     *
     * @param int $id The object ID.
     * @return state
     */
    public function get_state($id) {
        $userfields = user_utils::picture_fields('u', 'userid');
        $contextfields = context_helper::get_preload_record_columns_sql('ctx');

        $sql = "SELECT u.id, x.userid, x.xp, $userfields, $contextfields
                  FROM {user} u
                  JOIN {context} ctx
                    ON ctx.instanceid = u.id
                   AND ctx.contextlevel = :contextlevel
             LEFT JOIN {{$this->table}} x
                    ON x.userid = u.id
                   AND x.courseid = :courseid
                 WHERE u.id = :userid";

        $params = [
            'contextlevel' => CONTEXT_USER,
            'courseid' => $this->courseid,
            'userid' => $id,
        ];

        return $this->make_state_from_record($this->db->get_record_sql($sql, $params, MUST_EXIST));
    }

    /**
     * Delete a state.
     *
     * @param int $id The object ID.
     * @return void
     */
    public function delete($id) {
        $params = [];
        $params['userid'] = $id;
        $params['courseid'] = $this->courseid;
        $this->db->delete_records($this->table, $params);

        if ($this->logger instanceof collection_logger_with_id_reset) {
            $this->logger->reset_by_id($id);
        }
    }

    /**
     * Return whether the entry exists.
     *
     * @param int $id The receiver.
     * @return stdClass|false
     */
    protected function exists($id) {
        $params = [];
        $params['userid'] = $id;
        $params['courseid'] = $this->courseid;
        return $this->db->get_record($this->table, $params);
    }

    /**
     * Add a certain amount of experience points.
     *
     * @param int $id The receiver.
     * @param int $amount The amount.
     */
    public function increase($id, $amount) {
        $prexp = 0;
        $postxp = $amount;

        if ($record = $this->exists($id)) {
            $prexp = $record->xp;
            $postxp = $prexp + $amount;

            $sql = "UPDATE {{$this->table}}
                       SET xp = xp + :xp
                     WHERE courseid = :courseid
                       AND userid = :userid";
            $params = [
                'xp' => $amount,
                'courseid' => $this->courseid,
                'userid' => $id,
            ];
            $this->db->execute($sql, $params);
        } else {
            $this->insert($id, $amount);
        }

        $this->observe_increase($id, $prexp, $postxp);
    }

    /**
     * Add a certain amount of experience points.
     *
     * @param int $id The receiver.
     * @param int $amount The amount.
     * @param reason $reason A reason.
     */
    public function increase_with_reason($id, $amount, reason $reason) {
        $this->increase($id, $amount);
        $this->logger->log_reason($id, $amount, $reason);
    }

    /**
     * Insert the entry in the database.
     *
     * @param int $id The receiver.
     * @param int $amount The amount.
     */
    protected function insert($id, $amount) {
        $record = new stdClass();
        $record->courseid = $this->courseid;
        $record->userid = $id;
        $record->xp = $amount;
        $this->db->insert_record($this->table, $record);
    }

    /**
     * Make a user_state from the record.
     *
     * @param stdClass $record The row.
     * @param string $useridfield The user ID field.
     * @return user_state
     */
    public function make_state_from_record(stdClass $record, $useridfield = 'userid') {
        $user = user_utils::unalias_picture_fields($record, $useridfield);
        context_helper::preload_from_record($record);
        $xp = !empty($record->xp) ? $record->xp : 0;
        return new user_state($user, $xp, $this->levelsinfo, $this->courseid);
    }

    /**
     * Observe when increased.
     *
     * @param int $id The recipient.
     * @param int $beforexp The points before.
     * @param int $afterxp The points after.
     * @return void
     */
    protected function observe_increase($id, $beforexp, $afterxp) {
        $xpgained = $afterxp - $beforexp;

        if ($this->pointsobserver && $xpgained > 0) {
            $this->pointsobserver->points_increased($this, $id, $xpgained);
        }

        if ($this->observer) {
            $beforelevel = $this->levelsinfo->get_level_from_xp($beforexp);
            $afterlevel = $this->levelsinfo->get_level_from_xp($afterxp);
            if ($beforelevel->get_level() < $afterlevel->get_level()) {
                $this->observer->leveled_up($this, $id, $beforelevel, $afterlevel);
            }
        }
    }

    /**
     * Observe when set.
     *
     * @param int $id The recipient.
     * @param int $beforexp The points before.
     * @param int $afterxp The points after.
     * @return void
     */
    protected function observe_set($id, $beforexp, $afterxp) {
        if (!$this->observer) {
            return;
        }

        $beforelevel = $this->levelsinfo->get_level_from_xp($beforexp);
        $afterlevel = $this->levelsinfo->get_level_from_xp($afterxp);
        if ($beforelevel->get_level() < $afterlevel->get_level()) {
            $this->observer->leveled_up($this, $id, $beforelevel, $afterlevel);
        }
    }

    /**
     * Recalculate all the levels.
     *
     * Remember, these values are used for ordering only.
     *
     * @deprecated Since Level Up XP 3.15 without replacement.
     * @return void
     */
    public function recalculate_levels() {
        debugging('Reclaculating levels has been deprecated and made ineffective, do not use.', DEBUG_DEVELOPER);
    }

    /**
     * Reset all experience points.
     *
     * @return void
     */
    public function reset() {
        $this->db->delete_records($this->table, ['courseid' => $this->courseid]);
        $this->logger->reset();
    }

    /**
     * Reset all experience for users in a group.
     *
     * @param int $groupid The group ID.
     * @return void
     */
    public function reset_by_group($groupid) {
        $sql = "DELETE
                  FROM {{$this->table}}
                 WHERE courseid = :courseid
                   AND userid IN
               (SELECT gm.userid
                  FROM {groups_members} gm
                 WHERE gm.groupid = :groupid)";

        $params = [
            'courseid' => $this->courseid,
            'groupid' => $groupid,
        ];

        $this->db->execute($sql, $params);

        if ($this->logger instanceof collection_logger_with_group_reset) {
            $this->logger->reset_by_group($groupid);
        }
    }

    /**
     * Set the amount of experience points.
     *
     * @param int $id The receiver.
     * @param int $amount The amount.
     */
    public function set($id, $amount) {
        $prexp = 0;
        $postxp = $amount;

        if ($record = $this->exists($id)) {
            $prexp = $record->xp;
            $postxp = $amount;

            $sql = "UPDATE {{$this->table}}
                       SET xp = :xp
                     WHERE courseid = :courseid
                       AND userid = :userid";
            $params = [
                'xp' => $amount,
                'courseid' => $this->courseid,
                'userid' => $id,
            ];
            $this->db->execute($sql, $params);
        } else {
            $this->insert($id, $amount);
        }

        $this->observe_set($id, $prexp, $postxp);
    }

    /**
     * Set the amount of experience points.
     *
     * @param int $id The receiver.
     * @param int $amount The amount.
     * @param reason $reason A reason.
     */
    public function set_with_reason($id, $amount, reason $reason) {
        $this->set($id, $amount);
        $this->logger->log_reason($id, $amount, $reason);
    }

}
