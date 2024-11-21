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
 * Recommended plugins setting.
 *
 * @package    block_xp
 * @copyright  2022 Branch Up Pty Ltd
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\setting;

use block_xp\di;
use core_plugin_manager;
use moodle_url;

/**
 * Recommended plugins setting.
 *
 * @package    block_xp
 * @copyright  2022 Branch Up Pty Ltd
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class recommended_plugins_setting extends static_setting {

    /**
     * Constructor.
     */
    public function __construct() {
        parent::__construct('block_xp/recommendedplugins', get_string('recommendedplugins', 'block_xp'), '');
    }

    /**
     * Get HTML content.
     *
     * @return string
     */
    protected function get_html_content() {
        $pluginman = core_plugin_manager::instance();

        $plugins = array_map(function($plugin) use ($pluginman) {
            $isinstalled = !empty($plugin['isinstalled']);
            if (!isset($plugin['isinstalled'])) {
                $plugininfo = $pluginman->get_plugin_info($plugin['component']);
                $isinstalled = !empty($plugininfo);
            }
            return array_merge($plugin, ['isinstalled' => $isinstalled]);
        }, [
            [
                'component' => 'availability_xp',
                'name' => 'Level Up XP Availability',
                'description' => get_string('pluginavailabilityxpdesc', 'block_xp'),
                'url' => new moodle_url('https://moodle.org/plugins/availability_xp'),
            ],
            [
                'component' => 'enrol_xp',
                'name' => 'Level Up XP Enrolment',
                'description' => get_string('pluginenrolxpdesc', 'block_xp'),
                'url' => new moodle_url('https://moodle.org/plugins/enrol_xp'),
            ],
            [
                'component' => 'filter_shortcodes',
                'name' => 'Shortcodes',
                'description' => get_string('pluginshortcodesdesc', 'block_xp'),
                'url' => new moodle_url('https://moodle.org/plugins/filter_shortcodes'),
            ],
        ]);

        return di::get('renderer')->render_from_template('block_xp/admin-recommended-plugins', ['plugins' => $plugins]);
    }

}
