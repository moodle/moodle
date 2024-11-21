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
 * Filters of level up.
 *
 * @module     moodle-block_xp-filters
 * @package    block_xp
 * @copyright  2015 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @main       moodle-block_xp-filters
 */

/**
 * @module moodle-block_xp-filters
 */

/**
 * Filters.
 *
 * Note that this code has many assumption on the DOM structure and it is therefore
 * very tricky to alter it. This is not good, I am well aware of this...
 *
 * @namespace Y.M.block_xp
 * @class Filters
 * @constructor
 */
var FILTERS = function() {
    FILTERS.superclass.constructor.apply(this, arguments);
};
Y.namespace('M.block_xp').Filters = Y.extend(FILTERS, Y.Base, {

    /**
     * Copy of the node to add a new filter.
     * @type {Node}
     */
    addFilterLink: null,

    /**
     * Whether we can manually add more filters.
     * @type {Bool}
     */
    canAddFilter: false,

    /**
     * The main container.
     * @type {Node}
     */
    container: null,

    /**
     * Reference to the filter D&D object.
     * @type {Object}
     */
    filterDnD: null,

    /**
     * The dialog.
     * @type {Y.Panel}
     */
    rulepicker: null,

    /**
     * Reference to the rules D&D objects.
     * @type {Object}
     */
    rulesDnD: null,

    /**
     * The ruleset in which the user wants to add something.
     * @type {Node}
     */
    rulesetTarget: null,


    /**
     * Initializer.
     */
    initializer: function() {
        this.container = Y.one(this.get('containerSelector'));
        this.container.delegate('click', this.addNewFilter, SELECTORS.ADDFILTERBTN, this);
        this.container.delegate('click', this.addNewRule, SELECTORS.ADDRULEBTN, this);
        this.container.delegate('click', this.deleteFilter, SELECTORS.DELETEFILTERBTN, this);
        this.container.delegate('click', this.deleteRule, SELECTORS.DELETERULEBTN, this);

        var addFilterLink = this.container.one(SELECTORS.ADDFILTER);
        if (addFilterLink !== null) {
            this.addFilterLink = this.container.one(SELECTORS.ADDFILTER).cloneNode(true);
            this.canAddFilter = true;
        }

        this.prepareRuleDialog();

        this.filterDnD = Y.namespace('M.block_xp.Filters.DnD').init({
            containerClass: CSS.FILTERSLIST,
            containerSelector: this.get('containerSelector') + ' ' + SELECTORS.FILTERSLIST,
            groups: ['filters_' + this.container.generateID()],
            handleSelector: SELECTORS.FILTERMOVE,
            nodeClass: CSS.FILTER,
            nodeSelector: SELECTORS.FILTER
        });

        this.filterDnD.on('drag:end', function() {
            this.fixFilterSortorder();
        }, this);

        this.filterDnD.on('drop:over', function() {
            this.fixAddFilterLink();
        }, this);

        this.rulesDnD = {};
        this.container.all(SELECTORS.FILTER).each(function(node) {
            this.setFilterRulesDnD(node);
        }, this);
    },

    /**
     * Callback when clicking to add a new filter.
     *
     * @param {EventFacade} e
     */
    addNewFilter: function(e) {
        var link = e.currentTarget.get('parentNode'),
            filterNode = this.getNewFilterTemplate();

        e.preventDefault();

        if (this.canAddFilter) {
            link.insert(this.addFilterLink.cloneNode(true), 'after');
        }
        link.insert(filterNode, 'after');

        this.fixFilterSortorder();
        this.filterDnD.syncTargets();
        this.setFilterRulesDnD(filterNode);
    },

    /**
     * Callback when clicking to add a new rule.
     *
     * @param {EventFacade} e
     */
    addNewRule: function(e) {
        e.preventDefault();

        if (!this.rulepicker) {
            this.prepareRuleDialog();
        }

        this.rulesetTarget = e.currentTarget.ancestor(SELECTORS.RULE);
        this.rulepicker.display();
    },

    /**
     * Get the number of children rules a rule has.
     *
     * @param {Node} ruleNode The rule node.
     * @return {Number}
     */
    countChildrenRulesInRule: function(ruleNode) {
        var childrenRulesContainer = ruleNode.one(SELECTORS.RULES);
        return childrenRulesContainer ? childrenRulesContainer.all(SELECTORS.RULE).size() : 0;
    },

    /**
     * Delete a rule.
     *
     * @param  {EventFacade} e
     */
    deleteFilter: function(e) {
        e.preventDefault();
        var filter = e.currentTarget.ancestor(SELECTORS.FILTER);

        var deleteOperation = function() {
            // Delete the fitler.
            filter.remove();
            delete this.rulesDnD[filter.generateID()];

            // Fix the things.
            this.fixFilterSortorder();
            this.fixAddFilterLink();
        }.bind(this);

        // Check if the filter's main rule has rules.
        var rulesContainer = filter.one(SELECTORS.FILTERRULES);
        var firstRule = rulesContainer ? rulesContainer.one(SELECTORS.RULE) : null;
        if (firstRule && this.countChildrenRulesInRule(firstRule) > 0) {
            var confirm = new M.core.confirm({
                title: M.util.get_string('deleterule', 'block_xp'),
                question: M.util.get_string('areyousure', 'core'),
            });
            confirm.on('complete-yes', deleteOperation, this);
            confirm.show();
            return;
        }

        deleteOperation();
    },

    /**
     * Delete a rule.
     *
     * @param  {EventFacade} e
     */
    deleteRule: function(e) {
        e.preventDefault();
        var rule = e.currentTarget.ancestor(SELECTORS.RULE);
        var parentRule = rule.ancestor(SELECTORS.RULE, false, Y.bind(function(el) {
            return el == this.container;
        }, this));

        // Do not remove the main rule.
        if (!parentRule) {
            return;
        }

        var deleteOperation = function() {
            rule.remove(true);
        };

        // When rule has children, show confirmation.
        if (this.countChildrenRulesInRule(rule) > 0) {
            var confirm = new M.core.confirm({
                title: M.util.get_string('deletecondition', 'block_xp'),
                question: M.util.get_string('areyousure', 'core'),
            });
            confirm.on('complete-yes', deleteOperation, this);
            confirm.show();
            return;
        }

        deleteOperation();
    },

    /**
     * Check and fix the presence of the links to add a filter.
     */
    fixAddFilterLink: function() {
        if (!this.canAddFilter) {
            return;
        }

        var nodes = this.container.all(SELECTORS.FILTERSLISTNODES),
            lastNode,
            count = nodes.size();

        nodes.each(function(node, index) {
            var isLink = node.hasClass(CSS.ADDFILTER),
                isFirstNode = !lastNode,
                isDeleted = node.getData('deleted'),
                wasLink = !isFirstNode && lastNode.hasClass(CSS.ADDFILTER),
                isLastNode = index - 1 == count;

            // Ignore the deleted fitlers.
            if (isDeleted) {
                return;
            }

            // The first node is not a link.
            if (isFirstNode && !isLink) {
                node.insert(this.addFilterLink.cloneNode(true), 'before');
            }

            // The add link is duplicated.
            else if (!isFirstNode && wasLink && isLink) {
                node.remove();
                return;
            }

            // There are two filters in a row.
            else if (!isFirstNode && !wasLink && !isLink) {
                node.insert(this.addFilterLink.cloneNode(true), 'before');
            }

            // The last node is not a link.
            else if (isLastNode && !isLink) {
                node.insert(this.addFilterLink.cloneNode(true), 'after');
            }

            lastNode = node;
        }, this);

    },

    /**
     * Fix the sortorder of the filters.
     */
    fixFilterSortorder: function() {
        var filters = this.container.all(SELECTORS.FILTER),
            sortorder = 0;

        filters.each(function(node) {
            var basename = node.getData('basename'),
                sortnode = node.one('input[name="' + basename + '[sortorder]"]');
            if (sortnode) {
                sortnode.setAttribute('value', sortorder);
                sortorder++;
            }
        }, this);
    },

    /**
     * Generate the basename of a filter.
     *
     * This did not need to be in a method, but this highlights the way the names are constructed.
     *
     * @param  {Number} increment
     * @return {String}
     */
    generateFilterBasename: function(increment) {
        return 'filters[' + increment + ']';
    },

    /**
     * Generate the basename of a rule.
     *
     * This did not need to be in a method, but this highlights the way the names are constructed.
     *
     * @param  {String} basename
     * @param  {Number} increment
     * @return {String}
     */
    generateRuleBasename: function(basename, increment) {
        return basename + '[' + increment + ']';
    },

    /**
     * Get an unused filter increment.
     *
     * @return {Number}
     */
    getNewFilterIncrement: function() {
        var filters = this.container.all(SELECTORS.FILTER),
            filterIncrement = 0;

        filters.each(function(node) {
            var basename = node.getData('basename'),
                inc = parseInt(/\[([0-9]+)\]$/.exec(basename)[1] || 0, 10);

            filterIncrement = filterIncrement < inc ? inc : filterIncrement;
        }, this);

        return filterIncrement + 1;
    },

    /**
     * Get a new template for a filter.
     *
     * @return {Node}
     */
    getNewFilterTemplate: function() {
        var tpl = this.get('filter');

        tpl = tpl.replace(this.get('filterTemplateBasename'), this.generateFilterBasename(this.getNewFilterIncrement()));
        return Y.Node.create(tpl);
    },

    /**
     * Get an unused rule increment from a rule container.
     *
     * @param {Node} ruleContainer
     * @return {Number}
     */
    getNewRuleIncrement: function(ruleContainer) {
        var filters = ruleContainer.all(SELECTORS.CHILDRULESDEFINITIONS),
            increment = 0;

        filters.each(function(node) {
            var basename = node.getData('basename'),
                inc = parseInt(/\[([0-9]+)\]$/.exec(basename)[1] || 0, 10);

            increment = increment < inc ? inc : increment;
        }, this);

        return increment + 1;
    },

    /**
     * Callback executed when a rule is picked.
     *
     * @param  {EventFacacde} e
     * @param  {String} ruleId Matching the key of our rules attribute.
     */
    newRulePicked: function(e, ruleId) {
        var rule = this.get('rules')[ruleId],
            tpl = rule.template,
            rulesContainer = this.rulesetTarget.one(SELECTORS.RULES),
            basename = this.generateRuleBasename(rulesContainer.getData('basename'), this.getNewRuleIncrement(this.rulesetTarget));

        tpl = tpl.replace(this.get('ruleTemplateBasename'), basename);
        rulesContainer.insertBefore(tpl, rulesContainer.one(SELECTORS.ADDRULEINRULES));

        this.rulesDnD[rulesContainer.ancestor(SELECTORS.FILTER).generateID()].syncTargets();
    },

    /**
     * Prepare the rule picker dialog.
     */
    prepareRuleDialog: function() {
        var rules = [];
        Y.Object.each(this.get('rules'), function(v, k) {
            rules.push({
                id: k,
                name: v.name,
                info: v.info
            });
        }, this);
        this.rulepicker = Y.namespace('M.block_xp.RulePicker').init({
            rules: rules
        });
        this.rulepicker.on('picked', this.newRulePicked, this);
    },

    /**
     * Set drag & drop for rules in a filter.
     *
     * @param {Node} filterNode The filter container.
     */
    setFilterRulesDnD: function(filterNode) {
        if (!filterNode.one(SELECTORS.RULES)) {
            // If the filter node does not contain the selector, it means that there is only one rule
            // in it, that's the legacy rules. Therefore we ignore this.
            return;
        }

        this.rulesDnD[filterNode.generateID()] = Y.namespace('M.block_xp.Filters.DnD').init({
            additionalDropsSelector: SELECTORS.ADDRULE,
            dropBeforeSelector: SELECTORS.ADDRULE,
            containerClass: CSS.RULES,
            containerSelector: '#' + filterNode.generateID() + ' ' + SELECTORS.RULES,
            groups: ['rules_' + filterNode.generateID()],
            handleSelector: SELECTORS.RULEMOVE,
            nodeClass: CSS.RULE,
            nodeSelector: SELECTORS.RULE
        });

        this.rulesDnD[filterNode.generateID()].on('drop:hit', function(e) {
            var drag = e.drag.get('node'),
                drop = e.drop.get('node'),
                initialBasename = drag.one(SELECTORS.RULEDEFINITION).getData('basename'),
                ruleBasename = drop.ancestor(SELECTORS.RULES, true).getData('basename'),
                newIncrement = this.getNewRuleIncrement(drop.ancestor(SELECTORS.RULE)),
                newBasename = ruleBasename + '[' + newIncrement + ']';

            // Update the structure.
            drag.all('[data-basename], [name]').each(function(node) {
                if (node.hasAttribute('data-basename')) {
                    node.setAttribute('data-basename', node.getAttribute('data-basename').replace(initialBasename, newBasename));
                }
                if (node.hasAttribute('name')) {
                    node.setAttribute('name', node.getAttribute('name').replace(initialBasename, newBasename));
                }
            }, this);

        }, this);
    }

}, {
    NAME: NAME,
    ATTRS: {

        /**
         * Selector for the main container.
         *
         * @type {Object}
         */
        containerSelector: {
            validator: Y.Lang.isString,
            value: null
        },

        /**
         * Template for a new filter.
         *
         * @type {String}
         */
        filter: {
            validator: Y.Lang.isString,
            value: ''
        },

        /**
         * Regex to use when replacing the basename in a filter template.
         *
         * @type {String}
         */
        filterTemplateBasename: {
            value: /filters\[[0-9]+\]/g
        },

        /**
         * List of rules.
         *
         * The keys of the object must be a rule identifier.
         *
         * Each entry contains the following keys:
         * - String name: The name of the rule.
         * - String template: The HTML of the template.
         * - String info (optional): Information about the rule.
         *
         * @type {Object}
         */
        rules: {
            validator: Y.Lang.isObject,
            value: null
        },

        /**
         * Regex to use when replacing the basename in a rule template.
         *
         * @type {String}
         */
        ruleTemplateBasename: {
            value: /XXXXX/g
        },

    }
});

Y.namespace('M.block_xp.Filters').init = function(config) {
    return new FILTERS(config);
};
