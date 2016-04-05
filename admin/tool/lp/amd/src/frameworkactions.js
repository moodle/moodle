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
 * Competency frameworks actions via ajax.
 *
 * @module     tool_lp/frameworkactions
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/templates', 'core/ajax', 'core/notification', 'core/str'], function($, templates, ajax, notification, str) {
    // Private variables and functions.

    /** @var {Number} pagecontextid The id of the context */
    var pagecontextid = 0;

    /** @var {Number} frameworkid The id of the framework */
    var frameworkid = 0;

    /**
     * Callback to replace the dom element with the rendered template.
     *
     * @param {String} newhtml The new html to insert.
     * @param {String} newjs The new js to run.
     */
    var updatePage = function(newhtml, newjs) {
        $('[data-region="managecompetencies"]').replaceWith(newhtml);
        templates.runTemplateJS(newjs);
    };

    /**
     * Callback to render the page template again and update the page.
     *
     * @param {Object} context The context for the template.
     */
    var reloadList = function(context) {
        templates.render('tool_lp/manage_competency_frameworks_page', context)
            .done(updatePage)
            .fail(notification.exception);
    };

    /**
     * Duplicate a framework and reload the page.
     * @method doDuplicate
     * @param {Event} e
     */
    var doDuplicate = function(e) {
        e.preventDefault();

        frameworkid = $(this).attr('data-frameworkid');

        // We are chaining ajax requests here.
        var requests = ajax.call([{
            methodname: 'core_competency_duplicate_competency_framework',
            args: { id: frameworkid }
        }, {
            methodname: 'tool_lp_data_for_competency_frameworks_manage_page',
            args: {
                pagecontext: {
                    contextid: pagecontextid
                }
            }
        }]);
        requests[1].done(reloadList).fail(notification.exception);
    };
    /**
     * Delete a framework and reload the page.
     */
    var doDelete = function() {

        // We are chaining ajax requests here.
        var requests = ajax.call([{
            methodname: 'core_competency_delete_competency_framework',
            args: { id: frameworkid }
        }, {
            methodname: 'tool_lp_data_for_competency_frameworks_manage_page',
            args: {
                pagecontext: {
                    contextid: pagecontextid
                }
            }
        }]);
        requests[0].done(function (success) {
            if (success === false) {
                var req = ajax.call([{
                    methodname: 'core_competency_read_competency_framework',
                    args: { id: frameworkid }
                }]);
                req[0].done(function (framework) {
                    str.get_strings([
                        { key: 'frameworkcannotbedeleted', component: 'tool_lp', param: framework.shortname },
                        { key: 'cancel', component: 'moodle' }
                    ]).done(function (strings) {
                        notification.alert(
                            null,
                            strings[0]
                        );
                    }).fail(notification.exception);
                });
            }
        }).fail(notification.exception);
        requests[1].done(reloadList).fail(notification.exception);
    };

    /**
     * Handler for "Delete competency framework" actions.
     * @param {Event} e
     */
    var confirmDelete = function(e) {
        e.preventDefault();

        var id = $(this).attr('data-frameworkid');
        frameworkid = id;

        var requests = ajax.call([{
            methodname: 'core_competency_read_competency_framework',
            args: { id: frameworkid }
        }]);

        requests[0].done(function(framework) {
            str.get_strings([
                { key: 'confirm', component: 'moodle' },
                { key: 'deletecompetencyframework', component: 'tool_lp', param: framework.shortname },
                { key: 'delete', component: 'moodle' },
                { key: 'cancel', component: 'moodle' }
            ]).done(function (strings) {
                notification.confirm(
                    strings[0], // Confirm.
                    strings[1], // Delete competency framework X?
                    strings[2], // Delete.
                    strings[3], // Cancel.
                    doDelete
                );
            }).fail(notification.exception);
        }).fail(notification.exception);

    };


    return /** @alias module:tool_lp/frameworkactions */ {
        // Public variables and functions.

        /**
         * Expose the event handler for delete.
         * @method deleteHandler
         * @param {Event} e
         */
        deleteHandler: confirmDelete,

        /**
         * Expose the event handler for duplicate.
         * @method duplicateHandler
         * @param {Event} e
         */
        duplicateHandler: doDuplicate,

        /**
         * Initialise the module.
         * @method init
         * @param {Number} contextid The context id of the page.
         */
        init: function(contextid) {
            pagecontextid = contextid;
        }
    };
});
