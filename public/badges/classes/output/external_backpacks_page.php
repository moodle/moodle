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
 * Manage enabled backpacks for the site.
 *
 * @package    core_badges
 * @copyright  2019 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_badges\output;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/badgeslib.php');

use core_badges\external\backpack_exporter;

/**
 * Manage enabled backpacks renderable.
 *
 * @package    core_badges
 * @copyright  2019 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external_backpacks_page implements \renderable {

    /** @var \moodle_url Badges backpacks URL. */
    protected $url;

    /** @var array List the backpacks at site level. */
    protected $backpacks = [];

    /**
     * Constructor.
     * @param \moodle_url $url
     */
    public function __construct(\moodle_url $url) {
        $this->url = $url;

        $this->backpacks = badges_get_site_backpacks();
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Renderer base.
     * @return stdClass
     */
    public function export_for_template(\renderer_base $output) {
        global $PAGE;

        $rownumber = 0;
        $rowcount = count($this->backpacks);

        $data = new \stdClass();
        $data->baseurl = $this->url;
        $data->backpacks = array();
        $data->sesskey = sesskey();
        foreach ($this->backpacks as $backpack) {
            $exporter = new backpack_exporter($backpack);
            $backpack = $exporter->export($output);
            $backpack->cantest = ($backpack->apiversion == OPEN_BADGES_V2);
            $backpack->canmoveup = $rownumber > 0;
            $backpack->canmovedown = $rownumber < $rowcount - 1;

            $data->backpacks[] = $backpack;
            $rownumber++;
        }

        return $data;
    }
}
