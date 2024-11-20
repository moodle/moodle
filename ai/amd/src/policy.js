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

import {getPolicyStatus, setPolicyStatus} from "./repository";

/**
 * The Javascript module to handle the policy acceptance.
 *
 * @module     core_ai/policy
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
export default class {
    static #policyAcceptedFor = {};

    static preconfigurePolicyState(userid, state) {
        if (!this.#policyAcceptedFor.hasOwnProperty(userid)) {
            this.#policyAcceptedFor[userid] = state;
        }
    }

    /**
     * Get the policy status for a user.
     *
     * @param {Number} userid The user ID.
     * @return {Promise<Object>} The policy status.
     */
    static async getPolicyStatus(userid) {
        if (this.#policyAcceptedFor[userid]) {
            return this.#policyAcceptedFor[userid];
        }

        const accepted = await getPolicyStatus(userid);

        this.#policyAcceptedFor[userid] = accepted.status;

        return accepted.status;
    }

    static acceptPolicy() {
        this.#policyAcceptedFor[M.cfg.userId] = true;

        return setPolicyStatus(M.cfg.contextid);
    }
}
