<?php // $Id$

/**
 * This file contains the htmlarea subclass for moodle editorObject.
 *
 * @author Janne Mikkonen
 * @version  $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package editorObject
 */
class htmlarea extends editorObject {

    /**
    * Configuration array to hold configuration data.
    * @var array $htmlareaconf
    */
    var $htmlareaconf = array();

    /**
    * Configuration keys array to store possible configuration keys.
    * @var array $htmlareaconfkeys
    */
    var $htmlareaconfkeys = array("width","height","statusBar","undoSteps","undoTimeout",
                                  "sizeIncludesToolbar","fullPage","pageStyle","killWordOnPaste",
                                  "toolbar","fontname","fontsize","formatblock","customSelects");

    /**
    * An array to store valid value types that can
    * be passed to specific configuration key.
    * @var array $htmlareaconfkeytypes
    */
    var $htmlareaconfkeytypes = array('width' => 'string', 'height' => 'string', 'statusBar' => 'bool',
                                      'undoSteps' => 'int', 'undoTimeout' => 'int',
                                      'sizeIncludeToolbar' => 'bool', 'fullPage' => 'bool',
                                      'pageStyle' => 'string', 'killWordOnPaste' => 'bool',
                                      'toolbar' => 'array', 'fontname' => 'assoc', 'fontsize' => 'assoc',
                                      'formatblock' => 'assoc', 'customSelects' => 'array');

    /**
    * Array of default configuration set via editor settings.
    * @var array $defaults
    */
    var $defaults = array();

    /**
    * PHP4 style class constructor.
    *
    * @param int $courseid Courseid.
    */
    function htmlarea($courseid) {
        parent::editorObject();
        $this->courseid = clean_param($courseid, PARAM_INT);

        $pagestyle  = 'body {';
        $pagestyle .= !empty($this->cfg->editorbackgroundcolor) ?
                             ' background-color: '. $this->cfg->editorbackgroundcolor .'; ' : '';
        $pagestyle .= !empty($this->cfg->editorfontfamily) ?
                             ' font-family: '. $this->cfg->editorfontfamily .';' : '';
        $pagestyle .= !empty($this->cfg->editorfontsize) ?
                             ' font-size: '. $this->cfg->editorfontsize .';' : '';
        $pagestyle .= '}';

        $this->defaults['pageStyle'] = $pagestyle;
        $this->defaults['killWordOnPaste'] = !empty($this->cfg->editorkillword) ? true : false;

        $fontlist = isset($this->cfg->editorfontlist) ? explode(';', $this->cfg->editorfontlist) : array();
        $fonts = array();
        foreach ( $fontlist as $fontline ) {
            if ( !empty($fontline) ) {
                list($fontkey, $fontvalue) = split(":", $fontline);
                $fonts[$fontkey] = $fontvalue;
            }
        }
        $this->defaults['fontname'] = $fonts;
        $this->defaults['hideSomeButtons'] = !empty($this->cfg->editorhidebuttons) ?
                                                    ' '. $this->cfg->editorhidebuttons .' ' : '';

    }

    /**
    * PHP5 style class constructor.
    * @param int $courseid Course id.
    */
    function __construct($courseid) {
        $this->htmlarea($courseid);
    }

    /**
    * Check if passed configuration key is valid.
    * @param string $key
    * @return bool Return true if key is valid and false if it's not.
    */
    function __is_valid_key($key) {
        if ( in_array($key, $this->htmlareaconfkeys) ) {
            return true;
        }
        return false;
    }

