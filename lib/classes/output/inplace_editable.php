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

/**
 * Contains class \core\output\inplace_editable
 *
 * @package    core
 * @category   output
 * @copyright  2016 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\output;

use templatable;
use renderable;
use lang_string;

/**
 * Class allowing to quick edit a title inline
 *
 * This class is used for displaying an element that can be in-place edited by the user. To display call:
 * echo $OUTPUT->render($element);
 * or
 * echo $OUTPUT->render_from_template('core/inplace_editable', $element->export_for_template($OUTPUT));
 *
 * Template core/inplace_editable will automatically load javascript module with the same name
 * core/inplace_editable. Javascript module registers a click-listener on edit link and
 * then replaces the displayed value with an input field. On "Enter" it sends a request
 * to web service core_update_inplace_editable, which invokes the callback from the component.
 * Any exception thrown by the web service (or callback) is displayed as an error popup.
 *
 * Callback {$component}_inplace_editable($itemtype, $itemid, $newvalue) must be present in the lib.php file of
 * the component or plugin. It must return instance of this class.
 *
 * @package    core
 * @category   output
 * @copyright  2016 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class inplace_editable implements templatable, renderable {

    /**
     * @var string component responsible for diplsying/updating
     */
    protected $component = null;

    /**
     * @var string itemtype inside the component
     */
    protected $itemtype = null;

    /**
     * @var int identifier of the editable element (usually database id)
     */
    protected $itemid = null;

    /**
     * @var string value of the editable element as it is present in the database
     */
    protected $value = null;

    /**
     * @var string value of the editable element as it should be displayed,
     * must be formatted and may contain links or other html tags
     */
    protected $displayvalue = null;

    /**
     * @var string label for the input element (for screenreaders)
     */
    protected $editlabel = null;

    /**
     * @var string hint for the input element (for screenreaders)
     */
    protected $edithint = null;

    /**
     * @var bool indicates if the current user is allowed to edit this element - set in constructor after permissions are checked
     */
    protected $editable = false;

    /**
     * @var string type of the element - text, toggle or select
     */
    protected $type = 'text';

    /**
     * @var string options for the element, for example new value for the toggle or json-encoded list of options for select
     */
    protected $options = '';

    /**
     * Constructor.
     *
     * @param string $component name of the component or plugin responsible for the updating of the value (must declare callback)
     * @param string $itemtype type of the item inside the component - each component/plugin may implement multiple inplace-editable elements
     * @param int $itemid identifier of the item that can be edited in-place
     * @param bool $editable whether this value is editable (check capabilities and editing mode), if false, only "displayvalue"
     *              will be displayed without anything else
     * @param string $displayvalue what needs to be displayed to the user, it must be cleaned, with applied filters (call
     *              {@link format_string()}). It may be wrapped in an html link, contain icons or other decorations
     * @param string $value what needs to be edited - usually raw value from the database, it may contain multilang tags
     * @param lang_string|string $edithint hint (title) that will be displayed under the edit link
     * @param lang_string|string $editlabel label for the input element in the editing mode (for screenreaders)
     */
    public function __construct($component, $itemtype, $itemid, $editable,
            $displayvalue, $value = null, $edithint = null, $editlabel = null) {
        $this->component = $component;
        $this->itemtype = $itemtype;
        $this->itemid = $itemid;
        $this->editable = $editable;
        $this->displayvalue = $displayvalue;
        $this->value = $value;
        $this->edithint = $edithint;
        $this->editlabel = $editlabel;
    }

    /**
     * Sets the element type to be a toggle
     *
     * For toggle element $editlabel is not used.
     * $displayvalue must be specified, it can have text or icons but can not contain html links.
     *
     * Toggle element can have two or more options.
     *
     * @param array $options toggle options as simple, non-associative array; defaults to array(0,1)
     * @return self
     */
    public function set_type_toggle($options = null) {
        if ($options === null) {
            $options = array(0, 1);
        }
        $options = array_values($options);
        $idx = array_search($this->value, $options, true);
        if ($idx === false) {
            throw new \coding_exception('Specified value must be one of the toggle options');
        }
        $nextvalue = ($idx < count($options) - 1) ? $idx + 1 : 0;

        $this->type = 'toggle';
        $this->options = (string)$nextvalue;
        return $this;
    }

    /**
     * Sets the element type to be a dropdown
     *
     * For select element specifying $displayvalue is optional, if null it will
     * be assumed that $displayvalue = $options[$value].
     * However displayvalue can still be specified if it needs icons and/or
     * html links.
     *
     * If only one option specified, the element will not be editable.
     *
     * @param array $options associative array with dropdown options
     * @return self
     */
    public function set_type_select($options) {
        if (!array_key_exists($this->value, $options)) {
            throw new \coding_exception('Options for select element must contain an option for the specified value');
        }
        if (count($options) < 2) {
            $this->editable = false;
        }
        $this->type = 'select';

        $pairedoptions = [];
        foreach ($options as $key => $value) {
            $pairedoptions[] = [
                'key' => $key,
                'value' => $value,
            ];
        }
        $this->options = json_encode($pairedoptions);
        if ($this->displayvalue === null) {
            $this->displayvalue = $options[$this->value];
        }
        return $this;
    }

    /**
     * Whether the link should contain all of the content or not.
     */
    protected function get_linkeverything() {
        if ($this->type === 'toggle') {
            return true;
        }

        if (preg_match('#<a .*>.*</a>#', $this->displayvalue) === 1) {
            return false;
        }

        return true;
    }

    /**
     * Export this data so it can be used as the context for a mustache template (core/inplace_editable).
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return array data context for a mustache template
     */
    public function export_for_template(\renderer_base $output) {
        if (!$this->editable) {
            return array(
                'displayvalue' => (string)$this->displayvalue
            );
        }

        return array(
            'component' => $this->component,
            'itemtype' => $this->itemtype,
            'itemid' => $this->itemid,
            'displayvalue' => (string)$this->displayvalue,
            'value' => (string)$this->value,
            'edithint' => (string)$this->edithint,
            'editlabel' => (string)$this->editlabel,
            'type' => $this->type,
            'options' => $this->options,
            'linkeverything' => $this->get_linkeverything() ? 1 : 0,
        );
    }

    /**
     * Renders this element
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return string
     */
    public function render(\renderer_base $output) {
        return $output->render_from_template('core/inplace_editable', $this->export_for_template($output));
    }
}
