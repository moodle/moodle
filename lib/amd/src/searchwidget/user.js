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
 * Allow the user to search for student within the report.
 *
 * @module    core/searchwidget/user
 * @copyright 2023 Mathew May <mathew.solutions>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import UserSearch from 'core_user/comboboxsearch/user';
import Url from 'core/url';
import * as Repository from 'core/searchwidget/repository';

export default class User extends UserSearch {

    static extraParams;
    constructor() {
        super();
    }

    static init(extraParams = null) {
        User.extraParams = extraParams;
        return new User();
    }

    /**
     * Get the data we will be searching against in this component.
     *
     * @returns {Promise<*>}
     */
    fetchDataset() {
        if (User?.extraParams?.cmid === undefined) {
            return Repository.userFetch({courseid: User.extraParams.courseid},
                User.extraParams.service).then((r) => r.users);
        }

        const params = {
            cmid: User.extraParams.cmid,
            mode: User.extraParams.reportmode,
            params: JSON.stringify(User.extraParams?.params),
        };

        return Repository.userFetch(params,
            User.extraParams.service).then((r) => {
                // We need to update more extra strings to allow users to search more fields like: username,
                // custom profile fields.
                let extraStrings = [
                    'username',
                ];

                if (r.users.length > 0) {
                    // Update the profile_field key for additional fields.
                    // Retrieve the first record to analyze its index.
                    Object.keys(r.users[0]).forEach(key => {
                        // We only get the profile fields key.
                        if (/^profile_field_(.*)$/.test(key)) {
                            extraStrings.push(key);
                        }
                    });
                }
                // Update required strings to allow searching by profile_field.
                super.updateRequiredStrings(extraStrings);

                return r.users;
            });
    }

    /**
     * Build up the view all link.
     *
     * @returns {string|*}
     */
    selectAllResultsLink() {
        return Url.relativeUrl(User.extraParams.path, {
            ...User.extraParams.params,
            gpr_search: this.getSearchTerm(),
        }, false);
    }

    /**
     * Build up the link that is dedicated to a particular result.
     *
     * @param {Number} userID The ID of the user selected.
     * @returns {string|*}
     */
    selectOneLink(userID) {
        return Url.relativeUrl(User.extraParams.path, {
            ...User.extraParams.params,
            gpr_search: this.getSearchTerm(),
            gpr_userid: userID,
        }, false);
    }
}
