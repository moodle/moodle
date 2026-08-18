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

namespace core\api\output;

use core\output\html_writer;
use core\output\named_templatable;
use core\output\renderer_base;

/**
 * Renderable revealing a freshly created token's secret, once.
 *
 * @package    core
 * @copyright  Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class created_token implements \core\output\renderable, named_templatable {
    /**
     * Constructor.
     *
     * @param string $token The token to reveal, which is not recoverable after this render.
     * @param string $name The name given to the token.
     */
    public function __construct(
        /** @var string The token to reveal, which is not recoverable after this render. */
        protected readonly string $token,
        /** @var string The name given to the token. */
        protected readonly string $name,
    ) {
    }

    /**
     * Export the data for the mustache template.
     *
     * @param renderer_base $output The renderer.
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        return [
            'token' => $this->token,
            'name' => $this->name,
            // The field needs a stable id so the copy button can point at it.
            'tokenelementid' => html_writer::random_id('pat_token_'),
        ];
    }

    /**
     * Get the name of the template to use.
     *
     * @param renderer_base $renderer The renderer requesting the template name.
     * @return string
     */
    public function get_template_name(renderer_base $renderer): string {
        // Named explicitly: the renderer cannot guess the `api` subdirectory from the class name.
        return 'core/api/created_token';
    }
}
