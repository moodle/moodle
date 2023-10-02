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

use core\output\named_templatable;
use renderable;

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
class dialog implements named_templatable, renderable {
    /** Dropdown dialog positions. */
    public const POSITION = [
        'start' => 'dropdown-menu-left',
        'end' => 'dropdown-menu-right',
    ];

    /** Dropdown dialog positions. */
    public const WIDTH = [
        'default' => '',
        'big' => 'dialog-big',
        'small' => 'dialog-small',
    ];

    /**
     * @var string content of dialog.
     */
    protected $dialogcontent = '';

    /**
     * @var bool if the footer should auto enable or not.
     */
    protected $buttoncontent = true;

    /**
     * @var string trigger button CSS classes.
     */
    protected $buttonclasses = '';

    /**
     * @var string component CSS classes.
     */
    protected $classes = '';

    /**
     * @var string the dropdown position.
     */
    protected $dropdownposition = self::POSITION['start'];

    /**
     * @var string dropdown preferred width.
     */
    protected $dropdownwidth = self::WIDTH['default'];


    /**
     * @var array extra HTML attributes (attribute => value).
     */
    protected $extras = [];

    /**
     * Constructor.
     *
     * The definition object could contain the following keys:
     * - classes: component CSS classes.
     * - buttonclasses: the button CSS classes.
     * - dialogwidth: the dropdown width.
     * - dropdownposition: the dropdown position.
     * - extras: extra HTML attributes (attribute => value).
     *
     * @param string $buttoncontent the button content
     * @param string $dialogcontent the footer content
     * @param array $definition an optional array of the element definition
     */
    public function __construct(string $buttoncontent, string $dialogcontent, array $definition = []) {
        $this->buttoncontent = $buttoncontent;
        $this->dialogcontent = $dialogcontent;
        if (isset($definition['classes'])) {
            $this->classes = $definition['classes'];
        }
        if (isset($definition['buttonclasses'])) {
            $this->buttonclasses = $definition['buttonclasses'];
        }
        if (isset($definition['extras'])) {
            $this->extras = $definition['extras'];
        }
        if (isset($definition['dialogwidth'])) {
            $this->dropdownwidth = $definition['dialogwidth'];
        }
        if (isset($definition['dropdownposition'])) {
            $this->dropdownposition = $definition['dropdownposition'];
        }
    }

    /**
     * Set the dialog contents.
     *
     * @param string $dialogcontent
     */
    public function set_content(string $dialogcontent) {
        $this->dialogcontent = $dialogcontent;
    }

    /**
     * Set the button contents.
     *
     * @param string $buttoncontent
     * @param string|null $buttonclasses the button classes
     */
    public function set_button(string $buttoncontent, ?string $buttonclasses = null) {
        $this->buttoncontent = $buttoncontent;
        if ($buttonclasses !== null) {
            $this->buttonclasses = $buttonclasses;
        }
    }

    /**
     * Set the dialog width.
     *
     * @param string $width
     */
    public function set_dialog_width(string $width) {
        $this->dropdownwidth = $width;
    }

    /**
     * Add extra classes to trigger butotn.
     *
     * @param string $buttonclasses the extra classes
     */
    public function set_button_classes(string $buttonclasses) {
        $this->buttonclasses = $buttonclasses;
    }

    /**
     * Add extra classes to the component.
     *
     * @param string $classes the extra classes
     */
    public function set_classes(string $classes) {
        $this->classes = $classes;
    }

    /**
     * Add extra extras to the sticky footer element.
     *
     * @param string $attribute the extra attribute
     * @param string $value the value
     */
    public function add_extra(string $attribute, string $value) {
        $this->extras[$attribute] = $value;
    }

    /**
     * Set the button element id.
     *
     * @param string $value the value
     */
    public function add_button_id(string $value) {
        $this->extras['buttonid'] = $value;
    }

    /**
     * Set the dropdown position.
     * @param string $position the position
     */
    public function set_position(string $position) {
        $this->dropdownposition = $position;
    }

    /**
     * Export this data so it can be used as the context for a mustache template (core/inplace_editable).
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return array data context for a mustache template
     */
    public function export_for_template(\renderer_base $output): array {
        $extras = [];
        // Id is required to add JS controls to the dropdown.
        $dropdownid = $this->extras['id'] ?? \html_writer::random_id('dropdownDialog_');
        if (isset($this->extras['id'])) {
            unset($this->extras['id']);
        }
        foreach ($this->extras as $attribute => $value) {
            $extras[] = [
                'attribute' => $attribute,
                'value' => $value,
            ];
        }
        $data = [
            // Id is required for the correct HTML labelling.
            'dropdownid' => $dropdownid,
            'buttonid' => $this->extras['buttonid'] ?? \html_writer::random_id('dropwdownbutton_'),
            'buttoncontent' => (string) $this->buttoncontent,
            'dialogcontent' => (string) $this->dialogcontent,
            'classes' => $this->classes,
            'buttonclasses' => $this->buttonclasses,
            'dialogclasses' => $this->dropdownwidth,
            'extras' => $extras,
        ];
        // Bootstrap 4 dropdown position still uses left and right literals.
        $data["position"] = $this->dropdownposition;
        if (right_to_left()) {
            $rltposition = [
                self::POSITION['start'] => self::POSITION['end'],
                self::POSITION['end'] => self::POSITION['end'],
            ];
            $data["position"] = $rltposition[$this->dropdownposition];
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
        return 'core/local/dropdown/dialog';
    }
}
