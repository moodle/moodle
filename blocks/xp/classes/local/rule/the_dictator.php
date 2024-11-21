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
 * Dictator.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\rule;

use block_xp\local\rule\instance;
use block_xp\local\rulefilter\handler;
use moodle_database;

/**
 * Dictator.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class the_dictator implements dictator {

    /** @var moodle_database The database. */
    protected $db;
    /** @var handler The filter provider. */
    protected $filterhandler;

    /** @var array Array indexed by cache key, containing instances. */
    protected $rulesinctxcache = [];

    /**
     * Constructor.
     *
     * @param moodle_database $db The database.
     * @param handler $filterhandler The filter provider.
     */
    public function __construct(moodle_database $db, handler $filterhandler) {
        $this->db = $db;
        $this->filterhandler = $filterhandler;
    }

    /**
     * Normalise the child context ID.
     *
     * @param \context $storecontext The context.
     * @param \context|null $childcontext The child context.
     * @return int
     */
    protected function normalise_childcontext_id(\context $storecontext, \context $childcontext = null): int {
        $childcontextid = 0;
        if ($childcontext && $childcontext->is_child_of($storecontext, false)) {
            $childcontextid = $childcontext->id;
        }
        return $childcontextid;
    }

    /**
     * Count rules in context.
     *
     * @param \context $storecontext The context.
     * @param \context|null $childcontext The child context.
     * @param array $options Some options (expected to support type, and filter).
     * @return instance[]
     */
    public function count_rules_in_context(\context $storecontext, \context $childcontext = null, array $options = []) {
        $conditions = [
            'contextid' => $storecontext->id,
            'childcontextid' => $this->normalise_childcontext_id($storecontext, $childcontext),
        ];

        if (!empty($options['type'])) {
            $wheres[] = 'type = :type';
            $conditions['type'] = $options['type'];
        }
        if (!empty($options['filter'])) {
            $conditions['filter'] = $options['filter'];
        }

        return $this->db->count_records('block_xp_rule', $conditions);
    }

    /**
     * Fetch rules in context.
     *
     * @param \context $storecontext The context.
     * @param \context|null $childcontext The child context.
     * @return instance[]
     */
    protected function fetch_rules_in_context(\context $storecontext, \context $childcontext = null) {
        $sql = "SELECT r.*, ctx.contextlevel AS contextlevel, childctx.contextlevel AS childcontextlevel
                  FROM {block_xp_rule} r
                  JOIN {context} ctx ON ctx.id = r.contextid
             LEFT JOIN {context} childctx ON childctx.id = r.childcontextid
                 WHERE r.contextid = :contextid
                   AND r.childcontextid = :childcontextid";
        $params = [
            'contextid' => $storecontext->id,
            'childcontextid' => $this->normalise_childcontext_id($storecontext, $childcontext),
        ];
        $records = $this->db->get_records_sql($sql, $params);

        return array_values(array_map(function($record) {
            return new static_instance($record);
        }, $records));
    }

    /**
     * Get the effective contexts.
     *
     * @param \context $storecontext The context.
     * @param \context $actioncontext The action context.
     * @return \context[]
     */
    protected function get_effective_contexts(\context $storecontext, \context $actioncontext) {
        $contexts = [$storecontext];
        if (!$storecontext instanceof \context_course) {
            $subcontext = $actioncontext->get_course_context(false) ?: null;
            if ($subcontext && $subcontext instanceof \context_course) {
                $contexts[] = $subcontext;
            }
        }
        return $contexts;
    }

    /**
     * Get the effective rules.
     *
     * @param \context $storecontext The context.
     * @param \context $actioncontext The child context.
     * @return instance[]
     */
    public function get_effective_rules(\context $storecontext, \context $actioncontext) {
        $contexts = $this->get_effective_contexts($storecontext, $actioncontext);
        return $this->get_rules_in_contexts($storecontext, $contexts);
    }

    /**
     * Get the effective rules grouped by type.
     *
     * @param \context $storecontext The context.
     * @param \context $actioncontext The child context.
     * @return instance[]
     */
    public function get_effective_rules_grouped_by_type(\context $storecontext, \context $actioncontext) {
        $rules = $this->get_effective_rules($storecontext, $actioncontext);
        $groupedrules = [];
        foreach ($rules as $rule) {
            $typename = $rule->get_type_name();
            if (!isset($groupedrules[$typename])) {
                $groupedrules[$typename] = [];
            }
            $groupedrules[$typename][] = $rule;
        }
        return $groupedrules;
    }

    /**
     * Get rules in context.
     *
     * @param \context $storecontext The context.
     * @param \context|null $childcontext The child context.
     * @return instance[]
     */
    public function get_rules_in_context(\context $storecontext, \context $childcontext = null) {
        $cachekey = $storecontext->id . ':' . ($childcontext ? $childcontext->id : 0);
        if (!isset($this->rulesinctxcache[$cachekey])) {
            $this->rulesinctxcache[$cachekey] = $this->fetch_rules_in_context($storecontext, $childcontext);
        }
        return $this->rulesinctxcache[$cachekey];
    }

    /**
     * Get rules in contexts.
     *
     * @param \context $storecontext The context.
     * @param \context[] $contexts The contexts.
     * @return instance[]
     */
    protected function get_rules_in_contexts(\context $storecontext, array $contexts) {
        $rules = [];
        foreach ($contexts as $context) {
            $rules = array_merge($rules, $this->get_rules_in_context($storecontext, $context));
        }
        return $rules;
    }

    /**
     * Get rules of particular types in context.
     *
     * @param \context $storecontext The context.
     * @param string[] $types The type names.
     * @param \context|null $childcontext The child context.
     * @return instance[]
     */
    public function get_rules_of_types_in_context(\context $storecontext, array $types, \context $childcontext = null) {
        // This may not be seen as very efficient, however getting the rules in the context is expected
        // to be cached, and therefore it should be good enough to filter as we do here for now.
        $rules = $this->get_rules_in_context($storecontext, $childcontext);
        return array_filter($rules, function($rule) use ($types) {
            return in_array($rule->get_type_name(), $types);
        });
    }

    /**
     * Sort the rules by priority.
     *
     * @param instance[] $rules The rules.
     * @return instance[] The most important rules first.
     */
    public function sort_rules_by_priority($rules): array {
        // Sort the rules by context level, filter method weight, and then points, all descendingly, and then ID.
        // This means that the deepest context, with the highest weight, and the most points will be evaluated first.
        // Although, the context depth does not currently handle a hierarchy within a context level (like course cat).
        usort($rules, function($a, $b) {
            $achildcontext = $a->get_child_context();
            $bchildcontext = $b->get_child_context();

            // Sort by context.
            $acontextlevel = (int) ($achildcontext ? $achildcontext->contextlevel : $a->get_context()->contextlevel);
            $bcontextlevel = (int) ($bchildcontext ? $bchildcontext->contextlevel : $b->get_context()->contextlevel);
            if ($acontextlevel !== $bcontextlevel) {
                return $bcontextlevel - $acontextlevel;
            }

            // Sort by filter.
            if ($a->get_filter_name() !== $b->get_filter_name()) {
                $prioritya = $this->filterhandler->get_filter_priority_from_name($a->get_filter_name());
                $priorityb = $this->filterhandler->get_filter_priority_from_name($b->get_filter_name());
                return $priorityb - $prioritya;
            }

            // Sort by points descending.
            if ($a->get_points() !== $b->get_points()) {
                return $b->get_points() - $a->get_points();
            }

            // In case we've got a duplicate, sort by ID.
            return $a->get_id() - $b->get_id();
        });

        return $rules;
    }

}
