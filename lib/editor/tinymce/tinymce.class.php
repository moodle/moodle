<?php // $Id$

/**
 * This file contains the tinymce subclass for moodle editorObject.
 *
 * @author Janne Mikkonen
 * @version  $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package editorObject
 */
class tinymce extends editorObject {

    /**
    * The tinyconf variable holds custom variable keys and values pairs in array.
    * @var array $tinyconf
    */
    var $tinyconf = array();

    /**
    * Internal tinyconfkeys is an array of valid configuration keys.
    * @var array $tinyconfkeys
    */
    var $tinyconfkeys = array(
        "mode","theme","plugins","language","ask","textarea_trigger",
        "editor_selector","editor_deselector","elements","docs_language",
        "debug","focus_alert","directionality","auto_reset_designmode",
        "auto_focus","nowrap","button_tile_map","auto_resize","browsers",
        "dialog_type","accessibility_warnings","accessibility_focus",
        "event_elements","table_inline_editing","object_resizing","custom_shortcuts",
        "cleanup","valid_elements","extended_valid_elements","invalid_elements",
        "verify_css_classes","verify_html","preformatted","encoding","cleanup_on_startup",
        "fix_content_duplication","inline_styles","convert_newlines_to_brs","force_br_newlines",
        "force_p_newlines","entities","entity_encoding","remove_linebreaks","convert_fonts_to_spans",
        "font_size_classes","font_size_style_values","merge_styles_invalid_parents",
        "force_hex_style_colors","apply_source_formatting","trim_span_elements","doctype",
        "convert_urls","relative_urls","remove_script_host","document_base_url",
        "urlconverter_callback","insertlink_callback","insertimage_callback","setupcontent_callback",
        "save_callback","onchange_callback","init_instance_callback","file_browser_callback",
        "cleanup_callback","handle_event_callback","execcommand_callback","oninit","onpageload",
        "content_css","popups_css","editor_css","width","height","visual","visual_table_class",
        "custom_undo_redo","custom_undo_redo_levels","custom_undo_redo_keyboard_shortcuts",
        "custom_undo_redo_restore_selection","external_link_list_url","external_image_list_url",
        "add_form_submit_trigger","add_unload_trigger","submit_patch");

    /**
    * Array of valid advanced theme configuration keys.
    * @var array $tinythemekeys
    */
    var $tinythemekeys = array(
        "theme_advanced_layout_manager","theme_advanced_blockformats","theme_advanced_styles",
        "theme_advanced_source_editor_width","theme_advanced_source_editor_height",
        "theme_advanced_toolbar_location","theme_advanced_toolbar_align",
        "theme_advanced_statusbar_location","theme_advanced_buttons<1-n>","theme_advanced_buttons<1-n>_add",
        "theme_advanced_buttons<1-n>_add_before","theme_advanced_disable","theme_advanced_containers",
        "theme_advanced_containers_default_class","theme_advanced_containers_default_align",
        "theme_advanced_container_<container>","theme_advanced_container_<container>_class",
        "theme_advanced_container_<container>_align","theme_advanced_custom_layout",
        "theme_advanced_link_targets","theme_advanced_resizing","theme_advanced_resizing_use_cookie",
        "theme_advanced_resize_horizontal","theme_advanced_path","theme_advanced_fonts");

    /**
    * The defaults configuration array for internal use.
    * @var array $defaults
    */
    var $defaults = array();

    /**
    * For internal usage variable which holds the information
    * should dialogs script be printed after configuration.
    * @var bool $printdialogs
    */
    var $printdialogs = false;

