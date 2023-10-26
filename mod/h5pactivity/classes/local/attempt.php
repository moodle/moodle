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
 * H5P activity attempt object
 *
 * @package    mod_h5pactivity
 * @since      Moodle 3.9
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_h5pactivity\local;

use stdClass;
use core_xapi\local\statement;

/**
 * Class attempt for H5P activity
 *
 * @package    mod_h5pactivity
 * @since      Moodle 3.9
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class attempt {

    /** @var stdClass the h5pactivity_attempts record. */
    private $record;

    /** @var boolean if the DB statement has been updated. */
    private $scoreupdated = false;

    /**
     * Create a new attempt object.
     *
     * @param stdClass $record the h5pactivity_attempts record
     */
    public function __construct(stdClass $record) {
        $this->record = $record;
        $this->results = null;
    }

    /**
     * Create a new user attempt in a specific H5P activity.
     *
     * @param stdClass $user a user record
     * @param stdClass $cm a course_module record
     * @return attempt|null a new attempt object or null if fail
     */
    public static function new_attempt(stdClass $user, stdClass $cm): ?attempt {
        global $DB;
        $record = new stdClass();
        $record->h5pactivityid = $cm->instance;
        $record->userid = $user->id;
        $record->timecreated = time();
        $record->timemodified = $record->timecreated;
        $record->rawscore = 0;
        $record->maxscore = 0;
        $record->duration = 0;
        $record->completion = null;
        $record->success = null;

        // Get last attempt number.
        $conditions = ['h5pactivityid' => $cm->instance, 'userid' => $user->id];
        $countattempts = $DB->count_records('h5pactivity_attempts', $conditions);
        $record->attempt = $countattempts + 1;

        $record->id = $DB->insert_record('h5pactivity_attempts', $record);
        if (!$record->id) {
            return null;
        }
        return new attempt($record);
    }

    /**
     * Get the last user attempt in a specific H5P activity.
     *
     * If no previous attempt exists, it generates a new one.
     *
     * @param stdClass $user a user record
     * @param stdClass $cm a course_module record
     * @return attempt|null a new attempt object or null if some problem accured
     */
    public static function last_attempt(stdClass $user, stdClass $cm): ?attempt {
        global $DB;
        $conditions = ['h5pactivityid' => $cm->instance, 'userid' => $user->id];
        $records = $DB->get_records('h5pactivity_attempts', $conditions, 'attempt DESC', '*', 0, 1);
        if (empty($records)) {
            return self::new_attempt($user, $cm);
        }
        return new attempt(array_shift($records));
    }

    /**
     * Wipe all attempt data for specific course_module and an optional user.
     *
     * @param stdClass $cm a course_module record
     * @param stdClass $user a user record
     */
    public static function delete_all_attempts(stdClass $cm, stdClass $user = null): void {
        global $DB;

        $where = 'a.h5pactivityid = :h5pactivityid';
        $conditions = ['h5pactivityid' => $cm->instance];
        if (!empty($user)) {
            $where .= ' AND a.userid = :userid';
            $conditions['userid'] = $user->id;
        }

        $DB->delete_records_select('h5pactivity_attempts_results', "attemptid IN (
                SELECT a.id
                FROM {h5pactivity_attempts} a
                WHERE $where)", $conditions);

        $DB->delete_records('h5pactivity_attempts', $conditions);
    }

    /**
     * Delete a specific attempt.
     *
     * @param attempt $attempt the attempt object to delete
     */
    public static function delete_attempt(attempt $attempt): void {
        global $DB;
        $attempt->delete_results();
        $DB->delete_records('h5pactivity_attempts', ['id' => $attempt->get_id()]);
    }

    /**
     * Save a new result statement into the attempt.
     *
     * It also updates the rawscore and maxscore if necessary.
     *
     * @param statement $statement the xAPI statement object
     * @param string $subcontent = '' optional subcontent identifier
     * @return bool if it can save the statement into db
     */
    public function save_statement(statement $statement, string $subcontent = ''): bool {
        global $DB;

        // Check statement data.
        $xapiobject = $statement->get_object();
        if (empty($xapiobject)) {
            return false;
        }
        $xapiresult = $statement->get_result();
        $xapidefinition = $xapiobject->get_definition();
        if (empty($xapidefinition) || empty($xapiresult)) {
            return false;
        }

        $xapicontext = $statement->get_context();
        if ($xapicontext) {
            $context = $xapicontext->get_data();
        } else {
            $context = new stdClass();
        }
        $definition = $xapidefinition->get_data();
        $result = $xapiresult->get_data();
        $duration = $xapiresult->get_duration();

        // Insert attempt_results record.
        $record = new stdClass();
        $record->attemptid = $this->record->id;
        $record->subcontent = $subcontent;
        $record->timecreated = time();
        $record->interactiontype = $definition->interactionType ?? 'other';
        $record->description = $this->get_description_from_definition($definition);
        $record->correctpattern = $this->get_correctpattern_from_definition($definition);
        $record->response = $result->response ?? '';
        $record->additionals = $this->get_additionals($definition, $context);
        $record->rawscore = 0;
        $record->maxscore = 0;
        if (isset($result->score)) {
            $record->rawscore = $result->score->raw ?? 0;
            $record->maxscore = $result->score->max ?? 0;
        }
        $record->duration = $duration;
        if (isset($result->completion)) {
            $record->completion = ($result->completion) ? 1 : 0;
        }
        if (isset($result->success)) {
            $record->success = ($result->success) ? 1 : 0;
        }
        if (!$DB->insert_record('h5pactivity_attempts_results', $record)) {
            return false;
        }

        // If no subcontent provided, results are propagated to the attempt itself.
        if (empty($subcontent)) {
            $this->set_duration($record->duration);
            $this->set_completion($record->completion ?? null);
            $this->set_success($record->success ?? null);
            // If Maxscore is not empty means that the rawscore is valid (even if it's 0)
            // and scaled score can be calculated.
            if ($record->maxscore) {
                $this->set_score($record->rawscore, $record->maxscore);
            }
        }
        // Refresh current attempt.
        return $this->save();
    }

    /**
     * Update the current attempt record into DB.
     *
     * @return bool true if update is succesful
     */
    public function save(): bool {
        global $DB;
        $this->record->timemodified = time();
        // Calculate scaled score.
        if ($this->scoreupdated) {
            if (empty($this->record->maxscore)) {
                $this->record->scaled = 0;
            } else {
                $this->record->scaled = $this->record->rawscore / $this->record->maxscore;
            }
        }
        return $DB->update_record('h5pactivity_attempts', $this->record);
    }

    /**
     * Set the attempt score.
     *
     * @param int|null $rawscore the attempt rawscore
     * @param int|null $maxscore the attempt maxscore
     */
    public function set_score(?int $rawscore, ?int $maxscore): void {
        $this->record->rawscore = $rawscore;
        $this->record->maxscore = $maxscore;
        $this->scoreupdated = true;
    }

    /**
     * Set the attempt duration.
     *
     * @param int|null $duration the attempt duration
     */
    public function set_duration(?int $duration): void {
        $this->record->duration = $duration;
    }

    /**
     * Set the attempt completion.
     *
     * @param int|null $completion the attempt completion
     */
    public function set_completion(?int $completion): void {
        $this->record->completion = $completion;
    }

    /**
     * Set the attempt success.
     *
     * @param int|null $success the attempt success
     */
    public function set_success(?int $success): void {
        $this->record->success = $success;
    }

    /**
     * Delete the current attempt results from the DB.
     */
    public function delete_results(): void {
        global $DB;
        $conditions = ['attemptid' => $this->record->id];
        $DB->delete_records('h5pactivity_attempts_results', $conditions);
    }

    /**
     * Return de number of results stored in this attempt.
     *
     * @return int the number of results stored in this attempt.
     */
    public function count_results(): int {
        global $DB;
        $conditions = ['attemptid' => $this->record->id];
        return $DB->count_records('h5pactivity_attempts_results', $conditions);
    }

    /**
     * Return all results stored in this attempt.
     *
     * @return stdClass[] results records.
     */
    public function get_results(): array {
        global $DB;
        $conditions = ['attemptid' => $this->record->id];
        return $DB->get_records('h5pactivity_attempts_results', $conditions, 'id ASC');
    }

    /**
     * Get additional data for some interaction types.
     *
     * @param stdClass $definition the statement object definition data
     * @param stdClass $context the statement optional context
     * @return string JSON encoded additional information
     */
    private function get_additionals(stdClass $definition, stdClass $context): string {
        $additionals = [];
        $interactiontype = $definition->interactionType ?? 'other';
        switch ($interactiontype) {
            case 'choice':
            case 'sequencing':
                $additionals['choices'] = $definition->choices ?? [];
            break;

            case 'matching':
                $additionals['source'] = $definition->source ?? [];
                $additionals['target'] = $definition->target ?? [];
            break;

            case 'likert':
                $additionals['scale'] = $definition->scale ?? [];
            break;

            case 'performance':
                $additionals['steps'] = $definition->steps ?? [];
            break;
        }

        $additionals['extensions'] = $definition->extensions ?? new stdClass();

        // Add context extensions.
        $additionals['contextExtensions'] = $context->extensions ?? new stdClass();

        if (empty($additionals)) {
            return '';
        }
        return json_encode($additionals);
    }

    /**
     * Extract the result description from statement object definition.
     *
     * In principle, H5P package can send a multilang description but the reality
     * is that most activities only send the "en_US" description if any and the
     * activity does not have any control over it.
     *
     * @param stdClass $definition the statement object definition
     * @return string The available description if any
     */
    private function get_description_from_definition(stdClass $definition): string {
        if (!isset($definition->description)) {
            return '';
        }
        $translations = (array) $definition->description;
        if (empty($translations)) {
            return '';
        }
        // By default, H5P packages only send "en-US" descriptions.
        return $translations['en-US'] ?? array_shift($translations);
    }

    /**
     * Extract the correct pattern from statement object definition.
     *
     * The correct pattern depends on the type of content and the plugin
     * has no control over it so we just store it in case that the statement
     * data have it.
     *
     * @param stdClass $definition the statement object definition
     * @return string The correct pattern if any
     */
    private function get_correctpattern_from_definition(stdClass $definition): string {
        if (!isset($definition->correctResponsesPattern)) {
            return '';
        }
        // Only arrays are allowed.
        if (is_array($definition->correctResponsesPattern)) {
            return json_encode($definition->correctResponsesPattern);
        }
        return '';
    }

    /**
     * Return the attempt number.
     *
     * @return int the attempt number
     */
    public function get_attempt(): int {
        return $this->record->attempt;
    }

    /**
     * Return the attempt ID.
     *
     * @return int the attempt id
     */
    public function get_id(): int {
        return $this->record->id;
    }

    /**
     * Return the attempt user ID.
     *
     * @return int the attempt userid
     */
    public function get_userid(): int {
        return $this->record->userid;
    }

    /**
     * Return the attempt H5P timecreated.
     *
     * @return int the attempt timecreated
     */
    public function get_timecreated(): int {
        return $this->record->timecreated;
    }

    /**
     * Return the attempt H5P timemodified.
     *
     * @return int the attempt timemodified
     */
    public function get_timemodified(): int {
        return $this->record->timemodified;
    }

    /**
     * Return the attempt H5P activity ID.
     *
     * @return int the attempt userid
     */
    public function get_h5pactivityid(): int {
        return $this->record->h5pactivityid;
    }

    /**
     * Return the attempt maxscore.
     *
     * @return int the maxscore value
     */
    public function get_maxscore(): int {
        return $this->record->maxscore;
    }

    /**
     * Return the attempt rawscore.
     *
     * @return int the rawscore value
     */
    public function get_rawscore(): int {
        return $this->record->rawscore;
    }

    /**
     * Return the attempt duration.
     *
     * @return int|null the duration value
     */
    public function get_duration(): ?int {
        return $this->record->duration;
    }

    /**
     * Return the attempt completion.
     *
     * @return int|null the completion value
     */
    public function get_completion(): ?int {
        return $this->record->completion;
    }

    /**
     * Return the attempt success.
     *
     * @return int|null the success value
     */
    public function get_success(): ?int {
        return $this->record->success;
    }

    /**
     * Return the attempt scaled.
     *
     * @return int|null the scaled value
     */
    public function get_scaled(): ?int {
        return is_null($this->record->scaled) ? $this->record->scaled : (int)$this->record->scaled;
    }

    /**
     * Return if the attempt has been modified.
     *
     * Note: adding a result only add track information unless the statement does
     * not specify subcontent. In this case this will update also the statement.
     *
     * @return bool if the attempt score have been modified
     */
    public function get_scoreupdated(): bool {
        return $this->scoreupdated;
    }
}
