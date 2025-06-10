<?php
// This file is part of the gradereport rubrics plugin
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
namespace gradereport_rubrics;
use core_text;

/**
 * Provides CSV functionality.
 *
 * @package    gradereport_rubrics
 * @copyright  2021 onward Brickfield Education Labs Ltd, https://www.brickfield.ie
 * @author     2021 Karen Holland <karen@brickfieldlabs.ie>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class csv {

    /**
     * Replace or add proper quotations for csv files
     *
     * @param mixed $value
     * @param mixed $excel
     * @return void
     */
    public function csv_quote($value, $excel) {
        if ($excel) {
            return core_text::convert('"'.str_replace('"', "'", $value).'"', 'UTF-8', 'UTF-16LE');
        } else {
            return '"'.str_replace('"', "'", $value).'"';
        }
    }
}
