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

namespace mod_bigbluebuttonbn\completion;

use core_completion\activity_custom_completion;
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\logger;
use moodle_exception;
use stdClass;

/**
 * Class custom_completion
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 */
class custom_completion extends activity_custom_completion {

    /**
     * Get current state
     *
     * @param string $rule
     * @return int
     */
    public function get_state(string $rule): int {
        // Get instance details.
        $instance = instance::get_from_cmid($this->cm->id);

        if (empty($instance)) {
            throw new moodle_exception("Can't find bigbluebuttonbn instance {$this->cm->instance}");
        }

        // Default return value.
        $value = COMPLETION_INCOMPLETE;
        $filters = $rule != "completionview" ? [logger::EVENT_SUMMARY] : [logger::EVENT_JOIN, logger::EVENT_PLAYED];
        $logs = logger::get_user_completion_logs($instance, $this->userid, $filters);

        if (method_exists($this, "get_{$rule}_value")) {
            $valuecount = $this->count_actions($logs, self::class . "::get_{$rule}_value");
            if ($valuecount) {
                if (!is_null($instance->get_instance_var($rule))) {
                    if ($instance->get_instance_var($rule) <= $valuecount) {
                        $value = COMPLETION_COMPLETE;
                    }
                } else {
                    // If there is at least a hit, we consider it as complete.
                    $value = $valuecount ? COMPLETION_COMPLETE : COMPLETION_INCOMPLETE;
                }
            }
        }
        return $value;
    }

    /**
     * Compute current state from logs.
     *
     * @param array $logs
     * @param callable $logvaluegetter
     * @return int the number of hits on this particular rule
     */
    protected function count_actions(array $logs, callable $logvaluegetter): int {
        if (empty($logs)) {
            // As completion by engagement with $rulename hand was required, the activity hasn't been completed.
            return 0;
        }

        $valuecount = 0;
        foreach ($logs as $log) {
            $valuecount += $logvaluegetter($log);
        }

        return $valuecount;
    }

    /**
     * Fetch the list of custom completion rules that this module defines.
     *
     * @return array
     */
    public static function get_defined_custom_rules(): array {
        return [
            'completionattendance',
            'completionengagementchats',
            'completionengagementtalks',
            'completionengagementraisehand',
            'completionengagementpollvotes',
            'completionengagementemojis',
            'completionview' // Completion view is now a customrule as it depends on the logs and not
            // the view action itself.
        ];
    }

    /**
     * Returns an associative array of the descriptions of custom completion rules.
     *
     * @return array
     */
    public function get_custom_rule_descriptions(): array {
        $completionengagementchats = $this->cm->customdata['customcompletionrules']['completionengagementchats'] ?? 1;
        $completionengagementtalks = $this->cm->customdata['customcompletionrules']['completionengagementtalks'] ?? 1;
        $completionengagementraisehand = $this->cm->customdata['customcompletionrules']['completionengagementraisehand'] ?? 1;
        $completionengagementpollvotes = $this->cm->customdata['customcompletionrules']['completionengagementpollvotes'] ?? 1;
        $completionengagementemojis = $this->cm->customdata['customcompletionrules']['completionengagementemojis'] ?? 1;
        $completionattendance = $this->cm->customdata['customcompletionrules']['completionattendance'] ?? 1;
        return [
            'completionengagementchats' => get_string('completionengagementchats_desc', 'mod_bigbluebuttonbn',
                $completionengagementchats),
            'completionengagementtalks' => get_string('completionengagementtalks_desc', 'mod_bigbluebuttonbn',
                $completionengagementtalks),
            'completionengagementraisehand' => get_string('completionengagementraisehand_desc', 'mod_bigbluebuttonbn',
                $completionengagementraisehand),
            'completionengagementpollvotes' => get_string('completionengagementpollvotes_desc', 'mod_bigbluebuttonbn',
                $completionengagementpollvotes),
            'completionengagementemojis' => get_string('completionengagementemojis_desc', 'mod_bigbluebuttonbn',
                $completionengagementemojis),
            'completionattendance' => get_string('completionattendance_desc', 'mod_bigbluebuttonbn',
                $completionattendance),
        ];
    }

