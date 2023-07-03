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

namespace factor_email;

use tool_mfa\local\factor\object_factor_base;

/**
 * Email factor class.
 *
 * @package     factor_email
 * @subpackage  tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class factor extends object_factor_base {

    /**
     * E-Mail Factor implementation.
     *
     * @param \MoodleQuickForm $mform
     * @return object $mform
     */
    public function login_form_definition($mform) {

        $mform->addElement('text', 'verificationcode', get_string('verificationcode', 'factor_email'));
        $mform->setType('verificationcode', PARAM_ALPHANUM);
        return $mform;
    }

    /**
     * E-Mail Factor implementation.
     *
     * @param \MoodleQuickForm $mform Form to inject global elements into.
     * @return object $mform
     */
    public function login_form_definition_after_data($mform) {
        $this->generate_and_email_code();
        return $mform;
    }

    /**
     * Sends and e-mail to user with given verification code.
     *
     * @param int $instanceid
     */
    public static function email_verification_code($instanceid) {
        global $PAGE, $USER;
        $noreplyuser = \core_user::get_noreply_user();
        $subject = get_string('email:subject', 'factor_email');
        $renderer = $PAGE->get_renderer('factor_email');
        $body = $renderer->generate_email($instanceid);
        email_to_user($USER, $noreplyuser, $subject, $body, $body);
    }

    /**
     * E-Mail Factor implementation.
     *
     * @param array $data
     * @return array
     */
    public function login_form_validation($data) {
        global $USER;
        $return = [];

        if (!$this->check_verification_code($data['verificationcode'])) {
            $return['verificationcode'] = get_string('error:wrongverification', 'factor_email');
        }

        return $return;
    }

    /**
     * E-Mail Factor implementation.
     *
     * @param stdClass $user the user to check against.
     * @return array
     */
    public function get_all_user_factors($user) {
        global $DB;

        $records = $DB->get_records('tool_mfa', [
            'userid' => $user->id,
            'factor' => $this->name,
            'label' => $user->email,
        ]);

        if (!empty($records)) {
            return $records;
        }

        // Null records returned, build new record.
        $record = [
            'userid' => $user->id,
            'factor' => $this->name,
            'label' => $user->email,
            'createdfromip' => $user->lastip,
            'timecreated' => time(),
            'revoked' => 0,
        ];
        $record['id'] = $DB->insert_record('tool_mfa', $record, true);
        return [(object) $record];
    }

    /**
     * E-Mail Factor implementation.
     *
     * {@inheritDoc}
     */
    public function has_input() {
        if (self::is_ready()) {
            return true;
        }
        return false;
    }

    /**
     * E-Mail Factor implementation.
     *
     * {@inheritDoc}
     */
    public function get_state() {
        if (!self::is_ready()) {
            return \tool_mfa\plugininfo\factor::STATE_NEUTRAL;
        }

        return parent::get_state();
    }

    /**
     * Checks whether user email is correctly configured.
     *
     * @return bool
     */
    private static function is_ready() {
        global $DB, $USER;

        if (empty($USER->email)) {
            return false;
        }
        if (!validate_email($USER->email)) {
            return false;
        }
        if (over_bounce_threshold($USER)) {
            return false;
        }

        // If this factor is revoked, set to not ready.
        if ($DB->record_exists('tool_mfa', ['userid' => $USER->id, 'factor' => 'email', 'revoked' => 1])) {
            return false;
        }
        return true;
    }

    /**
     * Generates and emails the code for login to the user, stores codes in DB.
     *
     * @return void
     */
    private function generate_and_email_code() {
        global $DB, $USER;

        // Get instance that isnt parent email type (label check).
        // This check must exclude the main singleton record, with the label as the email.
        // It must only grab the record with the user agent as the label.
        $sql = 'SELECT *
                  FROM {tool_mfa}
                 WHERE userid = ?
                   AND factor = ?
               AND NOT label = ?';

        $record = $DB->get_record_sql($sql, [$USER->id, 'email', $USER->email]);
        $duration = get_config('factor_email', 'duration');
        $newcode = random_int(100000, 999999);

        if (empty($record)) {
            // No code active, generate new code.
            $instanceid = $DB->insert_record('tool_mfa', [
                'userid' => $USER->id,
                'factor' => 'email',
                'secret' => $newcode,
                'label' => $_SERVER['HTTP_USER_AGENT'],
                'timecreated' => time(),
                'createdfromip' => $USER->lastip,
                'timemodified' => time(),
                'lastverified' => time(),
                'revoked' => 0,
            ], true);
            $this->email_verification_code($instanceid);
        } else if ($record->timecreated + $duration < time()) {
            // Old code found. Keep id, update fields.
            $DB->update_record('tool_mfa', [
                'id' => $record->id,
                'secret' => $newcode,
                'label' => $_SERVER['HTTP_USER_AGENT'],
                'timecreated' => time(),
                'createdfromip' => $USER->lastip,
                'timemodified' => time(),
                'lastverified' => time(),
                'revoked' => 0,
            ]);
            $instanceid = $record->id;
            $this->email_verification_code($instanceid);
        }
    }

    /**
     * Verifies entered code against stored DB record.
     *
     * @param string $enteredcode
     * @return bool
     */
    private function check_verification_code($enteredcode) {
        global $DB, $USER;
        $duration = get_config('factor_email', 'duration');

        // Get instance that isnt parent email type (label check).
        // This check must exclude the main singleton record, with the label as the email.
        // It must only grab the record with the user agent as the label.
        $sql = 'SELECT *
                  FROM {tool_mfa}
                 WHERE userid = ?
                   AND factor = ?
               AND NOT label = ?';
        $record = $DB->get_record_sql($sql, [$USER->id, 'email', $USER->email]);

        if ($enteredcode == $record->secret) {
            if ($record->timecreated + $duration > time()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Cleans up email records once MFA passed.
     *
     * {@inheritDoc}
     */
    public function post_pass_state() {
        global $DB, $USER;
        // Delete all email records except base record.
        $selectsql = 'userid = ?
                  AND factor = ?
              AND NOT label = ?';
        $DB->delete_records_select('tool_mfa', $selectsql, [$USER->id, 'email', $USER->email]);

        // Update factor timeverified.
        parent::post_pass_state();
    }

    /**
     * Email factor implementation.
     * Email page must be safe to authorise session from link.
     *
     * {@inheritDoc}
     */
    public function get_no_redirect_urls() {
        $email = new \moodle_url('/admin/tool/mfa/factor/email/email.php');
        return [$email];
    }

    /**
     * Email factor implementation.
     *
     * @param \stdClass $user
     */
    public function possible_states($user) {
        // Email can return all states.
        return [
            \tool_mfa\plugininfo\factor::STATE_FAIL,
            \tool_mfa\plugininfo\factor::STATE_PASS,
            \tool_mfa\plugininfo\factor::STATE_NEUTRAL,
            \tool_mfa\plugininfo\factor::STATE_UNKNOWN,
        ];
    }
}
