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

namespace factor_totp;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tcpdf/tcpdf_barcodes_2d.php');
require_once(__DIR__.'/../extlib/OTPHP/OTPInterface.php');
require_once(__DIR__.'/../extlib/OTPHP/TOTPInterface.php');
require_once(__DIR__.'/../extlib/OTPHP/ParameterTrait.php');
require_once(__DIR__.'/../extlib/OTPHP/OTP.php');
require_once(__DIR__.'/../extlib/OTPHP/TOTP.php');

require_once(__DIR__.'/../extlib/ParagonIE/ConstantTime/EncoderInterface.php');
require_once(__DIR__.'/../extlib/ParagonIE/ConstantTime/Binary.php');
require_once(__DIR__.'/../extlib/ParagonIE/ConstantTime/Base32.php');

use MoodleQuickForm;
use tool_mfa\local\factor\object_factor_base;
use OTPHP\TOTP;
use stdClass;
use core\clock;
use core\context\system;
use core\di;

/**
 * TOTP factor class.
 *
 * @package     factor_totp
 * @subpackage  tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class factor extends object_factor_base {

    /** @var string */
    const TOTP_OLD = 'old';

    /** @var string */
    const TOTP_FUTURE = 'future';

    /** @var string */
    const TOTP_USED = 'used';

    /** @var string */
    const TOTP_VALID = 'valid';

    /** @var string */
    const TOTP_INVALID = 'invalid';

    /** @var string Factor icon */
    protected $icon = 'fa-mobile-screen';

    /** @var clock */
    private readonly clock $clock;

    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct(string $name) {
        parent::__construct($name);
        $this->clock = di::get(clock::class);
    }


    /**
     * Generates TOTP URI for given secret key.
     * Uses site name, hostname and user name to make GA account look like:
     * "Sitename hostname (username)".
     *
     * @param string $secret
     * @return string
     */
    public function generate_totp_uri(string $secret): string {
        global $USER, $SITE, $CFG;
        $host = parse_url($CFG->wwwroot, PHP_URL_HOST);
        $sitename = str_replace(':', '', format_string($SITE->fullname, true, ['context' => system::instance()]));
        $issuer = $sitename.' '.$host;
        $totp = TOTP::create($secret, clock: $this->clock);
        $totp->setLabel($USER->username);
        $totp->setIssuer($issuer);
        return $totp->getProvisioningUri();
    }

    /**
     * Generates HTML sting with QR code for given secret key.
     *
     * @param string $secret
     * @return string
     */
    public function generate_qrcode(string $secret): string {
        $uri = $this->generate_totp_uri($secret);
        $qrcode = new \TCPDF2DBarcode($uri, 'QRCODE');
        $image = $qrcode->getBarcodePngData(7, 7);
        $html = \html_writer::img('data:image/png;base64,' . base64_encode($image), '', ['width' => '150px']);
        return $html;
    }

    /**
     * TOTP state
     *
     * {@inheritDoc}
     */
    public function get_state(): string {
        global $USER;
        $userfactors = $this->get_active_user_factors($USER);

        // If no codes are setup then we must be neutral not unknown.
        if (count($userfactors) == 0) {
            return \tool_mfa\plugininfo\factor::STATE_NEUTRAL;
        }

        return parent::get_state();
    }

    /**
     * TOTP Factor implementation.
     *
     * @param MoodleQuickForm $mform
     * @return MoodleQuickForm $mform
     */
    public function setup_factor_form_definition(MoodleQuickForm $mform): MoodleQuickForm {
        $secret = $this->generate_secret_code();
        $mform->addElement('hidden', 'secret', $secret);
        $mform->setType('secret', PARAM_ALPHANUM);

        return $mform;
    }

    /**
     * TOTP Factor implementation.
     *
     * @param MoodleQuickForm $mform
     * @return MoodleQuickForm $mform
     */
    public function setup_factor_form_definition_after_data(MoodleQuickForm $mform): MoodleQuickForm {
        global $OUTPUT, $SITE, $USER;

        // Array of elements to allow XSS.
        $xssallowedelements = [];

        $headingstring = $mform->elementExists('replaceid') ? 'replacefactor' : 'setupfactor';
        $mform->addElement('html', $OUTPUT->heading(get_string($headingstring, 'factor_totp'), 2));

        $html = \html_writer::tag('p', get_string('setupfactor:intro', 'factor_totp'));
        $mform->addElement('html', $html);

        // Device name.
        $html = \html_writer::tag('p', get_string('setupfactor:instructionsdevicename', 'factor_totp'), ['class' => 'bold']);
        $mform->addElement('html', $html);

        $mform->addElement('text', 'devicename', get_string('setupfactor:devicename', 'factor_totp'), [
            'placeholder' => get_string('devicenameexample', 'factor_totp'),
            'autofocus' => 'autofocus',
        ]);
        $mform->setType('devicename', PARAM_TEXT);
        $mform->addRule('devicename', get_string('required'), 'required', null, 'client');

        $html = \html_writer::tag('p', get_string('setupfactor:devicenameinfo', 'factor_totp'));
        $mform->addElement('static', 'devicenameinfo', '', $html);

        // Scan QR code.
        $html = \html_writer::tag('p', get_string('setupfactor:instructionsscan', 'factor_totp'), ['class' => 'bold']);
        $mform->addElement('html', $html);

        $secretfield = $mform->getElement('secret');
        $secret = $secretfield->getValue();
        $qrcode = $this->generate_qrcode($secret);

        $html = \html_writer::tag('p', $qrcode);
        $mform->addElement('static', 'scan', '', $html);

        // Enter manually.
        $secret = wordwrap($secret, 4, ' ', true) . '</code>';
        $secret = \html_writer::tag('code', $secret);

        $sitefullname = format_string($SITE->fullname, true, ['context' => system::instance()]);

        $manualtable = new \html_table();
        $manualtable->id = 'manualattributes';
        $manualtable->attributes['class'] = 'generaltable table table-bordered table-sm w-auto';
        $manualtable->attributes['style'] = 'width: auto;';
        $manualtable->data = [
            [get_string('setupfactor:key', 'factor_totp'), $secret],
            [get_string('setupfactor:account', 'factor_totp'), "{$sitefullname} ({$USER->username})"],
            [get_string('setupfactor:mode', 'factor_totp'), get_string('setupfactor:mode:timebased', 'factor_totp')],
        ];

        $html = \html_writer::table($manualtable);
        // Wrap the table in a couple of divs to be controlled via bootstrap.
        $html = \html_writer::div($html, 'collapse', ['id' => 'collapseManualAttributes']);

        $togglelink = \html_writer::tag('a', get_string('setupfactor:link', 'factor_totp'), [
            'data-bs-toggle' => 'collapse',
            'data-bs-target' => '#collapseManualAttributes',
            'aria-expanded' => 'false',
            'aria-controls' => 'collapseManualAttributes',
            'href' => '#',
        ]);

        $html = $togglelink . $html;
        $xssallowedelements[] = $mform->addElement('static', 'enter', '', $html);

        // Allow XSS.
        if (method_exists('MoodleQuickForm_static', 'set_allow_xss')) {
            foreach ($xssallowedelements as $xssallowedelement) {
                $xssallowedelement->set_allow_xss(true);
            }
        }

        // Verification.
        $html = \html_writer::tag('p', get_string('setupfactor:instructionsverification', 'factor_totp'), ['class' => 'bold']);
        $mform->addElement('html', $html);

        $verificationfield = new \tool_mfa\local\form\verification_field(
            attributes: ['class' => 'tool-mfa-verification-code'],
            auth: false,
            elementlabel: get_string('setupfactor:verificationcode', 'factor_totp'),
        );
        $mform->addElement($verificationfield);
        $mform->setType('verificationcode', PARAM_ALPHANUM);
        $mform->addRule('verificationcode', get_string('required'), 'required', null, 'client');

        return $mform;
    }

    /**
     * TOTP Factor implementation.
     *
     * @param array $data
     * @return array
     */
    public function setup_factor_form_validation(array $data): array {
        $errors = [];

        $totp = TOTP::create($data['secret'], clock: $this->clock);
        if (!$totp->verify($data['verificationcode'], $this->clock->time(), 1)) {
            $errors['verificationcode'] = get_string('error:wrongverification', 'factor_totp');
        }

        return $errors;
    }

    /**
     * TOTP Factor implementation.
     *
     * @param MoodleQuickForm $mform
     * @return MoodleQuickForm $mform
     */
    public function login_form_definition(MoodleQuickForm $mform): MoodleQuickForm {

        $mform->disable_form_change_checker();
        $mform->addElement(new \tool_mfa\local\form\verification_field());
        $mform->setType('verificationcode', PARAM_ALPHANUM);

        return $mform;
    }

    /**
     * TOTP Factor implementation.
     *
     * @param array $data
     * @return array
     */
    public function login_form_validation(array $data): array {
        global $USER;
        $factors = $this->get_active_user_factors($USER);
        $result = ['verificationcode' => get_string('error:wrongverification', 'factor_totp')];
        $window = get_config('factor_totp', 'window');

        foreach ($factors as $factor) {
            $totp = TOTP::create($factor->secret, clock: $this->clock);
            $factorresult = $this->validate_code($data['verificationcode'], $window, $totp, $factor);
            $time = userdate($this->clock->time(), get_string('systimeformat', 'factor_totp'));

            switch ($factorresult) {
                case self::TOTP_USED:
                    return ['verificationcode' => get_string('error:codealreadyused', 'factor_totp')];

                case self::TOTP_OLD:
                    return ['verificationcode' => get_string('error:oldcode', 'factor_totp', $time)];

                case self::TOTP_FUTURE:
                    return ['verificationcode' => get_string('error:futurecode', 'factor_totp', $time)];

                case self::TOTP_VALID:
                    $this->update_lastverified($factor->id);
                    return [];

                default:
                    continue(2);
            }
        }
        return $result;
    }

    /**
     * Checks the code for reuse, clock skew, and validity.
     *
     * @param string $code the code to check.
     * @param int $window the window to check validity for.
     * @param TOTP $totp the totp object to check against.
     * @param stdClass $factor the factor with information required.
     *
     * @return string constant with verification state.
     */
    public function validate_code(string $code, int $window, TOTP $totp, stdClass $factor): string {
        // First check if this code matches the last verified timestamp.
        $lastverified = $this->get_lastverified($factor->id);
        if ($lastverified > 0 && $totp->verify($code, $lastverified, $window)) {
            return self::TOTP_USED;
        }

        // Check if the code is valid, returning early.
        if ($totp->verify($code, $this->clock->time(), $window)) {
            return self::TOTP_VALID;
        }

        // Check for clock skew in the past and future 10 periods.
        for ($i = 1; $i <= 10; $i++) {
            $pasttimestamp = $this->clock->time() - $i * $totp->getPeriod();
            $futuretimestamp = $this->clock->time() + $i * $totp->getPeriod();

            if ($totp->verify($code, $pasttimestamp, $window)) {
                return self::TOTP_OLD;
            }

            if ($totp->verify($code, $futuretimestamp, $window)) {
                return self::TOTP_FUTURE;
            }
        }

        // In all other cases, the code is invalid.
        return self::TOTP_INVALID;
    }

    /**
     * Generates cryptographically secure pseudo-random 16-digit secret code.
     *
     * @return string
     */
    public function generate_secret_code(): string {
        $totp = TOTP::create(clock: $this->clock);
        return substr($totp->getSecret(), 0, 16);
    }

    /**
     * TOTP Factor implementation.
     *
     * @param stdClass $data
     * @return stdClass|null the factor record, or null.
     */
    public function setup_user_factor(stdClass $data): stdClass|null {
        global $DB, $USER;

        if (!empty($data->secret)) {
            $row = new stdClass();
            $row->userid = $USER->id;
            $row->factor = $this->name;
            $row->secret = $data->secret;
            $row->label = $data->devicename;
            $row->timecreated = $this->clock->time();
            $row->createdfromip = $USER->lastip;
            $row->timemodified = $this->clock->time();
            $row->lastverified = 0;
            $row->revoked = 0;

            // Check if a record with this configuration already exists, warning the user accordingly.
            $record = $DB->get_record('tool_mfa', [
                'userid' => $row->userid,
                'secret' => $row->secret,
                'factor' => $row->factor,
            ], '*', IGNORE_MULTIPLE);
            if ($record) {
                \core\notification::warning(get_string('error:alreadyregistered', 'factor_totp'));
                return null;
            }

            $id = $DB->insert_record('tool_mfa', $row);
            $record = $DB->get_record('tool_mfa', ['id' => $id]);
            $this->create_event_after_factor_setup($USER);

            return $record;
        }

        return null;
    }

    /**
     * TOTP Factor implementation with replacement of existing factor.
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

    /**
     * TOTP Factor implementation.
     *
     * @param stdClass $user the user to check against.
     * @return array
     */
    public function get_all_user_factors($user): array {
        global $DB;
        return $DB->get_records('tool_mfa', ['userid' => $user->id, 'factor' => $this->name]);
    }

    /**
     * TOTP Factor implementation.
     *
     * {@inheritDoc}
     */
    public function has_revoke(): bool {
        return true;
    }

    /**
     * TOTP Factor implementation.
     */
    public function has_replace(): bool {
        return true;
    }

    /**
     * TOTP Factor implementation.
     *
     * {@inheritDoc}
     */
    public function has_setup(): bool {
        return true;
    }

    /**
     * TOTP Factor implementation
     *
     * {@inheritDoc}
     */
    public function show_setup_buttons(): bool {
        return true;
    }

    /**
     * TOTP Factor implementation.
     * Empty override of parent.
     *
     * {@inheritDoc}
     */
    public function post_pass_state(): void {
        return;
    }

    /**
     * TOTP Factor implementation.
     * TOTP cannot return fail state.
     *
     * @param stdClass $user
     */
    public function possible_states(stdClass $user): array {
        return [
            \tool_mfa\plugininfo\factor::STATE_PASS,
            \tool_mfa\plugininfo\factor::STATE_NEUTRAL,
            \tool_mfa\plugininfo\factor::STATE_UNKNOWN,
        ];
    }

    /**
     * TOTP Factor implementation.
     *
     * {@inheritDoc}
     */
    public function get_setup_string(): string {
        return get_string('setupfactorbutton', 'factor_totp');
    }

    /**
     * Gets the string for manage button on preferences page.
     *
     * @return string
     */
    public function get_manage_string(): string {
        return get_string('managefactorbutton', 'factor_totp');
    }
}
