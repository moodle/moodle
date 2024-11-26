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
 * Adaptable theme.
 *
 * Provider class file. As required for any data privacy information required.
 *
 * @package    theme_adaptable
 * @copyright  2019 Manoj Solanki (Coventry University)
 * @copyright  2023 G J Barnard
 * @author     G J Barnard -
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace theme_adaptable\privacy;

use core_privacy\local\request\writer;
use core_privacy\local\metadata\collection;

/**
 * Privacy provider.
 */
class provider implements
    // This plugin has data.
    \core_privacy\local\metadata\provider,

    // This plugin has some sitewide user preferences to export.
    \core_privacy\local\request\user_preference_provider {

    /**
     * Returns meta data about this system.
     *
     * @param   collection $items The initialised item collection to add items to.
     * @return  collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $items): collection {
        $items->add_user_preference('collapseblock', 'privacy:metadata:preference:collapseblock');
        $items->add_user_preference('drawer-open-index', 'privacy:metadata:preference:draweropenindex');
        $items->add_user_preference('drawer-open-block', 'privacy:metadata:preference:draweropenblock');
        $items->add_user_preference('theme_adaptable_zoom', 'privacy:metadata:preference:themeadaptablezoom');
        $items->add_user_preference('theme_adaptable_full', 'privacy:metadata:preference:themeadaptablefull');
        $items->add_user_preference('themeadaptablealertkey', 'privacy:metadata:preference:themeadaptablealertkey');
        return $items;
    }

    /**
     * Store all user preferences for the plugin.
     *
     * @param int $userid The user id of the user whose data is to be exported.
     */
    public static function export_user_preferences(int $userid) {
        $preferences = get_user_preferences(null, null, $userid);
        foreach ($preferences as $name => $value) {
            $blockid = null;
            $matches = [];
            if (preg_match('/(?<=block)\d*(?=hidden)/', $name, $matches)) {
                if (!empty($matches[0])) {
                    $blockid = $matches[0];
                    $decoded = ($value) ? get_string('privacy:open', 'theme_adaptable') :
                        get_string('privacy:closed', 'theme_adaptable');

                    writer::export_user_preference(
                        'theme_adaptable',
                        $name,
                        $value,
                        get_string('privacy:request:preference:collapseblock', 'theme_adaptable', (object) [
                            'name' => $name,
                            'blockid' => $blockid,
                            'value' => $value,
                            'decoded' => $decoded,
                        ])
                    );
                }
            } else if ($name == 'drawer-open-index') {
                $decoded = ($value) ? get_string('privacy:open', 'theme_adaptable') :
                    get_string('privacy:closed', 'theme_adaptable');

                writer::export_user_preference(
                    'theme_adaptable',
                    $name,
                    $value,
                    get_string('privacy:request:preference:draweropenindex', 'theme_adaptable', (object) [
                        'name' => $name,
                        'value' => $value,
                        'decoded' => $decoded,
                    ])
                );
            } else if ($name == 'drawer-open-block') {
                $decoded = ($value) ? get_string('privacy:open', 'theme_adaptable') :
                    get_string('privacy:closed', 'theme_adaptable');

                writer::export_user_preference(
                    'theme_adaptable',
                    $name,
                    $value,
                    get_string('privacy:request:preference:draweropenblock', 'theme_adaptable', (object) [
                        'name' => $name,
                        'value' => $value,
                        'decoded' => $decoded,
                    ])
                );
            } else if ($name == 'theme_adaptable_zoom') {
                $decoded = ($value) ? get_string('privacy:open', 'theme_adaptable') :
                    get_string('privacy:closed', 'theme_adaptable');

                writer::export_user_preference(
                    'theme_adaptable',
                    $name,
                    $value,
                    get_string('privacy:request:preference:themeadaptablezoom', 'theme_adaptable', (object) [
                        'name' => $name,
                        'value' => $value,
                        'decoded' => $decoded,
                    ])
                );
            } else if ($name == 'theme_adaptable_full') {
                $decoded = get_string('privacy:'.$value, 'theme_adaptable');

                writer::export_user_preference(
                    'theme_adaptable',
                    $name,
                    $value,
                    get_string('privacy:request:preference:themeadaptablefull', 'theme_adaptable', (object) [
                        'name' => $name,
                        'value' => $value,
                        'decoded' => $decoded,
                    ])
                );
            } else if (preg_match('/(?<=theme_adaptable_alertkey)\d*/', $name, $matches)) {
                // Now in local_adaptable - here to report alerts used with the theme when used with local_adaptable.
                if (!empty($matches[0])) {
                    $alertid = $matches[0];

                    writer::export_user_preference(
                        'theme_adaptable',
                        $name,
                        $value,
                        get_string('privacy:request:preference:themeadaptablealertkey', 'theme_adaptable', (object) [
                            'name' => $name,
                            'alertid' => $alertid,
                            'value' => $value,
                        ])
                    );
                }
            }
        }
    }
}
