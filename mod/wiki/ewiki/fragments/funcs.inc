#
# this file contains various useful helper functions, to interfer
# with the ewiki database from within another site engine
#
# may be there is something useful in here for you, too
#


#-- save newest pages
function save_newest_pages()
{
   $sorted = array();
   foreach (ewiki_database("GETALL", array("lastmodified", "flags", "version")) as $row) {
      if (($row["flags"] & EWIKI_DB_F_TYPE) == EWIKI_DB_F_TEXT) {
         $sorted[$row["id"]] = $row["lastmodified"];
      }
   }
   arsort($sorted);
   $n = 0;
   $o = "";
   foreach ($sorted as $id=>$uu) {
      $o .= '·<a href="/wiki/?id=' . urlencode($id) . '">' .
            preg_replace('/(\w{15}[a-zäöüß]*)(\w{3,5})/', '$1&shy;$2', $id) . "</a><br>\n";
      if ($n++ >= 15) break;
   }
   $o = addslashes($o);

   mysql_query("UPDATE text_table SET html='$o' WHERE filename='wiki-updated' ")
   or
   return($o);
}


