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

defined('MOODLE_INTERNAL') || die();

use core\output\choicelist;
use core\output\local\dropdown\status;

require_once('HTML/QuickForm/select.php');
require_once('templatable_form_element.php');

/**
 * User choice using a dropdown type form element.
 *
 * @package   core_form
 * @category  form
 * @copyright 2023 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_choicedropdown extends HTML_QuickForm_select implements templatable {

    use templatable_form_element {
        export_for_template as export_for_template_base;
    }

    /**
     * @var string html for help button, if empty then no help.
     */
    protected string $_helpbutton = '';

    /**
     * @var bool if true label will be hidden.
     */
    protected bool $_hiddenLabel = false;

    /**
     * @var choicelist the user choices.
     */
    protected ?choicelist $choice = null;

    /**
     * @var string[] Dropdown dialog width.
     */
    public const WIDTH = status::WIDTH;

    /**
     * @var string the dropdown width (from core\output\local\dropdown\status::WIDTH).
     */
    protected string $dropdownwidth = status::WIDTH['small'];

    /**
     * Constructor.
     *
     * @param string $elementname Select name attribute
     * @param mixed $elementlabel Label(s) for the select
     * @param choicelist $options Data to be used to populate options
     * @param mixed $attributes Either a typical HTML attribute string or an associative array
     */
    public function __construct(
        $elementname = null,
        $elementlabel = null,
        choicelist $options = null,
        $attributes = null
    ) {
        parent::__construct($elementname, $elementlabel, $options, $attributes);
        $this->_type = 'choicedropdown';
    }

    /**
     * Set the dropdown width.
     *
     * @param string $width
     */
    public function set_dialog_width(string $width) {
        $this->dropdownwidth = $width;
    }

    /**
     * Loads options from a choicelist.
     *
     * @param choicelist $choice Options source currently supports assoc array or DB_result
     * @param string|null $value optional value (in case it is not defined in the choicelist)
     * @param string|null $unused2 unused
     * @param string|null $unused3 unused
     * @param string|null $unused4 unused
     * @return bool
     */
    public function load(&$choice, $value = null, $unused2 = null, $unused3 = null, $unused4 = null): bool {
        if (!$choice instanceof choicelist) {
            throw new coding_exception('Choice must be instance of choicelist');
        }
        $this->choice = $choice;
        $this->choice->set_allow_empty(false);
        if ($value !== null && is_string($value)) {
            $choice->set_selected_value($value);
        }
        $this->setSelected($choice->get_selected_value());
        return true;
    }

    /**
     * Sets label to be hidden
     *
     * @param bool $hiddenLabel sets if label should be hidden
     */
    public function setHiddenLabel($hiddenLabel) {
        $this->_hiddenLabel = $hiddenLabel;
    }

    /**
     * Returns HTML for select form element.
     *
     * This method is only needed when forms renderer is forces via
     * $GLOBALS['_HTML_QuickForm_default_renderer']. Otherwise the
     * renderer will use mustache templates.
     *
     * @return string
     */
    public function toHtml(): string {
        $html = '';
        if ($this->_hiddenLabel) {
            $this->_generateId();
            $html .= '<label class="accesshide" for="'.$this->getAttribute('id').'" >'.$this->getLabel().'</label>';
        }
        $html .= parent::toHtml();
        return $html;
    }

    /**
     * get html for help button
     *
     * @return string html for help button
     */
    public function getHelpButton(): string {
        return $this->_helpbutton;
    }

    /**
     * Slightly different container template when frozen. Don't want to use a label tag
     * with a for attribute in that case for the element label but instead use a div.
     * Templates are defined in renderer constructor.
     *
     * @return string
     */
    public function getElementTemplateType(): string {
        if ($this->_flagFrozen) {
            return 'static';
        } else {
            return 'default';
        }
    }

    /**
     * We check the options and return only the values that _could_ have been
     * selected. We also return a scalar value if select is not "multiple"
     *
     * @param string $submitvalues submitted values
     * @param bool $assoc if true the returned value is associated array
     * @return string|null
     */
    public function exportValue(&$submitvalues, $assoc = false) {
        $value = $this->_findValue($submitvalues) ?? $this->getValue();
        if (is_array($value)) {
            $value = reset($value);
        }
        if ($value === null) {
            return $this->_prepareValue($value, $assoc);
        }
        if (!$this->choice->has_value($value)) {
            $value = $this->choice->get_selected_value();
        }
        return $this->_prepareValue($value, $assoc);
    }

    public function export_for_template(renderer_base $output): array {
        $context = $this->export_for_template_base($output);

        if (!empty($this->_values)) {
            $this->choice->set_selected_value(reset($this->_values));
        }

        $dialog = new status(
            $this->choice->get_selected_content($output),
            $this->choice,
            [
                'extras' => ['data-form-controls' => $context['id']],
                'buttonsync' => true,
                'updatestatus' => true,
                'dialogwidth' => $this->dropdownwidth,
            ]
        );
        $context['dropdown'] = $dialog->export_for_template($output);
        $context['select'] = $this->choice->export_for_template($output);
        $context['nameraw'] = $this->getName();
        return $context;
    }
}
