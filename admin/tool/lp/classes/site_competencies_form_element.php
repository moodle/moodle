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
 * Course competencies element.
 *
 * @package   tool_lp
 * @copyright 2016 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/form/hidden.php');

/**
 * Course competencies element.
 *
 * @package   tool_lp
 * @copyright 2016 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_lp_site_competencies_form_element extends MoodleQuickForm_hidden {

    /**
     * Constructor
     *
     * @param string $elementName Element name
     * @param mixed $elementLabel Label(s) for an element
     * @param array $options Options to control the element's display
     * @param mixed $attributes Either a typical HTML attribute string or an associative array.
     */
    public function __construct($elementName=null, $value='', $attributes=null) {
        global $OUTPUT;

        if ($elementName == null) {
            // This is broken quickforms messing with the constructors.
            return;
        }
        $attributes = array_merge(['data-action' => 'competencies'], $attributes?$attributes:[]);

        parent::__construct($elementName, $value, $attributes);
        $this->setType('hidden');
    }

    /**
     * Generate the hidden field and the controls to show and pick the competencies.
     */
    function toHtml(){
        global $PAGE;

        $html = parent::toHTML();

        if (!$this->isFrozen()) {
            $context = context_system::instance();
            $params = [$context->id];
            // Require some JS to select the competencies.
            $PAGE->requires->js_call_amd('tool_lp/form_competency_element', 'init', $params);
            $html .= '<div class="form-group row">';
            $html .= '<div class="col-md-3"></div>';
            $html .= '<div class="col-md-9">';
            $html .= '<div data-region="competencies"></div>';
            $html .= '<div class="mt-3">';
            $html .= '<a class="btn btn-secondary" role="button" data-action="select-competencies">' . get_string('addcompetency', 'tool_lp') . '</a>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }
        return $html;
    }

    /**
     * Accepts a renderer
     *
     * @param object     An HTML_QuickForm_Renderer object
     * @access public
     * @return void
     */
    function accept(&$renderer, $required=false, $error=null)
    {
        $renderer->renderElement($this, false, '');
    }
}
