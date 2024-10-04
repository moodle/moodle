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

namespace gradepenalty_duedate;

use context;
use context_system;
use core\lang_string;
use core\persistent;

/**
 * To create/load/update/delete penalty rules.
 *
 * @package   gradepenalty_duedate
 * @copyright 2024 Catalyst IT Australia Pty Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class penalty_rule extends persistent {
    /** The table name this persistent object maps to. */
    const TABLE = 'gradepenalty_duedate_rule';

    /**
     * Return the definition of the properties of this model.
     */
    protected static function define_properties(): array {
        return [
            'contextid' => [
                'type' => PARAM_INT,
                'null' => NULL_NOT_ALLOWED,
            ],
            'overdueby' => [
                'type' => PARAM_INT,
                'null' => NULL_NOT_ALLOWED,
            ],
            'penalty' => [
                'type' => PARAM_FLOAT,
                'null' => NULL_NOT_ALLOWED,
            ],
            'sortorder' => [
                'type' => PARAM_INT,
                'null' => NULL_NOT_ALLOWED,
                'default' => 0,
            ],
        ];
    }

    /**
     * Validate the overdueby before saving.
     *
     * @param int $value overdueby value.
     * @return true|lang_string error message if overdueby is invalid.
     */
    protected function validate_overdueby($value): bool|lang_string {
        if ($value < constants::OVERDUEBY_MIN) {
            return new lang_string('error_overdueby_minvalue', 'gradepenalty_duedate', constants::OVERDUEBY_MIN);
        }
        return true;
    }

    /**
     * Validate the penalty before saving.
     *
     * @param int $value penalty value.
     * @return true|lang_string error message if penalty is invalid.
     */
    protected function validate_penalty($value): bool|lang_string {
        if ($value < constants::PENALTY_MIN) {
            return new lang_string('error_penalty_minvalue', 'gradepenalty_duedate', constants::PENALTY_MIN);
        } else if ($value > constants::PENALTY_MAX) {
            return new lang_string('error_penalty_maxvalue', 'gradepenalty_duedate', constants::PENALTY_MAX);
        }
        return true;
    }

    /**
     * Get the penalty rules for a context.
     * If not found, it will get the rules from the parent context.
     *
     * @param int $contextid context id
     * @return array penalty_rule records.
     */
    public static function get_rules(int $contextid): array {
        $rules = [];
        $currentcontext = context::instance_by_id($contextid);
        while (empty($rules) && $currentcontext) {
            $rules = self::get_records(['contextid' => $currentcontext->id], 'sortorder');
            $currentcontext = $currentcontext->get_parent_context();
        }

        return $rules;
    }

    /**
     * Reset rules for a context.
     * Delete all rules for the context.
     *
     * @param int $contextid context id.
     * @return void
     */
    public static function reset_rules(int $contextid): void {
        // Get rules for the context.
        $rules = self::get_records(['contextid' => $contextid]);

        // Delete all rules.
        foreach ($rules as $rule) {
            $rule->delete();
        }

        // Check if it is system context, create a default rule.
        if ($contextid == context_system::instance()->id) {
            $rule = new penalty_rule();
            $rule->set('contextid', $contextid);
            $rule->set('overdueby', 1);
            $rule->set('penalty', 0);
            $rule->set('sortorder', 0);
            $rule->save();
        }
    }

    /**
     * Check if rules are overridden in a context.
     *
     * @param int $contextid context id.
     * @return bool true if rules are overridden.
     */
    public static function is_overridden(int $contextid): bool {
        // Exclude system context.
        if ($contextid == context_system::instance()->id) {
            return false;
        }
        $rules = self::get_records(['contextid' => $contextid]);
        // If there is no rules in parent contexts, still consider they are overridden.
        return count($rules) > 0;
    }

    /**
     * Check if the rules are inherited from the parent context.
     *
     * @param int $contextid context id.
     * @return bool true if rules are inherited.
     */
    public static function is_inherited(int $contextid): bool {
        // Exclude system context.
        if ($contextid == context_system::instance()->id) {
            return false;
        }

        $rules = self::get_records(['contextid' => $contextid]);
        $parentrules = self::get_rules($contextid);
        return count($rules) == 0 && count($parentrules) > 0;
    }
}
