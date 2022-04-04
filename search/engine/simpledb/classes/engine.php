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
     * Total number of available results.
     *
     * @var null|int
     */
    protected $totalresults = null;

    /**
     * Prepares a SQL query, applies filters and executes it returning its results.
     *
     * @throws \core_search\engine_exception
     * @param  stdClass     $filters Containing query and filters.
     * @param  array        $usercontexts Contexts where the user has access. True if the user can access all contexts.
     * @param  int          $limit The maximum number of results to return.
     * @return \core_search\document[] Results or false if no results
     */
    public function execute_query($filters, $usercontexts, $limit = 0) {
        global $DB, $USER;

        $serverstatus = $this->is_server_ready();
        if ($serverstatus !== true) {
            throw new \core_search\engine_exception('engineserverstatus', 'search');
        }

        if (empty($limit)) {
            $limit = \core_search\manager::MAX_RESULTS;
        }

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
                if (!empty($filters->areaids) && !in_array($areaid, $filters->areaids)) {
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
        if (!empty($filters->courseids)) {
            list($conditionsql, $conditionparams) = $DB->get_in_or_equal($filters->courseids);
            $ands[] = 'courseid ' . $conditionsql;
            $params = array_merge($params, $conditionparams);
        }

        // Area id filter.
        if (!empty($filters->areaids)) {
            list($conditionsql, $conditionparams) = $DB->get_in_or_equal($filters->areaids);
            $ands[] = 'areaid ' . $conditionsql;
            $params = array_merge($params, $conditionparams);
        }

        if (!empty($filters->title)) {
            $ands[] = $DB->sql_like('title', '?', false, false);
            $params[] = $filters->title;
        }

        if (!empty($filters->timestart)) {
            $ands[] = 'modified >= ?';
            $params[] = $filters->timestart;
        }
        if (!empty($filters->timeend)) {
            $ands[] = 'modified <= ?';
            $params[] = $filters->timeend;
        }

        // And finally the main query after applying all AND filters.
        if (!empty($filters->q)) {
            switch ($DB->get_dbfamily()) {
                case 'postgres':
                    $ands[] = "(" .
                        "to_tsvector('simple', title) @@ plainto_tsquery('simple', ?) OR ".
                        "to_tsvector('simple', content) @@ plainto_tsquery('simple', ?) OR ".
                        "to_tsvector('simple', description1) @@ plainto_tsquery('simple', ?) OR ".
                        "to_tsvector('simple', description2) @@ plainto_tsquery('simple', ?)".
                        ")";
                    $params[] = $filters->q;
                    $params[] = $filters->q;
                    $params[] = $filters->q;
                    $params[] = $filters->q;
                    break;
                case 'mysql':
                    if ($DB->is_fulltext_search_supported()) {
                        $ands[] = "MATCH (title, content, description1, description2) AGAINST (?)";
                        $params[] = $filters->q;

                        // Sorry for the hack, but it does not seem that we will have a solution for
                        // this soon (https://bugs.mysql.com/bug.php?id=78485).
                        if ($filters->q === '*') {
                            return array();
                        }
                    } else {
                        // Clumsy version for mysql versions with no fulltext support.
                        list($queryand, $queryparams) = $this->get_simple_query($filters->q);
                        $ands[] = $queryand;
                        $params = array_merge($params, $queryparams);
                    }
                    break;
                case 'mssql':
                    if ($DB->is_fulltext_search_supported()) {
                        $ands[] = "CONTAINS ((title, content, description1, description2), ?)";
                        // Special treatment for double quotes:
                        // - Puntuation is ignored so we can get rid of them.
                        // - Phrases should be enclosed in double quotation marks.
                        $params[] = '"' . str_replace('"', '', $filters->q) . '"';
                    } else {
                        // Clumsy version for mysql versions with no fulltext support.
                        list($queryand, $queryparams) = $this->get_simple_query($filters->q);
                        $ands[] = $queryand;
                        $params = array_merge($params, $queryparams);
                    }
                    break;
                default:
                    list($queryand, $queryparams) = $this->get_simple_query($filters->q);
                    $ands[] = $queryand;
                    $params = array_merge($params, $queryparams);
                    break;
            }
        }

        // It is limited to $limit, no need to use recordsets.
        $documents = $DB->get_records_select('search_simpledb_index', implode(' AND ', $ands), $params, '', '*', 0, $limit);

        // Hopefully database cached results as this applies the same filters than above.
        $this->totalresults = $DB->count_records_select('search_simpledb_index', implode(' AND ', $ands), $params);

        $numgranted = 0;

        // Iterate through the results checking its availability and whether they are available for the user or not.
        $docs = array();
        foreach ($documents as $docdata) {
            if ($docdata->owneruserid != \core_search\manager::NO_OWNER_ID && $docdata->owneruserid != $USER->id) {
                // If owneruserid is set, no other user should be able to access this record.
                continue;
            }

            if (!$searcharea = $this->get_search_area($docdata->areaid)) {
                $this->totalresults--;
                continue;
            }

            // Switch id back to the document id.
            $docdata->id = $docdata->docid;
            unset($docdata->docid);

            $access = $searcharea->check_access($docdata->itemid);
            switch ($access) {
                case \core_search\manager::ACCESS_DELETED:
                    $this->delete_by_id($docdata->id);
                    $this->totalresults--;
                    break;
                case \core_search\manager::ACCESS_DENIED:
                    $this->totalresults--;
                    break;
                case \core_search\manager::ACCESS_GRANTED:
                    $numgranted++;
                    $docs[] = $this->to_document($searcharea, (array)$docdata);
                    break;
            }

            // This should never happen.
            if ($numgranted >= $limit) {
                $docs = array_slice($docs, 0, $limit, true);
                break;
            }
        }

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

        } catch (\dml_exception $ex) {
            debugging('dml error while trying to insert document with id ' . $doc->docid . ': ' . $ex->getMessage(),
                DEBUG_DEVELOPER);
            return false;
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

    /**
     * Returns the total results.
     *
     * Including skipped results.
     *
     * @return int
     */
    public function get_query_total_count() {
        if (!is_null($this->totalresults)) {
            // This is a just in case as we count total results in execute_query.
            return \core_search\manager::MAX_RESULTS;
        }

        return $this->totalresults;
    }

    /**
     * Returns the default query for db engines.
     *
     * @param string $q The query string
     * @return array SQL string and params list
     */
    protected function get_simple_query($q) {
        global $DB;

        $sql = '(' .
            $DB->sql_like('title', '?', false, false) . ' OR ' .
            $DB->sql_like('content', '?', false, false) . ' OR ' .
            $DB->sql_like('description1', '?', false, false) . ' OR ' .
            $DB->sql_like('description2', '?', false, false) .
            ')';

        // Remove quotes from the query.
        $q = str_replace('"', '', $q);
        $params = [
            '%' . $q . '%',
            '%' . $q . '%',
            '%' . $q . '%',
            '%' . $q . '%'
        ];

        return array($sql, $params);
    }

    /**
     * Simpledb supports deleting the index for a context.
     *
     * @param int $oldcontextid Context that has been deleted
     * @return bool True to indicate that any data was actually deleted
     * @throws \core_search\engine_exception
     */
    public function delete_index_for_context(int $oldcontextid) {
        global $DB;
        try {
            $DB->delete_records('search_simpledb_index', ['contextid' => $oldcontextid]);
        } catch (\dml_exception $e) {
            throw new \core_search\engine_exception('dbupdatefailed');
        }
        return true;
    }

    /**
     * Simpledb supports deleting the index for a course.
     *
     * @param int $oldcourseid
     * @return bool True to indicate that any data was actually deleted
     * @throws \core_search\engine_exception
     */
    public function delete_index_for_course(int $oldcourseid) {
        global $DB;
        try {
            $DB->delete_records('search_simpledb_index', ['courseid' => $oldcourseid]);
        } catch (\dml_exception $e) {
            throw new \core_search\engine_exception('dbupdatefailed');
        }
        return true;
    }
}
