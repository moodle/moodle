<?php // $Id$

/**
 * This script enables Moodle translators to edit /docs and /help language
 * files directly via WWW interface.
 *
 * Author:     mudrd8mz@uxit.pedf.cuni.cz (http://moodle.cz)
 * Based on:   lang.php in 1.4.3+ release
 * Thanks:     Jaime Villate for important bug fixing, koen roggemans for his job and all moodlers
 *             for intensive testing of this my first contribution
 */
    require_once('../config.php');
    require_once($CFG->libdir.'/adminlib.php');
    admin_externalpage_setup('langedit');

    //
    // Some local configuration
    //
    $fileeditorrows = 12;           // number of textareas' rows
    $fileeditorcols = 100;          // dtto cols
    $fileeditorinline = 1;          // shall be textareas put in one row?
    $filemissingmark = ' (***)';    // mark to add to non-existing filenames in selection form
    $fileoldmark = ' (old?)';       // mark to add to filenames in selection form id english version is newer
                                    // or to filenames with filesize() == 0
    $filetemplate = '';             // template for new files, i.e. '$Id$';

    $currentfile = optional_param('currentfile', 'docs/README.txt', PARAM_PATH); 

    $strlanguage = get_string("language");
    $strcurrentlanguage = get_string("currentlanguage");
    $strthislanguage = get_string("thislanguage");
    $stredithelpdocs = get_string('edithelpdocs', 'admin');

    admin_externalpage_print_header();

    notify('NOTICE: This interface is obsolete now and will be removed. You should use 
        improved <a href="lang.php?mode=helpfiles">lang.php</a> interface.');

    $currentlang = current_language();
    $langdir = "$CFG->dataroot/lang/$currentlang";
    $enlangdir = "$CFG->dirroot/lang/en_utf8";


    if (!file_exists($langdir)) {
        error ('to edit this language pack, you need to put it in '.$CFG->dataroot.'/lang');
    }
    // Shall I save POSTed data?

    if (isset($_POST['currentfile'])) {
        if (confirm_sesskey()) {
            if (langdoc_save_file($langdir, $currentfile, $_POST['filedata'])) {
                notify(get_string("changessaved")." ($langdir/$currentfile)", "green");
            } else {
                error("Could not save the file '$currentfile'!", "langdoc.php?currentfile=$currentfile&sesskey=$USER->sesskey");
            }
        }
    }

    error_reporting(0); // Error reporting turned off due to non-existing files

    // Generate selection for all help and documentation files

    // Get all files from /docs directory

    if (! $files = get_directory_list("$CFG->dirroot/lang/en_utf8/docs", "CVS")) {
        error("Could not find English language docs files!");
    }

    $options = array();

    foreach ($files as $filekey => $file) {    // check all the docs files.
        $options["docs/$file"] = "docs/$file";
        // add mark if file doesn't exist or is empty
        if (( !file_exists("$langdir/docs/$file")) || (filesize("$langdir/docs/$file") == 0)) {
            $options["docs/$file"] .= "$filemissingmark";
        } else {       
            if (filemtime("$langdir/docs/$file") < filemtime("$CFG->dirroot/lang/en_utf8/docs/$file")) {
                $options["docs/$file"] .= "$fileoldmark";
            }
        }    
    }

    // Get all files from /help directory

    if (! $files = get_directory_list("$CFG->dirroot/lang/en_utf8/help", "CVS")) {
        error("Could not find English language help files!");
    }

    foreach ($files as $filekey => $file) {    // check all the help files.
        $options["help/$file"] = "help/$file";
        if (( !file_exists("$langdir/help/$file")) || (filesize("$CFG->dirroot/lang/en_utf8/help/$file") == 0)) {
            $options["help/$file"] .= "$filemissingmark";
        } else {
            if (filemtime("$langdir/help/$file") < filemtime("$langdir/help/$file")) {
                $options["help/$file"] .= "$fileoldmark";
            }
        }    
    }

    echo "<table align=\"center\"><tr><td align=\"center\">";
    echo popup_form ("$CFG->wwwroot/$CFG->admin/langdoc.php?sesskey=$USER->sesskey&amp;currentfile=", $options, "choosefile", $currentfile, "", "", "", true);
    echo "</td></tr></table>";

    // Generate textareas

    if (!empty($currentfile)) {

        if (!file_exists("$langdir/$currentfile")) {
            //check if directory exist
            $pathparts = explode('/',$currentfile);
            $checkpath = $langdir;
            for ($a=0; $a < count($pathparts)-1 ; $a++) {
                $checkpath .= "/".$pathparts[$a];
                if(!file_exists($checkpath)){
                     if(!mkdir($checkpath, $CFG->directorypermissions)){
                         echo ("Cannot create directory: $checkpath");
                     }    
                }    
            }
            //
            // file doesn't exist - let's check webserver's permission to create it
            //
            if (!touch("$langdir/$currentfile")) {
                //
                // webserver is unable to create new file
                //
                echo "<p align=\"center\"><font color=red>".get_string("filemissing", "", "
$langdir/$currentfile")."</font></p>";
                $editable = false;
            } else {
                //
                // webserver can create new file - we can delete it now and let
                // the langdoc_save_file() create it again if its filesize() > 0
                //
                $editable = true;
                unlink("$langdir/$currentfile");
            }
        } elseif ($f = fopen("$langdir/$currentfile","r+")) {
            //
            // file exists and is writeable - good for you, translator ;-)
            //
            $editable = true;
            fclose($f);
        } else {
            //
            // file exists but it is not writeable by web server process :-(
            //
            $editable = false;
            echo "<p><font size=1>".get_string("makeeditable", "", "$langdir/$currentfile")
."</font></p>";
        }

        //en_utf8 in dataroot is not editable
        if ($currentlang == 'en_utf8') {
            $editable = false;
        }

        echo "<table align=\"center\"><tr valign=\"center\"><td align=\"center\">\n";
        echo "<textarea rows=\"$fileeditorrows\" cols=\"$fileeditorcols\" name=\"\">\n";
        echo htmlspecialchars(file_get_contents("$enlangdir/$currentfile"));
        echo "</textarea>\n";
        //link_to_popup_window("/lang/en_utf8/$currentfile", "popup", get_string("preview"));
        $preview_url = langdoc_preview_url($currentfile);
        if ($preview_url) {
            link_to_popup_window($preview_url.'&amp;forcelang=en_utf8', 'popup', get_string('preview'));
        }
        echo "</td>\n";
        if ($fileeditorinline == 1) {
            echo "</tr>\n<tr valign=\"center\">\n";
        }
        echo "<td align=\"center\">\n";

        if ($editable) {
            echo "<form id=\"$currentfile\" action=\"langdoc.php\" method=\"post\">";
            echo '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
            echo '<input type="hidden" name="currentfile" value="'.$currentfile.'" />';

            echo "<textarea rows=\"$fileeditorrows\" cols=\"$fileeditorcols\" name=\"filedata\">\n";
            if (file_exists("$langdir/$currentfile")) {
                echo htmlspecialchars(file_get_contents("$langdir/$currentfile"));
            } else {
                echo ($filetemplate);
            }
            echo "</textarea>\n";
            $preview_url = langdoc_preview_url($currentfile);
            if ($preview_url) {
                link_to_popup_window($preview_url, 'popup', get_string('preview'));
            }
            echo '<div class="mdl-align"><input type="submit" value="'.get_string('savechanges').': lang/'.$currentlang.'/'.$currentfile.'" /></div>';
            echo '</form>';
        }

        echo "</td>\n</tr>\n</table>";


        error_reporting($CFG->debug);
    }

    admin_externalpage_print_footer();

//////////////////////////////////////////////////////////////////////

function langdoc_save_file($path, $file, $content) {

// $path is a full pathname to the file
// $file is the file to overwrite.
// $content are data to write

    global $CFG, $USER;

    error_reporting(0);
    
    if (!$f = fopen("$path/$file","w")) {
        error_reporting($CFG->debug);
        return false;
    }
    
    error_reporting($CFG->debug);

    $content = str_replace("\r", "",$content);              // Remove linefeed characters
    $content = preg_replace("/\n{3,}/", "\n\n", $content);  // Collapse runs of blank lines
    $content = trim($content);                              // Delete leading/trailing whitespace
        
    fwrite($f, stripslashes($content));

    fclose($f);

    // Remove file if its empty

    if (filesize("$path/$file") == 0) {
        unlink("$path/$file");
    }

    return true;
}

/**
 * Return a preview URL for the file, if available.
 *
 * Documentation will be moved into moodle.org wiki and current version 1.6 does not
 * seem to be able to display local documentation. Thus, return empty URL for doc files.
 * See lib/moodlelib.php document_file() - it still relies on old pre-UTF8 lang/ location.
 */
function langdoc_preview_url($currentfile) {
    if (substr($currentfile, 0, 5) == 'help/') {
        $currentfile = substr($currentfile, 5);
        $currentpathexp = explode('/', $currentfile);
        if (count($currentpathexp) > 1) {
            $url = '/help.php?module='.implode('/',array_slice($currentpathexp,0,count($currentpathexp)-1)).'&amp;file='.end($currentpathexp); 
        } else {
            $url = '/help.php?module=moodle&amp;file='.$currentfile;
        }
    } else {
        $url = '';
    }
    return $url;
}

?>
