<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core\output;

/**
 * Sticky footer class with supplementary content.
 *
 * @package    core
 * @copyright  2026 Sara Arjona <sara@moodle.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class supplementary_sticky_footer extends \core\output\sticky_footer {
    /** @var action_link|null The link added as supplementary content or null if not defined. */
    protected ?action_link $supplementarycontent = null;

    /**
     * Add supplementary content to the sticky footer.
     *
     * @param action_link $content The action link to be added as supplementary content.
     */
    public function add_supplementary_content(
        action_link $content,
    ): void {
        $this->supplementarycontent = $content;
        $this->supplementarycontent->add_class('fw-medium');
    }

    #[\Override]
    public function export_for_template(renderer_base $output): array {
        $data = parent::export_for_template($output);
        if ($this->supplementarycontent !== null) {
            $data['supplementary'] = $this->supplementarycontent->export_for_template($output);
        }
        return $data;
    }

    #[\Override]
    public function get_template_name(\renderer_base $renderer): string {
        return 'core/supplementary_sticky_footer';
    }
}
