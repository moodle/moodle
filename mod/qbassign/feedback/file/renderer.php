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
 * This file contains a renderer for the qbassignment class
 *
 * @package   qbassignfeedback_file
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * A custom renderer class that extends the plugin_renderer_base and is used by the qbassign module.
 *
 * @package qbassignfeedback_file
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbassignfeedback_file_renderer extends plugin_renderer_base {

    /**
     * Render a summary of the zip file import
     *
     * @param qbassignfeedback_file_import_summary $summary - Stats about the zip import
     * @return string The html response
     */
    public function render_qbassignfeedback_file_import_summary($summary) {
        $o = '';
        $o .= $this->container(get_string('userswithnewfeedback', 'qbassignfeedback_file', $summary->userswithnewfeedback));
        $o .= $this->container(get_string('filesupdated', 'qbassignfeedback_file', $summary->feedbackfilesupdated));
        $o .= $this->container(get_string('filesadded', 'qbassignfeedback_file', $summary->feedbackfilesadded));

        $url = new moodle_url('view.php',
                              array('id'=>$summary->cmid,
                                    'action'=>'grading'));
        $o .= $this->continue_button($url);
        return $o;
    }
}

