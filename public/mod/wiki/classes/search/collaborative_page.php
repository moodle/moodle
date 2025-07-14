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
 * Search area for mod_wiki collaborative pages.
 *
 * @package    mod_wiki
 * @copyright  2016 Eric Merrill {@link http://www.merrilldigital.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_wiki\search;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/wiki/locallib.php');

/**
 * Search area for mod_wiki collaborative pages.
 *
 * @package    mod_wiki
 * @copyright  2016 Eric Merrill {@link http://www.merrilldigital.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class collaborative_page extends \core_search\base_mod {
    /**
     * @var array Cache of wiki records.
     */
    protected $wikiscache = array();

    /**
     * Returns a recordset with all required page information.
     *
     * @param int $modifiedfrom
     * @param \context|null $context Optional context to restrict scope of returned results
     * @return moodle_recordset|null Recordset (or null if no results)
     */
    public function get_document_recordset($modifiedfrom = 0, ?\context $context = null) {
        global $DB;

        list ($contextjoin, $contextparams) = $this->get_context_restriction_sql(
                $context, 'wiki', 'w');
        if ($contextjoin === null) {
            return null;
        }

        $sql = "SELECT p.*, w.id AS wikiid, w.course AS courseid, s.groupid AS groupid
                  FROM {wiki_pages} p
                  JOIN {wiki_subwikis} s ON s.id = p.subwikiid
                  JOIN {wiki} w ON w.id = s.wikiid
          $contextjoin
                 WHERE p.timemodified >= ?
                   AND w.wikimode = ?
              ORDER BY p.timemodified ASC";
        return $DB->get_recordset_sql($sql, array_merge($contextparams,
                [$modifiedfrom, 'collaborative']));
    }

    /**
     * Returns the document for a particular page.
     *
     * @param \stdClass $record A record containing, at least, the indexed document id and a modified timestamp
     * @param array     $options Options for document creation
     * @return \core_search\document
     */
    public function get_document($record, $options = array()) {
        try {
            $cm = $this->get_cm('wiki', $record->wikiid, $record->courseid);
            $context = \context_module::instance($cm->id);
        } catch (\dml_missing_record_exception $ex) {
            // Notify it as we run here as admin, we should see everything.
            debugging('Error retrieving ' . $this->areaid . ' ' . $record->id . ' document, not all required data is available: ' .
                $ex->getMessage(), DEBUG_DEVELOPER);
            return false;
        } catch (\dml_exception $ex) {
            // Notify it as we run here as admin, we should see everything.
            debugging('Error retrieving ' . $this->areaid . ' ' . $record->id . ' document: ' . $ex->getMessage(), DEBUG_DEVELOPER);
            return false;
        }

        // Make a page object without extra fields.
        $page = clone $record;
        unset($page->courseid);
        unset($page->wikiid);

        // Conversion based wiki_print_page_content().
        // Check if we have passed the cache time.
        if ($page->timerendered + WIKI_REFRESH_CACHE_TIME < time()) {
            $content = wiki_refresh_cachedcontent($page);
            $page = $content['page'];
        }
        // Convert to text.
        $content = content_to_text($page->cachedcontent, FORMAT_MOODLE);

        // Prepare associative array with data from DB.
        $doc = \core_search\document_factory::instance($record->id, $this->componentname, $this->areaname);
        $doc->set('title', content_to_text($record->title, false));
        $doc->set('content', $content);
        $doc->set('contextid', $context->id);
        $doc->set('courseid', $record->courseid);
        if ($record->groupid > 0) {
            $doc->set('groupid', $record->groupid);
        }
        $doc->set('owneruserid', \core_search\manager::NO_OWNER_ID);
        $doc->set('modified', $record->timemodified);

        // Check if this document should be considered new.
        if (isset($options['lastindexedtime']) && ($options['lastindexedtime'] < $record->timecreated)) {
            // If the document was created after the last index time, it must be new.
            $doc->set_is_new(true);
        }

        return $doc;
    }

    /**
     * Can the current user see the document.
     *
     * @param int $id The internal search area entity id.
     * @return bool True if the user can see it, false otherwise
     */
    public function check_access($id) {
        global $DB;

        try {
            $page = $DB->get_record('wiki_pages', array('id' => $id), '*', MUST_EXIST);
            if (!isset($this->wikiscache[$page->subwikiid])) {
                $sql = 'SELECT w.*
                          FROM {wiki_subwikis} s
                          JOIN {wiki} w ON w.id = s.wikiid
                         WHERE s.id = ?';
                $this->wikiscache[$page->subwikiid] = $DB->get_record_sql($sql, array('id' => $page->subwikiid), MUST_EXIST);
            }
            $wiki = $this->wikiscache[$page->subwikiid];
            $cminfo = $this->get_cm('wiki', $wiki->id, $wiki->course);
        } catch (\dml_missing_record_exception $ex) {
            return \core_search\manager::ACCESS_DELETED;
        } catch (\dml_exception $ex) {
            return \core_search\manager::ACCESS_DENIED;
        }

        // Recheck uservisible although it should have already been checked in core_search.
        if ($cminfo->uservisible === false) {
            return \core_search\manager::ACCESS_DENIED;
        }

        $context = \context_module::instance($cminfo->id);

        if (!has_capability('mod/wiki:viewpage', $context)) {
            return \core_search\manager::ACCESS_DENIED;
        }

        return \core_search\manager::ACCESS_GRANTED;
    }

    /**
     * Returns a url to the page.
     *
     * @param \core_search\document $doc
     * @return \moodle_url
     */
    public function get_doc_url(\core_search\document $doc) {
        $params = array('pageid' => $doc->get('itemid'));
        return new \moodle_url('/mod/wiki/view.php', $params);
    }

    /**
     * Returns a url to the wiki.
     *
     * @param \core_search\document $doc
     * @return \moodle_url
     */
    public function get_context_url(\core_search\document $doc) {
        $contextmodule = \context::instance_by_id($doc->get('contextid'));
        return new \moodle_url('/mod/wiki/view.php', array('id' => $contextmodule->instanceid));
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
     * Return the context info required to index files for
     * this search area.
     *
     * @return array
     */
    public function get_search_fileareas() {
        $fileareas = array('attachments'); // Filearea.

        return $fileareas;
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
