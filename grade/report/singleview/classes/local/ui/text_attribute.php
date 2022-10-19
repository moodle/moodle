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
 * UI element for a text input field.
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradereport_singleview\local\ui;

defined('MOODLE_INTERNAL') || die;

/**
 * UI element for a text input field.
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class text_attribute extends element {

    /**
     * Is this input disabled?
     * @var bool $isdisabled
     */
    private $isdisabled;

    /** @var bool If this is a read-only input. */
    private bool $isreadonly;

    /**
     * Constructor
     *
     * @param string $name The input name (the first bit)
     * @param string $value The input initial value.
     * @param string $label The label for this input field.
     * @param bool $isdisabled Is this input disabled.
     * @param bool $isreadonly If this is a read-only input.
     */
    public function __construct(string $name, string $value, string $label, bool $isdisabled = false, bool $isreadonly = false) {
        $this->isdisabled = $isdisabled;
        $this->isreadonly = $isreadonly;
        parent::__construct($name, $value, $label);
    }

    /**
     * Nasty function allowing custom textbox behaviour outside the class.
     * @return bool Is this a textbox.
     */
    public function is_textbox(): bool {
        return true;
    }

    /**
     * Render the html for this field.
     * @return string The HTML.
     */
    public function html(): string {
        global $OUTPUT;

        $context = (object) [
            'id' => $this->name,
            'name' => $this->name,
            'value' => $this->value,
            'disabled' => $this->isdisabled,
            'readonly' => $this->isreadonly,
        ];

        $context->label = '';
        if (preg_match("/^feedback/", $this->name)) {
            $context->label = get_string('feedbackfor', 'gradereport_singleview', $this->label);
            $context->tabindex = '2';
        } else if (preg_match("/^finalgrade/", $this->name)) {
            $context->label = get_string('gradefor', 'gradereport_singleview', $this->label);
            $context->tabindex = '1';
        }

        return $OUTPUT->render_from_template('gradereport_singleview/text_attribute', $context);
    }
}
