#!/usr/bin/php
<?php

$sec = time();

/**
 * This script creates Moodle langfiles from the translation files distributed
 * by TinyMCE. 
 *
 * I recommend always running it twice - if there's an obvious problem in the 
 * file output, php will throw a parse error. This will prevent feeding really
 * bad files to every moodle site out there...
 */

error_reporting(E_ALL | E_NOTICE);

// this should be the path to a checkout of the translations tree
// the english file will need to be separately committed to all
// relevant cvs checkouts (2.0 and up)
define('MOODLE_LANG_PATH', '/home/mathieu/workspace/lang');

// this should be the path to the unzipped tinymce langpacks
define('LANGPACK_PATH', '/home/mathieu/workspace/tinymce_langs');

// this should be the path to your HEAD checkout - the english translation
// will be saved there.
define('MOODLE_CVSHEAD_PATH', '/home/mathieu/workspace/head');

// language correspondance: codes used in TinyMCE that matches a different 
// code in Moodle. Note that languages without an existing folder are 
// ignored if they are not in this array.
$langmatches = array(
    'sr' => 'sr_lt',
    'zh' => 'zh_tw',
);

/**
 * I don't recommend making this script web-accessible. Really, it should only
 * be run by developers.
 */
if (isset($_SERVER['REMOTE_ADDR'])) { // if the script is accessed via the web.
    exit;
}

// Do it.
import_language_files();

/****************************************************************************
 * Everything's a function.
 */

/**
 * Find the language file for a given language.
 * @param string $lang the language code
 * @return mixed a string to the language file (does not mean the file exists! - just that this is where it should be) or boolean false if the language is invalid for Moodle.
 */
function find_language_file($lang) {
    global $langmatches;

    if (array_key_exists($lang, $langmatches) && ($langmatches[$lang] != $lang)) {
        $lang = $langmatches[$lang];
    }

    if ($lang == 'en') {
        $filepath = MOODLE_CVSHEAD_PATH .'/lang/en_utf8';
    } else {
        $filepath = MOODLE_LANG_PATH .'/'. $lang .'_utf8';
    }

    // These conditions will be ambiguous if a file (not a folder) exists with the name a of language code. 
    // This shouldn't happen though and would be a bug on it's own.
    if (file_exists($filepath) && !is_dir($filepath)) {
        die(" * path $filepath already exists, but is not a folder, impossible to write translations there.\nFix this and try again.");

    } elseif (!is_dir($filepath)) {
        return FALSE;
    }

    return $filepath .'/tinymce.php';
}

/**
 * read_language_file - reads a translation file
 * @param string $lang language-code specifying a translation file to read
 * @return mixed an array, empty if no translation currently exists or boolean false if the language is invalid for Moodle
 */
function read_language_file($lang) {
    $string = array();
    // we load the current translation file, if it exists, so we don't loose old strings.
    $langfile = find_language_file($lang);
    if ($langfile === FALSE) {
        return FALSE;
    }
    if (file_exists($langfile)) {
        include($langfile);
    } 
    return $string;
}

/** 
 * write_language_file
 * @param string $lang language-code specifying the translation file to write
 * @param array $langdata a $string array to be written into the tinymce translation file
 * @return void 
 */
function write_language_file($lang, $langdata) {

    $languagefile = find_language_file($lang);
    if ($languagefile === FALSE) {
        return;
    }

    $file = fopen(find_language_file($lang), 'w');
    fwrite($file, "<?php\n");
    fwrite($file, "/* Note to translators: this file was automatically imported from TinyMCE's \n * translations. Any change to an automatically imported string will be overwritten \n * the next time the import script is run. */\n");

    foreach ($langdata as $id => $line) {
        // the next two lines are there to make you enjoy how php deals with backslashes
        $line = preg_replace_callback('/\\\\u([0-9A-F]{4})/', 'unichr', $line); // we're matching something like \u00E9
        // we're only escaping single quotes, but we gotta prevent escaping those that have already been escaped.
        fwrite($file, '$string[\''. $id ."']='". strtr($line, array('\\\'' => '\\\'', '\'' => '\\\'', '%' => '%%', '$' => '\$')) ."';\n"); 
    }

    fwrite($file, "?>");
    fclose($file);
}

/**
 * Unicode aware version of chr, while we wait for PHP to reach the 21st century.
 * Taken on http://au.php.net/manual/en/function.chr.php#77911
 * 
 * This function expects to be used as callback from preg_replace_callback, so 
 * the only argument is an array.
 */
