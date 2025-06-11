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
 * A checkbox ui element.
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace gradereport_singleview\local\ui;

use html_writer;

defined('MOODLE_INTERNAL') || die;

/**
 * A checkbox ui element.
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class checkbox_attribute extends element {

    /** @var bool $ischecked Is it checked? */
    private $ischecked;

    /** @var bool If this is a read-only input. */
    private bool $isreadonly;

    /** @var int Is this element locked either 0 or a time. */
    protected int $locked;

    /**
     * Constructor
     *
     * @param string $name The element name
     * @param string $label The label for the form element
     * @param bool $ischecked Is this thing on?
     * @param int $locked Is this element locked either 0 or a time.
     * @param bool $isreadonly If this is a read-only input.
     */
    public function __construct(string $name, string $label, bool $ischecked = false, int $locked=0, bool $isreadonly = false) {
        $this->ischecked = $ischecked;
        $this->locked = $locked;
        $this->isreadonly = $isreadonly;
        parent::__construct($name, 1, $label);
    }

    /**
     * Nasty function allowing checkbox logic to escape the class.
     * @return bool
     */
    public function is_checkbox(): bool {
        return true;
    }

    /**
     * Generate the HTML
     *
     * @return string
     */
    public function html(): string {
        global $OUTPUT;

        $attributes = [
            'type' => 'checkbox',
            'name' => $this->name,
            'value' => 1,
            'id' => $this->name
        ];

        // UCSB fixed user should not be able to override locked grade.
        if ( $this->locked) {
            $attributes['disabled'] = 'DISABLED';
        }

        $hidden = [
            'type' => 'hidden',
            'name' => 'old' . $this->name
        ];

        if ($this->ischecked) {
            $attributes['checked'] = 'CHECKED';
            $hidden['value'] = 1;
        }

        $type = "override";
        if (preg_match("/^exclude/", $this->name)) {
            $type = "exclude";
        }

        if (!$this->isreadonly) {
            return (
                html_writer::tag('label',
                                 get_string($type . 'for', 'gradereport_singleview', $this->label),
                                 ['for' => $this->name, 'class' => 'accesshide']) .
                html_writer::empty_tag('input', $attributes) .
                html_writer::empty_tag('input', $hidden)
            );
        } else if ($this->ischecked) {
            return $OUTPUT->pix_icon('i/checked', get_string('selected', 'core_form'),
                'moodle', ['class' => 'overrideexcludecheck']);
        } else {
            return '';
        }
    }
}
