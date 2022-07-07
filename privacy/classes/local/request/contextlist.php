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
        if ($fieldname = $this->guess_id_field_from_sql($sql)) {
            if (is_numeric($fieldname)) {
                $wrapper = "
                  SELECT {$fields}
                    FROM {context} ctx
                   WHERE ctx.id = :fieldvalue";
                $params = ['fieldvalue' => $fieldname];
            } else {
                // Able to guess a field name.
                $wrapper = "
                  SELECT {$fields}
                    FROM {context} ctx
                    JOIN ({$sql}) target ON ctx.id = target.{$fieldname}";
            }
        } else {
            // No field name available. Fall back on a potentially slower version.
            $wrapper = "
              SELECT {$fields}
                FROM {context} ctx
               WHERE ctx.id IN ({$sql})";
        }
        $contexts = $DB->get_recordset_sql($wrapper, $params);

        $contextids = [];
        foreach ($contexts as $context) {
            $contextids[] = $context->ctxid;
            \context_helper::preload_from_record($context);
        }
        $contexts->close();

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

    /**
     * Guess the name of the contextid field from the supplied SQL.
     *
     * @param   string  $sql The SQL to guess from
     * @return  string  The field name or a numeric value representing the context id
     */
    protected function guess_id_field_from_sql(string $sql) : string {
        // We are not interested in any subquery/view/conditions for the purpose of this method, so
        // let's reduce the query to the interesting parts by recursively cleaning all
        // contents within parenthesis. If there are problems (null), we keep the text unmodified.
        // So just top-level sql will remain after the reduction.
        $recursiveregexp = '/\((([^()]*|(?R))*)\)/';
        $sql = (preg_replace($recursiveregexp, '', $sql) ?: $sql);
        // Get the list of relevant words from the SQL Query.
        // We explode the SQL by the space character, then trim any extra whitespace (e.g. newlines), before we filter
        // empty value, and finally we re-index the array.
        $sql = rtrim($sql, ';');
        $words = array_map('trim', preg_split('/\s+/', $sql));
        $words = array_filter($words, function($word) {
            return $word !== '';
        });
        $words = array_values($words);
        $uwords = array_map('strtoupper', $words); // Uppercase all them.

        // If the query has boolean operators (UNION, it is the only one we support cross-db)
        // then we cannot guarantee whats coming after the first query, it can be anything.
        if (array_search('UNION', $uwords)) {
            return '';
        }

        if ($firstfrom = array_search('FROM', $uwords)) {
            // Found a FROM keyword.
            // Select the previous word.
            $fieldname = $words[$firstfrom - 1];
            if (is_numeric($fieldname)) {
                return $fieldname;
            }

            if ($hasdot = strpos($fieldname, '.')) {
                // This field is against a table alias. Take off the alias.
                $fieldname = substr($fieldname, $hasdot + 1);
            }

            return $fieldname;

        } else if ((count($words) == 1) && (is_numeric($words[0]))) {
            // Not a real SQL, just a single numerical value - such as one returned by {@link self::add_system_context()}.
            return $words[0];

        } else if ((count($words) == 2) && (strtoupper($words[0]) === 'SELECT') && (is_numeric($words[1]))) {
            // SQL returning a constant numerical value.
            return $words[1];
        }

        return '';
    }
}