    /**
    * PHP5 style class constructor.
    *
    * @param int $courseid
    */
    function __construct($courseid) {
        parent::editorObject();
        $this->courseid = clean_param($courseid, PARAM_INT);

        $isteacher = isteacher($courseid);

        $this->defaults = array(
            "mode"     => "textareas",
            "theme"    => $this->cfg->tinymcetheme,
            "language" => $this->__get_language(),
            "width"    => "100%",
            "plugins"      => !empty($this->cfg->tinymceplugins) ?
                              $this->cfg->tinymceplugins : '',
            "content_css"  => !empty($this->cfg->tinymcecontentcss) ?
                              $this->cfg->tinymcecontentcss : '',
            "popup_css"    => !empty($this->cfg->tinymcepopupcss) ?
                              $this->cfg->tinymcepopupcss : '',
            "editor_css"   => !empty($this->cfg->tinymceeditorcss) ?
                              $this->cfg->tinymceeditorcss : '',
            "file_browser_callback" => has_capability('moodle/course:managefiles', get_context_instance(CONTEXT_COURSE, $courseid)) ? 'moodleFileBrowser' : '',
            "convert_urls"  => false,
            "relative_urls" => false);

            if ( $this->cfg->tinymcetheme == 'advanced' ) {
                $this->defaults['theme_advanced_buttons1_add'] = "fontselect,fontsizeselect";
                $this->defaults['theme_advanced_buttons2_add'] = "separator,insertdate,inserttime,preview,zoom,separator,forecolor,backcolor,liststyle";
                $this->defaults['theme_advanced_buttons2_add_before'] = "cut,copy,paste,pastetext,pasteword,separator,search,replace,separator";
                $this->defaults['theme_advanced_buttons3_add_before'] = "tablecontrols,separator";
                $this->defaults['theme_advanced_buttons3_add'] = "emotions,iespell,flash,advhr,separator,print,separator,ltr,rtl,separator,fullscreen";
                $this->defaults['theme_advanced_toolbar_location'] = "top";
                $this->defaults['theme_advanced_toolbar_align'] = "left";
                $this->defaults['theme_advanced_statusbar_location'] = "bottom";
                $this->defaults['theme_advanced_resizing'] = true;
                $this->defaults['theme_advanced_resize_horizontal'] = true;
            }

            $this->printdialogs = has_capability('moodle/course:managefiles', get_context_instance(CONTEXT_COURSE, $courseid)) ? true : false;
    }

    /**
    * Checks configuration key validity.
    * @param string $key Configuration key to check.
    * @return bool Returns true if key is valid. Otherwise false is returned.
    */
    function __is_valid_key($key) {

        if ( is_array($key) ) {
            return false;
        }

        if ( strstr($key, "theme_advanced_") ) { // Search in theme keys.

            foreach ( $this->tinythemekeys as $value ) {
                if ( strstr($key, "<1-n>") ) {
                    $value = preg_replace("/<(.*)>/", "([0-9]+)", $value);
                } else {
                    $value = preg_replace("/<(.*)>/", "([a-z0-9]+)", $value);
                }

                if ( preg_match("/^(". $value .")$/i", $key) ) {
                    return true;
                }
            }

        } else {
            if ( in_array($key, $this->tinyconfkeys) ) {
                return true;
            }
        }
        return false;
    }

    /**
    * Sets configuration key and value pairs.
    * Passed parameters can be key and value pair or
    * an associative array of keys and values.
    * @todo code example
    */
    function setconfig () {

        $numargs = func_num_args();

        if ( $numargs > 2 ) {
            $this->error("Too many arguments!");
            exit;
        }

        if ( $numargs < 1 ) {
            $this->error("No arguments passed!");
            exit;
        }

        switch ( $numargs ) {
            case 1: // Must be an array.

                $arg = func_get_arg(0);
                if ( is_array($arg) ) {
                    foreach ( $arg as $key => $value ) {
                        if ( !is_string($key) ) {
                            $this->error("Array is not associative array!");
                            exit;
                        }
                        if ( !$this->__is_valid_key($key) ) {
                            $this->error("Invalid configuration key: '$key'");
                        }

                        $this->tinyconf[$key] = $value;
                    }
                } else {
                    $this->error("Given argument is not an array!!!");
                }

            break;
            case 2: // Key, Value pair.

                $key   = func_get_arg(0);
                $value = func_get_arg(1);

                if ( !$this->__is_valid_key($key) ) {
                    $this->error("Invalid configuration key: $key");
                }
                $this->tinyconf[$key] = $value;

            break;
        }

    }

