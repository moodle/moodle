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

require_once(__DIR__.'/../extlib/Assert/Assertion.php');
require_once(__DIR__.'/../extlib/Assert/AssertionFailedException.php');
require_once(__DIR__.'/../extlib/Assert/InvalidArgumentException.php');
require_once(__DIR__.'/../extlib/ParagonIE/ConstantTime/EncoderInterface.php');
require_once(__DIR__.'/../extlib/ParagonIE/ConstantTime/Binary.php');
require_once(__DIR__.'/../extlib/ParagonIE/ConstantTime/Base32.php');

use tool_mfa\local\factor\object_factor_base;
use OTPHP\TOTP;
use stdClass;

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
        $sitename = str_replace(':', '', $SITE->fullname);
        $issuer = $sitename.' '.$host;
        $totp = TOTP::create($secret);
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
        $html = \html_writer::tag('p', get_string('setupfactor:scanwithapp', 'factor_totp'));
        $html .= \html_writer::img('data:image/png;base64,' . base64_encode($image), '', ['width' => '150px']);
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
     * @param \MoodleQuickForm $mform
     * @return \MoodleQuickForm $mform
     */
    public function setup_factor_form_definition(\MoodleQuickForm $mform): \MoodleQuickForm {
        $secret = $this->generate_secret_code();
        $mform->addElement('hidden', 'secret', $secret);
        $mform->setType('secret', PARAM_ALPHANUM);

        return $mform;
    }

    /**
     * TOTP Factor implementation.
     *
     * @param \MoodleQuickForm $mform
     * @return \MoodleQuickForm $mform
     */
    public function setup_factor_form_definition_after_data(\MoodleQuickForm $mform): \MoodleQuickForm {
        global $OUTPUT, $SITE, $USER;

        // Array of elements to allow XSS.
        $xssallowedelements = [];

        $mform->addElement('html', $OUTPUT->heading(get_string('setupfactor', 'factor_totp'), 2));
        $mform->addElement('html', \html_writer::tag('p', get_string('info', 'factor_totp')));
        $mform->addElement('html', \html_writer::tag('hr', ''));

        $mform->addElement('text', 'devicename', get_string('devicename', 'factor_totp'), [
            'placeholder' => get_string('devicenameexample', 'factor_totp'),
            'autofocus' => 'autofocus',
        ]);
        $mform->addHelpButton('devicename', 'devicename', 'factor_totp');
        $mform->setType('devicename', PARAM_TEXT);
        $mform->addRule('devicename', get_string('required'), 'required', null, 'client');

        // Scan.
        $secretfield = $mform->getElement('secret');
        $secret = $secretfield->getValue();
        $qrcode = $this->generate_qrcode($secret);

        $html = \html_writer::tag('p', $qrcode);
        $xssallowedelements[] = $mform->addElement('static', 'scan', get_string('setupfactor:scan', 'factor_totp'), $html);

        // Link.
        if (get_config('factor_totp', 'totplink')) {
            $uri = $this->generate_totp_uri($secret);
            $html = $OUTPUT->action_link($uri, get_string('setupfactor:linklabel', 'factor_totp'));
            $xssallowedelements[] = $mform->addElement('static', 'link', get_string('setupfactor:link', 'factor_totp'), $html);
            $mform->addHelpButton('link', 'setupfactor:link', 'factor_totp');
        }

        // Enter manually.
        $secret = wordwrap($secret, 4, ' ', true) . '</code>';
        $secret = \html_writer::tag('code', $secret);

        $manualtable = new \html_table();
        $manualtable->id = 'manualattributes';
        $manualtable->attributes['class'] = 'generaltable table table-bordered table-sm w-auto';
        $manualtable->attributes['style'] = 'width: auto;';
        $manualtable->data = [
            [get_string('setupfactor:key', 'factor_totp'), $secret],
            [get_string('setupfactor:account', 'factor_totp'), "$SITE->fullname ($USER->username)"],
            [get_string('setupfactor:mode', 'factor_totp'), get_string('setupfactor:mode:timebased', 'factor_totp')],
        ];

        $html = \html_writer::table($manualtable);
        $html = \html_writer::tag('p', get_string('setupfactor:enter', 'factor_totp')) . $html;
        // Wrap the table in a couple of divs to be controlled via bootstrap.
        $html = \html_writer::div($html, 'card card-body', ['style' => 'padding-left: 0 !important;']);
        $html = \html_writer::div($html, 'collapse', ['id' => 'collapseManualAttributes']);

        $togglelink = \html_writer::tag('btn', get_string('setupfactor:scanfail', 'factor_totp'), [
            'class' => 'btn btn-secondary',
            'type' => 'button',
            'data-toggle' => 'collapse',
            'data-target' => '#collapseManualAttributes',
            'aria-expanded' => 'false',
            'aria-controls' => 'collapseManualAttributes',
            'style' => 'font-size: 14px;',
        ]);

        $html = $togglelink . $html;
        $xssallowedelements[] = $mform->addElement('static', 'enter', '', $html);

        // Allow XSS.
        if (method_exists('MoodleQuickForm_static', 'set_allow_xss')) {
            foreach ($xssallowedelements as $xssallowedelement) {
                $xssallowedelement->set_allow_xss(true);
            }
        }

        $mform->addElement(new \tool_mfa\local\form\verification_field(null, false));
        $mform->setType('verificationcode', PARAM_ALPHANUM);
        $mform->addHelpButton('verificationcode', 'verificationcode', 'factor_totp');
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

        $totp = TOTP::create($data['secret']);
        if (!$totp->verify($data['verificationcode'], time(), 1)) {
            $errors['verificationcode'] = get_string('error:wrongverification', 'factor_totp');
        }

        return $errors;
    }

    /**
     * TOTP Factor implementation.
     *
     * @param \MoodleQuickForm $mform
     * @return \MoodleQuickForm $mform
     */
    public function login_form_definition(\MoodleQuickForm $mform): \MoodleQuickForm {

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
        $windowconfig = get_config('factor_totp', 'window');

        foreach ($factors as $factor) {
            $totp = TOTP::create($factor->secret);
            // Convert seconds to windows.
            $window = (int) floor($windowconfig / $totp->getPeriod());
            $factorresult = $this->validate_code($data['verificationcode'], $window, $totp, $factor);
            $time = userdate(time(), get_string('systimeformat', 'factor_totp'));

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

        // The window in which to check for clock skew, 5 increments past valid window.
        $skewwindow = $window + 5;
        $pasttimestamp = time() - ($skewwindow * $totp->getPeriod());
        $futuretimestamp = time() + ($skewwindow * $totp->getPeriod());

        if ($totp->verify($code, time(), $window)) {
            return self::TOTP_VALID;
        } else if ($totp->verify($code, $pasttimestamp, $skewwindow)) {
            // Check for clock skew in the past 10 periods.
            return self::TOTP_OLD;
        } else if ($totp->verify($code, $futuretimestamp, $skewwindow)) {
            // Check for clock skew in the future 10 periods.
            return self::TOTP_FUTURE;
        } else {
            // In all other cases, code is invalid.
            return self::TOTP_INVALID;
        }
    }

    /**
     * Generates cryptographically secure pseudo-random 16-digit secret code.
     *
     * @return string
     */
    public function generate_secret_code(): string {
        $totp = TOTP::create();
        return substr($totp->getSecret(), 0, 16);
    }

    /**
     * TOTP Factor implementation.
     *
     * @param stdClass $data
     * @return stdClass the factor record, or null.
     */
    public function setup_user_factor(stdClass $data): stdClass|null {
        global $DB, $USER;

        if (!empty($data->secret)) {
            $row = new stdClass();
            $row->userid = $USER->id;
            $row->factor = $this->name;
            $row->secret = $data->secret;
            $row->label = $data->devicename;
            $row->timecreated = time();
            $row->createdfromip = $USER->lastip;
            $row->timemodified = time();
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
                return $record;
            }

            $id = $DB->insert_record('tool_mfa', $row);
            $record = $DB->get_record('tool_mfa', ['id' => $id]);
            $this->create_event_after_factor_setup($USER);

            return $record;
        }

        return null;
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
        return get_string('factorsetup', 'factor_totp');
    }
}
