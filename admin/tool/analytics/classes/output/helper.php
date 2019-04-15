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
 * Typical crappy helper class with tiny functions.
 *
 * @package   tool_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_analytics\output;

defined('MOODLE_INTERNAL') || die();

/**
 * Helper class with general purpose tiny functions.
 *
 * @package   tool_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * Converts a class full name to a select option key
     *
     * @param string $class
     * @return string
     */
    public static function class_to_option($class) {
        // Form field is PARAM_ALPHANUMEXT and we are sending fully qualified class names
        // as option names, but replacing the backslash for a string that is really unlikely
        // to ever be part of a class name.
        return str_replace('\\', '__', $class);
    }

    /**
     * option_to_class
     *
     * @param string $option
     * @return string
     */
    public static function option_to_class($option) {
        // Really unlikely but yeah, I'm a bad booyyy.
        return str_replace('__', '\\', $option);
    }

    /**
     * Sets an analytics > analytics models > $title breadcrumb.
     *
     * @param string $title
     * @param \moodle_url $url
     * @param \context|null $context Defaults to context_system
     * @return null
     */
    public static function set_navbar(string $title, \moodle_url $url, ?\context $context = null) {
        global $PAGE;

        if (!$context) {
            $context = \context_system::instance();
        }

        $PAGE->set_context($context);
        $PAGE->set_url($url);

        if ($siteadmin = $PAGE->settingsnav->find('root', \navigation_node::TYPE_SITE_ADMIN)) {
            $PAGE->navbar->add($siteadmin->get_content(), $siteadmin->action());
        }
        if ($analytics = $PAGE->settingsnav->find('analytics', \navigation_node::TYPE_SETTING)) {
            $PAGE->navbar->add($analytics->get_content(), $analytics->action());
        }
        if ($analyticmodels = $PAGE->settingsnav->find('analyticmodels', \navigation_node::TYPE_SETTING)) {
            $PAGE->navbar->add($analyticmodels->get_content(), $analyticmodels->action());
        }
        $PAGE->navbar->add($title);

        $PAGE->set_pagelayout('report');
        $PAGE->set_title($title);
        $PAGE->set_heading($title);
    }

    /**
     * Resets the current page.
     *
     * Note that this function can only be used by analytics pages that work at the system context.
     *
     * @return null
     */
    public static function reset_page() {
        global $PAGE;
        $PAGE->reset_theme_and_output();
        $PAGE->set_context(\context_system::instance());
    }
}
