<?PHP // $Id$

//  Manage all uploaded files in a course file area

//  All the Moodle-specific stuff is in this top section
//  Configuration and access control occurs here.
//  Must define:  USER, basedir, baseweb, html_header and html_footer
//  USER is a persistent variable using sessions

    require("../config.php");

    require_variable($id);

    if (! $course = get_record("course", "id", $id) ) {
        error("That's an invalid course id");
    }

    require_login($course->id);

    if (! isteacher($course->id) ) {
        error("Only teachers can edit files");
    }

    function html_footer() {
        global $course;
        echo "</td></tr></table></body></html>";
        print_footer($course);
    }
    
    function html_header($formfield=""){
        global $course;
    
        print_header("$course->shortname: Files", "$course->shortname: Files", 
                    "<A HREF=\"../course/view.php?id=$course->id\">$course->shortname</A> -> Files", $formfield);
        echo "<table border=0 align=center cellspacing=3 cellpadding=3 width=640>";
        echo "<tr>";
        echo "<td colspan=\"2\">";
    }

    if (! file_exists($CFG->dataroot)) {
        if (! mkdir($CFG->dataroot, 0750)) {
            error("You need to create the directory $CFG->dataroot with web server write access");
        }
    }
    $basedir = "$CFG->dataroot/$course->id";

    if (! file_exists($basedir)) {
        if (! mkdir($basedir, 0750)) {
            error("Could not create a directory for this course ($basedir)");
        }
    }
    $baseweb = $CFG->wwwroot;

