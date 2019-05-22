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
 * Enables the provided model.
 *
 * @package    tool_analytics
 * @copyright  2017 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require_once(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir.'/clilib.php');

$help = "Enables the provided model.

Options:
--modelid           Model id
--list              List models
--analysisinterval  Time splitting method full class name
-h, --help          Print out this help

Example:
\$ php admin/tool/analytics/cli/enable_model.php --modelid=1 --analysisinterval=\"\\core\\analytics\\time_splitting\\quarters\"
";

// Now get cli options.
list($options, $unrecognized) = cli_get_params(
    array(
        'help'             => false,
        'list'             => false,
        'modelid'          => false,
        'analysisinterval' => false
    ),
    array(
        'h' => 'help',
    )
);

if ($options['help']) {
    echo $help;
    exit(0);
}

if ($options['list'] || $options['modelid'] === false) {
    \tool_analytics\clihelper::list_models();
    exit(0);
}

if ($options['analysisinterval'] === false) {
    echo $help;
    exit(0);
}

// We need admin permissions.
\core\session\manager::set_user(get_admin());

$model = new \core_analytics\model($options['modelid']);

// Evaluate its suitability to predict accurately.
$model->enable($options['analysisinterval']);

cli_heading(get_string('success'));
exit(0);
