<?PHP // $Id$

//  Manage ONLY ZIP & PIF uploaded files in a course file area

//  This file is a hack to files/index.php that removes
//  the headers and adds some controls so that images
//  can be selected within the Richtext editor.  

//  All the Moodle-specific stuff is in this top section
//  Configuration and access control occurs here.
//  Must define:  USER, basedir, baseweb, html_header and html_footer
//  USER is a persistent variable using sessions

    require("../../config.php");
    require("../../files/mimetypes.php");

    require_variable($id);
    optional_variable($file, "");
    optional_variable($wdir, "");
    optional_variable($action, "");

    if (! $course = get_record("course", "id", $id) ) {
        error("That's an invalid course id");
    }

    require_login($course->id);

    if (! isteacher($course->id) ) {
        error("Only teachers can edit files");
    }

    function html_footer() {
        echo "</td></tr></table></body></html>";
    }
    
    function html_header($course, $wdir, $formfield=""){

        global $CFG,$THEME,$ME;

        if (! $site = get_site()) {
            error("Invalid site!");
        }

        if ($course->id == $site->id) {
            $strfiles = get_string("sitefiles");
        } else {
            $strfiles = get_string("files");
        }
    
        if ($wdir == "/") {
            $fullnav = "$strfiles";
        } else {
            $dirs = explode("/", $wdir);
            $numdirs = count($dirs);
            $link = "";
            $navigation = "";
            for ($i=1; $i<$numdirs; $i++) {
               $navigation .= " -> ";
               $link .= "/".urlencode($dirs[$i]);
               $navigation .= "<a href=\"".$ME."?id=$course->id&wdir=$link\">".$dirs[$i]."</a>";
            }
            $fullnav = "<a href=\"".$ME."?id=$course->id&wdir=/\">$strfiles</a> $navigation";
        }

        print_header();
        ?>
	    <script language="javascript" type="text/javascript">
	    <!--
	    function set_value(txt) {
		    opener.document.forms['form'].reference.value = txt;
		    window.close();
	    }
        -->
	    </script>
        <?php

        echo '<table border="0" cellpadding="3" cellspacing="0" width="100%">';
        echo '<tr>';
        echo '<td bgcolor="'.$THEME->cellheading.'" class="navbar">';
        echo '<font size="2"><b>'."$course->shortname -> $fullnav".'</b></font>';
        echo '</td>';
        echo '</tr>';
        echo '</table>';

        if ($course->id == $site->id) {
            print_heading(get_string("publicsitefileswarning"), "center", 2);
        }

        echo "<table border=0 align=center cellspacing=3 cellpadding=3 width=640>";
        echo "<tr>";
        echo "<td colspan=\"2\">";
    }

    if (! $basedir = make_upload_directory("$course->id")) {
        error("The site administrator needs to fix the file permissions");
    }

    $baseweb = $CFG->wwwroot;

