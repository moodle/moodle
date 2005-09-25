<?php

    /**
     * help.php - Displays help page.
     *
     * Prints a very simple page and includes
     * page content or a string from elsewhere.
     * Usually this will appear in a popup
     * See {@link helpbutton()} in {@link lib/moodlelib.php}
     *
     * @author Martin Dougiamas
     * @version $Id$
     * @package moodlecore
     */


    require_once('config.php');

    $file = optional_param('file', '',PARAM_FILE);
    $text = optional_param('text', 'No text to display',PARAM_CLEAN);
    $module = optional_param('module', 'moodle', PARAM_ALPHAEXT);

    print_header();

    if (detect_munged_arguments($module .'/'. $file)) {
        error('Filenames contain illegal characters!');
    }

    print_simple_box_start('center', '96%');

    $helpfound = false;
    $langs = array(current_language(), get_string('parentlanguage'), 'en');  // Fallback

    if (!empty($file)) {
        foreach ($langs as $lang) {
            if (empty($lang)) {
                continue;
            }
            if ($module == 'moodle') {
                $filepath = $CFG->dirroot .'/lang/'. $lang .'/help/'. $file;
            } else {
                $filepath = $CFG->dirroot .'/lang/'. $lang .'/help/'. $module .'/'. $file;
                if (!file_exists($filepath)) {
                    $filepath = $CFG->dirroot.'/mod/'.$module.'/lang/'. $lang .'/help/'. $module .'/'. $file;
                }
            }

            if (file_exists($filepath)) {
                $helpfound = true;
                include($filepath);   // The actual helpfile

                if ($module == 'moodle' and ($file == 'index.html' or $file == 'mods.html')) {
                    // include file for each module

                    if (!$modules = get_records('modules', 'visible', 1)) {
                        error('No modules found!!');        // Should never happen
                    }

                    foreach ($modules as $mod) {
                        $strmodulename = get_string('modulename', $mod->name);
                        $modulebyname[$strmodulename] = $mod;
                    }
                    ksort($modulebyname);

                    foreach ($modulebyname as $mod) {
                        foreach ($langs as $lang) {
                            if (empty($lang)) {
                                continue;
                            }
                            $filepath = $CFG->dirroot .'/lang/'. $lang .'/help/'. $mod->name .'/'. $file;

                            if (file_exists($filepath)) {
                                echo '<hr size="1" />';
                                include($filepath);   // The actual helpfile
                                break;
                            }
                        }
                    }
                }

                // Some horrible hardcoded stuff follows, should be delegated to modules to handle

                if ($module == 'moodle' and ($file == 'resource/types.html')) {  // RESOURCES
                    require_once($CFG->dirroot .'/mod/resource/lib.php');
                    $typelist = resource_get_resource_types();
                    $typelist['label'] = get_string('resourcetypelabel', 'resource');

                    foreach ($typelist as $type => $name) {
                        foreach ($langs as $lang) {
                            if (empty($lang)) {
                                continue;
                            }
                            $filepath = $CFG->dirroot .'/lang/'. $lang .'/help/resource/type/'. $type .'.html';
                            if (file_exists($filepath)) {
                                echo '<hr size="1" />';
                                include($filepath);   // The actual helpfile
                                break;
                            }
                        }
                    }
                }
                if ($module == 'moodle' and ($file == 'assignment/types.html')) {  // ASSIGNMENTS
                    require_once($CFG->dirroot .'/mod/assignment/lib.php');
                    $typelist = assignment_types();

                    foreach ($typelist as $type => $name) {
                        echo '<p><b>'.$name.'</b></p>';
                        echo get_string('help'.$type, 'assignment');
                        echo '<hr size="1" />';
                    }
                }
                break;
            }
        }
    } else {
        echo '<p>';
        echo clean_text($text);
        echo '</p>';
        $helpfound = true;
    }

    print_simple_box_end();

    if (!$helpfound) {
        $file = clean_text($file);  // Keep it clean!
        notify('Help file "'. $file .'" could not be found!');
    }

    close_window_button();

    echo '<p align="center"><a href="help.php?file=index.html">'. get_string('helpindex') .'</a></p>';
    print_footer('none');
?>
