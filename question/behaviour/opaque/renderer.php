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
 * Renderer for outputting parts of a question when the actual behaviour
 * used is not available.
 *
 * @package qbehaviour_opaque
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


class qbehaviour_opaque_renderer extends qbehaviour_renderer {
    public function controls(question_attempt $qa, question_display_options $options) {
        if ($qa->get_state()->is_gave_up()) {
            return html_writer::tag('div', get_string('notcompletedmessage', 'qtype_opaque'),
                    array('class' => 'question_aborted'));
        }

        $opaquestate =& update_opaque_state($qa);
        if (is_string($opaquestate)) {
            return notify($opaquestate, '', '', true);
            // TODO
        }

        return html_writer::tag('div', $opaquestate->xhtml,
                array('class' => opaque_browser_type()));
    }

    public function head_code(question_attempt $qa) {
        $output = '';
        $opaquestate =& update_opaque_state($qa);

        $question = $qa->get_question();
        $resourcecache = new opaque_resource_cache($question->engineid,
                $question->remoteid, $question->remoteversion);

        if (!empty($opaquestate->cssfilename) && $resourcecache->file_in_cache($opaquestate->cssfilename)) {
            $output .= '<link rel="stylesheet" type="text/css" href="' .
                    $resourcecache->file_url($opaquestate->cssfilename) . '" />';
        }

        if(!empty($opaquestate->headXHTML)) {
            $output .= $opaquestate->headXHTML;
        }

        return $output;
    }
}