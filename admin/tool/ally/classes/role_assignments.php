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
 * Determine if a user has a role assignment in a context or parent contexts.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally;

/**
 * Determine if a user has a role assignment in a context or parent contexts.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class role_assignments {
    /**
     * @var array
     */
    private $roleids;

    /**
     * @var array
     */
    private $data;
    /**
     * @var \moodle_database
     */
    private $db;

    public function __construct(array $roleids = [], \moodle_database $db = null) {
        global $DB;

        $this->roleids = $roleids;
        $this->db      = $db ?: $DB;
    }

    /**
     * Determine if a user has a role assignment in a context or parent contexts.
     *
     * @param int $userid
     * @param \context $context
     * @return bool
     */
    public function has($userid, \context $context) {
        if (empty($this->roleids)) {
            return false; // Nothing to do.
        }

        foreach ($context->get_parent_context_ids(true) as $contextid) {
            $this->load_role_assignments($contextid);
            if (array_key_exists($contextid, $this->data) && array_key_exists($userid, $this->data[$contextid])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return users with acceptable role assignments for a specific context.
     * @param \context $context
     * @return array
     */
    public function user_ids_for_context(\context $context) {
        $this->load_role_assignments($context->id);
        if (empty($this->data[$context->id])) {
            return [];
        }
        return $this->data[$context->id];
    }

    /**
     * Load all role assignments that we care about and store them into the class.
     * @param int $contextid
     * @throws \dml_exception
     */
    private function load_role_assignments(int $contextid) {
        if (empty($this->roleids)) {
            return; // Nothing to do.
        }

        if (is_array($this->data) && array_key_exists($contextid, $this->data)) {
            return; // Already loaded.
        }

        if (!is_array($this->data)) {
            $this->data = [];
        }

        list($insql, $params) = $this->db->get_in_or_equal($this->roleids, SQL_PARAMS_NAMED);

        $query = <<<SQL
            SELECT id, contextid, userid
              FROM {role_assignments}
             WHERE roleid $insql
               AND contextid = :contextid
SQL;
        $params['contextid'] = $contextid;

        $rs = $this->db->get_recordset_sql($query, $params);
        foreach ($rs as $row) {
            if (!array_key_exists($row->contextid, $this->data)) {
                $this->data[$row->contextid] = [];
            }
            $this->data[$row->contextid][$row->userid] = true;
        }
        $rs->close();
    }
}
