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
 * Simple moodle database engine.
 *
 * @package    search_simpledb
 * @copyright  2016 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace search_simpledb;

defined('MOODLE_INTERNAL') || die();

/**
 * Simple moodle database engine.
 *
 * @package    search_simpledb
 * @copyright  2016 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class engine extends \core_search\engine {

    /**
     * Prepares a Solr query, applies filters and executes it returning its results.
     *
     * @throws \core_search\engine_exception
     * @param  stdClass     $filters Containing query and filters.
     * @param  array        $usercontexts Contexts where the user has access. True if the user can access all contexts.
     * @return \core_search\document[] Results or false if no results
     */
    public function execute_query($filters, $usercontexts) {
        global $DB, $USER;

        // Let's keep these changes internal.
        $data = clone $filters;

        $serverstatus = $this->is_server_ready();
        if ($serverstatus !== true) {
            throw new \core_search\engine_exception('engineserverstatus', 'search');
        }

        $sql = 'SELECT * FROM {search_simpledb_index} WHERE ';
        $params = array();

        // To store all conditions we will add to where.
        $ands = array();

        // Get results only available for the current user.
        $ands[] = '(owneruserid = ? OR owneruserid = ?)';
        $params = array_merge($params, array(\core_search\manager::NO_OWNER_ID, $USER->id));

        // Restrict it to the context where the user can access, we want this one cached.
        // If the user can access all contexts $usercontexts value is just true, we don't need to filter
        // in that case.
        if ($usercontexts && is_array($usercontexts)) {
            // Join all area contexts into a single array and implode.
            $allcontexts = array();
            foreach ($usercontexts as $areaid => $areacontexts) {
                if (!empty($data->areaid) && ($areaid !== $data->areaid)) {
                    // Skip unused areas.
                    continue;
                }
                foreach ($areacontexts as $contextid) {
                    // Ensure they are unique.
                    $allcontexts[$contextid] = $contextid;
                }
            }
            if (empty($allcontexts)) {
                // This means there are no valid contexts for them, so they get no results.
                return array();
            }

            list($contextsql, $contextparams) = $DB->get_in_or_equal($allcontexts);
            $ands[] = 'contextid ' . $contextsql;
            $params = array_merge($params, $contextparams);
        }

        // Course id filter.
        if (!empty($data->courseids)) {
            list($conditionsql, $conditionparams) = $DB->get_in_or_equal($data->courseids);
            $ands[] = 'courseid ' . $conditionsql;
            $params = array_merge($params, $conditionparams);
        }

        // Area id filter.
        if (!empty($data->areaid)) {
            list($conditionsql, $conditionparams) = $DB->get_in_or_equal($data->areaid);
            $ands[] = 'areaid ' . $conditionsql;
            $params = array_merge($params, $conditionparams);
        }

        if (!empty($data->title)) {
            list($conditionsql, $conditionparams) = $DB->get_in_or_equal($data->title);
            $ands[] = 'title ' . $conditionsql;
            $params = array_merge($params, $conditionparams);
        }

        if (!empty($data->timestart)) {
            $ands[] = 'modified >= ?';
            $params[] = $data->timestart;
        }
        if (!empty($data->timeend)) {
            $ands[] = 'modified <= ?';
            $params[] = $data->timeend;
        }

        // And finally the main query after applying all AND filters.
        $ands[] = '(' .
            $DB->sql_like('title', '?', false, false) . ' OR ' .
            $DB->sql_like('content', '?', false, false) . ' OR ' .
            $DB->sql_like('description1', '?', false, false) . ' OR ' .
            $DB->sql_like('description2', '?', false, false) .
            ')';
        $params[] = '%' . $data->q . '%';
        $params[] = '%' . $data->q . '%';
        $params[] = '%' . $data->q . '%';
        $params[] = '%' . $data->q . '%';

        $recordset = $DB->get_recordset_sql($sql . implode(' AND ', $ands), $params, 0, \core_search\manager::MAX_RESULTS);

        $numgranted = 0;

        if (!$recordset->valid()) {
            return array();
        }

        // Iterate through the results checking its availability and whether they are available for the user or not.
        $docs = array();
        foreach ($recordset as $docdata) {
            if (!$searcharea = $this->get_search_area($docdata->areaid)) {
                continue;
            }

            // Switch id back to the document id.
            $docdata->id = $docdata->docid;
            unset($docdata->docid);

            $access = $searcharea->check_access($docdata->itemid);
            switch ($access) {
                case \core_search\manager::ACCESS_DELETED:
                    $this->delete_by_id($docdata->id);
                    break;
                case \core_search\manager::ACCESS_DENIED:
                    break;
                case \core_search\manager::ACCESS_GRANTED:
                    $numgranted++;
                    $docs[] = $this->to_document($searcharea, (array)$docdata);
                    break;
            }

            // This should never happen.
            if ($numgranted >= \core_search\manager::MAX_RESULTS) {
                $docs = array_slice($docs, 0, \core_search\manager::MAX_RESULTS, true);
                break;
            }
        }
        $recordset->close();

        return $docs;
    }

    /**
     * Adds a document to the search engine.
     *
     * This does not commit to the search engine.
     *
     * @param \core_search\document $document
     * @param bool $fileindexing True if file indexing is to be used
     * @return bool False if the file was skipped or failed, true on success
     */
    public function add_document($document, $fileindexing = false) {
        global $DB;

        $doc = (object)$document->export_for_engine();

        // Moodle's ids using DML are always autoincremented.
        $doc->docid = $doc->id;
        unset($doc->id);

        $id = $DB->get_field('search_simpledb_index', 'id', array('docid' => $doc->docid));
        try {
            if ($id) {
                $doc->id = $id;
                $DB->update_record('search_simpledb_index', $doc);
            } else {
                $DB->insert_record('search_simpledb_index', $doc);
            }

        } catch (dml_exception $ex) {
            debugging('dml error while trying to insert document with id ' . $doc->docid . ': ' . $e->getMessage(),
                DEBUG_DEVELOPER);
        }

        return true;
    }

    /**
     * Deletes the specified document.
     *
     * @param string $id The document id to delete
     * @return void
     */
    public function delete_by_id($id) {
        global $DB;
        $DB->delete_records('search_simpledb_index', array('docid' => $id));
    }

    /**
     * Delete all area's documents.
     *
     * @param string $areaid
     * @return void
     */
    public function delete($areaid = null) {
        global $DB;
        if ($areaid) {
            $DB->delete_records('search_simpledb_index', array('areaid' => $areaid));
        } else {
            $DB->delete_records('search_simpledb_index');
        }
    }

    /**
     * Checks that the required table was installed.
     *
     * @return true|string Returns true if all good or an error string.
     */
    public function is_server_ready() {
        global $DB;
        if (!$DB->get_manager()->table_exists('search_simpledb_index')) {
            return 'search_simpledb_index table does not exist';
        }

        return true;
    }

    /**
     * It is always installed.
     *
     * @return true
     */
    public function is_installed() {
        return true;
    }
}
