<?php
/*  MySQL database backend
    (Glue between Moodle and ewiki Database)

    Adapted by Michael Schneider
*/

/// Glue
$ewiki_plugins["database"][0] = "ewiki_database_moodle";

///     #-- predefine some of the configuration constants
define("EWIKI_NAME", $wiki_entry->pagename);

define("EWIKI_CONTROL_LINE", 0);
define("EWIKI_LIST_LIMIT", 25);
define("EWIKI_DEFAULT_LANG", current_language());
define("EWIKI_HTML_CHARS", 1);
define("EWIKI_DB_TABLE_NAME", "wiki_pages");


function ewiki_database_moodle($action, &$args, $sw1, $sw2) {
    global $wiki, $wiki_entry, $CFG;
    #-- result array
    $r = array();

    switch($action) {

        /*  Returns database entry as array for the page whose name was given
          with the "id" key in the $args array, usually fetches the latest
          version of a page, unless a specific "version" was requested in
          the $args array.
        */
        # Ugly, but we need to choose which wiki we are about to change/read
    case "GET":
        $id = "'" . anydb_escape_string($args["id"]) . "'";
        ($version = 0 + @$args["version"]) and ($version = "AND (version=$version)") or ($version="");

        $select="(pagename=$id) AND wiki=".$wiki_entry->id."  $version ";
        $sort="version DESC";

        $result_obj=get_records_select(EWIKI_DB_TABLE_NAME, $select,$sort,"*",0,1);
        if($result_obj)  {
            $r=get_object_vars($result_obj[$args["id"]]);
            $r["id"] = $r["pagename"];
            unset($r["pagename"]);
            $r["meta"] = @unserialize($r["meta"]);
        }
        break;

     /*  Increases the hit counter for the page name given in $args array
         with "id" index key.
     */
    case "HIT":
        execute_sql("UPDATE " .$CFG->prefix.EWIKI_DB_TABLE_NAME . " SET hits=(hits+1) WHERE pagename='" . anydb_escape_string($args["id"]) . "' and wiki=".$wiki_entry->id, 0);
        break;
    /*  Stores the $data array into the database, while not overwriting
        existing entries (using WRITE); returns 0 on failure and 1 if
        saved correctly.
    */
    case "OVERWRITE":
        $COMMAND = "REPLACE";
        break;

    case "WRITE":
        $COMMAND="WRITE";
        $args["pagename"] = $args["id"];
        unset($args["id"]);

        if (is_array($args["meta"])) {
           $args["meta"] = serialize($args["meta"]);
        }

        foreach ($args as $index => $value) {
           if (is_int($index)) {
               continue;
           }
           $args[$index] =anydb_escape_string($value);
        }
        $args["wiki"]=$wiki_entry->id;

        # Check if Record exists
        if($COMMAND=="REPLACE") {
            if(count_records(EWIKI_DB_TABLE_NAME,"wiki", $wiki_entry->id,"pagename",$args["pagename"],"version",$args["version"])) {
                delete_record(EWIKI_DB_TABLE_NAME,"wiki", $wiki_entry->id,"pagename",$args["pagename"],"version",$args["version"]);
            }
        }

        # Write
        $result=insert_record(EWIKI_DB_TABLE_NAME,$args,false,"pagename");

        return $result;
        break;

      /*  Checks for existence of the WikiPages whose names are given in
          the $args array. Returns an array with the specified WikiPageNames
          associated with values of "0" or "1" (stating if the page exists
          in the database). For images/binary db entries returns the "meta"
          field instead of an "1".
      */
    case "FIND":
        $select = "";
        foreach (array_values($args) as $id) {
            if (strlen($id)) {
                $r[$id] = 0;
                $select .= ($select ? " OR " : "") .
                    "(pagename='" . anydb_escape_string($id) . "')";
            }
        }
        if($select) {
            $select = "(".$select.") AND wiki=".$wiki_entry->id;
            $result = get_records_select(EWIKI_DB_TABLE_NAME,$select);
            while(list($key, $val) = @each($result)) {
                $r[$val->pagename]=strpos($val->meta, 's:5:"image"') ? $val->meta : 1;
            }
        }
         break;

     /* Counts the number of Versions 
     */
    case "COUNTVERSIONS":
        $sql= "SELECT pagename AS id, count(*) as versioncount".
           " FROM ". $CFG->prefix.EWIKI_DB_TABLE_NAME .
           " WHERE wiki = ".$wiki_entry->id.
           " GROUP BY id";

        $result=get_records_sql($sql);
        while(list($key, $val) = each($result)) {
            $r[$key]=$val->versioncount;
        }
        break;
      
    /*  Returns an array of __all__ pages, where each entry is made up
        of the fields from the database requested with the $args array,
        e.g. array("flags","meta","lastmodified");
    */
    case "GETALL":
        switch ($CFG->dbtype) {
           case 'postgres7': 
                $sql= "SELECT pagename AS id, ".
                implode(", ", $args) .
                " FROM ". $CFG->prefix.EWIKI_DB_TABLE_NAME .
                " WHERE wiki = ".$wiki_entry->id.
                " GROUP BY id, ".implode(", ", $args);
                break;
           default:
                $sql= "SELECT pagename AS id, ".
                implode(", ", $args) .
                " FROM ". $CFG->prefix.EWIKI_DB_TABLE_NAME .
                " WHERE wiki = ".$wiki_entry->id.
                " GROUP BY id, version" ;
        }

        $result=get_records_sql($sql);
        $r = new ewiki_dbquery_result($args);

        $drop = "";

        if(!$result) {
           $result=array();
        }
        while(list($key, $val) = each($result)) {
            $row=get_object_vars($val);
            $i = EWIKI_CASE_INSENSITIVE ? strtolower($row["id"]) : $row["id"];
            if ($i != $drop) {
               $drop = $i;
               $r->add($row);
            }
        }

        break;



    /*  Returns array of database entries (also arrays), where the one
        specified column matches the specified content string, for example
        $args = array("content" => "text...piece")
        is not guaranteed to only search/return the latest version of a page
    */
    case "SEARCH":
        $field = implode("", array_keys($args));
        $content = strtolower(implode("", $args));
        if ($field == "id") { $field = "pagename"; }

        switch ($CFG->dbtype) {
            case 'postgres7':
                $sql= "SELECT pagename AS id, version, flags" .
                    (EWIKI_DBQUERY_BUFFER && ($field!="pagename") ? ", $field" : "") .
                    " FROM " . $CFG->prefix.EWIKI_DB_TABLE_NAME .
                    " WHERE $field ILIKE '%".$content."%'  and wiki=".$wiki_entry->id .
                    " GROUP BY id, version, flags ". 
                    (EWIKI_DBQUERY_BUFFER && ($field!="pagename") ? ", $field" : "") ;
                break;
            default: 
                $sql= "SELECT pagename AS id, version, flags" .
                    (EWIKI_DBQUERY_BUFFER && ($field!="pagename") ? ", $field" : "") .
                    " FROM " . $CFG->prefix.EWIKI_DB_TABLE_NAME .
                    " WHERE LOCATE('" . anydb_escape_string($content) . "', LCASE($field))  and wiki=".$wiki_entry->id .
                    " GROUP BY id, version ";
        } 
        
        $result=get_records_sql($sql);

        $r = new ewiki_dbquery_result(array("id","version",$field));
        $drop = "";
        while(list($key, $val) = @each($result)) {
            $row=get_object_vars($val);
            $i = EWIKI_CASE_INSENSITIVE ? strtolower($row["id"]) : $row["id"];
            if ($i != $drop) {
                $drop = $i;
                $r->add($row);
            }
        }
        break;


    case "DELETE":
        $id = anydb_escape_string($args["id"]);
        $version = $args["version"];

        delete_records(EWIKI_DB_TABLE_NAME,"wiki", $wiki_entry->id,"pagename",$id,"version",$version);

        break;



   case "INIT":
        break;

   default:
}

   return($r);
}

function anydb_escape_string($s) {
   global $CFG ;
   $type = ($CFG->dbtype);
   switch ($CFG->dbtype) {
        case 'mysql':
            $s = mysql_escape_string($s);
            break;
        case 'postgres7':
            $s = pg_escape_string($s);
            break;
        default:
            $s = addslashes($s);
   }

   return($s);
}