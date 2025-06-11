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

namespace tool_brickfield\local\htmlchecker\guidelines;

use tool_brickfield\local\htmlchecker\brickfield_accessibility_guideline;
use tool_brickfield\manager;

/**
 * Brickfield Guideline
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class brickfield_guideline extends brickfield_accessibility_guideline {

    /**
     * brickfield_guideline constructor.
     * @param \DOMDocument $dom
     * @param \brickfield_accessibility_css $css
     * @param array $path
     * @param null $arg
     * @param string $domain
     * @param bool $cmsmode
     * @throws \dml_exception
     */
    public function __construct(&$dom, &$css, array &$path, $arg = null, string $domain = 'en', bool $cmsmode = false) {
        global $DB;

        $this->tests = array_values($DB->get_records_menu(manager::DB_CHECKS, null, '', 'id,shortname'));

        parent::__construct($dom, $css, $path, $arg, $domain, $cmsmode);
    }
}
