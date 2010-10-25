<?php // $Id$

//  Manage all uploaded files in a course file area

//  All the Moodle-specific stuff is in this top section
//  Configuration and access control occurs here.
//  Must define:  USER, basedir, baseweb, html_header and html_footer
//  USER is a persistent variable using sessions

    require('../config.php');
    require_once($CFG->libdir . '/filelib.php');
    require_once($CFG->libdir . '/adminlib.php');

    $id      = required_param('id', PARAM_INT);
    $file    = optional_param('file', '', PARAM_PATH);
    $wdir    = optional_param('wdir', '', PARAM_PATH);
    $action  = optional_param('action', '', PARAM_ACTION);
    $name    = optional_param('name', '', PARAM_FILE);
    $oldname = optional_param('oldname', '', PARAM_FILE);
    $choose  = optional_param('choose', '', PARAM_FILE); //in fact it is always 'formname.inputname'
    $userfile= optional_param('userfile','',PARAM_FILE);
    $save    = optional_param('save', 0, PARAM_BOOL);
    $text    = optional_param('text', '', PARAM_RAW);
    $confirm = optional_param('confirm', 0, PARAM_BOOL);

    if ($choose) {
        if (count(explode('.', $choose)) > 2) {
            error('Incorrect format for choose parameter');
        }
    }


    if (! $course = get_record("course", "id", $id) ) {
        error("That's an invalid course id");
    }

    require_login($course);

    require_capability('moodle/course:managefiles', get_context_instance(CONTEXT_COURSE, $course->id));

    function html_footer() {
        global $COURSE, $choose;

        echo '</td></tr></table>';

        print_footer($COURSE);
    }

    function html_header($course, $wdir, $formfield=""){
        global $CFG, $ME, $choose;

        $navlinks = array();
        // $navlinks[] = array('name' => $course->shortname, 'link' => "../course/view.php?id=$course->id", 'type' => 'misc');

        if ($course->id == SITEID) {
            $strfiles = get_string("sitefiles");
        } else {
            $strfiles = get_string("files");
        }

        if ($wdir == "/") {
            $navlinks[] = array('name' => $strfiles, 'link' => null, 'type' => 'misc');
        } else {
            $dirs = explode("/", $wdir);
            $numdirs = count($dirs);
            $link = "";
            $navlinks[] = array('name' => $strfiles,
                                'link' => $ME."?id=$course->id&amp;wdir=/&amp;choose=$choose",
                                'type' => 'misc');

            for ($i=1; $i<$numdirs-1; $i++) {
                $link .= "/".urlencode($dirs[$i]);
                $navlinks[] = array('name' => $dirs[$i],
                                    'link' => $ME."?id=$course->id&amp;wdir=$link&amp;choose=$choose",
                                    'type' => 'misc');
            }
            $navlinks[] = array('name' => $dirs[$numdirs-1], 'link' => null, 'type' => 'misc');
        }

        $navigation = build_navigation($navlinks);

        if ($choose) {
            print_header();

            $chooseparts = explode('.', $choose);
            if (count($chooseparts)==2){
            ?>
            <script type="text/javascript">
            //<![CDATA[
            function set_value(txt) {
                opener.document.forms['<?php echo $chooseparts[0]."'].".$chooseparts[1] ?>.value = txt;
                window.close();
            }
            //]]>
            </script>

            <?php
            } elseif (count($chooseparts)==1){
            ?>
            <script type="text/javascript">
            //<![CDATA[
            function set_value(txt) {
                opener.document.getElementById('<?php echo $chooseparts[0] ?>').value = txt;
                window.close();
            }
            //]]>
            </script>

            <?php

            }
            $fullnav = '';
            $i = 0;
            foreach ($navlinks as $navlink) {
                // If this is the last link do not link
                if ($i == count($navlinks) - 1) {
                    $fullnav .= $navlink['name'];
                } else {
                    $fullnav .= '<a href="'.$navlink['link'].'">'.$navlink['name'].'</a>';
                }
                $fullnav .= ' -> ';
                $i++;
            }
            $fullnav = substr($fullnav, 0, -4);
            $fullnav = str_replace('->', '&raquo;', format_string($course->shortname) . " -> " . $fullnav);
            echo '<div id="nav-bar">'.$fullnav.'</div>';

            if ($course->id == SITEID and $wdir != "/backupdata") {
                print_heading(get_string("publicsitefileswarning3"), "center", 2);
            }

        } else {

            if ($course->id == SITEID) {

                if ($wdir == "/backupdata") {
                    admin_externalpage_setup('frontpagerestore');
                    admin_externalpage_print_header();
                } else {
                    admin_externalpage_setup('sitefiles');
                    admin_externalpage_print_header();

                    print_heading(get_string("publicsitefileswarning3"), "center", 2);

                }

            } else {
                print_header("$course->shortname: $strfiles", $course->fullname, $navigation,  $formfield);
            }
        }


        echo "<table border=\"0\" style=\"margin-left:auto;margin-right:auto\" cellspacing=\"3\" cellpadding=\"3\" width=\"640\">";
        echo "<tr>";
        echo "<td colspan=\"2\">";

    }


    if (! $basedir = make_upload_directory("$course->id")) {
        error("The site administrator needs to fix the file permissions");
    }

    // make sure site files contain the backupdata or else people put backups into public area!!
    if ($course->id == SITEID) {
        if (!file_exists("$CFG->dataroot/$course->id/backupdata")) {
            make_upload_directory("$course->id/backupdata");
        }
    }

    $baseweb = $CFG->wwwroot;

