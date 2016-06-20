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

    const TYPE_DEFAULT = null;
    const TYPE_LINE = 'line';

    protected $label;
    protected $type = self::TYPE_DEFAULT;
    protected $values = [];

    public function __construct($label, $values) {
        $this->values = $values;
        $this->label = $label;
    }

    public function get_count() {
        return count($this->values);
    }

    public function get_label() {
        return $this->label;
    }

    public function get_type() {
        return $this->type;
    }

    public function get_values() {
        return $this->values;
    }

    public function jsonSerialize() {
        $data = [
            'label' => $this->label,
            'type' => $this->type,
            'values' => $this->values
        ];
        return $data;
    }

    public function set_color($color) {
        $this->color = $color;
    }

    public function set_type($type) {
        if (!in_array($type, [self::TYPE_DEFAULT, self::TYPE_LINE])) {
            throw new coding_exception('Invalid serie type.');
        }
        $this->type = $type;
    }

}
