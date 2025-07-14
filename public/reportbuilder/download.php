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
 * Download a report
 *
 * @package   core_reportbuilder
 * @copyright 2021 David Matamoros <davidmc@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

use core_reportbuilder\permission;
use core_reportbuilder\system_report_factory;

require_once(__DIR__ . '/../config.php');

require_login();

$reportid = required_param('id', PARAM_INT);
$download = required_param('download', PARAM_ALPHA);
$parameters = optional_param('parameters', null, PARAM_RAW);

$reportpersistent = new \core_reportbuilder\local\models\report($reportid);
$context = $reportpersistent->get_context();

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/reportbuilder/download.php'));

if ($reportpersistent->get('type') === \core_reportbuilder\local\report\base::TYPE_SYSTEM_REPORT) {
    $parameters = (array) json_decode($parameters);

    // Re-create the exact report that is being downloaded.
    $systemreport = system_report_factory::create($reportpersistent->get('source'), $context, $reportpersistent->get('component'),
        $reportpersistent->get('area'), $reportpersistent->get('itemid'), $parameters);

    if (!$systemreport->can_be_downloaded()) {
        throw new \core_reportbuilder\exception\report_access_exception();
    }

    // Combine original report parameters with 'download' parameter.
    $parameters['download'] = $download;

    $outputreport = new \core_reportbuilder\output\system_report($reportpersistent, $systemreport, $parameters);
    echo $PAGE->get_renderer('core_reportbuilder')->render($outputreport);
} else {
    permission::require_can_view_report($reportpersistent);

    $customreport = new \core_reportbuilder\output\custom_report($reportpersistent, false, $download);
    echo $PAGE->get_renderer('core_reportbuilder')->render($customreport);
}
