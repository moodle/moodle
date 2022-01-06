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
 * Mustache helper that will add JS to the end of the page.
 *
 * @package    core
 * @category   output
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\output;

/**
 * Store a list of JS calls to insert at the end of the page.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */
class mustache_javascript_helper {

    /** @var moodle_page $page - Page used to get requirement manager */
    private $page = null;

    /**
     * Create new instance of mustache javascript helper.
     *
     * @param moodle_page $page Page.
     */
    public function __construct($page) {
        $this->page = $page;
    }

    /**
     * Add the block of text to the page requires so it is appended in the footer. The
     * content of the block can contain further mustache tags which will be resolved.
     *
     * @param string $text The script content of the section.
     * @param \Mustache_LambdaHelper $helper Used to render the content of this block.
     */
    public function help($text, \Mustache_LambdaHelper $helper) {
        $this->page->requires->js_amd_inline($helper->render($text));
    }
}