//  End of configuration and access control


    require("mimetypes.php");

    $regexp="\\.\\.";
    if (ereg( $regexp, $file, $regs )| ereg( $regexp, $wdir,$regs )) {           
        $message = "Error: Directories can not contain \"..\"";
        $wdir = "/";
        $action = "";
    }    


    if (!match_referer("$baseweb/files/index.php")) {   // To stop spoofing 
        $action="cancel";
        $wdir="/";
    }

    if (!$wdir) {
        $wdir="/";
    }



    switch ($action) {

        case "upload":
            html_header();
            if ($save) {
                if ($userfile == "none" || $userfile_size==0) {
                    echo "<P>Error: That was not a valid file.";
                } else {
                    $userfile_name = clean_filename($userfile_name);
                    if ($userfile_name != "") {
                        $newfile = "$basedir$wdir/$userfile_name";
                        copy ($userfile, $newfile); 
                        chmod ($newfile, 0750);
                        echo "Uploaded $userfile_name ($userfile_type) to $wdir";
                    }
                }
                displaydir($wdir);
                    
            } else {
                echo "<P>Upload a file into <B>$wdir</B>:";
                echo "<TABLE><TR><TD COLSPAN=2>";
                echo "<FORM ENCTYPE=\"multipart/form-data\" METHOD=\"post\" ACTION=index.php>";
                echo " <INPUT TYPE=hidden NAME=MAX_FILE_SIZE value=5000000>";
                echo " <INPUT TYPE=hidden NAME=id VALUE=$id>";
                echo " <INPUT TYPE=hidden NAME=wdir VALUE=$wdir>";
                echo " <INPUT TYPE=hidden NAME=action VALUE=upload>";
                echo " <INPUT NAME=\"userfile\" TYPE=\"file\" size=\"50\">";
                echo " </TD><TR><TD WIDTH=10>";
                echo " <INPUT TYPE=submit NAME=save VALUE=\"Upload this file\">";
                echo "</FORM>";
                echo "</TD><TD WIDTH=100%>";
                echo "<FORM ACTION=index.php METHOD=get>";
                echo " <INPUT TYPE=hidden NAME=id VALUE=$id>";
                echo " <INPUT TYPE=hidden NAME=wdir VALUE=$wdir>";
                echo " <INPUT TYPE=hidden NAME=action VALUE=cancel>";
                echo " <INPUT TYPE=submit VALUE=\"Cancel\">";
                echo "</FORM>";
                echo "</TD></TR></TABLE>";
            }
            html_footer();
            break;

        case "delete":
            if ($confirm) {
                html_header();
                foreach ($USER->filelist as $file) {
                    $fullfile = $basedir.$file;
                    if (! fulldelete($fullfile)) {
                        echo "<BR>Error: Could not delete: $fullfile";
                    }
                }
                clearfilelist();
                displaydir($wdir);
                html_footer();

            } else {
                html_header();
                if (setfilelist($HTTP_POST_VARS)) {
                    echo "<P ALIGN=CENTER>You are about to delete:</P>";
                    print_simple_box_start("center");
                    printfilelist($USER->filelist);
                    print_simple_box_end();
                    echo "<BR>";
                    notice_yesno ("Are you sure you want to delete these?", 
                                "index.php?id=$id&wdir=$wdir&action=delete&confirm=1",
                                "index.php?id=$id&wdir=$wdir&action=cancel");
                } else {
                    displaydir($wdir);
                }
                html_footer();
            }
            break;

        case "move":
            html_header();
            if ($count = setfilelist($HTTP_POST_VARS)) {
                $USER->fileop     = $action;
                $USER->filesource = $wdir;
                echo "<P align=center>$count files selected for moving. Now go to the destination and press \"Move files to here\".</P>";
            }
            displaydir($wdir);
            html_footer();
            break;

        case "paste":
            html_header();
            if ($USER->fileop == "move") {
                foreach ($USER->filelist as $file) {
                    $shortfile = basename($file);
                    $oldfile = $basedir.$file;
                    $newfile = $basedir.$wdir."/".$shortfile;
                    if (!rename($oldfile, $newfile)) {
                        echo "<P>Error: $shortfile not moved";
                    }
                }
            }
            clearfilelist();
            displaydir($wdir);
            html_footer();
            break;

        case "rename":
            if ($name) {
                html_header();
                $name = clean_filename($name);
                if (file_exists($basedir.$wdir."/".$name)) {
                    echo "Error: $name already exists!";
                } else if (!rename($basedir.$wdir."/".$oldname, $basedir.$wdir."/".$name)) {
                    echo "Error: could not rename $oldname to $name";
                }
                displaydir($wdir);
                    
            } else {
                html_header("form.name");
                echo "<P>Rename <B>$file</B> to:";
                echo "<TABLE><TR><TD>";
                echo "<FORM ACTION=index.php METHOD=post NAME=form>";
                echo " <INPUT TYPE=hidden NAME=id VALUE=$id>";
                echo " <INPUT TYPE=hidden NAME=wdir VALUE=$wdir>";
                echo " <INPUT TYPE=hidden NAME=action VALUE=rename>";
                echo " <INPUT TYPE=hidden NAME=oldname VALUE=\"$file\">";
                echo " <INPUT TYPE=text NAME=name SIZE=35 VALUE=\"$file\">";
                echo " <INPUT TYPE=submit VALUE=\"Rename\">";
                echo "</FORM>";
                echo "</TD><TD>";
                echo "<FORM ACTION=index.php METHOD=get>";
                echo " <INPUT TYPE=hidden NAME=id VALUE=$id>";
                echo " <INPUT TYPE=hidden NAME=wdir VALUE=$wdir>";
                echo " <INPUT TYPE=hidden NAME=action VALUE=cancel>";
                echo " <INPUT TYPE=submit VALUE=\"Cancel\">";
                echo "</FORM>";
                echo "</TD></TR></TABLE>";
            }
            html_footer();
            break;

        case "mkdir":
            if ($name) {
                html_header();
                $name = clean_filename($name);
                if (file_exists($basedir.$wdir."/".$name)) {
                    echo "Error: $name already exists!";
                } else if (!mkdir($basedir.$wdir."/".$name, 0750)) {
                    echo "Error: could not create $name";
                }
                displaydir($wdir);
                    
            } else {
                html_header("form.name");
                echo "<P>Create folder in $wdir:";
                echo "<TABLE><TR><TD>";
                echo "<FORM ACTION=index.php METHOD=post NAME=form>";
                echo " <INPUT TYPE=hidden NAME=id VALUE=$id>";
                echo " <INPUT TYPE=hidden NAME=wdir VALUE=$wdir>";
                echo " <INPUT TYPE=hidden NAME=action VALUE=mkdir>";
                echo " <INPUT TYPE=text NAME=name SIZE=35>";
                echo " <INPUT TYPE=submit VALUE=\"Create\">";
                echo "</FORM>";
                echo "</TD><TD>";
                echo "<FORM ACTION=index.php METHOD=get>";
                echo " <INPUT TYPE=hidden NAME=id VALUE=$id>";
                echo " <INPUT TYPE=hidden NAME=wdir VALUE=$wdir>";
                echo " <INPUT TYPE=hidden NAME=action VALUE=cancel>";
                echo " <INPUT TYPE=submit VALUE=\"Cancel\">";
                echo "</FORM>";
                echo "</TD></TR></TABLE>";
            }
            html_footer();
            break;

        case "edit":
            html_header();
            if (isset($text)) {
                $fileptr = fopen($basedir.$file,"w");
                fputs($fileptr, stripslashes($text));
                fclose($fileptr);
                displaydir($wdir);
                    
            } else {
                $fileptr  = fopen($basedir.$file, "r");
                $contents = fread($fileptr, filesize($basedir.$file));
                fclose($fileptr);

                echo "<P>Editing <B>$file</B>:";
                echo "<TABLE><TR><TD COLSPAN=2>";
                echo "<FORM ACTION=index.php METHOD=post NAME=form>";
                echo " <INPUT TYPE=hidden NAME=id VALUE=$id>";
                echo " <INPUT TYPE=hidden NAME=wdir VALUE=\"$wdir\">";
                echo " <INPUT TYPE=hidden NAME=file VALUE=\"$file\">";
                echo " <INPUT TYPE=hidden NAME=action VALUE=edit>";
                echo "<TEXTAREA ROWS=20 COLS=60 NAME=text>";
                echo htmlspecialchars($contents);
                echo "</TEXTAREA>";
                echo "</TD></TR><TR><TD>";
                echo " <INPUT TYPE=submit VALUE=\"Save changes\">";
                echo "</FORM>";
                echo "</TD><TD>";
                echo "<FORM ACTION=index.php METHOD=get>";
                echo " <INPUT TYPE=hidden NAME=id VALUE=$id>";
                echo " <INPUT TYPE=hidden NAME=wdir VALUE=$wdir>";
                echo " <INPUT TYPE=hidden NAME=action VALUE=cancel>";
                echo " <INPUT TYPE=submit VALUE=\"Cancel\">";
                echo "</FORM>";
                echo "</TD></TR></TABLE>";
            }
            html_footer();
            break;

        case "zip":
            if ($name) {
                html_header();
                $name = clean_filename($name);
                $files = "";
                foreach ($USER->filelist as $file) {
                    $files .= basename($file);
                    $files .= " ";
                }
                $command = "cd $basedir/$wdir ; /usr/bin/zip -r $name $files";
                Exec($command);
                clearfilelist();
                displaydir($wdir);
                    
            } else {
                html_header("form.name");
                if (setfilelist($HTTP_POST_VARS)) {
                    echo "<P ALIGN=CENTER>You are about create a zip file containing:</P>";
                    print_simple_box_start("center");
                    printfilelist($USER->filelist);
                    print_simple_box_end();
                    echo "<BR>";
                    echo "<P ALIGN=CENTER>What do you want to call the zip file?";
                    echo "<TABLE><TR><TD>";
                    echo "<FORM ACTION=index.php METHOD=post NAME=form>";
                    echo " <INPUT TYPE=hidden NAME=id VALUE=$id>";
                    echo " <INPUT TYPE=hidden NAME=wdir VALUE=\"$wdir\">";
                    echo " <INPUT TYPE=hidden NAME=action VALUE=zip>";
                    echo " <INPUT TYPE=text NAME=name SIZE=35 VALUE=\"new.zip\">";
                    echo " <INPUT TYPE=submit VALUE=\"Create zip file\">";
                    echo "</FORM>";
                    echo "</TD><TD>";
                    echo "<FORM ACTION=index.php METHOD=get>";
                    echo " <INPUT TYPE=hidden NAME=id VALUE=$id>";
                    echo " <INPUT TYPE=hidden NAME=wdir VALUE=$wdir>";
                    echo " <INPUT TYPE=hidden NAME=action VALUE=cancel>";
                    echo " <INPUT TYPE=submit VALUE=\"Cancel\">";
                    echo "</FORM>";
                    echo "</TD></TR></TABLE>";
                } else {
                    displaydir($wdir);
                    clearfilelist();
                }
            }
            html_footer();
            break;

        case "unzip":
            html_header();
            if ($file) {
                echo "<P ALIGN=CENTER>Unzipping $file:</P>";
                print_simple_box_start("center");
                echo "<PRE>";
                $file = basename($file);
                $command = "cd $basedir/$wdir ; /usr/bin/unzip -o $file 2>&1";
                passthru($command);
                echo "</PRE>";
                print_simple_box_end();
                echo "<CENTER><FORM ACTION=index.php METHOD=get>";
                echo " <INPUT TYPE=hidden NAME=id VALUE=$id>";
                echo " <INPUT TYPE=hidden NAME=wdir VALUE=$wdir>";
                echo " <INPUT TYPE=hidden NAME=action VALUE=cancel>";
                echo " <INPUT TYPE=submit VALUE=\"OK\">";
                echo "</FORM>";
                echo "</CENTER>";
            } else {
                displaydir($wdir);
            }
            html_footer();
            break;

        case "cancel";
            clearfilelist();

        default:
            html_header();
            displaydir($wdir);
            html_footer();
            break;
}


