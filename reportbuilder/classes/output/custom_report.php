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

use core_reportbuilder\external\custom_report_exporter;
use core_reportbuilder\local\models\report;
use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * Custom report output class
 *
 * @package     core_reportbuilder
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_report implements renderable, templatable {

    /** @var report $reportpersistent */
    protected $persistent;

    /** @var bool $editmode */
    protected $editmode;

    /** @var string $download */
    protected $download;

    /**
     * Class constructor
     *
     * @param report $reportpersistent
     * @param bool $editmode
     * @param string $download
     */
    public function __construct(report $reportpersistent, bool $editmode = true, string $download = '') {
        $this->persistent = $reportpersistent;
        $this->editmode = $editmode;
        $this->download = $download;
    }

    /**
     * Export report data suitable for a template
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass {
        $exporter = new custom_report_exporter($this->persistent, [], $this->editmode, $this->download);

        return $exporter->export($output);
    }
}
