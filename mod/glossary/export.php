<?php   // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    $id = required_param('id', PARAM_INT);      // Course Module ID

    $mode= optional_param('mode', '', PARAM_ALPHA);           // term entry cat date letter search author approval
    $hook= optional_param('hook', '', PARAM_CLEAN);           // the term, entry, cat, etc... to look for based on mode
    $cat = optional_param('cat',0, PARAM_ALPHANUM);

    if (! $cm = get_coursemodule_from_id('glossary', $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    if (! $glossary = get_record("glossary", "id", $cm->instance)) {
        error("Course module is incorrect");
    }

    require_login($course->id, false, $cm);  
    
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    require_capability('mod/glossary:export', $context);

    $strglossaries = get_string("modulenameplural", "glossary");
    $strglossary = get_string("modulename", "glossary");
    $strallcategories = get_string("allcategories", "glossary");
    $straddentry = get_string("addentry", "glossary");
    $strnoentries = get_string("noentries", "glossary");
    $strsearchconcept = get_string("searchconcept", "glossary");
    $strsearchindefinition = get_string("searchindefinition", "glossary");
    $strsearch = get_string("search");
    $strexportfile = get_string("exportfile", "glossary");
    $strexportentries = get_string('exportentriestoxml', 'glossary');

    $navigation = build_navigation($strexportentries, $cm);
    print_header_simple(format_string($glossary->name), "",$navigation,
        "", "", true, update_module_button($cm->id, $course->id, $strglossary),
        navmenu($course, $cm));

    print_heading($strexportentries);

    print_box_start('glossarydisplay generalbox');
    ?>
    <form action="exportfile.php" method="post">
    <table border="0" cellpadding="6" cellspacing="6" width="100%">
    <tr><td align="center">
        <input type="submit" value="<?php p($strexportfile)?>" />
    </td></tr></table>
    <div>
    <input type="hidden" name="id" value="<?php p($id)?>" />
    <input type="hidden" name="cat" value="<?php p($cat)?>" />
    </div>
    </form>
<?php
    print_box_end();
    print_footer($course);
?>
