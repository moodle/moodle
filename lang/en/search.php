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
 * Strings for component 'search', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   core
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['advancedsearch'] = 'Advanced search';
$string['all'] = 'All';
$string['allareas'] = 'All areas';
$string['allcourses'] = 'All courses';
$string['allusers'] = 'All users';
$string['author'] = 'Author';
$string['authorname'] = 'Author name';
$string['back'] = 'Back';
$string['beadmin'] = 'You need to be an admin user to use this page.';
$string['commenton'] = 'Comment on';
$string['confirm_delete'] = 'Are you sure you want to delete the index for {$a}? Until the search area is indexed, users will not get search results from this area.';
$string['confirm_indexall'] = 'Are you sure you want to update indexed contents now? If a large amount of content needs indexing, this can take a long time. For live servers, you should normally leave indexing to the \'Global search indexing\' scheduled task.';
$string['confirm_reindexall'] = 'Are you sure you want to reindex all site contents now? If your site contains a large amount of content, this will take a long time, and users may not get full search results until it completes.';
$string['confirm_deleteall'] = 'Are you sure you want to delete all indexed contents now? Until the site is indexed again, users will not get search results.';
$string['core-all'] = 'All';
$string['core-course-content'] = 'Course content';
$string['core-courses'] = 'Courses';
$string['core-users'] = 'Users';
$string['core-other'] = 'Other';
$string['createanindex'] = 'create an index';
$string['createdon'] = 'Created on';
$string['database'] = 'Database';
$string['databasestate'] = 'Indexing database state';
$string['datadirectory'] = 'Data directory';
$string['deleteindex'] = 'Delete index {$a}';
$string['deletionsinindex'] = 'Deletions in index';
$string['docmodifiedon'] = 'Last modified on {$a}';
$string['doctype'] = 'Doctype';
$string['doctypenotsupported'] = 'The specified doc type is not yet supported';
$string['documents'] = 'documents';
$string['documentsfor'] = 'Documents for';
$string['documentsindatabase'] = 'Documents in database';
$string['documentsinindex'] = 'Documents in index';
$string['duration'] = 'Duration';
$string['emptydatabaseerror'] = 'Database table is not present, or contains no index records.';
$string['enginenotfound'] = 'Engine {$a} not found.';
$string['enginenotinstalled'] = 'Engine {$a} is not installed.';
$string['enginenotselected'] = 'You have not selected any search engine.';
$string['engineserverstatus'] = 'The search engine is not available. Please contact your administrator.';
$string['enteryoursearchquery'] = 'Enter your search query';
$string['error_indexing'] = 'An error occurred while indexing';
$string['errors'] = 'Errors';
$string['errorareanotavailable'] = '{$a} search area is not available.';
$string['everywhere'] = 'Everywhere you can access';
$string['filesinindexdirectory'] = 'Files in index directory';
$string['filterheader'] = 'Filter';
$string['fromtime'] = 'Modified after';
$string['globalsearch'] = 'Global search';
$string['globalsearchdisabled'] = 'Global searching is not enabled.';
$string['gradualreindex'] = 'Gradual reindex {$a}';
$string['gradualreindex_confirm'] = 'Are you sure you want to reindex {$a}? This may take some time, although existing data will remain available during the reindex.';
$string['gradualreindex_queued'] = 'Reindexing has been requested for {$a->name} ({$a->count} contexts). This indexing will be carried out by the "Global search indexing" scheduled task.';
$string['checkdb'] = 'Check database';
$string['checkdbadvice'] = 'Check your database for any problems.';
$string['checkdir'] = 'Check dir';
$string['checkdiradvice'] = 'Ensure the data directory exists and is writable.';
$string['incourse'] = 'in course {$a}';
$string['index'] = 'Index';
$string['indexwhendisabledfullnotice'] = 'Indexing is currently not permitted when search is disabled. To enable this, please see the <a href="{$a->url}">searchindexwhendisabled</a> setting.';
$string['indexwhendisabledshortnotice'] = 'Indexing is not available.';
$string['invalidindexerror'] = 'Index directory either contains an invalid index, or nothing at all.';
$string['ittook'] = 'It took';
$string['matchingfile'] = 'Matched from file <span class="filename">{$a}</span>';
$string['matchingfiles'] = 'Matched from files:';
$string['mycoursesonly'] = 'My courses only';
$string['next'] = 'Next';
$string['noindexmessage'] = 'Admin: There appears to be no search index. Please';
$string['noresults'] = 'No results';
$string['notitle'] = 'No title';
$string['normalsearch'] = 'Normal search';
$string['openedon'] = 'opened on';
$string['optimize'] = 'Optimize';
$string['order'] = 'Results order';
$string['order_location'] = 'Prioritise results related to {$a}';
$string['order_relevance'] = 'Most relevant results first';
$string['priority'] = 'Priority';
$string['priority_reindexing'] = 'Reindexing';
$string['priority_normal'] = 'Normal';
$string['privacy:metadata'] = 'The search subsystem does not store any personal data.';
$string['progress'] = 'Progress';
$string['queryerror'] = 'The query you provided could not be parsed by the search engine: {$a}';
$string['queueheading'] = 'Additional indexing queue ({$a} items)';
$string['resultsreturnedfor'] = 'results returned for';
$string['runindexer'] = 'Run indexer (real)';
$string['runindexertest'] = 'Run indexer test';
$string['schemanotupdated'] = 'The search schema is out of date.';
$string['schemaversionunknown'] = 'Search engine does not know about the current schema version.';
$string['score'] = 'Score';
$string['search'] = 'Search';
$string['search:message_received'] = 'Messages - received';
$string['search:message_sent'] = 'Messages - sent';
$string['search:mycourse'] = 'My courses';
$string['search:course'] = 'Courses';
$string['search:section'] = 'Course sections';
$string['search:user'] = 'Users';
$string['searcharea'] = 'Search area';
$string['searchareacategories'] = 'Search area categories';
$string['searching'] = 'Searching in ...';
$string['searchnotpermitted'] = 'You are not allowed to do a search';
$string['searchsetupdescription'] = 'The following steps help you to set up Moodle global search.';
$string['searchwithin'] = 'Search within';
$string['seconds'] = 'seconds';
$string['solutions'] = 'Solutions';
$string['statistics'] = 'Statistics';
$string['step'] = 'Step';
$string['thesewordshelpimproverank'] = 'These words help improve rank';
$string['thesewordsmustappear'] = 'These words must appear';
$string['thesewordsmustnotappear'] = 'These words must not appear';
$string['title'] = 'Title';
$string['tofetchtheseresults'] = 'to fetch these results';
$string['totalsize'] = 'Total size';
$string['totime'] = 'Modified before';
$string['type'] = 'Type';
$string['uncompleteindexingerror'] = 'Indexing was not successfully completed, please restart it.';
$string['versiontoolow'] = 'Sorry, global search requires PHP 5.0.0 or later';
$string['viewresultincontext'] = 'View this result in context';
$string['whichmodulestosearch?'] = 'Which modules to search?';
$string['wordsintitle'] = 'Words in title';
$string['search:customfield'] = 'Course custom fields';
