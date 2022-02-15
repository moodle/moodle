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

namespace core_reportbuilder\table;

use moodle_url;

/**
 * Custom report view dynamic table class
 *
 * @package     core_reportbuilder
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_report_table_view extends custom_report_table {

    /** @var bool We're pre/viewing the report, not editing it */
    protected const REPORT_EDITING = false;

    /**
     * Override printed headers, to use those of grandparent class
     */
    public function print_headers() {
        $columns = $this->get_active_columns();
        if (empty($columns)) {
            return;
        }

        base_report_table::print_headers();
    }

    /**
     * Get the html for the download buttons
     *
     * @return string
     */
    public function download_buttons(): string {
        global $OUTPUT;

        if (!$this->is_downloading()) {
            return $OUTPUT->download_dataformat_selector(
                get_string('downloadas', 'table'),
                new moodle_url('/reportbuilder/download.php'),
                'download',
                ['id' => $this->persistent->get('id')]
            );
        }

        return '';
    }
}
