<?php // $Id$
    require_once("../../config.php"); 
    require_once($CFG->libdir.'/filelib.php');

# this is the upload/download plugin, which allows to put arbitrary binary
# files into the ewiki database using the provided specialized form, or the
# standard image upload form below every edit page (if EWIKI_ALLOW_BINARY)


#-- settings

# Defined in view.php
#define("EWIKI_UPLOAD_MAXSIZE", 2*1024*1024);
define("EWIKI_PAGE_UPLOAD", "FileUpload");
define("EWIKI_PAGE_DOWNLOAD", "FileDownload");
define("EWIKI_ACTION_ATTACHMENTS", "attachments");  #-- define to 0 to disable

#-- register plugin (main part)
$ewiki_plugins["page"][EWIKI_PAGE_UPLOAD] = "ewiki_page_fileupload";
$ewiki_plugins["page"][EWIKI_PAGE_DOWNLOAD] = "ewiki_page_filedownload";
$ewiki_plugins["action"]["binary"] = "ewiki_binary";

#-- allow per-page downloads
if (defined("EWIKI_ACTION_ATTACHMENTS") && EWIKI_ACTION_ATTACHMENTS) {
  $ewiki_plugins["action"][EWIKI_ACTION_ATTACHMENTS] = "ewiki_action_attachments";
  $ewiki_config["action_links"]["view"][EWIKI_ACTION_ATTACHMENTS] = "Attachments";
}


#-- icons (best given absolute to www root)
/*$ewiki_binary_icons = array(
   ".bin"    => "/icons/exec.gif",
   "application/" => "/icons/exec.gif",
   "application/octet-stream" => "/icons/exec.gif",
   ".ogg"    => "/icons/son.gif",
   ".jpeg"    => "/icons/pic.gif",
   "text/"    => "/icons/txt.gif",
   ".pdf"    => "/icons/txt.gif",
);*/


#-- the upload function __can__ use different sections
$ewiki_upload_sections = array(
   "" => "main",
#   "section2" => "section2",
);


#-- text, translations
$ewiki_t["en"]["UPLOAD0"] = "Use this form to upload an arbitrary binary file into the wiki:<br />";
$ewiki_t["en"]["UPL_NEWNAM"] = "Save with different filename";
$ewiki_t["en"]["UPL_INSECT"] = "Upload into section";
$ewiki_t["en"]["UPL_TOOLARGE"] = "Your upload has been rejected, because that file was too large!";
$ewiki_t["en"]["UPL_REJSECT"] = 'The given download section "$sect" has been rejected. Please only use the default ones, or tell the WikiAdmin to reenable per-page uploads; else others can\'t find your uploaded files easily.<br /><br />';
$ewiki_t["en"]["UPL_OK"] = "Your file was uploaded correctly, please see <a href=\"\$script".EWIKI_PAGE_DOWNLOAD."\">".EWIKI_PAGE_DOWNLOAD."</a>.<br /><br />";
$ewiki_t["en"]["UPL_ERROR"] = "We're sorry, but something went wrong during the file upload.<br /><br />";
$ewiki_t["en"]["DWNL_SEEUPL"] = 'See also <a href="$script'.EWIKI_PAGE_UPLOAD.'">FileUpload</a>, this page is only about downloading.<br /><br />';
$ewiki_t["en"]["DWNL_NOFILES"] = "No files uploaded yet.<br />\n";
$ewiki_t["en"]["file"] = "File";
$ewiki_t["en"]["of"] = "of";
$ewiki_t["en"]["comment"] = "Comment";
$ewiki_t["en"]["dwnl_section"] = "download section";
$ewiki_t["en"]["DWNL_ENTRY_FORMAT"] =
    '<div class="download"><a href="$url">$icon$title</a><small>$size<br />'.
        'uploaded on <b>$time</b>, downloaded <tt>$hits</tt> times<br />'.
        '(<a href="$url">$id</a>)<br />'.
        '$section'.'file is of type <tt>$type</tt>'.
        '$comment'."</small></div><br />\n";

