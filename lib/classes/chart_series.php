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
 * Chart series.
 *
 * @package    core
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core;
defined('MOODLE_INTERNAL') || die();

use coding_exception;
use JsonSerializable;

/**
 * Chart series class.
 *
 * @package    core
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class chart_series implements JsonSerializable {

    /** Default type for a series. */
    const TYPE_DEFAULT = null;
    /** Series of type line. */
    const TYPE_LINE = 'line';

    /** @var string[] Colors of the series. */
    protected $colors = [];
    /** @var string Fill mode for area charts. See https://www.chartjs.org/docs/latest/charts/area.html */
    protected $fill = null;
    /** @var string Label for this series. */
    protected $label;
    /** @var string[] Labels for the values of the series. */
    protected $labels = null;
    /** @var bool Whether the line of the serie should be smooth or not. */
    protected $smooth = null;
    /** @var string Type of the series. */
    protected $type = self::TYPE_DEFAULT;
    /** @var float[] Values of the series. */
    protected $values = [];
    /** @var int Index of the X axis. */
    protected $xaxis = null;
    /** @var int Index of the Y axis. */
    protected $yaxis = null;

    /**
     * Constructor.
     *
     * @param string $label The label of the series.
     * @param float[] $values The values of this series.
     */
    public function __construct($label, $values) {
        $this->values = $values;
        $this->label = $label;
    }

    /**
     * Get the color.
     *
     * @return string|null
     */
    public function get_color() {
        return isset($this->color[0]) ? $this->color[0] : null;
    }

    /**
     * Get the colors for each value in the series.
     *
     * @return string[]
     */
    public function get_colors() {
        return $this->colors;
    }

    /**
     * Get the number of values in this series.
     *
     * @return int
     */
    public function get_count() {
        return count($this->values);
    }

    /**
     * Get area fill mode for series.
     */
    public function get_fill() {
        return $this->fill;
    }

    /**
     * Get the label of the series.
     *
     * @return string
     */
    public function get_label() {
        return $this->label;
    }

    /**
     * Set labels for the values of the series.
     *
     * @return array
     */
    public function get_labels() {
        return $this->labels;
    }

    /**
     * Get whether the line of the serie should be smooth or not.
     *
     * @return bool
     */
    public function get_smooth() {
        return $this->smooth;
    }

    /**
     * Get the type of series.
     *
     * @return string
     */
    public function get_type() {
        return $this->type;
    }

    /**
     * Get the values of the series.
     *
     * @return string[]
     */
    public function get_values() {
        return $this->values;
    }

    /**
     * Get the index of the X axis.
     *
     * @return int
     */
    public function get_xaxis() {
        return $this->xaxis;
    }

    /**
     * Get the index of the Y axis.
     *
     * @return int
     */
    public function get_yaxis() {
        return $this->yaxis;
    }

    /**
     * Whether there is a color per value.
     *
     * @return bool
     */
    public function has_colored_values() {
        return count($this->colors) == $this->get_count();
    }

    /**
     * Serialize the object.
     *
     * @return array
     */
    public function jsonSerialize(): array {
        $data = [
            'label' => $this->label,
            'labels' => $this->labels,
            'type' => $this->type,
            'values' => $this->values,
            'colors' => $this->colors,
            'fill' => $this->fill,
            'axes' => [
                'x' => $this->xaxis,
                'y' => $this->yaxis,
            ],
            'smooth' => $this->smooth
        ];
        return $data;
    }

    /**
     * Set the color of the series.
     *
     * @param string $color CSS compatible color.
     */
    public function set_color($color) {
        $this->colors = [$color];
    }

    /**
     * Set a color for each value in the series.
     *
     * @param string[] $colors CSS compatible colors.
     */
    public function set_colors(array $colors) {
        $this->colors = $colors;
    }

    /**
     * Set fill mode for the series.
     * @param string $fill
     */
    public function set_fill($fill) {
        $this->fill = $fill;
    }

    /**
     * Set labels for the values of the series.
     *
     * @param array $labels The labels for the series values.
     */
    public function set_labels($labels) {
        $this->labels = $labels;
    }

    /**
     * Set whether the line of the serie should be smooth or not.
     *
     * Only applicable for line chart or a line series, if null it assumes the chart default (not smooth).
     *
     * @param bool $smooth True if the line should be smooth, false for tensioned lines.
     */
    public function set_smooth($smooth) {
        $this->smooth = $smooth;
    }

    /**
     * Set the type of the series.
     *
     * @param string $type Constant value from self::TYPE_*.
     */
    public function set_type($type) {
        if (!in_array($type, [self::TYPE_DEFAULT, self::TYPE_LINE])) {
            throw new coding_exception('Invalid serie type.');
        }
        $this->type = $type;
    }

    /**
     * Set the index of the X axis.
     *
     * @param int $index The index.
     */
    public function set_xaxis($index) {
        $this->xaxis = $index;
    }

    /**
     * Set the index of the Y axis.
     *
     * @param int $index The index.
     */
    public function set_yaxis($index) {
        $this->yaxis = $index;
    }

}
