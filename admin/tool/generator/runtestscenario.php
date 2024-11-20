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
 * Web interface to list and filter steps
 *
 * @package    tool_generator
 * @copyright  2023 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_generator\local\testscenario\runner;
use tool_generator\form\featureimport;
use tool_generator\output\parsingresult;
use tool_generator\output\stepsinformation;

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/behat/classes/behat_config_manager.php');

// Executing behat generator can take some time.
core_php_time_limit::raise(300);

admin_externalpage_setup('toolgenerator_runtestscenario');

$currenturl = new moodle_url('/admin/tool/generator/runtestscenario.php');
$runner = new runner();

/** @var core_renderer $output*/
$output = $PAGE->get_renderer('core');

echo $output->header();
echo $output->heading(get_string('testscenario', 'tool_generator'));

echo $output->paragraph(get_string('testscenario_description', 'tool_generator'));

try {
    $runner->init();
} catch (Exception $e) {
    echo $output->notification(get_string('testscenario_notready', 'tool_generator'), null, false);
    echo $output->footer();
    die;
}

echo $output->render(new stepsinformation($runner));

$mform = new featureimport();

$data = null;
if (!$mform->is_cancelled()) {
    $data = $mform->get_data();
}

if (empty($data)) {
    $mform->display();
    echo $OUTPUT->footer();
    die;
}

$content = $mform->get_feature_contents();

if (empty($content)) {
    echo $output->notification(get_string('testscenario_invalidfile', 'tool_generator'));
    echo $output->continue_button($currenturl);
    echo $output->footer();
    die;
}

try {
    if ($data->executecleanup) {
        $parsedfeature = $runner->parse_cleanup($content);
    } else {
        $parsedfeature = $runner->parse_feature($content);
    }
} catch (\Throwable $th) {
    echo $output->notification(get_string('testscenario_errorparsing', 'tool_generator', $th->getMessage()));
    echo $output->continue_button($currenturl);

    echo $output->footer();
    die;
}

$runner->execute($parsedfeature);

echo $output->render(new parsingresult($parsedfeature));
echo $output->continue_button($currenturl);
echo $output->footer();
