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
 * Renderable that initialises the grading "app".
 *
 * @package    mod_assign
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

use \mod_assign\output\grading_app;

class theme_essential_mod_assign_renderer extends mod_assign_renderer {

    /**
     * Defer to template..
     *
     * @param grading_app $app - All the data to render the grading app.
     */
    public function render_grading_app(grading_app $app) {
        $context = $app->export_for_template($this);

        $context->breadcrumb = '<div id="page-navbar" class="clearfix row-fluid">';
        $context->breadcrumb .= '<div class="breadcrumb-nav span12">';
        global $OUTPUT;
        $context->breadcrumb .= $OUTPUT->navbar();
        $context->breadcrumb .= '</div>';
        $context->breadcrumb .= '</div>';

        return $this->render_from_template('mod_assign/grading_app', $context);
    }
}
