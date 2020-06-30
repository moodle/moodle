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
 * Overriden course topics format renderer.
 *
 * @package    theme_moove
 * @copyright  2018 Willian Mano - http://conecti.me
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_moove\privacy;

use \core_privacy\local\metadata\collection;
use \core_privacy\local\metadata\provider as baseprovider;
use \core_privacy\local\request\user_preference_provider;
use \core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * The moove theme does not store any data.
 *
 * @copyright  2018 Willian Mano - http://conecti.me
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    // This plugin has data.
    baseprovider,
    // This plugin has some sitewide user preferences to export.
    user_preference_provider {

    /** The user preference for the font size. */
    const FONTSIZE = 'accessibilitystyles_fontsizeclass';
    /** The user preference for the site color. */
    const SITECOLOR = 'accessibilitystyles_sitecolorclass';
    /** The user preference for the font type. */
    const FONTTYPE = 'thememoovesettings_fonttype';
    /** The user preference for the enable accessibility toolbar. */
    const TOOLBAR = 'thememoovesettings_enableaccessibilitytoolbar';

    /**
     * Returns meta data about this system.
     *
     * @param  collection $items The initialised item collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $items) : collection {
        $items->add_user_preference(self::FONTSIZE, 'privacy:metadata:preference:accessibilitystyles_fontsizeclass');
        $items->add_user_preference(self::SITECOLOR, 'privacy:metadata:preference:accessibilitystyles_sitecolorclass');
        $items->add_user_preference(self::FONTTYPE, 'privacy:metadata:preference:thememoovesettings_fonttype');
        $items->add_user_preference(self::TOOLBAR, 'privacy:metadata:preference:thememoovesettings_enableaccessibilitytoolbar');
        return $items;
    }

    /**
     * Store all user preferences for the plugin.
     *
     * @param int $userid The userid of the user whose data is to be exported.
     * @throws \coding_exception
     */
    public static function export_user_preferences(int $userid) {
        $toolbar = get_user_preferences(self::TOOLBAR, null, $userid);
        if (isset($toolbar)) {
            writer::export_user_preference(
                'theme_moove',
                self::TOOLBAR,
                $toolbar,
                get_string('privacy:thememoovesettings_enableaccessibilitytoolbar', 'theme_moove', $toolbar)
            );

            $fontsize = get_user_preferences(self::FONTSIZE, null, $userid);
            if (isset($fontsize)) {
                writer::export_user_preference(
                    'theme_moove',
                    self::FONTSIZE,
                    $fontsize,
                    get_string('privacy:accessibilitystyles_fontsizeclass', 'theme_moove', $fontsize)
                );
            }

            $sitecolor = get_user_preferences(self::SITECOLOR, null, $userid);
            if (isset($sitecolor)) {
                writer::export_user_preference(
                    'theme_moove',
                    self::SITECOLOR,
                    $sitecolor,
                    get_string('privacy:accessibilitystyles_sitecolorclass', 'theme_moove', $sitecolor)
                );
            }
        }

        $fonttype = get_user_preferences(self::FONTTYPE, null, $userid);
        if (isset($fonttype)) {
            writer::export_user_preference(
                'theme_moove',
                self::FONTTYPE,
                $fonttype,
                get_string('privacy:thememoovesettings_fonttype', 'theme_moove', $fonttype)
            );
        }
    }
}
