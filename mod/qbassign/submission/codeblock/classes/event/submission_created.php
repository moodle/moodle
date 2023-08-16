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
 * The qbassignsubmission_codeblock submission_created event.
 *
 * @package    qbassignsubmission_codeblock
 * @copyright  2014 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qbassignsubmission_codeblock\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The qbassignsubmission_codeblock submission_created event class.
 *
 * @property-read array $other {
 *      Extra information about the event.
 *
 *      - int codeblockwordcount: Word count of the code block submission.
 * }
 *
 * @package    qbassignsubmission_codeblock
 * @since      Moodle 2.7
 * @copyright  2014 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class submission_created extends \mod_qbassign\event\submission_created {

    /**
     * Init method.
     */
    protected function init() {
        parent::init();
        $this->data['objecttable'] = 'qbassignsubmission_codeblock';
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        $descriptionstring = "The user with id '$this->userid' created an code block submission with " .
            "'{$this->other['codeblockwordcount']}' words in the qbassignment with course module id " .
            "'$this->contextinstanceid'";
        if (!empty($this->other['groupid'])) {
            $descriptionstring .= " for the group with id '{$this->other['groupid']}'.";
        } else {
            $descriptionstring .= ".";
        }

        return $descriptionstring;
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();
        if (!isset($this->other['codeblockwordcount'])) {
            throw new \coding_exception('The \'codeblockwordcount\' value must be set in other.');
        }
    }

    public static function get_objectid_mapping() {
        // No mapping available for 'qbassignsubmission_codeblock'.
        return array('db' => 'qbassignsubmission_codeblock', 'restore' => \core\event\base::NOT_MAPPED);
    }
}
