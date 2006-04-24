<?php  /// $Id $
       /// Load IMS required Javascript libraries, adding them
       /// before the standard one ($standard_javascript)

    if (!defined('MOODLE_INTERNAL')) {
        die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
    }

/// We use this globals to be able to generate the proper JavaScripts
    global $jsarg, $standard_javascript;

/// Load IMS needed JavaScript
/// The dummy LMS API hack to stop some SCORM packages giving errors.
    echo "<script language=\"JavaScript\" type=\"text/javascript\" src=\"$CFG->wwwroot/mod/resource/type/ims/dummyapi.js\"></script>\n";
/// The resize iframe script
    echo "    <script language=\"JavaScript\" type=\"text/javascript\" src=\"$CFG->wwwroot/mod/resource/type/ims/resize.js\"></script>\n";
    echo "    <script language=\"JavaScript\" type=\"text/javascript\">
        window.onresize = function() {
            resizeiframe($jsarg);
        };
        window.name='ims-cp-page';
    </script>\n";

/// Load standard JavaScript
    include("$standard_javascript");
?>
