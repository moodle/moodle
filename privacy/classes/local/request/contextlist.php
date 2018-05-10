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
 * Privacy Fetch Result Set.
 *
 * @package    core_privacy
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_privacy\local\request;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy Fetch Result Set.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class contextlist extends contextlist_base {

    /**
     * Add a set of contexts from  SQL.
     *
     * The SQL should only return a list of context IDs.
     *
     * @param   string  $sql    The SQL which will fetch the list of * context IDs
     * @param   array   $params The set of SQL parameters
     * @return  $this
     */
    public function add_from_sql(string $sql, array $params) : contextlist {
        global $DB;

        $fields = \context_helper::get_preload_record_columns_sql('ctx');
        $wrapper = "SELECT {$fields} FROM {context} ctx WHERE id IN ({$sql})";
        $contexts = $DB->get_recordset_sql($wrapper, $params);

        $contextids = [];
        foreach ($contexts as $context) {
            $contextids[] = $context->ctxid;
            \context_helper::preload_from_record($context);
        }

        $this->set_contextids(array_merge($this->get_contextids(), $contextids));

        return $this;
    }

    /**
     * Adds the system context.
     *
     * @return $this
     */
    public function add_system_context() : contextlist {
        return $this->add_from_sql(SYSCONTEXTID, []);
    }

    /**
     * Adds the user context for a given user.
     *
     * @param int $userid
     * @return $this
     */
    public function add_user_context(int $userid) : contextlist {
        $sql = "SELECT DISTINCT ctx.id
                  FROM {context} ctx
                 WHERE ctx.contextlevel = :contextlevel
                   AND ctx.instanceid = :instanceid";
        return $this->add_from_sql($sql, ['contextlevel' => CONTEXT_USER, 'instanceid' => $userid]);
    }

    /**
     * Adds the user contexts for given users.
     *
     * @param array $userids
     * @return $this
     */
    public function add_user_contexts(array $userids) : contextlist {
        global $DB;

        list($useridsql, $useridparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $sql = "SELECT DISTINCT ctx.id
                  FROM {context} ctx
                 WHERE ctx.contextlevel = :contextlevel
                   AND ctx.instanceid $useridsql";
        return $this->add_from_sql($sql, ['contextlevel' => CONTEXT_USER] + $useridparams);
    }

    /**
     * Sets the component for this contextlist.
     *
     * @param string $component the frankenstyle component name.
     */
    public function set_component($component) {
        parent::set_component($component);
    }
}
