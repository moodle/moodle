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
 * Report Tiles for dashboard block instances.
 * @package  block_reporttiles
 * @author sreekanth <sreekanth@eabyas.in>
 */
namespace block_reporttiles\output;
defined('MOODLE_INTERNAL') || die();
use renderable;
use renderer_base;
use templatable;
use stdClass;
use context_system;

class reporttile implements renderable, templatable {
    public $data;

    public function __construct($data) {
        $this->data = $data;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $DB;
        $data = $this->data;
        return $data;
    }
}
