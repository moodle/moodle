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

declare(strict_types=1);

namespace mod_tincanlaunch\completion;

use core_completion\activity_custom_completion;

/**
 * Activity custom completion subclass for the tincanlaunch activity.
 *
 * Class for defining mod_tincanlaunch custom completion rules and fetching the
 * completion statuses of the custom completion rules for a given tincanlaunch
 * instance and a user.
 *
 * @package   mod_tincanlaunch
 * @copyright 2023 David Pesce <david.pesce@exputo.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_completion extends activity_custom_completion {

    /**
     * Fetches the completion state for a given completion rule.
     *
     * @param string $rule The completion rule.
     * @return int The completion state.
     */
    public function get_state(string $rule): int {
        global $DB;

        $this->validate_rule($rule);

        $status = false;
        $userid = $this->userid;
        $cm = $this->cm;

        $tincanlaunch = $DB->get_record('tincanlaunch', array('id' => $cm->instance), '*', MUST_EXIST);

        $tincanlaunchsettings = tincanlaunch_settings($cm->instance);

        // Retrieve expirydays (if set) and set expirydate.
        $expiryrangestartdate = null;
        $expirydays = $tincanlaunch->tincanexpiry;
        if ($expirydays > 0) {
            $expirydatetime = new \DateTime();
            $expirydatetime->sub(new \DateInterval('P'.$expirydays.'D'));
            $expiryrangestartdate = $expirydatetime->format('c');
        }

        if (!empty($tincanlaunch->tincanverbid)) {
            // Retrieve statements from LRS matching actor, object, and completion verb.
            $user = $DB->get_record('user', array ('id' => $userid));
            $statementquery = tincanlaunch_get_statements(
                $tincanlaunchsettings['tincanlaunchlrsendpoint'],
                $tincanlaunchsettings['tincanlaunchlrslogin'],
                $tincanlaunchsettings['tincanlaunchlrspass'],
                $tincanlaunchsettings['tincanlaunchlrsversion'],
                $tincanlaunch->tincanactivityid,
                tincanlaunch_getactor($cm->instance, $user),
                $tincanlaunch->tincanverbid,
                $expiryrangestartdate
            );
        }

        // Determine if the statement exists.
        if (!empty($statementquery->content) && $statementquery->success) {
            foreach ($statementquery->content as $statement) {

                // Check if the statement activity id matches the launched activity URI.
                $target = $statement->getTarget();
                $objectid = $target->getId();
                $objecttype = $target->getObjectType();
                if ($objecttype == "Activity" && $tincanlaunch->tincanactivityid == $objectid) {

                    // If expiry is set, see if the timestamp is within expiry.
                    $statementtimestamp = $statement->getTimestamp();
                    if ($expiryrangestartdate !== null && $expiryrangestartdate <= $statementtimestamp) {
                        $status = true;
                        break;
                    } else if ($expiryrangestartdate === null) {
                        $status = true;
                        break;
                    }
                }
            }
        }

        return $status ? COMPLETION_COMPLETE : COMPLETION_INCOMPLETE;
    }

    /**
     * Fetch the list of custom completion rules that this module defines.
     *
     * @return array
     */
    public static function get_defined_custom_rules(): array {
        return [
            'tincancompletionverb',
            'tincancompletioexpiry'
        ];
    }

    /**
     * Returns an associative array of the descriptions of custom completion rules.
     *
     * @return array
     */
    public function get_custom_rule_descriptions(): array {
        global $DB;

        $cm = $this->cm;

        $tincanlaunch = $DB->get_record('tincanlaunch', array('id' => $cm->instance), '*', MUST_EXIST);
        $tincanverbid = $tincanlaunch->tincanverbid;
        $tincanverb = ucfirst(substr($tincanverbid, strrpos($tincanverbid, '/') + 1));

        $tincanexpirydays = $tincanlaunch->tincanexpiry;

        return [
            'tincancompletionverb' => get_string('completiondetail:completionbyverb', 'tincanlaunch', $tincanverb),
            'tincancompletioexpiry' => get_string('completiondetail:completionexpiry', 'tincanlaunch', $tincanexpirydays)
        ];
    }

    /**
     * Show the manual completion or not regardless of the course's showcompletionconditions setting.
     *
     * @return bool
     */
    public function manual_completion_always_shown(): bool {
        return true;
    }

    /**
     * Returns an array of all completion rules, in the order they should be displayed to users.
     *
     * @return array
     */
    public function get_sort_order(): array {
        return [
            'completionview',
            'tincancompletionverb',
            'tincancompletioexpiry'
        ];
    }
}
