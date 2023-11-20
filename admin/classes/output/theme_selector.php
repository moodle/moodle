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

namespace core_admin\output;

use moodle_url;
use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * Theme selector renderable.
 *
 * @package    core_admin
 * @copyright  2023 David Woloszyn <david.woloszyn@moodle.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_selector implements renderable, templatable {

    /** @var array $themedata Theme data to pass to the template. */
    private $themedata = null;

    /** @var bool Whether $CFG->theme is defined in config.php. */
    private $definedinconfig;

    /**
     * Constructor.
     *
     * @param array $themedata Theme data used for template.
     * @param bool $definedinconfig Whether $CFG->theme is defined in config.php.
     */
    public function __construct(array $themedata, bool $definedinconfig = false) {
        $this->themedata = $themedata;
        $this->definedinconfig = $definedinconfig;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Renderer base.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass {

        $data = new stdClass();
        // Theme data used to populate cards and modal.
        $data->themes = $this->themedata;
        // Reset theme caches button.
        $reseturl = new moodle_url('/admin/themeselector.php', ['sesskey' => sesskey(), 'reset' => 1]);
        $resetbutton = new \single_button($reseturl, get_string('themeresetcaches', 'admin'), 'post',
            \single_button::BUTTON_SECONDARY);
        $data->resetbutton = $resetbutton->export_for_template($output);
        $data->definedinconfig = $this->definedinconfig;

        return $data;
    }
}
