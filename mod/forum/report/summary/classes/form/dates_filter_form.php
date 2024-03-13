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
 * The mform used by the forum summary report dates filter.
 *
 * @package forumreport_summary
 * @copyright 2019 Michael Hawkins <michaelh@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace forumreport_summary\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * The mform class for creating the forum summary report dates filter.
 *
 * @copyright 2019 Michael Hawkins <michaelh@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dates_filter_form extends \moodleform {
    /**
     * The form definition.
     *
     */
    public function definition() {
        $attributes = [
            'class' => 'align-items-center',
        ];

        // From date field.
        $this->_form->addElement('date_selector', 'filterdatefrompopover',
                                 get_string('fromdate'), ['optional' => true], $attributes);

        // To date field.
        $this->_form->addElement('date_selector', 'filterdatetopopover',
                                 get_string('todate'), ['optional' => true], $attributes);
    }
}
