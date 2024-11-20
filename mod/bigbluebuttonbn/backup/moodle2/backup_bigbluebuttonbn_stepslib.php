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
 * Class for the structure used for backup BigBlueButtonBN.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Fred Dixon  (ffdixon [at] blindsidenetworks [dt] com)
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 */

/**
 * Define all the backup steps that will be used by the backup_bigbluebuttonbn_activity_task.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_bigbluebuttonbn_activity_structure_step extends backup_activity_structure_step {
    /**
     * Define the complete bigbluebuttonbn structure for backup, with file and id annotations.
     *
     * @return object
     */
    protected function define_structure() {

        // To know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated.
        $bigbluebuttonbn = new backup_nested_element('bigbluebuttonbn', ['id'], [
            'type', 'course', 'name', 'intro', 'introformat', 'meetingid',
            'moderatorpass', 'viewerpass', 'wait', 'record', 'recordallfromstart',
            'recordhidebutton', 'welcome', 'voicebridge', 'openingtime', 'closingtime', 'timecreated',
            'timemodified', 'presentation', 'participants', 'userlimit',
            'recordings_html', 'recordings_deleted', 'recordings_imported', 'recordings_preview',
            'clienttype', 'muteonstart', 'completionattendance',
            'completionengagementchats', 'completionengagementtalks', 'completionengagementraisehand',
            'completionengagementpollvotes', 'completionengagementemojis',
            'guestallowed', 'mustapproveuser', 'showpresentation']);

        $logs = new backup_nested_element('logs');

        $log = new backup_nested_element('log', ['id'], [
            'courseid', 'bigbluebuttonbnid', 'userid', 'timecreated', 'meetingid', 'log', 'meta']);

        $recordings = new backup_nested_element('recordings');

        $recording = new backup_nested_element('recording', ['id'], [
            'courseid', 'bigbluebuttonbnid', 'groupid', 'recordingid', 'headlesss', 'imported', 'status', 'importeddata',
            'timecreated']);

        // Build the tree.
        $bigbluebuttonbn->add_child($logs);
        $logs->add_child($log);
        $bigbluebuttonbn->add_child($recordings);
        $recordings->add_child($recording);

        // Define sources.
        $bigbluebuttonbn->set_source_table('bigbluebuttonbn', ['id' => backup::VAR_ACTIVITYID]);

        // This source definition only happen if we are including user info.
        if ($userinfo) {
            $log->set_source_table('bigbluebuttonbn_logs', ['bigbluebuttonbnid' => backup::VAR_PARENTID]);
            $recording->set_source_table('bigbluebuttonbn_recordings', ['bigbluebuttonbnid' => backup::VAR_PARENTID]);
        }

        // Define id annotations.
        $log->annotate_ids('user', 'userid');

        // Define file annotations.
        $bigbluebuttonbn->annotate_files('mod_bigbluebuttonbn', 'intro', null);

        $this->add_subplugin_structure('bbbext', $bigbluebuttonbn, true);
        // Return the root element (bigbluebuttonbn), wrapped into standard activity structure.
        return $this->prepare_activity_structure($bigbluebuttonbn);
    }
}