/// FILE FUNCTIONS ///////////////////////////////////////////////////////////


function fulldelete($location) { 
    if (is_dir($location)) {
        $currdir = opendir($location);
        while ($file = readdir($currdir)) { 
            if ($file <> ".." && $file <> ".") {
                $fullfile = $location."/".$file;
                if (is_dir($fullfile)) { 
                    if (!fulldelete($fullfile)) {
                        return false;
                    }
                } else {
                    if (!unlink($fullfile)) {
                        return false;
                    }
                } 
            }
        } 
        closedir($currdir);
        if (! rmdir($location)) {
            return false;
        }

    } else {
        if (!unlink($location)) {
            return false;
        }
    }
    return true;
}

function clean_filename($string) {
    $string = eregi_replace("\.\.", "", $string);
    $string = eregi_replace("[^([:alnum:]|\.)]", "_", $string);
    return    eregi_replace("_+", "_", $string);
}



function setfilelist($VARS) {
    global $USER;

    $USER->filelist = array ();
    $USER->fileop = "";

    $count = 0;
    foreach ($VARS as $key => $val) {
        if (substr($key,0,4) == "file") {
            $count++;
            $USER->filelist[] = rawurldecode($val);
        }
    }

    return $count;
}

function clearfilelist() {
    global $USER;

    $USER->filelist = array ();
    $USER->fileop = "";
}


