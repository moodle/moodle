<?php // $Id$

class resource extends resource_base {


function resource($cmid=0) {
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
        $navigation = "<a target=\"{$CFG->framename}\" href=\"index.php?id={$this->course->id}\">$strresources</a> ->";             }                                                                                                             


    add_to_log($this->course->id, "resource", "view", "view.php?id={$this->cm->id}", $this->resource->id, $this->cm->id);
    print_header($pagetitle, $this->course->fullname, "$navigation {$this->resource->name}",
                 "", "", true, update_module_button($this->cm->id, $this->course->id, $strresource), navmenu($this->course, $this->cm));

    print_simple_box(format_text($this->resource->alltext, FORMAT_HTML), "center", "", "$THEME->cellcontent", "20");

    echo "<center><p><font size=1>$strlastmodified: ".userdate($this->resource->timemodified)."</p></center>";

    print_footer($this->course);
}



function setup($form) {
    global $CFG, $usehtmleditor;
    
    parent::setup($form);

    include("$CFG->dirroot/mod/resource/type/html/html.html");

    parent::setup_end();
}


}

?>
