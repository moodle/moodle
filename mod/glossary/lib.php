<?PHP  // $Id$

/// Library of functions and constants for module glossary
/// (replace glossary with the name of your module and delete this line)

require_once("$CFG->dirroot/files/mimetypes.php");

$tCFG->TabTableBGColor = $THEME->cellcontent2;
$tCFG->TabTableWidth = "70%";
$tCFG->ActiveTabColor = $THEME->cellcontent2;
$tCFG->InactiveTabColor = $THEME->cellheading2;
$tCFG->TabsPerRow = 5;
$tCFG->TabSeparation = 4;

function glossary_add_instance($glossary) {
/// Given an object containing all the necessary data,
/// (defined by the form in mod.html) this function
/// will create a new instance and return the id number
/// of the new instance.

    $glossary->timecreated = time();
    $glossary->timemodified = $glossary->timecreated;

    # May have to add extra stuff in here #

    return insert_record("glossary", $glossary);
}


function glossary_update_instance($glossary) {
/// Given an object containing all the necessary data,
/// (defined by the form in mod.html) this function
/// will update an existing instance with new data.

    $glossary->timemodified = time();
    $glossary->id = $glossary->instance;

    # May have to add extra stuff in here #

    return update_record("glossary", $glossary);
}


function glossary_delete_instance($id) {
/// Given an ID of an instance of this module,
/// this function will permanently delete the instance
/// and any data that depends on it.

    if (! $glossary = get_record("glossary", "id", "$id")) {
        return false;
    }

    $result = true;

    # Delete any dependent records here #

    if (! delete_records("glossary", "id", "$glossary->id")) {
        $result = false;
    }
    delete_records("glossary_entries", "glossaryid", "$glossary->id");

    return $result;
}

function glossary_user_outline($course, $user, $mod, $glossary) {
/// Return a small object with summary information about what a
/// user has done with a given particular instance of this module
/// Used for user activity reports.
/// $return->time = the time they did it
/// $return->info = a short text description

    return $return;
}

function glossary_user_complete($course, $user, $mod, $glossary) {
/// Print a detailed representation of what a  user has done with
/// a given particular instance of this module, for user activity reports.

    return true;
}

function glossary_print_recent_activity($course, $isteacher, $timestart) {
/// Given a course and a time, this module should find recent activity
/// that has occurred in glossary activities and print it out.
/// Return true if there was output, or false is there was none.

    global $CFG, $THEME;

    if (!$logs = get_records_select("log", "time > '$timestart' AND ".
                                           "course = '$course->id' AND ".
                                           "module = 'glossary' AND ".
                                           "action = 'add %' ", "time ASC")) {
        return false;
    }

    foreach ($logs as $log) {
        //Create a temp valid module structure (course,id)
        $tempmod->course = $log->course;
        $tempmod->id = $log->info;
        //Obtain the visible property from the instance
        $modvisible = instance_is_visible($log->module,$tempmod);

        //Only if the mod is visible
        if ($modvisible) {
            $entries[$log->info] = glossary_log_info($log);
            $entries[$log->info]->time = $log->time;
            $entries[$log->info]->url  = $log->url;
        }
    }

    $content = false;
    if ($entries) {
        $strftimerecent = get_string("strftimerecent");
        $content = true;
        print_headline(get_string("newentries", "glossary").":");
        foreach ($entries as $entry) {
            $date = userdate($entry->timemodified, $strftimerecent);
            echo "<p><font size=1>$date - $entry->firstname $entry->lastname<br>";
            echo "\"<a href=\"$CFG->wwwroot/mod/glossary/$entry->url\">";
            echo "$entry->concept";
            echo "</a>\"</font></p>";
        }
    }

    return $content;
}

function glossary_cron () {
/// Function to be run periodically according to the moodle cron
/// This function searches for things that need to be done, such
/// as sending out mail, toggling flags etc ...

    global $CFG;

    return true;
}

