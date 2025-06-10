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

// This is an example of how to make a block date extractor.
// If we ever need to edit dates in block, use this as a template.

/**
 * Example of how you would extract date settings from a block.
 *
 * @package   report_editdates
 * @copyright 2011 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;


/**
 * Example {@link report_editdates_block_date_extractor} subclass.
 *
 * @copyright 2011 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_editdates_block_html_date_extractor  extends report_editdates_block_date_extractor {

    /**
     * Constructor.
     * @param object $course course settings from the DB.
     */
    public function __construct($course) {
        parent::__construct($course, 'html');
        parent::load_data();
    }

    public function get_settings(block_base $block) {
        // Check if title text is a valid date then return the array.
        $title = $block->title;
        if ((string) (int) $title === $title) {
                return array('title' => new report_editdates_date_setting
                                             (get_string('availabledate', 'assignment'),
                                             $block->title,
                                             self::DATETIME, false, 5)
                );
        }
    }

    public function validate_dates(block_base $block, array $dates) {
        $errors = array();
        if ($dates['title'] == 0 ) {
            $errors['title'] = get_string('datemustnotzero', 'report_editdates');
        }
        return $errors;
    }
}
