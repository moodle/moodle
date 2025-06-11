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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class backup_quickmail_block_structure_step extends backup_block_structure_step {
    protected function define_structure() {
        global $DB;

        $params = array('course_id' => $this->get_courseid());
        $context = context_course::instance( $params['course_id']);
        // Logs for backups.
        $quickmaillogs = $DB->get_records('block_quickmail_messages', $params);
        $includehistory = $this->get_setting_value('include_quickmail_log');

        // Quickmail backup config.
        // Attempt to create block settings step for quickmail, so people can restore their quickmail settings.
        $paramstwo = array('coursesid' => $this->get_courseid());
        $quickmailblocklevelsettings = $DB->get_records('block_quickmail_config', $paramstwo);
        $includeconfig = $this->get_setting_value('include_quickmail_config');

        // Backup logs and settings.
        $backuplogsandsettings = new backup_nested_element('emaillogs', array('course_id'), null);

        $log = new backup_nested_element('log', array('id'), array(
            'course_id', 'user_id', 'message_type', 'notification_id', 'alternate_email_id',
            'signature_id', 'subject', 'body', 'editor_format', 'sent_at', 'to_send_at',
            'is_draft', 'send_reciept', 'send_to_mentors', 'is_sending', 'no_reply',
            'usermodified', 'timecreated', 'timemodified', 'timedeleted'
        ));

        // Courseid name value.
        $quickmailsettings = new backup_nested_element('block_level_setting', array('id'), array(
            'coursesid', 'name', 'value'
        ));

        $backuplogsandsettings->add_child($log);

        $backuplogsandsettings->add_child($quickmailsettings);

        $backuplogsandsettings->set_source_array(array((object)$params));

        if (!empty($quickmaillogs) && $includehistory) {
            $log->set_source_sql(
                'SELECT * FROM {block_quickmail_messages}
                WHERE course_id = ?', array(array('sqlparam' => $this->get_courseid()))
            );
        }

        if (!empty($quickmailblocklevelsettings) && $includeconfig) {
            $quickmailsettings->set_source_sql(
                'SELECT * FROM {block_quickmail_config}
                WHERE coursesid = ?', array(array('sqlparam' => $this->get_courseid()))
            );
        }

        $log->annotate_ids('user', 'user_id');

        return $this->prepare_block_structure($backuplogsandsettings);
    }
}
