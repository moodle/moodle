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

    if ($this->course->category) {
        require_login($this->course->id);
        $navigation = "<a target=\"{$CFG->framename}\" href=\"../../course/view.php?id={$this->course->id}\">{$this->course->shortname}</a> ->              
            <a target=\"{$CFG->framename}\" href=\"index.php?id={$this->course->id}\">$strresources</a> ->";
    } else {
        $navigation = "<a target=\"{$CFG->framename}\" href=\"index.php?id={$this->course->id}\">$strresources</a> ->";     }

    require_once("../../files/mimetypes.php");

    $subdir = isset($_GET['subdir']) ? $_GET['subdir'] : '';

    add_to_log($this->course->id, "resource", "view", "view.php?id={$this->cm->id}", $this->resource->id, $this->cm->id);

    if ($this->resource->reference) {
        $relativepath = "{$this->course->id}/{$this->resource->reference}";
    } else {
        $relativepath = "{$this->course->id}";
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
        $subnav = "<a href=\"view.php?id={$this->cm->id}\">{$this->resource->name}</a>";
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
        $subnav = $this->resource->name;
    }

    $pagetitle = strip_tags($this->course->shortname.': '.$this->resource->name);

    print_header($pagetitle, $this->course->fullname, "$navigation $subnav",
            "", "", true, update_module_button($this->cm->id, $this->course->id, $strresource),
            navmenu($this->course, $this->cm));

    if (isteacheredit($this->course->id)) {
        echo "<div align=\"right\"><img src=\"$CFG->pixpath/i/files.gif\" height=16 width=16 alt=\"\">&nbsp".
            "<a href=\"$CFG->wwwroot/files/index.php?id={$this->course->id}&wdir=/{$this->resource->reference}$subdir\">".
            get_string("editfiles")."...</a></div>";
    }

    if (trim(strip_tags($this->resource->summary))) {
        print_simple_box(text_to_html($this->resource->summary), "center");
        print_spacer(10,10);
    }

    $files = get_directory_list("$CFG->dataroot/$relativepath", 'moddata', false, true, true);


    if (!$files) {
        print_heading(get_string("nofilesyet"));
        print_footer($this->course);
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
        echo "<img src=\"$CFG->pixpath/f/$icon\" width=\"16\" height=\"16\">";
        echo '</td>';
        echo '<td nowrap="nowrap"><p>';
        if ($icon == 'folder.gif') {
            echo "<a href=\"view.php?id={$this->cm->id}&subdir=$subdir/$file\">$file</a>";
        } else {
            link_to_popup_window($relativeurl, "resourcedirectory{$this->resource->id}", "$file", 450, 600, '');
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

    print_footer($this->course);

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
