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
 * url_finder cli script. Examines DB for non-https src or data links, and lists them.
 *
 * @package    tool_httpsreplace
 * @copyright Copyright (c) 2016 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);
require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir.'/clilib.php');

list($options, $unrecognized) = cli_get_params(array('help' => false), array('h' => 'help'));
if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized), 2);
}
if ($options['help']) {
    $help = "List of http (not https) urls on a site in the DB
Options:
-h, --help            Print out this help
Example:
\$sudo -u www-data /usr/bin/php admin/tool/httpsreplace/cli/url_finder.php  \n";
    echo $help;
    exit(0);
}

$urlfinder = new \tool_httpsreplace\url_finder();
$results = $urlfinder->http_link_stats();
$fp = fopen('php://stdout', 'w');
fputcsv($fp, ['clientsite', 'httpdomain', 'urlcount']);
foreach ($results as $domain => $count) {
    fputcsv($fp, [$SITE->shortname, $domain, $count]);
}
fclose($fp);
