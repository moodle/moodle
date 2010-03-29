<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
 * A simple test script that hammers get_string, and times how long it takes.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/moodlelib.php');

define('NUM_CALLS', 20000);
define('NUM_REPITITIONS', 3);
$TEST_LANGUAGES = array('en_utf8', 'fr_utf8', 'fr_ca_utf8', 'nonexistant');

require_login();
require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

$title = 'get_string performance test';
$PAGE->navbar->add($title);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_url('/lib/simpletest/getstringperformancetester.php');
echo $OUTPUT->header();

$installedlangs = get_list_of_languages();
$requiredlangs = $TEST_LANGUAGES;
array_pop($requiredlangs);
foreach ($requiredlangs as $lang) {
    if (!isset($installedlangs[$lang])) {
        echo $OUTPUT->notification('You must install the following language packs to run these test: ' . implode(', ', $requiredlangs));
        echo $OUTPUT->footer();
        die;
    }
}

time_for_loop();
echo $OUTPUT->heading('Timing calling functions');
time_function_call('dummy_function');
time_function_call('simple_eval');
time_function_call('simple_eval_with_interp');
time_function_call('sprintf_eval');
time_function_call('sprintf_eval_with_interp');
time_function_call('strtr_eval');
time_function_call('strtr_eval_with_interp');
time_function_call('proposed');
time_function_call('proposed_with_interp');
time_log_file('empty.log.php');

unset($COURSE->lang);
if (isset($SESSION->lang)) {
    $originalsessionlang = $SESSION->lang;
} else {
    $originalsessionlang = null;
}
try {

    foreach ($TEST_LANGUAGES as $lang) {
        echo $OUTPUT->heading("Language '$lang'");
        $SESSION->lang = $lang;

        time_log_file('admin_index.php_get_string.log.php');
        time_log_file('course_view.php_get_string.log.php');
        time_log_file('admin_index.php_old_get_string.log.php');
        time_log_file('course_view.php_old_get_string.log.php');

        test_one_case('info', '', null);
        test_one_case('attemptquiznow', 'quiz', null);
        $a = new stdClass;
        $a->firstname = 'Martin';
        $a->lastname = 'Dougiamas';
        test_one_case('fullnamedisplay', '', $a);
        test_one_case('stringthatdoesnotexistinanyfile', 'qtype_shortanswer', null);
    }

} catch(Exception $e) { // Did they really leave finally out of PHP?
    if (is_null($originalsessionlang)) {
        unset($SESSION->lang);
    } else {
        $SESSION->lang = $originalsessionlang;
    }
}

echo $OUTPUT->footer();

/**
 * plays back one of the files recored by turning on the logging option in string_manager.
 * @param $filename
 * @return unknown_type
 */
function time_log_file($filename) {
    global $CFG, $OUTPUT;
    echo $OUTPUT->heading("Playing back calls from $filename", 3);
    $fullpath = $CFG->libdir . '/simpletest/get_string_fixtures/pagelogs/' . $filename;
    for ($i = 0; $i < NUM_REPITITIONS; ++$i) {
        set_time_limit(60);
        $startime = microtime(true);
        include($fullpath);
        $duration = microtime(true) - $startime;
        echo '<p>Time for ' . $filename . ': <b>' . format_float($duration, 3) . "s</b>.</p>\n";
        flush();
    }
}

/**
 * Repeat one call to get_string NUM_CALLS times.
 * @param $string
 * @param $module
 * @param $a
 */
function test_one_case($string, $module, $a) {
    global $OUTPUT;
    echo $OUTPUT->heading("get_string('$string', '$module', " . print_r($a, true) . ")", 3);
    echo '<p>Resulting string: ' . get_string($string, $module, $a) . "</p>\n";
    for ($i = 0; $i < NUM_REPITITIONS; ++$i) {
        set_time_limit(60);
        $startime = microtime(true);
        for ($j = 0; $j < NUM_CALLS; $j++) {
            get_string($string, $module, $a);
        }
        $duration = microtime(true) - $startime;
        print_result_line($duration, 'calls to get_string');
    }
}

function time_for_loop() {
    global $OUTPUT;
    echo $OUTPUT->heading('Timing an empty for loop');
    $startime = microtime(true);
    for ($i = 0; $i < NUM_CALLS; $i++) {
    }
    $duration = microtime(true) - $startime;
    print_result_line($duration, 'trips through an empty for loop', 'iterations per second');
}

