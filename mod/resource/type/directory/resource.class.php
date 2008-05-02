<?php // $Id$

class resource_directory extends resource_base {

function resource_directory($cmid=0) {
    parent::resource_base($cmid);
}

function add_instance($resource) {
    $this->_postprocess($resource);
    return parent::add_instance($resource);
}

function update_instance($resource) {
    $this->_postprocess($resource);
    return parent::update_instance($resource);
}

function _postprocess(&$resource) {
    if($resource->reference=='0')
        $resource->reference = '';

    $resource->popup = '';
    $resource->alltext = '';
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

    $subdir = optional_param('subdir','', PARAM_PATH);
    $resource->reference = clean_param($resource->reference, PARAM_PATH);

    $formatoptions = new object();
    $formatoptions->noclean = true;
    $formatoptions->para = false; // MDL-12061, <p> in html editor breaks xhtml strict

    add_to_log($course->id, "resource", "view", "view.php?id={$cm->id}", $resource->id, $cm->id);

    if ($resource->reference) {
        $relativepath = "{$course->id}/{$resource->reference}";
    } else {
        $relativepath = "{$course->id}";
    }

    if ($subdir) {
        $relativepath = "$relativepath$subdir";
        if (stripos($relativepath, 'backupdata') !== FALSE or stripos($relativepath, $CFG->moddata) !== FALSE) {
            error("Access not allowed!");
        }

        $subs = explode('/', $subdir);
        array_shift($subs);
        $countsubs = count($subs);
        $count = 0;
        $backsub = '';

        foreach ($subs as $sub) {
            $count++;
            if ($count < $countsubs) {
                $backsub .= "/$sub";
                
                $this->navlinks[] = array('name' => $sub, 'link' => "view.php?id={$cm->id}", 'type' => 'title');
            } else {
                $this->navlinks[] = array('name' => $sub, 'link' => '', 'type' => 'title');
            }
        }
    }

    $pagetitle = strip_tags($course->shortname.': '.format_string($resource->name));

    $update = update_module_button($cm->id, $course->id, $this->strresource);
    if (has_capability('moodle/course:managefiles', get_context_instance(CONTEXT_COURSE, $course->id))) {
        $options = array('id'=>$course->id, 'wdir'=>'/'.$resource->reference.$subdir);
        $editfiles = print_single_button("$CFG->wwwroot/files/index.php", $options, get_string("editfiles"), 'get', '', true);
        $update = $editfiles.$update;
    }
    $navigation = build_navigation($this->navlinks, $cm);
    print_header($pagetitle, $course->fullname, $navigation,
            "", "", true, $update,
            navmenu($course, $cm));


    if (trim(strip_tags($resource->summary))) {
        print_simple_box(format_text($resource->summary, FORMAT_MOODLE, $formatoptions, $course->id), "center");
        print_spacer(10,10);
    }

    $files = get_directory_list("$CFG->dataroot/$relativepath", array($CFG->moddata, 'backupdata'), false, true, true);


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
    $strfolder = get_string("folder");
    $strfile = get_string("file");

    echo '<table cellpadding="4" cellspacing="1" class="files" summary="">';
    echo "<tr><th class=\"header name\" scope=\"col\">$strname</th>".
         "<th align=\"right\" colspan=\"2\" class=\"header size\" scope=\"col\">$strsize</th>".
         "<th align=\"right\" class=\"header date\" scope=\"col\">$strmodified</th>".
         "</tr>";
    foreach ($files as $file) {
        if (is_dir("$CFG->dataroot/$relativepath/$file")) {          // Must be a directory
            $icon = "folder.gif";
            $relativeurl = "/view.php?blah";
            $filesize = display_size(get_directory_size("$CFG->dataroot/$relativepath/$file"));

        } else {
            $icon = mimeinfo("icon", $file);
            $relativeurl = get_file_url("$relativepath/$file");
            $filesize = display_size(filesize("$CFG->dataroot/$relativepath/$file"));
        }

        if ($icon == 'folder.gif') {
            echo '<tr class="folder">';
            echo '<td class="name">';
            echo "<a href=\"view.php?id={$cm->id}&amp;subdir=$subdir/$file\">";
            echo "<img src=\"$CFG->pixpath/f/$icon\" class=\"icon\" alt=\"$strfolder\" />&nbsp;$file</a>";
        } else {
            echo '<tr class="file">';
            echo '<td class="name">';
            link_to_popup_window($relativeurl, "resourcedirectory{$resource->id}", "<img src=\"$CFG->pixpath/f/$icon\" class=\"icon\" alt=\"$strfile\" />&nbsp;$file", 450, 600, '');
        }
        echo '</td>';
        echo '<td>&nbsp;</td>';
        echo '<td align="right" class="size">';
        echo $filesize;
        echo '</td>';
        echo '<td align="right" class="date">';
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

    $rawdirs = get_directory_list("$CFG->dataroot/{$this->course->id}", array($CFG->moddata, 'backupdata'), true, true, false);
    $dirs = array();
    foreach ($rawdirs as $rawdir) {
        $dirs[$rawdir] = $rawdir;
    }

    include("$CFG->dirroot/mod/resource/type/directory/directory.html");

    parent::setup_end();
}

function setup_elements(&$mform) {
    global $CFG;

    $rawdirs = get_directory_list($CFG->dataroot.'/'.$this->course->id, array($CFG->moddata, 'backupdata'), true, true, false);
    $dirs = array();
    $dirs[0]=get_string('maindirectory', 'resource');
    foreach ($rawdirs as $rawdir) {
        $dirs[$rawdir] = $rawdir;
    }

    $mform->addElement('select', 'reference', get_string('resourcetypedirectory', 'resource'), $dirs);
    $mform->setDefault('windowpopup', 0);

}


}

?>