$ewiki_t["de"]["UPLOAD0"] = "Mit diesem Formular kannst du beliebige Dateien in das Wiki abspeichern:<br />";
$ewiki_t["de"]["UPL_NEWNAM"] = "Mit unterschiedlichem Dateinamen speichern";
$ewiki_t["de"]["UPL_INSECT"] = "Hochladen in Bereich:";
$ewiki_t["de"]["UPL_TOOLARGE"] = "Deine Datei wurde nicht aufgenommen, weil sie zu gro� war!";
$ewiki_t["de"]["UPL_REJSECT"] = 'Der angegebene Download-Bereich "$sect" wird nicht verwendet. Bitte verwende einen von den voreingestellten Bereichen, damit Andere die Datei sp�ter auch finden k�nnen, oder frag den Administrator das Hochladen f�r beliebige Seiten zu aktivieren.<br /><br />';
$ewiki_t["de"]["UPL_OK"] = "Deine Datei wurde korrekt hochgeladen, sehe einfach auf der <a href=\"\$script".EWIKI_PAGE_DOWNLOAD."\">".EWIKI_PAGE_DOWNLOAD."</a> nach.<br /><br />";
$ewiki_t["de"]["UPL_ERROR"] = "'Tschuldige, aber irgend etwas ist w�hrend des Hochladens gr�ndlich schief gelaufen.<br /><br />";
$ewiki_t["de"]["DWNL_SEEUPL"] = 'Siehe auch <a href="$script'.EWIKI_PAGE_UPLOAD.'">DateiHochladen</a>, auf dieser Seite stehen nur die Downloads.<br /><br />';
$ewiki_t["de"]["DWNL_NOFILES"] = "Noch keine Dateien hochgeladen.<br />\n";
$ewiki_t["de"]["file"] = "Datei";
$ewiki_t["de"]["of"] = "von";
$ewiki_t["de"]["comment"] = "Kommentar";
$ewiki_t["de"]["dwnl_section"] = "Download Bereich";
$ewiki_t["de"]["DWNL_ENTRY_FORMAT"] =
    '<div class="download"><a href="$url">$icon$title</a><small>$size<br />'.
        'am <b>$time</b> hochgeladen, <tt>$hits</tt> mal abgerufen<br />'.
        '(<a href="$url">$id</a>)<br />'.
        '$section'.'Datei ist vom Typ <tt>$type</tt>'.
        '$comment'."</small></div><br />\n";




function ewiki_page_fileupload($id, $data, $action, $def_sec="") {

   global $CFG, $ewiki_upload_sections, $ewiki_plugins;

   $o = ewiki_make_title($id, $id, 2);

   $upload_file = $_FILES[EWIKI_UP_UPLOAD];
   if (empty($upload_file)) {

      $o .= ewiki_t("UPLOAD0");

      $o .= '<div class="upload">'.
            '<form action="' .
            ewiki_script( ($action!="view" ? $action : ""), $id).
          '" method="post" enctype="multipart/form-data">' ;
      $o .= '<fieldset class="invisiblefieldset">';
      require_once($CFG->dirroot.'/lib/uploadlib.php');
      $o .= upload_print_form_fragment(1,array(EWIKI_UP_UPLOAD),array(ewiki_t("file")),false,null,0,0,true);
      $o .= '<input type="submit" value="' . EWIKI_PAGE_UPLOAD . '" /><br /><br />'
          .'<b>' . ewiki_t("comment") . '</b><br /><textarea name="comment" cols="35" rows="3"></textarea><br /><br />';
      
      if (empty($ewiki_upload_sections[$def_sec])) {
         $ewiki_upload_sections[$def_sec] = $def_sec;
      }
      if (count($ewiki_upload_sections) > 1) {
         if (empty($def_sec)) {
            $def_sec = $_REQUEST["section"];
         }
         $o .= '<b>'.ewiki_t("UPL_INSECT").'</b><br /><select name="section">';
         foreach ($ewiki_upload_sections as $id => $title) {
            $o .= '<option value="'.$id.'"' .($id==$def_sec?' selected':''). '>'.$title.'</option>';
         }
         $o .= '</select><br /><br />';
      }

      $o .= '<b>'.ewiki_t("UPL_NEWNAM").'</b><br /><input type="text" name="new_filename" size="20" /><br /><br />';

      $o .= '</fieldset></form></div>';

   }
   elseif ($upload_file["size"] > EWIKI_UPLOAD_MAXSIZE) {

      $o .= ewiki_t("UPL_TOOLARGE");

   }
   else {

      $meta = array(
         "X-Content-Type" => $upload_file["type"],
        #"X-Content-Length" => $upload_file["size"],
      );
      if (($s = $upload_file["name"]) && (strlen($s) >= 3)
         || ($s = substr(md5(time()+microtime()),0,8) . ".dat"))
      {
         if (strlen($uu = trim($_REQUEST["new_filename"])) >= 3) {
            if ($uu != $s) {
               $meta["Original-Filename"] = $s;
            }
            $s = $uu;
         }
         $meta["Content-Location"] = $s;
         ($p = 0) or
         ($p = strrpos($s, "/")) and ($p++) or
         ($p = strrpos($s, '\\')) and ($p++);
         $meta["Content-Disposition"] = 'attachment; filename="'.urlencode(substr($s, $p)).'"';
      }
      if (strlen($sect = $_REQUEST["section"])) {
         if ($ewiki_upload_sections[$sect]
            || ($action==EWIKI_ACTION_ATTACHMENTS) && ($data["content"])
               && strlen($ewiki_plugins["action"][EWIKI_ACTION_ATTACHMENTS])) {
            $meta["section"] = $sect;
         }
         else {
            $o .= ewiki_t("UPL_REJSECT", array('sect' => $sect));

            return($o);
         }
      }
      if (strlen($s = trim($_REQUEST["comment"]))) {
         $meta["comment"] = $s;
      }

      $result = ewiki_binary_save_image($upload_file["tmp_name"], "", "RETURN", $meta, "ACCEPT_ALL", $care_for_images=0);

      if ($result) {
         $o .= ewiki_t("UPL_OK", array('$script'=>ewiki_script()));
      }
      else {
         $o .= ewiki_t("UPL_ERROR");
      }

   }

   return($o);
}