function simple_eval($string, $module, $a) {
    $result = '';
    $code = '$result = "Language string";';
    if (eval($code) === FALSE) { // Means parse error.
        debugging('Parse error while trying to load string "'.$identifier.'" from file "' . $langfile . '".', DEBUG_DEVELOPER);
    }
    return $result;
}
function simple_eval_with_interp($string, $module, $a) {
    $result = '';
    $code = '$result = "Language string $a->field.";';
    if (eval($code) === FALSE) { // Means parse error.
        debugging('Parse error while trying to load string "'.$identifier.'" from file "' . $langfile . '".', DEBUG_DEVELOPER);
    }
    return $result;
}
function sprintf_eval($string, $module, $a) {
    $result = '';
    $code = '$result = sprintf("Language string");';
    if (eval($code) === FALSE) { // Means parse error.
        debugging('Parse error while trying to load string "'.$identifier.'" from file "' . $langfile . '".', DEBUG_DEVELOPER);
    }
    return $result;
    }
function sprintf_eval_with_interp($string, $module, $a) {
    $result = '';
    $code = '$result = sprintf("Language string $a->field.");';
    if (eval($code) === FALSE) { // Means parse error.
        debugging('Parse error while trying to load string "'.$identifier.'" from file "' . $langfile . '".', DEBUG_DEVELOPER);
    }
    return $result;
}
function strtr_eval($string, $module, $a) {
    $result = '';
    $code = '$result = strtr("Language string", "", "");';
    if (eval($code) === FALSE) { // Means parse error.
        debugging('Parse error while trying to load string "'.$identifier.'" from file "' . $langfile . '".', DEBUG_DEVELOPER);
    }
    return $result;
}
function strtr_eval_with_interp($string, $module, $a) {
    $result = '';
    $code = '$result = strtr("Language string $a->field.", "", "");';
    if (eval($code) === FALSE) { // Means parse error.
        debugging('Parse error while trying to load string "'.$identifier.'" from file "' . $langfile . '".', DEBUG_DEVELOPER);
    }
    return $result;
}
function proposed($string, $module, $a) {
    $result = 'Language string';
    if (strpos($result, '$') !== false) {
        $code = '$result = strtr("' . $result . '", "", "");';
        if (eval($code) === FALSE) { // Means parse error.
            debugging('Parse error while trying to load string "'.$identifier.'" from file "' . $langfile . '".', DEBUG_DEVELOPER);
        }
    }
    return $result;
}
function proposed_with_interp($string, $module, $a) {
    $result = 'Language string $a->field.';
    if (strpos($result, '$') !== false) {
        $code = '$result = strtr("' . $result . '", "", "");';
        if (eval($code) === FALSE) { // Means parse error.
            debugging('Parse error while trying to load string "'.$identifier.'" from file "' . $langfile . '".', DEBUG_DEVELOPER);
        }
    }
    return $result;
}
function dummy_function($string, $module, $a) {
}
function time_function_call($functionname) {
    $string = 'string';
    $module = 'module';
    $a = new stdClass;
    $a->field = 'value';
    $startime = microtime(true);
    for ($i = 0; $i < NUM_CALLS; $i++) {
        $functionname($string, $module, $a);
    }
    $duration = microtime(true) - $startime;
    print_result_line($duration, 'calls to ' . $functionname);
}

function print_result_line($duration, $action1, $action2 = 'calls per second') {
    echo '<p>Time for ' . format_float(NUM_CALLS, 0) . ' ' . $action1 . ': <b>' .
            format_float($duration, 3) . 's</b> which is ' .
            format_float((NUM_CALLS / $duration), 0) . ' ' . $action2 . ".</p>\n";
    flush();
}

// =============================================================================
// The rest of this file is the old implementation of get_string, with get_string
// renamed to old_get_string, so we can do comparative timings.

/**
 * fix up the optional data in get_string()/print_string() etc
 * ensure possible sprintf() format characters are escaped correctly
 * needs to handle arbitrary strings and objects
 * @param mixed $a An object, string or number that can be used
 * @return mixed the supplied parameter 'cleaned'
 */
