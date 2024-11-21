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

namespace local_intelliboard\output;

defined('MOODLE_INTERNAL') || die();

use moodle_url;
use renderable;
use renderer_base;
use templatable;

/**
 * Class containing data of "Setup" page
 *
 * @package    local_intelliboard
 * @copyright  2019 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */
class setup implements renderable, templatable {

    var $params = null;

    public function __construct($params = null) {
        $this->params = $params;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function export_for_template(renderer_base $output) {
        global $CFG, $DB, $USER, $OUTPUT;

        $servicedata = $DB->get_record("external_services", ["component" => "local_intelliboard"], "*", MUST_EXIST);
        $nexturl = new \moodle_url("/local/intelliboard/index.php");
        $partners = (array) $this->params["intelliboard"]->partners;

        array_walk($partners, function(&$item, $id) {
            $item = [
                "id" => $id,
                "name" => $item
            ];
        });
        $this->params["intelliboard"]->partners = array_values($partners);

        $loaderimgurl = $OUTPUT->image_url('spinner', 'local_intelliboard');

        return [
            "CFG" => $CFG,
            "intelliboard" => $this->params["intelliboard"],
            "service" => $servicedata,
            "current_user" => $USER,
            "full_name" => fullname($USER),
            "loader_img_url" => $loaderimgurl,
            "next_url" => $nexturl->out()
        ];
    }
}