function unichr($c) {
    $c = hexdec($c[1]);
    if ($c <= 0x7F) {
        $s = chr($c);
    } else if ($c <= 0x7FF) {
        $s = chr(0xC0 | $c >> 6) . chr(0x80 | $c & 0x3F);
    } else if ($c <= 0xFFFF) {
        $s = chr(0xE0 | $c >> 12) . chr(0x80 | $c >> 6 & 0x3F)
            . chr(0x80 | $c & 0x3F);
    } else if ($c <= 0x10FFFF) {
        $s = chr(0xF0 | $c >> 18) . chr(0x80 | $c >> 12 & 0x3F)
            . chr(0x80 | $c >> 6 & 0x3F)
            . chr(0x80 | $c & 0x3F);
    } else {
        return '';
    }
    return $s;
}

function import_language_files() {
    // build file list
    $languagefiles = array();
    $langfolders = array(
        // this array makes is easier to carry enough information to eventually
        // create the xml file TinyMCE wants to provide translation files.
        // funny quoting to prevent problems in vim indentation
           'main' => '/langs/', 
        'plugins' => '/plugins/'.'*/langs/', 
         'themes' => '/themes/'.'*/langs/'
    ); 
    foreach ($langfolders as $moduletype => $folders) {
        foreach (glob(LANGPACK_PATH . $folders .'*.js') as $file) {
            $fileinfo = pathinfo($file);

            $filelang = substr($fileinfo['filename'], 0, 2); // some files are 'lang.js' and some are 'lang_dlg.js'
            $filepath = $fileinfo['dirname'];
            $filename = $fileinfo['basename'];

            $languagefiles[$filelang][$filepath][$moduletype][] = $filename;
        }
    }

    // process the files and import strings
    foreach ($languagefiles as $currentlang => $filepaths) {

        $strings = array();
        $strings = read_language_file($currentlang);
        if ($strings === FALSE) {
            print("$currentlang is not available in Moodle - skipped.\n");
            continue;
        }

        print($filename .' - ');

        if (!empty($strings)) {
            print('loaded '. count($strings) .' current strings - ');
        } else {
            print('currently empty - ');
        }

        $importedstrings = 0;
        foreach ($filepaths as $currentpath => $moduletypes) {
            foreach ($moduletypes as $moduletype => $filenames) {
                foreach ($filenames as $filename) {
                    $subsection = '';

                    $file = file($currentpath .'/'. $filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

                    $lastline = trim(array_pop($file)); // remove section ending line
                    if ($lastline == '});') {
                        $filetype = 'submodule';
                        $currentline = explode("'", array_shift($file));
                        $section = substr($currentline[1], 3) .':'; // remove language code, keep section 
                    } else {
                        $filetype = 'main';
                        $currentline = explode('{', array_shift($file)); 
                        $section = substr($currentline[1], 3); // remove language code, keep section
                    }

                    //print($currentline[1] ."\n");

                    $linenumber = 1;
                    while (!empty($file)) {
                        $currentline = trim(array_shift($file));
                        if (($filetype == 'main') && ($pos = strpos($currentline, ':{')) !== false) { // subsections in main file
                            $subsection = substr($currentline, 0, $pos+1);

                        } elseif (($pos = strpos($currentline, '\',{')) !== false) { // subsection in dialog files
                            $subsection = substr($currentline, 21, $pos) + ',';

                        } elseif ($currentline == '},') { // subsection closing
                            continue;

                        } elseif (($pos = strpos($currentline, ':')) !== false) { // string
                            $stringid = substr($currentline, 0, $pos); 
                            $stringvalue = preg_replace('/^(")(.*)(",?)$/', '\\2', trim(substr($currentline, $pos+1))); 
                            $modulestring = '';
                            if (!empty($moduletype)) {
                                $modulestring = $moduletype .'/';
                            }
                            $strings[$modulestring . $section . $subsection . $stringid] = $stringvalue;
                            $importedstrings++;

                        } else { // wrong line !?
                            print("\n!!! problem in $currentpath/$filename:$linenumber !!!\n"); 
                        }

                        $linenumber++;
                    }
                }
            }
        }

        write_language_file($currentlang, $strings);
        print("imported $importedstrings strings.\n");
    }
    global $sec;
    print("\nIt is suggested you run this script twice. This will include() \nthe generated files once and detect parse errors before you \ncommit the files and wreach havoc on every Moodle site out there. \n\nReally. \n\nIt's quick (this script only took ". (time() - $sec) ." seconds to run), so do it!\n\nThe english translation file was only saved in head. You will need \n\nto copy it to any other necessary checkout manually.\n\n");
}


?>