function clean_getstring_data( $a ) {
    if (is_string($a)) {
        return str_replace( '%','%%',$a );
    }
    elseif (is_object($a)) {
        $a_vars = get_object_vars( $a );
        $new_a_vars = array();
        foreach ($a_vars as $fname => $a_var) {
            $new_a_vars[$fname] = clean_getstring_data( $a_var );
        }
        return (object)$new_a_vars;
    }
    else {
        return $a;
    }
}

/**
 * @return array places to look for lang strings based on the prefix to the
 * module name. For example qtype_ in question/type. Used by get_string and
 * help.php.
 */
function places_to_search_for_lang_strings() {
    global $CFG;

    return array(
        '__exceptions' => array('moodle', 'langconfig'),
        'assignment_' => array('mod/assignment/type'),
        'auth_' => array('auth'),
        'block_' => array('blocks'),
        'datafield_' => array('mod/data/field'),
        'datapreset_' => array('mod/data/preset'),
        'enrol_' => array('enrol'),
        'filter_' => array('filter'),
        'format_' => array('course/format'),
        'quiz_' => array('mod/quiz/report'),
        'qtype_' => array('question/type'),
        'qformat_' => array('question/format'),
        'report_' => array($CFG->admin.'/report', 'course/report'),
        'repository_'=>array('repository'),
        'resource_' => array('mod/resource/type'),
        'gradereport_' => array('grade/report'),
        'gradeimport_' => array('grade/import'),
        'gradeexport_' => array('grade/export'),
        'profilefield_' => array('user/profile/field'),
        'portfolio_' => array('portfolio'),
        '' => array('mod')
    );
}

/**
 * Returns a localized string.
 *
 * Returns the translated string specified by $identifier as
 * for $module.  Uses the same format files as STphp.
 * $a is an object, string or number that can be used
 * within translation strings
 *
 * eg "hello \$a->firstname \$a->lastname"
 * or "hello \$a"
 *
 * If you would like to directly echo the localized string use
 * the function {@link print_string()}
 *
 * Example usage of this function involves finding the string you would
 * like a local equivalent of and using its identifier and module information
 * to retrive it.<br/>
 * If you open moodle/lang/en/moodle.php and look near line 1031
 * you will find a string to prompt a user for their word for student
 * <code>
 * $string['wordforstudent'] = 'Your word for Student';
 * </code>
 * So if you want to display the string 'Your word for student'
 * in any language that supports it on your site
 * you just need to use the identifier 'wordforstudent'
 * <code>
 * $mystring = '<strong>'. get_string('wordforstudent') .'</strong>';
or
 * </code>
 * If the string you want is in another file you'd take a slightly
 * different approach. Looking in moodle/lang/en/calendar.php you find
 * around line 75:
 * <code>
 * $string['typecourse'] = 'Course event';
 * </code>
 * If you want to display the string "Course event" in any language
 * supported you would use the identifier 'typecourse' and the module 'calendar'
 * (because it is in the file calendar.php):
 * <code>
 * $mystring = '<h1>'. get_string('typecourse', 'calendar') .'</h1>';
 * </code>
 *
 * As a last resort, should the identifier fail to map to a string
 * the returned string will be [[ $identifier ]]
 *
 * @uses $CFG
 * @param string $identifier The key identifier for the localized string
 * @param string $module The module where the key identifier is stored,
 *      usually expressed as the filename in the language pack without the
 *      .php on the end but can also be written as mod/forum or grade/export/xls.
 *      If none is specified then moodle.php is used.
 * @param mixed $a An object, string or number that can be used
 *      within translation strings
 * @param array $extralocations DEPRICATED. An array of strings with other
 *      locations to look for string files. This used to be used by plugins so
 *      they could package their language strings in the plugin folder, however,
 *      There is now a better way to achieve this. See
 *      http://docs.moodle.org/en/Development:Places_to_search_for_lang_strings.
 * @return string The localized string.
 */
