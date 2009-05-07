<?php // $Id$
    include("../../../../config.php");
    $lastmodified = filemtime("en.php");
    $lifetime = 1800;

    // Commenting this out since it's creating problems
    // where solution seem to be hard to find...
    // http://moodle.org/mod/forum/discuss.php?d=34376
    //if ( function_exists('ob_gzhandler') ) {
    //    ob_start("ob_gzhandler");
    //}

    // use course language
    $courseid  = optional_param('id', 1, PARAM_INT);
    course_setup($courseid);

    header("Content-type: application/x-javascript; charset: utf-8");  // Correct MIME type
    header("Last-Modified: " . gmdate("D, d M Y H:i:s", $lastmodified) . " GMT");
    header("Expires: " . gmdate("D, d M Y H:i:s", time() + $lifetime) . " GMT");
    // See Bug #2387
    header("Cache-control: max_age = -1");
    header("Pragma: no-cache");

?>
// I18N constants

// LANG: "en", ENCODING: UTF-8 | ISO-8859-1
// Author: Mihai Bazon, <mishoo@infoiasi.ro>

// FOR TRANSLATORS:
//
//   1. PLEASE PUT YOUR CONTACT INFO IN THE ABOVE LINE
//      (at least a valid email address)
//
//   2. PLEASE TRY TO USE UTF-8 FOR ENCODING;
//      (if this is not possible, please include a comment
//       that states what encoding is necessary.)
HTMLArea.I18N = {

    // the following should be the filename without .js extension
    // it will be used for automatically load plugin language.
    lang: "en",

    tooltips: {
        bold:           "<?php print_string("bold","editor") ?>",
        italic:         "<?php print_string("italic","editor") ?>",
        underline:      "<?php print_string("underline","editor") ?>",
        strikethrough:  "<?php print_string("strikethrough","editor") ?>",
        subscript:      "<?php print_string("subscript","editor") ?>",
        superscript:    "<?php print_string("superscript","editor") ?>",
        justifyleft:    "<?php print_string("justifyleft","editor") ?>",
        justifycenter:  "<?php print_string("justifycenter","editor") ?>",
        justifyright:   "<?php print_string("justifyright","editor") ?>",
        justifyfull:    "<?php print_string("justifyfull","editor") ?>",
        insertorderedlist:    "<?php print_string("orderedlist","editor") ?>",
        insertunorderedlist:  "<?php print_string("unorderedlist","editor") ?>",
        outdent:        "<?php print_string("outdent","editor") ?>",
        indent:         "<?php print_string("indent","editor") ?>",
        forecolor:      "<?php print_string("forecolor","editor") ?>",
        hilitecolor:    "<?php print_string("hilitecolor","editor") ?>",
        inserthorizontalrule: "<?php print_string("horizontalrule","editor") ?>",
        createlink:     "<?php print_string("createlink","editor") ?>",
        unlink:         "<?php print_string("removelink","editor") ?>",
        nolink:         "<?php print_string("nolink","editor") ?>",
        insertimage:    "<?php print_string("insertimage","editor") ?>",
        inserttable:    "<?php print_string("inserttable","editor") ?>",
        htmlmode:       "<?php print_string("htmlmode","editor") ?>",
        popupeditor:    "<?php print_string("popupeditor","editor") ?>",
        about:          "<?php print_string("about","editor") ?>",
        showhelp:       "<?php print_string("showhelp","editor") ?>",
        textindicator:  "<?php print_string("textindicator","editor") ?>",
        undo:           "<?php print_string("undo","editor") ?>",
        redo:           "<?php print_string("redo","editor") ?>",
        cut:            "<?php print_string("cut","editor") ?>",
        copy:           "<?php print_string("copy","editor") ?>",
        paste:          "<?php print_string("paste","editor") ?>",
        insertsmile:    "<?php print_string("insertsmile","editor") ?>",
        insertchar:     "<?php print_string("insertchar","editor") ?>",
        search_replace: "<?php print_string("searchandreplace","editor") ?>",
        clean:          "<?php print_string("wordclean","editor") ?>",
        lefttoright:    "<?php print_string("lefttoright","editor");?>",
        righttoleft:    "<?php print_string("righttoleft","editor");?>"
    },

    buttons: {
        "ok":           "<?php print_string("ok","editor") ?>",
        "cancel":       "<?php print_string("cancel","editor") ?>",
        "browse":       "<?php print_string("browse","editor") ?>"
    },

    msg: {
        "Path":         "<?php print_string("path","editor") ?>",
        "TEXT_MODE":    "<?php print_string("textmode","editor") ?>"
    }
};
