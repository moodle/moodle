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
 * Informs about the last merge operations related to a given user.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol-Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers\local;

use dml_exception;
use stdClass;

/**
 * Informs about the last merge operations related to a given user.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol-Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class last_merge {
    /** @var int user.id to check. */
    private readonly int $userid;
    /** @var bool user.suspended value for the given $userid */
    private readonly bool $suspended;
    /** @var null|stdClass null when there is no last merge as user to be kept; the log record otherwise. */
    private readonly mixed $tome;
    /** @var null|stdClass null when there is no last merge as user to be removed; the log record otherwise. */
    private readonly mixed $fromme;

    /**
     * Just informs whether the user is deletable from this plugin viewpoint.
     *
     * @param int $userid user.id field.
     * @throws dml_exception
     */
    public static function is_user_deletable(int $userid): bool {
        return self::from($userid)->is_this_user_deletable();
    }

    /**
     * Initialitzes the instance with the last merges for the given $userid.
     *
     * @param int $userid user.id field.
     * @throws dml_exception
     */
    public static function from(int $userid): last_merge {
        return new self($userid);
    }

    /**
     * Initializes the instance with the last merges.
     *
     * @param int $userid user.id field.
     * @throws dml_exception
     */
    private function __construct(int $userid) {
        global $DB;
        $logger = new logger();
        $this->userid = $userid;
        $this->suspended = (bool)(int)$DB->get_field('user', 'suspended', ['id' => $userid]);
        // Find last merge to/from this profile.
        $this->tome = current($logger->get(['touserid' => $userid], 0, 1)) ?: null;
        $this->fromme = current($logger->get(['fromuserid' => $userid], 0, 1)) ?: null;
    }

    /**
     * Informs if according to last merges, the user is deletable.
     *
     * @return bool true when it is deletable.
     */
    public function is_this_user_deletable(): bool {
        // Current userid is deletable only if:
        // 1. Its account is suspended.
        $deletable = $this->suspended;
        // 2. This account was used as user to be removed and it was successful.
        $deletable = $deletable && isset($this->fromme->timemodified) && (int)$this->fromme->success;
        // 3. Or, in presence of merge as user to be kept, the merge as user to be removed is the last one and successful.
        $deletable = $deletable ||
            (   isset($this->tome->timemodified) &&
                isset($this->fromme->timemodified) &&
                (int)$this->tome->success &&
                $this->fromme->timemodified > $this->tome->timemodified
            );
        return $deletable;
    }

    /**
     * Gets the calculated last merge with this user as to be removed.
     *
     * @return stdClass|null
     */
    public function fromme(): null|stdClass {
        return $this->fromme;
    }

    /**
     * Gets the calculated last merge with this user as to be kept.
     *
     * @return stdClass|null
     */
    public function tome(): null|stdClass {
        return $this->tome;
    }

    /**
     * Lists deletable users according to this plugin overview.
     *
     * Deletable users match these conditions:
     *
     * 1. Are not deleted yet.
     * 2. Were merged as user to remove.
     * 3. Are suspended.
     * 4. In presence of a merge as user to keep, the merge as user to remove was later.
     * 5. Both merges were successful.
     *
     * @throws dml_exception
     */
    public static function list_all_deletable_users(): array {
        global $DB;
        $sql = "
         SELECT mremove2.fromuserid,
                mremove2.timemodified AS fromtimemodified,
                mkeep2.touserid,
                mkeep2.timemodified AS totimemodified
           FROM (SELECT mremove.fromuserid,
                        mremove.timemodified
                   FROM (SELECT m.fromuserid,
                                m.timemodified,
                                m.success,
                                ROW_NUMBER() OVER (PARTITION BY m.fromuserid ORDER BY m.timemodified DESC) AS rownumber
                           FROM {tool_mergeusers} m
                        ) mremove
                   JOIN {user} u ON (u.id = mremove.fromuserid)
                  WHERE mremove.rownumber = :rownumber1
                    AND u.deleted = :nodeleted
                    AND u.suspended = :suspended
                    AND mremove.success = :success1
                  ) mremove2
            LEFT JOIN (SELECT
                           mkeep.touserid,
                           mkeep.timemodified
                       FROM (SELECT
                                 m.touserid,
                                 m.timemodified,
                                 m.success,
                                 ROW_NUMBER() OVER (PARTITION BY m.touserid ORDER BY m.timemodified DESC) AS rownumber
                             FROM {tool_mergeusers} m) mkeep
                       WHERE mkeep.rownumber = :rownumber2
                         AND mkeep.success = :success2
            ) mkeep2 ON (mremove2.fromuserid = mkeep2.touserid AND mkeep2.timemodified < mremove2.timemodified)";

        $params = [
            'rownumber1' => 1,
            'rownumber2' => 1,
            'nodeleted' => 0,
            'suspended' => 1,
            'success1' => 1,
            'success2' => 1,
        ];

        return $DB->get_records_sql($sql, $params);
    }
}
