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

namespace mod_scorm\completion;

defined('MOODLE_INTERNAL') || die();

use core_completion\activity_custom_completion;

require_once($CFG->dirroot.'/mod/scorm/locallib.php');

/**
 * Activity custom completion subclass for the scorm activity.
 *
 * Contains the class for defining mod_scorm's custom completion rules
 * and fetching a scorm instance's completion statuses for a user.
 *
 * @package mod_scorm
 * @copyright Michael Hawkins <michaelh@moodle.com>
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

        // Base query used when fetching user's tracks data.
        $basequery = "SELECT id, scoid, element, value
                        FROM {scorm_scoes_track}
                       WHERE scormid = ?
                         AND userid = ?";

        switch ($rule) {
            case 'completionstatusrequired':
                $status = COMPLETION_INCOMPLETE;
                $query = $basequery .
                    " AND element IN (
                          'cmi.core.lesson_status',
                          'cmi.completion_status',
                          'cmi.success_status'
                    )";

                $tracks = $DB->get_records_sql($query, [$this->cm->instance, $this->userid]);

                // Get available status list.
                $statuses = array_flip(\scorm_status_options());
                $statusbits = 0;

                $requiredcompletionstatusid = $this->cm->customdata['customcompletionrules']['completionstatusrequired'] ?? 0;

                // Check at least one track meets the required completion status value(s).
                foreach ($tracks as $track) {
                    if (array_key_exists($track->value, $statuses)) {
                        $statusbits |= $statuses[$track->value];
                    }

                    // All completion status requirements met.
                    if ($statusbits == $requiredcompletionstatusid) {
                        $status = COMPLETION_COMPLETE;
                        break;
                    }
                }

                break;

            case 'completionscorerequired':
                $status = COMPLETION_INCOMPLETE;
                $query = $basequery .
                    " AND element IN (
                          'cmi.core.score.raw',
                          'cmi.score.raw'
                    )";

                $tracks = $DB->get_records_sql($query, [$this->cm->instance, $this->userid]);

                $requiredscore = $this->cm->customdata['customcompletionrules']['completionscorerequired'];

                // Check if any track meets or exceeds the minimum score required.
                foreach ($tracks as $track) {
                    if (strlen($track->value) && (floatval($track->value) >= $requiredscore)) {
                        $status = COMPLETION_COMPLETE;

                        // No need to check any other tracks once condition is confirmed completed.
                        break;
                    }
                }

                break;

            case 'completionstatusallscos':
                // Assume complete unless we find a sco that is not complete.
                $status = COMPLETION_COMPLETE;
                $query = $basequery .
                    " AND element IN (
                          'cmi.core.lesson_status',
                          'cmi.completion_status',
                          'cmi.success_status'
                    )";

                $tracks = $DB->get_records_sql($query, [$this->cm->instance, $this->userid]);

                // Get available status list.
                $statuses = array_flip(\scorm_status_options());

                // Make a list of all sco IDs.
                $scoids = [];
                foreach ($tracks as $track) {
                    if (array_key_exists($track->value, $statuses)) {
                        $scoids[] = $track->scoid;
                    }
                }

                // Iterate over all scos and ensure each has a lesson_status.
                $scos = $DB->get_records('scorm_scoes', ['scorm' => $this->cm->instance, 'scormtype' => 'sco']);

                foreach ($scos as $sco) {
                    // If we find a sco without a lesson status, this condition is not completed.
                    if (!in_array($sco->id, $scoids)) {
                        $status = COMPLETION_INCOMPLETE;
                    }
                }

                break;

            default:
                $status = COMPLETION_INCOMPLETE;
                break;
        }

        // If not yet meeting the requirement and no attempts remain to complete it, mark it as failed.
        if ($status === COMPLETION_INCOMPLETE) {
            $scorm = $DB->get_record('scorm', ['id' => $this->cm->instance]);
            $attemptcount = scorm_get_attempt_count($this->userid, $scorm);

            if ($scorm->maxattempt > 0 && $attemptcount >= $scorm->maxattempt) {
                $status = COMPLETION_COMPLETE_FAIL;
            }
        }

        return $status;
    }

    /**
     * Fetch the list of custom completion rules that this module defines.
     *
     * @return array
     */
    public static function get_defined_custom_rules(): array {
        return [
            'completionstatusrequired',
            'completionscorerequired',
            'completionstatusallscos',
        ];
    }

    /**
     * Returns an associative array of the descriptions of custom completion rules.
     *
     * @return array
     */
    public function get_custom_rule_descriptions(): array {
        $scorerequired = $this->cm->customdata['customcompletionrules']['completionscorerequired'] ?? 0;

        // Prepare completion status requirements string.
        $statusstrings = \scorm_status_options();
        $completionstatusid = $this->cm->customdata['customcompletionrules']['completionstatusrequired'] ?? 0;

        if (array_key_exists($completionstatusid, $statusstrings)) {
            // Single status required.
            $statusrequired = $statusstrings[$completionstatusid];
        } else {
            // All statuses required.
            $statusrequired = 'completedandpassed';
        }

        return [
            'completionstatusrequired' => get_string("completiondetail:completionstatus{$statusrequired}", 'scorm'),
            'completionscorerequired' => get_string('completiondetail:completionscore', 'scorm', $scorerequired),
            'completionstatusallscos' => get_string('completiondetail:allscos', 'scorm'),
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
            'completionstatusallscos',
            'completionstatusrequired',
            'completionusegrade',
            'completionscorerequired',
        ];
    }
}