    /**
    * Check if passed value's type is valid.
    * @param string $key Configuration key name.
    * @param mixed $value Configuration value.
    * @return bool Returns true if value is valid type and false if it's not.
    */
    function __is_valid_value_type($key, $value) {

        if ( !empty($this->htmlareaconfkeytypes[$key]) ) {
            $keytype = $this->htmlareaconfkeytypes[$key];

            switch ( $keytype ) {
                case 'bool':
                    if ( is_bool($value) ) {
                        return true;
                    }
                break;
                case 'string':
                    if ( is_string($value) ) {
                        return true;
                    }
                break;
                case 'int':
                    if ( is_int($value) ) {
                        return true;
                    }
                break;
                case 'array':
                    if ( is_array($value) ) {
                        return true;
                    }
                break;
                case 'assoc':
                    if ( is_array($value) ) {
                        // Check first key.
                        $key = key($value);
                        if ( preg_match("/[a-z]+/i", $key) ) {
                            return true;
                        }
                    }
                break;
                default:
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
    function setconfig() {

        $numargs = func_num_args();

        switch ( $numargs ) {
            case 1: // Must be an array.
                $args = func_get_arg(0);
                if ( !is_array($args) ) {
                    $this->error("Passed argument is not an array!!!");
                }
                foreach ( $args as $key => $value ) {
                    if ( !preg_match("/[a-z]+/i", $key) && !$this->__is_valid_key($key) ) {
                        $this->error("Invalid configuration key!!!");
                    }
                    if ( $this->__is_valid_value_type($key, $value) ) {
                        $this->htmlareaconf[$key] = $value;
                    } else {
                        $this->error("Invalid key, value pair!!!");
                    }
                }
            break;
            case 2: // Must be key, value pair.
                $key   = func_get_arg(0);
                $value = func_get_arg(1);
                if ( empty($key) or !isset($value) ) {
                    $this->error("Empty key or value passed!!!");
                }
                if ( !preg_match("/[a-z]+/i", $key) ) {
                    $this->error("Configuration key must be a string!!");
                }

                if ( !$this->__is_valid_key($key) ) {
                    $this->error("Invalid configuration key!!!");
                }

                if ( $this->__is_valid_value_type($key, $value) ) {
                    $this->htmlareaconf[$key] = $value;
                } else {
                    $this->error("Invalid key, value pair!!!");
                }
            break;
            default:
                if ( $numargs > 2 ) {
                    $this->error("Too many arguments!!!");
                }
                if ( $numargs < 1 ) {
                    $this->error("No arguments passed!!!");
                }
        }
    }

    /**
    * For internal usage. Print out configuration arrays.
    * @param string $conftype Type of configuration.
    * @return void
    */
    function __printconfig($conftype='') {

        $conf = NULL;
        $assocs = array('fontname','fontsize','formatblocks');

        switch( $conftype ) {
            case 'merge': // New config overrides defaults if found.
                $conf = array_merge($this->defaults,$this->htmlareaconf);
            break;
            case 'append': // Append mode leave default value if found.
                $conf = $this->defaults;
                $keys = array_keys($this->defaults);
                foreach ( $this->htmlareaconf as $key => $value ) {
                    if ( in_array($key, $keys) ) {
                        continue;
                    } else {
                        $conf[$key] = $value;
                    }
                }
            break;
            case 'default': // Use only default config.
                $conf = $this->defaults;
            break;
            default: // Print what's in htmlareaconf.
                $conf = $this->htmlareaconf;
        }

        echo "\n";
        echo '<script type="text/javascript" defer="defer">'."\n";
        echo '//<![CDATA['."\n";
        echo '    var config = new HTMLArea.Config();'."\n";

        foreach ( $conf as $key => $value ) {

            if ( empty($value) ) {
                continue;
            }

            echo '    config.'. $key .' = ';
            if ( is_bool($value) ) {
                echo $value ? "true;\n" : "false;\n";
            } else if ( in_array($key, $assocs) ) {

                echo '{'."\n";
                $cnt = 1;
                foreach ( $value as $key => $value ) {
                    if ( $cnt > 1 ) {
                        echo ",\n";
                    }
                    echo "\t\"$key\" : \"$value\"";
                    $cnt++;
                }
                echo ' };'."\n";

            } else if ( $key == 'toolbar' ) {
                // toolbar is array of arrays.
                echo '['."\n";
                $max = count($conf['toolbar']);
                $cnt = 1;
                foreach ( $conf['toolbar'] as $row ) {
                    echo "\t" . '[ ';
                    $count = count($row);
                    for ( $i = 0; $i < $count; $i++ ) {
                        if ( $i > 0 ) {
                            echo ',';
                        }
                        if ( $i != 0 && ($i % 4) == 0 ) {
                            echo "\n\t";
                        }
                        echo '"'. $row[$i] .'"';
                    }
                    if ( $cnt < $max ) {
                        echo " ],\n";
                    } else {
                        echo " ]\n";
                    }
                    $cnt++;
                }
                echo "\t" . '];'. "\n";

            } else {
                echo '"'. $value .'"'. "\n";
            }
        }

        if ( !empty($this->cfg->editorspelling) && !empty($this->cfg->aspellpath) ) {
            echo "\n";
            $this->print_speller_code(true);
            echo "\n";
        }

        echo '    HTMLArea.replaceAll(config);'."\n";
        echo '//]]>'."\n";
        echo '</script>'."\n";

    }

    /**
    * Print out code that start up the editor.
    * @param string $conftype Configuration type to print.
    */
    function starteditor($configtype='') {
        $this->__printconfig($configtype);
    }

    /**
    * For backward compatibility only.
    * @param string $name
    * @param string $editorhidesomebuttons
    */
    function use_html_editor ( $name='', $editorhidebuttons='' ) {

        if ( !empty($editorhidesomebuttons) ) {
            $this->defaults['hideSomeButtons'] = $editorhidesomebuttons;
        }

        if (empty($name)) {
            $this->starteditor('default');
        } else {
            $this->starteditor('default');
        }

        if ( !empty($this->cfg->editorsrc) ) {
            unset($this->cfg->editorsrc);
        }

    }

    /**
    * Prints out needed code for spellchecking.
    * @param bool $usehtmleditor
    * @todo Deprecated? see lib/weblib.php::print_speller_code()
    * @see lib/weblib.php::print_speller_code()
    */
    function print_speller_code ($usehtmleditor=false) {
        echo "\n".'<script type="text/javascript">'."\n";
        echo '//<![CDATA['."\n";
        if (!$usehtmleditor) {
            echo 'function openSpellChecker() {'."\n";
            echo "\tvar speller = new spellChecker();\n";
            echo "\tspeller.popUpUrl = \"" . $this->cfg->httpswwwroot ."/lib/speller/spellchecker.html\";\n";
            echo "\tspeller.spellCheckScript = \"". $this->cfg->httpswwwroot ."/lib/speller/server-scripts/spellchecker.php\";\n";
            echo "\tspeller.spellCheckAll();\n";
            echo '}'."\n";
        } else {
            echo "\n\tfunction spellClickHandler(editor, buttonId) {\n";
            echo "\t\teditor._textArea.value = editor.getHTML();\n";
            echo "\t\tvar speller = new spellChecker( editor._textArea );\n";
            echo "\t\tspeller.popUpUrl = \"" . $this->cfg->httpswwwroot ."/lib/speller/spellchecker.html\";\n";
            echo "\t\tspeller.spellCheckScript = \"". $this->cfg->httpswwwroot ."/lib/speller/server-scripts/spellchecker.php\";\n";
            echo "\t\tspeller._moogle_edit=1;\n";
            echo "\t\tspeller._editor=editor;\n";
            echo "\t\tspeller.openChecker();\n\t";
            echo '}'."\n";
        }
        echo '//]]>'."\n";
        echo '</script>'."\n";

    }

}
?>
