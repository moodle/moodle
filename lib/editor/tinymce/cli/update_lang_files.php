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
 * This script imports TinyMCE lang strings into Moodle English lang pack.
 *
 * @package    editor_tinymce
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require __DIR__ . '/../../../../config.php';

if (!$CFG->debugdeveloper) {
    die('Only for developers!!!!!');
}

// Current strings in our lang pack.
$old_strings = editor_tinymce_get_all_strings();
ksort($old_strings);

// Upstream strings.
$parsed = editor_tinymce_parse_js_files();
ksort($parsed);

// Our modifications and upstream changes in existing strings.
$tweaked = array();

// Detect changes and new additions - ignore case difference, no UTF-8 here.
foreach ($parsed as $key=>$value) {
    if (array_key_exists($key, $old_strings)) {
        $oldvalue = $old_strings[$key];
        if (strtolower($oldvalue) === strtolower($value)) {
            $parsed[$key] = $oldvalue;
        } else {
            $tweaked[$key] = $oldvalue;
        }
        unset($old_strings[$key]);
    }
}

if (!$handle = fopen("$CFG->dirroot/lib/editor/tinymce/lang/en/editor_tinymce.php", 'w')) {
     echo "Cannot write to $filename !!";
     exit(1);
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
 * Strings for component 'editor_tinymce', language 'en'.
 *
 * Note: use editor/tinymce/extra/tools/update_lang_files.php script to import strings from upstream JS lang files.
 *
 * @package    editor_tinymce
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

EOT;

fwrite($handle, $header);

fwrite($handle, "\n\n//== Custom Moodle strings that are not part of upstream TinyMCE ==\n");
foreach ($old_strings as $key=>$value) {
    fwrite($handle, editor_tinymce_encode_stringline($key, $value));
}

fwrite($handle, "\n\n// == TinyMCE upstream lang strings from all standard upstream plugins ==\n");
foreach ($parsed as $key=>$value) {
    fwrite($handle, editor_tinymce_encode_stringline($key, $value, isset($tweaked[$key])));
}

if ($tweaked) {
    fwrite($handle, "\n\n// == Our modifications or upstream changes ==\n");
    foreach ($tweaked as $key=>$value) {
        fwrite($handle, editor_tinymce_encode_stringline($key, $value));
    }
}

fclose($handle);

get_string_manager()->reset_caches();
die("\nFinished update of EN lang pack (other langs have to be imported via AMOS)\n\n");



/// ============ Utility functions ========================

function editor_tinymce_encode_stringline($key, $value, $commentedout=false) {
    $return = "\$string['$key'] = ".var_export($value, true).";";
    if ($commentedout) {
        $return = "/* $return */";
    }
    return $return."\n";
}

function editor_tinymce_get_all_strings() {
    $sm = get_string_manager();
    return $sm->load_component_strings('editor_tinymce', 'en', true, true);
}

function editor_tinymce_parse_js_files() {
    global $CFG;

    require_once("$CFG->libdir/editorlib.php");
    $editor = get_texteditor('tinymce');
    $basedir = "$CFG->libdir/editor/tinymce/tiny_mce/$editor->version";

    $files = array();
    $strings = array();

    $files['simple'] = "$basedir/themes/simple/langs/en.js";
    $files['advanced'] = "$basedir/themes/advanced/langs/en.js";
    $files['advanced_dlg'] = "$basedir/themes/advanced/langs/en_dlg.js";

    $items = new DirectoryIterator("$basedir/plugins/");
    foreach ($items as $item) {
        if ($item->isDot() or !$item->isDir()) {
            continue;
        }
        $plugin = $item->getFilename();
        if ($plugin === 'example') {
            continue;
        }
        if (file_exists("$basedir/plugins/$plugin/langs/en.js")) {
            $files[$plugin] = "$basedir/plugins/$plugin/langs/en.js";
        }
        if (file_exists("$basedir/plugins/$plugin/langs/en_dlg.js")) {
            $files[$plugin.'_dlg'] = "$basedir/plugins/$plugin/langs/en_dlg.js";
        }
        unset($item);
    }
    unset($items);

    // It would be too easy if TinyMCE used standard JSON in lang files...

    // Core upstream pack.
    $content = file_get_contents("$basedir/langs/en.js");
    $content = trim($content);
    $content = preg_replace("/^tinyMCE.addI18n\(\{en:/", '', $content);
    $content = preg_replace("/\}\);$/", '', $content);
    $content = preg_replace("/([\{,])([a-zA-Z0-9_]+):/", '$1"$2":', $content);
    $content = preg_replace("/:'([^']*)'/", ':"$1"', $content);
    $content = str_replace("\\'", "'", $content);
    $maindata = json_decode($content, true);

    if (is_null($maindata) or json_last_error() != 0) {
        echo "error processing main lang file\n";
        echo $content."\n\n";
        exit(1);
    }
    foreach($maindata as $component=>$data) {
        foreach ($data as $key=>$value) {
            $strings["$component:$key"] = $value;
        }
    }
    unset($content);
    unset($maindata);

    // Upstream plugins.
    foreach($files as $plugin=>$path) {
        $content = file_get_contents($path);
        $content = trim($content);
        $content = preg_replace("/^tinyMCE\.addI18n\('en\.[a-z09_]*',\s*/", '', $content);
        $content = preg_replace("/\);$/", '', $content);

        $content = preg_replace('/(\{|"\s*,)\s*([a-z0-9_]+)\s*:\s*"/m', '$1"$2":"', $content);
        $content = str_replace("\\'", "'", $content);

        $data = json_decode($content, true);
        if (is_null($data) or json_last_error() != 0) {
            echo "error processing $path lang file\n";
            echo $content."\n\n";
            exit(1);
        }
        foreach ($data as $key=>$value) {
            if ($key === '_empty_') {
                continue;
            }
            $strings["$plugin:$key"] = $value;
        }
    }

    return $strings;
}
