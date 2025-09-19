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
 * @module    mod_quiz/searchwidget/user
 * @copyright 2024 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import UserSearch from 'core_user/comboboxsearch/user';
import Url from 'core/url';
import * as Repository from 'mod_quiz/searchwidget/repository';
import {getStrings} from 'core/str';

export default class QuizUserSearch extends UserSearch {

    static extraParams;
    constructor() {
        super();
    }

    static init(extraParams = null) {
        QuizUserSearch.extraParams = extraParams;
        return new QuizUserSearch();
    }

    /**
     * Get the data we will be searching against in this component.
     *
     * @returns {Promise<*>}
     */
    async fetchDataset() {
        const params = {
            cmid: QuizUserSearch.extraParams.cmid,
            mode: QuizUserSearch.extraParams.reportmode,
            params: JSON.stringify(QuizUserSearch.extraParams?.params),
        };

        // Await the user fetch request
        const r = await Repository.userFetch(params);

        // Await the result of getStrings
        const stringArray = await getStrings(r.extrafields.map((key) => ({key})));

        // Map the extrafields to profilestringmap
        this.profilestringmap = new Map(
            r.extrafields.map((key, index) => ([key, stringArray[index]]))
        );

        // Return r.users as the final result
        return r.users;
    }

    /**
     * Build up the view all link.
     *
     * @returns {string|*}
     */
    selectAllResultsLink() {
        return Url.relativeUrl(QuizUserSearch.extraParams.path, {
            ...QuizUserSearch.extraParams.params,
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
        return Url.relativeUrl(QuizUserSearch.extraParams.path, {
            ...QuizUserSearch.extraParams.params,
            gpr_search: this.getSearchTerm(),
            gpr_userid: userID,
        }, false);
    }
}
