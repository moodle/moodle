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
 * Chart axis.
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
 * Chart axis class.
 *
 * @package    core
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class chart_axis implements JsonSerializable {

    /** Default axis position. */
    const POS_DEFAULT = null;
    /** Bottom axis position. */
    const POS_BOTTOM = 'bottom';
    /** Left axis position. */
    const POS_LEFT = 'left';
    /** Right axis position. */
    const POS_RIGHT = 'right';
    /** Top axis position. */
    const POS_TOP = 'top';

    /** @var string The axis label. */
    protected $label = null;
    /** @var string[] The axis labels, tick values. */
    protected $labels = null;
    /** @var float The maximum tick value. */
    protected $max = null;
    /** @var float The minimum tick value. */
    protected $min = null;
    /** @var string The axis position. */
    protected $position = self::POS_DEFAULT;
    /** @var float The stepsize between ticks. */
    protected $stepsize = null;

    /**
     * Constructor.
     *
     * Must not take any argument.
     */
    public function __construct() {
    }

    /**
     * Get the label.
     *
     * @return string
     */
    public function get_label() {
        return $this->label;
    }

    /**
     * Get the labels.
     *
     * @return string[]
     */
    public function get_labels() {
        return $this->labels;
    }

    /**
     * Get the max value.
     *
     * @return float
     */
    public function get_max() {
        return $this->max;
    }

    /**
     * Get the min value.
     *
     * @return float
     */
    public function get_min() {
        return $this->min;
    }

    /**
     * Get the axis position.
     *
     * @return string
     */
    public function get_position() {
        return $this->position;
    }

    /**
     * Get the step size.
     *
     * @return float
     */
    public function get_stepsize() {
        return $this->stepsize;
    }

    /**
     * Serialize the object.
     *
     * @return array
     */
    public function jsonSerialize() {
        return [
            'label' => $this->label,
            'labels' => $this->labels,
            'max' => $this->max,
            'min' => $this->min,
            'position' => $this->position,
            'stepSize' => $this->stepsize,
        ];
    }

    /**
     * Set the label.
     *
     * @param string $label The label.
     */
    public function set_label($label) {
        $this->label = $label;
    }

    /**
     * Set the labels.
     *
     * @param string[] $labels The labels.
     */
    public function set_labels($labels) {
        $this->labels = $labels;
    }

    /**
     * Set the max value.
     *
     * @param float $max The max value.
     */
    public function set_max($max) {
        $this->max = $max;
    }

    /**
     * Set the min value.
     *
     * @param float $min The min value.
     */
    public function set_min($min) {
        $this->min = $min;
    }

    /**
     * Set the position.
     *
     * @param string $position Use constant self::POS_*.
     */
    public function set_position($position) {
        $this->position = $position;
    }

    /**
     * Set the step size.
     *
     * @param float $stepsize The step size.
     */
    public function set_stepsize($stepsize) {
        $this->stepsize = $stepsize;
    }

}
