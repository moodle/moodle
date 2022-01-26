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

use table_dataformat_export_format;

defined('MOODLE_INTERNAL') || die();

require_once("{$CFG->libdir}/tablelib.php");

/**
 * Dataformat export class for reports
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dataformat_export_format extends table_dataformat_export_format {

    /**
     * Add a row of data
     *
     * @param array $row
     * @return bool
     */
    public function add_data($row): bool {
        $row = $this->format_data($row);

        return parent::add_data($row);
    }

    /**
     * Format a row of data. If the export format doesn't support HTML, then format cell contents to remove tags
     *
     * @param array $row
     * @return array
     */
    public function format_data(array $row): array {
        if (!$this->dataformat->supports_html()) {
            $row = array_map([$this, 'format_text'], $row);
        }

        return $row;
    }
}
