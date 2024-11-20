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
 * A subclass of cachestore_file but which doesn't report that it has TTL support.
 *
 * This is so we can easily test behaviour involving the TTL wrapper objects.
 *
 * @package core_cache
 * @copyright 2021 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cachestore_file_with_ttl_wrappers extends cachestore_file {
    /**
     * Reports the same supported features as the parent, but without SUPPORTS_NATIVE_TTL.
     *
     * @param array $configuration Configuration
     * @return int Supported features
     */
    public static function get_supported_features(array $configuration = []) {
        return parent::get_supported_features($configuration) - self::SUPPORTS_NATIVE_TTL;
    }
}