//  End of configuration and access control


    if ($wdir == '') {
        $wdir = "/";
    }

    if ($wdir{0} != '/') {  //make sure $wdir starts with slash
        $wdir = "/".$wdir;
    }

    if ($wdir == "/backupdata") {
        if (! make_upload_directory("$course->id/backupdata")) {   // Backup folder
            error("Could not create backupdata folder.  The site administrator needs to fix the file permissions");
        }
    }

    if (!is_dir($basedir.$wdir)) {
        html_header($course, $wdir);
        error("Requested directory does not exist.", "$CFG->wwwroot/files/index.php?id=$id");
    }

    switch ($action) {

        case "upload":
            html_header($course, $wdir);
            require_once($CFG->dirroot.'/lib/uploadlib.php');

            if ($save and confirm_sesskey()) {
                $course->maxbytes = 0;  // We are ignoring course limits
                $um = new upload_manager('userfile',false,false,$course,false,0);
                $dir = "$basedir$wdir";
                if ($um->process_file_uploads($dir)) {
                    notify(get_string('uploadedfile'));
                }
                // um will take care of error reporting.
                displaydir($wdir);
            } else {
                $upload_max_filesize = get_max_upload_file_size($CFG->maxbytes);
                $filesize = display_size($upload_max_filesize);

                $struploadafile = get_string("uploadafile");
                $struploadthisfile = get_string("uploadthisfile");
                $strmaxsize = get_string("maxsize", "", $filesize);
                $strcancel = get_string("cancel");

                echo "<p>$struploadafile ($strmaxsize) --> <b>$wdir</b></p>";
                echo "<form enctype=\"multipart/form-data\" method=\"post\" action=\"index.php\">";
                echo "<div>";
                echo "<table><tr><td colspan=\"2\">";
                echo ' <input type="hidden" name="choose" value="'.$choose.'" />';
                echo " <input type=\"hidden\" name=\"id\" value=\"$id\" />";
                echo " <input type=\"hidden\" name=\"wdir\" value=\"$wdir\" />";
                echo " <input type=\"hidden\" name=\"action\" value=\"upload\" />";
                echo " <input type=\"hidden\" name=\"sesskey\" value=\"$USER->sesskey\" />";
                upload_print_form_fragment(1,array('userfile'),null,false,null,$upload_max_filesize,0,false);
                echo " </td></tr></table>";
                echo " <input type=\"submit\" name=\"save\" value=\"$struploadthisfile\" />";
                echo "</div>";
                echo "</form>";
                echo "<form action=\"index.php\" method=\"get\">";
                echo "<div>";
                echo ' <input type="hidden" name="choose" value="'.$choose.'" />';
                echo " <input type=\"hidden\" name=\"id\" value=\"$id\" />";
                echo " <input type=\"hidden\" name=\"wdir\" value=\"$wdir\" />";
                echo " <input type=\"hidden\" name=\"action\" value=\"cancel\" />";
                echo " <input type=\"submit\" value=\"$strcancel\" />";
                echo "</div>";
                echo "</form>";
            }
            html_footer();
            break;

        case "delete":
            if ($confirm and confirm_sesskey()) {
                html_header($course, $wdir);
                if (!empty($USER->filelist)) {
                    foreach ($USER->filelist as $file) {
                        $fullfile = $basedir.'/'.$file;
                        if (! fulldelete($fullfile)) {
                            echo "<br />Error: Could not delete: $fullfile";
                        }
                    }
                }
                clearfilelist();
                displaydir($wdir);
                html_footer();

            } else {
                html_header($course, $wdir);

                if (setfilelist($_POST)) {
                    notify(get_string('deletecheckwarning').':');
                    print_simple_box_start("center");
                    printfilelist($USER->filelist);
                    print_simple_box_end();
                    echo "<br />";

                    require_once($CFG->dirroot.'/mod/resource/lib.php');
                    $block = resource_delete_warning($course, $USER->filelist);

                    if (empty($CFG->resource_blockdeletingfile) or $block == '') {
                        $optionsyes = array('id'=>$id, 'wdir'=>$wdir, 'action'=>'delete', 'confirm'=>1, 'sesskey'=>sesskey(), 'choose'=>$choose);
                        $optionsno  = array('id'=>$id, 'wdir'=>$wdir, 'action'=>'cancel', 'choose'=>$choose);
                        notice_yesno (get_string('deletecheckfiles'), 'index.php', 'index.php', $optionsyes, $optionsno, 'post', 'get');
                    } else {

                        notify(get_string('warningblockingdelete', 'resource'));
                        $options  = array('id'=>$id, 'wdir'=>$wdir, 'action'=>'cancel', 'choose'=>$choose);
                        print_continue("index.php?id=$id&amp;wdir=$wdir&amp;action=cancel&amp;choose=$choose");
                    }
                } else {
                    displaydir($wdir);
                }
                html_footer();
            }
            break;

        case "move":
            html_header($course, $wdir);
            if (($count = setfilelist($_POST)) and confirm_sesskey()) {
                $USER->fileop     = $action;
                $USER->filesource = $wdir;
                echo "<p class=\"centerpara\">";
                print_string("selectednowmove", "moodle", $count);
                echo "</p>";
            }
            displaydir($wdir);
            html_footer();
            break;

        case "paste":
            html_header($course, $wdir);
            if (isset($USER->fileop) and ($USER->fileop == "move") and confirm_sesskey()) {
                foreach ($USER->filelist as $file) {
                    $shortfile = basename($file);
                    $oldfile = $basedir.'/'.$file;
                    $newfile = $basedir.$wdir."/".$shortfile;
                    if (!rename($oldfile, $newfile)) {
                        echo "<p>Error: $shortfile not moved</p>";
                    }
                }
            }
            clearfilelist();
            displaydir($wdir);
            html_footer();
            break;

        case "rename":
            if (($name != '') and confirm_sesskey()) {
                html_header($course, $wdir);
                $name = clean_filename($name);
                if (file_exists($basedir.$wdir."/".$name)) {
                    echo "<center>Error: $name already exists!</center>";
                } else if (!rename($basedir.$wdir."/".$oldname, $basedir.$wdir."/".$name)) {
                    echo "<p align=\"center\">Error: could not rename $oldname to $name</p>";
                } else {
                    //file was renamed now update resources if needed
                    require_once($CFG->dirroot.'/mod/resource/lib.php');
                    resource_renamefiles($course, $wdir, $oldname, $name);
                }
                displaydir($wdir);

            } else {
                $strrename = get_string("rename");
                $strcancel = get_string("cancel");
                $strrenamefileto = get_string("renamefileto", "moodle", $file);
                html_header($course, $wdir, "form.name");
                echo "<p>$strrenamefileto:</p>";
                echo "<table><tr><td>";
                echo "<form action=\"index.php\" method=\"post\">";
                echo "<fieldset class=\"invisiblefieldset\">";
                echo ' <input type="hidden" name="choose" value="'.$choose.'" />';
                echo " <input type=\"hidden\" name=\"id\" value=\"$id\" />";
                echo " <input type=\"hidden\" name=\"wdir\" value=\"$wdir\" />";
                echo " <input type=\"hidden\" name=\"action\" value=\"rename\" />";
                echo " <input type=\"hidden\" name=\"oldname\" value=\"$file\" />";
                echo " <input type=\"hidden\" name=\"sesskey\" value=\"$USER->sesskey\" />";
                echo " <input type=\"text\" name=\"name\" size=\"35\" value=\"$file\" />";
                echo " <input type=\"submit\" value=\"$strrename\" />";
                echo "</fieldset>";
                echo "</form>";
                echo "</td><td>";
                echo "<form action=\"index.php\" method=\"get\">";
                echo "<div>";
                echo ' <input type="hidden" name="choose" value="'.$choose.'" />';
                echo " <input type=\"hidden\" name=\"id\" value=\"$id\" />";
                echo " <input type=\"hidden\" name=\"wdir\" value=\"$wdir\" />";
                echo " <input type=\"hidden\" name=\"action\" value=\"cancel\" />";
                echo " <input type=\"submit\" value=\"$strcancel\" />";
                echo "</div>";
                echo "</form>";
                echo "</td></tr></table>";
            }
            html_footer();
            break;

        case "makedir":
            if (($name != '') and confirm_sesskey()) {
                html_header($course, $wdir);
                $name = clean_filename($name);
                if (file_exists("$basedir$wdir/$name")) {
                    echo "Error: $name already exists!";
                } else if (! make_upload_directory("$course->id$wdir/$name")) {
                    echo "Error: could not create $name";
                }
                displaydir($wdir);

            } else {
                $strcreate = get_string("create");
                $strcancel = get_string("cancel");
                $strcreatefolder = get_string("createfolder", "moodle", $wdir);
                html_header($course, $wdir, "form.name");
                echo "<p>$strcreatefolder:</p>";
                echo "<table><tr><td>";
                echo "<form action=\"index.php\" method=\"post\">";
                echo "<fieldset class=\"invisiblefieldset\">";
                echo ' <input type="hidden" name="choose" value="'.$choose.'" />';
                echo " <input type=\"hidden\" name=\"id\" value=\"$id\" />";
                echo " <input type=\"hidden\" name=\"wdir\" value=\"$wdir\" />";
                echo " <input type=\"hidden\" name=\"action\" value=\"makedir\" />";
                echo " <input type=\"text\" name=\"name\" size=\"35\" />";
                echo " <input type=\"hidden\" name=\"sesskey\" value=\"$USER->sesskey\" />";
                echo " <input type=\"submit\" value=\"$strcreate\" />";
                echo "</fieldset>";
                echo "</form>";
                echo "</td><td>";
                echo "<form action=\"index.php\" method=\"get\">";
                echo "<div>";
                echo ' <input type="hidden" name="choose" value="'.$choose.'" />';
                echo " <input type=\"hidden\" name=\"id\" value=\"$id\" />";
                echo " <input type=\"hidden\" name=\"wdir\" value=\"$wdir\" />";
                echo " <input type=\"hidden\" name=\"action\" value=\"cancel\" />";
                echo " <input type=\"submit\" value=\"$strcancel\" />";
                echo "</div>";
                echo "</form>";
                echo "</td></tr></table>";
            }
            html_footer();
            break;

        case "edit":
            html_header($course, $wdir);
            if (($text != '') and confirm_sesskey()) {
                $fileptr = fopen($basedir.'/'.$file,"w");
                $text = preg_replace('/\x0D/', '', $text);  // http://moodle.org/mod/forum/discuss.php?d=38860
                fputs($fileptr, stripslashes($text));
                fclose($fileptr);
                displaydir($wdir);

            } else {
                $streditfile = get_string("edit", "", "<b>$file</b>");
                $fileptr  = fopen($basedir.'/'.$file, "r");
                $contents = fread($fileptr, filesize($basedir.'/'.$file));
                fclose($fileptr);

                if (mimeinfo("type", $file) == "text/html") {
                    $usehtmleditor = can_use_html_editor();
                } else {
                    $usehtmleditor = false;
                }
                $usehtmleditor = false;    // Always keep it off for now

                print_heading("$streditfile");

                echo "<table><tr><td colspan=\"2\">";
                echo "<form action=\"index.php\" method=\"post\">";
                echo "<div>";
                echo ' <input type="hidden" name="choose" value="'.$choose.'" />';
                echo " <input type=\"hidden\" name=\"id\" value=\"$id\" />";
                echo " <input type=\"hidden\" name=\"wdir\" value=\"$wdir\" />";
                echo " <input type=\"hidden\" name=\"file\" value=\"$file\" />";
                echo " <input type=\"hidden\" name=\"action\" value=\"edit\" />";
                echo " <input type=\"hidden\" name=\"sesskey\" value=\"$USER->sesskey\" />";
                print_textarea($usehtmleditor, 25, 80, 680, 400, "text", $contents);
                echo "</td></tr><tr><td>";
                echo " <input type=\"submit\" value=\"".get_string("savechanges")."\" />";
                echo "</div>";
                echo "</form>";
                echo "</td><td>";
                echo "<form action=\"index.php\" method=\"get\">";
                echo "<div>";
                echo ' <input type="hidden" name="choose" value="'.$choose.'" />';
                echo " <input type=\"hidden\" name=\"id\" value=\"$id\" />";
                echo " <input type=\"hidden\" name=\"wdir\" value=\"$wdir\" />";
                echo " <input type=\"hidden\" name=\"action\" value=\"cancel\" />";
                echo " <input type=\"submit\" value=\"".get_string("cancel")."\" />";
                echo "</div>";
                echo "</form>";
                echo "</td></tr></table>";

                if ($usehtmleditor) {
                    use_html_editor();
                }


            }
            html_footer();
            break;

        case "zip":
            if (($name != '') and confirm_sesskey()) {
                html_header($course, $wdir);
                $name = clean_filename($name);

                $files = array();
                foreach ($USER->filelist as $file) {
                   $files[] = "$basedir/$file";
                }

                if (!zip_files($files,"$basedir$wdir/$name")) {
                    print_error("zipfileserror","error");
                }

                clearfilelist();
                displaydir($wdir);

            } else {
                html_header($course, $wdir, "form.name");

                if (setfilelist($_POST)) {
                    echo "<p align=\"center\">".get_string("youareabouttocreatezip").":</p>";
                    print_simple_box_start("center");
                    printfilelist($USER->filelist);
                    print_simple_box_end();
                    echo "<br />";
                    echo "<p align=\"center\">".get_string("whattocallzip")."</p>";
                    echo "<table><tr><td>";
                    echo "<form action=\"index.php\" method=\"post\">";
                    echo "<fieldset class=\"invisiblefieldset\">";
                    echo ' <input type="hidden" name="choose" value="'.$choose.'" />';
                    echo " <input type=\"hidden\" name=\"id\" value=\"$id\" />";
                    echo " <input type=\"hidden\" name=\"wdir\" value=\"$wdir\" />";
                    echo " <input type=\"hidden\" name=\"action\" value=\"zip\" />";
                    echo " <input type=\"text\" name=\"name\" size=\"35\" value=\"new.zip\" />";
                    echo " <input type=\"hidden\" name=\"sesskey\" value=\"$USER->sesskey\" />";
                    echo " <input type=\"submit\" value=\"".get_string("createziparchive")."\" />";
                    echo "<fieldset>";
                    echo "</form>";
                    echo "</td><td>";
                    echo "<form action=\"index.php\" method=\"get\">";
                    echo "<div>";
                    echo ' <input type="hidden" name="choose" value="'.$choose.'" />';
                    echo " <input type=\"hidden\" name=\"id\" value=\"$id\" />";
                    echo " <input type=\"hidden\" name=\"wdir\" value=\"$wdir\" />";
                    echo " <input type=\"hidden\" name=\"action\" value=\"cancel\" />";
                    echo " <input type=\"submit\" value=\"".get_string("cancel")."\" />";
                    echo "</div>";
                    echo "</form>";
                    echo "</td></tr></table>";
                } else {
                    displaydir($wdir);
                    clearfilelist();
                }
            }
            html_footer();
            break;

        case "unzip":
            html_header($course, $wdir);
            if (($file != '') and confirm_sesskey()) {
                $strok = get_string("ok");
                $strunpacking = get_string("unpacking", "", $file);

                echo "<p align=\"center\">$strunpacking:</p>";

                $file = basename($file);

                if (!unzip_file("$basedir$wdir/$file")) {
                    print_error("unzipfileserror","error");
                }

                echo "<div style=\"text-align:center\"><form action=\"index.php\" method=\"get\">";
                echo "<div>";
                echo ' <input type="hidden" name="choose" value="'.$choose.'" />';
                echo " <input type=\"hidden\" name=\"id\" value=\"$id\" />";
                echo " <input type=\"hidden\" name=\"wdir\" value=\"$wdir\" />";
                echo " <input type=\"hidden\" name=\"action\" value=\"cancel\" />";
                echo " <input type=\"submit\" value=\"$strok\" />";
                echo "</div>";
                echo "</form>";
                echo "</div>";
            } else {
                displaydir($wdir);
            }
            html_footer();
            break;

        case "listzip":
            html_header($course, $wdir);
            if (($file != '') and confirm_sesskey()) {
                $strname = get_string("name");
                $strsize = get_string("size");
                $strmodified = get_string("modified");
                $strok = get_string("ok");
                $strlistfiles = get_string("listfiles", "", $file);

                echo "<p align=\"center\">$strlistfiles:</p>";
                $file = basename($file);

                include_once("$CFG->libdir/pclzip/pclzip.lib.php");
                $archive = new PclZip(cleardoubleslashes("$basedir$wdir/$file"));
                if (!$list = $archive->listContent(cleardoubleslashes("$basedir$wdir"))) {
                    notify($archive->errorInfo(true));

                } else {
                    echo "<table cellpadding=\"4\" cellspacing=\"2\" border=\"0\" width=\"640\" class=\"files\">";
                    echo "<tr class=\"file\"><th align=\"left\" class=\"header name\" scope=\"col\">$strname</th><th align=\"right\" class=\"header size\" scope=\"col\">$strsize</th><th align=\"right\" class=\"header date\" scope=\"col\">$strmodified</th></tr>";
                    foreach ($list as $item) {
                        echo "<tr>";
                        print_cell("left", s($item['filename']), 'name');
                        if (! $item['folder']) {
                            print_cell("right", display_size($item['size']), 'size');
                        } else {
                            echo "<td>&nbsp;</td>";
                        }
                        $filedate  = userdate($item['mtime'], get_string("strftimedatetime"));
                        print_cell("right", $filedate, 'date');
                        echo "</tr>";
                    }
                    echo "</table>";
                }
                echo "<br /><center><form action=\"index.php\" method=\"get\">";
                echo "<div>";
                echo ' <input type="hidden" name="choose" value="'.$choose.'" />';
                echo " <input type=\"hidden\" name=\"id\" value=\"$id\" />";
                echo " <input type=\"hidden\" name=\"wdir\" value=\"$wdir\" />";
                echo " <input type=\"hidden\" name=\"action\" value=\"cancel\" />";
                echo " <input type=\"submit\" value=\"$strok\" />";
                echo "</div>";
                echo "</form>";
                echo "</center>";
            } else {
                displaydir($wdir);
            }
            html_footer();
            break;

        case "restore":
            html_header($course, $wdir);
            if (($file != '') and confirm_sesskey()) {
                echo "<p align=\"center\">".get_string("youaregoingtorestorefrom").":</p>";
                print_simple_box_start("center");
                echo $file;
                print_simple_box_end();
                echo "<br />";
                echo "<p align=\"center\">".get_string("areyousuretorestorethisinfo")."</p>";
                $restore_path = "$CFG->wwwroot/backup/restore.php";
                notice_yesno (get_string("areyousuretorestorethis"),
                                $restore_path."?id=".$id."&amp;file=".cleardoubleslashes($id.$wdir."/".$file)."&amp;method=manual",
                                "index.php?id=$id&amp;wdir=$wdir&amp;action=cancel");
            } else {
                displaydir($wdir);
            }
            html_footer();
            break;

        case "cancel":
            clearfilelist();

        default:
            html_header($course, $wdir);
            displaydir($wdir);
            html_footer();
            break;
}


