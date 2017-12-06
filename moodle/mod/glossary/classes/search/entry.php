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
 * Glossary entries search.
 *
 * @package    mod_glossary
 * @copyright  2015 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_glossary\search;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/glossary/lib.php');

/**
 * Glossary entries search.
 *
 * @package    mod_glossary
 * @copyright  2015 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class entry extends \core_search\base_mod {

    /**
     * @var array Internal quick static cache.
     */
    protected $entriesdata = array();

    /**
     * Returns recordset containing required data for indexing glossary entries.
     *
     * @param int $modifiedfrom timestamp
     * @return moodle_recordset
     */
    public function get_recordset_by_timestamp($modifiedfrom = 0) {
        global $DB;

        $sql = "SELECT ge.*, g.course FROM {glossary_entries} ge
                  JOIN {glossary} g ON g.id = ge.glossaryid
                WHERE ge.timemodified >= ? ORDER BY ge.timemodified ASC";
        return $DB->get_recordset_sql($sql, array($modifiedfrom));
    }

    /**
     * Returns the documents associated with this glossary entry id.
     *
     * @param stdClass $entry glossary entry.
     * @param array    $options
     * @return \core_search\document
     */
    public function get_document($entry, $options = array()) {
        global $DB;

        $keywords = array();
        if ($aliases = $DB->get_records('glossary_alias', array('entryid' => $entry->id))) {
            foreach ($aliases as $alias) {
                $keywords[] = $alias->alias;
            }
        }

        try {
            $cm = $this->get_cm('glossary', $entry->glossaryid, $entry->course);
            $context = \context_module::instance($cm->id);
        } catch (\dml_missing_record_exception $ex) {
            // Notify it as we run here as admin, we should see everything.
            debugging('Error retrieving mod_glossary ' . $entry->id . ' document, not all required data is available: ' .
                $ex->getMessage(), DEBUG_DEVELOPER);
            return false;
        } catch (\dml_exception $ex) {
            // Notify it as we run here as admin, we should see everything.
            debugging('Error retrieving mod_glossary' . $entry->id . ' document: ' . $ex->getMessage(), DEBUG_DEVELOPER);
            return false;
        }

        // Prepare associative array with data from DB.
        $doc = \core_search\document_factory::instance($entry->id, $this->componentname, $this->areaname);
        $doc->set('title', content_to_text($entry->concept, false));
        $doc->set('content', content_to_text($entry->definition, $entry->definitionformat));
        $doc->set('contextid', $context->id);
        $doc->set('courseid', $entry->course);
        $doc->set('userid', $entry->userid);
        $doc->set('owneruserid', \core_search\manager::NO_OWNER_ID);
        $doc->set('modified', $entry->timemodified);

        // Check if this document should be considered new.
        if (isset($options['lastindexedtime']) && ($options['lastindexedtime'] < $entry->timecreated)) {
            // If the document was created after the last index time, it must be new.
            $doc->set_is_new(true);
        }

        // Adding keywords as extra info.
        if ($keywords) {
            // No need to pass through content_to_text here as this is just a list of keywords.
            $doc->set('description1', implode(' ' , $keywords));
        }

        return $doc;
    }

    /**
     * Whether the user can access the document or not.
     *
     * @throws \dml_missing_record_exception
     * @throws \dml_exception
     * @param int $id Glossary entry id
     * @return bool
     */
    public function check_access($id) {
        global $USER;

        try {
            $entry = $this->get_entry($id);
            $cminfo = $this->get_cm('glossary', $entry->glossaryid, $entry->course);
        } catch (\dml_missing_record_exception $ex) {
            return \core_search\manager::ACCESS_DELETED;
        } catch (\dml_exception $ex) {
            return \core_search\manager::ACCESS_DENIED;
        }

        if (!glossary_can_view_entry($entry, $cminfo)) {
            return \core_search\manager::ACCESS_DENIED;
        }

        return \core_search\manager::ACCESS_GRANTED;
    }

    /**
     * Link to glossary entry.
     *
     * @param \core_search\document $doc
     * @return \moodle_url
     */
    public function get_doc_url(\core_search\document $doc) {
        global $USER;

        // The post is already in static cache, we fetch it in self::search_access.
        $entry = $this->get_entry($doc->get('itemid'));
        $contextmodule = \context::instance_by_id($doc->get('contextid'));

        if ($entry->approved == false && $entry->userid != $USER->id) {
            // The URL should change when the entry is not approved and it was not created by the user.
            $docparams = array('id' => $contextmodule->instanceid, 'mode' => 'approval');
        } else {
            $docparams = array('id' => $contextmodule->instanceid, 'mode' => 'entry', 'hook' => $doc->get('itemid'));

        }
        return new \moodle_url('/mod/glossary/view.php', $docparams);
    }

    /**
     * Link to the glossary.
     *
     * @param \core_search\document $doc
     * @return \moodle_url
     */
    public function get_context_url(\core_search\document $doc) {
        $contextmodule = \context::instance_by_id($doc->get('contextid'));
        return new \moodle_url('/mod/glossary/view.php', array('id' => $contextmodule->instanceid));
    }

    /**
     * Returns the specified glossary entry checking the internal cache.
     *
     * Store minimal information as this might grow.
     *
     * @throws \dml_exception
     * @param int $entryid
     * @return stdClass
     */
    protected function get_entry($entryid) {
        global $DB;

        if (empty($this->entriesdata[$entryid])) {
            $this->entriesdata[$entryid] = $DB->get_record_sql("SELECT ge.*, g.course, g.defaultapproval FROM {glossary_entries} ge
                                                                  JOIN {glossary} g ON g.id = ge.glossaryid
                                                                WHERE ge.id = ?", array('id' => $entryid), MUST_EXIST);
        }
        return $this->entriesdata[$entryid];
    }
}
