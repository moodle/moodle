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
 * load all the contents of one roles.xml (roles description) file to the backup_ids table
 * storing the whole structure there for later processing.
 * Note: only "needed" roles are loaded (must have roleref record in backup_ids)
 *
 * TODO: Complete phpdocs
 */
class restore_roles_parser_processor extends grouped_parser_processor {

    protected $restoreid;

    public function __construct($restoreid) {
        $this->restoreid = $restoreid;
        parent::__construct(array());
        // Set the paths we are interested on, returning all them grouped under user
        $this->add_path('/roles_definition/role');
    }

    protected function dispatch_chunk($data) {
        // Received one role chunck, we are going to store it into backup_ids
        // table, with name = role
        $itemname = 'role';
        $itemid   = $data['tags']['id'];
        $info = $data['tags'];
        // Only load it if needed (exist same roleref itemid in table)
        if (restore_dbops::get_backup_ids_record($this->restoreid, 'roleref', $itemid)) {
            restore_dbops::set_backup_ids_record($this->restoreid, $itemname, $itemid, 0, null, $info);
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
