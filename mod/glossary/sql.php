<?php

/**
 * SQL.PHP
 *    This file is include from view.php and print.php
 * @version $Id$
 * @copyright 2003 
 **/

/// Creating the SQL statements

/// Initialise some variables
    $sqlorderby = '';
    $sqlsortkey = NULL;
    $textlib = textlib_get_instance();

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

    $userid = '';
    if ( !empty($USER->id) ) {
        $userid = "OR ge.userid = $USER->id";
    }
    switch ($tab) {
    case GLOSSARY_CATEGORY_VIEW:
        if ($hook == GLOSSARY_SHOW_ALL_CATEGORIES  ) { 

            $sqlselect = "SELECT gec.id AS cid, ge.*, gec.entryid, gc.name AS glossarypivot";
            $sqlfrom   = "FROM {$CFG->prefix}glossary_entries ge,
                         {$CFG->prefix}glossary_entries_categories gec,
                         {$CFG->prefix}glossary_categories gc";
            $sqlwhere  = "WHERE (ge.glossaryid = '$glossary->id' OR ge.sourceglossaryid = '$glossary->id') AND
                          ge.id = gec.entryid AND gc.id = gec.categoryid AND
                          (ge.approved != 0 $userid)";

            $sqlorderby = ' ORDER BY gc.name, ge.concept';

        } elseif ($hook == GLOSSARY_SHOW_NOT_CATEGORISED ) { 

            $printpivot = 0;
            $sqlselect = "SELECT ge.*, concept AS glossarypivot";
            $sqlfrom   = "FROM {$CFG->prefix}glossary_entries ge LEFT JOIN {$CFG->prefix}glossary_entries_categories gec
                          ON ge.id = gec.entryid";
            $sqlwhere  = "WHERE (glossaryid = '$glossary->id' OR sourceglossaryid = '$glossary->id') AND
                          (ge.approved != 0 $userid) AND gec.entryid IS NULL";


            $sqlorderby = ' ORDER BY concept';

        } else {

            $printpivot = 0;
            $sqlselect  = "SELECT ge.*, ce.entryid, c.name AS glossarypivot";
            $sqlfrom    = "FROM {$CFG->prefix}glossary_entries ge, {$CFG->prefix}glossary_entries_categories ce, {$CFG->prefix}glossary_categories c";
            $sqlwhere   = "WHERE ge.id = ce.entryid AND ce.categoryid = '$hook' AND
                                 ce.categoryid = c.id AND ge.approved != 0 AND
                                 (ge.glossaryid = '$glossary->id' OR ge.sourceglossaryid = '$glossary->id') AND
                          (ge.approved != 0 $userid)";

            $sqlorderby = ' ORDER BY c.name, ge.concept';

        }
    break;
    case GLOSSARY_AUTHOR_VIEW:

        $where = '';

        if ( $sqlsortkey == 'firstname' ) {
            $usernamefield = sql_fullname('u.firstname' , 'u.lastname');
        } else {
            $usernamefield = sql_fullname('u.lastname' , 'u.firstname');
        }
        $where = "AND " . sql_substr() . "(upper($usernamefield),1," .  $textlib->strlen($hook) . ") = '" . $textlib->strtoupper($hook) . "'";

        if ( $hook == 'ALL' ) {
            $where = '';
        }

        $sqlselect  = "SELECT ge.*, $usernamefield AS glossarypivot, 1 AS userispivot ";
        $sqlfrom    = "FROM {$CFG->prefix}glossary_entries ge, {$CFG->prefix}user u";
        $sqlwhere   = "WHERE ge.userid = u.id  AND
                             (ge.approved != 0 $userid)
                             $where AND 
                             (ge.glossaryid = '$glossary->id' OR ge.sourceglossaryid = '$glossary->id')";
        $sqlorderby = "ORDER BY $usernamefield $sqlsortorder, ge.concept";
    break;
    case GLOSSARY_APPROVAL_VIEW:
        $fullpivot = 0;
        $printpivot = 0;

        $where = '';
        if ($hook != 'ALL' and $hook != 'SPECIAL') {
            $where = 'AND ' . sql_substr() . '(upper(concept),1,' .  $textlib->strlen($hook) . ') = \'' . $textlib->strtoupper($hook) . '\'';
        }

        $sqlselect  = "SELECT ge.*, ge.concept AS glossarypivot";
        $sqlfrom    = "FROM {$CFG->prefix}glossary_entries ge";
        $sqlwhere   = "WHERE (ge.glossaryid = '$glossary->id' OR ge.sourceglossaryid = '$glossary->id') AND
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
        $sqlfrom    = "FROM {$CFG->prefix}glossary_entries ge";

        $where = '';
        $fullpivot = 0;
        $LIKE = sql_ilike();
        $NOTLIKE = 'NOT ' . $LIKE;

        switch ( $mode ) {
        case 'search': 

            /// Some differences in syntax for PostgreSQL
            if ($CFG->dbfamily == "postgres") {
                $REGEXP = "~*";
                $NOTREGEXP = "!~*";
            } else {
                $REGEXP = "REGEXP";
                $NOTREGEXP = "NOT REGEXP";
            }

            $conceptsearch = "";
            $aliassearch = "";
            $definitionsearch = "";

            $searchterms = explode(" ",$hook);

            foreach ($searchterms as $searchterm) {

                if ($conceptsearch) {
                    $conceptsearch .= " AND ";
                }
                if ($aliassearch) {
                    $aliassearch .= " AND ";
                }
                if ($definitionsearch) {
                    $definitionsearch .= " AND ";
                }

            /// Under Oracle and MSSQL, trim the + and - operators and perform
            /// simpler LIKE search
                if ($CFG->dbfamily == 'oracle' || $CFG->dbfamily == 'mssql') {
                    $searchterm = trim($searchterm, '+-');
                }

                if (substr($searchterm,0,1) == "+") {
                    $searchterm = substr($searchterm,1);
                    $conceptsearch .= " ge.concept $REGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
                    $aliassearch .= " al.alias $REGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
                    $definitionsearch .= " ge.definition $REGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
                } else if (substr($searchterm,0,1) == "-") {
                    $searchterm = substr($searchterm,1);
                    $conceptsearch .= " ge.concept $NOTREGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
                    $aliassearch .= " al.alias $NOTREGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
                    $definitionsearch .= " ge.definition $NOTREGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
                } else {
                    $conceptsearch .= " ge.concept $LIKE '%$searchterm%' ";
                    $aliassearch .= " al.alias $LIKE '%$searchterm%' ";
                    $definitionsearch .= " ge.definition $LIKE '%$searchterm%' ";
                }
            }
     
            //Search in aliases first
            $idaliases = '';
            $listaliases = array();
            $recaliases = get_records_sql ("SELECT al.id, al.entryid
                                              FROM {$CFG->prefix}glossary_alias al,
                                                   {$CFG->prefix}glossary_entries ge
                                              WHERE (ge.glossaryid = '$glossary->id' OR
                                                     ge.sourceglossaryid = '$glossary->id') AND
                                                    (ge.approved != 0 $userid) AND
                                                    ge.id = al.entryid AND
                                                    $aliassearch");
            //Process aliases id
            if ($recaliases) {
                foreach ($recaliases as $recalias) {
                    $listaliases[] = $recalias->entryid;
                }
                $idaliases = implode (',',$listaliases);
            }
           
            //Add seach conditions in concepts and, if needed, in definitions
            $printpivot = 0;
            $where = "AND (( $conceptsearch) ";

            //Include aliases id if found
            if (!empty($idaliases)) {
                $where .= " OR ge.id IN ($idaliases) ";
            }

            //Include search in definitions if requested
            if ( $fullsearch ) {
                $where .= " OR ($definitionsearch) )";
            } else {
                $where .= ")";
            }

        break;
        
        case 'term': 
            $printpivot = 0;
            $sqlfrom .= " left join {$CFG->prefix}glossary_alias ga on ge.id = ga.entryid ";
            $where = "AND (ge.concept = '$hook' OR ga.alias = '$hook' )
                     ";
        break;

        case 'entry': 
            $printpivot = 0;
            $where = "AND ge.id = '$hook'";
        break;

        case 'letter': 
            if ($hook != 'ALL' and $hook != 'SPECIAL') {
                $where = 'AND ' . sql_substr() . '(upper(concept),1,' .  $textlib->strlen($hook) . ') = \'' . $textlib->strtoupper($hook) . '\'';
            }
            if ($hook == 'SPECIAL') {
                //Create appropiate IN contents
                $alphabet = explode(",", get_string("alphabet"));
                $sqlalphabet = '';
                for ($i = 0; $i < count($alphabet); $i++) {
                    if ($i != 0) {
                        $sqlalphabet .= ',';
                    }
                    $sqlalphabet .= '\''.$alphabet[$i].'\'';
                }
                $where = 'AND ' . sql_substr() . '(upper(concept),1,1) NOT IN (' . $textlib->strtoupper($sqlalphabet) . ')';
            }
        break;
        }
        
        $sqlwhere   = "WHERE (ge.glossaryid = '$glossary->id' or ge.sourceglossaryid = '$glossary->id') AND
                             (ge.approved != 0 $userid)
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
    $count = count_records_sql("select count(*) $sqlfrom $sqlwhere");

    $limitfrom = $offset;
    $limitnum = 0;
    
    if ( $offset >= 0 ) {
        $limitnum = $entriesbypage;
    }

    $allentries = get_records_sql("$sqlselect $sqlfrom $sqlwhere $sqlorderby", $limitfrom, $limitnum);
?>
