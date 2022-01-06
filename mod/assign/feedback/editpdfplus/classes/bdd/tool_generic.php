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
 * This file contains the annotation class for the assignfeedback_editpdfplus plugin
 *
 * @package   assignfeedback_editpdfplus
 * @copyright  2016 Université de Lausanne
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignfeedback_editpdfplus\bdd;

/**
 * Description of tool
 *
 * @author kury
 */
class tool_generic extends tool {

    const DISPLAY_CLASS_BUTTON = array(
        "highlight" => "fa fa-paint-brush",
        "line" => "fa fa-minus",
        "oval" => "fa fa-circle-o",
        "pen" => "fa fa-pencil",
        "rectangle" => "fa fa-square-o",
        "drag" => "fa fa-hand-paper-o",
        "select" => "fa fa-mouse-pointer",
        "resize" => "fa fa-arrows-h",
        "annotationcolour" => "fa fa-tint",
        "rotateleft" => "fa fa-undo",
        "rotateright" => "fa fa-undo fa-flip-horizontal"
    );

    /**
     * Get elements (display label, parameters) to render a button in HTML
     * @param bool $disabled if the button must be disabled
     * @return array
     */
    public function get_renderer_bouton_html_display($disabled = false) {
        $iconhtml = \html_writer::tag("i", "", array('class' => self::DISPLAY_CLASS_BUTTON[$this->label], 'aria-hidden' => 'true'));
        $iconparams = array(
            'data-tool' => $this->label,
            'class' => $this->label . 'button generictoolbarbutton btn btn-light',
            'type' => 'button'
        );
        if ($this->id) {
            $iconparams['id'] = 'ctbutton' . $this->id;
        }
        if ($disabled) {
            $iconparams['disabled'] = 'true';
        }
        return array(
            'content' => $iconhtml,
            'parameters' => $iconparams
        );
    }

}
