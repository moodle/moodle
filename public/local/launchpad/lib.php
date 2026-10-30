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
 * Callbacks for local_launchpad.
 *
 * @package    local_launchpad
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Ensure Email self-registration is available before pages render.
 *
 * Covers sites that have not yet run the LaunchPad upgrade step.
 */
function local_launchpad_before_http_headers(): void {
    if (during_initial_install()) {
        return;
    }
    \local_launchpad\registration::ensure_email_self_registration(true);
    // On /login/index.php?portal=… pin gated wantsurl early (GET display + POST).
    \local_launchpad\login_portal::maybe_pin_wantsurl_from_request();
}

/**
 * Before signup form: force Fellowship portal on the session / URL.
 */
function local_launchpad_pre_signup_requests(): void {
    \local_launchpad\registration::prepare_signup_session();
}

/**
 * Inject Fellowship portal field into signup.
 *
 * @param \MoodleQuickForm $mform Signup form.
 */
function local_launchpad_extend_signup_form($mform): void {
    // Keep Moodle's native country <select> (accurate ISO list + empty default).
    // The Fellowship signup template enhances it into one searchable dropdown.
    if ($mform->elementExists('country')) {
        $mform->setDefault('country', '');
    }

    $mform->addElement('hidden', 'portal', \local_launchpad\registration::PORTAL_FELLOWSHIP);
    $mform->setType('portal', PARAM_ALPHA);
    $mform->setConstant('portal', \local_launchpad\registration::PORTAL_FELLOWSHIP);
}

/**
 * Fellowship-only self-registration validation.
 *
 * @param array $data Submitted signup data.
 * @return array Field errors.
 */
function local_launchpad_validate_extend_signup_form(array $data): array {
    $errors = [];
    $portal = \local_launchpad\registration::normalise_portal((string) ($data['portal'] ?? ''));
    if ($portal !== \local_launchpad\registration::PORTAL_FELLOWSHIP) {
        $errors['portal'] = get_string('signupportalinvalid', 'local_launchpad');
    }
    return $errors;
}

/**
 * After validated signup data: keep Fellowship portal on the session for user_created.
 *
 * Preference is written in the user_created observer (user id does not exist yet here).
 *
 * @param \stdClass $user Signup user data (no id yet).
 */
function local_launchpad_post_signup_requests($user): void {
    global $SESSION;

    $portal = \local_launchpad\registration::PORTAL_FELLOWSHIP;
    if (!empty($user->portal)) {
        $portal = \local_launchpad\registration::normalise_portal((string) $user->portal);
    }
    if ($portal !== \local_launchpad\registration::PORTAL_FELLOWSHIP) {
        $portal = \local_launchpad\registration::PORTAL_FELLOWSHIP;
    }
    $SESSION->{\local_launchpad\registration::SESSION_PORTAL} = $portal;
}
