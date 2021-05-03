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

declare(strict_types = 1);

namespace core_completion;

use cm_info;
use coding_exception;
use moodle_exception;

/**
 * Base class for defining an activity module's custom completion rules.
 *
 * Class for defining an activity module's custom completion rules and fetching the completion statuses
 * of the custom completion rules for a given module instance and a user.
 *
 * @package   core_completion
 * @copyright 2021 Jun Pataleta <jun@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class activity_custom_completion {

    /** @var cm_info The course module information object. */
    protected $cm;

    /** @var int The user's ID. */
    protected $userid;

    /**
     * activity_custom_completion constructor.
     *
     * @param cm_info $cm
     * @param int $userid
     */
    public function __construct(cm_info $cm, int $userid) {
        $this->cm = $cm;
        $this->userid = $userid;
    }

    /**
     * Validates that the custom rule is defined by this plugin and is enabled for this activity instance.
     *
     * @param string $rule The custom completion rule.
     */
    public function validate_rule(string $rule): void {
        // Check that this custom completion rule is defined.
        if (!$this->is_defined($rule)) {
            throw new coding_exception("Undefined custom completion rule '$rule'");
        }

        // Check that this custom rule is included in the course module's custom completion rules.
        if (!$this->is_available($rule)) {
            throw new moodle_exception("Custom completion rule '$rule' is not used by this activity.");
        }
    }

    /**
     * Whether this module defines this custom rule.
     *
     * @param string $rule The custom completion rule.
     * @return bool
     */
    public function is_defined(string $rule): bool {
        return in_array($rule, static::get_defined_custom_rules());
    }

    /**
     * Checks whether the custom completion rule is being used by the activity module instance.
     *
     * @param string $rule The custom completion rule.
     * @return bool
     */
    public function is_available(string $rule): bool {
        return in_array($rule, $this->get_available_custom_rules());
    }

    /**
     * Fetches the list of custom completion rules that are being used by this activity module instance.
     *
     * @return array
     */
    public function get_available_custom_rules(): array {
        $rules = static::get_defined_custom_rules();
        $availablerules = [];
        $customdata = (array)$this->cm->customdata;
        foreach ($rules as $rule) {
            $customrule = $customdata['customcompletionrules'][$rule] ?? false;
            if (!empty($customrule)) {
                $availablerules[] = $rule;
            }
        }
        return $availablerules;
    }

    /**
     * Fetches the overall completion status of this activity instance for a user based on its available custom completion rules.
     *
     * @return int The completion state (e.g. COMPLETION_COMPLETE, COMPLETION_INCOMPLETE).
     */
    public function get_overall_completion_state(): int {
        foreach ($this->get_available_custom_rules() as $rule) {
            $state = $this->get_state($rule);
            // Return early if one of the custom completion rules is not yet complete.
            if ($state == COMPLETION_INCOMPLETE) {
                return $state;
            }
        }
        // If this was reached, then all custom rules have been marked complete.
        return COMPLETION_COMPLETE;
    }

    /**
     * Fetches the description for a given custom completion rule.
     *
     * @param string $rule The custom completion rule.
     * @return string
     */
    public function get_custom_rule_description(string $rule): string {
        $descriptions = $this->get_custom_rule_descriptions();
        if (!isset($descriptions[$rule])) {
            // Lang string not found for this custom completion rule. Just return it.
            return $rule;
        }
        return $descriptions[$rule];
    }

    /**
     * Show the manual completion or not regardless of the course's showcompletionconditions setting.
     * Returns false by default for plugins that don't need to override the course's showcompletionconditions setting.
     * Activity plugins that need to always show manual completion need to override this function.
     *
     * @return bool
     */
    public function manual_completion_always_shown(): bool {
        return false;
    }

    /**
     * Fetches the module's custom completion class implementation if it's available.
     *
     * @param string $modname The activity module name. Usually from cm_info::modname.
     * @return string|null
     */
    public static function get_cm_completion_class(string $modname): ?string {
        $cmcompletionclass = "mod_{$modname}\\completion\\custom_completion";
        if (class_exists($cmcompletionclass) && is_subclass_of($cmcompletionclass, self::class)) {
            return $cmcompletionclass;
        }
        return null;
    }

    /**
     * Fetches the completion state for a given completion rule.
     *
     * @param string $rule The completion rule.
     * @return int The completion state.
     */
    abstract public function get_state(string $rule): int;

    /**
     * Fetch the list of custom completion rules that this module defines.
     *
     * @return array
     */
    abstract public static function get_defined_custom_rules(): array;

    /**
     * Returns an associative array of the descriptions of custom completion rules.
     *
     * @return array
     */
    abstract public function get_custom_rule_descriptions(): array;

    /**
     * Returns an array of all completion rules, in the order they should be displayed to users.
     *
     * @return array
     */
    abstract public function get_sort_order(): array;
}
