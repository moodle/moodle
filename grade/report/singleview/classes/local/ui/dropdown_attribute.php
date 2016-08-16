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
 * Drop down list (select list) element
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradereport_singleview\local\ui;

use html_writer;

defined('MOODLE_INTERNAL') || die;

/**
 * Drop down list (select list) element
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dropdown_attribute extends element {

    /** @var string $selected Who is selected ? */
    private $selected;

    /** @var array $options List of options ? */
    private $options;

    /** @var bool $isdisabled Is this input disabled. */
    private $isdisabled;

    /**
     * Constructor
     *
     * @param string $name The first bit of the name of this input.
     * @param array $options The options list for this select.
     * @param string $label The form label for this input.
     * @param string $selected The name of the selected item in this input.
     * @param bool $isdisabled Are we disabled?
     */
    public function __construct($name, $options, $label, $selected = '', $isdisabled = false) {
        $this->selected = $selected;
        $this->options = $options;
        $this->isdisabled = $isdisabled;
        parent::__construct($name, $selected, $label);
    }

    /**
     * Nasty function spreading dropdown logic around.
     *
     * @return bool
     */
    public function is_dropdown() {
        return true;
    }

    /**
     * Render this element as html.
     *
     * @return string
     */
    public function html() {
        $old = array(
            'type' => 'hidden',
            'name' => 'old' . $this->name,
            'value' => $this->selected
        );

        $attributes = array('tabindex' => '1');

        if (!empty($this->isdisabled)) {
            $attributes['disabled'] = 'DISABLED';
        }

        $select = html_writer::select(
            $this->options, $this->name, $this->selected, false, $attributes
        );

        return ($select . html_writer::empty_tag('input', $old));
    }
}
