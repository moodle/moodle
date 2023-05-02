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
 *
 * @package   theme_lambda
 * @copyright 2020 redPIthemes
 *
 */

namespace theme_lambda\privacy;

use \core_privacy\local\metadata\collection;

defined('MOODLE_INTERNAL') || die();


class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\user_preference_provider {

    const SITEBAR_STAT = 'theme_lambda_sidebar';

    public static function get_metadata(collection $items) : collection {
        $items->add_user_preference(self::SITEBAR_STAT, 'privacy:metadata:preference:sidebarstat');
        return $items;
    }

    public static function export_user_preferences(int $userid) {
        $draweropennavpref = get_user_preferences(self::SITEBAR_STAT, null, $userid);

        if (isset($draweropennavpref)) {
            $preferencestring = get_string('privacy_sidebar_closed', 'theme_lambda');
            if ($draweropennavpref == 'true') {
                $preferencestring = get_string('privacy_sidebar_open', 'theme_lambda');
            }
            \core_privacy\local\request\writer::export_user_preference(
                'theme_lambda',
                self::SITEBAR_STAT,
                $draweropennavpref,
                $preferencestring
            );
        }
    }
}