//  End of configuration and access control


    $regexp="\\.\\.";
    if (ereg( $regexp, $file, $regs )| ereg( $regexp, $wdir,$regs )) {           
        $message = "Error: Directories can not contain \"..\"";
        $wdir = "/";
        $action = "";
    }    

    if (!$wdir) {
        $wdir="/";
    }


    switch ($action) {

        case "upload":
            html_header($course, $wdir);

            if (!empty($_FILES['userfile'])) {
                $userfile = $_FILES['userfile'];
            } else {
                $save = false;
            }
            if (!empty($save)) {
                if (!is_uploaded_file($userfile['tmp_name']) or $userfile['size'] == 0) {
                    notify(get_string("uploadnofilefound"));
                } else {
                    $userfile_name = clean_filename($userfile['name']);
                    if ($userfile_name) {
                        $newfile = "$basedir$wdir/$userfile_name";
                        if (move_uploaded_file($userfile['tmp_name'], $newfile)) {
                            chmod($newfile, 0666);
                            $a = NULL;
                            $a->file = "$userfile_name (".$userfile['type'].")";
                            $a->directory = $wdir;
                            print_string("uploadedfileto", "", $a);
                        } else {
                            notify(get_string("uploadproblem", "", $userfile_name));
                        }
                    }
                }
                displaydir($wdir);
                    
            } else {
                $upload_max_filesize = get_max_upload_file_size($CFG->maxbytes);
                $filesize = display_size($upload_max_filesize);

                $struploadafile = get_string("uploadafile");
                $struploadthisfile = get_string("uploadthisfile");
                $strmaxsize = get_string("maxsize", "", $filesize);
                $strcancel = get_string("cancel");

                echo "<P>$struploadafile ($strmaxsize) --> <B>$wdir</B>";
                echo "<TABLE><TR><TD COLSPAN=2>";
                echo "<FORM ENCTYPE=\"multipart/form-data\" METHOD=\"post\" ACTION=\"".$ME."\">";
                echo " <INPUT TYPE=hidden NAME=MAX_FILE_SIZE value=\"$upload_max_filesize\">";
                echo " <INPUT TYPE=hidden NAME=id VALUE=$id>";
                echo " <INPUT TYPE=hidden NAME=wdir VALUE=$wdir>";
                echo " <INPUT TYPE=hidden NAME=action VALUE=upload>";
                echo " <INPUT NAME=\"userfile\" TYPE=\"file\" size=\"60\">";
                echo " </TD><TR><TD WIDTH=10>";
                echo " <INPUT TYPE=submit NAME=save VALUE=\"$struploadthisfile\">";
                echo "</FORM>";
                echo "</TD><TD WIDTH=100%>";
                echo "<FORM ACTION=\"".$ME."\" METHOD=\"get\">";
                echo " <INPUT TYPE=hidden NAME=id VALUE=$id>";
                echo " <INPUT TYPE=hidden NAME=wdir VALUE=$wdir>";
                echo " <INPUT TYPE=hidden NAME=action VALUE=cancel>";
                echo " <INPUT TYPE=submit VALUE=\"$strcancel\">";
                echo "</FORM>";
                echo "</TD></TR></TABLE>";
            }
            html_footer();
            break;

        case "delete":
            if (!empty($confirm)) {
                html_header($course, $wdir);
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
                html_header($course, $wdir);
                if (setfilelist($_POST)) {
                    echo "<p align=center>".get_string("deletecheckwarning").":</p>";
                    print_simple_box_start("center");
                    printfilelist($USER->filelist);
                    print_simple_box_end();
                    echo "<br />";
                    notice_yesno (get_string("deletecheckfiles"), 
                                "".basename($ME)."?id=$id&wdir=$wdir&action=delete&confirm=1",
                                "".basename($ME)."?id=$id&wdir=$wdir&action=cancel");
                } else {
                    displaydir($wdir);
                }
                html_footer();
            }
            break;

        case "move":
            html_header($course, $wdir);
            if ($count = setfilelist($_POST)) {
                $USER->fileop     = $action;
                $USER->filesource = $wdir;
                echo "<p align=center>";
                print_string("selectednowmove", "moodle", $count);
                echo "</p>";
            }
            displaydir($wdir);
            html_footer();
            break;

        case "paste":
            html_header($course, $wdir);
            if (isset($USER->fileop) and $USER->fileop == "move") {
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
            if (!empty($name)) {
                html_header($course, $wdir);
                $name = clean_filename($name);
                if (file_exists($basedir.$wdir."/".$name)) {
                    echo "Error: $name already exists!";
                } else if (!rename($basedir.$wdir."/".$oldname, $basedir.$wdir."/".$name)) {
                    echo "Error: could not rename $oldname to $name";
                }
                displaydir($wdir);
                    
            } else {
                $strrename = get_string("rename");
                $strcancel = get_string("cancel");
                $strrenamefileto = get_string("renamefileto", "moodle", $file);
                html_header($course, $wdir, "form.name");
                echo "<P>$strrenamefileto:";
                echo "<TABLE><TR><TD>";
                echo "<FORM ACTION=\"".$ME."\" METHOD=\"post\" NAME=\"form\">";
                echo " <INPUT TYPE=hidden NAME=id VALUE=$id>";
                echo " <INPUT TYPE=hidden NAME=wdir VALUE=$wdir>";
                echo " <INPUT TYPE=hidden NAME=action VALUE=rename>";
                echo " <INPUT TYPE=hidden NAME=oldname VALUE=\"$file\">";
                echo " <INPUT TYPE=text NAME=name SIZE=35 VALUE=\"$file\">";
                echo " <INPUT TYPE=submit VALUE=\"$strrename\">";
                echo "</FORM>";
                echo "</TD><TD>";
                echo "<FORM ACTION=\"".$ME."\" METHOD=get>";
                echo " <INPUT TYPE=hidden NAME=id VALUE=$id>";
                echo " <INPUT TYPE=hidden NAME=wdir VALUE=$wdir>";
                echo " <INPUT TYPE=hidden NAME=action VALUE=cancel>";
                echo " <INPUT TYPE=submit VALUE=\"$strcancel\">";
                echo "</FORM>";
                echo "</TD></TR></TABLE>";
            }
            html_footer();
            break;

        case "mkdir":
            if (!empty($name)) {
                html_header($course, $wdir);
                $name = clean_filename($name);
                if (file_exists("$basedir$wdir/$name")) {
                    echo "Error: $name already exists!";
                } else if (! make_upload_directory("$course->id/$wdir/$name")) {
                    echo "Error: could not create $name";
                }
                displaydir($wdir);
                    
            } else {
                $strcreate = get_string("create");
                $strcancel = get_string("cancel");
                $strcreatefolder = get_string("createfolder", "moodle", $wdir);
                html_header($course, $wdir, "form.name");
                echo "<P>$strcreatefolder:";
                echo "<TABLE><TR><TD>";
                echo "<FORM ACTION=\"".$ME."\" METHOD=post NAME=form>";
                echo " <INPUT TYPE=hidden NAME=id VALUE=$id>";
                echo " <INPUT TYPE=hidden NAME=wdir VALUE=$wdir>";
                echo " <INPUT TYPE=hidden NAME=action VALUE=mkdir>";
                echo " <INPUT TYPE=text NAME=name SIZE=35>";
                echo " <INPUT TYPE=submit VALUE=\"$strcreate\">";
                echo "</FORM>";
                echo "</TD><TD>";
                echo "<FORM ACTION=\"".$ME."\" METHOD=get>";
                echo " <INPUT TYPE=hidden NAME=id VALUE=$id>";
                echo " <INPUT TYPE=hidden NAME=wdir VALUE=$wdir>";
                echo " <INPUT TYPE=hidden NAME=action VALUE=cancel>";
                echo " <INPUT TYPE=submit VALUE=\"$strcancel\">";
                echo "</FORM>";
                echo "</TD></TR></TABLE>";
            }
            html_footer();
            break;

        case "edit":
            html_header($course, $wdir);
            if (isset($text)) {
                $fileptr = fopen($basedir.$file,"w");
                fputs($fileptr, stripslashes($text));
                fclose($fileptr);
                displaydir($wdir);
                    
            } else {
                $streditfile = get_string("edit", "", "<B>$file</B>");
                $fileptr  = fopen($basedir.$file, "r");
                $contents = fread($fileptr, filesize($basedir.$file));
                fclose($fileptr);

                if (mimeinfo("type", $file) == "text/html") {
                    if ($usehtmleditor = can_use_richtext_editor()) {
                        $onsubmit = "onsubmit=\"copyrichtext(document.form.text);\"";
                    } else {
                        $onsubmit = "";
                    }
                } else {
                    $usehtmleditor = false;
                    $onsubmit = "";
                }

                print_heading("$streditfile");

                echo "<TABLE><TR><TD COLSPAN=2>";
                echo "<FORM ACTION=\"".$ME."\" METHOD=\"post\" NAME=\"form\" $onsubmit>";
                echo " <INPUT TYPE=hidden NAME=id VALUE=$id>";
                echo " <INPUT TYPE=hidden NAME=wdir VALUE=\"$wdir\">";
                echo " <INPUT TYPE=hidden NAME=file VALUE=\"$file\">";
                echo " <INPUT TYPE=hidden NAME=action VALUE=edit>";
                print_textarea($usehtmleditor, 25, 80, 680, 400, "text", $contents);
                echo "</TD></TR><TR><TD>";
                echo " <INPUT TYPE=submit VALUE=\"".get_string("savechanges")."\">";
                echo "</FORM>";
                echo "</TD><TD>";
                echo "<FORM ACTION=\"".$ME."\" METHOD=get>";
                echo " <INPUT TYPE=hidden NAME=id VALUE=$id>";
                echo " <INPUT TYPE=hidden NAME=wdir VALUE=$wdir>";
                echo " <INPUT TYPE=hidden NAME=action VALUE=cancel>";
                echo " <INPUT TYPE=submit VALUE=\"".get_string("cancel")."\">";
                echo "</FORM>";
                echo "</TD></TR></TABLE>";

                if ($usehtmleditor) { 
                    print_richedit_javascript("form", "text", "yes");
                }


            }
            html_footer();
            break;

        case "zip":
            if (!empty($name)) {
                html_header($course, $wdir);
                $name = clean_filename($name);
                if (empty($CFG->zip)) {    // Use built-in php-based zip function
                    $files = array();
                    foreach ($USER->filelist as $file) {
                        $files[] = "$basedir/$file";
                    }
                    include_once($CFG->libdir.'/pclzip/pclzip.lib.php');
                    $archive = new PclZip("$basedir/$wdir/$name");
                    if (($list = $archive->create($files,'',"$basedir/$wdir/")) == 0) {
                        error($archive->errorInfo(true));
                    }
                } else {                   // Use external zip program
                    $files = "";
                    foreach ($USER->filelist as $file) {
                        $files .= basename($file);
                        $files .= " ";
                    }
                    $command = "cd $basedir/$wdir ; $CFG->zip -r $name $files";
                    Exec($command);
                }
                clearfilelist();
                displaydir($wdir);
                    
            } else {
                html_header($course, $wdir, "form.name");

                if (setfilelist($_POST)) {
                    echo "<P ALIGN=CENTER>".get_string("youareabouttocreatezip").":</P>";
                    print_simple_box_start("center");
                    printfilelist($USER->filelist);
                    print_simple_box_end();
                    echo "<BR>";
                    echo "<P ALIGN=CENTER>".get_string("whattocallzip");
                    echo "<TABLE><TR><TD>";
                    echo "<FORM ACTION=\"".$ME."\" METHOD=post NAME=form>";
                    echo " <INPUT TYPE=hidden NAME=id VALUE=$id>";
                    echo " <INPUT TYPE=hidden NAME=wdir VALUE=\"$wdir\">";
                    echo " <INPUT TYPE=hidden NAME=action VALUE=zip>";
                    echo " <INPUT TYPE=text NAME=name SIZE=35 VALUE=\"new.zip\">";
                    echo " <INPUT TYPE=submit VALUE=\"".get_string("createziparchive")."\">";
                    echo "</FORM>";
                    echo "</TD><TD>";
                    echo "<FORM ACTION=\"".$ME."\" METHOD=get>";
                    echo " <INPUT TYPE=hidden NAME=id VALUE=$id>";
                    echo " <INPUT TYPE=hidden NAME=wdir VALUE=$wdir>";
                    echo " <INPUT TYPE=hidden NAME=action VALUE=cancel>";
                    echo " <INPUT TYPE=submit VALUE=\"".get_string("cancel")."\">";
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
            html_header($course, $wdir);
            if (!empty($file)) {
                $strname = get_string("name");
                $strsize = get_string("size");
                $strmodified = get_string("modified");
                $strstatus = get_string("status");
                $strok = get_string("ok");
                $strunpacking = get_string("unpacking", "", $file);

                echo "<P ALIGN=CENTER>$strunpacking:</P>";

                $file = basename($file);

                if (empty($CFG->unzip)) {    // Use built-in php-based unzip function
                    include_once($CFG->libdir.'/pclzip/pclzip.lib.php');
                    $archive = new PclZip("$basedir/$wdir/$file");
                    if (!$list = $archive->extract("$basedir/$wdir")) {
                        error($archive->errorInfo(true));
                    } else {  // print some output
                        echo "<table cellpadding=\"4\" cellspacing=\"2\" border=\"0\" width=640>";
                        echo "<tr><th align=left>$strname</th>";
                        echo "<th align=right>$strsize</th>";
                        echo "<th align=right>$strmodified</th>";
                        echo "<th align=right>$strstatus</th></tr>";
                        foreach ($list as $item) {
                            echo "<tr>";
                            $item['filename'] = str_replace("$basedir/$wdir/", "", $item['filename']);
                            print_cell("left", $item['filename']);
                            if (! $item['folder']) {
                                print_cell("right", display_size($item['size']));
                            } else {
                                echo "<td>&nbsp;</td>";
                            }
                            $filedate  = userdate($item['mtime'], get_string("strftimedatetime"));
                            print_cell("right", $filedate);
                            print_cell("right", $item['status']);
                            echo "</tr>";
                        }
                        echo "</table>";
                    }
                    
                } else {                     // Use external unzip program
                    print_simple_box_start("center");
                    echo "<PRE>";
                    $command = "cd $basedir/$wdir ; $CFG->unzip -o $file 2>&1";
                    passthru($command);
                    echo "</PRE>";
                    print_simple_box_end();
                }

                echo "<CENTER><FORM ACTION=\"".$ME."\" METHOD=get>";
                echo " <INPUT TYPE=hidden NAME=id VALUE=$id>";
                echo " <INPUT TYPE=hidden NAME=wdir VALUE=$wdir>";
                echo " <INPUT TYPE=hidden NAME=action VALUE=cancel>";
                echo " <INPUT TYPE=submit VALUE=\"$strok\">";
                echo "</FORM>";
                echo "</CENTER>";
            } else {
                displaydir($wdir);
            }
            html_footer();
            break;

        case "listzip":
            html_header($course, $wdir);
            if (!empty($file)) {
                $strname = get_string("name");
                $strsize = get_string("size");
                $strmodified = get_string("modified");
                $strok = get_string("ok");
                $strlistfiles = get_string("listfiles", "", $file);

                echo "<P ALIGN=CENTER>$strlistfiles:</P>";
                $file = basename($file);

                include_once($CFG->libdir.'/pclzip/pclzip.lib.php');
                $archive = new PclZip("$basedir/$wdir/$file");
                if (!$list = $archive->listContent("$basedir/$wdir")) {
                    notify($archive->errorInfo(true));

                } else {
                    echo "<table cellpadding=\"4\" cellspacing=\"2\" border=\"0\" width=640>";
                    echo "<tr><th align=left>$strname</th><th align=right>$strsize</th><th align=right>$strmodified</th></tr>";
                    foreach ($list as $item) {
                        echo "<tr>";
                        print_cell("left", $item['filename']);
                        if (! $item['folder']) {
                            print_cell("right", display_size($item['size']));
                        } else {
                            echo "<td>&nbsp;</td>";
                        }
                        $filedate  = userdate($item['mtime'], get_string("strftimedatetime"));
                        print_cell("right", $filedate);
                        echo "</tr>";
                    }
                    echo "</table>";
                }
                echo "<br><center><form action=\"".$ME."\" method=get>";
                echo " <INPUT TYPE=hidden NAME=id VALUE=$id>";
                echo " <INPUT TYPE=hidden NAME=wdir VALUE=$wdir>";
                echo " <INPUT TYPE=hidden NAME=action VALUE=cancel>";
                echo " <INPUT TYPE=submit VALUE=\"$strok\">";
                echo "</FORM>";
                echo "</CENTER>";
            } else {
                displaydir($wdir);
            }
            html_footer();
            break;

        case "torte":
        	if($_POST)
        	{
	        	while(list($key, $val) = each($_POST))
	        	{
		        	if(ereg("file([0-9]+)", $key, $regs))
		        	{
			        	$file = $val;
		        	}
	        	}
	        	if(@filetype($CFG->dataroot ."/". $course->id . $file) == "file")
	        	{
		        	if(mimeinfo("icon", $file) == "image.gif")
		        	{
		        		$url = $CFG->wwwroot ."/file.php?file=/" .$course->id . $file;
		        		runjavascript($url);
	        		}
	        		else
	        		{
		        		print "File is not a image!";
	        		}
	        	}
	        	else
	        	{
		        	print "You cannot insert FOLDER into richtext editor!!!";
	        	}
        	}
        	break;
        case "cancel";
            clearfilelist();

        default:
            html_header($course, $wdir);
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
    global $basedir, $CFG;

    foreach ($filelist as $file) {
        if (is_dir($basedir.$file)) {
            echo "<img src=\"$CFG->pixpath/f/folder.gif\" height=16 width=16> $file<br>";
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
            echo "<img src=\"$CFG->pixpath/f/$icon\"  height=16 width=16> $file<br>";
        }
    }
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
    global $USER, $CFG, $ME;

    $fullpath = $basedir.$wdir;

    $directory = opendir($fullpath);             // Find all files
    while ($file = readdir($directory)) {
        if ($file == "." || $file == "..") {
            continue;
        }
        
        if (is_dir($fullpath."/".$file)) {
            $dirlist[] = $file;
        } else {
            if ((basename(strtolower($file),".zip") != basename(strtolower($file))) || (basename(strtolower($file),".pif") != basename(strtolower($file))))
        	$filelist[] = $file;
        }
    }
    closedir($directory);

    $strname = get_string("name");
    $strsize = get_string("size");
    $strmodified = get_string("modified");
    $straction = get_string("action");
    $strmakeafolder = get_string("makeafolder");
    $struploadafile = get_string("uploadafile");
    $strwithchosenfiles = get_string("withchosenfiles");
    $strmovetoanotherfolder = get_string("movetoanotherfolder");
    $strmovefilestohere = get_string("movefilestohere");
    $strdeletecompletely = get_string("deletecompletely");
    $strcreateziparchive = get_string("createziparchive");
    $strrename = get_string("rename");
    $stredit   = get_string("edit");
    $strunzip  = get_string("unzip");
    $strlist   = get_string("list");
    $strchoose   = get_string("choose");


    echo "<FORM ACTION=\"".$ME."\" METHOD=post NAME=dirform>";
    echo "<TABLE BORDER=0 cellspacing=2 cellpadding=2 width=640>";    
    echo "<TR>";
    echo "<TH WIDTH=5></TH>";
    echo "<TH ALIGN=left>$strname</TH>";
    echo "<TH ALIGN=right>$strsize</TH>";
    echo "<TH ALIGN=right>$strmodified</TH>";
    echo "<TH ALIGN=right>$straction</TH>";
    echo "</TR>\n";

    if ($wdir == "/") {
        $wdir = "";
    }

    $count = 0;

    if (!empty($dirlist)) {
        asort($dirlist);
        foreach ($dirlist as $dir) {

            $count++;

            $filename = $fullpath."/".$dir;
            $fileurl  = rawurlencode($wdir."/".$dir);
            $filesafe = rawurlencode($dir);
            $filedate = userdate(filectime($filename), "%d %b %Y, %I:%M %p");
    
            echo "<TR>";

            print_cell("center", "<INPUT TYPE=checkbox NAME=\"file$count\" VALUE=\"$fileurl\">");
            print_cell("left", "<A HREF=\"".basename($ME)."?id=$id&wdir=$fileurl\"><IMG SRC=\"$CFG->pixpath/f/folder.gif\" HEIGHT=16 WIDTH=16 BORDER=0 ALT=\"Folder\"></A> <A HREF=\"".basename($ME)."?id=$id&wdir=$fileurl\">".htmlspecialchars($dir)."</A>");
            print_cell("right", "-");
            print_cell("right", $filedate);
            print_cell("right", "<A HREF=\"".basename($ME)."?id=$id&wdir=$wdir&file=$filesafe&action=rename\">$strrename</A>");
    
            echo "</TR>";
        }
    }


    if (!empty($filelist)) {
        asort($filelist);
        foreach ($filelist as $file) {

            $icon = mimeinfo("icon", $file);

            $count++;
            $filename    = "$fullpath/$file";
            $fileurl     = "$wdir/$file";
            $filesafe    = rawurlencode($file);
            $fileurlsafe = rawurlencode($fileurl);
            $filedate    = userdate(filectime($filename), "%d %b %Y, %I:%M %p");

            if (substr($fileurl,0,1) == '/') {
                $selectfile = substr($fileurl,1);
            } else {
                $selectfile = $fileurl;
            }
            if ($CFG->slasharguments) {
                $ffurl = "/file.php/$id$fileurl";
            } else {
                $ffurl = "/file.php?file=/$id$fileurl";
            }

            echo "<tr>";

            print_cell("center", "<input type=\"checkbox\" name=\"file$count\" value=\"$fileurl\">");

            echo "<td align=left nowrap>";
            link_to_popup_window ($ffurl, "display", 
                                  "<img src=\"$CFG->pixpath/f/$icon\" height=16 width=16 border=0 alt=\"file\">", 
                                  480, 640);
            echo "<font size=\"-1\" face=\"Arial, Helvetica\">";
            link_to_popup_window ($ffurl, "display", htmlspecialchars($file), 480, 640);
            echo "</font></td>";

            $file_size = filesize($filename);
            print_cell("right", display_size($file_size));
            print_cell("right", $filedate);

            $edittext = "<b><a onMouseDown=\"return set_value('$selectfile')\" href=\"\">$strchoose</a></b>&nbsp;";

            if ($icon == "text.gif" || $icon == "html.gif") {
                $edittext .= "<a href=\"".basename($ME)."?id=$id&wdir=$wdir&file=$fileurl&action=edit\">$stredit</a>";
            } else if ($icon == "zip.gif") {
                $edittext .= "<a href=\"".basename($ME)."?id=$id&wdir=$wdir&file=$fileurl&action=unzip\">$strunzip</a>&nbsp;";
                $edittext .= "<a href=\"".basename($ME)."?id=$id&wdir=$wdir&file=$fileurl&action=listzip\">$strlist</a> ";
            }

            print_cell("right", "$edittext <A HREF=\"".basename($ME)."?id=$id&wdir=$wdir&file=$filesafe&action=rename\">$strrename</A>");
    
            echo "</tr>";
        }
    }
    echo "</table>";
    echo "<hr width=640 align=center noshade size=1>";

    if (empty($wdir)) {
        $wdir = "/";
    }

    echo "<TABLE BORDER=0 cellspacing=2 cellpadding=2 width=640>";    
    echo "<TR><TD>";
    echo "<INPUT TYPE=hidden NAME=id VALUE=\"$id\">";
    echo "<INPUT TYPE=hidden NAME=wdir VALUE=\"$wdir\"> ";
    $options = array (
                   "move" => "$strmovetoanotherfolder",
                   "delete" => "$strdeletecompletely",
                   "zip" => "$strcreateziparchive"
               );
    if (!empty($count)) {
        choose_from_menu ($options, "action", "", "$strwithchosenfiles...", "javascript:document.dirform.submit()");
    }

    echo "</FORM>";
    echo "<TD ALIGN=center>";
    if (!empty($USER->fileop) and ($USER->fileop == "move") and ($USER->filesource <> $wdir)) {
        echo "<FORM ACTION=\"".$ME."\" METHOD=get>";
        echo " <INPUT TYPE=hidden NAME=id VALUE=$id>";
        echo " <INPUT TYPE=hidden NAME=wdir VALUE=\"$wdir\">";
        echo " <INPUT TYPE=hidden NAME=action VALUE=paste>";
        echo " <INPUT TYPE=submit VALUE=\"$strmovefilestohere\">";
        echo "</FORM>";
    }
    echo "<TD ALIGN=right>";
        echo "<FORM ACTION=\"".$ME."\" METHOD=get>";
        echo " <INPUT TYPE=hidden NAME=id VALUE=$id>";
        echo " <INPUT TYPE=hidden NAME=wdir VALUE=\"$wdir\">";
        echo " <INPUT TYPE=hidden NAME=action VALUE=mkdir>";
        echo " <INPUT TYPE=submit VALUE=\"$strmakeafolder\">";
        echo "</FORM>";
    echo "</TD>";
    echo "<TD ALIGN=right>";
        echo "<FORM ACTION=\"".$ME."\" METHOD=get>";
        echo " <INPUT TYPE=hidden NAME=id VALUE=$id>";
        echo " <INPUT TYPE=hidden NAME=wdir VALUE=\"$wdir\">";
        echo " <INPUT TYPE=hidden NAME=action VALUE=upload>";
        echo " <INPUT TYPE=submit VALUE=\"$struploadafile\">";
        echo "</FORM>";
    echo "</TD></TR>";
    echo "</TABLE>";
    echo "<HR WIDTH=640 ALIGN=CENTER NOSHADE SIZE=1>";

}

?>
