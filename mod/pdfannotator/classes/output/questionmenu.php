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
 * Dropdown menu in questionstable on overview tab.
 *
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen (see README.md)
 * @author    Friederike Schwager
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_pdfannotator\output;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

class questionmenu implements \renderable, \templatable {

    private $url;
    private $iconclass;
    private $label;
    private $buttonclass;

    /**
     * Constructor of renderable for dropdown menu in questionstable.
     *
     * @param int $commentid Id of the question
     * @param array $urlparams Parameters for the link
     */
    public function __construct($commentid, $urlparams) {

        global $CFG;

        $iconclass = "icon fa fa-share fa-fw";
        $label = get_string('forward', 'pdfannotator');
        $buttonclass = 'comment-subscribe subscribe';

        $urlparams['action'] = 'forwardquestion';
        $urlparams['fromoverview'] = '1';
        $urlparams['commentid'] = $commentid;
        $urlparams['sesskey'] = sesskey();
        $url = new moodle_url($CFG->wwwroot . '/mod/pdfannotator/view.php', $urlparams);

        $this->url = $url;
        $this->iconclass = $iconclass;
        $this->label = $label;
        $this->buttonclass = $buttonclass;
    }

    /**
     * This function is required by any renderer to retrieve the data structure
     * passed into the template.
     * @param \renderer_base $output
     * @return type
     */
    public function export_for_template(\renderer_base $output) {
        $data = [];
        $data['url'] = $this->url->out();
        $data['iconclass'] = $this->iconclass;
        $data['label'] = $this->label;
        $data['buttonclass'] = $this->buttonclass;
        return $data;
    }

}
