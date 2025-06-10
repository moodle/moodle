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
 *  tool_crawler cli
 *
 * @package    tool_crawler
 * @author     Brendan Heywood <brendan@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.php');
require_once($CFG->libdir.'/clilib.php');
require_once($CFG->dirroot .'/admin/tool/crawler/lib.php');

list($options, $unrecognized) = cli_get_params(array(
        'help'      => false,
        'verbose'   => 1,
),
        array(
                'h' => 'help'
        ));

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help']) {
    echo get_string('clicrawlerhelp', 'tool_crawler');
    die();
}

if ($options['verbose'] && (!is_numeric($options['verbose']) || $options['verbose'] < 0 || $options['verbose'] > 2)) {
    echo get_string('clicrawlerhelp', 'tool_crawler');
    die();
}

tool_crawler_crawl($options['verbose']);

