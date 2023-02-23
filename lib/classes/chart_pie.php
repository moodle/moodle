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
 * Chart pie.
 *
 * @package    core
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core;
defined('MOODLE_INTERNAL') || die();

/**
 * Chart pie class.
 *
 * @package    core
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class chart_pie extends chart_base {

    /** @var bool $doughnut Whether the chart should be displayed as doughnut. */
    protected $doughnut = null;

    /**
     * Get parent JSON and add specific pie related attributes and values.
     *
     * @return array
     */
    public function jsonSerialize(): array {
        $data = parent::jsonSerialize();
        $data['doughnut'] = $this->get_doughnut();
        return $data;
    }

    /**
     * Get whether the chart should be displayed as doughnut.
     *
     * @return bool
     */
    public function get_doughnut() {
        return $this->doughnut;
    }

    /**
     * Set whether the chart should be displayed as doughnut.
     *
     * @param bool $doughnut True for doughnut type, false for pie.
     */
    public function set_doughnut($doughnut) {
        $this->doughnut = $doughnut;
    }
}
