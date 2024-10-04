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
 * url_replace cli script. Examines DB for non-https src or data links, and lists broken ones or replaces all links.
 *
 * @package    tool_httpsreplace
 * @copyright Copyright (c) 2016 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);
require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir.'/clilib.php');

list($options, $unrecognized) = cli_get_params(
    array(
        'help' => false,
        'list' => false,
        'replace' => false,
        'confirm' => false,
    ),
    array(
        'h' => 'help',
        'l' => 'list',
        'r' => 'replace',
    )
);
if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized), 2);
}
if ($options['help'] || (!$options['list'] && !$options['replace'])) {
    $help = "Examines DB for non-https src or data links, and lists broken links or replaces all links.
Options:
-h, --help            Print out this help
-l, --list            List of http (not https) urls on a site in the DB that would become broken.
-r, --replace         List of http (not https) urls on a site in the DB that would become broken.
--confirm             Replaces http urls with https across a site's content.
Example:
\$ sudo -u www-data /usr/bin/php admin/tool/httpsreplace/cli/url_replace.php --list \n";
    echo $help;
    exit(0);
}

if (!$DB->replace_all_text_supported()) {
    echo $OUTPUT->notification(get_string('notimplemented', 'tool_httpsreplace'));
    exit(1);
}

if (!is_https()) {
    echo $OUTPUT->notification(get_string('httpwarning', 'tool_httpsreplace'), 'warning');
    echo "\n";
}

if ($options['replace']) {

    if ($options['confirm']) {

        $urlfinder = new \tool_httpsreplace\url_finder();
        $urlfinder->upgrade_http_links();
    } else {
        echo "Once this is tool run, changes made can't be reverted. \n" .
             "A complete backup should be made before running this script. \n\n" .
             "There is a low risk that the wrong content will be replaced, introducing problems. \n" .
             "If you are sure you want to continue, add --confirm\n\n";
    }

} else {

    $urlfinder = new \tool_httpsreplace\url_finder();
    $results = $urlfinder->http_link_stats();
    asort($results);
    $fp = fopen('php://stdout', 'w');
    fputcsv($fp, ['clientsite', 'httpdomain', 'urlcount'], escape: '\\');
    foreach ($results as $domain => $count) {
        fputcsv($fp, [$SITE->shortname, $domain, $count], escape: '\\');
    }
    fclose($fp);
}
