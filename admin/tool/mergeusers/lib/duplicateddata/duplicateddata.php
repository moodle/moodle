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
 * @author    Daniel Tomé <danieltomefer@gmail.com>
 * @copyright 2018 Servei de Recursos Educatius (http://www.sre.urv.cat)
 */

defined('MOODLE_INTERNAL') || die();

class DuplicatedData {
    private $toremove;

    private $tomodify;

    public static function from_empty() {
        return new static([], []);
    }

    public static function from_remove_and_modify($toremove, $tomodify) {
        return new static(array_combine($toremove, $toremove), array_combine($tomodify, $tomodify));
    }

    public static function from_remove($toremove) {
        return new static(array_combine($toremove, $toremove), []);
    }

    private function __construct($toremove, $tomodify) {
        $this->toremove = $toremove;
        $this->tomodify = $tomodify;
    }

    public function to_remove() {
        return $this->toremove;
    }

    public function to_modify() {
        return $this->tomodify;
    }
}
