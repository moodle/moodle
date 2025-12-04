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

namespace qbank_managecategories\output;

use core\output\renderable;
use core\output\renderer_base;
use core\output\templatable;
use core\url;

/**
 * Category name and question count link.
 *
 * @package   qbank_managecategories
 * @copyright 2025 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class category_link implements renderable, templatable {
    /**
     * Constructor.
     *
     * @param string $name The question bank name.
     * @param url $questionbankurl The URL for the question bank link.
     * @param int $questioncount The number of questions in the bank.
     */
    public function __construct(
        /** @var string $name The question bank name. */
        protected string $name,
        /** @var url $questionbankurl The URL for the question bank link. */
        protected url $questionbankurl,
        /** @var int $questioncount The number of questions in the bank. */
        protected int $questioncount
    ) {
    }

    #[\Override]
    public function export_for_template(renderer_base $output): array {
        return [
            'categoryname' => $this->name,
            'questionbankurl' => $this->questionbankurl->out(),
            'questioncount' => $this->questioncount,
        ];
    }
}
