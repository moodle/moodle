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
 * Mustache helper to load strings from string_manager and perform HTML escaping on them.
 *
 * @package    core
 * @category   output
 * @copyright  2021 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\output;

use Mustache_LambdaHelper;

/**
 * This class will load language strings in a template.
 *
 * @copyright  2021 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      4.0
 */
class mustache_clean_string_helper {

    /** @var mustache_string_helper A string helper instance that is being used internally for fetching strings */
    private $stringhelper;

    /**
     * Create new instance of mustache clean string helper.
     */
    public function __construct() {
        $this->stringhelper = new \core\output\mustache_string_helper();
    }

    /**
     * Read a lang string from a template and get it from get_string.
     *
     * Some examples for calling this from a template are:
     *
     * {{#cleanstr}}activity{{/cleanstr}}
     * {{#cleanstr}}actionchoice, core, {{#str}}delete{{/str}}{{/cleanstr}} (Together with the str helper)
     * {{#cleanstr}}addinganewto, core, {"what":"This", "to":"That"}{{/cleanstr}} (Complex $a)
     *
     * The args are comma separated and only the first is required.
     * The last is a $a argument for get string. For complex data here, use JSON.
     *
     * @param string $text The text to parse for arguments.
     * @param Mustache_LambdaHelper $helper Used to render nested mustache variables.
     * @return string
     */
    public function cleanstr($text, Mustache_LambdaHelper $helper) {
        return s($this->stringhelper->str($text, $helper));
    }
}
