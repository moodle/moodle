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

    const POS_DEFAULT = null;
    const POS_BOTTOM = 'bottom';
    const POS_LEFT = 'left';
    const POS_RIGHT = 'right';
    const POS_TOP = 'top';

    protected $label = null;
    protected $position = self::POS_DEFAULT;

    public function __construct() {
    }

    public function get_label() {
        return $this->label;
    }

    public function get_position() {
        return $this->position;
    }

    public function jsonSerialize() {
        return [
            'label' => $this->label,
            'position' => $this->position,
        ];
    }

    public function set_label($label) {
        return $this->label = $label;
    }

    public function set_position($position) {
        return $this->position = $position;
    }

}
