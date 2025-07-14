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

namespace core_cache;

/**
 * Class wrapping information in the cache that is tagged with a version number.
 *
 * @package core_cache
 * @copyright 2021 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class version_wrapper {
    /**
     * The data being stored.
     * @var mixed
     */
    public $data;

    /**
     * Version number for the data
     * @var int
     */
    public $version;

    /**
     * Constructs a version tag wrapper.
     *
     * @param mixed $data
     * @param int $version Version number
     */
    public function __construct($data, int $version) {
        $this->data = $data;
        $this->version = $version;
    }
}
