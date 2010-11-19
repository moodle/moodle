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
 * @package    editor
 * @subpackage tinymce
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);
require_once dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/config.php';

if (!debugging('', DEBUG_DEVELOPER)) {
    die('Only for developers!!!!!');
}

$langconversion = array(
    // mapping of TinyMCE lang codes to Moodle codes
    'nb'    => 'no',
    'sr'    => 'sr_lt',

    // ignore the following files due to known errors
    'ch'    => false,   // XML parsing error, Moodle does not seem to have Chamorro yet anyway
    'zh'    => false,   // XML parsing error, use 'zh' => 'zh_tw' when sorted out
);

$targetlangdir = "$CFG->dirroot/lib/editor/tinymce/extra/tools/temp/langs"; // change if needed
$tempdir       = "$CFG->dirroot/lib/editor/tinymce/extra/tools/temp/tinylangs";
$enfile        = "$CFG->dirroot/lib/editor/tinymce/lang/en/editor_tinymce.php";


/// first update English lang pack
if (!file_exists("$tempdir/en.xml")) {
    die('Missing temp/tinylangs/en.xml! Did you download langs?');
}
$old_strings = editor_tinymce_get_all_strings('en');
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

$header = <<<EOT
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
 * Strings for component 'editor_tinymce', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package    editor
 * @subpackage tinymce
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

EOT;

fwrite($handle, $header);

fwrite($handle, "\n\n//== Custom Moodle strings that are not part of upstream TinyMCE ==\n");
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
$en_strings = editor_tinymce_get_all_strings('en');
if (!file_exists($targetlangdir)) {
    echo "Can not find target lang dir: $targetlangdir !!";
}

$xmlfiles = new DirectoryIterator($tempdir);
foreach ($xmlfiles as $xmlfile) {
    if ($xmlfile->isDot() or $xmlfile->isLink() or $xmlfile->isDir()) {
        continue;
    }

    $filename = $xmlfile->getFilename();

    if ($filename == 'en.xml') {
        continue;
    }
    if (substr($filename, -4) !== '.xml') {
        continue;
    }

    $xmllang = substr($filename, 0, strlen($filename) - 4);

    echo "Processing $xmllang ...\n";

    if (array_key_exists($xmllang, $langconversion)) {
        $lang = $langconversion[$xmllang];
        if (empty($lang)) {
            echo "  Ignoring: $xmllang\n";
            continue;
        } else {
            echo "  Mapped to: $lang\n";
        }
    } else {
        $lang = $xmllang;
    }

    $langfile = "$targetlangdir/$lang/editor_tinymce.php";
    if (!file_exists(dirname($langfile))) {
        mkdir(dirname($langfile), 0755, true);
    }

    if (file_exists($langfile)) {
        unlink($langfile);
    }

    $parsed = editor_tinymce_parse_xml_lang("$tempdir/$xmlfile");
    ksort($parsed);

    if (!$handle = fopen($langfile, 'w')) {
         echo "*** Cannot write to $langfile !!\n";
         continue;
    }

    fwrite($handle, "<?php\n\n// upload this file into the AMOS stage, rebase the stage, review the changes and commit\n");
    foreach ($parsed as $key=>$value) {
        fwrite($handle, editor_tinymce_encode_stringline($key, $value));
    }

    fclose($handle);
}
unset($xmlfiles);


die("\nFinished!\n\n");







/// ============ Utility functions ========================

function editor_tinymce_encode_stringline($key, $value) {
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
            //undo quoted stuff
            $value = str_replace('\n', "\n", $value);
            $value = str_replace('\'', "'", $value);
            $value = str_replace('\\\\', '\\', $value);
            $result["$section:$name"] = $value;
        }
    }
    return $result;
}

function editor_tinymce_get_all_strings($lang) {
    $sm = get_string_manager();
    return $sm->load_component_strings('editor_tinymce', $lang);
}