function glossary_grades($glossaryid) {
/// Must return an array of grades for a given instance of this module,
/// indexed by user.  It also returns a maximum allowed grade.

    $return->grades = NULL;
    $return->maxgrade = NULL;

    return $return;
}


//////////////////////////////////////////////////////////////////////////////////////
/// Any other glossary functions go here.  Each of them must have a name that
/// starts with glossary_

function glossary_log_info($log) {
    global $CFG;
    return get_record_sql("SELECT g.*, u.firstname, u.lastname
                             FROM {$CFG->prefix}glossary_entries g,
                                  {$CFG->prefix}user u
                            WHERE g.glossaryid = '$log->info'
                              AND u.id = '$log->userid'");
}

function glossary_get_entries($glossaryid, $entrylist) {
    global $CFG;

    return get_records_sql("SELECT id,userid,concept,definition,format
                            FROM {$CFG->prefix}glossary_entries
                            WHERE glossaryid = '$glossaryid'
                            AND id IN ($entrylist)");
}

function glossary_print_entry($course, $cm, $glossary, $entry,$currentview="",$cat="") {
    global $THEME, $USET, $CFG;
    
    $PermissionGranted = 0;
    $formatfile = "$CFG->dirroot/mod/glossary/formats/$glossary->displayformat.php";
    $functionname = "glossary_print_entry_by_format";

    if ( $glossary->displayformat > 0 ) {
        if ( file_exists($formatfile) ) {
           include_once($formatfile);
           if (function_exists($functionname) ) {
              $PermissionGranted = 1;
           }
        }
    } else {
       $PermissionGranted = 1;
    }
    
    if ( $glossary->displayformat > 0 and $PermissionGranted ) {
        glossary_print_entry_by_format($course, $cm, $glossary, $entry,$currentview,$cat);
    } else {
        glossary_print_entry_by_default($course, $cm, $glossary, $entry,$currentview,$cat);
    }

}

function glossary_print_entry_by_default($course, $cm, $glossary, $entry,$currentview="",$cat="") {
    global $THEME, $USER;

    $colour = $THEME->cellheading2;

    echo "\n<TR>";
    echo "<TD WIDTH=100% BGCOLOR=\"#FFFFFF\">";
    if ($entry->attachment) {
          $entry->course = $course->id;
          echo "<table border=0 align=right><tr><td>";
          echo glossary_print_attachments($entry,"html");
          echo "</td></tr></table>";
    }
    echo "<b>$entry->concept</b>: ";
    echo format_text($entry->definition, $entry->format);
    glossary_print_entry_icons($course, $cm, $glossary, $entry,$currentview,$cat);
    echo "</td>";
    echo "</TR>";
}

function glossary_print_entry_icons($course, $cm, $glossary, $entry,$currentview="",$cat="") {
    global $THEME, $USER;

	  if (isteacher($course->id) or $glossary->studentcanpost and $entry->userid == $USER->id) {
 	  	echo "<p align=right>";
		if (isteacher($course->id) and !$glossary->mainglossary) {
			$mainglossary = get_record("glossary","mainglossary",1,"course",$course->id);
			if ( $mainglossary ) {

				echo "<a href=\"exportentry.php?id=$cm->id&entry=$entry->id&currentview=$currentview&cat=$cat\"><img  alt=\"" . get_string("exporttomainglossary","glossary") . "\"src=\"export.gif\" height=11 width=11 border=0></a> ";

			}
		}
		echo "<a href=\"deleteentry.php?id=$cm->id&mode=delete&entry=$entry->id&currentview=$currentview&cat=$cat\"><img  alt=\"" . get_string("delete") . "\"src=\"../../pix/t/delete.gif\" height=11 width=11 border=0></a> ";
	  	echo "<a href=\"edit.php?id=$cm->id&e=$entry->id&currentview=$currentview&cat=$cat\"><img  alt=\"" . get_string("edit") . "\" src=\"../../pix/t/edit.gif\" height=11 width=11 border=0></a>";
	  }
}

function glossary_search_entries($searchterms, $glossary, $includedefinition) {
/// Returns a list of entries found using an array of search terms
/// eg   word  +word -word
///

    global $CFG;

    if (!isteacher($glossary->course)) {
        $glossarymodule = get_record("modules", "name", "glossary");
        $onlyvisible = " AND g.id = cm.instance AND cm.visible = 1 AND cm.module = $glossarymodule->id";
        $onlyvisibletable = ", {$CFG->prefix}course_modules cm";
    } else {

        $onlyvisible = "";
        $onlyvisibletable = "";
    }

    /// Some differences in syntax for PostgreSQL
    if ($CFG->dbtype == "postgres7") {
        $LIKE = "ILIKE";   // case-insensitive
        $NOTLIKE = "NOT ILIKE";   // case-insensitive
        $REGEXP = "~*";
        $NOTREGEXP = "!~*";
    } else {
        $LIKE = "LIKE";
        $NOTLIKE = "NOT LIKE";
        $REGEXP = "REGEXP";
        $NOTREGEXP = "NOT REGEXP";
    }

    $conceptsearch = "";
    $definitionsearch = "";


    foreach ($searchterms as $searchterm) {
        if (strlen($searchterm) < 2) {
            continue;
        }
        if ($conceptsearch) {
            $conceptsearch.= " OR ";
        }
        if ($definitionsearch) {
            $definitionsearch.= " OR ";
        }

        if (substr($searchterm,0,1) == "+") {
            $searchterm = substr($searchterm,1);
            $conceptsearch.= " e.concept $REGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
            $definitionsearch .= " e.definition $REGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
        } else if (substr($searchterm,0,1) == "-") {
            $searchterm = substr($searchterm,1);
            $conceptsearch .= " e.concept $NOTREGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
            $definitionsearch .= " e.definition $NOTREGEXP '(^|[^a-zA-Z0-9])$searchterm([^a-zA-Z0-9]|$)' ";
        } else {
            $conceptsearch .= " e.concept $LIKE '%$searchterm%' ";
            $definitionsearch .= " e.definition $LIKE '%$searchterm%' ";
        }
    }

	if ( !$includedefinition ) {
		$definitionsearch = "0";
	}

    $selectsql = "{$CFG->prefix}glossary_entries e,
                  {$CFG->prefix}glossary g $onlyvisibletable
             WHERE ($conceptsearch OR $definitionsearch)
               AND e.glossaryid = g.id $onlyvisible
		   AND g.id = $glossary->id";

    $totalcount = count_records_sql("SELECT COUNT(*) FROM $selectsql");

	return get_records_sql("SELECT e.concept, e.definition, e.userid, e.timemodified, e.id, e.format  FROM
                            $selectsql ORDER BY e.concept ASC $limit");
}

function glossary_get_participants($glossaryid) {
//Returns the users with data in one glossary
//(users with records in glossary_entries, students)

    global $CFG;

    //Get students
    $students = get_records_sql("SELECT DISTINCT u.*
                                 FROM {$CFG->prefix}user u,
                                      {$CFG->prefix}glossary_entries g
                                 WHERE g.glossaryid = '$glossaryid' and
                                       u.id = g.userid");

    //Return students array (it contains an array of unique users)
    return ($students);
}


function glossary_file_area_name($entry) {
//  Creates a directory file name, suitable for make_upload_directory()
    global $CFG;

    return "$entry->course/$CFG->moddata/glossary/$entry->glossaryid/$entry->id";
}

function glossary_file_area($entry) {
    return make_upload_directory( glossary_file_area_name($entry) );
}

function glossary_delete_old_attachments($entry, $exception="") {
// Deletes all the user files in the attachments area for a entry
// EXCEPT for any file named $exception

    if ($basedir = glossary_file_area($entry)) {
        if ($files = get_directory_list($basedir)) {
            foreach ($files as $file) {
                if ($file != $exception) {
                    unlink("$basedir/$file");
//                    notify("Existing file '$file' has been deleted!");
                }
            }
        }
        if (!$exception) {  // Delete directory as well, if empty
            rmdir("$basedir");
        }
    }
}

function glossary_copy_attachments($entry, $newentry) {
/// Given a entry object that is being copied to glossaryid,
/// this function checks that entry
/// for attachments, and if any are found, these are
/// copied to the new glossary directory.

    global $CFG;

    $return = true;

    if ($entries = get_records_select("glossary_entries", "id = '$entry->id' AND attachment <> ''")) {
        foreach ($entries as $curentry) {
            $oldentry->id = $entry->id;
            $oldentry->course = $entry->course;
            $oldentry->glossaryid = $curentry->glossaryid;
            $oldentrydir = "$CFG->dataroot/".glossary_file_area_name($oldentry);
            if (is_dir($oldentrydir)) {

                $newentrydir = glossary_file_area($newentry);
                if (! copy("$oldentrydir/$newentry->attachment", "$newentrydir/$newentry->attachment")) {
                    $return = false;
                }
            }
        }
     }
    return $return;
}

function glossary_move_attachments($entry, $glossaryid) {
/// Given a entry object that is being moved to glossaryid,
/// this function checks that entry
/// for attachments, and if any are found, these are
/// moved to the new glossary directory.

    global $CFG;

    $return = true;

    if ($entries = get_records_select("glossary_entries", "glossaryid = '$entry->id' AND attachment <> ''")) {
        foreach ($entries as $entry) {
            $oldentry->course = $entry->course;
            $oldentry->glossaryid = $entry->glossaryid;
            $oldentrydir = "$CFG->dataroot/".glossary_file_area_name($oldentry);
            if (is_dir($oldentrydir)) {
                $newentry = $oldentry;
                $newentry->glossaryid = $glossaryid;
                $newentrydir = "$CFG->dataroot/".glossary_file_area_name($newentry);
                if (! @rename($oldentrydir, $newentrydir)) {
                    $return = false;
                }
            }
        }
    }
    return $return;
}

function glossary_add_attachment($entry, $newfile) {
// $entry is a full entry record, including course and glossary
// $newfile is a full upload array from $_FILES
// If successful, this function returns the name of the file

    global $CFG;

    if (empty($newfile['name'])) {
        return "";
    }

    $newfile_name = clean_filename($newfile['name']);

    if (valid_uploaded_file($newfile)) {
        if (! $newfile_name) {
            notify("This file had a wierd filename and couldn't be uploaded");

        } else if (! $dir = glossary_file_area($entry)) {
            notify("Attachment could not be stored");
            $newfile_name = "";

        } else {
            if (move_uploaded_file($newfile['tmp_name'], "$dir/$newfile_name")) {
                chmod("$dir/$newfile_name", $CFG->directorypermissions);
                glossary_delete_old_attachments($entry, $newfile_name);
            } else {
                notify("An error happened while saving the file on the server");
                $newfile_name = "";
            }
        }
    } else {
        $newfile_name = "";
    }

    return $newfile_name;
}

function glossary_print_attachments($entry, $return=NULL) {
// if return=html, then return a html string.
// if return=text, then return a text-only string.
// otherwise, print HTML for non-images, and return image HTML

    global $CFG;

    $filearea = glossary_file_area_name($entry);

    $imagereturn = "";
    $output = "";

    if ($basedir = glossary_file_area($entry)) {
        if ($files = get_directory_list($basedir)) {
            $strattachment = get_string("attachment", "glossary");
            $strpopupwindow = get_string("popupwindow");
            foreach ($files as $file) {
                $icon = mimeinfo("icon", $file);
                if ($CFG->slasharguments) {
                    $ffurl = "file.php/$filearea/$file";
                } else {
                    $ffurl = "file.php?file=/$filearea/$file";
                }
                $image = "<img border=0 src=\"$CFG->wwwroot/files/pix/$icon\" height=16 width=16 alt=\"$strpopupwindow\">";

                if ($return == "html") {
                    $output .= "<a target=_image href=\"$CFG->wwwroot/$ffurl\">$image</a> ";
                    $output .= "<a target=_image href=\"$CFG->wwwroot/$ffurl\">$file</a><br />";
                } else if ($return == "text") {
                    $output .= "$strattachment $file:\n$CFG->wwwroot/$ffurl\n";

                } else {
                    if ($icon == "image.gif") {    // Image attachments don't get printed as links
                        $imagereturn .= "<br /><img src=\"$CFG->wwwroot/$ffurl\">";
                    } else {
                        link_to_popup_window("/$ffurl", "attachment", $image, 500, 500, $strattachment);
                        echo "<a target=_image href=\"$CFG->wwwroot/$ffurl\">$file</a>";
                        echo "<br />";
                    }
                }
            }
        }
    }

    if ($return) {
        return $output;
    }

    return $imagereturn;
}

function print_tabbed_table_start($data, $CurrentTab, $tTHEME = NULL) {

if ( !$tTHEME ) {
     global $THEME;
     $tTHEME = $THEME;
}

$TableColor           = $tTHEME->TabTableBGColor;
$TableWidth           = $tTHEME->TabTableWidth;
$CurrentTabColor      = $tTHEME->ActiveTabColor;
$TabColor             = $tTHEME->InactiveTabColor;
$TabsPerRow           = $tTHEME->TabsPerRow;
$TabSeparation        = $tTHEME->TabSeparation;

$Tabs                 = count($data);
$TabWidth             = (int) (100 / $TabsPerRow);

$CurrentRow           = ( $CurrentTab - ( $CurrentTab % $TabsPerRow) ) / $TabsPerRow;

$NumRows              = (int) ( $Tabs / $TabsPerRow ) + 1;

?>
  <center>
  <table border="0" cellpadding="0" cellspacing="0" width="<? p($TableWidth) ?>">
    <tr>
      <td width="100%">

      <table border="0" cellpadding="0" cellspacing="0" width="100%">

<?
$TabProccessed = 0;
for ($row = 0; $row < $NumRows; $row++) {
     echo "<tr>\n";
     if ( $row != $CurrentRow ) {
          for ($col = 0; $col < $TabsPerRow; $col++) {
               if ( $TabProccessed < $Tabs ) {
                    if ($TabProccessed == $CurrentTab) {
                         $CurrentColor = $CurrentTabColor;
                    } else {
                         $CurrentColor = $TabColor;
                    }
                    ?>
                    <td width="<? p($TabWidth) ?>%" bgcolor="<? p($CurrentColor) ?>" align="center">
                    <b><a href="<? p($data[$TabProccessed]->link) ?>"><? p($data[$TabProccessed]->caption) ?></a></b></td>
                    <? if ($col < $TabsPerRow) { ?>
                         <td width="<? p($TabSeparation) ?>" align="center">&nbsp;</td>
                    <? }
               } else {
                    $CurrentColor = "";
               }
               $TabProccessed++;
          }
     } else {
          $FirstTabInCurrentRow = $TabProccessed;
          $TabProccessed += $TabsPerRow;
     }
     echo "</tr><tr><td colspan=" . (2* $TabsPerRow) . " ></td></tr>\n";
}
     echo "<tr>\n";
          $TabProccessed = $FirstTabInCurrentRow;
          for ($col = 0; $col < $TabsPerRow; $col++) {
               if ( $TabProccessed < $Tabs ) {
                    if ($TabProccessed == $CurrentTab) {
                         $CurrentColor = $CurrentTabColor;
                    } else {
                         $CurrentColor = $TabColor;
                    }
                    ?>
                    <td width="<? p($TabWidth) ?>%" bgcolor="<? p($CurrentColor) ?>" align="center">
                    <b><a href="<? p($data[$TabProccessed]->link) ?>"><? p($data[$TabProccessed]->caption) ?></a></b></td>
                    <? if ($col < $TabsPerRow) { ?>
                         <td width="<? p($TabSeparation) ?>" align="center">&nbsp;</td>
                    <? }
               } else {
                    if ($NumRows > 1) {
                         $CurrentColor = $TabColor;
                    } else {
                         $CurrentColor = "";
                    }
                    echo "<td colspan = " . (2 * ($TabsPerRow - $col)) . " bgcolor=\"$CurrentColor\" align=\"center\">";
                    echo "</td>";

                    $col = $TabsPerRow;
               }
               $TabProccessed++;
          }
     echo "</tr>\n";
     ?>

      </table>
      </td>
    </tr>
    <tr>
      <td width="100%" bgcolor="<? p($TableColor) ?>"><hr></td>
    </tr>
    <tr>
      <td width="100%" bgcolor="<? p($TableColor) ?>">
          <center>
<?
}

function print_tabbed_table_end() {
     echo "</center><p></td></tr></table></center>";
}

function glossary_print_alphabet_menu($cm, $glossary, $l) {
global $CFG, $THEME;
     $strselectletter = get_string("selectletter", "glossary");
     $strspecial      = get_string("special", "glossary");
     $strallentries   = get_string("allentries", "glossary");

     echo "<CENTER>$strselectletter";

      if ( $glossary->showspecial ) {
          if ( $l == "SPECIAL" ) {
               echo "<p><b>$strspecial</b> | ";
          } else {
               echo "<p><a href=\"$CFG->wwwroot/mod/glossary/view.php?id=$cm->id&l=SPECIAL\">$strspecial</a> | ";
          }
      }

      if ( $glossary->showalphabet ) {
           $alphabet = explode("|", get_string("alphabet","glossary"));
           $letters_by_line = 14;
           for ($i = 0; $i < count($alphabet); $i++) {
               if ( $l == $alphabet[$i] ) {
                    echo "<b>$alphabet[$i]</b>";
               } else {
                    echo "<a href=\"$CFG->wwwroot/mod/glossary/view.php?id=$cm->id&l=$alphabet[$i]\">$alphabet[$i]</a>";
               }
               if ((int) ($i % $letters_by_line) != 0 or $i == 0) {
                    echo " | ";
               } else {
                    echo "<br>";
               }
           }
      }

      if ( $glossary->showall ) {
          if ( $l == "ALL" ) {
               echo "<b>$strallentries</b></p>";
          } else {
               echo "<a href=\"$CFG->wwwroot/mod/glossary/view.php?id=$cm->id&l=ALL\">$strallentries</a></p>";
          }
      }
}
function glossary_print_categories_menu($course, $cm, $glossary, $category) {
global $CFG, $THEME;
     echo "<table border=0 width=100%>";
     echo "<tr>";

     echo "<td align=center width=20%>";
     if ( isteacher($course->id) ) {
             $options['id'] = $cm->id;
             $options['cat'] = $cat;
             echo print_single_button("editcategories.php", $options, get_string("editcategories","glossary"), "get");
     }
     echo "</td>";

     echo "<td align=center width=60%>";
     echo "<b>";
     if ( $category ) {
        echo $category->name;
     } else {
        echo get_string("entrieswithoutcategory","glossary");
     }
     echo "</b></td>";
     echo "<td align=center width=20%>";
     $menu[0] = get_string("nocategorized","glossary");

     $categories = get_records("glossary_categories", "glossaryid", $glossary->id, "name ASC");
     if ( $categories ) {
          foreach ($categories as $currentcategory) {
                 $url = $currentcategory->id;
                 if ($currentcategory->id == $category->id) {
                     $selected = $url;
                 }
                 $menu[$url] = $currentcategory->name;
          }
     }

     echo popup_form("$CFG->wwwroot/mod/glossary/view.php?id=$cm->id&currentview=categories&cat=", $menu, "catmenu", $selected, get_string("jumpto"),
                      "", "", false);

     echo "</td>";
     echo "</tr>";

     echo "<tr><td colspan=3><hr></td></tr>";
     echo "</table>";
}
?>
