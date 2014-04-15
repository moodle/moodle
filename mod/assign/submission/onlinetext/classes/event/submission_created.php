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
 * assignsubmission_onlinetext submission_created event.
 *
 * @package    assignsubmission_onlinetext
 * @copyright  2014 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignsubmission_onlinetext\event;

defined('MOODLE_INTERNAL') || die();

/**
 * assignsubmission_onlinetext submission_created event class.
 *
 * @property-read array $other Extra information about the event.
 *     -int submissionid: ID number of this submission.
 *     -int submissionattempt: Number of attempts made on this submission.
 *     -string submissionstatus: Status of the submission.
 *     -int groupid: (optional) The group ID if this is a teamsubmission (optional).
 *     -int onlinetextwordcount: Word count of the online text submission.
 * }
 *
 * @package    assignsubmission_onlinetext
 * @since      Moodle 2.7
 * @copyright  2014 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class submission_created extends \mod_assign\event\submission_created {

    /**
     * Init method.
     */
    protected function init() {
        parent::init();
        $this->data['objecttable'] = 'assignsubmission_file';
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        if (!empty($this->other['groupid'])) {
            $context = $this->get_context();
            if (isset($context)) {
                $descriptionstring = 'A user with an id of ' . $this->userid . ' updated a file submission and uploaded ' .
                        $this->other['onlinetextwordcount'] . ' file/s in the assign module with an id of ' .
                        $this->other['submissionid'] . ' for the group ' .
                        \format_string($this->other['groupname'], true, array('context' => $context)) . '.';
            } else {
                $descriptionstring = 'A user with an id of ' . $this->userid . ' updated a file submission and uploaded ' .
                        $this->other['onlinetextwordcount'] . ' file/s in the assign module with an id of ' .
                        $this->other['submissionid'] . ' for the group ' . $this->other['groupid'] . '.';

            }
        } else {
            $descriptionstring = 'A user with an id of ' . $this->userid . ' updated a file submission and uploaded '.
                    $this->other['onlinetextwordcount'] . ' file/s in the assign module with an id of ' .
                    $this->other['submissionid'];
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
        if (!isset($this->other['onlinetextwordcount'])) {
            throw new \coding_exception('Other must contain the key onlinetextwordcount.');
        }
    }
}