    /**
     * Returns an array of all completion rules, in the order they should be displayed to users.
     *
     * @return array
     */
    public function get_sort_order(): array {
        return [
            'completionview',
            'completionengagementchats',
            'completionengagementtalks',
            'completionengagementraisehand',
            'completionengagementpollvotes',
            'completionengagementemojis',
            'completionattendance',
        ];
    }

    /**
     * Get current state in a  friendly version
     *
     * @param string $rule
     * @return string
     */
    public function get_printable_state(string $rule): string {
        // Get instance details.
        $instance = instance::get_from_cmid($this->cm->id);

        if (empty($instance)) {
            throw new moodle_exception("Can't find bigbluebuttonbn instance {$this->cm->instance}");
        }

        $summary = "";
        $filters = $rule != "completionview" ? [logger::EVENT_SUMMARY] : [logger::EVENT_JOIN, logger::EVENT_PLAYED];
        $logs = logger::get_user_completion_logs($instance, $this->userid, $filters);

        if (method_exists($this, "get_{$rule}_value")) {
            $summary = get_string(
                $rule . '_event_desc',
                'mod_bigbluebuttonbn',
                $this->count_actions($logs, self::class . "::get_{$rule}_value")
            );
        }
        return $summary;
    }

    /**
     * Get current state in a  friendly version
     *
     * @param string $rule
     * @return string
     */
    public function get_last_log_timestamp(string $rule): string {
        // Get instance details.
        $instance = instance::get_from_cmid($this->cm->id);

        if (empty($instance)) {
            throw new moodle_exception("Can't find bigbluebuttonbn instance {$this->cm->instance}");
        }
        $filters = $rule != "completionview" ? [logger::EVENT_SUMMARY] : [logger::EVENT_JOIN, logger::EVENT_PLAYED];
        return logger::get_user_completion_logs_max_timestamp($instance, $this->userid, $filters);
    }

    /**
     * Fetches the list of custom completion rules that are being used by this activity module instance.
     *
     * @return array
     */
    public function get_available_custom_rules(): array {
        $availablerules = parent::get_available_custom_rules();
        $availablerules[] = 'completionview'; // Completion view is now a customrule.
        return $availablerules;
    }

    /**
     * Get completion view value
     *
     * This will override the usual completion value (see COMPLETION_CUSTOM_MODULE_FLOW)
     *
     * @param stdClass $log
     * @return int
     */
    protected static function get_completionview_value(stdClass $log): int {
        return $log->log == logger::EVENT_PLAYED || $log->log == logger::EVENT_JOIN;
    }

    /**
     * Get attendance summary value
     *
     * @param stdClass $log
     * @return int
     */
    protected static function get_completionattendance_value(stdClass $log): int {
        $summary = json_decode($log->meta);
        if ($summary && !empty($summary->data->duration)) {
            return COMPLETION_COMPLETE;
        }
        return COMPLETION_INCOMPLETE;
    }

    /**
     * Get general completion engagement value
     *
     * @param stdClass $log
     * @return int
     */
    protected static function get_completionengagementchats_value(stdClass $log): int {
        return self::get_completionengagement_value($log, 'chats');
    }

    /**
     * Get general completion engagement value
     *
     * @param stdClass $log
     * @return int
     */
    protected static function get_completionengagementtalks_value(stdClass $log): int {
        return self::get_completionengagement_value($log, 'talks');
    }

    /**
     * Get general completion engagement value
     *
     * @param stdClass $log
     * @return int
     */
    protected static function get_completionengagementraisehand_value(stdClass $log): int {
        return self::get_completionengagement_value($log, 'raisehand');
    }

    /**
     * Get general completion engagement value
     *
     * @param stdClass $log
     * @return int
     */
    protected static function get_completionengagementpollvotes_value(stdClass $log): int {
        return self::get_completionengagement_value($log, 'pollvotes');
    }

    /**
     * Get general completion engagement value
     *
     * @param stdClass $log
     * @return int
     */
    protected static function get_completionengagementemojis_value(stdClass $log): int {
        return self::get_completionengagement_value($log, 'emojis');
    }

    /**
     * Get general completion engagement value
     *
     * @param stdClass $log
     * @param string $type
     * @return int
     */
    protected static function get_completionengagement_value(stdClass $log, string $type): int {
        $summary = json_decode($log->meta);
        if ($summary && !empty($summary->data->engagement->$type)) {
            return COMPLETION_COMPLETE;
        }
        return COMPLETION_INCOMPLETE;
    }
}
