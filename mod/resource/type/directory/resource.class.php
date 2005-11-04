<?php // $Id$

class resource_directory extends resource_base {

function resource_directory($cmid=0) {
    parent::resource_base($cmid);
}


function display() {
    global $CFG;

/// Set up generic stuff first, including checking for access
    parent::display();

/// Set up some shorthand variables
    $cm = $this->cm;     
    $course = $this->course;
    $resource = $this->resource; 

    require_once($CFG->libdir.'/filelib.php');
 
    $subdir = isset($_GET['subdir']) ? $_GET['subdir'] : '';

    add_to_log($course->id, "resource", "view", "view.php?id={$cm->id}", $resource->id, $cm->id);


    if ($resource->reference) {
        if (detect_munged_arguments($resource->reference, 0)) {
            error("The filename contains illegal characters!");
        }
        $relativepath = "{$course->id}/{$resource->reference}";
    } else {
        $relativepath = "{$course->id}";
    }

    if ($subdir) {
        if (detect_munged_arguments($subdir, 0)) {
            error("The value for 'subdir' contains illegal characters!");
        }
        $relativepath = "$relativepath$subdir";

        $subs = explode('/', $subdir);
        array_shift($subs);
        $countsubs = count($subs);
        $count = 0;
        $subnav = "<a href=\"view.php?id={$cm->id}\">".format_string($resource->name,true)."</a>";
        $backsub = '';
        foreach ($subs as $sub) {
            $count++;
            if ($count < $countsubs) {
                $backsub .= "/$sub";
                $subnav  .= " -> <a href=\"view.php?id={$cm->id}&amp;subdir=$backsub\">$sub</a>";
            } else {
                $subnav .= " -> $sub";
            }
        }
    } else {
        $subnav = format_string($resource->name);
    }

    $pagetitle = strip_tags($course->shortname.': '.format_string($resource->name));

    print_header($pagetitle, $course->fullname, "$this->navigation $subnav",
            "", "", true, update_module_button($cm->id, $course->id, $this->strresource),
            navmenu($course, $cm));

    if (isteacheredit($course->id)) {
        echo "<div align=\"right\"><img src=\"$CFG->pixpath/i/files.gif\" height=\"16\" width=\"16\" alt=\"\" />&nbsp".
            "<a href=\"$CFG->wwwroot/files/index.php?id={$course->id}&amp;wdir=/{$resource->reference}$subdir\">".
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

    print_simple_box_start("center", "", "", '0' );

    $strftime = get_string('strftimedatetime');
    $strname = get_string("name");
    $strsize = get_string("size");
    $strmodified = get_string("modified");

    echo '<table cellpadding="4" cellspacing="1" class="files">';
    echo "<tr><th colspan=\"2\" class=\"header name\">$strname</th>".
         "<th align=\"right\" colspan=\"2\" class=\"header size\">$strsize</th>".
         "<th align=\"right\" class=\"header date\">$strmodified</th>".
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

        if ($icon == 'folder.gif') {
            echo '<tr class="folder"><td>';
            echo "<img src=\"$CFG->pixpath/f/$icon\" width=\"16\" height=\"16\" alt=\"\"/>";
            echo '</td>';
            echo '<td nowrap="nowrap" class="name">';
            echo "<a href=\"view.php?id={$cm->id}&amp;subdir=$subdir/$file\">$file</a>";
        } else {
            echo '<tr class="file"><td>';
            echo "<img src=\"$CFG->pixpath/f/$icon\" width=\"16\" height=\"16\" alt=\"\"/>";
            echo '</td>';
            echo '<td nowrap="nowrap" class="name">';
            link_to_popup_window($relativeurl, "resourcedirectory{$resource->id}", "$file", 450, 600, '');
        }
        echo '</td>';
        echo '<td>&nbsp;</td>';
        echo '<td align="right" nowrap="nowrap" class="size">';
        echo $filesize;
        echo '</td>';
        echo '<td align="right" nowrap="nowrap" class="date">';
        echo userdate(filemtime("$CFG->dataroot/$relativepath/$file"), $strftime);
        echo '</td>';
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
