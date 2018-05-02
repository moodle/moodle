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

namespace core_mocksearch\search;

/**
 * Component implementing search for testing purposes.
 *
 * @package   core_search
 * @category  phpunit
 * @copyright David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

class mock_search_area extends \core_search\base {

    /**
     * Multiple context level so we can test get_areas_user_accesses.
     * @var int[]
     */
    protected static $levels = [CONTEXT_SYSTEM, CONTEXT_USER];

    /**
     * To make things easier, base class required config stuff.
     *
     * @return bool
     */
    public function is_enabled() {
        return true;
    }

    public function get_recordset_by_timestamp($modifiedfrom = 0) {
        global $DB;

        $sql = "SELECT * FROM {temp_mock_search_area} WHERE timemodified >= ? ORDER BY timemodified ASC";
        return $DB->get_recordset_sql($sql, array($modifiedfrom));
    }


    /**
     * A helper function that will turn a record into 'data array', for use with document building.
     */
    public function convert_record_to_doc_array($record) {
        $docdata = (array)unserialize($record->info);
        $docdata['areaid'] = $this->get_area_id();
        $docdata['itemid'] = $record->id;
        $docdata['modified'] = $record->timemodified;

        return $docdata;
    }

    public function get_document($record, $options = array()) {
        global $USER;

        $info = unserialize($record->info);

        // Prepare associative array with data from DB.
        $doc = \core_search\document_factory::instance($record->id, $this->componentname, $this->areaname);
        $doc->set('title', $info->title);
        $doc->set('content', $info->content);
        $doc->set('description1', $info->description1);
        $doc->set('description2', $info->description2);
        $doc->set('contextid', $info->contextid);
        $doc->set('courseid', $info->courseid);
        $doc->set('userid', $info->userid);
        $doc->set('owneruserid', $info->owneruserid);
        $doc->set('modified', $record->timemodified);

        return $doc;
    }

    public function attach_files($document) {
        global $DB;

        if (!$record = $DB->get_record('temp_mock_search_area', array('id' => $document->get('itemid')))) {
            return;
        }

        $info = unserialize($record->info);
        foreach ($info->attachfileids as $fileid) {
            $document->add_stored_file($fileid);
        }
    }

    public function uses_file_indexing() {
        return true;
    }

    public function check_access($id) {
        global $DB, $USER;

        if ($record = $DB->get_record('temp_mock_search_area', array('id' => $id))) {
            $info = unserialize($record->info);

            if (in_array($USER->id, $info->denyuserids)) {
                return \core_search\manager::ACCESS_DENIED;
            }
            return \core_search\manager::ACCESS_GRANTED;
        }
        return \core_search\manager::ACCESS_DELETED;
    }

    public function get_doc_url(\core_search\document $doc) {
        return new \moodle_url('/index.php');
    }

    public function get_context_url(\core_search\document $doc) {
        return new \moodle_url('/index.php');
    }

    public function get_visible_name($lazyload = false) {
        return 'Mock search area';
    }
}
