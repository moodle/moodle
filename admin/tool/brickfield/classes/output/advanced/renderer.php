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

namespace tool_brickfield\output\advanced;

use tool_brickfield\local\tool\filter;
use tool_brickfield\manager;

/**
 * tool_brickfield/advanced renderer
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, https://www.brickfield.ie
 * @author     Mike Churchward
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \tool_brickfield\output\renderer {
    /**
     * Render the page containing the Advanced report.
     *
     * @param \stdClass $data Report data.
     * @param filter $filter Display filters.
     * @return String HTML showing charts.
     * @throws \moodle_exception
     */
    public function display(\stdClass $data, filter $filter): string {
        return $this->render_from_template(manager::PLUGINNAME . '/advanced', $data);
    }
}