function printfilelist($filelist) {
    global $basedir;

    foreach ($filelist as $file) {
        if (is_dir($basedir.$file)) {
            echo "<IMG SRC=\"pix/folder.gif\" HEIGHT=16 WIDTH=16> $file<BR>";
            $subfilelist = array();
            $currdir = opendir($basedir.$file);
            while ($subfile = readdir($currdir)) { 
                if ($subfile <> ".." && $subfile <> ".") {
                    $subfilelist[] = $file."/".$subfile;
                }
            }
            printfilelist($subfilelist);

        } else { 
            $icon = mimeinfo("icon", $file);
            echo "<IMG SRC=\"pix/$icon\"  HEIGHT=16 WIDTH=16> $file<BR>";
        }
    }
}


function display_size($file) {
    $file_size = filesize($file);
    if ($file_size >= 1073741824) {
        $file_size = round($file_size / 1073741824 * 100) / 100 . "g";
    } else if ($file_size >= 1048576) {
        $file_size = round($file_size / 1048576 * 100) / 100 . "m";
    } else if ($file_size >= 1024) {
        $file_size = round($file_size / 1024 * 100) / 100 . "k";
    } else { 
        $file_size = $file_size . "b";
    }
    return $file_size;
}


function print_cell($alignment="center", $text="&nbsp;") {
    echo "<TD ALIGN=\"$alignment\" NOWRAP>";
    echo "<FONT SIZE=\"-1\" FACE=\"Arial, Helvetica\">";
    echo "$text";
    echo "</FONT>";
    echo "</TD>\n";
}