    /**
    * For internal usage. Print out configuration arrays.
    * @param string $conftype Type of configuration.
    * @return void
    */
    function __printconfig ($conftype='') {

        switch ( $conftype ) {
            case 'merge': // New config overrides defaults if found.
                $conf = array_merge($this->defaults,$this->tinyconf);
            break;
            case 'append': // Append mode leave default value if found.
                $conf = $this->defaults;
                $keys = array_keys($this->defaults);
                foreach ( $this->tinyconf as $key => $value ) {
                    if ( in_array($key, $keys) ) {
                        continue;
                    } else {
                        $conf[$key] = $value;
                    }
                }
            break;
            case 'default':
                $conf = $this->defaults;
            break;
            default:
                $conf = $this->tinyconf;
        }

        echo "\n";
        echo '<script type="text/javascript">'."\n";
        echo '//<![CDATA['."\n";
        echo '  tinyMCE.init({'."\n";

        if ( !empty($conf) ) {
            $max = count($conf);
            $cnt = 1;
            foreach ( $conf as $key => $value ) {

                if ( empty($value) ) {
                    continue;
                }

                if ( $cnt > 1 ) {
                    echo ',' ."\n";
                }

                echo "\t" . $key .' : ';

                if ( is_bool($value) ) {
                    echo ($value) ? 'true' : 'false';
                } else {
                    echo '"'. $value .'"';
                }

                $cnt++;
            }
        }

        echo '  });'."\n";

        if ( $this->printdialogs ) {
            $this->__dialogs();
        }
        echo '//]]>'."\n";
        echo '</script>'."\n";

    }

    /**
    * Print out code that start up the editor.
    * @param string $conftype Configuration type to print.
    */
    function starteditor($conftype='default') {
        $this->__printconfig($conftype);
    }

    /**
    * For backward compatibility only.
    * @param string $name
    * @param string $editorhidesomebuttons
    */
    function use_html_editor($name='', $editorhidesomebuttons='') {
        if ( empty($this->tinyconf) ) {
            $this->__printconfig('default');
        }

        if ( !empty($this->cfg->editorsrc) ) {
            unset($this->cfg->editorsrc);
        }
    }

    /**
    * Print out needed script for custom dialog which is
    * needed to provide access to Moodle's files and folders.
    * For internal use only.
    */
    function __dialogs() {
        ?>

        function moodleFileBrowser (field_name, url, type, win) {
            Dialog("<?php p($this->cfg->wwwroot) ?>/lib/editor/htmlarea/popups/link.php?id=<?php p($this->courseid) ?>", 470, 400, function (param) {

                if ( !param ) {
                    return false;
                }

                win.document.forms[0].elements[field_name].value = param;

            },null);
        }

        <?php
    }

    /**
    * Try to generate TinyMCE compatible language string from
    * current users language. If not successful return default
    * language which is english.
    * For internal use only.
    */
    function __get_language() {

        $tinylangdir = $this->cfg->libdir .'/editor/tinymce/jscripts/tiny_mce/langs';
        $currentlanguage = current_language();
        $defaultlanguage = 'en';

        if ( !$fp = opendir($tinylangdir) ) {
            return $defaultlanguage;
            exit;
        }

        $languages = array();

        while ( ($file = readdir($fp)) !== false ) {
            if ( preg_match("/\.js$/i", $file) ) {
                array_push($languages, basename($file, '.js'));
            }
        }

        if ( $fp ) {
            closedir($fp);
        }

        // If language is found in array.
        if ( in_array($currentlanguage, $languages) ) {
            return $currentlanguage;
        }

        // Check if two character country code is found (eg. fi, de, sv etc.)
        // then return that.
        $currentlanguage = str_replace("_utf8", "", $currentlanguage);

        if ( in_array($currentlanguage, $languages) ) {
            return $currentlanguage;
        }

        return $defaultlanguage;

    }

}

?>
