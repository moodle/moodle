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

declare(strict_types=1);

namespace core_reportbuilder\output;

use core_external;
use html_writer;
use moodle_url;
use core\output\inplace_editable;
use core_reportbuilder\permission;
use core_reportbuilder\local\models\report;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once("{$CFG->libdir}/external/externallib.php");

/**
 * Report name editable component
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_name_editable extends inplace_editable {

    /**
     * Class constructor
     *
     * @param int $reportid
     * @param report|null $report The report persistent, note that in addition to id/name properties being present we also
     *      require the following to be correctly set in order to perform permission checks: contextid/type/usercreated
     */
    public function __construct(int $reportid, ?report $report = null) {
        if ($report === null) {
            $report = new report($reportid);
        }

        $editable = permission::can_edit_report($report);

        $url = $editable
            ? new moodle_url('/reportbuilder/edit.php', ['id' => $report->get('id')])
            : new moodle_url('/reportbuilder/view.php', ['id' => $report->get('id')]);

        $displayvalue = html_writer::link($url, $report->get_formatted_name());

        parent::__construct('core_reportbuilder', 'reportname', $report->get('id'), $editable, $displayvalue, $report->get('name'),
            get_string('editreportname', 'core_reportbuilder'));
    }

    /**
     * Update report persistent and return self, called from inplace_editable callback
     *
     * @param int $reportid
     * @param string $value
     * @return self
     */
    public static function update(int $reportid, string $value): self {
        $report = new report($reportid);

        core_external::validate_context($report->get_context());
        permission::require_can_edit_report($report);

        $value = trim(clean_param($value, PARAM_TEXT));
        if ($value !== '') {
            $report
                ->set('name', $value)
                ->update();
        }

        return new self(0, $report);
    }
}
