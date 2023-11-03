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
 * CLI script allowing to get and set config values.
 *
 * This is technically just a thin wrapper for {@link get_config()} and
 * {@link set_config()} functions.
 *
 * @package     block_learnerscript
 * @subpackage  cli
 * @copyright   2017 Arun Kumar M <arun@eabyas.in>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir.'/clilib.php');

$usage = "Displays the current value of the given site setting. Allows to set it to the given value, too.

Usage:
    # php learnerscript.php [--report=<reportname>]
    # php learnerscript.php [--help|-h]
    # php learnerscript.php [--version|-v]

Options:
    -h --help                   Print this help.
    --report=<reportname>       Create the Report with default methods.
    -v --version                Print Plugin Version.

Examples:

";

list($options, $unrecognised) = cli_get_params([
    'help' => false,
    'report' => null,
    'version' => false
], [
    'h' => 'help',
    'v' => 'version',
    'r' => 'report'
]);

if ($unrecognised) {
    $unrecognised = implode(PHP_EOL . '  ', $unrecognised);
    cli_error(get_string('cliunknowoption', 'core_admin', $unrecognised));
}

if ($options['help']) {
    cli_writeln($usage);
    exit(2);
}
if ($options['version']) {
    $version = get_config('block_learnerscript', 'version');
    cli_writeln(get_string('ls_cli_version', 'block_learnerscript', $version));
    exit(3);
}
if ($options['report'] !== null) {
    if (!$options['report']) {
        cli_error(get_string('ls_cli_missing', 'block_learnerscript', 'Report'));
    }
    if (!file_exists($CFG->dirroot . '/blocks/learnerscript/reports/' . $options['report'] . '')) {
        mkdir($CFG->dirroot . '/blocks/learnerscript/reports/' . $options['report'] . '');

        $report = generate_report($options['report']);
        if (($fh = fopen($CFG->dirroot . '/blocks/learnerscript/reports/' . $options['report'] .
             '/report.class.php', 'w')) !== false) {
            fwrite($fh, $report);
            fclose($fh);
            cli_writeln(get_string('ls_cli_create', 'block_learnerscript', $options['report']));
        }
    } else {
        cli_writeln(get_string('ls_cli_exists', 'block_learnerscript', $options['report']));
    }
}


/**
 * Returns content of Report.
 *
 * Uses PHP_EOL for generating proper end of lines for the given platform.
 *
 * @param string $reportname Report Name
 * @return string
 */
function generate_report($reportname) {
    $report = '<?php' . PHP_EOL;
    $report .= '// This file is part of Moodle - http://moodle.org/' . PHP_EOL;
    $report .= '//' . PHP_EOL;
    $report .= '// Moodle is free software: you can redistribute it and/or modify' . PHP_EOL;
    $report .= '// it under the terms of the GNU General Public License as published by' . PHP_EOL;
    $report .= '// the Free Software Foundation, either version 3 of the License, or' . PHP_EOL;
    $report .= '// (at your option) any later version.' . PHP_EOL;
    $report .= '//' . PHP_EOL;
    $report .= '// Moodle is distributed in the hope that it will be useful,' . PHP_EOL;
    $report .= '// but WITHOUT ANY WARRANTY; without even the implied warranty of' . PHP_EOL;
    $report .= '// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the' . PHP_EOL;
    $report .= '// GNU General Public License for more details.' . PHP_EOL;
    $report .= '//' . PHP_EOL;
    $report .= '// You should have received a copy of the GNU General Public License' . PHP_EOL;
    $report .= '// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.' . PHP_EOL;
    $report .= PHP_EOL;
    $report .= '/**' . PHP_EOL;
    $report .= ' * ' . PHP_EOL;
    $report .= ' *' . PHP_EOL;
    $report .= ' * @package    block_learnerscript' . PHP_EOL;
    $report .= ' * @copyright Arun Kumar M <arun@eabyas.in>' . PHP_EOL;
    $report .= ' * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later' . PHP_EOL;
    $report .= ' */' . PHP_EOL;
    $report .= PHP_EOL;
    $report .= 'use block_learnerscript\local\reportbase;' . PHP_EOL;
    $report .= 'use block_learnerscript\report;' . PHP_EOL;
    $report .= PHP_EOL;
    $report .= 'class report_' . $reportname . ' extends reportbase implements report {' . PHP_EOL;
    $report .= '    public function __construct($report, $reportproperties) {' . PHP_EOL;
    $report .= '        parent::__construct($report);' . PHP_EOL;
    $report .= '        $this->components = array();' . PHP_EOL;
    $report .= '        $this->columns = array();' . PHP_EOL;
    $report .= '        $this->conditions = array();' . PHP_EOL;
    $report .= '        $this->filters = array();' . PHP_EOL;
    $report .= '        $this->basicparams = array();' . PHP_EOL;
    $report .= '        $this->parent = true;' . PHP_EOL;
    $report .= '        $this->courselevel = true;' . PHP_EOL;
    $report .= '        $this->orderable = array();' . PHP_EOL;
    $report .= '    }' . PHP_EOL;
    $report .= '    /**' . PHP_EOL;
    $report .= '      * Report for LearnerScript' . PHP_EOL;
    $report .= '      * @param  string $sqlorder Ordering data.' . PHP_EOL;
    $report .= '      * @param  array $conditionfinalelements array of conditional elements.' .
     PHP_EOL;
    $report .= '      * @return array (array) elements and (integer) List of records per page and
     total length for pagination.' . PHP_EOL;
    $report .= '    */' . PHP_EOL;
    $report .= '    public function get_all_elements($sqlorder = \'\', $conditionfinalelements = array()) {' . PHP_EOL;
    $report .= '        global $DB, $USER, $COURSE;' . PHP_EOL;
    $report .= '        $elements = array();' . PHP_EOL;
    $report .= '        return array($elements, count($elements));' . PHP_EOL;
    $report .= '    }' . PHP_EOL;
    $report .= '    /**' . PHP_EOL;
    $report .= '      * Report for ' . PHP_EOL;
    $report .= '      * @param  array $elements List of elements.' . PHP_EOL;
    $report .= '      * @return array $data Final records.' . PHP_EOL;
    $report .= '      */' . PHP_EOL;
    $report .= '    public function get_rows($elements) {' . PHP_EOL;
    $report .= '        return $elements;' . PHP_EOL;
    $report .= '    }' . PHP_EOL;
    $report .= '}' . PHP_EOL;

    return $report;
}