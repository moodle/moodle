<?php // $Id$

class resource_directory extends resource_base {

function resource_directory($cmid=0) {
    parent::resource_base($cmid);
}


function display() {
    global $CFG, $THEME;

    $strresource = get_string("modulename", "resource");
    $strresources = get_string("modulenameplural", "resource");
    $strlastmodified = get_string("lastmodified");

    $course = $this->course;      // Shortcut
    $resource = $this->resource;  // Shortcut

    if ($course->category) {
        require_login($course->id);
        $navigation = "<a target=\"{$CFG->framename}\" href=\"../../course/view.php?id={$course->id}\">{$course->shortname}</a> ->              
            <a target=\"{$CFG->framename}\" href=\"index.php?id={$course->id}\">$strresources</a> ->";
    } else {
        $navigation = "<a target=\"{$CFG->framename}\" href=\"index.php?id={$course->id}\">$strresources</a> ->";     }

    require_once("../../files/mimetypes.php");

    $subdir = isset($_GET['subdir']) ? $_GET['subdir'] : '';

    add_to_log($course->id, "resource", "view", "view.php?id={$this->cm->id}", $resource->id, $this->cm->id);

    if ($resource->reference) {
        $relativepath = "{$course->id}/{$resource->reference}";
    } else {
        $relativepath = "{$course->id}";
    }

    if ($subdir) {
        if (detect_munged_arguments($subdir)) {
            error("The value for 'subdir' contains illegal characters!");
        }
        $relativepath = "$relativepath$subdir";

        $subs = explode('/', $subdir);
        array_shift($subs);
        $countsubs = count($subs);
        $count = 0;
        $subnav = "<a href=\"view.php?id={$this->cm->id}\">{$resource->name}</a>";
        $backsub = '';
        foreach ($subs as $sub) {
            $count++;
            if ($count < $countsubs) {
                $backsub .= "/$sub";
                $subnav  .= " -> <a href=\"view.php?id={$this->cm->id}&subdir=$backsub\">$sub</a>";
            } else {
                $subnav .= " -> $sub";
            }
        }
    } else {
        $subnav = $resource->name;
    }

    $pagetitle = strip_tags($course->shortname.': '.$resource->name);

    print_header($pagetitle, $course->fullname, "$navigation $subnav",
            "", "", true, update_module_button($this->cm->id, $course->id, $strresource),
            navmenu($course, $this->cm));

    if (isteacheredit($course->id)) {
        echo "<div align=\"right\"><img src=\"$CFG->pixpath/i/files.gif\" height=\"16\" width=\"16\" alt=\"\" />&nbsp".
            "<a href=\"$CFG->wwwroot/files/index.php?id={$course->id}&wdir=/{$resource->reference}$subdir\">".
            get_string("editfiles")."...</a></div>";
    }

    if (trim(strip_tags($resource->summary))) {
        $formatoptions->noclean = true;
        print_simple_box(format_text($resource->summary, FORMAT_MOODLE, $formatoptions, $course->id), "center");
        print_spacer(10,10);
    }

    $files = get_directory_list("$CFG->dataroot/$relativepath", 'moddata', false, true, true);


    if (!$files) {
        print_heading(get_string("nofilesyet"));
        print_footer($course);
        exit;
    }

    print_simple_box_start("center", "", "$THEME->cellcontent", '0' );

    $strftime = get_string('strftimedatetime');
    $strname = get_string("name");
    $strsize = get_string("size");
    $strmodified = get_string("modified");

    echo '<table cellpadding="4" cellspacing="1">';
    echo "<tr><th colspan=\"2\">$strname</th>".
         "<th align=\"right\" colspan=\"2\">$strsize</th>".
         "<th align=\"right\">$strmodified</th>".
         "</tr>";
    foreach ($files as $file) {
        if (is_dir("$CFG->dataroot/$relativepath/$file")) {          // Must be a directory
            $icon = "folder.gif";
            $relativeurl = "/view.php?blah";
            $filesize = display_size(get_directory_size("$CFG->dataroot/$relativepath/$file"));

        } else {
            $icon = mimeinfo("icon", $file);

            if ($CFG->slasharguments) {
                $relativeurl = "/file.php/$relativepath/$file";
            } else {
                $relativeurl = "/file.php?file=/$relativepath/$file";
            }
            $filesize = display_size(filesize("$CFG->dataroot/$relativepath/$file"));
        }

        echo '<tr>';
        echo '<td>';
        echo "<img src=\"$CFG->pixpath/f/$icon\" width=\"16\" height=\"16\" />";
        echo '</td>';
        echo '<td nowrap="nowrap"><p>';
        if ($icon == 'folder.gif') {
            echo "<a href=\"view.php?id={$this->cm->id}&subdir=$subdir/$file\">$file</a>";
        } else {
            link_to_popup_window($relativeurl, "resourcedirectory{$resource->id}", "$file", 450, 600, '');
        }
        echo '</p></td>';
        echo '<td>&nbsp;</td>';
        echo '<td align="right" nowrap="nowrap"><p><font size="-1">';
        echo $filesize;
        echo '</font></p></td>';
        echo '<td align="right" nowrap="nowrap"><p><font size="-1">';
        echo userdate(filectime("$CFG->dataroot/$relativepath/$file"), $strftime);
        echo '</font></p></td>';
        echo '</tr>';
    }
    echo '</table>';

    print_simple_box_end();

    print_footer($course);

}


function setup($form) {
    global $CFG;

    parent::setup($form);
    
    $rawdirs = get_directory_list("$CFG->dataroot/{$this->course->id}", 'moddata', true, true, false);
    $dirs = array();
    foreach ($rawdirs as $rawdir) {
        $dirs[$rawdir] = $rawdir;
    }
    
    include("$CFG->dirroot/mod/resource/type/directory/directory.html");

    parent::setup_end();
}


}

?>
