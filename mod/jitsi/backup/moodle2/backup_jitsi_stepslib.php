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
 * Define the complete jitsi structure for backup, with file and id annotations
 *
 * @package   mod_jitsi
 * @category  backup
 * @copyright 2016 Your Name <your@email.address>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_jitsi_activity_structure_step extends backup_activity_structure_step {

    /**
     * Defines the backup structure of the module
     *
     * @return backup_nested_element
     */
    protected function define_structure() {

        $userinfo = $this->get_setting_value('userinfo');

        $jitsi = new backup_nested_element('jitsi', array('id'), array('name', 'intro', 'introformat',
            'timeopen', 'timeclose', 'validitytime', 'minpretime', 'token', 'completionminutes'));
        $sources = new backup_nested_element('sources');
        $source = new backup_nested_element('source', array('id'), array('link', 'account', 'timecreated', 'userid'));
        $records = new backup_nested_element('records');
        $record = new backup_nested_element('record', array('id'), array('deleted', 'source', 'visible', 'name'));
        $accounts = new backup_nested_element('accounts');
        $account = new backup_nested_element('account', array('id'), array('name'));

        // Build the tree.
        $jitsi->add_child($records);
        $records->add_child($record);

        $record->add_child($sources);
        $sources->add_child($source);

        $source->add_child($accounts);
        $accounts->add_child($account);

        $jitsi->set_source_table('jitsi', array('id' => backup::VAR_ACTIVITYID));

        if ($userinfo) {
            $source->set_source_table('jitsi_source_record', array('id' => '../../source'));
            $record->set_source_table('jitsi_record', array('jitsi' => '../../id'));
            $account->set_source_table('jitsi_record_account', array('id' => '../../account'));
        }

        $source->annotate_ids('user', 'userid');

        $jitsi->annotate_files('mod_jitsi', 'intro', null);
        return $this->prepare_activity_structure($jitsi);
    }
}
