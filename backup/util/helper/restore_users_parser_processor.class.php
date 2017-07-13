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
 * @package moodlecore
 * @subpackage backup-helper
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/backup/util/xml/parser/processors/grouped_parser_processor.class.php');

/**
 * helper implementation of grouped_parser_processor that will
 * load all the contents of one users.xml file to the backup_ids table
 * storing the whole structure there for later processing.
 * Note: only "needed" users are loaded (must have userref record in backup_ids)
 * Note: parentitemid will contain the user->contextid
 * Note: althought included in backup, we don't restore user context ras/caps
 * in same site they will be already there and it doesn't seem a good idea
 * to make them "transportable" arround sites.
 *
 * TODO: Complete phpdocs
 */
class restore_users_parser_processor extends grouped_parser_processor {

    protected $restoreid;

    public function __construct($restoreid) {
        $this->restoreid = $restoreid;
        parent::__construct(array());
        // Set the paths we are interested on, returning all them grouped under user
        $this->add_path('/users/user', true);
        $this->add_path('/users/user/custom_fields/custom_field');
        $this->add_path('/users/user/tags/tag');
        $this->add_path('/users/user/preferences/preference');
        // As noted above, we skip user context ras and caps
        // $this->add_path('/users/user/roles/role_overrides/override');
        // $this->add_path('/users/user/roles/role_assignments/assignment');
    }

    protected function dispatch_chunk($data) {
        // Received one user chunck, we are going to store it into backup_ids
        // table, with name = user and parentid = contextid for later use
        $itemname = 'user';
        $itemid   = $data['tags']['id'];
        $parentitemid = $data['tags']['contextid'];
        $info = $data['tags'];
        // Only load it if needed (exist same userref itemid in table)
        if (restore_dbops::get_backup_ids_record($this->restoreid, 'userref', $itemid)) {
            restore_dbops::set_backup_ids_record($this->restoreid, $itemname, $itemid, 0, $parentitemid, $info);
        }
    }

    protected function notify_path_start($path) {
        // nothing to do
    }

    protected function notify_path_end($path) {
        // nothing to do
    }

    /**
     * Provide NULL decoding
     */
    public function process_cdata($cdata) {
        if ($cdata === '$@NULL@$') {
            return null;
        }
        return $cdata;
    }
}
