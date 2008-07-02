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

    public function configure($editor = NULL) {

        global $CFG;
        static $configured = Array();

        if (!isset($CFG->htmleditor) or (!$CFG->htmleditor)) {
            return;
        }

        if ($editor == '') {
            $editor = (isset($CFG->defaulthtmleditor) ? $CFG->defaulthtmleditor : '');
        }

        $configuration = '';

        switch ($editor) {

            case 'tinymce':
                if (!isset($configured['tinymce'])) {
                    $configuration = <<<EOF
<script type="text/javascript" src="{$CFG->wwwroot}/lib/editor/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" src="{$CFG->wwwroot}/lib/editor/tinymce.js.php"></script>
EOF;
                    $configured['tinymce'] = true;
                }
                break;

            case 'fckeditor':
                $configuration = <<<EOF
<script type="text/javascript" src="{$CFG->wwwroot}/lib/editor/fckeditor/fckeditor.js"></script>
<script type="text/javascript" src="{$CFG->wwwroot}/lib/editor/fckeditor.js.php"></script>
EOF;
                break;


            case 'xinha':
                $configuration = <<<EOF
<script type="text/javascript">
    _editor_url  = "{$CFG->wwwroot}/lib/editor/xinha/"
    _editor_lang = "en";
    _editor_skin = "blue-look";
</script>
<script type="text/javascript" src="{$CFG->wwwroot}/lib/editor/xinha/XinhaCore.js"></script>
<script type="text/javascript" src="{$CFG->wwwroot}/lib/editor/xinha.js.php"></script>
EOF;
                break;

            case 'yuirte':
                $configuration = <<<EOF
<!-- Skin CSS file -->
<link rel="stylesheet" type="text/css" href="{$CFG->wwwroot}/lib/editor/yui/build/assets/skins/sam/skin.css">

<!-- Utility Dependencies -->
<script type="text/javascript" src="{$CFG->wwwroot}/lib/editor/yui/build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="{$CFG->wwwroot}/lib/editor/yui/build/element/element-beta-min.js"></script>

<!-- Needed for Menus, Buttons and Overlays used in the Toolbar -->
<script src="{$CFG->wwwroot}/lib/editor/yui/build/container/container_core-min.js"></script>
<script src="{$CFG->wwwroot}/lib/editor/yui/build/menu/menu-min.js"></script>
<script src="{$CFG->wwwroot}/lib/editor/yui/build/button/button-min.js"></script>

<!-- Source file for Rich Text Editor-->
<script src="{$CFG->wwwroot}/lib/editor/yui/build/editor/editor-beta-min.js"></script>

<script type="text/javascript" src="{$CFG->wwwroot}/lib/editor/yuirte.js.php"></script>
EOF;
                break;

            default:
                break;

        }

        return $configuration;

    }
}

?>
