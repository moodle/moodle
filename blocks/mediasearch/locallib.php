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
 * Version details
 *
 * @package    block_mediasearch
 * @copyright  2015 E-Learn Design http://www.e-learndesign.co.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class mediasearch {
    // Class to hold the local libraries.
    
    /**
     *
     *
     */
    public static function get_media_entries($search = '', $sort = 'coursefullname', $dir = 'ASC', $page = 0, $perpage = 20) {
        global $DB, $CFG;

        if (!empty($search)) {
            $thissearch = "WHERE msd.title LIKE '%:search%'";
        } else {
            $thissearch = "";
        }
        $required = "msd.id, c.shortname AS coursefullname, msd.title, msd.description, msd.descriptionformat, msd.courseid, msd.keywords, msd.link";
        // Get the entries.
        $entries = $DB->get_records_sql("SELECT $required
                                         FROM {block_mediasearch_data} msd
                                         JOIN {course} c ON (msd.courseid = c.id)
                                         $thissearch
                                         ORDER BY $sort $dir",
                                         array('search' => $search),
                                         $page * $perpage, $perpage);

        $countentries = $DB->get_records_sql("SELECT id
                                         FROM {block_mediasearch_data} msd
                                         $thissearch",
                                         array('search' => $search));

        $count = count($countentries);
        
        $return = new stdclass();
        $return->totalcount = $count;
        $return->entries = $entries; 

        return $return;
    }

    /**
     *
     *
     */
    public static function search_entries($searchterms, $page, $perpage) {
        global $CFG, $DB, $USER, $COURSE;

        if (!has_capability('block/mediasearch:manageentries', context_system::instance())) {
            $mycourses = enrol_get_my_courses();
        } else {
            $mycourses = $DB->get_records('course', array(), null, 'id');
        }

        if (empty($mycourses) || (count($searchterms) == 1 && empty($searchterms[0]))) {
            $return = new stdclass();
            $return->records = array();
            $return->totalcount = 0;
            return $return;
        }

        $searchstring = '';

        // Need to concat these back together for parser to work.
        foreach($searchterms as $searchterm){
            if ($searchstring != '') {
                $searchstring .= ' ';
            }
            $searchstring .= $searchterm;
        }
    
        // We need to allow quoted strings for the search. The quotes *should* be stripped
        // by the parser, but this should be examined carefully for security implications.
        $searchstring = str_replace("\\\"","\"",$searchstring);
        $parser = new search_parser();
        $lexer = new search_lexer($parser);
    
        if ($lexer->parse($searchstring)) {
            $parsearray = $parser->get_parsed_array();        
        }
        list($titlesearch, $titleparams) = search_generate_text_SQL($parsearray, 'p.title', 'p.description',
                                                                   'p.keywords',
                                                                   'c.fullname',
                                                                   null,
                                                                   null,
                                                                   null,
                                                                   null);
        list($keysearch, $keyparams) = search_generate_text_SQL($parsearray, 'p.keywords', 'c.fullname',
                                                                   null,
                                                                   null,
                                                                   null,
                                                                   null,
                                                                   null,
                                                                   null);

        $fromsql = "{block_mediasearch_data} p JOIN {course} c ON (p.courseid = c.id) ";

        $selectsql = " $titlesearch OR $keysearch 
                      AND p.courseid IN (" . implode(',', array_keys($mycourses)).") ";

        $params = array('courseid' => $COURSE->id) + $titleparams + $keyparams;
        $countsql = "SELECT p.id
                     FROM $fromsql
                     WHERE $selectsql
                     GROUP BY p.id, p.courseid, c.fullname, p.title
                     ORDER BY CASE WHEN p.courseid = :courseid THEN 1 ELSE 2 END, c.fullname, p.title";

        $searchsql = "SELECT p.*, c.fullname
                     FROM $fromsql
                     WHERE $selectsql
                     GROUP BY p.id, p.courseid, c.fullname, p.title
                     ORDER BY CASE WHEN p.courseid = :courseid THEN 1 ELSE 2 END, c.fullname, p.title";

        $totalcount = count($DB->get_records_sql($countsql, $params));
        $records = $DB->get_records_sql($searchsql, $params, $page * $perpage, $perpage);

        $return = new stdclass();

        if ($totalcount > 0) {
            $return->entries = $records;
            $return->totalcount = $totalcount;
        } else {
            $return->entries = array();
            $return->totalcount = 0;
        }
        return $return;
    }        
}