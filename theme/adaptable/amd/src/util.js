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
// Util Module based on core_user/repository.
//
// @module     theme_adaptable/util
// @copyright  2020 Andrew Nicols <andrew@nicols.co.uk>
// @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
// @copyright  2023 G J Barnard.
// @author     G J Barnard -
//               {@link https://moodle.org/user/profile.php?id=442195}
//               {@link https://gjbarnard.co.uk}
// @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
//

import {call as fetchMany} from 'core/ajax';

/**
 * Set single user preference
 *
 * @param {String} name Name of the preference
 * @param {String|null} value Value of the preference (omit if you want to remove the current value)
 * @return {Promise}
 */
export const setUserPreference = (name, value = null) => {
    return setUserPreferences([{name, value}]);
};

/**
 * Set multiple user preferences
 *
 * @param {Object[]} preferences Array of preferences containing name/value attributes
 * @return {Promise}
 */
export const setUserPreferences = (preferences) => {
    return fetchMany([{
        methodname: 'theme_adaptable_user_set_user_preferences',
        args: {preferences}
    }])[0];
};
