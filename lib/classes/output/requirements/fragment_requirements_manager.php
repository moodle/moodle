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

namespace core\output\requirements;

use core\output\html_writer;
use core\output\js_writer;

/**
 * This requirements manager captures the appropriate html for creating a fragment to
 * be inserted elsewhere.
 *
 * @copyright 2016 Adrian Greeve <adrian@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 3.1
 * @package core
 * @category output
 */
class fragment_requirements_manager extends page_requirements_manager {
    /**
     * Page fragment constructor.
     */
    public function __construct() {
        parent::__construct();
        // As this is a fragment the header should already be done.
        $this->headdone = true;
    }

    /**
     * Returns js code to load amd module loader, then insert inline script tags
     * that contain require() calls using RequireJS.
     *
     * @return string
     */
    protected function get_amd_footercode() {
        global $CFG;
        $output = '';

        // First include must be to a module with no dependencies, this prevents multiple requests.
        $prefix = 'M.util.js_pending("core/first");';
        $prefix .= "require(['core/first'], function() {\n";
        $suffix = "\n});";
        $suffix .= 'M.util.js_complete("core/first");';
        $output .= html_writer::script($prefix . implode(";\n", $this->amdjscode) . $suffix);
        return $output;
    }

    /**
     * Generate any HTML that needs to go at the end of the page.
     *
     * @return string the HTML code to to at the end of the page.
     */
    public function get_end_code() {
        global $CFG;

        $output = '';

        // Call amd init functions.
        $output .= $this->get_amd_footercode();

        // Add other requested modules.
        $output .= $this->get_extra_modules_code();

        // All the other linked scripts - there should be as few as possible.
        if ($this->jsincludes['footer']) {
            foreach ($this->jsincludes['footer'] as $url) {
                $output .= html_writer::script('', $url);
            }
        }

        if (!empty($this->stringsforjs)) {
            // Add all needed strings.
            $strings = [];
            foreach ($this->stringsforjs as $component => $v) {
                foreach ($v as $indentifier => $langstring) {
                    $strings[$component][$indentifier] = $langstring->out();
                }
            }
            // Append don't overwrite.
            $output .= html_writer::script('require(["jquery"], function($) {
                M.str = $.extend(true, M.str, ' . json_encode($strings) . ');
            });');
        }

        // Add variables.
        if ($this->jsinitvariables['footer']) {
            $js = '';
            foreach ($this->jsinitvariables['footer'] as $data) {
                [$var, $value] = $data;
                $js .= js_writer::set_variable($var, $value, true);
            }
            $output .= html_writer::script($js);
        }

        $inyuijs = $this->get_javascript_code(false);
        $ondomreadyjs = $this->get_javascript_code(true);
        // See if this is still needed when we get to the ajax page.
        $jsinit = $this->get_javascript_init_code();
        $handlersjs = $this->get_event_handler_code();

        // There is a global Y, make sure it is available in your scope.
        $js = "(function() {{$inyuijs}{$ondomreadyjs}{$jsinit}{$handlersjs}})();";

        $output .= html_writer::script($js);

        return $output;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(fragment_requirements_manager::class, \fragment_requirements_manager::class);
