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

namespace tool_mfa\local\form;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/form/text.php');

/**
 * MFA Verification code element.
 *
 * @package     tool_mfa
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class verification_field extends \MoodleQuickForm_text {

    /** @var bool */
    private $appendjs;

    /**
     * Verification field is a text entry box that features some useful extras.
     *
     * Contains JS to autosubmit the auth page when code is entered, as well as additional styling.
     *
     * @param array $attributes
     * @param boolean $auth is this constructed in auth.php loginform_* definitions. Set to false to prevent autosubmission of form.
     * @param string|null $elementlabel Provide a different element label.
     */
    public function __construct($attributes = null, $auth = true, ?string $elementlabel = null) {
        global $PAGE;

        // Force attributes.
        if (empty($attributes)) {
            $attributes = [];
        }

        $attributes['autocomplete'] = 'one-time-code';
        $attributes['inputmode'] = 'numeric';
        $attributes['pattern'] = '[0-9]*';
        // Overwrite default classes if set.
        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] : 'tool-mfa-verification-code fw-bold';
        $attributes['maxlength'] = 6;

        // If we aren't on the auth page, this might be part of a larger form such as for setup.
        // We shouldn't autofocus here, as it probably isn't the only element, or main target.
        if ($auth) {
            $attributes['autofocus'] = 'autofocus';
        }

        // If we are on the auth page, load JS for element.
        $this->appendjs = false;
        if ($auth) {
            $PAGE->requires->js_call_amd('tool_mfa/autosubmit_verification_code', 'init', []);
        }

        // Force element name to match JS.
        $elementname = 'verificationcode';
        // Overwrite default element label if set.
        $elementlabel = !empty($elementlabel) ? $elementlabel : get_string('entercode', 'tool_mfa');

        return parent::__construct($elementname, $elementlabel, $attributes);
    }

    /**
     * Returns HTML for this form element.
     *
     * phpcs:disable moodle.NamingConventions.ValidFunctionName.LowercaseMethod
     *
     * @return string
     */
    public function toHtml(): string {
        // Empty the value after all attributes decided.
        $this->_attributes['value'] = '';
        $result = parent::toHtml();

        $submitjs = "<script>
            document.querySelector('#id_verificationcode').addEventListener('keyup', function() {
                if (this.value.length == 6) {
                    // Submits the closes form (parent).
                    this.closest('form').submit();
                }
            });
            </script>";

        if ($this->appendjs) {
            $result .= $submitjs;
        }
        return $result;
    }

    /**
     * Setup and return the script for autosubmission while inside the secure layout.
     *
     * @return string the JS to inline attach to the rendered object.
     */
    public function secure_js(): string {
        // Empty the value after all attributes decided.
        $this->_attributes['value'] = '';

        return "<script>
            document.querySelector('#id_verificationcode').addEventListener('keyup', function() {
                if (this.value.length == 6) {
                    // Submits the closes form (parent).
                    this.closest('form').submit();
                }
            });
        </script>";
    }
}
