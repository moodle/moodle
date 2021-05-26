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
 * Handle add/remove competency links.
 *
 * @module     tool_lp/competencies
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery',
        'core/notification',
        'core/ajax',
        'core/templates',
        'core/str',
        'tool_lp/competencypicker',
        'tool_lp/dragdrop-reorder',
        'core/pending'],
       function($, notification, ajax, templates, str, Picker, dragdrop, Pending) {

    /**
     * Constructor
     *
     * @class tool_lp/competencies
     * @param {Number} itemid
     * @param {String} itemtype
     * @param {Number} pagectxid
     */
    var competencies = function(itemid, itemtype, pagectxid) {
        this.itemid = itemid;
        this.itemtype = itemtype;
        this.pageContextId = pagectxid;
        this.pickerInstance = null;

        $('[data-region="actions"] button').prop('disabled', false);
        this.registerEvents();
        this.registerDragDrop();
    };

    /**
     * Initialise the drag/drop code.
     * @method registerDragDrop
     */
    competencies.prototype.registerDragDrop = function() {
        var localthis = this;
        // Init this module.
        str.get_string('movecompetency', 'tool_lp').done(
            function(movestring) {
                dragdrop.dragdrop('movecompetency',
                                  movestring,
                                  {identifier: 'movecompetency', component: 'tool_lp'},
                                  {identifier: 'movecompetencyafter', component: 'tool_lp'},
                                  'drag-samenode',
                                  'drag-parentnode',
                                  'drag-handlecontainer',
                                  function(drag, drop) {
                                      localthis.handleDrop(drag, drop);
                                  });
            }
        ).fail(notification.exception);

    };

    /**
     * Handle a drop from a drag/drop operation.
     *
     * @method handleDrop
     * @param {DOMNode} drag The dragged node.
     * @param {DOMNode} drop The dropped on node.
     */
    competencies.prototype.handleDrop = function(drag, drop) {
        var fromid = $(drag).data('id');
        var toid = $(drop).data('id');
        var localthis = this;
        var requests = [];

        if (localthis.itemtype == 'course') {
            requests = ajax.call([
                {
                    methodname: 'core_competency_reorder_course_competency',
                    args: {courseid: localthis.itemid, competencyidfrom: fromid, competencyidto: toid}
                }
            ]);
        } else if (localthis.itemtype == 'template') {
            requests = ajax.call([
                {
                    methodname: 'core_competency_reorder_template_competency',
                    args: {templateid: localthis.itemid, competencyidfrom: fromid, competencyidto: toid}
                }
            ]);
        } else if (localthis.itemtype == 'plan') {
            requests = ajax.call([
                {
                    methodname: 'core_competency_reorder_plan_competency',
                    args: {planid: localthis.itemid, competencyidfrom: fromid, competencyidto: toid}
                }
            ]);
        } else {
            return;
        }

        requests[0].fail(notification.exception);
    };

    /**
     * Pick a competency
     *
     * @method pickCompetency
     * @return {Promise}
     */
    competencies.prototype.pickCompetency = function() {
        var self = this;
        var requests;
        var pagerender;
        var pageregion;
        var pageContextIncludes;

        if (!self.pickerInstance) {
            if (self.itemtype === 'template' || self.itemtype === 'course') {
                pageContextIncludes = 'parents';
            }
            self.pickerInstance = new Picker(self.pageContextId, false, pageContextIncludes);
            self.pickerInstance.on('save', function(e, data) {
                var compIds = data.competencyIds;
                var pendingPromise = new Pending();

                if (self.itemtype === "course") {
                    requests = [];

                    $.each(compIds, function(index, compId) {
                        requests.push({
                            methodname: 'core_competency_add_competency_to_course',
                            args: {courseid: self.itemid, competencyid: compId}
                        });
                    });
                    requests.push({
                        methodname: 'tool_lp_data_for_course_competencies_page',
                        args: {courseid: self.itemid, moduleid: 0}
                    });

                    pagerender = 'tool_lp/course_competencies_page';
                    pageregion = 'coursecompetenciespage';

                } else if (self.itemtype === "template") {
                    requests = [];

                    $.each(compIds, function(index, compId) {
                        requests.push({
                            methodname: 'core_competency_add_competency_to_template',
                            args: {templateid: self.itemid, competencyid: compId}
                        });
                    });
                    requests.push({
                        methodname: 'tool_lp_data_for_template_competencies_page',
                        args: {templateid: self.itemid, pagecontext: {contextid: self.pageContextId}}
                    });
                    pagerender = 'tool_lp/template_competencies_page';
                    pageregion = 'templatecompetenciespage';
                } else if (self.itemtype === "plan") {
                    requests = [];

                    $.each(compIds, function(index, compId) {
                        requests.push({
                            methodname: 'core_competency_add_competency_to_plan',
                            args: {planid: self.itemid, competencyid: compId}
                        });
                    });
                    requests.push({
                         methodname: 'tool_lp_data_for_plan_page',
                         args: {planid: self.itemid}
                    });
                    pagerender = 'tool_lp/plan_page';
                    pageregion = 'plan-page';
                }
                ajax.call(requests)[requests.length - 1]
                .then(function(context) {
                    return templates.render(pagerender, context);
                })
                .then(function(html, js) {
                    templates.replaceNode($('[data-region="' + pageregion + '"]'), html, js);
                    return;
                })
                .then(pendingPromise.resolve)
                .catch(notification.exception);
            });
        }

        return self.pickerInstance.display();
    };

    /**
     * Delete the link between competency and course, template or plan. Reload the page.
     *
     * @method doDelete
     * @param {int} deleteid The id of record to delete.
     */
    competencies.prototype.doDelete = function(deleteid) {
        var localthis = this;
        var requests = [],
            pagerender = '',
            pageregion = '';

        // Delete the link and reload the page template.
        if (localthis.itemtype == 'course') {
            requests = ajax.call([
                {methodname: 'core_competency_remove_competency_from_course',
                    args: {courseid: localthis.itemid, competencyid: deleteid}},
                {methodname: 'tool_lp_data_for_course_competencies_page',
                    args: {courseid: localthis.itemid, moduleid: 0}}
            ]);
            pagerender = 'tool_lp/course_competencies_page';
            pageregion = 'coursecompetenciespage';
        } else if (localthis.itemtype == 'template') {
            requests = ajax.call([
                {methodname: 'core_competency_remove_competency_from_template',
                    args: {templateid: localthis.itemid, competencyid: deleteid}},
                {methodname: 'tool_lp_data_for_template_competencies_page',
                    args: {templateid: localthis.itemid, pagecontext: {contextid: localthis.pageContextId}}}
            ]);
            pagerender = 'tool_lp/template_competencies_page';
            pageregion = 'templatecompetenciespage';
        } else if (localthis.itemtype == 'plan') {
            requests = ajax.call([
                {methodname: 'core_competency_remove_competency_from_plan',
                    args: {planid: localthis.itemid, competencyid: deleteid}},
                {methodname: 'tool_lp_data_for_plan_page',
                    args: {planid: localthis.itemid}}
            ]);
            pagerender = 'tool_lp/plan_page';
            pageregion = 'plan-page';
        }

        requests[1].done(function(context) {
            templates.render(pagerender, context).done(function(html, js) {
                $('[data-region="' + pageregion + '"]').replaceWith(html);
                templates.runTemplateJS(js);
            }).fail(notification.exception);
        }).fail(notification.exception);

    };

    /**
     * Show a confirm dialogue before deleting a competency.
     *
     * @method deleteHandler
     * @param {int} deleteid The id of record to delete.
     */
    competencies.prototype.deleteHandler = function(deleteid) {
        var localthis = this;
        var requests = [];
        var message;

        if (localthis.itemtype == 'course') {
            message = 'unlinkcompetencycourse';
        } else if (localthis.itemtype == 'template') {
            message = 'unlinkcompetencytemplate';
        } else if (localthis.itemtype == 'plan') {
            message = 'unlinkcompetencyplan';
        } else {
            return;
        }

        requests = ajax.call([{
            methodname: 'core_competency_read_competency',
            args: {id: deleteid}
        }]);

        requests[0].done(function(competency) {
            str.get_strings([
                {key: 'confirm', component: 'moodle'},
                {key: message, component: 'tool_lp', param: competency.shortname},
                {key: 'confirm', component: 'moodle'},
                {key: 'cancel', component: 'moodle'}
            ]).done(function(strings) {
                notification.confirm(
                    strings[0], // Confirm.
                    strings[1], // Unlink the competency X from the course?
                    strings[2], // Confirm.
                    strings[3], // Cancel.
                    function() {
                        localthis.doDelete(deleteid);
                    }
                );
            }).fail(notification.exception);
        }).fail(notification.exception);
    };

    /**
     * Register the javascript event handlers for this page.
     *
     * @method registerEvents
     */
    competencies.prototype.registerEvents = function() {
        var localthis = this;

        if (localthis.itemtype == 'course') {
            // Course completion rule handling.
            $('[data-region="coursecompetenciespage"]').on('change', 'select[data-field="ruleoutcome"]', function(e) {
                var pendingPromise = new Pending();
                var requests = [];
                var pagerender = 'tool_lp/course_competencies_page';
                var pageregion = 'coursecompetenciespage';
                var coursecompetencyid = $(e.target).data('id');
                var ruleoutcome = $(e.target).val();
                requests = ajax.call([
                    {methodname: 'core_competency_set_course_competency_ruleoutcome',
                      args: {coursecompetencyid: coursecompetencyid, ruleoutcome: ruleoutcome}},
                    {methodname: 'tool_lp_data_for_course_competencies_page',
                      args: {courseid: localthis.itemid, moduleid: 0}}
                ]);

                requests[1].then(function(context) {
                    return templates.render(pagerender, context);
                })
                .then(function(html, js) {
                    return templates.replaceNode($('[data-region="' + pageregion + '"]'), html, js);
                })
                .then(pendingPromise.resolve)
                .catch(notification.exception);
            });
        }

        $('[data-region="actions"] button').click(function(e) {
            var pendingPromise = new Pending();
            e.preventDefault();

            localthis.pickCompetency()
                .then(pendingPromise.resolve)
                .catch();
        });
        $('[data-action="delete-competency-link"]').click(function(e) {
            e.preventDefault();

            var deleteid = $(e.target).closest('[data-id]').data('id');
            localthis.deleteHandler(deleteid);
        });
    };

    return /** @alias module:tool_lp/competencies */ competencies;
});
