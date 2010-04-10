<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This script imports TinyMCE lang strings into Moodle lang packs.
 *
 * @package    moodlecore
 * @subpackage editor
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (isset($_SERVER['REMOTE_ADDR'])) { // this script is accessed only via command line
    die;
}

require_once dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/config.php';

if (!debugging('', DEBUG_DEVELOPER)) {
    die('Only for developers!!!!!');
}

// mapping of Moodle langs to TinyMCE langs
$langconversion = array(
    'no'    => 'nb',
    'ko'    => false,  // ignore Korean translation for now - does not parse
    'sr_lt' => false,  //'sr_lt' => 'sr' ignore the Serbian translation
    'zh_tw' => 'zh',
);

$targetlangdir = "$CFG->dirroot/../lang"; // change if needed
$tempdir       = "$CFG->dirroot/lib/editor/tinymce/extra/tools/temp";
$enfile        = "$CFG->dirroot/lang/en/editor_tinymce.php";


/// first update English lang pack
if (!file_exists("$tempdir/en.xml")) {
    die('Missing temp/en.xml! Did you download langs?');
}
$old_strings = editor_tinymce_get_all_strings($enfile);
ksort($old_strings);

// our modifications and upstream changes in existing strings
$tweaked = array();

//detect changes and new additions
$parsed = editor_tinymce_parse_xml_lang("$tempdir/en.xml");
ksort($parsed);
foreach ($parsed as $key=>$value) {
    if (array_key_exists($key, $old_strings)) {
        $oldvalue = $old_strings[$key];
        if ($oldvalue !== $value) {
            $tweaked[$key] = $oldvalue;
        }
        unset($old_strings[$key]);
    }
}

if (!$handle = fopen($enfile, 'w')) {
     echo "Cannot write to $filename !!";
     exit;
}

fwrite($handle, "<?php\n\n//== Custom Moodle strings that are not part of upstream TinyMCE ==\n");
foreach ($old_strings as $key=>$value) {
    fwrite($handle, editor_tinymce_encode_stringline($key, $value));
}

fwrite($handle, "\n\n// == TinyMCE upstream lang strings from all plugins ==\n");
foreach ($parsed as $key=>$value) {
    fwrite($handle, editor_tinymce_encode_stringline($key, $value));
}

if ($tweaked) {
    fwrite($handle, "\n\n// == Our modifications or upstream changes ==\n");
    foreach ($tweaked as $key=>$value) {
        fwrite($handle, editor_tinymce_encode_stringline($key, $value));
    }
}

fclose($handle);


//now update all other langs
$en_strings = editor_tinymce_get_all_strings($enfile);
if (!file_exists($targetlangdir)) {
    echo "Can not find target lang dir: $targetlangdir !!";
}

$langs = new DirectoryIterator($targetlangdir);
foreach ($langs as $lang) {
    if ($lang->isDot() or $lang->isLink() or !$lang->isDir()) {
        continue;
    }

    $lang = $lang->getFilename();

    if ($lang == 'en' or $lang == 'CVS' or $lang == '.settings') {
        continue;
    }

    $xmllang = $lang;
    if (array_key_exists($xmllang, $langconversion)) {
        $xmllang = $langconversion[$xmllang];
        if (empty($xmllang)) {
            echo "         Ignoring: $lang\n";
            continue;
        }
    }

    $xmlfile = "$tempdir/$xmllang.xml";
    if (!file_exists($xmlfile)) {
        echo "         Skipping: $lang\n";
        continue;
    }

    $langfile = "$targetlangdir/$lang/editor_tinymce.php";

    if (file_exists($langfile)) {
        $old_strings = editor_tinymce_get_all_strings($langfile);
        ksort($old_strings);
        foreach ($old_strings as $key=>$value) {
            if (!array_key_exists($key, $en_strings)) {
                unset($old_strings[$key]);
            }
        }
    } else {
        $old_strings = array();
    }

    // our modifications and upstream changes in existing strings
    $tweaked = array();

    //detect changes and new additions
    $parsed = editor_tinymce_parse_xml_lang($xmlfile);
    ksort($parsed);
    foreach ($parsed as $key=>$value) {
        if (array_key_exists($key, $old_strings)) {
            $oldvalue = $old_strings[$key];
            if ($oldvalue !== $value) {
                $tweaked[$key] = $oldvalue;
            }
            unset($old_strings[$key]);
        }
    }

    if (!$handle = fopen($langfile, 'w')) {
         echo "*** Cannot write to $langfile !!\n";
         continue;
    }
    echo " Modifying: $lang\n";

    fwrite($handle, "<?php\n\n//== Custom Moodle strings that are not part of upstream TinyMCE ==\n");
    foreach ($old_strings as $key=>$value) {
        fwrite($handle, editor_tinymce_encode_stringline($key, $value));
    }

    fwrite($handle, "\n\n// == TinyMCE upstream lang strings from all plugins ==\n");
    foreach ($parsed as $key=>$value) {
        fwrite($handle, editor_tinymce_encode_stringline($key, $value));
    }

    if ($tweaked) {
        fwrite($handle, "\n\n// == Our modifications or upstream changes ==\n");
        foreach ($tweaked as $key=>$value) {
            fwrite($handle, editor_tinymce_encode_stringline($key, $value));
        }
    }

    fclose($handle);
}
unset($langs);


die("\nFinished!\n\n");







/// ============ Utility functions ========================

function editor_tinymce_encode_stringline($key, $value) {
        $value = str_replace("%","%%",$value);              // Escape % characters
        return "\$string['$key'] = ".var_export($value, true).";\n";
}

function editor_tinymce_parse_xml_lang($file) {
    $result = array();

    $doc = new DOMDocument();
    $doc->load($file);
    $groups = $doc->getElementsByTagName('group');
    foreach($groups as $group) {
        $section = $group->getAttribute('target');
        $items = $group->getElementsByTagName('item');
        foreach($items as $item) {
            $name  = $item->getAttribute('name');
            $value = $item->textContent;
            $result["$section:$name"] = $value;
        }
    }
    return $result;
}

function editor_tinymce_get_all_strings($file) {
    global $CFG;

    $string = array();
    require($file);

    foreach ($string as $key=>$value) {
        $string[$key] = str_replace("%%","%",$value);              // Unescape % characters
    }

    return $string;
}