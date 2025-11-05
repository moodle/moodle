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
 * A javascript module to handle the board.
 *
 * @author     Karen Holland <karen@brickfieldlabs.ie>
 * @copyright  2021 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Board from "mod_board/board";
import Ajax from 'core/ajax';
import Notification from 'core/notification';

/**
 * Fetch the board configuration.
 *
 * @param {number} boardid the board id
 * @param {number} ownerid the owner id
 * @param {number} groupid the owner id
 * @returns {Promise} config
 */
const fetchBoardConfiguration = (boardid, ownerid, groupid) => {
    return Ajax.call([
        {
            methodname: 'mod_board_get_configuration',
            args: {
                id: boardid,
                ownerid: ownerid,
                groupid: groupid,
            },
            done: (response) => {
                return response;
            },
            fail: Notification.exception
        }
    ], false);
};

/**
 * Initialize the board.
 * @param {number} boardid The board id
 * @param {number} ownerid The owner id - 0 when single user mode disble
 * @param {number} groupid The group id
 */
const initialize = (boardid, ownerid, groupid) => {
    const promise = fetchBoardConfiguration(boardid, ownerid, groupid);
    promise[0].then((config) => {
        return new Board(config);
    }).catch((error) => {
        Notification.exception(error);
    });
};

export default {
    initialize: initialize
};
