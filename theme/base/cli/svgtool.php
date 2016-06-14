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
 * This script implements some useful svg manipulation tricks.
 *
 * @package    theme_base
 * @subpackage cli
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir.'/clilib.php');

// Now get cli options.
list($options, $unrecognized) = cli_get_params(array('help'=>false, 'ie9fix'=>false, 'noaspectratio'=>false, 'path'=>$CFG->dirroot),
    array('h'=>'help'));

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

// If necessary add files that should be ignored - such as in 3rd party plugins.
$blacklist = array();
$path = $options['path'];
if (!file_exists($path)) {
    cli_error("Invalid path $path");
}

if ($options['ie9fix']) {
    theme_base_recurse_svgs($path, '', 'theme_base_svgtool_ie9fix', $blacklist);

} else if ($options['noaspectratio']) {
    theme_base_recurse_svgs($path, '', 'theme_base_svgtool_noaspectratio', $blacklist);

} else {
    $help =
        "Some svg image tweaks for icon designers.

Options:

-h, --help            Print out this help
--ie9fix              Adds preserveAspectRatio=\"xMinYMid meet\" to every svg image
--noaspectratio       Removes preserveAspectRatio from svg files
--path=PATH           Path to directory or file to be converted, by default \$CFG->dirroot

Examples:
\$ php svgtool.php --ie9fix
\$ php svgtool.php --ie9fix --path=../../../pix
\$ php svgtool.php --noaspectratio
";

    echo $help;
    die;
}

exit(0);

/**
 * Fixes SVG images for IE9.
 *
 * @param string $file
 */
function theme_base_svgtool_ie9fix($file) {
    global $CFG;

    if (strpos($file, $CFG->dirroot.DIRECTORY_SEPARATOR) === 0) {
        $relfile = substr($file, strlen($CFG->dirroot));
    } else {
        $relfile = $file;
    }

    $content = file_get_contents($file);

    if (!preg_match('/<svg\s[^>]*>/', $content, $matches)) {
        echo "  skipping $relfile (invalid format)\n";
        return;
    }
    $svg = $matches[0];
    if (strpos($svg, 'preserveAspectRatio') !== false) {
        return;
    }

    if (!is_writable($file)) {
        echo "  skipping $relfile (can not modify file)\n";
        return;
    }

    $newsvg = rtrim($svg, '>').' preserveAspectRatio="xMinYMid meet">';

    $content = str_replace($svg, $newsvg, $content);
    echo "converting $relfile\n";
    file_put_contents($file, $content);
}

/**
 * Removes preserveAspectRatio attributes from SVG images.
 *
 * @param string $file
 */
function theme_base_svgtool_noaspectratio($file) {
    global $CFG;

    if (strpos($file, $CFG->dirroot.DIRECTORY_SEPARATOR) === 0) {
        $relfile = substr($file, strlen($CFG->dirroot));
    } else {
        $relfile = $file;
    }

    $content = file_get_contents($file);

    if (!preg_match('/<svg\s[^>]*>/', $content, $matches)) {
        echo "  skipping $relfile (invalid format)\n";
        return;
    }
    $svg = $matches[0];
    if (strpos($svg, 'preserveAspectRatio="xMinYMid meet"') === false) {
        return;
    }

    if (!is_writable($file)) {
        echo "  skipping $relfile (can not modify file)\n";
        return;
    }

    $newsvg = preg_replace('/ ?preserveAspectRatio="xMinYMid meet"/', '', $svg);

    $content = str_replace($svg, $newsvg, $content);
    echo "resetting $relfile\n";
    file_put_contents($file, $content);
}

/**
 * Recursively works through directories of this theme, finding and fixing SVG images.
 *
 * @param string $base
 * @param string $sub
 * @param string $filecallback
 * @param array $blacklist
 */
function theme_base_recurse_svgs($base, $sub, $filecallback, $blacklist) {
    if (is_dir("$base/$sub")) {
        $items = new DirectoryIterator("$base/$sub");
        foreach ($items as $item) {
            if ($item->isDot()) {
                continue;
            }
            $file = $item->getFilename();
            theme_base_recurse_svgs("$base/$sub", $file, $filecallback, $blacklist);
        }
        unset($item);
        unset($items);
        return;

    } else if (is_file("$base/$sub")) {
        if (substr($sub, -4) !== '.svg') {
            return;
        }
        $file = realpath("$base/$sub");
        if (in_array($file, $blacklist)) {
            return;
        }
        $filecallback($file);
    }
}
