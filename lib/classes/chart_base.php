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
    protected $xaxes = [];
    protected $yaxes = [];

    public function __construct() {
        $this->set_defaults();
    }

    public function add_series(chart_series $serie) {
        $this->series[] = $serie;
    }

    public function jsonSerialize() {
        return [
            'type' => $this->get_type(),
            'series' => $this->series,
            'labels' => $this->labels,
            'title' => $this->title,
            'axes' => [
                'x' => $this->xaxes,
                'y' => $this->yaxes,
            ]
        ];
    }

    private function get_axis($type, $index, $createifnotexists) {
        $isx = $type === 'x';
        if ($isx) {
            $axis = isset($this->xaxes[$index]) ? $this->xaxes[$index] : null;
        } else {
            $axis = isset($this->yaxes[$index]) ? $this->yaxes[$index] : null;
        }

        if ($axis === null) {
            if (!$createifnotexists) {
                throw new coding_exception('Unknown axis.');
            }

            $axis = new chart_axis();
            if ($isx) {
                $this->set_xaxis($axis, $index);
            } else {
                $this->set_yaxis($axis, $index);
            }
        }

        return $axis;
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

    public function get_xaxes() {
        return $this->xaxes;
    }

    public function get_xaxis($index = 0, $createifnotexists = false) {
        return $this->get_axis('x', $index, $createifnotexists);
    }

    public function get_yaxes() {
        return $this->yaxes;
    }

    public function get_yaxis($index = 0, $createifnotexists = false) {
        return $this->get_axis('y', $index, $createifnotexists);
    }

    protected function set_defaults() {
        // For the child classes to extend.
    }

    public function set_labels(array $labels) {
        $this->labels = $labels;
    }

    public function set_title($title) {
        $this->title = $title;
    }

    public function set_xaxis(chart_axis $axis, $index = 0) {
        return $this->xaxes[$index] = $axis;
    }

    public function set_yaxis(chart_axis $axis, $index = 0) {
        return $this->yaxes[$index] = $axis;
    }

}
