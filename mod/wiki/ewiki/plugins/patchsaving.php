<?php

/*
   This plugin catches concurrent edits of a page, and lets the 'patch'
   and 'diff' utilities try to merge the different versions. This will
   often prevent the "This page version was already saved by someone else"
   failure message.
   Please use the GNU diff and patch only. Sometimes the unified output
   format may be superiour; but this depends on the subjects in your Wiki.
*/

define("EWIKI_BIN_DIFF", "/usr/bin/diff");
define("EWIKI_BIN_PATCH", "/usr/bin/patch");

if (function_exists("is_executable") && is_executable(EWIKI_BIN_PATCH) && is_executable(EWIKI_BIN_DIFF)) {
  $ewiki_plugins["edit_patch"][] = "ewiki_edit_patch";
}


function ewiki_edit_patch($id, &$data) {

   $r = false;

   $base = ewiki_database(
      "GET",
      array("id"=>$id, "version"=>$_REQUEST["version"])
   );
   if (!$base) { 
     return(false);
   }

   $fn_base = EWIKI_TMP."/ewiki.base.".md5($base["content"]);
   $fn_requ = EWIKI_TMP."/ewiki..requ.".md5($_REQUEST["content"]);
   $fn_patch = EWIKI_TMP."/ewiki.patch.".md5($base["content"])."-".md5($_REQUEST["content"]);
   $fn_curr = EWIKI_TMP."/ewiki.curr.".md5($data["content"]);

   if ($f = fopen($fn_base, "w")) {
     fwrite($f, $base["content"]);
     fclose($f);
   }
   else { 
     return(false);
   }

   if ($f = fopen($fn_requ, "w")) {
     fwrite($f, $_REQUEST["content"]);
     fclose($f);
   }
   else { 
     unlink($fn_base);
     return(false);
   }

   if ($f = fopen($fn_curr, "w")) {
     fwrite($f, $data["content"]);
     fclose($f);
   }
   else { 
     unlink($fn_base);
     unlink($fn_requ);
     return(false);
   }

   exec("diff -c $fn_base $fn_requ > $fn_patch", $output, $retval);
   if ($retval) {

      exec("patch $fn_curr $fn_patch", $output, $retval);
      if (!$retval) {

         $_REQUEST["version"] = $curr["version"];
         $_REQUEST["content"] = implode("", file($fn_curr));
         $r = true;

      }
   }

   unlink($fn_base);
   unlink($fn_requ);
   unlink($fn_patch);
   unlink($fn_curr);

   return($r);
}


?>