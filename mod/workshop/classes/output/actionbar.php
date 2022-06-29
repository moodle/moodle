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

namespace mod_workshop\output;

use moodle_url;
use renderer_base;
use url_select;
use renderable;
use templatable;

/**
 * Output the rendered elements for the tertiary nav for page action.
 *
 * @package   mod_workshop
 * @copyright 2021 Sujith Haridasan <sujith@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class actionbar implements renderable, templatable {
    /**
     * The current url.
     *
     * @var moodle_url $currenturl
     */
    private $currenturl;

    /**
     * The workshop object.
     * @var \workshop $workshop
     */
    private $workshop;

    /**
     * actionbar constructor.
     *
     * @param moodle_url $currenturl The current URL.
     * @param \workshop $workshop The workshop object.
     */
    public function __construct(moodle_url $currenturl, \workshop $workshop) {
        $this->currenturl = $currenturl;
        $this->workshop = $workshop;
    }

    /**
     * Export the data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return array The urlselect menu and the heading to be used
     */
    public function export_for_template(renderer_base $output): array {
        $allocators = \workshop::installed_allocators();
        $menu = [];

        foreach (array_keys($allocators) as $methodid) {
            $selectorname = get_string('pluginname', 'workshopallocation_' . $methodid);
            $menu[$this->workshop->allocation_url($methodid)->out(false)] = $selectorname;
        }

        $urlselect = new url_select($menu, $this->currenturl->out(false), null, 'allocationsetting');

        return [
            'urlselect' => $urlselect->export_for_template($output),
            'heading' => $menu[$this->currenturl->out(false)] ?? null
        ];
    }
}
