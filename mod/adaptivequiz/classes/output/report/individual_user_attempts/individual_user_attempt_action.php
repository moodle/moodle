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
 * Contains definition of a renderable for an action available for an individual user attempt in the report.
 *
 * @package   mod_adaptivequiz
 * @copyright 2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_adaptivequiz\output\report\individual_user_attempts;

use moodle_url;
use pix_icon;
use renderable;
use renderer_base;
use templatable;

/**
 * Definition of a renderable for an action available for an individual user attempt in the report.
 *
 * @package mod_adaptivequiz
 */
final class individual_user_attempt_action implements renderable, templatable {

    /**
     * @var moodle_url $url;
     */
    private $url;

    /**
     * @var pix_icon $icon
     */
    private $icon;

    /**
     * @var string $title
     */
    private $title;

    /**
     * The constructor.
     *
     * @param moodle_url $url
     * @param pix_icon $icon
     * @param string $title
     */
    public function __construct(moodle_url $url, pix_icon $icon, string $title) {
        $this->url = $url;
        $this->icon = $icon;
        $this->title = $title;
    }

    /**
     * Exports the renderer data in a format that is suitable for a Mustache template.
     *
     * @param renderer_base $output
     */
    public function export_for_template(renderer_base $output): array {
        return [
            'url' => $this->url->out(false),
            'icon' => $output->render($this->icon),
            'title' => $this->title,
        ];
    }
}
