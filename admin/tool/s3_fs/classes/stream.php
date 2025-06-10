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
 * Stream factory.
 *
 * @package   tool_s3_fs
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_s3_fs;

use Psr\Http\Message\StreamInterface;

/**
 * Stream factory.
 *
 * @package   tool_s3_fs
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stream {
    /**
     * @param string $path
     * @return StreamInterface
     */
    public static function from_file($path) {
        $resource = fopen($path, 'rb');
        if ($resource === false) {
            throw new \file_exception('storedfilecannotread', '', $path);
        }

        return \GuzzleHttp\Psr7\stream_for($resource);
    }

    /**
     * @param string $string
     * @return StreamInterface
     */
    public static function from_string($string) {
        return \GuzzleHttp\Psr7\stream_for($string);
    }
}
