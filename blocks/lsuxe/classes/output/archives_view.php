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
 * Cross Enrollment Tool
 *
 * @package   block_lsuxe
 * @copyright 2008 onwards Louisiana State University
 * @copyright 2008 onwards David Lowe, Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_lsuxe\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;
use block_lsuxe\persistents\mappings;

require_once('../../config.php');
require_once($CFG->dirroot . '/blocks/lsuxe/lib.php');
require_login();

class archives_view implements renderable, templatable {
    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): array {
        global $CFG;
        $pname = new mappings();
        $helpers = new \lsuxe_helpers();

        $where = "WHERE timedeleted IS NOT NULL";
        $data = $pname->get_all_records("mappings", $where);
        $updateddata = $pname->transform_for_view($data, $helpers);
        $updateddata['xeurl'] = $CFG->wwwroot;

        return $updateddata;
    }
}
