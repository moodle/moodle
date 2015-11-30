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

use html_writer;
defined('MOODLE_INTERNAL') || die;

/**
 * UI element for a text input field.
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class text_attribute extends element {

    /** @var bool $isdisabled Is this input disabled? */
    private $isdisabled;

    /**
     * Constructor
     *
     * @param string $name The input name (the first bit)
     * @param string $value The input initial value.
     * @param string $label The label for this input field.
     * @param bool $isdisabled Is this input disabled.
     */
    public function __construct($name, $value, $label, $isdisabled = false) {
        $this->isdisabled = $isdisabled;
        parent::__construct($name, $value, $label);
    }

    /**
     * Nasty function allowing custom textbox behaviour outside the class.
     * @return bool Is this a textbox.
     */
    public function is_textbox() {
        return true;
    }

    /**
     * Render the html for this field.
     * @return string The HTML.
     */
    public function html() {
        $attributes = array(
            'type' => 'text',
            'name' => $this->name,
            'value' => $this->value,
            'id' => $this->name
        );

        if ($this->isdisabled) {
            $attributes['disabled'] = 'DISABLED';
        }

        $hidden = array(
            'type' => 'hidden',
            'name' => 'old' . $this->name,
            'value' => $this->value
        );

        $label = '';
        if (preg_match("/^feedback/", $this->name)) {
            $labeltitle = get_string('feedbackfor', 'gradereport_singleview', $this->label);
            $attributes['tabindex'] = '2';
            $label = html_writer::tag('label', $labeltitle,  array('for' => $this->name, 'class' => 'accesshide'));
        } else if (preg_match("/^finalgrade/", $this->name)) {
            $labeltitle = get_string('gradefor', 'gradereport_singleview', $this->label);
            $attributes['tabindex'] = '1';
            $label = html_writer::tag('label', $labeltitle,  array('for' => $this->name, 'class' => 'accesshide'));
        }

        return (
            $label .
            html_writer::empty_tag('input', $attributes) .
            html_writer::empty_tag('input', $hidden)
        );
    }
}
