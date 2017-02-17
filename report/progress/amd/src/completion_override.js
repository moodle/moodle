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
 * AMD module to handle overriding activity completion status.
 *
 * @module     report_progress/completion_override
 * @package    report_progress
 * @copyright  2016 onwards Eiz Eddin Al Katrib <eiz@barasoft.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.1
 */
define(['jquery', 'core/ajax', 'core/str', 'core/notification'],
        function($, ajax, str, notification) {
    return /** @alias module:report_progress/completion_override */ {

        /**
         * Change the activity completion state.
         *
         * @method change
         */
        update: function() {

            $('#completion-progress a.changecompl').on('click', function(e) {
                e.preventDefault();

                var el = $(this);
                var changecompl = el.data('changecompl');
                var changecomplfields = changecompl.split('-');
                var userid = changecomplfields[0];
                var cmid = changecomplfields[1];
                var newstate = changecomplfields[2];
                var newstatestr = (newstate == 1) ? 'completion-y' : 'completion-n';

                str.get_strings([
                    {key: newstatestr, component: 'completion'}
                ]).done(function(strings) {
                    str.get_strings([
                        {key: 'confirm', component: 'moodle'},
                        {key: 'areyousureoverridecompletion', component: 'completion', param: strings[0]},
                        {key: 'yes', component: 'moodle'},
                        {key: 'cancel', component: 'moodle'}
                    ]).done(function(strings) {
                        notification.confirm(
                            strings[0], // Confirm.
                            strings[1], // Message.
                            strings[2], // Yes.
                            strings[3], // Cancel.
                            function() {
                                el.append('<div class="ajaxworking" />');

                                var promise = ajax.call([{
                                    methodname: 'core_completion_override_activity_completion_status',
                                    args: {
                                        userid: userid, cmid: cmid, newstate: newstate
                                    }
                                }]);

                                promise[0].then(function(results) {
                                    el.data('changecompl', results.changecompl);
                                    el.attr('data-changecompl', results.changecompl);
                                    el.children("img").replaceWith(results.img);
                                    $('.ajaxworking').remove();
                                }).fail(notification.exception);
                            }
                        );
                    }).fail(notification.exception);
                }).fail(notification.exception);

            });
        }
    };
});
