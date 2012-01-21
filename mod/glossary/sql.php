<?php

/**
 * SQL.PHP
 *    This file is include from view.php and print.php
 * @copyright 2003
 **/

/// Creating the SQL statements

/// Initialise some variables
    $sqlorderby = '';
    $sqlsortkey = NULL;

    // For cases needing inner view
    $sqlwrapheader = '';
    $sqlwrapfooter = '';

/// Calculate the SQL sortkey to be used by the SQL statements later
    switch ( $sortkey ) {
        case "CREATION":
            $sqlsortkey = "timecreated";
            break;
        case "UPDATE":
            $sqlsortkey = "timemodified";
            break;
        case "FIRSTNAME":
            $sqlsortkey = "firstname";
            break;
        case "LASTNAME":
            $sqlsortkey = "lastname";
            break;
    }
    $sqlsortorder = $sortorder;

/// Pivot is the field that set the break by groups (category, initial, author name, etc)

/// fullpivot indicate if the whole pivot should be compared agasint the db or just the first letter
/// printpivot indicate if the pivot should be printed or not

    $fullpivot = 1;
    $params = array('gid1'=>$glossary->id, 'gid2'=>$glossary->id, 'myid'=>$USER->id, 'hook'=>$hook);

    $userid = '';
    if ( isloggedin() ) {
        $userid = "OR ge.userid = :myid";
    }
    switch ($tab) {
    case GLOSSARY_CATEGORY_VIEW:
        if ($hook == GLOSSARY_SHOW_ALL_CATEGORIES  ) {

            $sqlselect = "SELECT gec.id AS cid, ge.*, gec.entryid, gc.name AS glossarypivot";
            $sqlfrom   = "FROM {glossary_entries} ge,
                               {glossary_entries_categories} gec,
                               {glossary_categories} gc";
            $sqlwhere  = "WHERE (ge.glossaryid = :gid1 OR ge.sourceglossaryid = :gid2) AND
                          ge.id = gec.entryid AND gc.id = gec.categoryid AND
                          (ge.approved <> 0 $userid)";

            $sqlorderby = ' ORDER BY gc.name, ge.concept';

        } elseif ($hook == GLOSSARY_SHOW_NOT_CATEGORISED ) {

            $printpivot = 0;
            $sqlselect = "SELECT ge.*, concept AS glossarypivot";
            $sqlfrom   = "FROM {glossary_entries} ge LEFT JOIN {glossary_entries_categories} gec
                               ON ge.id = gec.entryid";
            $sqlwhere  = "WHERE (glossaryid = :gid1 OR sourceglossaryid = :gid2) AND
                          (ge.approved <> 0 $userid) AND gec.entryid IS NULL";


            $sqlorderby = ' ORDER BY concept';

        } else {

            $printpivot = 0;
            $sqlselect  = "SELECT ge.*, ce.entryid, c.name AS glossarypivot";
            $sqlfrom    = "FROM {glossary_entries} ge, {glossary_entries_categories} ce, {glossary_categories} c";
            $sqlwhere   = "WHERE ge.id = ce.entryid AND ce.categoryid = :hook AND
                                 ce.categoryid = c.id AND ge.approved != 0 AND
                                 (ge.glossaryid = :gid1 OR ge.sourceglossaryid = :gid2) AND
                          (ge.approved <> 0 $userid)";

            $sqlorderby = ' ORDER BY c.name, ge.concept';

        }
    break;
    case GLOSSARY_AUTHOR_VIEW:

        $where = '';
        $params['hookup'] = textlib::strtoupper($hook);

        if ( $sqlsortkey == 'firstname' ) {
            $usernamefield = $DB->sql_fullname('u.firstname' , 'u.lastname');
        } else {
            $usernamefield = $DB->sql_fullname('u.lastname' , 'u.firstname');
        }
        $where = "AND " . $DB->sql_substr("upper($usernamefield)", 1, textlib::strlen($hook)) . " = :hookup";

        if ( $hook == 'ALL' ) {
            $where = '';
        }

        $sqlselect  = "SELECT ge.*, $usernamefield AS glossarypivot, 1 AS userispivot ";
        $sqlfrom    = "FROM {glossary_entries} ge, {user} u";
        $sqlwhere   = "WHERE ge.userid = u.id  AND
                             (ge.approved <> 0 $userid)
                             $where AND
                             (ge.glossaryid = :gid1 OR ge.sourceglossaryid = :gid2)";
        $sqlorderby = "ORDER BY $usernamefield $sqlsortorder, ge.concept";
    break;
    case GLOSSARY_APPROVAL_VIEW:
        $fullpivot = 0;
        $printpivot = 0;

        $where = '';
        $params['hookup'] = textlib::strtoupper($hook);

        if ($hook != 'ALL' and $hook != 'SPECIAL') {
            $where = "AND " . $DB->sql_substr("upper(concept)", 1, textlib::strlen($hook)) . " = :hookup";
        }

        $sqlselect  = "SELECT ge.*, ge.concept AS glossarypivot";
        $sqlfrom    = "FROM {glossary_entries} ge";
        $sqlwhere   = "WHERE (ge.glossaryid = :gid1 OR ge.sourceglossaryid = :gid2) AND
                             ge.approved = 0 $where";

        if ( $sqlsortkey ) {
            $sqlorderby = "ORDER BY $sqlsortkey $sqlsortorder";
        } else {
            $sqlorderby = "ORDER BY ge.concept";
        }
    break;
    case GLOSSARY_DATE_VIEW:
        $printpivot = 0;
    case GLOSSARY_STANDARD_VIEW:
    default:
        $sqlselect  = "SELECT ge.*, ge.concept AS glossarypivot";
        $sqlfrom    = "FROM {glossary_entries} ge";

        $where = '';
        $fullpivot = 0;

        switch ( $mode ) {
        case 'search':

            if ($DB->sql_regex_supported()) {
                $REGEXP    = $DB->sql_regex(true);
                $NOTREGEXP = $DB->sql_regex(false);
            }

            $searchcond = array();
            $alcond     = array();
            //$params     = array();
            $i = 0;

            $concat = $DB->sql_concat('ge.concept', "' '", 'ge.definition',"' '", "COALESCE(al.alias, '')");

            $searchterms = explode(" ",$hook);

            foreach ($searchterms as $searchterm) {
                $i++;

                $NOT = false; /// Initially we aren't going to perform NOT LIKE searches, only MSSQL and Oracle
                           /// will use it to simulate the "-" operator with LIKE clause

            /// Under Oracle and MSSQL, trim the + and - operators and perform
            /// simpler LIKE (or NOT LIKE) queries
                if (!$DB->sql_regex_supported()) {
                    if (substr($searchterm, 0, 1) == '-') {
                        $NOT = true;
                    }
                    $searchterm = trim($searchterm, '+-');
                }

                if (substr($searchterm,0,1) == '+') {
                    $searchterm = trim($searchterm, '+-');
                    if (textlib::strlen($searchterm) < 2) {
                        continue;
                    }
                    $searchterm = preg_quote($searchterm, '|');
                    $searchcond[] = "$concat $REGEXP :ss$i";
                    $params['ss'.$i] = "(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)";

                } else if (substr($searchterm,0,1) == "-") {
                    $searchterm = trim($searchterm, '+-');
                    if (textlib::strlen($searchterm) < 2) {
                        continue;
                    }
                    $searchterm = preg_quote($searchterm, '|');
                    $searchcond[] = "$concat $NOTREGEXP :ss$i";
                    $params['ss'.$i] = "(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)";

                } else {
                    if (textlib::strlen($searchterm) < 2) {
                        continue;
                    }
                    $searchcond[] = $DB->sql_like($concat, ":ss$i", false, true, $NOT);
                    $params['ss'.$i] = "%$searchterm%";
                }
            }

            if (empty($searchcond)) {
                $where = "AND 1=2 "; // no search result

            } else {
                $searchcond = implode(" AND ", $searchcond);

                // Need one inner view here to avoid distinct + text
                $sqlwrapheader = 'SELECT ge.*, ge.concept AS glossarypivot
                                    FROM {glossary_entries} ge
                                    JOIN ( ';
                $sqlwrapfooter = ' ) gei ON (ge.id = gei.id)';

                $sqlselect  = "SELECT DISTINCT ge.id";
                $sqlfrom    = "FROM {glossary_entries} ge
                               LEFT JOIN {glossary_alias} al ON al.entryid = ge.id";
                $where      = "AND ($searchcond)";
            }

        break;

        case 'term':
            $params['hook2'] = $hook;
            $printpivot = 0;
            $sqlfrom .= " LEFT JOIN {glossary_alias} ga on ge.id = ga.entryid";
            $where = "AND (ge.concept = :hook OR ga.alias = :hook2) ";
        break;

        case 'entry':
            $printpivot = 0;
            $where = "AND ge.id = :hook";
        break;

        case 'letter':
            if ($hook != 'ALL' and $hook != 'SPECIAL') {
                $params['hookup'] = textlib::strtoupper($hook);
                $where = "AND " . $DB->sql_substr("upper(concept)", 1, textlib::strlen($hook)) . " = :hookup";
            }
            if ($hook == 'SPECIAL') {
                //Create appropiate IN contents
                $alphabet = explode(",", get_string('alphabet', 'langconfig'));
                list($nia, $aparams) = $DB->get_in_or_equal($alphabet, SQL_PARAMS_NAMED, $start='a', false);
                $params = array_merge($params, $aparams);
                $where = "AND " . $DB->sql_substr("upper(concept)", 1, 1) . " $nia";
            }
        break;
        }

        $sqlwhere   = "WHERE (ge.glossaryid = :gid1 or ge.sourceglossaryid = :gid2) AND
                             (ge.approved <> 0 $userid)
                              $where";
        switch ( $tab ) {
        case GLOSSARY_DATE_VIEW:
            $sqlorderby = "ORDER BY $sqlsortkey $sqlsortorder";
        break;

        case GLOSSARY_STANDARD_VIEW:
            $sqlorderby = "ORDER BY ge.concept";
        default:
        break;
        }
    break;
    }
    $count = $DB->count_records_sql("SELECT COUNT(DISTINCT(ge.id)) $sqlfrom $sqlwhere", $params);

    $limitfrom = $offset;
    $limitnum = 0;

    if ( $offset >= 0 ) {
        $limitnum = $entriesbypage;
    }

    $query = "$sqlwrapheader $sqlselect $sqlfrom $sqlwhere $sqlwrapfooter $sqlorderby";
    $allentries = $DB->get_records_sql($query, $params, $limitfrom, $limitnum);

