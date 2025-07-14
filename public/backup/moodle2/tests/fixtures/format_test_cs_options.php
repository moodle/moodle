<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Various fixture course formats for backup unit tests
 *
 * @package    core_backup
 * @category   test
 * @copyright  2022 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/course/format/topics/lib.php');

/**
 * Fixture course format with one option.
 */
class format_test_cs_options extends format_topics {
    /**
     * Override method format_topics::get_default_section_name to prevent PHPUnit errors related to the nonexistent
     * format_test_cs_options lang file.
     *
     * @param \stdClass $section The section in question.
     * @return string The section's name for display.
     */
    public function get_default_section_name($section) {
        if ($section->section == 0) {
            return parent::get_default_section_name($section);
        } else {
            return get_string('sectionname', 'format_topics') . ' ' . $section->section;
        }
    }

    public function section_format_options($foreditform = false) {
        return array(
            'numdaystocomplete' => array(
                 'type' => PARAM_INT,
                 'label' => 'Test days',
                 'element_type' => 'text',
                 'default' => 0,
             ),
         );
    }
}

/**
 * Fixture course format with 2 options, 1 inherited.
 */
class format_test_cs2_options extends format_test_cs_options {
    public function section_format_options($foreditform = false) {
        return array(
            'numdaystocomplete' => array(
                 'type' => PARAM_INT,
                 'label' => 'Test days',
                 'element_type' => 'text',
                 'default' => 0,
             ),
            'secondparameter' => array(
                'type' => PARAM_INT,
                'label' => 'Test Parmater',
                'element_type' => 'text',
                'default' => 0,
            ),
        ) + parent::section_format_options($foreditform);
    }
}