function ewiki_page_filedownload($id, $data, $action, $def_sec="") {

   global $ewiki_binary_icons, $ewiki_upload_sections;

   $o = ewiki_make_title($id, $id, 2);
#<off>#   $o .= ewiki_t("DWNL_SEEUPL", '$scr'=>ewiki_script("", ""));


   #-- params (section, orderby)
   ($orderby = $_REQUEST["orderby"]) or ($orderby = "created");

   if ($def_sec) {
      $section = $def_sec;
   }
   else {
      ($section = $_REQUEST["section"]) or ($section = "");
      if (count($ewiki_upload_sections) > 1) {
         $oa = array();
         $ewiki_upload_sections["*"] = "*";
         if (empty($ewiki_plugins["action"][EWIKI_ACTION_ATTACHMENTS])) {
            $ewiki_upload_sections["**"] = "**";
         }
         foreach ($ewiki_upload_sections as $sec=>$title) {
            $oa[] = '<a href="' . ewiki_script("", $id, array(
               "orderby"=>$orderby, "section" => $sec)) .
               '">' . $title . "</a>";
         }
         $o .= '<div class="mdl-align darker">'.implode(" &middot; ", $oa).'</div><br />';
      }
   }


   #-- collect entries
   $files = array();
   $sorted = array();
   $result = ewiki_database("GETALL", array("flags", "meta", "created", "hits", "userid"));

   while ($row = $result->get()) {
      if (($row["flags"] & EWIKI_DB_F_TYPE) == EWIKI_DB_F_BINARY) {

         $m = &$row["meta"];
         if(!$section) {
           $section="**";
         }
         if ($m["section"] != $section) {
            if ($section == "**") {
            }
            elseif (($section == "*") && !empty($ewiki_upload_sections[$m["section"]])) {
            }
            else {
               continue;
            }
         }
         else {
         }

         $files[$row["id"]] = $row;
         $sorted[$row["id"]] = $row[$orderby];
      }
   }


   #-- sort
   arsort($sorted);


   #-- slice
   ($pnum = $_REQUEST[EWIKI_UP_PAGENUM]) or ($pnum = 0);
   if (count($sorted) > EWIKI_LIST_LIMIT) {
      $o_nl .= '<div class="lighter">&gt;&gt; ';
      for ($n=0; $n < (int)(count($sorted) / EWIKI_LIST_LIMIT); $n++) {
         $o_nl .= '<a href="' . ewiki_script("", $id, array(
           "orderby"=>$orderby, "section"=>$section, EWIKI_UP_PAGENUM=>$n)) .
            '">[' . $n . "]</a>  ";
      }
      $o_nl .= '</div><br />';
      $o .= $o_nl;
   }
   $sorted = array_slice($sorted, $pnum * EWIKI_LIST_LIMIT, EWIKI_LIST_LIMIT);


   #-- output
   if (empty($sorted)) {

      $o .= ewiki_t("DWNL_NOFILES");
   }
   else {

      foreach ($sorted as $id=>$uu) {
         $row = $files[$id];
         $o .= ewiki_entry_downloads($row, $section[0]=="*", true);
      }
   }

   $o .= $o_nl;

   return($o);

}




