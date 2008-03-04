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

         # $result = mysql_query("SELECT * FROM " . EWIKI_DB_TABLE_NAME
         #   . " WHERE (pagename=$id) $version  ORDER BY version DESC  LIMIT 1"
         #);
         #if ($result && ($r = mysql_fetch_array($result, MYSQL_ASSOC))) {
         #   $r["id"] = $r["pagename"];
         #   unset($r["pagename"]);
         #}
         #if (strlen($r["meta"])) {
         #   $r["meta"] = @unserialize($r["meta"]);
         #}

         $select="(pagename=$id) AND wiki=".$wiki_entry->id."  $version ";
         $sort="version DESC";
         if ($result_arr = get_records_select(EWIKI_DB_TABLE_NAME, $select,$sort,"*",0,1)) {
             //Iterate to get the first (and unique!)
             foreach ($result_arr as $obj) {
                 $result_obj = $obj;
            }
         }
         if($result_obj)  {
           //Convert to array
           $r=get_object_vars($result_obj);
           $r["id"] = $r["pagename"];
           unset($r["pagename"]);
           $r["meta"] = @unserialize($r["meta"]);
         }
         break;



      /*  Increases the hit counter for the page name given in $args array
          with "id" index key.
      */
      case "HIT":
         #mysql_query("UPDATE " . EWIKI_DB_TABLE_NAME . " SET hits=(hits+1) WHERE pagename='" . anydb_escape_string($args["id"]) . "'");
         # set_field does not work because of the "hits+1" construct
         #print "DO ".anydb__escape_string($args["id"]); exit;
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

         #$sql1 = $sql2 = "";
         #foreach ($args as $index => $value) {
         #   if (is_int($index)) {
         #      continue;
         #   }
         #   $a = ($sql1 ? ', ' : '');
         #   $sql1 .= $a . $index;
         #   $sql2 .= $a . "'" . anydb_escape_string($value) . "'";
         #}

         #strlen(@$COMMAND) || ($COMMAND = "INSERT");

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
         $result=insert_record(EWIKI_DB_TABLE_NAME,(object)$args,false);

         #$result = mysql_query("$COMMAND INTO " . EWIKI_DB_TABLE_NAME .
         #   " (" . $sql1 . ") VALUES (" . $sql2 . ")"
         #);
         #return($result && mysql_affected_rows() ?1:0);

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
           #$sql = "SELECT pagename AS id, meta FROM " .
           #   EWIKI_DB_TABLE_NAME . " WHERE $sql "
           #);
           #while ($result && ($row = mysql_fetch_row($result))) {
           #   $r[$row[0]] = strpos($row[1], 's:5:"image"') ? $row[1] : 1;

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
            " GROUP BY pagename";

         #print "$sql";
         $result=get_records_sql($sql);
         while(list($key, $val) = each($result)) {
            $r[$key]=$val->versioncount;
         }
      break;

      /*  Returns an array of the lastest versions of __all__ pages,
          where each entry is made up of the fields from the database
          requested with the $args array, e.g.
          array("flags","meta","lastmodified");
      */
      case "GETALL":
         switch ($CFG->dbfamily) {
             case 'postgres':
                 // All but the latest version eliminated by DISTINCT
                 // ON (pagename)
                 $sql= "SELECT DISTINCT ON (pagename) pagename AS id, ".
                      implode(", ", $args) .
                      " FROM ". $CFG->prefix.EWIKI_DB_TABLE_NAME .
                      " WHERE wiki = ".$wiki_entry->id.
                      " ORDER BY pagename, version DESC";
                 break;
             case 'mysql':
                 // All but the latest version eliminated by
                 // mysql-specific GROUP BY-semantics
                 $sql= "SELECT pagename AS id, ".
                 implode(", ", $args) .
                      " FROM ". $CFG->prefix.EWIKI_DB_TABLE_NAME .
                      " WHERE wiki = ".$wiki_entry->id.
                      " GROUP BY id, version DESC " ;
             default:
                 // All but the latest version are here eliminated in
                 // get_records_sql, since it will return an array
                 // with only one result per id-field value. Note,
                 // that for this to work the query needs to order the
                 // records ascending by version, so later versions
                 // will overwrite previous ones in
                 // recordset_to_array. This is not pretty.
                 $sql= "SELECT pagename AS id, ".
                 implode(", ", $args) .
                      " FROM ". $CFG->prefix.EWIKI_DB_TABLE_NAME .
                      " WHERE wiki = ".$wiki_entry->id.
                      " ORDER BY version";
         }

         $result=get_records_sql($sql);
         $r = new ewiki_dbquery_result($args);

         if ($result) {
             foreach($result as $val) {
                 $r->add(get_object_vars($val));
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
         $sql= "SELECT pagename AS id, version, flags" .
             (EWIKI_DBQUERY_BUFFER && ($field!="pagename") ? ", $field" : "") .
             " FROM " . $CFG->prefix.EWIKI_DB_TABLE_NAME .
             " WHERE $field " . sql_ilike() . " '%".anydb_escape_string($content)."%'  and wiki=".$wiki_entry->id .
             " ORDER BY id, version ASC";
         $result=get_records_sql($sql);

         $r = new ewiki_dbquery_result(array("id","version",$field));
         $drop = "";
         #while ($result && ($row = mysql_fetch_array($result, MYSQL_ASSOC))) {
         #   $i = EWIKI_CASE_INSENSITIVE ? strtolower($row["id"]) : $row["id"];
         #   if ($i != $drop) {
         #      $drop = $i;
         #      $r->add($row);
         #   }
         #}
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

         #mysql_query("DELETE FROM " . EWIKI_DB_TABLE_NAME ."
         #   WHERE pagename='$id' AND version=$version");
         # print "DELETING wiki:".$wiki_entry->id."Pagename: $id Version: $version <br />\n";
         delete_records(EWIKI_DB_TABLE_NAME,"wiki", $wiki_entry->id,"pagename",$id,"version",$version);

         break;



      case "INIT":
         #mysql_query("CREATE TABLE " . EWIKI_DB_TABLE_NAME ."
         #   (pagename VARCHAR(160) NOT NULL,
         #   version INTEGER UNSIGNED NOT NULL DEFAULT 0,
         #   flags INTEGER UNSIGNED DEFAULT 0,
         #   content MEDIUMTEXT,
         #   author VARCHAR(100) DEFAULT 'ewiki',
         #   created INTEGER UNSIGNED DEFAULT ".time().",
         #   lastmodified INTEGER UNSIGNED DEFAULT 0,
         #   refs MEDIUMTEXT,
         #   meta MEDIUMTEXT,
         #   hits INTEGER UNSIGNED DEFAULT 0,
         #   PRIMARY KEY id (pagename, version) )
         #   ");
         #echo mysql_error();
         break;

      default:
   }

   return($r);
}

function anydb_escape_string($s) {
   return(addslashes($s));
}