/// FILE FUNCTIONS ///////////////////////////////////////////////////////////


function setfilelist($VARS) {
    global $USER;

    $USER->filelist = array ();
    $USER->fileop = "";

    $count = 0;
    foreach ($VARS as $key => $val) {
        if (substr($key,0,4) == "file") {
            $count++;
            $val = rawurldecode($val);
            $USER->filelist[] = clean_param($val, PARAM_PATH);
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
    global $CFG, $basedir;

    $strfolder = get_string("folder");
    $strfile   = get_string("file");

    foreach ($filelist as $file) {
        if (is_dir($basedir.'/'.$file)) {
            echo '<img src="'. $CFG->pixpath .'/f/folder.gif" class="icon" alt="'. $strfolder .'" /> '. htmlspecialchars($file) .'<br />';
            $subfilelist = array();
            $currdir = opendir($basedir.'/'.$file);
            while (false !== ($subfile = readdir($currdir))) {
                if ($subfile <> ".." && $subfile <> ".") {
                    $subfilelist[] = $file."/".$subfile;
                }
            }
            printfilelist($subfilelist);

        } else {
            $icon = mimeinfo("icon", $file);
            echo '<img src="'. $CFG->pixpath .'/f/'. $icon .'" class="icon" alt="'. $strfile .'" /> '. htmlspecialchars($file) .'<br />';
        }
    }
}


function print_cell($alignment='center', $text='&nbsp;', $class='') {
    if ($class) {
        $class = ' class="'.$class.'"';
    }
    echo '<td align="'.$alignment.'" style="white-space:nowrap "'.$class.'>'.$text.'</td>';
}

function displaydir ($wdir) {
//  $wdir == / or /a or /a/b/c/d  etc

    global $basedir;
    global $id;
    global $USER, $CFG;
    global $choose;

    $fullpath = $basedir.$wdir;
    $dirlist = array();

    $directory = opendir($fullpath);             // Find all files
    while (false !== ($file = readdir($directory))) {
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

    $strname = get_string("name");
    $strsize = get_string("size");
    $strmodified = get_string("modified");
    $straction = get_string("action");
    $strmakeafolder = get_string("makeafolder");
    $struploadafile = get_string("uploadafile");
    $strselectall = get_string("selectall");
    $strselectnone = get_string("deselectall");
    $strwithchosenfiles = get_string("withchosenfiles");
    $strmovetoanotherfolder = get_string("movetoanotherfolder");
    $strmovefilestohere = get_string("movefilestohere");
    $strdeletecompletely = get_string("deletecompletely");
    $strcreateziparchive = get_string("createziparchive");
    $strrename = get_string("rename");
    $stredit   = get_string("edit");
    $strunzip  = get_string("unzip");
    $strlist   = get_string("list");
    $strrestore= get_string("restore");
    $strchoose = get_string("choose");
    $strfolder = get_string("folder");
    $strfile   = get_string("file");


    echo "<form action=\"index.php\" method=\"post\" id=\"dirform\">";
    echo "<div>";
    echo '<input type="hidden" name="choose" value="'.$choose.'" />';
    // echo "<hr align=\"center\" noshade=\"noshade\" size=\"1\" />";
    echo "<hr/>";
    echo "<table border=\"0\" cellspacing=\"2\" cellpadding=\"2\" width=\"640\" class=\"files\">";
    echo "<tr>";
    echo "<th class=\"header\" scope=\"col\"></th>";
    echo "<th class=\"header name\" scope=\"col\">$strname</th>";
    echo "<th class=\"header size\" scope=\"col\">$strsize</th>";
    echo "<th class=\"header date\" scope=\"col\">$strmodified</th>";
    echo "<th class=\"header commands\" scope=\"col\">$straction</th>";
    echo "</tr>\n";

    if ($wdir != "/") {
        $dirlist[] = '..';
    }

    $count = 0;

    if (!empty($dirlist)) {
        asort($dirlist);
        foreach ($dirlist as $dir) {
            echo "<tr class=\"folder\">";

            if ($dir == '..') {
                $fileurl = rawurlencode(dirname($wdir));
                print_cell();
                // alt attribute intentionally empty to prevent repetition in screen reader
                print_cell('left', '<a href="index.php?id='.$id.'&amp;wdir='.$fileurl.'&amp;choose='.$choose.'"><img src="'.$CFG->pixpath.'/f/parent.gif" class="icon" alt="" />&nbsp;'.get_string('parentfolder').'</a>', 'name');
                print_cell();
                print_cell();
                print_cell();

            } else {
                $count++;
                $filename = $fullpath."/".$dir;
                $fileurl  = rawurlencode($wdir."/".$dir);
                $filesafe = rawurlencode($dir);
                $filesize = display_size(get_directory_size("$fullpath/$dir"));
                $filedate = userdate(filemtime($filename), get_string("strftimedatetime"));
                if ($wdir.$dir === '/moddata') {
                    print_cell();
                } else {
                    print_cell("center", "<input type=\"checkbox\" name=\"file$count\" value=\"$fileurl\" />", 'checkbox');
                }
                print_cell("left", "<a href=\"index.php?id=$id&amp;wdir=$fileurl&amp;choose=$choose\"><img src=\"$CFG->pixpath/f/folder.gif\" class=\"icon\" alt=\"$strfolder\" />&nbsp;".htmlspecialchars($dir)."</a>", 'name');
                print_cell("right", $filesize, 'size');
                print_cell("right", $filedate, 'date');
                if ($wdir.$dir === '/moddata') {
                    print_cell();
                } else { 
                    print_cell("right", "<a href=\"index.php?id=$id&amp;wdir=$wdir&amp;file=$filesafe&amp;action=rename&amp;choose=$choose\">$strrename</a>", 'commands');
                }
            }

            echo "</tr>";
        }
    }


    if (!empty($filelist)) {
        asort($filelist);
        foreach ($filelist as $file) {

            $icon = mimeinfo("icon", $file);

            $count++;
            $filename    = $fullpath."/".$file;
            $fileurl     = trim($wdir, "/")."/$file";
            $filesafe    = rawurlencode($file);
            $fileurlsafe = rawurlencode($fileurl);
            $filedate    = userdate(filemtime($filename), get_string("strftimedatetime"));

            $selectfile = trim($fileurl, "/");

            echo "<tr class=\"file\">";

            print_cell("center", "<input type=\"checkbox\" name=\"file$count\" value=\"$fileurl\" />", 'checkbox');
            echo "<td align=\"left\" style=\"white-space:nowrap\" class=\"name\">";

            $ffurl = get_file_url($id.'/'.$fileurl);
            link_to_popup_window ($ffurl, "display",
                                  "<img src=\"$CFG->pixpath/f/$icon\" class=\"icon\" alt=\"$strfile\" />&nbsp;".htmlspecialchars($file),
                                  480, 640);
            echo "</td>";

            $file_size = filesize($filename);
            print_cell("right", display_size($file_size), 'size');
            print_cell("right", $filedate, 'date');

            if ($choose) {
                $edittext = "<strong><a onclick=\"return set_value('$selectfile')\" href=\"#\">$strchoose</a></strong>&nbsp;";
            } else {
                $edittext = '';
            }


            if ($icon == "text.gif" || $icon == "html.gif") {
                $edittext .= "<a href=\"index.php?id=$id&amp;wdir=$wdir&amp;file=$fileurl&amp;action=edit&amp;choose=$choose\">$stredit</a>";
            } else if ($icon == "zip.gif") {
                $edittext .= "<a href=\"index.php?id=$id&amp;wdir=$wdir&amp;file=$fileurl&amp;action=unzip&amp;sesskey=$USER->sesskey&amp;choose=$choose\">$strunzip</a>&nbsp;";
                $edittext .= "<a href=\"index.php?id=$id&amp;wdir=$wdir&amp;file=$fileurl&amp;action=listzip&amp;sesskey=$USER->sesskey&amp;choose=$choose\">$strlist</a> ";
                if (!empty($CFG->backup_version) and has_capability('moodle/site:restore', get_context_instance(CONTEXT_COURSE, $id))) {
                    $edittext .= "<a href=\"index.php?id=$id&amp;wdir=$wdir&amp;file=$filesafe&amp;action=restore&amp;sesskey=$USER->sesskey&amp;choose=$choose\">$strrestore</a> ";
                }
            }

            print_cell("right", "$edittext <a href=\"index.php?id=$id&amp;wdir=$wdir&amp;file=$filesafe&amp;action=rename&amp;choose=$choose\">$strrename</a>", 'commands');

            echo "</tr>";
        }
    }
    echo "</table>";
    echo "<hr />";
    //echo "<hr width=\"640\" align=\"center\" noshade=\"noshade\" size=\"1\" />";

    echo "<table border=\"0\" cellspacing=\"2\" cellpadding=\"2\" width=\"640\">";
    echo "<tr><td>";
    echo "<input type=\"hidden\" name=\"id\" value=\"$id\" />";
    echo '<input type="hidden" name="choose" value="'.$choose.'" />';
    echo "<input type=\"hidden\" name=\"wdir\" value=\"$wdir\" /> ";
    echo "<input type=\"hidden\" name=\"sesskey\" value=\"$USER->sesskey\" />";
    $options = array (
                   "move" => "$strmovetoanotherfolder",
                   "delete" => "$strdeletecompletely",
                   "zip" => "$strcreateziparchive"
               );
    if (!empty($count)) {

        choose_from_menu ($options, "action", "", "$strwithchosenfiles...", "javascript:getElementById('dirform').submit()");
        echo '<div id="noscriptgo" style="display: inline;">';
        echo '<input type="submit" value="'.get_string('go').'" />';
        echo '<script type="text/javascript">'.
               "\n//<![CDATA[\n".
               'document.getElementById("noscriptgo").style.display = "none";'.
               "\n//]]>\n".'</script>';
        echo '</div>';

    }
    echo "</td></tr></table>";
    echo "</div>";
    echo "</form>";
    echo "<table border=\"0\" cellspacing=\"2\" cellpadding=\"2\" width=\"640\"><tr>";
    echo "<td align=\"center\">";
    if (!empty($USER->fileop) and ($USER->fileop == "move") and ($USER->filesource <> $wdir)) {
        echo "<form action=\"index.php\" method=\"get\">";
        echo "<div>";
        echo ' <input type="hidden" name="choose" value="'.$choose.'" />';
        echo " <input type=\"hidden\" name=\"id\" value=\"$id\" />";
        echo " <input type=\"hidden\" name=\"wdir\" value=\"$wdir\" />";
        echo " <input type=\"hidden\" name=\"action\" value=\"paste\" />";
        echo " <input type=\"hidden\" name=\"sesskey\" value=\"$USER->sesskey\" />";
        echo " <input type=\"submit\" value=\"$strmovefilestohere\" />";
        echo "</div>";
        echo "</form>";
    }
    echo "</td>";
    echo "<td align=\"right\">";
        echo "<form action=\"index.php\" method=\"get\">";
        echo "<div>";
        echo ' <input type="hidden" name="choose" value="'.$choose.'" />';
        echo " <input type=\"hidden\" name=\"id\" value=\"$id\" />";
        echo " <input type=\"hidden\" name=\"wdir\" value=\"$wdir\" />";
        echo " <input type=\"hidden\" name=\"action\" value=\"makedir\" />";
        echo " <input type=\"submit\" value=\"$strmakeafolder\" />";
        echo "</div>";
        echo "</form>";
    echo "</td>";
    echo "<td align=\"right\">";
        echo "<form action=\"index.php\" method=\"get\">"; //dummy form - alignment only
        echo "<fieldset class=\"invisiblefieldset\">";
        echo " <input type=\"button\" value=\"$strselectall\" onclick=\"checkall();\" />";
        echo " <input type=\"button\" value=\"$strselectnone\" onclick=\"uncheckall();\" />";
        echo "</fieldset>";
        echo "</form>";
    echo "</td>";
    echo "<td align=\"right\">";
        echo "<form action=\"index.php\" method=\"get\">";
        echo "<div>";
        echo ' <input type="hidden" name="choose" value="'.$choose.'" />';
        echo " <input type=\"hidden\" name=\"id\" value=\"$id\" />";
        echo " <input type=\"hidden\" name=\"wdir\" value=\"$wdir\" />";
        echo " <input type=\"hidden\" name=\"action\" value=\"upload\" />";
        echo " <input type=\"submit\" value=\"$struploadafile\" />";
        echo "</div>";
        echo "</form>";
    echo "</td></tr>";
    echo "</table>";
    echo "<hr/>";
    //echo "<hr width=\"640\" align=\"center\" noshade=\"noshade\" size=\"1\" />";

}

?>
