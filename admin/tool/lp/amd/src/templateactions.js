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
 * Handle actions on learning plan templates via ajax.
 *
 * @module     tool_lp/templateactions
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/templates', 'core/ajax', 'core/notification', 'core/str', 'tool_lp/actionselector'],
       function($, templates, ajax, notification, str, Actionselector) {
    // Private variables and functions.

    /** @var {Number} pagecontextid The id of the context */
    var pagecontextid = 0;

    /** @var {Number} templateid The id of the template */
    var templateid = 0;

    /** @var {Boolean} Action to apply to plans when deleting a template */
    var deleteplans = true;

    /**
     * Callback to replace the dom element with the rendered template.
     *
     * @method updatePage
     * @param {String} newhtml The new html to insert.
     * @param {String} newjs The new js to run.
     */
    var updatePage = function(newhtml, newjs) {
        $('[data-region="managetemplates"]').replaceWith(newhtml);
        templates.runTemplateJS(newjs);
    };

    /**
     * Callback to render the page template again and update the page.
     *
     * @method reloadList
     * @param {Object} context The context for the template.
     */
    var reloadList = function(context) {
        templates.render('tool_lp/manage_templates_page', context)
            .done(updatePage)
            .fail(notification.exception);
    };

    /**
     * Delete a template and reload the page.
     * @method doDelete
     */
    var doDelete = function() {

        // We are chaining ajax requests here.
        var requests = ajax.call([{
            methodname: 'core_competency_delete_template',
            args: {id: templateid,
                    deleteplans: deleteplans}
        }, {
            methodname: 'tool_lp_data_for_templates_manage_page',
            args: {
                pagecontext: {
                    contextid: pagecontextid
                }
            }
        }]);
        requests[1].done(reloadList).fail(notification.exception);
    };

    /**
     * Duplicate a template and reload the page.
     * @method doDuplicate
     * @param {Event} e
     */
    var doDuplicate = function(e) {
        e.preventDefault();

        templateid = $(this).attr('data-templateid');

        // We are chaining ajax requests here.
        var requests = ajax.call([{
            methodname: 'core_competency_duplicate_template',
            args: {id: templateid}
        }, {
            methodname: 'tool_lp_data_for_templates_manage_page',
            args: {
                pagecontext: {
                    contextid: pagecontextid
                }
            }
        }]);
        requests[1].done(reloadList).fail(notification.exception);
    };

    /**
     * Handler for "Delete learning plan template" actions.
     * @method confirmDelete
     * @param {Event} e
     */
    var confirmDelete = function(e) {
        e.preventDefault();

        var id = $(this).attr('data-templateid');
        templateid = id;
        deleteplans = true;

        var requests = ajax.call([{
            methodname: 'core_competency_read_template',
            args: {id: templateid}
        }, {
            methodname: 'core_competency_template_has_related_data',
            args: {id: templateid}
        }]);

        requests[0].done(function(template) {
            requests[1].done(function(templatehasrelateddata) {
                if (templatehasrelateddata) {
                    str.get_strings([
                        {key: 'deletetemplate', component: 'tool_lp', param: template.shortname},
                        {key: 'deletetemplatewithplans', component: 'tool_lp'},
                        {key: 'deleteplans', component: 'tool_lp'},
                        {key: 'unlinkplanstemplate', component: 'tool_lp'},
                        {key: 'confirm', component: 'moodle'},
                        {key: 'cancel', component: 'moodle'}
                    ]).done(function(strings) {
                        var actions = [{'text': strings[2], 'value': 'delete'},
                                       {'text': strings[3], 'value': 'unlink'}];
                        var actionselector = new Actionselector(
                                strings[0], // Title.
                                strings[1], // Message
                                actions, // Radio button options.
                                strings[4], // Confirm.
                                strings[5]); // Cancel.
                        actionselector.display();
                        actionselector.on('save', function(e, data) {
                            if (data.action != 'delete') {
                                deleteplans = false;
                            }
                            doDelete();
                        });
                    }).fail(notification.exception);
                } else {
                    str.get_strings([
                        {key: 'confirm', component: 'moodle'},
                        {key: 'deletetemplate', component: 'tool_lp', param: template.shortname},
                        {key: 'delete', component: 'moodle'},
                        {key: 'cancel', component: 'moodle'}
                    ]).done(function(strings) {
                        notification.confirm(
                        strings[0], // Confirm.
                        strings[1], // Delete learning plan template X?
                        strings[2], // Delete.
                        strings[3], // Cancel.
                        doDelete
                        );
                    }).fail(notification.exception);
                }
            }).fail(notification.exception);
        }).fail(notification.exception);

    };

    return /** @alias module:tool_lp/templateactions */ {
        // Public variables and functions.
        /**
         * Expose the event handler for the delete.
         * @method deleteHandler
         * @param {Event} e
         */
        deleteHandler: confirmDelete,

        /**
         * Expose the event handler for the duplicate.
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
