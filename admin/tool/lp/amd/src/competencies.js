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
        'tool_lp/dialogue',
        'core/str',
        'tool_lp/tree',
        'tool_lp/dragdrop-reorder'],
       function($, notification, ajax, templates, Dialogue, str, Ariatree, dragdrop) {

    /**
     * Constructor
     *
     * @param {Number} itemid
     * @param {String} itemtype
     */
    var competencies = function(itemid, itemtype) {
        this.itemid = itemid;
        this.itemtype = itemtype;
        this.selectedCompetency = 0;
        var localthis = this;
        var loadframeworks = ajax.call([
            { methodname: 'tool_lp_list_competency_frameworks', args: { filters: {}, sort: 'sortorder' } }
        ]);

        loadframeworks[0].done(function(frameworks) {
            localthis.frameworks = frameworks;
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
     * @param {Node} drag The dragged node.
     * @param {Node} drop The dropped on node.
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
     * Get the search text from the input field and reload the tree based on the search.
     *
     * @method applyFilter
     * @param {Event} e The event that triggered the button.
     */
    competencies.prototype.applyFilter = function(e) {
        e.preventDefault();
        var localthis = this;
        var searchInput = $('[data-region="filtercompetencies"] input');
        var searchText = searchInput.val();
        var framework = $('[data-action="chooseframework"]');
        var frameworkid = framework.val();

        this.searchCompetencies().done(function (competencies) {
            var i = 0;

            var framework = localthis.frameworks[0];
            for (i = 0; i < localthis.frameworks.length; i++) {
                if (localthis.frameworks[i].id == frameworkid) {
                    framework = localthis.frameworks[i];
                    framework.selected = true;
                } else {
                    localthis.frameworks[i].selected = false;
                }
            }
            framework.selected = true;
            var context = {
                framework: framework,
                frameworks: localthis.frameworks,
                competencies: competencies,
                search: searchText
            };
            templates.render('tool_lp/link_course_competencies', context).done(function(html) {
                $('[data-region="competencylinktree"]').replaceWith(html);
                localthis.initLinkCourseCompetencies();
            }).fail(notification.exception);
        }).fail(notification.exception);
    };

    /**
     * The link course competencies popup was just opened and we need to initialise it.
     *
     * @method initLinkCourseCompetencies
     */
    competencies.prototype.initLinkCourseCompetencies = function() {
        var localthis = this;

        new Ariatree('[data-enhance=linktree]', function(target) {
            localthis.selectedCompetency = target.data('id');
        });

        $('[data-action="chooseframework"]').change(function(e) {
            return localthis.applyFilter.call(localthis, e);
        });

        $('[data-region="filtercompetencies"] button').click(function(e) {
            $(e.target).attr('disabled', 'disabled');
            return localthis.applyFilter.call(localthis, e);
        });

        $('[data-region="competencylinktree"] [data-action="cancel"]').click(function(e) {
            $(e.target).attr('disabled', 'disabled');
            e.preventDefault();
            localthis.popup.close();
        });
        $('[data-region="competencylinktree"] [data-action="add"]').click(function(e) {
            var requests = [],
                pagerender = '',
                pageregion = '';

            e.preventDefault();
            if (!localthis.selectedCompetency) {
                return;
            }

            $(e.target).attr('disabled', 'disabled');

            // Add the link and reload the page template.
            if (localthis.itemtype == 'course') {
                requests = ajax.call([
                    { methodname: 'tool_lp_add_competency_to_course',
                      args: { courseid: localthis.itemid, competencyid: localthis.selectedCompetency } },
                    { methodname: 'tool_lp_data_for_course_competencies_page',
                      args: { courseid: localthis.itemid } }
                ]);
                pagerender = 'tool_lp/course_competencies_page';
                pageregion = 'coursecompetenciespage';
            } else if (localthis.itemtype == 'template') {
                requests = ajax.call([
                    { methodname: 'tool_lp_add_competency_to_template',
                        args: { templateid: localthis.itemid, competencyid: localthis.selectedCompetency } },
                    { methodname: 'tool_lp_data_for_template_competencies_page',
                        args: { templateid: localthis.itemid } }
                ]);
                pagerender = 'tool_lp/template_competencies_page';
                pageregion = 'templatecompetenciespage';
            } else {
                return null;
            }

            requests[1].done(function(context) {
                templates.render(pagerender, context).done(function(html, js) {
                    localthis.popup.close();
                    $('[data-region="' + pageregion + '"]').replaceWith(html);
                    templates.runTemplateJS(js);
                }).fail(notification.exception);
            }).fail(notification.exception);
        });
    };

    /**
     * Register the javascript event handlers for this page.
     *
     * @method registerEvents
     */
    competencies.prototype.registerEvents = function() {
        var localthis = this;
        $('[data-region="actions"] button').click(function(e) {
            return localthis.openCompetencySelector.call(localthis, e);
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
                        args: { templateid: localthis.itemid } }
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

    /**
     * Turn the flat list of competencies into a tree.
     *
     * @method addCompetencyChildren
     * @param {Object} parent The current parent node
     * @param {Object[]} competencies The flat list of all nodes.
     */
    competencies.prototype.addCompetencyChildren = function(parent, competencies) {
        var i;

        for (i = 0; i < competencies.length; i++) {
            if (competencies[i].parentid == parent.id) {
                parent.haschildren = true;
                competencies[i].children = [];
                competencies[i].haschildren = false;
                parent.children[parent.children.length] = competencies[i];
                this.addCompetencyChildren(competencies[i], competencies);
            }
        }
    };

    /**
     * Get the search text from the input, and reload the tree.
     *
     * @method searchCompetencies
     * @return {promise} When resolved it will contain the tree of competencies.
     */
    competencies.prototype.searchCompetencies = function() {
        var localthis = this;
        var deferred = $.Deferred();
        var searchInput = $('[data-region="filtercompetencies"] input');
        var searchText = '';
        if (searchInput.length) {
            searchText = searchInput.val();
        }
        var framework = $('[data-action="chooseframework"]');
        var frameworkid = localthis.frameworks[0].id;
        if (framework.length) {
            frameworkid = framework.val();
        }

        var loadcompetencies = ajax.call([
            { methodname: 'tool_lp_search_competencies', args: { searchtext: searchText, competencyframeworkid: frameworkid } }
        ]);

        loadcompetencies[0].done(function (competencies) {
            // Expand the list of competencies into a tree.
            var i, competenciestree = [];
            for (i = 0; i < competencies.length; i++) {
                var onecompetency = competencies[i];
                if (onecompetency.parentid === "0") {
                    onecompetency.children = [];
                    onecompetency.haschildren = 0;
                    competenciestree[competenciestree.length] = onecompetency;
                    localthis.addCompetencyChildren(onecompetency, competencies);
                }
            }
            deferred.resolve(competenciestree);
        }).fail(function (ex) { deferred.reject(ex); });

        return deferred.promise();
    };

    /**
     * Open a popup to choose competencies.
     *
     * @method openCompetencySelector
     */
    competencies.prototype.openCompetencySelector = function(e) {
        e.preventDefault();
        var localthis = this;

        this.searchCompetencies().done(function (competencies) {
            var framework = localthis.frameworks[0];
            framework.selected = true;
            var context = { framework: framework, frameworks: localthis.frameworks, competencies: competencies, search: '' };
            templates.render('tool_lp/link_course_competencies', context).done(function(html) {
                str.get_string('linkcompetencies', 'tool_lp').done(function(title) {
                    localthis.popup = new Dialogue(
                        title,
                        html, // The link UI.
                        function() {
                            localthis.initLinkCourseCompetencies.call(localthis);
                        }
                    );
                }).fail(notification.exception);
            }).fail(notification.exception);
        }).fail(notification.exception);
    };

    return /** @alias module:tool_lp/coursecompetencies */ competencies;
});
