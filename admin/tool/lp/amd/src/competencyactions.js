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
 * Handle selection changes and actions on the competency tree.
 *
 * @module     tool_lp/competencyselect
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/url', 'core/templates', 'core/notification', 'core/str', 'core/ajax', 'core/dragdrop-reorder', 'core/tree', 'core/dialogue', 'core/menu'],
       function($, url, templates, notification, str, ajax, dragdrop, ariatree, dialogue, menu) {

    // Private variables and functions.
    var treeModel = null;

    var moveSource = null;
    var moveTarget = null;

    var addHandler = function(e) {
        e.preventDefault();
        var parent = $('[data-region="competencyactions"]').data('competency');

        var params = {
            competencyframeworkid : treeModel.getCompetencyFrameworkId()
        };

        if (parent == null) {
            // We are adding at the root node.
        } else {
            // We are adding at a sub node.
            params['parentid'] = parent.id;
        }
        var queryparams = $.param(params);
        var actionurl = url.relativeUrl('/admin/tool/lp/editcompetency.php?' + queryparams);
        window.location = actionurl;
    };

    var doMove = function() {
        if (typeof (moveTarget) === "undefined") {
            // This is a top level node.
            moveTarget = 0;
        }

        if (moveTarget == moveSource) {
            return;
        }
        var frameworkid = $('[data-region="filtercompetencies"]').data('frameworkid');
        var requests = ajax.call([{
            methodname: 'tool_lp_set_parent_competency',
            args: { competencyid: moveSource, parentid: moveTarget }
        }, {
            methodname: 'tool_lp_data_for_competencies_manage_page',
            args: { competencyframeworkid: frameworkid,
                    search: $('[data-region="filtercompetencies"] input').val() }
        }]);
        requests[1].done(reloadPage).fail(notification.exception);
    };

    var initMovePopup = function(popup) {
        var movetree = new ariatree('[data-enhance=movetree]', function(target) {
            moveTarget = $(target).data('id');
        });

        var body = $(popup.getContent());
        body.on('click', '[data-action="move"]', function(e) { popup.close(); doMove() });
        body.on('click', '[data-action="cancel"]', function(e) { popup.close(); });
    };

    var addCompetencyChildren = function(parent, competencies) {
        var i;

        for (i = 0; i < competencies.length; i++) {
            if (competencies[i].parentid == parent.id) {
                parent.haschildren = true;
                competencies[i].children = [];
                competencies[i].haschildren = false;
                parent.children[parent.children.length] = competencies[i];
                addCompetencyChildren(competencies[i], competencies);
            }
        }
    };

    var moveHandler = function(e) {
        e.preventDefault();
        var competency = $('[data-region="competencyactions"]').data('competency');

        // Remember what we are moving.
        moveSource = competency.id;

        // Load data for the template.
        var requests = ajax.call([
            {
                methodname: 'tool_lp_search_competencies',
                args: {
                    competencyframeworkid: competency.competencyframeworkid,
                    searchtext: ''
                }
            },{
                methodname: 'tool_lp_read_competency_framework',
                args: {
                    id: competency.competencyframeworkid
                }
            }
        ]);

        // When all data has arrived, continue.
        $.when.apply(null, requests).done(function(competencies, framework) {

            // Expand the list of competencies into a tree.
            var i, competenciestree = [];
            for (i = 0; i < competencies.length; i++) {
                var onecompetency = competencies[i];
                if (onecompetency.parentid == 0) {
                    onecompetency.children = [];
                    onecompetency.haschildren = 0;
                    competenciestree[competenciestree.length] = onecompetency;
                    addCompetencyChildren(onecompetency, competencies);
                }
            }

            var strings = str.get_strings([
                { key: 'movecompetency', component: 'tool_lp', param: competency.shortname },
                { key: 'move', component: 'tool_lp' },
                { key: 'cancel', component: 'tool_lp' }
            ]).done(function (strings) {

                var context = {
                    framework: framework,
                    competencies: competenciestree
                };

                templates.render('tool_lp/competencies_move_tree', context)
                   .done(function(tree) {
                       var popup = new dialogue(
                           strings[0], // Move competency x.
                           tree, // The move tree.
                           initMovePopup
                       );

                   }).fail(notification.exception);

           }).fail(notification.exception);

        }).fail(notification.exception);

    };

    var editHandler = function(e) {
        e.preventDefault();
        var competency = $('[data-region="competencyactions"]').data('competency');

        var params = {
            competencyframeworkid : treeModel.getCompetencyFrameworkId(),
            id : competency.id,
            parentid: competency.parentid
        };

        var queryparams = $.param(params);
        var actionurl = url.relativeUrl('/admin/tool/lp/editcompetency.php?' + queryparams);
        window.location = actionurl;
    };

    var reloadPage = function(context) {
        templates.render('tool_lp/manage_competencies_page', context)
            .done(function(newhtml, newjs) {
                $('[data-region="managecompetencies"]').replaceWith(newhtml);
                templates.runTemplateJS(newjs);
            })
           .fail(notification.exception);
    };

    var updateSearchHandler = function(e) {
        e.preventDefault();

        var frameworkid = $('[data-region="filtercompetencies"]').data('frameworkid');

        var requests = ajax.call([{
            methodname: 'tool_lp_data_for_competencies_manage_page',
            args: { competencyframeworkid: frameworkid,
                    search: $('[data-region="filtercompetencies"] input').val() }
        }]);
        requests[0].done(reloadPage).fail(notification.exception);
    };

    var moveUpHandler = function() {
        // We are chaining ajax requests here.
        var competency = $('[data-region="competencyactions"]').data('competency');
        var requests = ajax.call([{
            methodname: 'tool_lp_move_up_competency',
            args: { id: competency.id }
        }, {
            methodname: 'tool_lp_data_for_competencies_manage_page',
            args: { competencyframeworkid: competency.competencyframeworkid,
                    search: $('[data-region="filtercompetencies"] input').val() }
        }]);
        requests[1].done(reloadPage).fail(notification.exception);
    };

    var moveDownHandler = function() {
        // We are chaining ajax requests here.
        var competency = $('[data-region="competencyactions"]').data('competency');
        var requests = ajax.call([{
            methodname: 'tool_lp_move_down_competency',
            args: { id: competency.id }
        }, {
            methodname: 'tool_lp_data_for_competencies_manage_page',
            args: { competencyframeworkid: competency.competencyframeworkid,
                    search: $('[data-region="filtercompetencies"] input').val() }
        }]);
        requests[1].done(reloadPage).fail(notification.exception);
    };

    var seeCoursesHandler = function(e) {
        e.preventDefault();
        var competency = $('[data-region="competencyactions"]').data('competency');
        var localthis = this;

        var requests = ajax.call([{
            methodname: 'tool_lp_list_courses_using_competency',
            args: { id: competency.id }
        }]);

        requests[0].done(function(courses) {
            var context = {
                courseviewurl: url.relativeUrl('/course/view.php'),
                courses: courses
            };
            templates.render('tool_lp/linked_courses_summary', context).done(function(html) {
                str.get_string('linkedcourses', 'tool_lp').done(function (linkedcourses) {
                    var popup = new dialogue(
                        linkedcourses, // Title.
                        html, // The linked courses.
                        initMovePopup
                    );
                }).fail(notification.exception);
            }).fail(notification.exception);
        }).fail(notification.exception);
    }

    var doDelete = function() {
        // We are chaining ajax requests here.
        var competency = $('[data-region="competencyactions"]').data('competency');
        var requests = ajax.call([{
            methodname: 'tool_lp_delete_competency',
            args: { id: competency.id }
        }, {
            methodname: 'tool_lp_data_for_competencies_manage_page',
            args: { competencyframeworkid: competency.competencyframeworkid,
                    search: $('[data-region="filtercompetencies"] input').val() }
        }]);
        requests[1].done(reloadPage).fail(notification.exception);
    };

    var deleteHandler = function(e) {
        e.preventDefault();
        var competency = $('[data-region="competencyactions"]').data('competency');

        templates.render('tool_lp/competency_summary', competency)
           .done(function(html) {

               var strings = str.get_strings([
                   { key: 'confirm', component: 'tool_lp' },
                   { key: 'deletecompetency', component: 'tool_lp', param: html },
                   { key: 'delete', component: 'tool_lp' },
                   { key: 'cancel', component: 'tool_lp' }
               ]).done(function (strings) {
                    notification.confirm(
                       strings[0], // Confirm.
                       strings[1], // Delete competency X?
                       strings[2], // Delete.
                       strings[3], // Cancel.
                       doDelete
                    );
               }).fail(notification.exception);
           }).fail(notification.exception);

    };

    var dragStart = function(e) {
        e.originalEvent.dataTransfer.setData('text', $(e.target).data('id'));
    };

    var allowDrop = function(e) {
        e.originalEvent.dataTransfer.dropEffect = 'move';
        e.preventDefault();
    };

    var dragEnter = function(e) {
        e.preventDefault();
        $(this).addClass('currentdragtarget');
    }

    var dragLeave = function(e) {
        e.preventDefault();
        $(this).removeClass('currentdragtarget');
    }

    var dropOver = function(e) {
        e.preventDefault();
        moveSource = e.originalEvent.dataTransfer.getData('text');
        moveTarget = $(e.target).data('id');
        $(this).removeClass('currentdragtarget');

        doMove();
    };

    return {
        init: function(model) {
            treeModel = model;
            str.get_string('edit', 'core').done(function (edit) {
                menu.menu(edit, '.competencyactionsmenu');

                $('[data-region="competencyactions"]').on('click', '[data-action="add"]', addHandler);
                $('[data-region="competencyactions"]').on('click', '[data-action="edit"]', editHandler);
                $('[data-region="competencyactions"]').on('click', '[data-action="delete"]', deleteHandler);
                $('[data-region="competencyactions"]').on('click', '[data-action="move"]', moveHandler);
                $('[data-region="competencyactions"]').on('click', '[data-action="moveup"]', moveUpHandler);
                $('[data-region="competencyactions"]').on('click', '[data-action="movedown"]', moveDownHandler);
                $('[data-region="competencyactions"]').on('click', '[data-action="linkedcourses"]', seeCoursesHandler);

            }).fail(notification.exception);
            $('[data-region="filtercompetencies"]').on('submit', updateSearchHandler);
            // Simple html5 drag drop because we already added an accessible alternative.
            $('[data-region="managecompetencies"] li').on('dragstart', dragStart);
            $('[data-region="managecompetencies"] li').on('dragover', allowDrop);
            $('[data-region="managecompetencies"] li').on('dragenter', dragEnter);
            $('[data-region="managecompetencies"] li').on('dragleave', dragLeave);
            $('[data-region="managecompetencies"] li').on('drop', dropOver);
        },
        // Public variables and functions.
        selectionChanged: function(node) {
            var id = $(node).data('id');
            if (typeof id === "undefined") {
                // Assume this is the root of the tree.
                // Here we are only getting the text from the top of the tree, to do it we clone the tree,
                // remove all children and then call text on the result.
                $('[data-region="competencyinfo"]').html(node.clone().children().remove().end().text());
                $('[data-region="competencyactions"]').data('competency', null);
                $('[data-region="competencyactionsmenu"]').hide();
                $('[data-region="competencyactions"] [data-action="add"]').removeAttr("disabled");
            } else {
                var competency = treeModel.getCompetency(id);

                templates.render('tool_lp/competency_summary', competency)
                   .done(function(html) {
                        $('[data-region="competencyinfo"]').html(html);
                   }).fail(notification.exception);

                $('[data-region="competencyactions"]').data('competency', competency);
                $('[data-region="competencyactions"] [data-action="add"]').removeAttr("disabled");
                $('[data-region="competencyactionsmenu"]').css('display', 'inline-block');

            }
        }
    };
});
