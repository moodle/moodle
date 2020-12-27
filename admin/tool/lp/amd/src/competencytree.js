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
 * Handle selection changes on the competency tree.
 *
 * @module     tool_lp/competencyselect
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['core/ajax', 'core/notification', 'core/templates', 'tool_lp/tree', 'tool_lp/competency_outcomes', 'jquery'],
       function(ajax, notification, templates, Ariatree, CompOutcomes, $) {

    // Private variables and functions.
    /** @var {Object[]} competencies - Cached list of competencies */
    var competencies = {};

    /** @var {Number} competencyFrameworkId - The current framework id */
    var competencyFrameworkId = 0;

    /** @var {String} competencyFrameworkShortName - The current framework short name */
    var competencyFrameworkShortName = '';

    /** @var {String} treeSelector - The selector for the root of the tree. */
    var treeSelector = '';

    /** @var {String} currentNodeId - The data-id of the current node in the tree. */
    var currentNodeId = '';

     /** @var {Boolean} competencyFramworkCanManage - Can manage the competencies framework */
    var competencyFramworkCanManage = false;

    /**
     * Build a tree from the flat list of competencies.
     * @param {Object} parent The parent competency.
     * @param {Array} all The list of all competencies.
     */
    var addChildren = function(parent, all) {
        var i = 0;
        var current = false;
        parent.haschildren = false;
        parent.children = [];
        for (i = 0; i < all.length; i++) {
            current = all[i];
            if (current.parentid == parent.id) {
                parent.haschildren = true;
                parent.children.push(current);
                addChildren(current, all);
            }
        }
    };

    /**
     * Load the list of competencies via ajax. Competencies are filtered by the searchtext.
     * @param {String} searchtext The text to filter on.
     * @return {promise}
     */
    var loadCompetencies = function(searchtext) {
        var deferred = $.Deferred();

        templates.render('tool_lp/loading', {}).done(function(loadinghtml, loadingjs) {
            templates.replaceNodeContents($(treeSelector), loadinghtml, loadingjs);

            var promises = ajax.call([{
                methodname: 'core_competency_search_competencies',
                args: {
                    searchtext: searchtext,
                    competencyframeworkid: competencyFrameworkId
                }
            }]);
            promises[0].done(function(result) {
                competencies = {};
                var i = 0;
                for (i = 0; i < result.length; i++) {
                    competencies[result[i].id] = result[i];
                }

                var children = [];
                var competency = false;
                for (i = 0; i < result.length; i++) {
                    competency = result[i];
                    if (parseInt(competency.parentid, 10) === 0) {
                        children.push(competency);
                        addChildren(competency, result);
                    }
                }
                var context = {
                    shortname: competencyFrameworkShortName,
                    canmanage: competencyFramworkCanManage,
                    competencies: children
                };
                templates.render('tool_lp/competencies_tree_root', context).done(function(html, js) {
                    templates.replaceNodeContents($(treeSelector), $(html).html(), js);
                    var tree = new Ariatree(treeSelector, false);

                    if (currentNodeId) {
                        var node = $(treeSelector).find('[data-id=' + currentNodeId + ']');
                        if (node.length) {
                            tree.selectItem(node);
                            tree.updateFocus(node);
                        }
                    }
                    deferred.resolve(competencies);
                }).fail(deferred.reject);
            }).fail(deferred.reject);
        });

        return deferred.promise();
    };

    /**
     * Whenever the current item in the tree is changed - remember the "id".
     * @param {Event} evt
     * @param {Object} params The parameters for the event (This is the selected node).
     */
    var rememberCurrent = function(evt, params) {
        var node = params.selected;
        currentNodeId = node.attr('data-id');
    };

    return /** @alias module:tool_lp/competencytree */ {
        // Public variables and functions.
        /**
         * Initialise the tree.
         *
         * @param {Number} id The competency framework id.
         * @param {String} shortname The framework shortname
         * @param {String} search The current search string
         * @param {String} selector The selector for the tree div
         * @param {Boolean} canmanage Can manage the competencies
         * @param {Number} competencyid The id of the competency to show first
         */
        init: function(id, shortname, search, selector, canmanage, competencyid) {
            competencyFrameworkId = id;
            competencyFrameworkShortName = shortname;
            competencyFramworkCanManage = canmanage;
            treeSelector = selector;
            loadCompetencies(search).fail(notification.exception);
            if (competencyid > 0) {
                currentNodeId = competencyid;
            }

            this.on('selectionchanged', rememberCurrent);
         },

        /**
         * Add an event handler for custom events emitted by the tree.
         *
         * @param {String} eventname The name of the event - only "selectionchanged" for now
         * @param {Function} handler The handler for the event.
         */
        on: function(eventname, handler) {
            // We can't use the tree on function directly
            // because the tree gets rebuilt whenever the search string changes,
            // instead we attach the listner to the root node of the tree which never
            // gets destroyed (same as "on()" code in the tree.js).
            $(treeSelector).on(eventname, handler);
        },

        /**
         * Get the children of a competency.
         *
         * @param  {Number} id The competency ID.
         * @return {Array}
         * @method getChildren
         */
        getChildren: function(id) {
            var children = [];
            $.each(competencies, function(index, competency) {
                if (competency.parentid == id) {
                    children.push(competency);
                }
            });
            return children;
        },

        /**
         * Get the competency framework id this model was initiliased with.
         *
         * @return {Number}
         */
        getCompetencyFrameworkId: function() {
            return competencyFrameworkId;
        },

        /**
         * Get a competency by id
         *
         * @param {Number} id The competency id
         * @return {Object}
         */
        getCompetency: function(id) {
            return competencies[id];
        },

        /**
         * Get the competency level.
         *
         * @param  {Number} id The competency ID.
         * @return {Number}
         */
        getCompetencyLevel: function(id) {
            var competency = this.getCompetency(id),
                level = competency.path.replace(/^\/|\/$/g, '').split('/').length;
            return level;
        },

        /**
         * Whether a competency has children.
         *
         * @param  {Number} id The competency ID.
         * @return {Boolean}
         * @method hasChildren
         */
        hasChildren: function(id) {
            return this.getChildren(id).length > 0;
        },

        /**
         * Does the competency have a rule?
         *
         * @param  {Number}  id The competency ID.
         * @return {Boolean}
         */
        hasRule: function(id) {
            var comp = this.getCompetency(id);
            if (comp) {
                return comp.ruleoutcome != CompOutcomes.OUTCOME_NONE
                    && comp.ruletype;
            }
            return false;
        },

        /**
         * Reload all the page competencies framework competencies.
         * @method reloadCompetencies
         * @return {Promise}
         */
        reloadCompetencies: function() {
            return loadCompetencies('').fail(notification.exception);
        },

        /**
         * Get all competencies for this framework.
         *
         * @return {Object[]}
         */
        listCompetencies: function() {
            return competencies;
        },

     };
 });
