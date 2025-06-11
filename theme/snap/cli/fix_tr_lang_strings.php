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
 * @copyright  Copyright (c) 2020 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');

// CLI options.
list($options, $unrecognized) = cli_get_params(
    [
        'help'    => false,
        'verbose' => false,
    ],
    [
        'h' => 'help',
        'v' => 'verbose',
    ]
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}


if (!empty($options['help'])) {
    echo "Overwrites the file lang/tr/theme_snap.php with a fixed version for tr language.

Options:
-h, --help    Print out this help.
-v, --verbose Print incongruencies details.

Example:
$ /usr/bin/php theme/snap/cli/fix_tr_lang_string.php" . PHP_EOL;
    die;
}

$strfile = file_get_contents("./trstrings.json");
$stringsarr = json_decode($strfile, true);

// Array is contained in "Strings" attribute.
$stringsarr = $stringsarr['Strings'];

$string = [];
$replaced = [];
$langfilelocation = $CFG->dirroot . '/theme/snap/lang/tr/theme_snap.php';
require_once($langfilelocation);

foreach ($stringsarr as $stringitem) {
    $stringid = $stringitem['Stringid'];
    $stringmaster = $stringitem['Master'];
    $stringlocal = $stringitem['Local'];

    if ($string[$stringid] !== $stringlocal) {
        $stringitem['Master'] = $string[$stringid];
        $string[$stringid] = $stringlocal;
        $replaced[] = $stringitem;
    }
}

if (empty($replaced)) {
    echo '[INFO] Strings are already up to date, tr lang file will not be replaced.' . PHP_EOL;
    exit;
}

$strfilecontents = <<<PHP
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
 * @copyright  Copyright (c) 2024 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


PHP;

foreach ($string as $key => $str) {
    $strfilecontents .= '$string[\'' . $key . '\'] = \'' . addcslashes($str, "'") . '\';' . PHP_EOL;
}

file_put_contents($langfilelocation, $strfilecontents);

echo '[INFO] Replaced lang file, found ' . count($replaced) . ' incongruencies in ' . $langfilelocation . '.' . PHP_EOL;

if (!empty($options['verbose'])) {
    foreach ($replaced as $stringitem) {
        $stringid = $stringitem['Stringid'];
        $stringmaster = $stringitem['Master'];
        $stringlocal = $stringitem['Local'];
        echo "[INFO] Replaced string \"$stringid\".
Original: \"$stringmaster\".
Replacement: \"$stringlocal\"
";
    }
}
