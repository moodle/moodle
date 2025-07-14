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

namespace factor_token;

use stdClass;
use tool_mfa\local\factor\object_factor_base;
use tool_mfa\local\secret_manager;

/**
 * Token factor class.
 *
 * @package     factor_token
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class factor extends object_factor_base {

    /**
     * Token implementation.
     *
     * {@inheritDoc}
     */
    public function has_input(): bool {
        return false;
    }

    /**
     * Token implementation.
     * This factor is a singleton, return single instance.
     *
     * @param stdClass $user the user to check against.
     * @return array
     */
    public function get_all_user_factors(stdClass $user): array {
        global $DB;
        $records = $DB->get_records('tool_mfa', ['userid' => $user->id, 'factor' => $this->name]);

        if (!empty($records)) {
            return $records;
        }

        // Null records returned, build new record.
        $record = [
            'userid' => $user->id,
            'factor' => $this->name,
            'timecreated' => time(),
            'createdfromip' => $user->lastip,
            'timemodified' => time(),
            'revoked' => 0,
        ];
        $record['id'] = $DB->insert_record('tool_mfa', $record, true);
        return [(object) $record];
    }

    /**
     * Token implementation.
     * Checks whether the user has selected roles in any context.
     *
     * {@inheritDoc}
     */
    public function get_state(): string {
        global $USER;

        // Check if there was a previous locked status to return.
        $state = parent::get_state();
        if ($state === \tool_mfa\plugininfo\factor::STATE_LOCKED) {
            return \tool_mfa\plugininfo\factor::STATE_LOCKED;
        }

        // Check cookie Exists.
        $cookie = 'MFA_TOKEN_' . $USER->id;
        if (NO_MOODLE_COOKIES || empty($_COOKIE[$cookie])) {
            return \tool_mfa\plugininfo\factor::STATE_NEUTRAL;
        }
        $token = $_COOKIE[$cookie];

        $secretmanager = new secret_manager($this->name);
        $verified = $secretmanager->validate_secret($token, true);

        // If we got a bad cookie value, someone is likely being dodgy.
        // In this instance we should just lock and make the user re-MFA.
        if ($verified === secret_manager::NONVALID) {
            $this->set_state(\tool_mfa\plugininfo\factor::STATE_LOCKED);
            return \tool_mfa\plugininfo\factor::STATE_LOCKED;
        } else if ($verified === secret_manager::VALID) {
            return \tool_mfa\plugininfo\factor::STATE_PASS;
        }

        // We should never get here. Factor cannot be revoked.
        return \tool_mfa\plugininfo\factor::STATE_NEUTRAL;
    }

    /**
     * Token Implementation.
     * We can't get_state like the parent here or it will recurse forever.
     *
     * @param string $state the state constant to set
     * @return bool
     */
    public function set_state($state): bool {
        global $SESSION;
        $property = 'factor_' . $this->name;
        $SESSION->$property = $state;
        return true;
    }

    /**
     * Token implementation.
     *
     * @param stdClass $user
     * @return array
     */
    public function possible_states(stdClass $user): array {
        return [
            \tool_mfa\plugininfo\factor::STATE_PASS,
            \tool_mfa\plugininfo\factor::STATE_NEUTRAL,
            \tool_mfa\plugininfo\factor::STATE_LOCKED,
        ];
    }

    /**
     * Token implementation.
     * Inject a checkbox into every auth form if needed.
     *
     * @param \MoodleQuickForm $mform Form to inject global elements into.
     * @return void
     */
    public function global_definition_after_data($mform): void {
        global $SESSION;

        // First thing, we need to decide on whether we should show the checkbox.
        $noproperty = !property_exists($SESSION, 'tool_mfa_factor_token');
        $nostate = $this->get_state() !== \tool_mfa\plugininfo\factor::STATE_PASS;

        if ($noproperty && $nostate) {
            $expiry = get_config('factor_token', 'expiry');
            $expirystring = format_time($expiry);
            $mform->addElement('advcheckbox', 'factor_token_trust', '', get_string('form:trust', 'factor_token', $expirystring));
            $mform->setType('factor_token_trust', PARAM_BOOL);
            $mform->setDefault('factor_token_trust', true);
        }
    }

    /**
     * Token implementation.
     * Store information about the token status.
     *
     * @param object $data Data from the form.
     * @return void
     */
    public function global_submit($data): void {
        global $SESSION;

        // Store any kind of response here, we shouldnt show again.
        $trust = $data->factor_token_trust;
        $SESSION->tool_mfa_factor_token = $trust;
    }

    /**
     * Token implementation.
     * Pass hook to set the cookie for use in subsequent auths.
     *
     * {@inheritDoc}
     */
    public function post_pass_state(): void {
        global $CFG, $SESSION, $USER;

        if (!property_exists($SESSION, 'tool_mfa_factor_token')) {
            return;
        }
        $settoken = $SESSION->tool_mfa_factor_token;
        if (!$settoken) {
            return;
        }
        $cookie = 'MFA_TOKEN_' . $USER->id;

        list($expirytime, $expiry) = $this->calculate_expiry_time();

        // Store this secret in the database.
        $secretmanager = new secret_manager($this->name);
        $secret = base64_encode(random_bytes(256));
        $secretmanager->create_secret($expiry, false, $secret);

        // All the prep is now done, we can set this cookie.
        setcookie($cookie, $secret, $expirytime, $CFG->sessioncookiepath, $CFG->sessioncookiedomain, false, true);

        // Finally emit a log event for storing the cookie.
        $state = [
            'expiry' => $expirytime,
            'cookie' => $cookie,
        ];
        $event = \factor_token\event\token_created::token_created_event($USER, $state);
        $event->trigger();
    }

    /**
     * Calculate the expiry time of the token, based on configuration.
     *
     * @param integer|null $basetime time to use for calcalations.
     * @return array
     */
    public function calculate_expiry_time($basetime = null): array {
        if (empty($basetime)) {
            $basetime = time();
        }

        // Calculate the expiry time. This is provided by config,
        // But optionally might need to be rounded  to expire a few hours after 0000 server time.
        $expiry = get_config('factor_token', 'expiry');
        $expirytime = $basetime + $expiry;

        // If expiring overnight, it should expire at 2am the following morning, if required.
        $expireovernight = get_config('factor_token', 'expireovernight');
        if ($expireovernight) {
            // Find out what 2am the following morning time is.
            $datetime = new \DateTime();
            $timezone = \core_date::get_user_timezone_object();

            // Bit to ensure 'expireovernight' works when 'expire' is longer than one day.
            $difftime = 0;
            if ($expiry > DAYSECS) {
                // Ensures a safe amount of days is added before doing the 2am checks.
                $difftime = $expiry - DAYSECS;
            }

            // Calculte the overnight expiry time, ignoring 'expiry' duration period.
            $workingexpirytime = $basetime + $difftime;
            $datetime->setTimezone($timezone);
            $datetime->setTimestamp($workingexpirytime);
            $datetime->add(new \DateInterval('P1D'));
            $datetime->setTime(2, 0); // Set the hour to 2am.

            // Ensure whatever happens, ensure the expiry never goes over the default 'expiry' time.
            $overnightexpirytime = $datetime->getTimestamp();
            $expirytime = min($overnightexpirytime, $expirytime);
            $expiry = $expirytime - $basetime;
        }

        return [$expirytime, $expiry];
    }
}
