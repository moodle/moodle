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

namespace factor_webauthn;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/webauthn/src/WebAuthn.php');

use lbuchs\WebAuthn\Binary\ByteBuffer;
use lbuchs\WebAuthn\WebAuthn;
use lbuchs\WebAuthn\WebAuthnException;
use stdClass;
use tool_mfa\local\factor\object_factor_base;

/**
 * WebAuthn factor class.
 *
 * @package     factor_webauthn
 * @author      Alex Morris <alex.morris@catalyst.net.nz>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class factor extends object_factor_base {

    /** @var WebAuthn WebAuthn server */
    private $webauthn;
    /** @var string Relying party ID */
    private $rpid;
    /** @var string User verification setting */
    private $userverification;

    /** @var string Factor icon */
    protected $icon = 'fa-hand-pointer';

    /**
     * Create webauthn server.
     *
     * @param string $name
     */
    public function __construct($name) {
        global $CFG, $SITE;
        parent::__construct($name);

        $this->rpid = (new \moodle_url($CFG->wwwroot))->get_host();
        $this->webauthn = new WebAuthn($SITE->fullname, $this->rpid);

        $this->userverification = get_config('factor_webauthn', 'userverification');
    }

    /**
     * WebAuthn Factor implementation.
     *
     * @param stdClass $user the user to check against.
     * @return array
     */
    public function get_all_user_factors(stdClass $user): array {
        global $DB;
        return $DB->get_records('tool_mfa', ['userid' => $user->id, 'factor' => $this->name]);
    }

    /**
     * WebAuthn Factor implementation.
     *
     * {@inheritDoc}
     */
    public function has_input(): bool {
        return true;
    }

    /**
     * WebAuthn Factor implementation.
     *
     * {@inheritDoc}
     */
    public function has_revoke(): bool {
        return true;
    }

    /**
     * WebAuthn Factor implementation.
     */
    public function has_replace(): bool {
        return true;
    }

    /**
     * WebAuthn Factor implementation.
     *
     * {@inheritDoc}
     */
    public function has_setup(): bool {
        return true;
    }

    /**
     * WebAuthn Factor implementation.
     *
     * {@inheritDoc}
     */
    public function show_setup_buttons(): bool {
        return true;
    }

    /**
     * WebAuthn factor implementation.
     *
     * @param stdClass $user
     * @return array
     */
    public function possible_states(stdClass $user): array {
        return [
            \tool_mfa\plugininfo\factor::STATE_PASS,
            \tool_mfa\plugininfo\factor::STATE_NEUTRAL,
            \tool_mfa\plugininfo\factor::STATE_UNKNOWN,
        ];
    }

    /**
     * WebAuthn state
     *
     * {@inheritDoc}
     */
    public function get_state(): string {
        global $USER;
        $userfactors = $this->get_active_user_factors($USER);

        // If no authenticators are set up then we are neutral not unknown.
        if (count($userfactors) == 0) {
            return \tool_mfa\plugininfo\factor::STATE_NEUTRAL;
        }

        return parent::get_state();
    }

    /**
     * Gets the string for setup button on preferences page.
     *
     * @return string
     */
    public function get_setup_string(): string {
        return get_string('setupfactorbutton', 'factor_webauthn');
    }

    /**
     * Gets the string for manage button on preferences page.
     *
     * @return string
     */
    public function get_manage_string(): string {
        return get_string('managefactorbutton', 'factor_webauthn');
    }

    /**
     * WebAuthn Factor implementation.
     *
     * @param \MoodleQuickForm $mform
     * @return \MoodleQuickForm $mform
     */
    public function login_form_definition(\MoodleQuickForm $mform): \MoodleQuickForm {
        global $PAGE, $USER, $SESSION;

        $mform->addElement('hidden', 'response_input', '', ['id' => 'id_response_input']);
        $mform->setType('response_input', PARAM_RAW);

        // Required to attach verification errors, so they can be displayed to the user.
        $mform->addElement('static', 'verificationcode', '', '');

        $ids = [];

        $authenticators = $this->get_active_user_factors($USER);
        foreach ($authenticators as $authenticator) {
            $registration = json_decode($authenticator->secret);
            $ids[] = base64_decode($registration->credentialId);
        }

        $types = explode(',', get_config('factor_webauthn', 'authenticatortypes'));
        $getargs =
            $this->webauthn->getGetArgs($ids, 20, in_array('usb', $types), in_array('nfc', $types), in_array('ble', $types),
                in_array('hybrid', $types), in_array('internal', $types), $this->userverification);

        $PAGE->requires->js_call_amd('factor_webauthn/login', 'init', [json_encode($getargs)]);

        // Challenge is regenerated on form submission, at this point we aren't aware if the form is submitted for being
        // loaded for the first time, so we store the existing and new challenge.
        if (isset($SESSION->factor_webauthn_challenge_new)) {
            $SESSION->factor_webauthn_challenge = $SESSION->factor_webauthn_challenge_new;
        }
        $SESSION->factor_webauthn_challenge_new = $this->webauthn->getChallenge()->getHex();

        return $mform;
    }

    /**
     * WebAuthn Factor implementation.
     *
     * @param array $data
     * @return array
     */
    public function login_form_validation(array $data): array {
        global $USER, $SESSION;

        $errors = [];
        if (empty($data['response_input'])) {
            $errors['verificationcode'] = get_string('error', 'factor_webauthn');
            return $errors;
        }

        $post = json_decode($data['response_input'], null, 512, JSON_THROW_ON_ERROR);

        $id = base64_decode($post->id);
        $clientdata = base64_decode($post->clientDataJSON);
        $authenticatordata = base64_decode($post->authenticatorData);
        $signature = base64_decode($post->signature);
        $credentialpublickey = null;
        $challenge = ByteBuffer::fromHex($SESSION->factor_webauthn_challenge);
        unset($SESSION->factor_webauthn_challenge);

        $authenticators = $this->get_active_user_factors($USER);
        foreach ($authenticators as $authenticator) {
            $registration = json_decode($authenticator->secret);
            if (base64_decode($registration->credentialId) === $id) {
                $credentialpublickey = $registration->credentialPublicKey;
                break;
            }
        }

        if ($credentialpublickey === null) {
            $errors['verificationcode'] = get_string('error', 'factor_webauthn');
            return $errors;
        }

        try {
            // Throws exception if authentication fails.
            $this->webauthn->processGet($clientdata, $authenticatordata, $signature, $credentialpublickey, $challenge, null,
                $this->userverification === 'required');
        } catch (WebAuthnException $ex) {
            $errors['verificationcode'] = get_string('error', 'factor_webauthn');
        }

        return $errors;
    }

    /**
     * WebAuthn Factor implementation.
     *
     * @param \MoodleQuickForm $mform
     * @return \MoodleQuickForm $mform
     */
    public function setup_factor_form_definition(\MoodleQuickForm $mform): \MoodleQuickForm {
        global $PAGE, $USER, $SESSION, $OUTPUT;

        $headingstring = $mform->elementExists('replaceid') ? 'replacefactor' : 'setupfactor';
        $mform->addElement('html', $OUTPUT->heading(get_string($headingstring, 'factor_webauthn'), 2));

        $html = \html_writer::tag('p', get_string('setupfactor:intro', 'factor_webauthn'));
        $mform->addElement('html', $html);

        // Security key name.
        $mform->addElement('html', \html_writer::tag('p', get_string('setupfactor:instructionssecuritykeyname', 'factor_webauthn'),
            ['class' => 'bold']));

        $mform->addElement('text', 'webauthn_name', get_string('authenticatorname', 'factor_webauthn'));
        $mform->setType('webauthn_name', PARAM_TEXT);
        $mform->addRule('webauthn_name', get_string('required'), 'required', null, 'client');

        $html = \html_writer::tag('p', get_string('setupfactor:securitykeyinfo', 'factor_webauthn'));
        $mform->addElement('static', 'devicenameinfo', '', $html);

        // Register security key.
        $mform->addElement('html', \html_writer::tag('p',
            get_string('setupfactor:instructionsregistersecuritykey', 'factor_webauthn'), ['class' => 'bold']));

        $registerbtn = \html_writer::tag('btn', get_string('register', 'factor_webauthn'), [
            'class' => 'btn btn-primary',
            'type' => 'button',
            'id' => 'factor_webauthn-register',
            'tabindex' => '0',
        ]);
        $mform->addElement('static', 'register', '', $registerbtn);

        $mform->addElement('hidden', 'response_input', '', ['id' => 'id_response_input']);
        $mform->setType('response_input', PARAM_RAW);
        $mform->addRule('response_input', get_string('required'), 'required', null, 'client');

        // Cross-platform: true if type internal is not allowed,
        // false if only internal is allowed,
        // null if internal and cross-platform is allowed.
        $types = explode(',', get_config('factor_webauthn', 'authenticatortypes'));
        $crossplatformattachment = null;
        if ((in_array('usb', $types) || in_array('nfc', $types) || in_array('ble', $types) || in_array('hybrid', $types)) &&
            !in_array('internal', $types)) {
            $crossplatformattachment = true;
        } else if (!in_array('usb', $types) && !in_array('nfc', $types) && !in_array('ble', $types) &&
            !in_array('hybrid', $types) && in_array('internal', $types)) {
            $crossplatformattachment = false;
        }

        $createargs = $this->webauthn->getCreateArgs($USER->id, $USER->username, fullname($USER), 20, false,
            $this->userverification, $crossplatformattachment);

        $PAGE->requires->js_call_amd('factor_webauthn/register', 'init', [json_encode($createargs)]);

        // Challenge is regenerated on form submission, at this point we aren't aware if the form is submitted for being
        // loaded for the first time, so we store the existing and new challenge.
        if (isset($SESSION->factor_webauthn_challenge_new)) {
            $SESSION->factor_webauthn_challenge = $SESSION->factor_webauthn_challenge_new;
        }
        $SESSION->factor_webauthn_challenge_new = $this->webauthn->getChallenge()->getHex();

        return $mform;
    }

    /**
     * WebAuthn Factor implementation.
     *
     * @param object $data
     * @return stdClass|null
     */
    public function setup_user_factor(object $data): stdClass|null {
        global $DB, $USER, $SESSION;

        if (!empty($data->webauthn_name) && !empty($data->response_input) && isset($SESSION->factor_webauthn_challenge)) {
            $post = json_decode($data->response_input, null, 512, JSON_THROW_ON_ERROR);

            $clientdata = base64_decode($post->clientDataJSON);
            $attestationobject = base64_decode($post->attestationObject);
            $challenge = ByteBuffer::fromHex($SESSION->factor_webauthn_challenge);
            unset($SESSION->factor_webauthn_challenge);

            $registration =
                $this->webauthn->processCreate($clientdata, $attestationobject, $challenge, $this->userverification === 'required',
                    true, false);
            $registration->credentialId = base64_encode($registration->credentialId);
            $registration->AAGUID = base64_encode($registration->AAGUID);
            unset($registration->certificate);

            $row = new \stdClass();
            $row->userid = $USER->id;
            $row->factor = $this->name;
            $row->label = $data->webauthn_name;
            $row->secret = json_encode($registration);
            $row->timecreated = time();
            $row->createdfromip = $USER->lastip;
            $row->timemodified = time();
            $row->lastverified = time();
            $row->revoked = 0;

            // Check if a record with this configuration already exists, warning the user accordingly.
            $record = $DB->get_record('tool_mfa', [
                'userid' => $row->userid,
                'secret' => $row->secret,
                'factor' => $row->factor,
            ], '*', IGNORE_MULTIPLE);
            if ($record) {
                \core\notification::warning(get_string('error:alreadyregistered', 'factor_webauthn'));
                return null;
            }

            $id = $DB->insert_record('tool_mfa', $row);
            $record = $DB->get_record('tool_mfa', array('id' => $id));
            $this->create_event_after_factor_setup($USER);

            return $record;
        }
        return null;
    }

    /**
     * WebAuthn Factor implementation with replacement of existing factor.
     *
     * @param stdClass $data The new factor data.
     * @param int $id The id of the factor to replace.
     * @return stdClass|null the factor record, or null.
     */
    public function replace_user_factor(stdClass $data, int $id): stdClass|null {
        global $DB, $USER;

        $oldrecord = $DB->get_record('tool_mfa', ['id' => $id]);
        $newrecord = null;

        // Ensure we have a valid existing record before setting the new one.
        if ($oldrecord) {
            $newrecord = $this->setup_user_factor($data);
        }
        // Ensure the new record was created before revoking the old.
        if ($newrecord) {
            $this->revoke_user_factor($id);
        } else {
            \core\notification::warning(get_string('error:couldnotreplace', 'tool_mfa'));
            return null;
        }
        $this->create_event_after_factor_setup($USER);

        return $newrecord ?? null;
    }

}
