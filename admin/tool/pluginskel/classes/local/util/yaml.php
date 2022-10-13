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
 * Provides tool_pluginskel\local\util\yaml class.
 *
 * @package     tool_pluginskel
 * @subpackage  util
 * @copyright   2016 Alexandru Elisei <alexandru.elisei@gmail.com>, David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_pluginskel\local\util;

use Symfony\Component\Yaml\Yaml as SymfonyYaml;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/'.$CFG->admin.'/tool/pluginskel/vendor/autoload.php');

/**
 * Wrapper for YAML format processing
 *
 * @copyright 2016 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class yaml {

    /**
     * Parse given YAML string
     *
     * @param string $string
     * @return array
     */
    public static function decode_string($string) {
        return SymfonyYaml::parse($string);
    }

    /**
     * Parse given YAML file
     *
     * @param string $path
     * @return array
     */
    public static function decode_file($path) {

        if (!is_readable($path)) {
            throw new exception('Unable to read YAML file: '.$path);
        }

        return SymfonyYaml::parseFile($path);
    }

    /**
     * Converts given data into YAML
     *
     * @param array $data
     * @return string
     */
    public static function encode(array $data) {
        return SymfonyYaml::dump($data, 10, 2);
    }
}
