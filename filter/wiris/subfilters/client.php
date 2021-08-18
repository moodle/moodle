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
//

/**
 * This filter does nothing to the received text.
 * Adds a Javascript library that will do all the heavy-lifting.
 *
 * @package    filter
 * @subpackage wiris
 * @copyright  WIRIS Europe (Maths for more S.L)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


class filter_wiris_client extends moodle_text_filter {

    /**
     * Set any context-specific configuration for this filter.
     *
     * @param context $context The current context.
     * @param array $localconfig Any context-specific configuration for this filter.
     */
    public function __construct($context, array $localconfig) {
        $this->context = $context;
        $this->localconfig = $localconfig;
    }

    /**
     * Includes the WIRISPlugins.js library with TECH = 'server'.
     * Since this filter does nothing to the markup server-wise, and waits for
     * the Javascript to update the received text, other active filters may
     * markup. Even if they have less priority than this one.
     * affect the expected.
     *
     * Important: The 'urltolink' filter is active by default in all new Moodle3_9
     * installations.
     *
     * @return [text] the received text as it is.
     */
    public function filter($text, array $options = array()) {
        global $PAGE;
        // Add the Javascript Thir-party library to the page.
        $PAGE->requires->js( new moodle_url('/filter/wiris/render/WIRISplugins.js?viewer=image&safeXml=true&async=true') );
        // Uses the option 'safeXml' to True to render directly from Safe MathML as stored on the database.
        // Therefore, this filter does not affect the markup server-side.
        // Options
        // - safeXml: true (acts on the SafeMathML as stored on the Moodle database)
        // - async: true (performance wise)
        // Return the text as it is.
        return $text;
    }

    /**
     * Returns true if 'urltolink' filter is active in active context.
     * Since this filter does nothing to the markup, and waits for the Javascript
     * to update the received text, other active filters may affect the expected
     * markup. Even if they have less priority than this one.
     *
     * Important: The 'urltolink' filter is active by default in all new Moodle3_9
     * installations.
     *
     * @return [bool] true if urltolink is on. False otherwise.
     */
    private function urltolink_is_on() {

        // The complex logic is working out the active state in the parent context,
        // so strip the current context from the list. We need avoid to call
        // filter_get_avaliable_in_context method if the context
        // is system context only.
        $contextids = explode('/', trim($this->context->path, '/'));
        array_pop($contextids);
        $contextids = implode(',', $contextids);
        // System context only.
        if (empty($contextids)) {
            return false;
        }

        $urltolinkpreference = false;
        $urltolinkfilteractive = false;
        $avaliablecontextfilters = filter_get_available_in_context($this->context);

        // First we need to know if urltolink filter is active in active context.
        if (array_key_exists('urltolink', $avaliablecontextfilters)) {
            $urltolinkfilter = $avaliablecontextfilters['urltolink'];
            $urltolinkfilteractive = $urltolinkfilter->localstate == TEXTFILTER_ON ||
                                   ($urltolinkfilter->localstate == TEXTFILTER_INHERIT &&
                                    $urltolinkfilter->inheritedstate == TEXTFILTER_ON);
        }
        return $urltolinkfilteractive;
    }
}
