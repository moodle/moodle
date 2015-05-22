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
 * Mustache helper to load strings from string_manager.
 *
 * @package    core
 * @category   output
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\output;

use Mustache_LambdaHelper;
use stdClass;

/**
 * This class will load language strings in a template.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */
class mustache_string_helper {

    /**
     * Read a lang string from a template and get it from get_string.
     *
     * Some examples for calling this from a template are:
     *
     * {{#str}}activity{{/str}}
     * {{#str}}actionchoice, core, {{#str}}delete{{/str}}{{/str}} (Nested)
     * {{#str}}addinganewto, core, {"what":"This", "to":"That"}{{/str}} (Complex $a)
     *
     * The args are comma separated and only the first is required.
     * The last is a $a argument for get string. For complex data here, use JSON.
     *
     * @param string $text The text to parse for arguments.
     * @param Mustache_LambdaHelper $helper Used to render nested mustache variables.
     * @return string
     */
    public function str($text, Mustache_LambdaHelper $helper) {
        // Split the text into an array of variables.
        $key = strtok($text, ",");
        $key = trim($key);
        $component = strtok(",");
        $component = trim($component);
        if (!$component) {
            $component = '';
        }

        $a = new stdClass();

        $next = strtok('');
        $next = trim($next);
        if ((strpos($next, '{') === 0) && (strpos($next, '{{') !== 0)) {
            $rawjson = $helper->render($next);
            $a = json_decode($rawjson);
        } else {
            $a = $helper->render($next);
        }
        return get_string($key, $component, $a);
    }
}

