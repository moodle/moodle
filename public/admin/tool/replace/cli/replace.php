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
 * Search and replace strings throughout all texts in the whole database.
 *
 * @package    tool_replace
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../../../config.php');
require_once($CFG->libdir.'/clilib.php');
require_once($CFG->libdir.'/adminlib.php');

$help =
    "Search and replace text throughout the whole database.

Options:
--search=STRING       String to search for.
--replace=STRING      String to replace with.
--skiptables=STRING   Skip these tables (comma separated list of tables).
--shorten             Shorten result if necessary.
--non-interactive     Perform the replacement without confirming.
-h, --help            Print out this help.

Example:
\$ sudo -u www-data /usr/bin/php admin/tool/replace/cli/replace.php --search=//oldsitehost --replace=//newsitehost
";

list($options, $unrecognized) = cli_get_params(
    array(
        'search'  => null,
        'replace' => null,
        'skiptables' => '',
        'shorten' => false,
        'non-interactive' => false,
        'help'    => false,
    ),
    array(
        'h' => 'help',
    )
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

// Ensure that user has populated both the search/replace parameters.
if ($options['help'] || !is_string($options['search']) || !is_string($options['replace'])) {
    echo $help;
    exit(0);
}

if (!$DB->replace_all_text_supported()) {
    cli_error(get_string('notimplemented', 'tool_replace'));
}

if (empty($options['shorten']) && core_text::strlen($options['search']) < core_text::strlen($options['replace'])) {
    cli_error(get_string('cannotfit', 'tool_replace'));
}

try {
    $search = validate_param($options['search'], PARAM_RAW);
    $replace = validate_param($options['replace'], PARAM_RAW);
    $skiptables = validate_param($options['skiptables'], PARAM_RAW);
} catch (invalid_parameter_exception $e) {
    cli_error(get_string('invalidcharacter', 'tool_replace'));
}

if (!$options['non-interactive']) {
    echo get_string('excludedtables', 'tool_replace') . "\n\n";
    echo get_string('notsupported', 'tool_replace') . "\n\n";
    $prompt = get_string('cliyesnoprompt', 'admin');
    $input = cli_input($prompt, '', array(get_string('clianswerno', 'admin'), get_string('cliansweryes', 'admin')));
    if ($input == get_string('clianswerno', 'admin')) {
        exit(1);
    }
}

if (!db_replace($search, $replace, $skiptables)) {
    cli_heading(get_string('error'));
    exit(1);
}

cli_heading(get_string('success'));
exit(0);
