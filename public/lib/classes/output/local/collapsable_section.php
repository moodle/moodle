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

namespace core\output\local;

use core\output\named_templatable;
use core\output\renderable;

/**
 * Collapsable section output.
 *
 * @package    core
 * @copyright  2024 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class collapsable_section implements named_templatable, renderable {
    /**
     * Constructor.
     *
     * @param string $titlecontent The content to be displayed inside the button.
     * @param string $sectioncontent The content to be displayed inside the dialog.
     * @param string $classes Additional CSS classes to be applied to the section.
     * @param array $extras An attribute => value array to be added to the element.
     * @param bool $open If the section is opened by default.
     * @param string|null $expandlabel The label for the expand button.
     * @param string|null $collapselabel The label for the collapse button.
     */
    public function __construct(
        /** @var string $titlecontent The content to be displayed inside the button. */
        protected string $titlecontent,
        /** @var string $sectioncontent The content to be displayed inside the dialog. */
        protected string $sectioncontent,
        /** @var string $classes Additional CSS classes to be applied to the section. */
        protected string $classes = '',
        /** @var array $extras A attribute => value array to be added to the element. */
        protected array $extras = [],
        /** @var bool $open if the section is opened by default. */
        protected bool $open = false,
        /** @var string|null $expandlabel The label for the expand button. */
        protected string|null $expandlabel = null,
        /** @var string|null $collapselabel The label for the collapse button. */
        protected string|null $collapselabel = null,
    ) {
    }

    /**
     * Set the title content.
     *
     * @param string $titlecontent
     */
    public function set_title_content(string $titlecontent) {
        $this->titlecontent = $titlecontent;
    }

    /**
     * Sets the content for the collapsable section.
     *
     * @param string $sectioncontent The content to be set for the section.
     */
    public function set_section_content(string $sectioncontent) {
        $this->sectioncontent = $sectioncontent;
    }

    /**
     * Sets the CSS classes for the collapsable section.
     *
     * @param string $classes The CSS classes to be applied to the collapsable section.
     */
    public function set_classes(string $classes) {
        $this->classes = $classes;
    }

    /**
     * Merges the provided extras array with the existing extras array.
     *
     * @param array $extras The array of extra attributes => extra value.
     */
    public function add_extra_attributes(array $extras) {
        $this->extras = array_merge($this->extras, $extras);
    }

    /**
     * Sets the default open state of the collapsible section.
     *
     * @param bool $open
     */
    public function set_open(bool $open) {
        $this->open = $open;
    }

    #[\Override]
    public function export_for_template(\renderer_base $output): array {
        $elementid = $this->extras['id'] ?? \html_writer::random_id('collapsableSection_');

        $data = [
            'titlecontent' => $this->titlecontent,
            'sectioncontent' => $this->sectioncontent,
            'classes' => $this->classes,
            'extras' => $this->export_extras(),
            'elementid' => $elementid,
        ];
        if ($this->open) {
            $data['open'] = 'true';
        }
        if ($this->expandlabel) {
            $data['expandlabel'] = $this->expandlabel;
        }
        if ($this->collapselabel) {
            $data['collapselabel'] = $this->collapselabel;
        }
        return $data;
    }

    /**
     * Exports the extras as an array of attribute-value pairs.
     *
     * @return array An array of associative arrays, each containing 'attribute' and 'value' keys.
     */
    private function export_extras(): array {
        $extras = [];
        foreach ($this->extras as $attribute => $value) {
            $extras[] = [
                'attribute' => $attribute,
                'value' => $value,
            ];
        }
        return $extras;
    }

    #[\Override]
    public function get_template_name(\renderer_base $renderer): string {
        return 'core/local/collapsable_section';
    }
}
