<?php   // $Id$

    require_once("../../config.php");
    require_once("lib.php");
    global $CFG, $USER;
    
    require_variable($id);           // Course Module ID

    optional_variable($l,"");
    optional_variable($cat,0);

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    } 
    
    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    } 
    
    if (! $glossary = get_record("glossary", "id", $cm->instance)) {
        error("Course module is incorrect");
    } 
    
    require_login($course->id);    
    if (!isteacher($course->id)) {
        error("You must be a teacher to use this page.");
    } 

    $strglossaries = get_string("modulenameplural", "glossary");
    $strglossary = get_string("modulename", "glossary");
    $strallcategories = get_string("allcategories", "glossary");
    $straddentry = get_string("addentry", "glossary");
    $strnoentries = get_string("noentries", "glossary");
    $strsearchconcept = get_string("searchconcept", "glossary");
    $strsearchindefinition = get_string("searchindefinition", "glossary");
    $strsearch = get_string("search");
    
    $navigation = "";
    if ($course->category) {
        $navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
        require_login($course->id);
    }

    print_header(strip_tags("$course->shortname: $glossary->name"), "$course->fullname",
        "$navigation <A HREF=index.php?id=$course->id>$strglossaries</A> -> $glossary->name",
        "", "", true, update_module_button($cm->id, $course->id, $strglossary),
        navmenu($course, $cm));
    
    echo '<p align="center"><font size="3"><b>' . stripslashes_safe($glossary->name);
    echo '</b></font></p>';

/// Info box

    if ( $glossary->intro ) {
        print_simple_box_start('center','70%');
        echo format_text($glossary->intro);
        print_simple_box_end();
    }

/// Tabbed browsing sections
    $lastl   = $l;
    $lastcat = $cat;
    $tab = GLOSSARY_EXPORT_VIEW;
    include("tabs.html");

    glossary_generate_export_file($glossary,$lastl,$lastcat);
    print_string("glosssaryexported","glossary");

    $ffurl = "/$course->id/glossary/" . clean_filename(strip_tags($glossary->name)) ."/glossary.xml";
    if ($CFG->slasharguments) {
        $ffurl = "../../file.php$ffurl" ;
    } else {
        $ffurl = "../../file.php?file=$ffurl";
    }
    echo '<p><center><a href="' . $ffurl . '" target=_blank>' . get_string("exportedfile","glossary") .  '</a></center><p>'
?>