function displaydir ($wdir) {
//  $wdir == / or /a or /a/b/c/d  etc

    global $basedir;
    global $id;
    global $USER;

    $fullpath = $basedir.$wdir;

    $directory = opendir($fullpath);             // Find all files
    while ($file = readdir($directory)) {
        if ($file == "." || $file == "..") {
            continue;
        }
        
        if (is_dir($fullpath."/".$file)) {
            $dirlist[] = $file;
        } else {
            $filelist[] = $file;
        }
    }
    closedir($directory);


    echo "<FORM ACTION=\"index.php\" METHOD=post NAME=dirform>";
    echo "<HR WIDTH=640 ALIGN=CENTER NOSHADE SIZE=1>";
    echo "<TABLE BORDER=0 cellspacing=2 cellpadding=2 width=640>";    
    echo "<TR><TD>&nbsp;</TD><TD COLSPAN=5><P><B>Current folder: $wdir</B></P>";
    echo "<TR>";
    echo "<TH WIDTH=5></TH>";
    echo "<TH ALIGN=left>Name</TH>";
    echo "<TH ALIGN=right>Size</TH>";
    echo "<TH ALIGN=right>Modified</TH>";
    echo "<TH ALIGN=right>Action</TH>";
    echo "</TR>\n";

    if ($wdir == "/") {
        $wdir = "";
    } else {
        $updir = dirname($wdir);
        echo "<TR>";
        print_cell("center", "");
        print_cell("left", "<A HREF=\"index.php?id=$id&wdir=$updir\"><IMG SRC=\"pix/parent.gif\" HEIGHT=16 WIDTH=16 BORDER=0 ALT=\"Parent folder\"></A> <A HREF=\"index.php?id=$id&wdir=$updir\">Up to $updir</A>");
        echo "</TR>\n";
    }


    $count = 0;

    if ($dirlist) {
        asort($dirlist);
        foreach ($dirlist as $dir) {

            $count++;

            $filename = $fullpath."/".$dir;
            $fileurl  = rawurlencode($wdir."/".$dir);
            $filesafe = rawurlencode($dir);
            $filedate = userdate(filectime($filename), "d-m-Y H:i:s");
    
            echo "<TR>";

            print_cell("center", "<INPUT TYPE=checkbox NAME=\"file$count\" VALUE=\"$fileurl\">");
            print_cell("left", "<A HREF=\"index.php?id=$id&wdir=$fileurl\"><IMG SRC=\"pix/folder.gif\" HEIGHT=16 WIDTH=16 BORDER=0 ALT=\"Folder\"></A> <A HREF=\"index.php?id=$id&wdir=$fileurl\">".htmlspecialchars($dir)."</A>");
            print_cell("right", "-");
            print_cell("right", $filedate);
            print_cell("right", "<A HREF=\"index.php?id=$id&wdir=$wdir&file=$filesafe&action=rename\">rename</A>");
    
            echo "</TR>";
        }
    }


    if ($filelist) {
        asort($filelist);
        foreach ($filelist as $file) {

            $icon = mimeinfo("icon", $file);

            $count++;
            $filename    = $fullpath."/".$file;
            $fileurl     = "$wdir/$file";
            $filesafe    = rawurlencode($file);
            $fileurlsafe = rawurlencode($fileurl);
            $filedate    = userdate(filectime($filename), "d-m-Y H:i:s");

            echo "<TR>";

            print_cell("center", "<INPUT TYPE=checkbox NAME=\"file$count\" VALUE=\"$fileurl\">");
            echo "<TD ALIGN=left NOWRAP>";
            link_to_popup_window ("/file.php/$id$fileurl", "display", 
                                  "<IMG SRC=\"pix/$icon\" HEIGHT=16 WIDTH=16 BORDER=0 ALT=\"File\">", 
                                  480, 640);
            echo "<FONT SIZE=\"-1\" FACE=\"Arial, Helvetica\">";
            link_to_popup_window ("/file.php/$id$fileurl", "display", 
                                  htmlspecialchars($file),
                                  480, 640);
            echo "</FONT></TD>";

            print_cell("right", display_size($filename));
            print_cell("right", $filedate);
            if ($icon == "text.gif" || $icon == "html.gif") {
                $edittext = "<A HREF=\"index.php?id=$id&wdir=$wdir&file=$fileurl&action=edit\">edit</A>";
            } else if ($icon == "zip.gif") {
                $edittext = "<A HREF=\"index.php?id=$id&wdir=$wdir&file=$fileurl&action=unzip\">unzip</A>";
            } else {
                $edittext = "";
            }
            print_cell("right", "$edittext <A HREF=\"index.php?id=$id&wdir=$wdir&file=$filesafe&action=rename\">rename</A>");
    
            echo "</TR>";
        }
    }
    echo "</TABLE>";
    echo "<HR WIDTH=640 ALIGN=CENTER NOSHADE SIZE=1>";

    if (!$wdir) {
        $wdir = "/";
    }

    echo "<TABLE BORDER=0 cellspacing=2 cellpadding=2 width=640>";    
    echo "<TR><TD>";
    echo "<INPUT TYPE=hidden NAME=id VALUE=\"$id\">";
    echo "<INPUT TYPE=hidden NAME=wdir VALUE=\"$wdir\"> ";
    $options = array (
                   "move" => "Move to another folder",
                   "delete" => "Delete completely",
                   "zip" => "Create zip archive"
               );
    if ($count) {
        choose_from_menu ($options, "action", "", $nothing="With chosen files...", "javascript:document.dirform.submit()");
        //echo "<INPUT TYPE=submit VALUE=Go>";
    }

    echo "</FORM>";
    echo "<TD ALIGN=center>";
    if (($USER->fileop == "move") && $USER->filesource <> $wdir) {
        echo "<FORM ACTION=index.php METHOD=get>";
        echo " <INPUT TYPE=hidden NAME=id VALUE=$id>";
        echo " <INPUT TYPE=hidden NAME=wdir VALUE=\"$wdir\">";
        echo " <INPUT TYPE=hidden NAME=action VALUE=paste>";
        echo " <INPUT TYPE=submit VALUE=\"Move files to here\">";
        echo "</FORM>";
    }
    echo "<TD ALIGN=right>";
        echo "<FORM ACTION=index.php METHOD=get>";
        echo " <INPUT TYPE=hidden NAME=id VALUE=$id>";
        echo " <INPUT TYPE=hidden NAME=wdir VALUE=\"$wdir\">";
        echo " <INPUT TYPE=hidden NAME=action VALUE=mkdir>";
        echo " <INPUT TYPE=submit VALUE=\"Make a folder\">";
        echo "</FORM>";
    echo "</TD>";
    echo "<TD ALIGN=right>";
        echo "<FORM ACTION=index.php METHOD=get>";
        echo " <INPUT TYPE=hidden NAME=id VALUE=$id>";
        echo " <INPUT TYPE=hidden NAME=wdir VALUE=\"$wdir\">";
        echo " <INPUT TYPE=hidden NAME=action VALUE=upload>";
        echo " <INPUT TYPE=submit VALUE=\"Upload a file\">";
        echo "</FORM>";
    echo "</TD></TR>";
    echo "</TABLE>";
    echo "<HR WIDTH=640 ALIGN=CENTER NOSHADE SIZE=1>";

}

?>
