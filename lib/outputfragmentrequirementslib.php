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
 * Library functions to facilitate the use of JavaScript in Moodle.
 *
 * Note: you can find history of this file in lib/ajax/ajaxlib.php
 *
 * @copyright 2016 Adrian Greeve <adrian@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core
 * @category output
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This class tracks all the things that are needed by the current page.
 *
 * Normally, the only instance of this class you will need to work with is the
 * one accessible via $PAGE->requires.
 *
 * Typical usage would be
 * <pre>
 *     $PAGE->requires->js_call_amd('mod_forum/view', 'init');
 * </pre>
 *
 * It also supports obsoleted coding style with/without YUI3 modules.
 * <pre>
 *     $PAGE->requires->js_init_call('M.mod_forum.init_view');
 *     $PAGE->requires->css('/mod/mymod/userstyles.php?id='.$id); // not overridable via themes!
 *     $PAGE->requires->js('/mod/mymod/script.js');
 *     $PAGE->requires->js('/mod/mymod/small_but_urgent.js', true);
 *     $PAGE->requires->js_function_call('init_mymod', array($data), true);
 * </pre>
 *
 * There are some natural restrictions on some methods. For example, {@link css()}
 * can only be called before the <head> tag is output. See the comments on the
 * individual methods for details.
 *
 * @copyright 2016 Adrian Greeve <adrian@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 3.1
 * @package core
 * @category output
 */
class fragment_requirements_manager extends page_requirements_manager {

    /**
     * Append YUI3 module to default YUI3 JS loader.
     * The structure of module array is described at {@link http://developer.yahoo.com/yui/3/yui/}
     *
     * @param string|array $module name of module (details are autodetected), or full module specification as array
     * @return void
     */
    public function js_module($module) {
        global $CFG;

        if (empty($module)) {
            throw new coding_exception('Missing YUI3 module name or full description.');
        }

        if (is_string($module)) {
            $module = $this->find_module($module);
        }

        if (empty($module) or empty($module['name']) or empty($module['fullpath'])) {
            throw new coding_exception('Missing YUI3 module details.');
        }

        $module['fullpath'] = $this->js_fix_url($module['fullpath'])->out(false);
        // Add all needed strings.
        if (!empty($module['strings'])) {
            foreach ($module['strings'] as $string) {
                $identifier = $string[0];
                $component = isset($string[1]) ? $string[1] : 'moodle';
                $a = isset($string[2]) ? $string[2] : null;
                $this->string_for_js($identifier, $component, $a);
            }
        }
        unset($module['strings']);

        // Process module requirements and attempt to load each. This allows
        // moodle modules to require each other.
        if (!empty($module['requires'])) {
            foreach ($module['requires'] as $requirement) {
                $rmodule = $this->find_module($requirement);
                if (is_array($rmodule)) {
                    $this->js_module($rmodule);
                }
            }
        }

        $this->extramodules[$module['name']] = $module;
    }


    /**
     * Returns js code to load amd module loader, then insert inline script tags
     * that contain require() calls using RequireJS.
     * @return string
     */
    protected function get_amd_footercode() {
        global $CFG;
        $output = '';

        // First include must be to a module with no dependencies, this prevents multiple requests.
        $prefix = "require(['core/first'], function() {\n";
        $suffix = "\n});";
        $output .= html_writer::script($prefix . implode(";\n", $this->amdjscode) . $suffix);
        return $output;
    }


    /**
     * Generate any HTML that needs to go at the end of the page.
     *
     * Normally, this method is called automatically by the code that prints the
     * page footer. You should not normally need to call it in your own code.
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

        $this->js_init_code('M.util.js_complete("init");', true);

        // All the other linked scripts - there should be as few as possible.
        if ($this->jsincludes['footer']) {
            foreach ($this->jsincludes['footer'] as $url) {
                $output .= html_writer::script('', $url);
            }
        }

        // Add all needed strings.
        $strings = array();
        foreach ($this->stringsforjs as $component => $v) {
            foreach ($v as $indentifier => $langstring) {
                $strings[$component][$indentifier] = $langstring->out();
            }
        }
        // Append don't overwrite.
        $output .= html_writer::script('require(["jquery"], function($) {
    M.str = $.extend(true, M.str, ' . json_encode($strings) . ');
});');

        // Add variables.
        if ($this->jsinitvariables['footer']) {
            $js = '';
            foreach ($this->jsinitvariables['footer'] as $data) {
                list($var, $value) = $data;
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

        // return 'bottom stuff';
        return $output;

    }
}
