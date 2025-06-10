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
 * Class containing data of "Help" page.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\output;

use renderable;
use templatable;
use renderer_base;

/**
 * Class containing data of "Help" page.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class help implements renderable, templatable {

    /**
     * @var array
     */
    public $params = [];

    /**
     * Output help construct.
     *
     * @param $params
     */
    public function __construct($params = []) {
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
        return [
            'currentyear' => date('Y'),
            'video_img_url' => (new \moodle_url('/local/intellidata/assets/img/video-background.png'))->out(false),
            'play_btn_url' => (new \moodle_url('/local/intellidata/assets/img/play-button.png'))->out(false),
            'bg_img_url' => (new \moodle_url('/local/intellidata/assets/img/bg-element@3x.png'))->out(false),
        ];
    }
}
