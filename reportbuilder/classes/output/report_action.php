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

use core\output\{renderer_base, templatable};
use core_reportbuilder\external\report_action_exporter;

/**
 * Encapsulate a report action
 *
 * @package     core_reportbuilder
 * @copyright   2025 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_action implements templatable {

    /**
     * Constructor
     *
     * @param string $title
     * @param array $attributes
     * @param string $tag
     */
    public function __construct(
        /** @var string */
        public readonly string $title,
        /** @var array */
        public readonly array $attributes,
        /** @var string */
        public readonly string $tag = 'button',
    ) {

    }

    #[\Override]
    public function export_for_template(renderer_base $output): array {
        $exporter = new report_action_exporter(null, ['reportaction' => $this]);

        return (array) $exporter->export($output);
    }
}
