<?php
#
#  The otherwise invisible markup [notify:you@there.net] will trigger a
#  mail, whenever a page is changed. The TLD decides in which language
#  the message will be delivered. One can also append the lang code after
#  a comma or semicolon behind the mail address to set it explicitely:
#  [notify:me@here.org,de] or [notify:you@there.net;eo]
#
#  Nevertheless English will be used as the default automagically, if
#  nothing else was specified, no need to worry about this.
#
#  additional features:
#   * diff inclusion
#   * [notify:icq:123456789] - suddenly ICQ.com took the pager service down
#
#  To include a diff, just set the following constant. Also use it to
#  define the minimum number of changed bytes that are necessary to
#  result in a notification mail. Only use it with Linux/UNIX.

define("EWIKI_NOTIFY_WITH_DIFF", 0);       #-- set it to 100 or so
define("EWIKI_NOTIFY_SENDER",'ewiki');


#-- glue
$ewiki_plugins["edit_hook"][] = "ewiki_notify_edit_hook";
$ewiki_plugins["format_source"][] = "ewiki_format_remove_notify";
$ewiki_config["interwiki"]["notify"] = "mailto:";


#-- email message text ---------------------------------------------------
$ewiki_t["en"]["NOTIFY_SUBJECT"] = '"$id" was changed [notify:...]';
$ewiki_t["en"]["NOTIFY_BODY"] = <<<_END_OF_STRING
Hi,

A WikiPage has changed and you requested to be notified when this
happens. The changed page was '\$id' and can be found
at the following URL:
\$link

To stop messages like this please strip the [notify:...] with your address
from the page edit box at \$edit_link

(\$wiki_title on http://\$server/)
\$server_admin
_END_OF_STRING;


#-- translation.de
$ewiki_t["de"]["NOTIFY_SUBJECT"] = '"$id" wurde geändert [notify:...]';
$ewiki_t["de"]["NOTIFY_BODY"] = <<<_END_OF_STRING
Hi,

Eine WikiSeite hat sich geändert, und du wolltest ja unbedingt wissen,
wenn das passiert. Die geänderte Seite war '\$id' und
ist leicht zu finden unter folgender URL:
\$link

Wenn du diese Benachrichtigungen nicht mehr bekommen willst, solltest du
deine [notify:...]-Adresse aus der entsprechenden Edit-Box herauslöschen:
\$edit_link

(\$wiki_title auf http://\$server/)
\$server_admin
_END_OF_STRING;


#----------------------------------------------------------------------------



#-- implementatition
function ewiki_notify_edit_hook($id, $data, &$hidden_postdata) {

   global $ewiki_t, $ewiki_plugins;
   $ret_err = 0;

   if (!isset($_REQUEST["save"])) {
      return(false);
   }

   $mailto = ewiki_notify_links($data["content"], 0);

   if (!count($mailto)) {
      return(false); 
   }

   #-- generate diff
   $diff = "";
   if (EWIKI_NOTIFY_WITH_DIFF && (DIRECTORY_SEPARATOR=="/")) {

      #-- save page versions temporarily as files
      $fn1 = EWIKI_TMP."/ewiki.tmp.notify.diff.".md5($data["content"]);
      $fn2 = EWIKI_TMP."/ewiki.tmp.notify.diff.".md5($_REQUEST["content"]);
      $f = fopen($fn1, "w");
      fwrite($f, $data["content"]);
      fclose($f);
      $f = fopen($fn2, "w");
      fwrite($f, $_REQUEST["content"]);
      fclose($f);
      #-- set mtime of the old one (GNU diff will report it)
      touch($fn1, $data["lastmodified"]);

      #-- get diff output, rm temp files
      $diff_exe = "diff";
      if ($f = popen("$diff_exe  --normal --ignore-case --ignore-space-change  $fn1 $fn2   2>&1 ", "r")) {

         $diff .= fread($f, 16<<10);
         pclose($f);

         $diff_failed = !strlen($diff)
                     || (strpos($diff, "Files ") === 0);

         #-- do not [notify:] if changes were minimal
         if ((!$diff_failed) && (strlen($diff) < EWIKI_NOTIFY_WITH_DIFF)) {
#echo("WikiNotice: no notify, because too few changes (" .strlen($diff)." byte)\n");
            $ret_err = 1;
         }

         $diff = "\n\n-----------------------------------------------------------------------------\n\n"
               . $diff;
      }
      else {
         $diff = "";
#echo("WikiWarning: diff failed in notify module\n");
      }

      unlink($fn1);
      unlink($fn2);

      if ($ret_err) {
         return(false);
      }
   }

   #-- separate addresses into (TLD) groups
   $mailto_lang = array(
   );
   foreach ($mailto as $m) {

      $lang = "";

      #-- remove lang selection trailer
      $m = strtok($m, ",");
      if ($uu = strtok(",")) {
         $lang = $uu;
      }
      $m = strtok($m, ";");
      if ($uu = strtok(";")) {
         $lang = $uu;
      }

      #-- else use TLD as language code
      if (empty($lang)) {
         $r = strrpos($m, ".");
         $lang = substr($m, $r+1);
      }
      $lang = trim($lang);

      #-- address mangling
      $m = trim($m);
      if (substr($m, 0, 4) == "icq:") {
         $m = substr($m, 4) . "@pager.icq.com";
      }

      $mailto_lang[$lang][] = $m;
   }

   #-- go thru email address groups
   foreach ($mailto_lang as $lang=>$a_mailto) {

      $pref_langs = array_merge(array(
         "$lang", "en"
      ), $ewiki_t["languages"]);

      ($server = $_SERVER["HTTP_HOST"]) or
      ($server = $_SERVER["SERVER_NAME"]);
      $s_4 = "http".($_SERVER['HTTPS'] == "on" ? 's':'')."://" . $server . $_SERVER["REQUEST_URI"];
      $link = str_replace("edit/$id", "$id", $s_4);

      $m_text = ewiki_t("NOTIFY_BODY", array(
         "id" => $id,
         "link" => $link,
         "edit_link" => $s_4,
         "server_admin" => $_SERVER["SERVER_ADMIN"],
         "server" => $server,
         "wiki_title" => EWIKI_PAGE_INDEX,
      ), $pref_langs);
      $m_text .= $diff;

      $m_from = EWIKI_NOTIFY_SENDER."@$server";
      $m_subject = ewiki_t("NOTIFY_SUBJECT", array(
         "id" => $id,
      ), $pref_langs);

      $m_to = implode(", ", $a_mailto);

      mail($m_to, $m_subject, $m_text, "From: \"$s_2\" <$m_from>\nX-Mailer: ErfurtWiki/".EWIKI_VERSION);

   }
}



function ewiki_notify_links(&$source, $strip=1) {
   $links = array();
   $l = 0;
   if (strlen($source) > 10)
   while (($l = @strpos($source, "[notify:", $l)) !== false) {
      $r = strpos($source, "]", $l);
      $str = substr($source, $l, $r + 1 - $l);
      if (!strpos("\n", $str)) {
         $links[] = trim(substr($str, 8, -1));
         if ($strip) {
            $source = substr($source, 0, $l) . substr($source, $r + 1);
         }
      }
      $l++;
   }
   return($links);
}



function ewiki_format_remove_notify(&$source) {
   ewiki_notify_links($source, 1);
}



?>