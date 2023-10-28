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
 * The purpose of this script is to collect the output data for the printview template
 * and make it available to the renderer. The data is collected via the pdfannotator model
 * and then processed. Therefore, class printview can be seen as a view controller.
 *
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen (see README.md)
 * @author    Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

namespace mod_pdfannotator\output;

defined('MOODLE_INTERNAL') || die();

class printview implements \renderable, \templatable {

    private $documentname;
    private $conversations;
    private $url;


    public function __construct($documentname=null, $conversations=null, $url=null) {

        $this->documentname = $documentname;
        $this->conversations = $conversations;
        $this->url = $url;
    }

    public function export_for_template(\renderer_base $output) {

        $data = [];
        $data['documentname'] = $this->documentname;
        $data['posts'] = $this->conversations;
        return $data;
    }

}
