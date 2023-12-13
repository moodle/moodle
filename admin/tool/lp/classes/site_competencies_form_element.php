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
 * Site competencies element.
 *
 * @package   tool_lp
 * @copyright 2019 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/form/hidden.php');

/**
 * Site competencies element.
 *
 * @package   tool_lp
 * @copyright 2019 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_lp_site_competencies_form_element extends MoodleQuickForm_hidden {

    /**
     * Constructor
     *
     * @param string $elementname Element name.
     * @param string $value The element value.
     * @param mixed $attributes Either a typical HTML attribute string or an associative array.
     */
    public function __construct($elementname=null, $value='', $attributes=null) {
        if ($elementname == null) {
            // This is broken quickforms messing with the constructors.
            return;
        }
        $attributes = array_merge(['data-action' => 'competencies'], $attributes ? $attributes : []);

        parent::__construct($elementname, $value, $attributes);
        $this->setType('hidden');
    }

    /**
     * Generate the hidden field and the controls to show and pick the competencies.
     */
    public function toHtml() {
        global $PAGE;

        $html = parent::toHTML();

        if (!$this->isFrozen()) {
            $context = context_system::instance();
            $params = [$context->id];
            // Require some JS to select the competencies.
            $PAGE->requires->js_call_amd('tool_lp/form_competency_element', 'init', $params);
            $html .= '<div class="mb-3 row">';
            $html .= '<div class="col-md-3"></div>';
            $html .= '<div class="col-md-9">';
            $html .= '<div data-region="competencies"></div>';
            $html .= '<div class="mt-3">';
            $html .= '<a class="btn btn-secondary" role="button" data-action="select-competencies">';
            $html .= get_string('addcompetency', 'tool_lp');
            $html .= '</a>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }
        return $html;
    }

    /**
     * Accepts a renderer
     *
     * @param HTML_QuickForm_Renderer $renderer the renderer for the element.
     * @param boolean $required not used.
     * @param string $error not used.
     * @return void
     */
    public function accept(&$renderer, $required=false, $error=null) {
        $renderer->renderElement($this, false, '');
    }
}