function ewiki_entry_downloads($row, $show_section=0, $fullinfo=false) {

   global $ewiki_binary_icons, $ewiki_upload_sections;

   $meta = &$row["meta"];

   $id = $row["id"];
   $p_title = basename($meta["Content-Location"]);
   $p_time = userdate($row["created"]);
   
   
   $p_hits = ($row["hits"] ? $row["hits"] : "0");
   $p_size = $meta["size"];
   $p_size = isset($p_size) ? (", " . ($p_size>=4096 ? round($p_size/1024)."K" : $p_size." bytes")) : "";
   $p_ct1 = $meta["Content-Type"];
   $p_ct2 = $meta["X-Content-Type"];
   if ($p_ct1==$p_ct2) { unset($p_ct2); }
       if ($p_ct1 && !$p_ct2) { $p_ct = "<tt>$p_ct1</tt>"; }
   elseif (!$p_ct1 && $p_ct2) { $p_ct = "<tt>$p_ct2</tt>"; }
   elseif ($p_ct1 && $p_ct2) { $p_ct = "<tt>$p_ct1</tt>, <tt>$p_ct2</tt>"; }
     else { $p_ct = "<tt>application/octet-stream</tt>"; }
   $p_section = $ewiki_upload_sections[$meta["section"]];
   $p_section = $p_section ? $p_section : $meta["section"];
   $p_comment = strlen($meta["comment"]) ? '<table border="1" cellpadding="2" cellspacing="0"><tr><td class="lighter">'.
                str_replace('</p>', '', str_replace('<p>', '',
                ewiki_format($meta["comment"]))) . '</td></tr></table>' : "<br />";

   $p_icon = "";
   /*foreach ($ewiki_binary_icons as $str => $i) {
      if (empty($str) || strstr($row["Content-Location"], $str) || strstr($p_ct, $str) || strstr($p_ct2, $str)) { 
         $p_icon = $i;
         $p_icon_t = $str;
      }
   }*/
   
   /// Moodle Icon Handling
   global $CFG;
   $icon = mimeinfo("icon", $id);
   $p_icon="$CFG->pixpath/f/$icon";
   $p_icon_t="";

   $info->id = $id;
   $info->size = $p_size;
   $info->icon = ($p_icon ? '<img src="'.$p_icon.'" alt="['.$p_icon_t.']" class="icon" /> ' : '');
   $info->time = $p_time;
   $info->hits = $p_hits;
   $info->section = ($show_section ? ewiki_t('dwnl_section') . ": $p_section<br />" : '');
   $info->type = $p_ct;
   $info->url =  ewiki_script_binary("", $row["id"]);
   $info->title = $p_title;
   $info->comment = format_text($p_comment);

   if ($fullinfo) {
        if ($user = get_record('user', 'id', (int)$row['userid'])) {
            if (!isset($course->id)) {
                $course->id = 1;
            }
            $picture = print_user_picture($user->id, $course->id, $user->picture, false, true, true);
            $value = $picture." <a href=\"$CFG->wwwroot/user/view.php?id=$user->id&amp;course=$course->id\">".
                     fullname($user)."</a>";
        }

        $o .= '<a href="'.$info->url.'">'.$info->icon.$info->title.'</a>'.$info->size.'<br />'.
            $info->comment.
            $info->section." ".get_string("fileisoftype","wiki").": ".$info->type.'<br />'.
            get_string("uploadedon","wiki").": ".$info->time.", ".
            ' by '.$value.'<br />'.
            get_string("downloadtimes","wiki",$info->hits)."<br />".
//            '(<a href="'.$info->url.'">'.$info->id."</a>)<br />".
            '<br /><br />';
   }
   else {
//       global $moodle_format;   // from wiki/view.php
        $o .= '<a href="'.$info->url.'">'.$info->icon.$info->title.'</a>'.$info->size.'<br />'.
              $info->comment.'<br /><br />';
//        $o = format_text($o, $moodle_format);
   }

   
   
   ewiki_t("DWNL_ENTRY_FORMAT", $info);

   return($o);
}



#------------------------------------------------------- per-page uploads ---


function ewiki_action_attachments($id, $data, $action=EWIKI_ACTION_ATTACHMENTS) {

   if (!empty($_FILES[EWIKI_UP_UPLOAD])) {
      $o .= ewiki_page_fileupload($id, $data, EWIKI_ACTION_ATTACHMENTS, $id);
   }

   $o .= ewiki_page_filedownload(ucwords(EWIKI_ACTION_ATTACHMENTS) . " " . ewiki_t("of") . " $id", $data, "view", $id);

   unset($_FILES[EWIKI_UP_UPLOAD]);
   $o .= ewiki_page_fileupload($id, $data, EWIKI_ACTION_ATTACHMENTS, $id);

   return($o);

}


?>
