<?php

/**
 *
 * @copyright &copy; 2006 The Open University
 * @author d.t.le@open.ac.uk, a.j.forth@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package portfolio
 */


class htmlEditor {

    public function __construct() {
    }

    /**
     * Does initial configuration for a given html editor.
     * @param string editor the name of desired html editor, system default will be used if none is passed
     * @param int courseid the courseid uploaded files should be linked to
     * @return bool true if an editor was configured, false otherwise.
     */
    public function configure($editor = NULL, $courseid = NULL) {

        global $CFG;
        static $configured = Array();

        if (!isset($CFG->htmleditor) or (!$CFG->htmleditor)) {
            return;
        }

        if ($editor == '') {
            $editor = (isset($CFG->defaulthtmleditor) ? $CFG->defaulthtmleditor : '');
        }

        if (isset($configured[$editor])) {
            return $configured[$editor];
        }

        $configuration = array();

        switch ($editor) {

            case 'tinymce':
                $configuration[] = $CFG->httpswwwroot ."/lib/editor/tinymce/jscripts/tiny_mce/tiny_mce.js";
                $configuration[] = $CFG->httpswwwroot ."/lib/editor/tinymce.js.php?course=". $courseid;
                $configured['tinymce'] = true;
                break;

            case 'fckeditor':
                $configuration[] = $CFG->httpswwwroot ."/lib/editor/fckeditor/fckeditor.js";
                $configuration[] = $CFG->httpswwwroot ."/lib/editor/fckeditor.js.php?course=". $courseid;
                $configured['fckeditor'] = true;
                break;

//            case 'xinha':
//                $configuration = <<<EOF
//<script type="text/javascript">
//    _editor_url  = "{$CFG->wwwroot}/lib/editor/xinha/"
//    _editor_lang = "en";
//    _editor_skin = "blue-look";
//</script>
//<script type="text/javascript" src="{$CFG->wwwroot}/lib/editor/xinha/XinhaCore.js"></script>
//<script type="text/javascript" src="{$CFG->wwwroot}/lib/editor/xinha.js.php"></script>
//EOF;
//                break;
//
//            case 'yuirte':
//                $configuration = <<<EOF
//<!-- Skin CSS file -->
//<link rel="stylesheet" type="text/css" href="{$CFG->wwwroot}/lib/editor/yui/build/assets/skins/sam/skin.css">
//
//<!-- Utility Dependencies -->
//<script type="text/javascript" src="{$CFG->wwwroot}/lib/editor/yui/build/yahoo-dom-event/yahoo-dom-event.js"></script>
//<script type="text/javascript" src="{$CFG->wwwroot}/lib/editor/yui/build/element/element-beta-min.js"></script>
//
//<!-- Needed for Menus, Buttons and Overlays used in the Toolbar -->
//<script src="{$CFG->wwwroot}/lib/editor/yui/build/container/container_core-min.js"></script>
//<script src="{$CFG->wwwroot}/lib/editor/yui/build/menu/menu-min.js"></script>
//<script src="{$CFG->wwwroot}/lib/editor/yui/build/button/button-min.js"></script>
//
//<!-- Source file for Rich Text Editor-->
//<script src="{$CFG->wwwroot}/lib/editor/yui/build/editor/editor-beta-min.js"></script>
//
//<script type="text/javascript" src="{$CFG->wwwroot}/lib/editor/yuirte.js.php"></script>
//EOF;
//                break;

            default:
                $configured[$editor] = false;
                break;

        }

        if (isset($CFG->editorsrc) && is_array($CFG->editorsrc)) {
            $CFG->editorsrc = $configuration + $CFG->editorsrc;
        } else {
            $CFG->editorsrc = $configuration;
        }

        return $configured[$editor];
    }
}

?>
