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

namespace core\output\local\dropdown;

use core\output\choicelist;

/**
 * Class to render a dropdown dialog element.
 *
 * A dropdown dialog allows to render any arbitrary HTML into a dropdown elements triggered
 * by a button.
 *
 * @package    core
 * @category   output
 * @copyright  2023 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class status extends dialog {

    /**
     * @var choicelist content of dialog.
     */
    protected $choices = null;

    /**
     * Constructor.
     *
     * The definition object could contain the following keys:
     * - classes: component CSS classes.
     * - buttonclasses: the button CSS classes.
     * - dialogwidth: the dropdown width.
     * - extras: extra HTML attributes (attribute => value).
     * - buttonsync: if the button should be synced with the selected value.
     * - updatestatus: if component must update the status and trigger a change event when clicked.
     *
     * @param string $buttoncontent the button content
     * @param choicelist $choices the choice object
     * @param array $definition an optional array of the element definition
     */
    public function __construct(string $buttoncontent, choicelist $choices, array $definition = []) {
        parent::__construct($buttoncontent, '', $definition);
        $this->set_choice($choices);
        if ($definition['buttonsync'] ?? false) {
            $this->extras['data-button-sync'] = 'true';
        }
        if ($definition['updatestatus'] ?? false) {
            $this->extras['data-update-status'] = 'true';
        }
    }

    /**
     * Set the dialog contents.
     *
     * @param choicelist $choices
     */
    public function set_choice(choicelist $choices) {
        $this->choices = $choices;
        $description = $choices->get_description();
        if (!empty($description)) {
            $this->set_content($description);
        }
    }

    /**
     * Export this data so it can be used as the context for a mustache template (core/inplace_editable).
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return array data context for a mustache template
     */
    public function export_for_template(\renderer_base $output): array {
        $data = parent::export_for_template($output);
        if ($this->choices !== null) {
            $data['choices'] = $this->choices->export_for_template($output);
        }
        $selectedvalue = $this->choices->get_selected_value();
        if ($selectedvalue !== null) {
            $data['extras'][] = (object)[
                'attribute' => 'data-value',
                'value' => $selectedvalue,
            ];
        }
        return $data;
    }

    /**
     * Get the name of the template to use for this templatable.
     *
     * @param \renderer_base $renderer The renderer requesting the template name
     * @return string the template name
     */
    public function get_template_name(\renderer_base $renderer): string {
        return 'core/local/dropdown/status';
    }
}
