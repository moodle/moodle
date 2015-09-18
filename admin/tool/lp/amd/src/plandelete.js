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
 * Delete plans via ajax.
 *
 * @module     tool_lp/plandelete
 * @package    tool_lp
 * @copyright  2015 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/templates', 'core/ajax', 'core/notification', 'core/str'], function($, templates, ajax, notification, str) {
    // Private variables and functions.

    /** @var {Number} planid The id of the plan */
    var planid = 0;

    /** @var {Number} userid The id of the user */
    var userid = 0;

    /**
     * Callback to replace the dom element with the rendered template.
     *
     * @param {string} newhtml The new html to insert.
     * @param {string} newjs The new js to run.
     */
    var updatePage = function(newhtml, newjs) {
        $('[data-region="plans"]').replaceWith(newhtml);
        templates.runTemplateJS(newjs);
    };

    /**
     * Callback to render the page template again and update the page.
     *
     * @param {Object} context The context for the template.
     */
    var reloadList = function(context) {
        templates.render('tool_lp/plans_page', context)
            .done(updatePage)
            .fail(notification.exception);
    };

    /**
     * Delete a plan and reload the page.
     */
    var doDelete = function() {

        // We are chaining ajax requests here.
        var requests = ajax.call([{
            methodname: 'tool_lp_delete_plan',
            args: { id: planid }
        }, {
            methodname: 'tool_lp_data_for_plans_page',
            args: { userid: userid }
        }]);
        requests[1].done(reloadList).fail(notification.exception);
    };

    /**
     * Handler for "Delete plan" actions.
     * @param {Event} e
     */
    var confirmDelete = function(e) {
        e.preventDefault();

        planid = $(this).attr('data-planid');
        userid = $(this).attr('data-userid');

        var requests = ajax.call([{
            methodname: 'tool_lp_read_plan',
            args: { id: planid }
        }]);

        requests[0].done(function(plan) {
            str.get_strings([
                { key: 'confirm', component: 'moodle' },
                { key: 'deleteplan', component: 'tool_lp', param: plan.name },
                { key: 'delete', component: 'moodle' },
                { key: 'cancel', component: 'moodle' }
            ]).done(function (strings) {
                notification.confirm(
                    strings[0], // Confirm.
                    strings[1], // Delete plan X?
                    strings[2], // Delete.
                    strings[3], // Cancel.
                    doDelete
                );
            }).fail(notification.exception);
        }).fail(notification.exception);

    };


    return {
        // Public variables and functions.
        /**
         * Expose the event handler for delete.
         * @method deleteHandler
         * @param {Event} e
         */
        deleteHandler: confirmDelete,

    };
});
