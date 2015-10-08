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
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery',
        'core/notification',
        'core/ajax',
        'core/templates',
        'core/str',
        'tool_lp/competencyselector',
        'tool_lp/dragdrop-reorder'],
       function($, notification, ajax, templates, str, competencyselector, dragdrop) {

    /**
     * Constructor
     *
     * @param {Number} itemid
     * @param {String} itemtype
     */
    var competencies = function(itemid, itemtype, pagectxid) {
        this.itemid = itemid;
        this.itemtype = itemtype;
        this.pageContextId = pagectxid;

        var localthis = this;
        var requests = null;
        var pagerender = null;
        var pageregion = null;

        if (itemtype === "course") {
            requests = [
                { methodname: 'tool_lp_add_competency_to_course',
                  args: { courseid: this.itemid } },
                { methodname: 'tool_lp_data_for_course_competencies_page',
                  args: { courseid: this.itemid } }
            ];
            pagerender = 'tool_lp/course_competencies_page';
            pageregion = 'coursecompetenciespage';

        } else if (itemtype === "template") {
            requests = [
                { methodname: 'tool_lp_add_competency_to_template',
                    args: { templateid: this.itemid } },
                { methodname: 'tool_lp_data_for_template_competencies_page',
                    args: { templateid: this.itemid } }
            ];
            pagerender = 'tool_lp/template_competencies_page';
            pageregion = 'templatecompetenciespage';
        }

        var promise = competencyselector.init(this.pageContextId);
        promise.done(function(frameworks) {
            if (frameworks.length === 0) {
                templates.render('tool_lp/no_frameworks_warning', {})
                    .done(function(html) {
                        $('[data-region="actions"]').append(html);
                        $('[data-region="actions"] button').hide();
                    }).fail(notification.exception);
                return;
            }
            $('[data-region="actions"] button').show();
            localthis.registerEvents();
            localthis.registerDragDrop();

            // And we finally attach the callbacks to execute once the user selected a competency to add.
            competencyselector.setAddCompetencyRequests(requests, pagerender, pageregion);
        }).fail(notification.exception);
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
                                  { identifier: 'movecompetency', component: 'tool_lp'},
                                  { identifier: 'movecompetencyafter', component: 'tool_lp'},
                                  'drag-samenode',
                                  'drag-parentnode',
                                  'drag-handlecontainer',
                                  function(drag, drop) {
                                      localthis.handleDrop.call(localthis, drag, drop);
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
                    methodname: 'tool_lp_reorder_course_competency',
                    args: { courseid: localthis.itemid, competencyidfrom: fromid, competencyidto: toid }
                }
            ]);
        } else if (localthis.itemtype == 'template') {
            requests = ajax.call([
                {
                    methodname: 'tool_lp_reorder_template_competency',
                    args: { templateid: localthis.itemid, competencyidfrom: fromid, competencyidto: toid }
                }
            ]);
        } else {
            return null;
        }

        requests[0].fail(notification.exception);
    };

    /**
     * Register the javascript event handlers for this page.
     *
     * @method registerEvents
     */
    competencies.prototype.registerEvents = function() {
        var localthis = this;
        $('[data-region="actions"] button').click(function(e) {
            return competencyselector.openCompetencySelector();
        });
        $('[data-action="delete-competency-link"]').click(function(e) {
            var requests = [],
                pagerender = '',
                pageregion = '';

            e.preventDefault();

            var deleteid = $(e.target).closest('[data-id]').data('id');

            // Delete the link and reload the page template.
            if (localthis.itemtype == 'course') {
                requests = ajax.call([
                    { methodname: 'tool_lp_remove_competency_from_course',
                      args: { courseid: localthis.itemid, competencyid: deleteid } },
                    { methodname: 'tool_lp_data_for_course_competencies_page',
                      args: { courseid: localthis.itemid } }
                ]);
                pagerender = 'tool_lp/course_competencies_page';
                pageregion = 'coursecompetenciespage';
            } else if (localthis.itemtype == 'template') {
                requests = ajax.call([
                    { methodname: 'tool_lp_remove_competency_from_template',
                        args: { templateid: localthis.itemid, competencyid: deleteid } },
                    { methodname: 'tool_lp_data_for_template_competencies_page',
                        args: { templateid: localthis.itemid, pagecontext: { contextid: localthis.pageContextId } } }
                ]);
                pagerender = 'tool_lp/template_competencies_page';
                pageregion = 'templatecompetenciespage';
            }

            requests[1].done(function(context) {
                templates.render(pagerender, context).done(function(html, js) {
                    $('[data-region="' + pageregion + '"]').replaceWith(html);
                    templates.runTemplateJS(js);
                }).fail(notification.exception);
            }).fail(notification.exception);
        });
    };

    return /** @alias module:tool_lp/competencies */ competencies;
});
