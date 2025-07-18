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

namespace core_courseformat\output\local\overview;

use core\output\externable;
use core\output\local\dropdown\dialog;
use core\output\local\properties\button;
use core_courseformat\external\overviewdialog_exporter;
use stdClass;

/**
 * Class to render an overview dialog element.
 *
 * @package    core_courseformat
 * @copyright  2025 Mikel Martín <mikel@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class overviewdialog extends dialog implements externable {
    /** @var stdClass[] The list of the items. */
    protected array $items;

    /**
     * Constructor.
     *
     * The definition object could contain the following keys:
     * - classes: component CSS classes.
     * - buttonclasses: the button CSS classes.
     * - dialogwidth: the dropdown width.
     * - extras: extra HTML attributes (attribute => value).
     *
     * @param string $buttoncontent the button content
     * @param string $title the overview dialog content title
     * @param string $description the overview dialog content description
     * @param array $definition an optional array of the element definition
     */
    public function __construct(
        string $buttoncontent,
        /** @var string The title of the overview dialog content. */
        protected string $title = '',
        /** @var string The title of the overview dialog content. */
        protected string $description = '',
        array $definition = []
    ) {
        parent::__construct($buttoncontent, '', $definition);
        $this->items = [];
    }

    /**
     * Set the items to be displayed in the overview dialog.
     *
     * @param string $label the label of the item
     * @param string $value the value of the item
     * @return overviewdialog
     */
    public function add_item(string $label, string $value): self {
        $this->items[] = (object) [
            'label' => $label,
            'value' => $value,
        ];
        return $this;
    }

    /**
     * Set the title of the overview dialog content.
     *
     * @param string $title the title to set
     * @return overviewdialog
     */
    public function set_title(string $title): self {
        $this->title = $title;
        return $this;
    }

    /**
     * Set the description of the overview dialog content.
     *
     * @param string $description the description to set
     * @return overviewdialog
     */
    public function set_description(string $description): self {
        $this->description = $description;
        return $this;
    }

    /**
     * Get the items to be displayed in the overview dialog.
     *
     * @return stdClass[] The items to be displayed in the overview dialog.
     */
    public function get_items(): array {
        return $this->items;
    }

    /**
     * Get the title of the overview dialog content.
     *
     * @return string The title of the overview dialog content.
     */
    public function get_title(): string {
        return $this->title;
    }

    /**
     * Get the description of the overview dialog content.
     *
     * @return string The description of the overview dialog content.
     */
    public function get_description(): string {
        return $this->description;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return array data context for a mustache template
     */
    public function export_for_template(\renderer_base $output): array {
        $data = [
            ...parent::export_for_template($output),
            'title' => $this->title,
            'items' => $this->items,
            'hasitems' => count($this->items),
            'description' => $this->description,
        ];

        // Overview dropdowns always have a dropdown toggle.
        $data['buttonclasses'] = empty($data['buttonclasses'])
            ? button::BODY_OUTLINE->classes() . ' dropdown-toggle'
            : $data['buttonclasses'] . ' dropdown-toggle';

        return $data;
    }

    /**
     * Get the name of the template to use for this templatable.
     *
     * @param \renderer_base $renderer The renderer requesting the template name
     * @return string the template name
     */
    public function get_template_name(\renderer_base $renderer): string {
        return 'core_courseformat/local/overview/overviewdialog';
    }

    #[\Override]
    public function get_exporter(?\core\context $context = null): overviewdialog_exporter {
        $context = $context ?? \core\context\system::instance();
        return new overviewdialog_exporter($this, ['context' => $context]);
    }

    #[\Override]
    public static function get_read_structure(
        int $required = VALUE_REQUIRED,
        mixed $default = null
    ): \core_external\external_single_structure {
        return overviewdialog_exporter::get_read_structure($required, $default);
    }

    #[\Override]
    public static function read_properties_definition(): array {
        return overviewdialog_exporter::read_properties_definition();
    }
}
