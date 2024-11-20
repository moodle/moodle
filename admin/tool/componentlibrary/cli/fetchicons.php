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
 * Moodle Component Library
 *
 * @package    tool_componentlibrary
 * @copyright  2021 Bas Brands <bas@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


define('CLI_SCRIPT', 1);

require_once(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir.'/clilib.php');
require_once($CFG->dirroot . '/lib/filelib.php');

list($options, $unrecognized) = cli_get_params(array(
    'help' => false,
    ), array('h' => 'help'));

if ($options['help']) {
    $help = <<<EOL
Generate a json file with all core icons for the component library.

Options:
-h, --help                  Print out this help.

Example:
\$sudo -u www-data /usr/bin/php admin/tool/componentlibrary/cli/fetchicons.php\n
EOL;
    echo $help;
    die;
}

$admin = get_admin();
if (!$admin) {
    mtrace("Error: No admin account was found");
    die;
}

$CFG->themedesignermode = true;

$output = $PAGE->get_renderer('core');
$isfontawesome = \core\output\icon_system::instance(\core\output\icon_system::FONTAWESOME);
$isstandard = \core\output\icon_system::instance(\core\output\icon_system::STANDARD);
$map = $isfontawesome->get_icon_name_map();
$icons = [];
foreach ($map as $name => $icon) {
    $parts = explode(':', $name);
    if ($parts[0] === 'theme') {
        continue;
    }
    $imageicon = new image_icon($parts[1], $name, $parts[0], []);
    $i = new \stdClass();
    $i->name = $name;
    $i->icon = $icon;
    if ($imageicon) {
        $i->standardicon = str_replace($CFG->wwwroot, 'MOODLESITE', $isstandard->render_pix_icon($output, $imageicon));
    }
    $icons[] = $i;
}
$jsonfile = $CFG->dirroot . '/admin/tool/componentlibrary/hugo/site/data/fontawesomeicons.json';
if (($fh = @fopen($jsonfile, 'w')) !== false) {
    fwrite($fh, json_encode($icons));
    fclose($fh);
    exit(0);
} else {
    echo 'Could not write to ' . $jsonfile;
    exit(1);
}
