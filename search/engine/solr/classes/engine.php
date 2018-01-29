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
 * Solr engine.
 *
 * @package    search_solr
 * @copyright  2015 Daniel Neis Araujo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace search_solr;

defined('MOODLE_INTERNAL') || die();

/**
 * Solr engine.
 *
 * @package    search_solr
 * @copyright  2015 Daniel Neis Araujo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class engine extends \core_search\engine {

    /**
     * @var string The date format used by solr.
     */
    const DATE_FORMAT = 'Y-m-d\TH:i:s\Z';

    /**
     * @var int Commit documents interval (number of miliseconds).
     */
    const AUTOCOMMIT_WITHIN = 15000;

    /**
     * The maximum number of results to fetch at a time.
     */
    const QUERY_SIZE = 120;

    /**
     * Highlighting fragsize. Slightly larger than output size (500) to allow for ... appending.
     */
    const FRAG_SIZE = 510;

    /**
     * Marker for the start of a highlight.
     */
    const HIGHLIGHT_START = '@@HI_S@@';

    /**
     * Marker for the end of a highlight.
     */
    const HIGHLIGHT_END = '@@HI_E@@';

    /**
     * @var \SolrClient
     */
    protected $client = null;

    /**
     * @var bool True if we should reuse SolrClients, false if not.
     */
    protected $cacheclient = true;

    /**
     * @var \curl Direct curl object.
     */
    protected $curl = null;

    /**
     * @var array Fields that can be highlighted.
     */
    protected $highlightfields = array('title', 'content', 'description1', 'description2');

    /**
     * @var int Number of total docs reported by Sorl for the last query.
     */
    protected $totalenginedocs = 0;

    /**
     * @var int Number of docs we have processed for the last query.
     */
    protected $processeddocs = 0;

    /**
     * @var int Number of docs that have been skipped while processing the last query.
     */
    protected $skippeddocs = 0;

    /**
     * Initialises the search engine configuration.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();

        $curlversion = curl_version();
        if (isset($curlversion['version']) && stripos($curlversion['version'], '7.35.') === 0) {
            // There is a flaw with curl 7.35.0 that causes problems with client reuse.
            $this->cacheclient = false;
        }
    }

    /**
     * Prepares a Solr query, applies filters and executes it returning its results.
     *
     * @throws \core_search\engine_exception
     * @param  stdClass  $filters Containing query and filters.
     * @param  array     $usercontexts Contexts where the user has access. True if the user can access all contexts.
     * @param  int       $limit The maximum number of results to return.
     * @return \core_search\document[] Results or false if no results
     */
    public function execute_query($filters, $usercontexts, $limit = 0) {
        global $USER;

        if (empty($limit)) {
            $limit = \core_search\manager::MAX_RESULTS;
        }

        // If there is any problem we trigger the exception as soon as possible.
        $client = $this->get_search_client();

        // Create the query object.
        $query = $this->create_user_query($filters, $usercontexts);

        // We expect good match rates, so for our first get, we will get a small number of records.
        // This significantly speeds solr response time for first few pages.
        $query->setRows(min($limit * 3, static::QUERY_SIZE));
        $response = $this->get_query_response($query);

        // Get count data out of the response, and reset our counters.
        list($included, $found) = $this->get_response_counts($response);
        $this->totalenginedocs = $found;
        $this->processeddocs = 0;
        $this->skippeddocs = 0;
        if ($included == 0 || $this->totalenginedocs == 0) {
            // No results.
            return array();
        }

        // Get valid documents out of the response.
        $results = $this->process_response($response, $limit);

        // We have processed all the docs in the response at this point.
        $this->processeddocs += $included;

        // If we haven't reached the limit, and there are more docs left in Solr, lets keep trying.
        while (count($results) < $limit && ($this->totalenginedocs - $this->processeddocs) > 0) {
            // Offset the start of the query, and since we are making another call, get more per call.
            $query->setStart($this->processeddocs);
            $query->setRows(static::QUERY_SIZE);

            $response = $this->get_query_response($query);
            list($included, $found) = $this->get_response_counts($response);
            if ($included == 0 || $found == 0) {
                // No new results were found. Found being empty would be weird, so we will just return.
                return $results;
            }
            $this->totalenginedocs = $found;

            // Get the new response docs, limiting to remaining we need, then add it to the end of the results array.
            $newdocs = $this->process_response($response, $limit - count($results));
            $results = array_merge($results, $newdocs);

            // Add to our processed docs count.
            $this->processeddocs += $included;
        }

        return $results;
    }

    /**
     * Takes a query and returns the response in SolrObject format.
     *
     * @param  SolrQuery  $query Solr query object.
     * @return SolrObject|false Response document or false on error.
     */
    protected function get_query_response($query) {
        try {
            return $this->get_search_client()->query($query)->getResponse();
        } catch (\SolrClientException $ex) {
            debugging('Error executing the provided query: ' . $ex->getMessage(), DEBUG_DEVELOPER);
            $this->queryerror = $ex->getMessage();
            return false;
        } catch (\SolrServerException $ex) {
            debugging('Error executing the provided query: ' . $ex->getMessage(), DEBUG_DEVELOPER);
            $this->queryerror = $ex->getMessage();
            return false;
        }
    }

    /**
     * Returns the total number of documents available for the most recently call to execute_query.
     *
     * @return int
     */
    public function get_query_total_count() {
        // Return the total engine count minus the docs we have determined are bad.
        return $this->totalenginedocs - $this->skippeddocs;
    }

    /**
     * Returns count information for a provided response. Will return 0, 0 for invalid or empty responses.
     *
     * @param SolrDocument $response The response document from Solr.
     * @return array A two part array. First how many response docs are in the response.
     *               Second, how many results are vailable in the engine.
     */
    protected function get_response_counts($response) {
        $found = 0;
        $included = 0;

        if (isset($response->grouped->solr_filegroupingid->ngroups)) {
            // Get the number of results for file grouped queries.
            $found = $response->grouped->solr_filegroupingid->ngroups;
            $included = count($response->grouped->solr_filegroupingid->groups);
        } else if (isset($response->response->numFound)) {
            // Get the number of results for standard queries.
            $found = $response->response->numFound;
            if ($found > 0 && is_array($response->response->docs)) {
                $included = count($response->response->docs);
            }
        }

        return array($included, $found);
    }

    /**
     * Prepares a new query object with needed limits, filters, etc.
     *
     * @param stdClass  $filters Containing query and filters.
     * @param array     $usercontexts Contexts where the user has access. True if the user can access all contexts.
     * @return SolrDisMaxQuery
     */
    protected function create_user_query($filters, $usercontexts) {
        global $USER;

        // Let's keep these changes internal.
        $data = clone $filters;

        $query = new \SolrDisMaxQuery();

        $this->set_query($query, $data->q);
        $this->add_fields($query);

        // Search filters applied, we don't cache these filters as we don't want to pollute the cache with tmp filters
        // we are really interested in caching contexts filters instead.
        if (!empty($data->title)) {
            $query->addFilterQuery('{!field cache=false f=title}' . $data->title);
        }
        if (!empty($data->areaids)) {
            // If areaids are specified, we want to get any that match.
            $query->addFilterQuery('{!cache=false}areaid:(' . implode(' OR ', $data->areaids) . ')');
        }
        if (!empty($data->courseids)) {
            $query->addFilterQuery('{!cache=false}courseid:(' . implode(' OR ', $data->courseids) . ')');
        }

        if (!empty($data->timestart) or !empty($data->timeend)) {
            if (empty($data->timestart)) {
                $data->timestart = '*';
            } else {
                $data->timestart = \search_solr\document::format_time_for_engine($data->timestart);
            }
            if (empty($data->timeend)) {
                $data->timeend = '*';
            } else {
                $data->timeend = \search_solr\document::format_time_for_engine($data->timeend);
            }

            // No cache.
            $query->addFilterQuery('{!cache=false}modified:[' . $data->timestart . ' TO ' . $data->timeend . ']');
        }

        // Restrict to users who are supposed to be able to see a particular result.
        $query->addFilterQuery('owneruserid:(' . \core_search\manager::NO_OWNER_ID . ' OR ' . $USER->id . ')');

        // And finally restrict it to the context where the user can access, we want this one cached.
        // If the user can access all contexts $usercontexts value is just true, we don't need to filter
        // in that case.
        if ($usercontexts && is_array($usercontexts)) {
            // Join all area contexts into a single array and implode.
            $allcontexts = array();
            foreach ($usercontexts as $areaid => $areacontexts) {
                if (!empty($data->areaids) && !in_array($areaid, $data->areaids)) {
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
            $query->addFilterQuery('contextid:(' . implode(' OR ', $allcontexts) . ')');
        }

        if ($this->file_indexing_enabled()) {
            // Now group records by solr_filegroupingid. Limit to 3 results per group.
            $query->setGroup(true);
            $query->setGroupLimit(3);
            $query->setGroupNGroups(true);
            $query->addGroupField('solr_filegroupingid');
        } else {
            // Make sure we only get text files, in case the index has pre-existing files.
            $query->addFilterQuery('type:'.\core_search\manager::TYPE_TEXT);
        }

        return $query;
    }

    /**
     * Prepares a new query by setting the query, start offset and rows to return.
     *
     * @param SolrQuery $query
     * @param object    $q Containing query and filters.
     */
    protected function set_query($query, $q) {
        // Set hightlighting.
        $query->setHighlight(true);
        foreach ($this->highlightfields as $field) {
            $query->addHighlightField($field);
        }
        $query->setHighlightFragsize(static::FRAG_SIZE);
        $query->setHighlightSimplePre(self::HIGHLIGHT_START);
        $query->setHighlightSimplePost(self::HIGHLIGHT_END);
        $query->setHighlightMergeContiguous(true);

        $query->setQuery($q);

        // A reasonable max.
        $query->setRows(static::QUERY_SIZE);
    }

    /**
     * Sets fields to be returned in the result.
     *
     * @param SolrDisMaxQuery|SolrQuery $query object.
     */
    public function add_fields($query) {
        $documentclass = $this->get_document_classname();
        $fields = $documentclass::get_default_fields_definition();

        $dismax = false;
        if ($query instanceof \SolrDisMaxQuery) {
            $dismax = true;
        }

        foreach ($fields as $key => $field) {
            $query->addField($key);
            if ($dismax && !empty($field['mainquery'])) {
                // Add fields the main query should be run against.
                $query->addQueryField($key);
            }
        }
    }

    /**
     * Finds the key common to both highlighing and docs array returned from response.
     * @param object $response containing results.
     */
    public function add_highlight_content($response) {
        if (!isset($response->highlighting)) {
            // There is no highlighting to add.
            return;
        }

        $highlightedobject = $response->highlighting;
        foreach ($response->response->docs as $doc) {
            $x = $doc->id;
            $highlighteddoc = $highlightedobject->$x;
            $this->merge_highlight_field_values($doc, $highlighteddoc);
        }
    }

    /**
     * Adds the highlighting array values to docs array values.
     *
     * @throws \core_search\engine_exception
     * @param object $doc containing the results.
     * @param object $highlighteddoc containing the highlighted results values.
     */
    public function merge_highlight_field_values($doc, $highlighteddoc) {

        foreach ($this->highlightfields as $field) {
            if (!empty($doc->$field)) {

                // Check that the returned value is not an array. No way we can make this work with multivalued solr fields.
                if (is_array($doc->{$field})) {
                    throw new \core_search\engine_exception('multivaluedfield', 'search_solr', '', $field);
                }

                if (!empty($highlighteddoc->$field)) {
                    // Replace by the highlighted result.
                    $doc->$field = reset($highlighteddoc->$field);
                }
            }
        }
    }

    /**
     * Filters the response on Moodle side.
     *
     * @param SolrObject $response Solr object containing the response return from solr server.
     * @param int        $limit The maximum number of results to return. 0 for all.
     * @param bool       $skipaccesscheck Don't use check_access() on results. Only to be used when results have known access.
     * @return array $results containing final results to be displayed.
     */
    protected function process_response($response, $limit = 0, $skipaccesscheck = false) {
        global $USER;

        if (empty($response)) {
            return array();
        }

        if (isset($response->grouped)) {
            return $this->grouped_files_process_response($response, $limit);
        }

        $userid = $USER->id;
        $noownerid = \core_search\manager::NO_OWNER_ID;

        $numgranted = 0;

        if (!$docs = $response->response->docs) {
            return array();
        }

        $out = array();
        if (!empty($response->response->numFound)) {
            $this->add_highlight_content($response);

            // Iterate through the results checking its availability and whether they are available for the user or not.
            foreach ($docs as $key => $docdata) {
                if ($docdata['owneruserid'] != $noownerid && $docdata['owneruserid'] != $userid) {
                    // If owneruserid is set, no other user should be able to access this record.
                    continue;
                }

                if (!$searcharea = $this->get_search_area($docdata->areaid)) {
                    continue;
                }

                $docdata = $this->standarize_solr_obj($docdata);

                if ($skipaccesscheck) {
                    $access = \core_search\manager::ACCESS_GRANTED;
                } else {
                    $access = $searcharea->check_access($docdata['itemid']);
                }
                switch ($access) {
                    case \core_search\manager::ACCESS_DELETED:
                        $this->delete_by_id($docdata['id']);
                        // Remove one from our processed and total counters, since we promptly deleted.
                        $this->processeddocs--;
                        $this->totalenginedocs--;
                        break;
                    case \core_search\manager::ACCESS_DENIED:
                        $this->skippeddocs++;
                        break;
                    case \core_search\manager::ACCESS_GRANTED:
                        $numgranted++;

                        // Add the doc.
                        $out[] = $this->to_document($searcharea, $docdata);
                        break;
                }

                // Stop when we hit our limit.
                if (!empty($limit) && count($out) >= $limit) {
                    break;
                }
            }
        }

        return $out;
    }

    /**
     * Processes grouped file results into documents, with attached matching files.
     *
     * @param SolrObject $response The response returned from solr server
     * @param int        $limit The maximum number of results to return. 0 for all.
     * @return array Final results to be displayed.
     */
    protected function grouped_files_process_response($response, $limit = 0) {
        // If we can't find the grouping, or there are no matches in the grouping, return empty.
        if (!isset($response->grouped->solr_filegroupingid) || empty($response->grouped->solr_filegroupingid->matches)) {
            return array();
        }

        $numgranted = 0;
        $orderedids = array();
        $completedocs = array();
        $incompletedocs = array();

        $highlightingobj = $response->highlighting;

        // Each group represents a "master document".
        $groups = $response->grouped->solr_filegroupingid->groups;
        foreach ($groups as $group) {
            $groupid = $group->groupValue;
            $groupdocs = $group->doclist->docs;
            $firstdoc = reset($groupdocs);

            if (!$searcharea = $this->get_search_area($firstdoc->areaid)) {
                // Well, this is a problem.
                continue;
            }

            // Check for access.
            $access = $searcharea->check_access($firstdoc->itemid);
            switch ($access) {
                case \core_search\manager::ACCESS_DELETED:
                    // If deleted from Moodle, delete from index and then continue.
                    $this->delete_by_id($firstdoc->id);
                    // Remove one from our processed and total counters, since we promptly deleted.
                    $this->processeddocs--;
                    $this->totalenginedocs--;
                    continue 2;
                    break;
                case \core_search\manager::ACCESS_DENIED:
                    // This means we should just skip for the current user.
                    $this->skippeddocs++;
                    continue 2;
                    break;
            }
            $numgranted++;

            $maindoc = false;
            $fileids = array();
            // Seperate the main document and any files returned.
            foreach ($groupdocs as $groupdoc) {
                if ($groupdoc->id == $groupid) {
                    $maindoc = $groupdoc;
                } else if (isset($groupdoc->solr_fileid)) {
                    $fileids[] = $groupdoc->solr_fileid;
                }
            }

            // Store the id of this group, in order, for later merging.
            $orderedids[] = $groupid;

            if (!$maindoc) {
                // We don't have the main doc, store what we know for later building.
                $incompletedocs[$groupid] = $fileids;
            } else {
                if (isset($highlightingobj->$groupid)) {
                    // Merge the highlighting for this doc.
                    $this->merge_highlight_field_values($maindoc, $highlightingobj->$groupid);
                }
                $docdata = $this->standarize_solr_obj($maindoc);
                $doc = $this->to_document($searcharea, $docdata);
                // Now we need to attach the result files to the doc.
                foreach ($fileids as $fileid) {
                    $doc->add_stored_file($fileid);
                }
                $completedocs[$groupid] = $doc;
            }

            if (!empty($limit) && $numgranted >= $limit) {
                // We have hit the max results, we will just ignore the rest.
                break;
            }
        }

        $incompletedocs = $this->get_missing_docs($incompletedocs);

        $out = array();
        // Now merge the complete and incomplete documents, in results order.
        foreach ($orderedids as $docid) {
            if (isset($completedocs[$docid])) {
                $out[] = $completedocs[$docid];
            } else if (isset($incompletedocs[$docid])) {
                $out[] = $incompletedocs[$docid];
            }
        }

        return $out;
    }

    /**
     * Retreive any missing main documents and attach provided files.
     *
     * The missingdocs array should be an array, indexed by document id, of main documents we need to retrieve. The value
     * associated to the key should be an array of stored_files or stored file ids to attach to the result document.
     *
     * Return array also indexed by document id.
     *
     * @param array() $missingdocs An array, indexed by document id, with arrays of files/ids to attach.
     * @return document[]
     */
    protected function get_missing_docs($missingdocs) {
        if (empty($missingdocs)) {
            return array();
        }

        $docids = array_keys($missingdocs);

        // Build a custom query that will get all the missing documents.
        $query = new \SolrQuery();
        $this->set_query($query, '*');
        $this->add_fields($query);
        $query->setRows(count($docids));
        $query->addFilterQuery('{!cache=false}id:(' . implode(' OR ', $docids) . ')');

        $response = $this->get_query_response($query);
        // We know the missing docs have already been checked for access, so don't recheck.
        $results = $this->process_response($response, 0, true);

        $out = array();
        foreach ($results as $result) {
            $resultid = $result->get('id');
            if (!isset($missingdocs[$resultid])) {
                // We got a result we didn't expect. Skip it.
                continue;
            }
            // Attach the files.
            foreach ($missingdocs[$resultid] as $filedoc) {
                $result->add_stored_file($filedoc);
            }
            $out[$resultid] = $result;
        }

        return $out;
    }

    /**
     * Returns a standard php array from a \SolrObject instance.
     *
     * @param \SolrObject $obj
     * @return array The returned document as an array.
     */
    public function standarize_solr_obj(\SolrObject $obj) {
        $properties = $obj->getPropertyNames();

        $docdata = array();
        foreach($properties as $name) {
            // http://php.net/manual/en/solrobject.getpropertynames.php#98018.
            $name = trim($name);
            $docdata[$name] = $obj->offsetGet($name);
        }
        return $docdata;
    }

    /**
     * Adds a document to the search engine.
     *
     * This does not commit to the search engine.
     *
     * @param document $document
     * @param bool     $fileindexing True if file indexing is to be used
     * @return bool
     */
    public function add_document($document, $fileindexing = false) {
        $docdata = $document->export_for_engine();

        if (!$this->add_solr_document($docdata)) {
            return false;
        }

        if ($fileindexing) {
            // This will take care of updating all attached files in the index.
            $this->process_document_files($document);
        }

        return true;
    }

    /**
     * Adds a text document to the search engine.
     *
     * @param array $doc
     * @return bool
     */
    protected function add_solr_document($doc) {
        $solrdoc = new \SolrInputDocument();
        foreach ($doc as $field => $value) {
            $solrdoc->addField($field, $value);
        }

        try {
            $result = $this->get_search_client()->addDocument($solrdoc, true, static::AUTOCOMMIT_WITHIN);
            return true;
        } catch (\SolrClientException $e) {
            debugging('Solr client error adding document with id ' . $doc['id'] . ': ' . $e->getMessage(), DEBUG_DEVELOPER);
        } catch (\SolrServerException $e) {
            // We only use the first line of the message, as it's a fully java stacktrace behind it.
            $msg = strtok($e->getMessage(), "\n");
            debugging('Solr server error adding document with id ' . $doc['id'] . ': ' . $msg, DEBUG_DEVELOPER);
        }

        return false;
    }

    /**
     * Index files attached to the docuemnt, ensuring the index matches the current document files.
     *
     * For documents that aren't known to be new, we check the index for existing files.
     * - New files we will add.
     * - Existing and unchanged files we will skip.
     * - File that are in the index but not on the document will be deleted from the index.
     * - Files that have changed will be re-indexed.
     *
     * @param document $document
     */
    protected function process_document_files($document) {
        if (!$this->file_indexing_enabled()) {
            return;
        }

        // Maximum rows to process at a time.
        $rows = 500;

        // Get the attached files.
        $files = $document->get_files();

        // If this isn't a new document, we need to check the exiting indexed files.
        if (!$document->get_is_new()) {
            // We do this progressively, so we can handle lots of files cleanly.
            list($numfound, $indexedfiles) = $this->get_indexed_files($document, 0, $rows);
            $count = 0;
            $idstodelete = array();

            do {
                // Go through each indexed file. We want to not index any stored and unchanged ones, delete any missing ones.
                foreach ($indexedfiles as $indexedfile) {
                    $fileid = $indexedfile->solr_fileid;

                    if (isset($files[$fileid])) {
                        // Check for changes that would mean we need to re-index the file. If so, just leave in $files.
                        // Filelib does not guarantee time modified is updated, so we will check important values.
                        if ($indexedfile->modified != $files[$fileid]->get_timemodified()) {
                            continue;
                        }
                        if (strcmp($indexedfile->title, $files[$fileid]->get_filename()) !== 0) {
                            continue;
                        }
                        if ($indexedfile->solr_filecontenthash != $files[$fileid]->get_contenthash()) {
                            continue;
                        }
                        if ($indexedfile->solr_fileindexstatus == document::INDEXED_FILE_FALSE &&
                                $this->file_is_indexable($files[$fileid])) {
                            // This means that the last time we indexed this file, filtering blocked it.
                            // Current settings say it is indexable, so we will allow it to be indexed.
                            continue;
                        }

                        // If the file is already indexed, we can just remove it from the files array and skip it.
                        unset($files[$fileid]);
                    } else {
                        // This means we have found a file that is no longer attached, so we need to delete from the index.
                        // We do it later, since this is progressive, and it could reorder results.
                        $idstodelete[] = $indexedfile->id;
                    }
                }
                $count += $rows;

                if ($count < $numfound) {
                    // If we haven't hit the total count yet, fetch the next batch.
                    list($numfound, $indexedfiles) = $this->get_indexed_files($document, $count, $rows);
                }

            } while ($count < $numfound);

            // Delete files that are no longer attached.
            foreach ($idstodelete as $id) {
                // We directly delete the item using the client, as the engine delete_by_id won't work on file docs.
                $this->get_search_client()->deleteById($id);
            }
        }

        // Now we can actually index all the remaining files.
        foreach ($files as $file) {
            $this->add_stored_file($document, $file);
        }
    }

    /**
     * Get the currently indexed files for a particular document, returns the total count, and a subset of files.
     *
     * @param document $document
     * @param int      $start The row to start the results on. Zero indexed.
     * @param int      $rows The number of rows to fetch
     * @return array   A two element array, the first is the total number of availble results, the second is an array
     *                 of documents for the current request.
     */
    protected function get_indexed_files($document, $start = 0, $rows = 500) {
        // Build a custom query that will get any document files that are in our solr_filegroupingid.
        $query = new \SolrQuery();

        // We want to get all file records tied to a document.
        // For efficiency, we are building our own, stripped down, query.
        $query->setQuery('*');
        $query->setRows($rows);
        $query->setStart($start);
        // We want a consistent sorting.
        $query->addSortField('id');

        // We only want the bare minimum of fields.
        $query->addField('id');
        $query->addField('modified');
        $query->addField('title');
        $query->addField('solr_fileid');
        $query->addField('solr_filecontenthash');
        $query->addField('solr_fileindexstatus');

        $query->addFilterQuery('{!cache=false}solr_filegroupingid:(' . $document->get('id') . ')');
        $query->addFilterQuery('type:' . \core_search\manager::TYPE_FILE);

        $response = $this->get_query_response($query);
        if (empty($response->response->numFound)) {
            return array(0, array());
        }

        return array($response->response->numFound, $this->convert_file_results($response));
    }

    /**
     * A very lightweight handler for getting information about already indexed files from a Solr response.
     *
     * @param SolrObject $responsedoc A Solr response document
     * @return stdClass[] An array of objects that contain the basic information for file processing.
     */
    protected function convert_file_results($responsedoc) {
        if (!$docs = $responsedoc->response->docs) {
            return array();
        }

        $out = array();

        foreach ($docs as $doc) {
            // Copy the bare minimim needed info.
            $result = new \stdClass();
            $result->id = $doc->id;
            $result->modified = document::import_time_from_engine($doc->modified);
            $result->title = $doc->title;
            $result->solr_fileid = $doc->solr_fileid;
            $result->solr_filecontenthash = $doc->solr_filecontenthash;
            $result->solr_fileindexstatus = $doc->solr_fileindexstatus;
            $out[] = $result;
        }

        return $out;
    }

    /**
     * Adds a file to the search engine.
     *
     * Notes about Solr and Tika indexing. We do not send the mime type, only the filename.
     * Tika has much better content type detection than Moodle, and we will have many more doc failures
     * if we try to send mime types.
     *
     * @param document $document
     * @param \stored_file $storedfile
     * @return void
     */
    protected function add_stored_file($document, $storedfile) {
        $filedoc = $document->export_file_for_engine($storedfile);

        if (!$this->file_is_indexable($storedfile)) {
            // For files that we don't consider indexable, we will still place a reference in the search engine.
            $filedoc['solr_fileindexstatus'] = document::INDEXED_FILE_FALSE;
            $this->add_solr_document($filedoc);
            return;
        }

        $curl = $this->get_curl_object();

        $url = $this->get_connection_url('/update/extract');

        // This will prevent solr from automatically making fields for every tika output.
        $url->param('uprefix', 'ignored_');

        // Control how content is captured. This will keep our file content clean of non-important metadata.
        $url->param('captureAttr', 'true');
        // Move the content to a field for indexing.
        $url->param('fmap.content', 'solr_filecontent');

        // These are common fields that matches the standard *_point dynamic field and causes an error.
        $url->param('fmap.media_white_point', 'ignored_mwp');
        $url->param('fmap.media_black_point', 'ignored_mbp');

        // Copy each key to the url with literal.
        // We place in a temp name then copy back to the true field, which prevents errors or Tika overwriting common field names.
        foreach ($filedoc as $key => $value) {
            // This will take any fields from tika that match our schema and discard them, so they don't overwrite ours.
            $url->param('fmap.'.$key, 'ignored_'.$key);
            // Place data in a tmp field.
            $url->param('literal.mdltmp_'.$key, $value);
            // Then move to the final field.
            $url->param('fmap.mdltmp_'.$key, $key);
        }

        // This sets the true filename for Tika.
        $url->param('resource.name', $storedfile->get_filename());

        // A giant block of code that is really just error checking around the curl request.
        try {
            // Now actually do the request.
            $result = $curl->post($url->out(false), array('myfile' => $storedfile));

            $code = $curl->get_errno();
            $info = $curl->get_info();

            // Now error handling. It is just informational, since we aren't tracking per file/doc results.
            if ($code != 0) {
                // This means an internal cURL error occurred error is in result.
                $message = 'Curl error '.$code.' while indexing file with document id '.$filedoc['id'].': '.$result.'.';
                debugging($message, DEBUG_DEVELOPER);
            } else if (isset($info['http_code']) && ($info['http_code'] !== 200)) {
                // Unexpected HTTP response code.
                $message = 'Error while indexing file with document id '.$filedoc['id'];
                // Try to get error message out of msg or title if it exists.
                if (preg_match('|<str [^>]*name="msg"[^>]*>(.*?)</str>|i', $result, $matches)) {
                    $message .= ': '.$matches[1];
                } else if (preg_match('|<title[^>]*>([^>]*)</title>|i', $result, $matches)) {
                    $message .= ': '.$matches[1];
                }
                // This is a common error, happening whenever a file fails to index for any reason, so we will make it quieter.
                if (CLI_SCRIPT && !PHPUNIT_TEST) {
                    mtrace($message);
                }
            } else {
                // Check for the expected status field.
                if (preg_match('|<int [^>]*name="status"[^>]*>(\d*)</int>|i', $result, $matches)) {
                    // Now check for the expected status of 0, if not, error.
                    if ((int)$matches[1] !== 0) {
                        $message = 'Unexpected Solr status code '.(int)$matches[1];
                        $message .= ' while indexing file with document id '.$filedoc['id'].'.';
                        debugging($message, DEBUG_DEVELOPER);
                    } else {
                        // The document was successfully indexed.
                        return;
                    }
                } else {
                    // We received an unprocessable response.
                    $message = 'Unexpected Solr response while indexing file with document id '.$filedoc['id'].': ';
                    $message .= strtok($result, "\n");
                    debugging($message, DEBUG_DEVELOPER);
                }
            }
        } catch (\Exception $e) {
            // There was an error, but we are not tracking per-file success, so we just continue on.
            debugging('Unknown exception while indexing file "'.$storedfile->get_filename().'".', DEBUG_DEVELOPER);
        }

        // If we get here, the document was not indexed due to an error. So we will index just the base info without the file.
        $filedoc['solr_fileindexstatus'] = document::INDEXED_FILE_ERROR;
        $this->add_solr_document($filedoc);
    }

    /**
     * Checks to see if a passed file is indexable.
     *
     * @param \stored_file $file The file to check
     * @return bool True if the file can be indexed
     */
    protected function file_is_indexable($file) {
        if (!empty($this->config->maxindexfilekb) && ($file->get_filesize() > ($this->config->maxindexfilekb * 1024))) {
            // The file is too big to index.
            return false;
        }

        $mime = $file->get_mimetype();

        if ($mime == 'application/vnd.moodle.backup') {
            // We don't index Moodle backup files. There is nothing usefully indexable in them.
            return false;
        }

        return true;
    }

    /**
     * Commits all pending changes.
     *
     * @return void
     */
    protected function commit() {
        $this->get_search_client()->commit();
    }

    /**
     * Do any area cleanup needed, and do anything to confirm contents.
     *
     * Return false to prevent the search area completed time and stats from being updated.
     *
     * @param \core_search\base $searcharea The search area that was complete
     * @param int $numdocs The number of documents that were added to the index
     * @param bool $fullindex True if a full index is being performed
     * @return bool True means that data is considered indexed
     */
    public function area_index_complete($searcharea, $numdocs = 0, $fullindex = false) {
        $this->commit();

        return true;
    }

    /**
     * Return true if file indexing is supported and enabled. False otherwise.
     *
     * @return bool
     */
    public function file_indexing_enabled() {
        return (bool)$this->config->fileindexing;
    }

    /**
     * Defragments the index.
     *
     * @return void
     */
    public function optimize() {
        $this->get_search_client()->optimize(1, true, false);
    }

    /**
     * Deletes the specified document.
     *
     * @param string $id The document id to delete
     * @return void
     */
    public function delete_by_id($id) {
        // We need to make sure we delete the item and all related files, which can be done with solr_filegroupingid.
        $this->get_search_client()->deleteByQuery('solr_filegroupingid:' . $id);
        $this->commit();
    }

    /**
     * Delete all area's documents.
     *
     * @param string $areaid
     * @return void
     */
    public function delete($areaid = null) {
        if ($areaid) {
            $this->get_search_client()->deleteByQuery('areaid:' . $areaid);
        } else {
            $this->get_search_client()->deleteByQuery('*:*');
        }
        $this->commit();
    }

    /**
     * Pings the Solr server using search_solr config
     *
     * @return true|string Returns true if all good or an error string.
     */
    public function is_server_ready() {

        $configured = $this->is_server_configured();
        if ($configured !== true) {
            return $configured;
        }

        // Check that the schema is already set up.
        try {
            $schema = new \search_solr\schema();
            $schema->validate_setup();
        } catch (\moodle_exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    /**
     * Is the solr server properly configured?.
     *
     * @return true|string Returns true if all good or an error string.
     */
    public function is_server_configured() {

        if (empty($this->config->server_hostname) || empty($this->config->indexname)) {
            return 'No solr configuration found';
        }

        if (!$client = $this->get_search_client(false)) {
            return get_string('engineserverstatus', 'search');
        }

        try {
            if ($this->get_solr_major_version() < 4) {
                // Minimum solr 4.0.
                return get_string('minimumsolr4', 'search_solr');
            }
        } catch (\SolrClientException $ex) {
            debugging('Solr client error: ' . html_to_text($ex->getMessage()), DEBUG_DEVELOPER);
            return get_string('engineserverstatus', 'search');
        } catch (\SolrServerException $ex) {
            debugging('Solr server error: ' . html_to_text($ex->getMessage()), DEBUG_DEVELOPER);
            return get_string('engineserverstatus', 'search');
        }

        return true;
    }

    /**
     * Returns the solr server major version.
     *
     * @return int
     */
    public function get_solr_major_version() {
        // We should really ping first the server to see if the specified indexname is valid but
        // we want to minimise solr server requests as they are expensive. system() emits a warning
        // if it can not connect to the configured index in the configured server.
        $systemdata = @$this->get_search_client()->system();
        $solrversion = $systemdata->getResponse()->offsetGet('lucene')->offsetGet('solr-spec-version');
        return intval(substr($solrversion, 0, strpos($solrversion, '.')));
    }

    /**
     * Checks if the PHP Solr extension is available.
     *
     * @return bool
     */
    public function is_installed() {
        return function_exists('solr_get_version');
    }

    /**
     * Returns the solr client instance.
     *
     * We don't reuse SolrClient if we are on libcurl 7.35.0, due to a bug in that version of curl.
     *
     * @throws \core_search\engine_exception
     * @param bool $triggerexception
     * @return \SolrClient
     */
    protected function get_search_client($triggerexception = true) {
        global $CFG;

        // Type comparison as it is set to false if not available.
        if ($this->client !== null) {
            return $this->client;
        }

        $options = array(
            'hostname' => $this->config->server_hostname,
            'path'     => '/solr/' . $this->config->indexname,
            'login'    => !empty($this->config->server_username) ? $this->config->server_username : '',
            'password' => !empty($this->config->server_password) ? $this->config->server_password : '',
            'port'     => !empty($this->config->server_port) ? $this->config->server_port : '',
            'secure' => !empty($this->config->secure) ? true : false,
            'ssl_cert' => !empty($this->config->ssl_cert) ? $this->config->ssl_cert : '',
            'ssl_key' => !empty($this->config->ssl_key) ? $this->config->ssl_key : '',
            'ssl_keypassword' => !empty($this->config->ssl_keypassword) ? $this->config->ssl_keypassword : '',
            'ssl_cainfo' => !empty($this->config->ssl_cainfo) ? $this->config->ssl_cainfo : '',
            'ssl_capath' => !empty($this->config->ssl_capath) ? $this->config->ssl_capath : '',
            'timeout' => !empty($this->config->server_timeout) ? $this->config->server_timeout : '30'
        );

        if ($CFG->proxyhost && !is_proxybypass('http://' . $this->config->server_hostname . '/')) {
            $options['proxy_host'] = $CFG->proxyhost;
            $options['proxy_port'] = $CFG->proxyport;
        }

        if (!class_exists('\SolrClient')) {
            throw new \core_search\engine_exception('enginenotinstalled', 'search', '', 'solr');
        }

        $client = new \SolrClient($options);

        if ($client === false && $triggerexception) {
            throw new \core_search\engine_exception('engineserverstatus', 'search');
        }

        if ($this->cacheclient) {
            $this->client = $client;
        }

        return $client;
    }

    /**
     * Returns a curl object for conntecting to solr.
     *
     * @return \curl
     */
    public function get_curl_object() {
        if (!is_null($this->curl)) {
            return $this->curl;
        }

        // Connection to Solr is allowed to use 'localhost' and other potentially blocked hosts/ports.
        $this->curl = new \curl(['ignoresecurity' => true]);

        $options = array();
        // Build the SSL options. Based on pecl-solr and general testing.
        if (!empty($this->config->secure)) {
            if (!empty($this->config->ssl_cert)) {
                $options['CURLOPT_SSLCERT'] = $this->config->ssl_cert;
                $options['CURLOPT_SSLCERTTYPE'] = 'PEM';
            }

            if (!empty($this->config->ssl_key)) {
                $options['CURLOPT_SSLKEY'] = $this->config->ssl_key;
                $options['CURLOPT_SSLKEYTYPE'] = 'PEM';
            }

            if (!empty($this->config->ssl_keypassword)) {
                $options['CURLOPT_KEYPASSWD'] = $this->config->ssl_keypassword;
            }

            if (!empty($this->config->ssl_cainfo)) {
                $options['CURLOPT_CAINFO'] = $this->config->ssl_cainfo;
            }

            if (!empty($this->config->ssl_capath)) {
                $options['CURLOPT_CAPATH'] = $this->config->ssl_capath;
            }
        }

        // Set timeout as for Solr client.
        $options['CURLOPT_TIMEOUT'] = !empty($this->config->server_timeout) ? $this->config->server_timeout : '30';

        $this->curl->setopt($options);

        if (!empty($this->config->server_username) && !empty($this->config->server_password)) {
            $authorization = $this->config->server_username . ':' . $this->config->server_password;
            $this->curl->setHeader('Authorization: Basic ' . base64_encode($authorization));
        }

        return $this->curl;
    }

    /**
     * Return a Moodle url object for the server connection.
     *
     * @param string $path The solr path to append.
     * @return \moodle_url
     */
    public function get_connection_url($path) {
        // Must use the proper protocol, or SSL will fail.
        $protocol = !empty($this->config->secure) ? 'https' : 'http';
        $url = $protocol . '://' . rtrim($this->config->server_hostname, '/');
        if (!empty($this->config->server_port)) {
            $url .= ':' . $this->config->server_port;
        }
        $url .= '/solr/' . $this->config->indexname . '/' . ltrim($path, '/');

        return new \moodle_url($url);
    }
}
