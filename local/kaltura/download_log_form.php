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
 * @package    local_kaltura
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/formslib.php');
/**
 * Download Kaltura logs form class.
 */
class local_kaltura_download_log_form extends moodleform {
    /**
     * This function defines the elements on the form.
     */
    public function definition() {
        $mform    =& $this->_form;

        $mform->addElement('header', 'setup', get_string('options'));
        $mform->addElement('date_selector', 'logs_start_time', get_string('download_log_range', 'local_kaltura'));

        $buttonarray=array();
        $buttonarray[] =& $mform->createElement('submit', 'submitbutton', get_string('download'));
        $buttonarray[] =& $mform->createElement('submit', 'cancel', get_string('cancel'));
        $buttonarray[] =& $mform->createElement('submit', 'deletelogs', get_string('delete_logs', 'local_kaltura'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
    }
}