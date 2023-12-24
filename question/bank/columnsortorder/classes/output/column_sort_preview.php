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

namespace qbank_columnsortorder\output;

use moodle_url;
use templatable;
use renderable;
use qbank_columnsortorder\column_manager;

/**
 * Renderable for the question bank preview.
 *
 * This takes the HTML for a question bank preview, and displays in a page with a link to return to the admin screen.
 *
 * @package   qbank_columnsortorder
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class column_sort_preview implements renderable, templatable {
    /** @var string Rendered preview HTML. */
    protected string $preview;

    /**
     * Store rendered preview for template context.
     *
     * @param string $preview
     */
    public function __construct(string $preview) {
        $this->preview = $preview;
    }

    public function export_for_template(\renderer_base $output): array {
        $context = [
            'backurl' => new moodle_url('/question/bank/columnsortorder/sortcolumns.php'),
            'preview' => $this->preview,
        ];
        return $context;
    }
}
