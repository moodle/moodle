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
 * load all the categories and questions (header info only) from then questions.xml file
 * to the backup_ids table storing the whole structure there for later processing.
 * Note: only "needed" categories are loaded (must have question_categoryref record in backup_ids)
 * Note: parentitemid will contain the category->contextid for categories
 * Note: parentitemid will contain the category->id for questions
 *
 * TODO: Complete phpdocs
 */
class restore_questions_parser_processor extends grouped_parser_processor {

    protected $restoreid;
    protected $lastcatid;

    public function __construct($restoreid) {
        $this->restoreid = $restoreid;
        $this->lastcatid = 0;
        parent::__construct(array());
        // Set the paths we are interested on
        $this->add_path('/question_categories/question_category');
        $this->add_path('/question_categories/question_category/questions/question');
    }

    protected function dispatch_chunk($data) {
        // Prepare question_category record
        if ($data['path'] == '/question_categories/question_category') {
            $info     = (object)$data['tags'];
            $itemname = 'question_category';
            $itemid   = $info->id;
            $parentitemid = $info->contextid;
            $this->lastcatid = $itemid;

        // Prepare question record
        } else if ($data['path'] == '/question_categories/question_category/questions/question') {
            $info = (object)$data['tags'];
            $itemname = 'question';
            $itemid   = $info->id;
            $parentitemid = $this->lastcatid;

        // Not question_category nor question, impossible. Throw exception.
        } else {
            throw new progressive_parser_exception('restore_questions_parser_processor_unexpected_path', $data['path']);
        }

        // Only load it if needed (exist same question_categoryref itemid in table)
        if (restore_dbops::get_backup_ids_record($this->restoreid, 'question_categoryref', $this->lastcatid)) {
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