function old_get_string($identifier, $module='', $a=NULL, $extralocations=NULL) {
    global $CFG;

/// originally these special strings were stored in moodle.php now we are only in langconfig.php
    $langconfigstrs = array('alphabet', 'backupnameformat', 'decsep', 'firstdayofweek', 'listsep', 'locale',
                            'localewin', 'localewincharset', 'oldcharset', 'parentlanguage',
                            'strftimedate', 'strftimedateshort', 'strftimedatefullshort', 'strftimedatetime',
                            'strftimedaydate', 'strftimedaydatetime', 'strftimedayshort', 'strftimedaytime',
                            'strftimemonthyear', 'strftimerecent', 'strftimerecentfull', 'strftimetime',
                            'thischarset', 'thisdirection', 'thislanguage', 'strftimedatetimeshort', 'thousandssep');

    $filetocheck = 'langconfig.php';
    $defaultlang = 'en_utf8';
    if (in_array($identifier, $langconfigstrs)) {
        $module = 'langconfig';  //This strings are under langconfig.php for 1.6 lang packs
    }

    $lang = current_language();

    if ($module == '') {
        $module = 'moodle';
    }

/// If the "module" is actually a pathname, then automatically derive the proper module name
    if (strpos($module, '/') !== false) {
        $modulepath = split('/', $module);

        switch ($modulepath[0]) {

            case 'mod':
                $module = $modulepath[1];
            break;

            case 'blocks':
            case 'block':
                $module = 'block_'.$modulepath[1];
            break;

            case 'enrol':
                $module = 'enrol_'.$modulepath[1];
            break;

            case 'format':
                $module = 'format_'.$modulepath[1];
            break;

            case 'grade':
                $module = 'grade'.$modulepath[1].'_'.$modulepath[2];
            break;
        }
    }

/// if $a happens to have % in it, double it so sprintf() doesn't break
    if ($a) {
        $a = clean_getstring_data( $a );
    }

/// Define the two or three major locations of language strings for this module
    $locations = array();

    if (!empty($extralocations)) {
        // This is an old, deprecated mechanism that predates the
        // places_to_search_for_lang_strings mechanism that comes later in
        // this function. So tell people who use it to change.
        debugging('The fourth, $extralocations parameter to get_string is deprecated. ' .
                'See http://docs.moodle.org/en/Development:Places_to_search_for_lang_strings ' .
                'for a better way to package language strings with your plugin.', DEBUG_DEVELOPER);
        if (is_array($extralocations)) {
            $locations += $extralocations;
        } else if (is_string($extralocations)) {
            $locations[] = $extralocations;
        } else {
            debugging('Bad lang path provided');
        }
    }

    if (!empty($CFG->running_installer) and $lang !== 'en_utf8') {
        static $stringnames = null;
        if (!$stringnames) {
            $stringnames = file($CFG->dirroot.'/install/stringnames.txt');
            $stringnames = array_map('trim', $stringnames);
        }
        if (array_search($identifier, $stringnames) !== false) {
            $module = 'installer';
            $filetocheck = 'installer.php';
            $defaultlang = 'en_utf8';
            $locations[] = $CFG->dirroot.'/install/lang/';
        }
    }

    $locations[] = $CFG->dataroot.'/lang/';
    $locations[] = $CFG->dirroot.'/lang/';
    $locations[] = $CFG->dirroot.'/local/lang/';

/// Add extra places to look for strings for particular plugin types.
    $rules = places_to_search_for_lang_strings();
    $exceptions = $rules['__exceptions'];
    unset($rules['__exceptions']);

    if (!in_array($module, $exceptions)) {
        $dividerpos = strpos($module, '_');
        if ($dividerpos === false) {
            $type = '';
            $plugin = $module;
        } else {
            $type = substr($module, 0, $dividerpos + 1);
            $plugin = substr($module, $dividerpos + 1);
        }
        if (!empty($rules[$type])) {
            foreach ($rules[$type] as $location) {
                $locations[] = $CFG->dirroot . "/$location/$plugin/lang/";
            }
        }
    }

/// First check all the normal locations for the string in the current language
    $resultstring = '';
    foreach ($locations as $location) {
        $locallangfile = $location.$lang.'_local'.'/'.$module.'.php';    //first, see if there's a local file
        if (file_exists($locallangfile)) {
            if ($result = get_string_from_file($identifier, $locallangfile, "\$resultstring")) {
                if (eval($result) === FALSE) {
                    trigger_error('Lang error: '.$identifier.':'.$locallangfile, E_USER_NOTICE);
                }
                return $resultstring;
            }
        }
        //if local directory not found, or particular string does not exist in local direcotry
        $langfile = $location.$lang.'/'.$module.'.php';
        if (file_exists($langfile)) {
            if ($result = get_string_from_file($identifier, $langfile, "\$resultstring")) {
                if (eval($result) === FALSE) {
                    trigger_error('Lang error: '.$identifier.':'.$langfile, E_USER_NOTICE);
                }
                return $resultstring;
            }
       }
    }

/// If the preferred language was English (utf8) we can abort now
/// saving some checks beacuse it's the only "root" lang
    if ($lang == 'en_utf8') {
        return '[['. $identifier .']]';
    }

/// Is a parent language defined?  If so, try to find this string in a parent language file

    foreach ($locations as $location) {
        $langfile = $location.$lang.'/'.$filetocheck;
        if (file_exists($langfile)) {
            if ($result = get_string_from_file('parentlanguage', $langfile, "\$parentlang")) {
                if (eval($result) === FALSE) {
                    trigger_error('Lang error: '.$identifier.':'.$langfile, E_USER_NOTICE);
                }
                if (!empty($parentlang) and strpos($parentlang, '<') === false) {   // found it!

                    //first, see if there's a local file for parent
                    $locallangfile = $location.$parentlang.'_local'.'/'.$module.'.php';
                    if (file_exists($locallangfile)) {
                        if ($result = get_string_from_file($identifier, $locallangfile, "\$resultstring")) {
                            if (eval($result) === FALSE) {
                                trigger_error('Lang error: '.$identifier.':'.$locallangfile, E_USER_NOTICE);
                            }
                            return $resultstring;
                        }
                    }

                    //if local directory not found, or particular string does not exist in local direcotry
                    $langfile = $location.$parentlang.'/'.$module.'.php';
                    if (file_exists($langfile)) {
                        if ($result = get_string_from_file($identifier, $langfile, "\$resultstring")) {
                            eval($result);
                            return $resultstring;
                        }
                    }
                }
            }
        }
    }

/// Our only remaining option is to try English

    foreach ($locations as $location) {
        $locallangfile = $location.$defaultlang.'_local/'.$module.'.php';    //first, see if there's a local file
        if (file_exists($locallangfile)) {
            if ($result = get_string_from_file($identifier, $locallangfile, "\$resultstring")) {
                eval($result);
                return $resultstring;
            }
        }

        //if local_en not found, or string not found in local_en
        $langfile = $location.$defaultlang.'/'.$module.'.php';

        if (file_exists($langfile)) {
            if ($result = get_string_from_file($identifier, $langfile, "\$resultstring")) {
                eval($result);
                return $resultstring;
            }
        }
    }

/// And, because under 1.6 en is defined as en_utf8 child, me must try
/// if it hasn't been queried before.
    if ($defaultlang  == 'en') {
        $defaultlang = 'en_utf8';
        foreach ($locations as $location) {
            $locallangfile = $location.$defaultlang.'_local/'.$module.'.php';    //first, see if there's a local file
            if (file_exists($locallangfile)) {
                if ($result = get_string_from_file($identifier, $locallangfile, "\$resultstring")) {
                    eval($result);
                    return $resultstring;
                }
            }

            //if local_en not found, or string not found in local_en
            $langfile = $location.$defaultlang.'/'.$module.'.php';

            if (file_exists($langfile)) {
                if ($result = get_string_from_file($identifier, $langfile, "\$resultstring")) {
                    eval($result);
                    return $resultstring;
                }
            }
        }
    }

    return '[['.$identifier.']]';  // Last resort
}

/**
 * This function is only used from {@link get_string()}.
 *
 * @internal Only used from get_string, not meant to be public API
 * @param string $identifier ?
 * @param string $langfile ?
 * @param string $destination ?
 * @return string|false ?
 * @staticvar array $strings Localized strings
 * @access private
 * @todo Finish documenting this function.
 */
function get_string_from_file($identifier, $langfile, $destination) {

    static $strings;    // Keep the strings cached in memory.

    if (empty($strings[$langfile])) {
        $string = array();
        include ($langfile);
        $strings[$langfile] = $string;
    } else {
        $string = &$strings[$langfile];
    }

    if (!isset ($string[$identifier])) {
        return false;
    }

    return $destination .'= sprintf("'. $string[$identifier] .'");';
}


