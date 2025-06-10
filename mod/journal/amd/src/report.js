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
 * Javascript module for the report page
 *
 * @module      mod_journal/createtemplate
 * @copyright   2022 elearning & software srl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Notification from 'core/notification';
import { get_string as getString } from 'core/str';
import { add as addToast } from 'core/toast';
import $ from 'jquery';

export const init = () => {
    $('.saveindividualfeedback').on('click', event => {
        event.preventDefault();
        const element = $(event.currentTarget);
        const sesskey = M.cfg.sesskey;
        const cmid = element.attr('data-cmid');
        const userid = element.attr('data-userid');
        const entryid = element.attr('data-entryid');
        const feedback = $('#c' + entryid).val();
        const grade = $('#r' + entryid).val();

        $.ajax(M.cfg.wwwroot + '/mod/journal/ajax/ajax.php', {
            data: {
                action: 'saveindividualfeedback',
                sesskey,
                cmid,
                userid,
                entryid,
                feedback,
                grade
            },
            dataType: 'json',
            method: 'post',
            success: (response) => {
                if (response.status === 'ok') {
                    addToast(response.content);
                } else {
                    getString('saving_failed', 'feedback').then(string => {
                        return Notification.addNotification({
                            type: 'error',
                            message: string + ': ' + response.content
                        });
                    }).catch();
                }
            },
            error: (error) => {
                getString('saving_failed', 'feedback').then(string => {
                    return Notification.addNotification({
                        type: 'error',
                        message: string + ': ' + error
                    });
                }).catch();
            }
        });
    });
};