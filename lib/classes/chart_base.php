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
 * Chart base.
 *
 * @package    core
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core;
defined('MOODLE_INTERNAL') || die();

use coding_exception;
use JsonSerializable;
use renderable;

/**
 * Chart base class.
 *
 * @package    core
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class chart_base implements JsonSerializable, renderable {

    protected $series = [];
    protected $labels = [];
    protected $title = null;

    public function __construct() {
    }

    public function add_series(chart_series $serie) {
        $this->series[] = $serie;
    }

    public function jsonSerialize() {
        return [
            'type' => $this->get_type(),
            'series' => $this->series,
            'labels' => $this->labels,
            'title' => $this->title
        ];
    }

    public function get_labels() {
        return $this->labels;
    }

    public function get_series() {
        return $this->series;
    }

    public function get_title() {
        return $this->title;
    }

    public function get_type() {
        $classname = get_class($this);
        return substr($classname, strpos($classname, '_') + 1);
    }

    public function set_labels(array $labels) {
        $this->labels = $labels;
    }

    public function set_title($title) {
        $this->title = $title;
    }
}
