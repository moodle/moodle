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

    /** @var chart_series[] The series constituting this chart. */
    protected $series = [];
    /** @var string[] The labels for the X axis when categorised. */
    protected $labels = [];
    /** @var string The title of the chart. */
    protected $title = null;
    /** @var chart_axis[] The X axes. */
    protected $xaxes = [];
    /** @var chart_axis[] The Y axes. */
    protected $yaxes = [];
    /** @var array Options for the chart legend. */
    protected $legendoptions = [];

    /**
     * Constructor.
     *
     * Must not take any argument.
     *
     * Most of the time you do not want to extend this, rather extend the
     * method {@link self::set_defaults} to set the defaults on instantiation.
     */
    public function __construct() {
        $this->set_defaults();
    }

    /**
     * Add a series to the chart.
     *
     * @param chart_series $serie The serie.
     */
    public function add_series(chart_series $serie) {
        $this->series[] = $serie;
    }

    /**
     * Serialize the object.
     *
     * @return array
     */
    public function jsonSerialize(): array {
        global $CFG;
        return [
            'type' => $this->get_type(),
            'series' => $this->series,
            'labels' => $this->labels,
            'title' => $this->title,
            'axes' => [
                'x' => $this->xaxes,
                'y' => $this->yaxes,
            ],
            'legend_options' => !empty($this->legendoptions) ? $this->legendoptions : null,
            'config_colorset' => !empty($CFG->chart_colorset) ? $CFG->chart_colorset : null
        ];
    }

    /**
     * Get an axis.
     *
     * @param string $type Accepts values 'x' or 'y'.
     * @param int $index The index of this axis.
     * @param bool $createifnotexists Whether to create the axis if not found.
     * @return chart_axis
     */
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

    /**
     * Get the labels of the X axis.
     *
     * @return string[]
     */
    public function get_labels() {
        return $this->labels;
    }

    /**
     * Get an array of options for the chart legend.
     *
     * @return array
     */
    public function get_legend_options() {
        return $this->legendoptions;
    }

    /**
     * Get the series.
     *
     * @return chart_series[]
     */
    public function get_series() {
        return $this->series;
    }

    /**
     * Get the title.
     *
     * @return string
     */
    public function get_title() {
        return $this->title;
    }

    /**
     * Get the chart type.
     *
     * @return string
     */
    public function get_type() {
        $classname = get_class($this);
        return substr($classname, strpos($classname, '_') + 1);
    }

    /**
     * Get the X axes.
     *
     * @return chart_axis[]
     */
    public function get_xaxes() {
        return $this->xaxes;
    }

    /**
     * Get an X axis.
     *
     * @param int $index The index of the axis.
     * @param bool $createifnotexists When true, create an instance of the axis if none exist at this index yet.
     * @return chart_axis
     */
    public function get_xaxis($index = 0, $createifnotexists = false) {
        return $this->get_axis('x', $index, $createifnotexists);
    }

    /**
     * Get the Y axes.
     *
     * @return chart_axis[]
     */
    public function get_yaxes() {
        return $this->yaxes;
    }

    /**
     * Get a Y axis.
     *
     * @param int $index The index of the axis.
     * @param bool $createifnotexists When true, create an instance of the axis if none exist at this index yet.
     * @return chart_axis
     */
    public function get_yaxis($index = 0, $createifnotexists = false) {
        return $this->get_axis('y', $index, $createifnotexists);
    }

    /**
     * Set the defaults for this chart type.
     *
     * Child classes can extend this to set default values on instantiation.
     *
     * In general the constructor could be used, but this method is here to
     * emphasize and self-document the default values set by the chart type.
     *
     * @return void
     */
    protected function set_defaults() {
    }

    /**
     * Set the chart labels.
     *
     * @param string[] $labels The labels.
     */
    public function set_labels(array $labels) {
        $this->labels = $labels;
    }

    /**
     * Set options for the chart legend.
     * See https://www.chartjs.org/docs/2.7.0/configuration/legend.html for options.
     *
     * Note: Setting onClick and onHover events is not directly supported through
     * this method. These config options must be set directly within Javascript
     * on the page.
     *
     * @param array $legendoptions Whether or not to display the chart's legend.
     */
    public function set_legend_options(array $legendoptions) {
        $this->legendoptions = $legendoptions;
    }

    /**
     * Set the title.
     *
     * @param string $title The title.
     */
    public function set_title($title) {
        $this->title = $title;
    }

    /**
     * Set an X axis.
     *
     * Note that this will override any predefined axis without warning.
     *
     * @param chart_axis $axis The axis.
     * @param int $index The index of the axis.
     */
    public function set_xaxis(chart_axis $axis, $index = 0) {
        $this->validate_axis('x', $axis, $index);
        return $this->xaxes[$index] = $axis;
    }

    /**
     * Set an Y axis.
     *
     * Note that this will override any predefined axis without warning.
     *
     * @param chart_axis $axis The axis.
     * @param int $index The index of the axis.
     */
    public function set_yaxis(chart_axis $axis, $index = 0) {
        $this->validate_axis('y', $axis, $index);
        return $this->yaxes[$index] = $axis;
    }

    /**
     * Validate an axis.
     *
     * We validate this from PHP because not doing it here could result in errors being
     * hard to trace down. For instance, if we were to add axis at keys without another
     * axis preceding, we would effectively contain the axes in an associative array
     * rather than a simple array, and that would have consequences on serialisation.
     *
     * @param string $xy Accepts x or y.
     * @param chart_axis $axis The axis to validate.
     * @param index $index The index of the axis.
     */
    protected function validate_axis($xy, chart_axis $axis, $index = 0) {
        if ($index > 0) {
            $axes = $xy == 'x' ? $this->xaxes : $this->yaxes;
            if (!isset($axes[$index - 1])) {
                throw new coding_exception('Missing ' . $xy . ' axis at index lower than ' . $index);
            }
        }
    }

}
