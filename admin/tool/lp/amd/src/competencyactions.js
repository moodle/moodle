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
 * @module     tool_lp/competencyactions
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery',
        'core/url',
        'core/templates',
        'core/notification',
        'core/str',
        'core/ajax',
        'tool_lp/dragdrop-reorder',
        'tool_lp/tree',
        'tool_lp/dialogue',
        'tool_lp/menubar',
        'tool_lp/competencypicker',
        'tool_lp/competency_outcomes',
        'tool_lp/competencyruleconfig'],
       function($, url, templates, notification, str, ajax, dragdrop, Ariatree, Dialogue, menubar, Picker, Outcomes, RuleConfig) {

    // Private variables and functions.
    /** @var {Object} treeModel - This is an object representing the nodes in the tree. */
    var treeModel = null;
    /** @var {Node} moveSource - The start of a drag operation */
    var moveSource = null;
    /** @var {Node} moveTarget - The end of a drag operation */
    var moveTarget = null;
    /** @var {Number} pageContextId The page context ID. */
    var pageContextId;
    /** @type {Object} Picker instance. */
    var pickerInstance;
    /** @type {Object} Rule config instance. */
    var ruleConfigInstance;
    /** @type {Object} The competency we're picking a relation to. */
    var relatedTarget;
    /** @type {Object} Taxonomy constants indexed per level. */
    var taxonomiesConstants;
    /** @type {Array} The rules modules. Values are object containing type, namd and amd. */
    var rulesModules;
    /** @type {Number} the selected competency ID. */
    var selectedCompetencyId = null;

    /**
     * Respond to choosing the "Add" menu item for the selected node in the tree.
     * @method addHandler
     */
    var addHandler = function() {
        var parent = $('[data-region="competencyactions"]').data('competency');

        var params = {
            competencyframeworkid: treeModel.getCompetencyFrameworkId(),
            pagecontextid: pageContextId
        };

        if (parent !== null) {
            // We are adding at a sub node.
            params.parentid = parent.id;
        }

        var relocate = function() {
            var queryparams = $.param(params);
            window.location = url.relativeUrl('/admin/tool/lp/editcompetency.php?' + queryparams);
        };

        if (parent !== null && treeModel.hasRule(parent.id)) {
            str.get_strings([
                {key: 'confirm', component: 'moodle'},
                {key: 'addingcompetencywillresetparentrule', component: 'tool_lp', param: parent.shortname},
                {key: 'yes', component: 'core'},
                {key: 'no', component: 'core'}
            ]).done(function(strings) {
                notification.confirm(
                    strings[0],
                    strings[1],
                    strings[2],
                    strings[3],
                    relocate
                );
            }).fail(notification.exception);
        } else {
            relocate();
        }
    };

    /**
     * A source and destination has been chosen - so time to complete a move.
     * @method doMove
     */
    var doMove = function() {
        var frameworkid = $('[data-region="filtercompetencies"]').data('frameworkid');
        var requests = ajax.call([{
            methodname: 'core_competency_set_parent_competency',
            args: {competencyid: moveSource, parentid: moveTarget}
        }, {
            methodname: 'tool_lp_data_for_competencies_manage_page',
            args: {competencyframeworkid: frameworkid,
                    search: $('[data-region="filtercompetencies"] input').val()}
        }]);
        requests[1].done(reloadPage).fail(notification.exception);
    };

    /**
     * Confirms a competency move.
     *
     * @method confirmMove
     */
    var confirmMove = function() {
        moveTarget = typeof moveTarget === "undefined" ? 0 : moveTarget;
        if (moveTarget == moveSource) {
            // No move to do.
            return;
        }

        var targetComp = treeModel.getCompetency(moveTarget) || {},
            sourceComp = treeModel.getCompetency(moveSource) || {},
            confirmMessage = 'movecompetencywillresetrules',
            showConfirm = false;

        // We shouldn't be moving the competency to the same parent.
        if (sourceComp.parentid == moveTarget) {
            return;
        }

        // If we are moving to a child of self.
        if (targetComp.path && targetComp.path.indexOf('/' + sourceComp.id + '/') >= 0) {
            confirmMessage = 'movecompetencytochildofselfwillresetrules';

            // Show a confirmation if self has rules, as they'll disappear.
            showConfirm = showConfirm || treeModel.hasRule(sourceComp.id);
        }

        // Show a confirmation if the current parent, or the destination have rules.
        showConfirm = showConfirm || (treeModel.hasRule(targetComp.id) || treeModel.hasRule(sourceComp.parentid));

        // Show confirm, and/or do the things.
        if (showConfirm) {
            str.get_strings([
                {key: 'confirm', component: 'moodle'},
                {key: confirmMessage, component: 'tool_lp'},
                {key: 'yes', component: 'moodle'},
                {key: 'no', component: 'moodle'}
            ]).done(function(strings) {
                notification.confirm(
                    strings[0], // Confirm.
                    strings[1], // Delete competency X?
                    strings[2], // Delete.
                    strings[3], // Cancel.
                    doMove
                );
            }).fail(notification.exception);

        } else {
            doMove();
        }
    };

    /**
     * A move competency popup was opened - initialise the aria tree in it.
     * @method initMovePopup
     * @param {dialogue} popup The tool_lp/dialogue that was created.
     */
    var initMovePopup = function(popup) {
        var body = $(popup.getContent());
        var treeRoot = body.find('[data-enhance=movetree]');
        var tree = new Ariatree(treeRoot, false);
        tree.on('selectionchanged', function(evt, params) {
            var target = params.selected;
            moveTarget = $(target).data('id');
        });
        treeRoot.show();

        body.on('click', '[data-action="move"]', function() {
          popup.close();
          confirmMove();
        });
        body.on('click', '[data-action="cancel"]', function() {
          popup.close();
        });
    };

    /**
     * Turn a flat list of competencies into a tree structure (recursive).
     * @method addCompetencyChildren
     * @param {Object} parent The current parent node in the tree
     * @param {Object[]} competencies The flat list of competencies
     */
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

    /**
     * A node was chosen and "Move" was selected from the menu. Open a popup to select the target.
     * @param {Event} e
     * @method moveHandler
     */
    var moveHandler = function(e) {
        e.preventDefault();
        var competency = $('[data-region="competencyactions"]').data('competency');

        // Remember what we are moving.
        moveSource = competency.id;

        // Load data for the template.
        var requests = ajax.call([
            {
                methodname: 'core_competency_search_competencies',
                args: {
                    competencyframeworkid: competency.competencyframeworkid,
                    searchtext: ''
                }
            }, {
                methodname: 'core_competency_read_competency_framework',
                args: {
                    id: competency.competencyframeworkid
                }
            }
        ]);

        // When all data has arrived, continue.
        $.when.apply(null, requests).done(function(competencies, framework) {

            // Expand the list of competencies into a tree.
            var i;
            var competenciestree = [];
            for (i = 0; i < competencies.length; i++) {
                var onecompetency = competencies[i];
                if (onecompetency.parentid == "0") {
                    onecompetency.children = [];
                    onecompetency.haschildren = 0;
                    competenciestree[competenciestree.length] = onecompetency;
                    addCompetencyChildren(onecompetency, competencies);
                }
            }

            str.get_strings([
                {key: 'movecompetency', component: 'tool_lp', param: competency.shortname},
                {key: 'move', component: 'tool_lp'},
                {key: 'cancel', component: 'moodle'}
            ]).done(function(strings) {

                var context = {
                    framework: framework,
                    competencies: competenciestree
                };

                templates.render('tool_lp/competencies_move_tree', context)
                   .done(function(tree) {
                       new Dialogue(
                           strings[0], // Move competency x.
                           tree, // The move tree.
                           initMovePopup
                       );

                   }).fail(notification.exception);

           }).fail(notification.exception);

        }).fail(notification.exception);

    };

    /**
     * Edit the selected competency.
     * @method editHandler
     */
    var editHandler = function() {
        var competency = $('[data-region="competencyactions"]').data('competency');

        var params = {
            competencyframeworkid: treeModel.getCompetencyFrameworkId(),
            id: competency.id,
            parentid: competency.parentid,
            pagecontextid: pageContextId
        };

        var queryparams = $.param(params);
        window.location = url.relativeUrl('/admin/tool/lp/editcompetency.php?' + queryparams);
    };

    /**
     * Re-render the page with the latest data.
     * @param {Object} context
     * @method reloadPage
     */
    var reloadPage = function(context) {
        templates.render('tool_lp/manage_competencies_page', context)
            .done(function(newhtml, newjs) {
                $('[data-region="managecompetencies"]').replaceWith(newhtml);
                templates.runTemplateJS(newjs);
            })
           .fail(notification.exception);
    };

    /**
     * Perform a search and render the page with the new search results.
     * @param {Event} e
     * @method updateSearchHandler
     */
    var updateSearchHandler = function(e) {
        e.preventDefault();

        var frameworkid = $('[data-region="filtercompetencies"]').data('frameworkid');

        var requests = ajax.call([{
            methodname: 'tool_lp_data_for_competencies_manage_page',
            args: {competencyframeworkid: frameworkid,
                    search: $('[data-region="filtercompetencies"] input').val()}
        }]);
        requests[0].done(reloadPage).fail(notification.exception);
    };

    /**
     * Move a competency "up". This only affects the sort order within the same branch of the tree.
     * @method moveUpHandler
     */
    var moveUpHandler = function() {
        // We are chaining ajax requests here.
        var competency = $('[data-region="competencyactions"]').data('competency');
        var requests = ajax.call([{
            methodname: 'core_competency_move_up_competency',
            args: {id: competency.id}
        }, {
            methodname: 'tool_lp_data_for_competencies_manage_page',
            args: {competencyframeworkid: competency.competencyframeworkid,
                    search: $('[data-region="filtercompetencies"] input').val()}
        }]);
        requests[1].done(reloadPage).fail(notification.exception);
    };

    /**
     * Move a competency "down". This only affects the sort order within the same branch of the tree.
     * @method moveDownHandler
     */
    var moveDownHandler = function() {
        // We are chaining ajax requests here.
        var competency = $('[data-region="competencyactions"]').data('competency');
        var requests = ajax.call([{
            methodname: 'core_competency_move_down_competency',
            args: {id: competency.id}
        }, {
            methodname: 'tool_lp_data_for_competencies_manage_page',
            args: {competencyframeworkid: competency.competencyframeworkid,
                    search: $('[data-region="filtercompetencies"] input').val()}
        }]);
        requests[1].done(reloadPage).fail(notification.exception);
    };

    /**
     * Open a dialogue to show all the courses using the selected competency.
     * @method seeCoursesHandler
     */
    var seeCoursesHandler = function() {
        var competency = $('[data-region="competencyactions"]').data('competency');

        var requests = ajax.call([{
            methodname: 'tool_lp_list_courses_using_competency',
            args: {id: competency.id}
        }]);

        requests[0].done(function(courses) {
            var context = {
                courses: courses
            };
            templates.render('tool_lp/linked_courses_summary', context).done(function(html) {
                str.get_string('linkedcourses', 'tool_lp').done(function(linkedcourses) {
                    new Dialogue(
                        linkedcourses, // Title.
                        html, // The linked courses.
                        initMovePopup
                    );
                }).fail(notification.exception);
            }).fail(notification.exception);
        }).fail(notification.exception);
    };

    /**
     * Open a competencies popup to relate competencies.
     *
     * @method relateCompetenciesHandler
     */
    var relateCompetenciesHandler = function() {
        relatedTarget = $('[data-region="competencyactions"]').data('competency');

        if (!pickerInstance) {
            pickerInstance = new Picker(pageContextId, relatedTarget.competencyframeworkid);
            pickerInstance.on('save', function(e, data) {
                var compIds = data.competencyIds;

                var calls = [];
                $.each(compIds, function(index, value) {
                    calls.push({
                        methodname: 'core_competency_add_related_competency',
                        args: {competencyid: value, relatedcompetencyid: relatedTarget.id}
                    });
                });

                calls.push({
                    methodname: 'tool_lp_data_for_related_competencies_section',
                    args: {competencyid: relatedTarget.id}
                });

                var promises = ajax.call(calls);

                promises[calls.length - 1].then(function(context) {
                    return templates.render('tool_lp/related_competencies', context);
                }).then(function(html, js) {
                    $('[data-region="relatedcompetencies"]').replaceWith(html);
                    templates.runTemplateJS(js);
                    updatedRelatedCompetencies();
                    return;
                }).catch(notification.exception);
            });
        }

        pickerInstance.setDisallowedCompetencyIDs([relatedTarget.id]);
        pickerInstance.display();
    };

    var ruleConfigHandler = function(e) {
        e.preventDefault();
        relatedTarget = $('[data-region="competencyactions"]').data('competency');
        ruleConfigInstance.setTargetCompetencyId(relatedTarget.id);
        ruleConfigInstance.display();
    };

    var ruleConfigSaveHandler = function(e, config) {
        var update = {
            id: relatedTarget.id,
            shortname: relatedTarget.shortname,
            idnumber: relatedTarget.idnumber,
            description: relatedTarget.description,
            descriptionformat: relatedTarget.descriptionformat,
            ruletype: config.ruletype,
            ruleoutcome: config.ruleoutcome,
            ruleconfig: config.ruleconfig
        };
        var promise = ajax.call([{
            methodname: 'core_competency_update_competency',
            args: {competency: update}
        }]);
        promise[0].then(function(result) {
            if (result) {
                relatedTarget.ruletype = config.ruletype;
                relatedTarget.ruleoutcome = config.ruleoutcome;
                relatedTarget.ruleconfig = config.ruleconfig;
                renderCompetencySummary(relatedTarget);
            }
            return;
        }).catch(notification.exception);
    };

    /**
     * Delete a competency.
     * @method doDelete
     */
    var doDelete = function() {
        // We are chaining ajax requests here.
        var competency = $('[data-region="competencyactions"]').data('competency');
        var requests = ajax.call([{
            methodname: 'core_competency_delete_competency',
            args: {id: competency.id}
        }, {
            methodname: 'tool_lp_data_for_competencies_manage_page',
            args: {competencyframeworkid: competency.competencyframeworkid,
                    search: $('[data-region="filtercompetencies"] input').val()}
        }]);
        requests[0].done(function(success) {
            if (success === false) {
                str.get_strings([
                {key: 'competencycannotbedeleted', component: 'tool_lp', param: competency.shortname},
                {key: 'cancel', component: 'moodle'}
                ]).done(function(strings) {
                    notification.alert(
                        null,
                        strings[0]
                    );
                }).fail(notification.exception);
            }
        }).fail(notification.exception);
        requests[1].done(reloadPage).fail(notification.exception);
    };

    /**
     * Show a confirm dialogue before deleting a competency.
     * @method deleteCompetencyHandler
     */
    var deleteCompetencyHandler = function() {
        var competency = $('[data-region="competencyactions"]').data('competency'),
            confirmMessage = 'deletecompetency';

        if (treeModel.hasRule(competency.parentid)) {
            confirmMessage = 'deletecompetencyparenthasrule';
        }

        str.get_strings([
            {key: 'confirm', component: 'moodle'},
            {key: confirmMessage, component: 'tool_lp', param: competency.shortname},
            {key: 'delete', component: 'moodle'},
            {key: 'cancel', component: 'moodle'}
        ]).done(function(strings) {
            notification.confirm(
                strings[0], // Confirm.
                strings[1], // Delete competency X?
                strings[2], // Delete.
                strings[3], // Cancel.
                doDelete
            );
        }).fail(notification.exception);
    };

    /**
     * HTML5 implementation of drag/drop (there is an accesible alternative in the menus).
     * @method dragStart
     * @param {Event} e
     */
    var dragStart = function(e) {
        e.originalEvent.dataTransfer.setData('text', $(e.target).parent().data('id'));
    };

    /**
     * HTML5 implementation of drag/drop (there is an accesible alternative in the menus).
     * @method allowDrop
     * @param {Event} e
     */
    var allowDrop = function(e) {
        e.originalEvent.dataTransfer.dropEffect = 'move';
        e.preventDefault();
    };

    /**
     * HTML5 implementation of drag/drop (there is an accesible alternative in the menus).
     * @method dragEnter
     * @param {Event} e
     */
    var dragEnter = function(e) {
        e.preventDefault();
        $(this).addClass('currentdragtarget');
    };

    /**
     * HTML5 implementation of drag/drop (there is an accesible alternative in the menus).
     * @method dragLeave
     * @param {Event} e
     */
    var dragLeave = function(e) {
        e.preventDefault();
        $(this).removeClass('currentdragtarget');
    };

    /**
     * HTML5 implementation of drag/drop (there is an accesible alternative in the menus).
     * @method dropOver
     * @param {Event} e
     */
    var dropOver = function(e) {
        e.preventDefault();
        moveSource = e.originalEvent.dataTransfer.getData('text');
        moveTarget = $(e.target).parent().data('id');
        $(this).removeClass('currentdragtarget');

        confirmMove();
    };

    /**
     * Deletes a related competency without confirmation.
     *
     * @param {Event} e The event that triggered the action.
     * @method deleteRelatedHandler
     */
    var deleteRelatedHandler = function(e) {
        e.preventDefault();

        var relatedid = this.id.substr(11);
        var competency = $('[data-region="competencyactions"]').data('competency');
        var removeRelated = ajax.call([
            {methodname: 'core_competency_remove_related_competency',
              args: {relatedcompetencyid: relatedid, competencyid: competency.id}},
            {methodname: 'tool_lp_data_for_related_competencies_section',
              args: {competencyid: competency.id}}
        ]);

        removeRelated[1].done(function(context) {
            templates.render('tool_lp/related_competencies', context).done(function(html) {
                $('[data-region="relatedcompetencies"]').replaceWith(html);
                updatedRelatedCompetencies();
            }).fail(notification.exception);
        }).fail(notification.exception);
    };

    /**
     * Updates the competencies list (with relations) and add listeners.
     *
     * @method updatedRelatedCompetencies
     */
    var updatedRelatedCompetencies = function() {

        // Listeners to newly loaded related competencies.
        $('[data-action="deleterelation"]').on('click', deleteRelatedHandler);

    };

    /**
     * Log the competency viewed event.
     *
     * @param  {Object} competency The competency.
     * @method triggerCompetencyViewedEvent
     */
    var triggerCompetencyViewedEvent = function(competency) {
        if (competency.id !== selectedCompetencyId) {
            // Set the selected competency id.
            selectedCompetencyId = competency.id;
            ajax.call([{
                    methodname: 'core_competency_competency_viewed',
                    args: {id: competency.id}
            }]);
        }
    };

    /**
     * Return the taxonomy constant for a level.
     *
     * @param  {Number} level The level.
     * @return {String}
     * @function getTaxonomyAtLevel
     */
    var getTaxonomyAtLevel = function(level) {
        var constant = taxonomiesConstants[level];
        if (!constant) {
            constant = 'competency';
        }
        return constant;
    };

    /**
     * Render the competency summary.
     *
     * @param  {Object} competency The competency.
     */
    var renderCompetencySummary = function(competency) {
        var promise = $.Deferred().resolve().promise(),
            context = {};

        context.competency = competency;
        context.showdeleterelatedaction = true;
        context.showrelatedcompetencies = true;
        context.showrule = false;

        if (competency.ruleoutcome != Outcomes.NONE) {
            // Get the outcome and rule name.
            promise = Outcomes.getString(competency.ruleoutcome).then(function(str) {
                var name;
                $.each(rulesModules, function(index, modInfo) {
                    if (modInfo.type == competency.ruletype) {
                        name = modInfo.name;
                    }
                });
                return [str, name];
            });
        }

        promise.then(function(strs) {
            if (typeof strs !== 'undefined') {
                context.showrule = true;
                context.rule = {
                    outcome: strs[0],
                    type: strs[1]
                };
            }
            return context;
        }).then(function(context) {
            return templates.render('tool_lp/competency_summary', context);
        }).then(function(html) {
            $('[data-region="competencyinfo"]').html(html);
            $('[data-action="deleterelation"]').on('click', deleteRelatedHandler);
            return templates.render('tool_lp/loading', {});
        }).then(function(html, js) {
            templates.replaceNodeContents('[data-region="relatedcompetencies"]', html, js);
            return ajax.call([{
                methodname: 'tool_lp_data_for_related_competencies_section',
                args: {competencyid: competency.id}
            }])[0];
        }).then(function(context) {
            return templates.render('tool_lp/related_competencies', context);
        }).then(function(html, js) {
            $('[data-region="relatedcompetencies"]').replaceWith(html);
            templates.runTemplateJS(js);
            updatedRelatedCompetencies();
            return;
        }).catch(notification.exception);
    };

    /**
     * Return the string "Add <taxonomy>".
     *
     * @param  {Number} level The level.
     * @return {String}
     * @function strAddTaxonomy
     */
    var strAddTaxonomy = function(level) {
        return str.get_string('taxonomy_add_' + getTaxonomyAtLevel(level), 'tool_lp');
    };

    /**
     * Return the string "Selected <taxonomy>".
     *
     * @param  {Number} level The level.
     * @return {String}
     * @function strSelectedTaxonomy
     */
    var strSelectedTaxonomy = function(level) {
        return str.get_string('taxonomy_selected_' + getTaxonomyAtLevel(level), 'tool_lp');
    };

    /**
     * Handler when a node in the aria tree is selected.
     * @method selectionChanged
     * @param {Event} evt The event that triggered the selection change.
     * @param {Object} params The parameters for the event. Contains a list of selected nodes.
     * @return {Boolean}
     */
    var selectionChanged = function(evt, params) {
        var node = params.selected,
            id = $(node).data('id'),
            btn = $('[data-region="competencyactions"] [data-action="add"]'),
            actionMenu = $('[data-region="competencyactionsmenu"]'),
            selectedTitle = $('[data-region="selected-competency"]'),
            level = 0,
            sublevel = 1;

        menubar.closeAll();

        if (typeof id === "undefined") {
            // Assume this is the root of the tree.
            // Here we are only getting the text from the top of the tree, to do it we clone the tree,
            // remove all children and then call text on the result.
            $('[data-region="competencyinfo"]').html(node.clone().children().remove().end().text());
            $('[data-region="competencyactions"]').data('competency', null);
            actionMenu.hide();

        } else {
            var competency = treeModel.getCompetency(id);

            level = treeModel.getCompetencyLevel(id);
            sublevel = level + 1;

            actionMenu.show();
            $('[data-region="competencyactions"]').data('competency', competency);
            renderCompetencySummary(competency);
            // Log Competency viewed event.
            triggerCompetencyViewedEvent(competency);
        }
        strSelectedTaxonomy(level).then(function(str) {
            selectedTitle.text(str);
            return;
        }).catch(notification.exception);

        strAddTaxonomy(sublevel).then(function(str) {
            btn.show()
                .find('[data-region="term"]')
                .text(str);
            return;
        }).catch(notification.exception);

        // We handled this event so consume it.
        evt.preventDefault();
        return false;
    };

    /**
     * Return the string "Selected <taxonomy>".
     *
     * @function parseTaxonomies
     * @param  {String} taxonomiesstr Comma separated list of taxonomies.
     * @return {Array} of level => taxonomystr
     */
    var parseTaxonomies = function(taxonomiesstr) {
        var all = taxonomiesstr.split(',');
        all.unshift("");
        delete all[0];

        // Note we don't need to fill holes, because other functions check for empty anyway.
        return all;
    };

    return {
        /**
         * Initialise this page (attach event handlers etc).
         *
         * @method init
         * @param {Object} model The tree model provides some useful functions for loading and searching competencies.
         * @param {Number} pagectxid The page context ID.
         * @param {Object} taxonomies Constants indexed by level.
         * @param {Object} rulesMods The modules of the rules.
         */
        init: function(model, pagectxid, taxonomies, rulesMods) {
            treeModel = model;
            pageContextId = pagectxid;
            taxonomiesConstants = parseTaxonomies(taxonomies);
            rulesModules = rulesMods;

            $('[data-region="competencyactions"] [data-action="add"]').on('click', addHandler);

            menubar.enhance('.competencyactionsmenu', {
                '[data-action="edit"]': editHandler,
                '[data-action="delete"]': deleteCompetencyHandler,
                '[data-action="move"]': moveHandler,
                '[data-action="moveup"]': moveUpHandler,
                '[data-action="movedown"]': moveDownHandler,
                '[data-action="linkedcourses"]': seeCoursesHandler,
                '[data-action="relatedcompetencies"]': relateCompetenciesHandler.bind(this),
                '[data-action="competencyrules"]': ruleConfigHandler.bind(this)
            });
            $('[data-region="competencyactionsmenu"]').hide();
            $('[data-region="competencyactions"] [data-action="add"]').hide();

            $('[data-region="filtercompetencies"]').on('submit', updateSearchHandler);
            // Simple html5 drag drop because we already added an accessible alternative.
            var top = $('[data-region="managecompetencies"] [data-enhance="tree"]');
            top.on('dragstart', 'li>span', dragStart)
                .on('dragover', 'li>span', allowDrop)
                .on('dragenter', 'li>span', dragEnter)
                .on('dragleave', 'li>span', dragLeave)
                .on('drop', 'li>span', dropOver);

            model.on('selectionchanged', selectionChanged);

            // Prepare the configuration tool.
            ruleConfigInstance = new RuleConfig(treeModel, rulesModules);
            ruleConfigInstance.on('save', ruleConfigSaveHandler.bind(this));
        }
    };
});
