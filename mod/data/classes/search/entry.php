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
 * Search area for mod_data activity entries.
 *
 * @package    mod_data
 * @copyright  2016 Devang Gaur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_data\search;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/data/lib.php');
require_once($CFG->dirroot . '/lib/grouplib.php');

/**
 * Search area for mod_data activity entries.
 *
 * @package    mod_data
 * @copyright  2016 Devang Gaur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class entry extends \core_search\base_mod {

    /**
     * @var array Internal quick static cache.
     */
    protected $entriesdata = array();

    /**
     * Returns recordset containing required data for indexing database entries.
     *
     * @param int $modifiedfrom timestamp
     * @param \context|null $context Optional context to restrict scope of returned results
     * @return moodle_recordset|null Recordset (or null if no results)
     */
    public function get_document_recordset($modifiedfrom = 0, \context $context = null) {
        global $DB;

        list ($contextjoin, $contextparams) = $this->get_context_restriction_sql(
                $context, 'data', 'd', SQL_PARAMS_NAMED);
        if ($contextjoin === null) {
            return null;
        }

        $sql = "SELECT dr.*, d.course
                  FROM {data_records} dr
                  JOIN {data} d ON d.id = dr.dataid
          $contextjoin
                 WHERE dr.timemodified >= :timemodified";
        return $DB->get_recordset_sql($sql,
                array_merge($contextparams, ['timemodified' => $modifiedfrom]));
    }

    /**
     * Returns the documents associated with this glossary entry id.
     *
     * @param stdClass $entry glossary entry.
     * @param array    $options
     * @return \core_search\document
     */
    public function get_document($entry, $options = array()) {
        try {
            $cm = $this->get_cm('data', $entry->dataid, $entry->course);
            $context = \context_module::instance($cm->id);
        } catch (\dml_missing_record_exception $ex) {
            // Notify it as we run here as admin, we should see everything.
            debugging('Error retrieving mod_data ' . $entry->id . ' document, not all required data is available: ' .
                $ex->getMessage(), DEBUG_DEVELOPER);
            return false;
        } catch (\dml_exception $ex) {
            // Notify it as we run here as admin, we should see everything.
            debugging('Error retrieving mod_data' . $entry->id . ' document: ' . $ex->getMessage(), DEBUG_DEVELOPER);
            return false;
        }

        // Prepare associative array with data from DB.
        $doc = \core_search\document_factory::instance($entry->id, $this->componentname, $this->areaname);
        $doc->set('contextid', $context->id);
        $doc->set('courseid', $entry->course);
        $doc->set('userid', $entry->userid);
        if ($entry->groupid > 0) {
            $doc->set('groupid', $entry->groupid);
        }
        $doc->set('owneruserid', \core_search\manager::NO_OWNER_ID);
        $doc->set('modified', $entry->timemodified);

        $indexfields = $this->get_fields_for_entries($entry);

        if (count($indexfields) < 2) {
            return false;
        }

        // All fields should be already returned as plain text by data_field_base::get_content_value.
        $doc->set('title', $indexfields[0]);
        $doc->set('content', $indexfields[1]);

        if (isset($indexfields[2])) {
            $doc->set('description1', $indexfields[2]);
        }

        if (isset($indexfields[3])) {
            $doc->set('description2', $indexfields[3]);
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
        global $DB, $USER;

        if (isguestuser()) {
            return \core_search\manager::ACCESS_DENIED;
        }

        $now = time();

        $sql = "SELECT dr.*, d.*
                  FROM {data_records} dr
                  JOIN {data} d ON d.id = dr.dataid
                 WHERE dr.id = ?";

        $entry = $DB->get_record_sql($sql, array( $id ), IGNORE_MISSING);

        if (!$entry) {
            return \core_search\manager::ACCESS_DELETED;
        }

        if (($entry->timeviewfrom && $now < $entry->timeviewfrom) || ($entry->timeviewto && $now > $entry->timeviewto)) {
            return \core_search\manager::ACCESS_DENIED;
        }

        $cm = $this->get_cm('data', $entry->dataid, $entry->course);
        $context = \context_module::instance($cm->id);

        $canmanageentries = has_capability('mod/data:manageentries', $context);

        if (!has_capability('mod/data:viewentry', $context)) {
            return \core_search\manager::ACCESS_DENIED;
        }

        $numberofentriesindb = $DB->count_records('data_records', array('dataid' => $entry->dataid));
        $requiredentriestoview = $entry->requiredentriestoview;

        if ($requiredentriestoview && ($requiredentriestoview > $numberofentriesindb) &&
                ($USER->id != $entry->userid) && !$canmanageentries) {
            return \core_search\manager::ACCESS_DENIED;
        }

        if ($entry->approval && !$entry->approved && ($entry->userid != $USER->id) && !$canmanageentries) {
            return \core_search\manager::ACCESS_DENIED;
        }

        $currentgroup = groups_get_activity_group($cm, true);
        $groupmode = groups_get_activity_groupmode($cm);

        if (($groupmode == 1) && ($entry->groupid != $currentgroup) && !$canmanageentries) {
            return \core_search\manager::ACCESS_DENIED;
        }

        return \core_search\manager::ACCESS_GRANTED;
    }

    /**
     * Link to database entry.
     *
     * @param \core_search\document $doc
     * @return \moodle_url
     */
    public function get_doc_url(\core_search\document $doc) {
        $entry = $this->get_entry($doc->get('itemid'));
        return new \moodle_url('/mod/data/view.php', array( 'd' => $entry->dataid, 'rid' => $entry->id ));
    }

    /**
     * Link to the database activity.
     *
     * @param \core_search\document $doc
     * @return \moodle_url
     */
    public function get_context_url(\core_search\document $doc) {
        $entry = $this->get_entry($doc->get('itemid'));
        return new \moodle_url('/mod/data/view.php', array('d' => $entry->dataid));
    }

    /**
     * Returns true if this area uses file indexing.
     *
     * @return bool
     */
    public function uses_file_indexing() {
        return true;
    }

    /**
     * Add the database entries attachments.
     *
     * @param \core_search\document $doc
     * @return void
     */
    public function attach_files($doc) {
        global $DB;

        $entryid = $doc->get('itemid');

        try {
            $entry = $this->get_entry($entryid);
        } catch (\dml_missing_record_exception $e) {
            debugging('Could not get record to attach files to '.$doc->get('id'), DEBUG_DEVELOPER);
            return;
        }

        $cm = $this->get_cm('data', $entry->dataid, $doc->get('courseid'));
        $context = \context_module::instance($cm->id);

        // Get the files and attach them.
        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'mod_data', 'content', $entryid, 'filename', false);
        foreach ($files as $file) {
            $doc->add_stored_file($file);
        }
    }

    /**
     * Get database entry data
     *
     * @throws \dml_exception
     * @param int $entryid
     * @return stdClass
     */
    protected function get_entry($entryid) {
        global $DB;

        if (empty($this->entriesdata[$entryid])) {
            $this->entriesdata[$entryid] = $DB->get_record('data_records', array( 'id' => $entryid ), '*', MUST_EXIST);
        }

        return $this->entriesdata[$entryid];
    }

    /**
     * get_fields_for_entries
     *
     * @param StdClass $entry
     * @return array
     */
    protected function get_fields_for_entries($entry) {
        global $DB;

        $indexfields = array();
        $validfieldtypes = array('text', 'textarea', 'menu', 'radiobutton', 'checkbox', 'multimenu', 'url');

        $sql = "SELECT dc.*, df.name AS fldname,
                       df.type AS fieldtype, df.required
                  FROM {data_content} dc, {data_fields} df
                 WHERE dc.fieldid = df.id
                       AND dc.recordid = :recordid";

        $contents = $DB->get_records_sql($sql, array('recordid' => $entry->id));
        $filteredcontents = array();

        $template = $DB->get_record_sql('SELECT addtemplate FROM {data} WHERE id = ?', array($entry->dataid));
        $template = $template->addtemplate;

        // Filtering out the data_content records having invalid fieldtypes.
        foreach ($contents as $content) {
            if (in_array($content->fieldtype, $validfieldtypes)) {
                $filteredcontents[] = $content;
            }
        }

        foreach ($filteredcontents as $content) {
            $classname = $this->get_field_class_name($content->fieldtype);
            $content->priority = $classname::get_priority();
            $content->addtemplateposition = strpos($template, '[['.$content->fldname.']]');
        }

        $orderqueue = new \SPLPriorityQueue();

        // Filtering out contents which belong to fields that aren't present in the addtemplate of the database activity instance.
        foreach ($filteredcontents as $content) {

            if ($content->addtemplateposition >= 0) {
                $orderqueue->insert($content, $content->addtemplateposition);
            }
        }

        $filteredcontents = array();

        while ($orderqueue->valid()) {
            $filteredcontents[] = $orderqueue->extract();
        }

        // SPLPriorityQueue sorts according to descending order of the priority (here, addtemplateposition).
        $filteredcontents = array_reverse($filteredcontents);

        // Using a CUSTOM SPLPriorityQueure instance to sort out the filtered contents according to these rules :
        // 1. Priorities in $fieldtypepriorities
        // 2. Compulsory fieldtypes are to be given the top priority.
        $contentqueue = new sortedcontentqueue($filteredcontents);

        foreach ($filteredcontents as $key => $content) {
            $contentqueue->insert($content, $key);
        }

        while ($contentqueue->valid()) {

            $content = $contentqueue->extract();
            $classname = $this->get_field_class_name($content->fieldtype);
            $indexfields[] = $classname::get_content_value($content);
        }

        // Limited to 4 fields as a document only has 4 content fields.
        if (count($indexfields) > 4) {
            $indexfields[3] = implode(' ', array_slice($indexfields, 3));
        }
        return $indexfields;
    }

    /**
     * Returns the class name for that field type and includes it.
     *
     * @param string $fieldtype
     * @return string
     */
    protected function get_field_class_name($fieldtype) {
        global $CFG;

        $fieldtype = trim($fieldtype);
        require_once($CFG->dirroot . '/mod/data/field/' . $fieldtype . '/field.class.php');
        return 'data_field_' . $fieldtype;
    }

    /**
     * Confirms that data entries support group restrictions.
     *
     * @return bool True
     */
    public function supports_group_restriction() {
        return true;
    }
}
