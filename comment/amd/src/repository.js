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
 * Module to handle comment AJAX requests
 *
 * @module      core_comment/repository
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';

/**
 * Delete single comment
 *
 * @param {Number} comment Comment ID
 * @return {Promise}
 */
export const deleteComment = comment => deleteComments([comment]);

/**
 * Delete multiple comments
 *
 * @param {Number[]} comments Comment IDs
 * @return {Promise}
 */
export const deleteComments = comments => {
    const request = {
        methodname: 'core_comment_delete_comments',
        args: {comments}
    };

    return Ajax.call([request])[0];
};
