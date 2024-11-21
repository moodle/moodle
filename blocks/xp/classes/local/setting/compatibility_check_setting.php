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
 * Setting.
 *
 * @package    block_xp
 * @copyright  2023 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\setting;

use block_xp\di;
use core_plugin_manager;

/**
 * Setting.
 *
 * @package    block_xp
 * @copyright  2023 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class compatibility_check_setting extends static_setting {

    /**
     * Constructor.
     */
    public function __construct() {
        parent::__construct('block_xp/compatibilitycheck', get_string('compatibilitycheck', 'block_xp'), '');
    }

    /**
     * Get HTML content.
     *
     * @return string
     */
    protected function get_html_content() {
        global $CFG;

        $pluginman = core_plugin_manager::instance();
        $addon = di::get('addon');
        $messages = [];

        $blockxp = $pluginman->get_plugin_info('block_xp');
        $localxp = $pluginman->get_plugin_info('local_xp');
        $humanbranch = moodle_major_version() ?: 'v?';

        if ($blockxp && strpos($blockxp->release, '-dev') !== false) {
            $messages[] = [
                'title' => get_string('unstableversioninstalled', 'block_xp'),
                'message' => get_string('unstableversioninstalledinfo', 'block_xp', ['version' => $blockxp->release]),
            ];
        }

        if ($addon->is_out_of_sync()) {
            $messages[] = [
                'title' => get_string('outofsync', 'block_xp'),
                'message' => get_string('outofsyncinfo', 'block_xp', [
                    'localxpversion' => $addon->get_expected_release(),
                ]),
                'url' => 'https://docs.levelup.plus/xp/docs/requirements-compatibility#out-of-sync',
            ];
        }

        if ($CFG->branch >= 39) {
            if (!empty($blockxp->pluginsupported) && ($CFG->branch < $blockxp->pluginsupported[0]
                    || $CFG->branch > $blockxp->pluginsupported[1])) {
                $messages[] = [
                    'title' => get_string('potentialmoodleincompatibility', 'block_xp'),
                    'message' => get_string('pluginxmaybeincompatible', 'block_xp',
                        ['name' => 'Level Up XP', 'component' => 'block_xp', 'version' => $humanbranch]),
                    'url' => 'https://docs.levelup.plus/xp/docs/requirements-compatibility#potential-moodle-incompatibility',
                ];
            }
            if ($localxp && (empty($localxp->pluginsupported) || ($CFG->branch < $localxp->pluginsupported[0]
                    || $CFG->branch > $localxp->pluginsupported[1]))) {
                $messages[] = [
                    'title' => get_string('potentialmoodleincompatibility', 'block_xp'),
                    'message' => get_string('pluginxmaybeincompatible', 'block_xp',
                        ['name' => 'Level Up XP+', 'component' => 'local_xp', 'version' => $humanbranch]),
                    'url' => 'https://docs.levelup.plus/xp/docs/requirements-compatibility#potential-moodle-incompatibility',
                ];
            }
        }

        return di::get('renderer')->render_from_template('block_xp/admin-compatibility-check', [
            'haswarnings' => !empty($messages),
            'warnings' => $messages,
        ]);
    }

}
