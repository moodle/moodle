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

    print_simple_box($this->resource->reference, "center");
    echo "<center><p>";
    echo text_to_html($this->resource->summary);
    echo "</p>";
    echo "<p>&nbsp</p>";
    echo "<p><font size=1>$strlastmodified: ".userdate($this->resource->timemodified)."</p>";
    echo "</center>";
    print_footer($this->course);
}



function setup($form) {
    global $CFG;
    
    global $editorfields;
    $editorfields = 'summary';
    
    parent::setup($form);
    
    include("$CFG->dirroot/mod/resource/type/reference/reference.html");

    parent::setup_end();
}


}

?>
