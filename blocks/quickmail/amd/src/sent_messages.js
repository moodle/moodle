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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali, David Lowe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */



define([
    'jquery',
    'block_quickmail/notifications',
    'block_quickmail/jaxy'
], function($, noti, jaxy) {
    'use strict';

    let removeSentItems = function(data) {
        jaxy.qmAjax(JSON.stringify({
            'call': 'remove_sent_messages',
            'params': {
                'ids': data
            },
            // 'path': 'classes/external/',
            'class': 'sent_messages_ctrl'
        // eslint-disable-next-line promise/always-return
        })).then(function (response) {
            noti.storeMsg(response);
            /* TODO: To reduce server calls via page reload this could remove table
             rows but needs to factor the pagination results.
             for (var i of response.data.ids) {
                 // $(this).find("[data-msgid='" + i + "']").closest("tr").remove();
                 $('.qm_sent_msgs').find("[data-msgid='" + i + "']").closest("tr").remove();
             }
             But instead, let's just reload the page
            */
            location.reload();
        }).catch ();
    };

    return {
        init: function() {
            // Show any pending messages.
            noti.showMsg();

            // Single Click Delete (Trash Icon).
            $('.qm_sent_msgs').on('click', '.qm_sm_trash', function (ev) {
                ev.preventDefault();
                removeSentItems([$(this).data("msgid")]);
            });

            // Single Checkbox Click.
            // If more than 1 checkbox is checked then show the "remove selected" button.
            $('.qm_sm_cb').click(function() {
                let counter = $(":checkbox:checked").length;
                if (counter > 1) {
                    $("#qm_sm_selected_remove").show();
                } else {
                    $("#qm_sm_selected_remove").hide();
                }
            });

            // Select ALL Checkboxes.
            $("#qm_sm_select_all").click(function () {
                $("#qm_sm_selected_remove").toggle(this.checked);
                $('input:checkbox').prop('checked', this.checked);
            });

            // Remove Selected Button Click.
            $('.qm_sent_msgs').on('click', '#qm_sm_selected_remove', function (ev) {
                ev.preventDefault();
                let remove_list = [];
                $(".qm_sm_cb:checkbox:checked").each(function() {
                    remove_list.push($(this).data("msgid"));
                });
                removeSentItems(remove_list);
            });
        }
    };
});
