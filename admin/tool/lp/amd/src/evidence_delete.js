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
 * Evidence delete.
 *
 * @package    tool_lp
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery',
        'core/notification',
        'core/ajax',
        'core/str',
        'core/log'],
        function($, Notification, Ajax, Str, Log) {

    var selectors = {};

    /**
     * Register an event listener.
     *
     * @param {String} triggerSelector The node on which the click will happen.
     * @param {String} containerSelector The parent node that will be removed and contains the evidence ID.
     */
    var register = function(triggerSelector, containerSelector) {
        if (typeof selectors[triggerSelector] !== 'undefined') {
            return;
        }

        selectors[triggerSelector] = $('body').delegate(triggerSelector, 'click', function(e) {
            var parent = $(e.currentTarget).parents(containerSelector);
            if (!parent.length || parent.length > 1) {
                Log.error('None or too many evidence container were found.');
                return;
            }
            var evidenceId = parent.data('id');
            if (!evidenceId) {
                Log.error('Evidence ID was not found.');
                return;
            }

            e.preventDefault();
            e.stopPropagation();

            Str.get_strings([
                {key: 'confirm', component: 'moodle'},
                {key: 'areyousure', component: 'moodle'},
                {key: 'delete', component: 'moodle'},
                {key: 'cancel', component: 'moodle'}
            ]).done(function(strings) {
                Notification.confirm(
                    strings[0], // Confirm.
                    strings[1], // Are you sure?
                    strings[2], // Delete.
                    strings[3], // Cancel.
                    function() {
                        var promise = Ajax.call([{
                            methodname: 'core_competency_delete_evidence',
                            args: {
                                id: evidenceId
                            }
                        }]);
                        promise[0].then(function() {
                            parent.remove();
                        }).fail(Notification.exception);
                    }
                );
            }).fail(Notification.exception);


        });
    };

    return /** @alias module:tool_lp/evidence_delete */ {

        /**
         * Register an event listener.
         *
         * @param {String} triggerSelector The node on which the click will happen.
         * @param {String} containerSelector The parent node that will be removed and contains the evidence ID.
         * @return {Void}
         */
        register: register
    };

});
