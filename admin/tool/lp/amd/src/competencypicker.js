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
 * Competency picker.
 *
 * To handle 'save' events use: picker.on('save')
 * This will receive a object with either a single 'competencyId', or an array in 'competencyIds'
 * depending on the value of multiSelect.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery',
        'core/notification',
        'core/ajax',
        'core/templates',
        'tool_lp/dialogue',
        'core/str',
        'tool_lp/tree'],
        function($, Notification, Ajax, Templates, Dialogue, Str, Tree) {

    /**
     * Competency picker class.
     * @param {Number} pageContextId The page context ID.
     * @param {Number|false} singleFramework The ID of the framework when limited to one.
     * @param {String} pageContextIncludes One of 'children', 'parents', 'self'.
     * @param {Boolean} multiSelect Support multi-select in the tree.
     */
    var Picker = function(pageContextId, singleFramework, pageContextIncludes, multiSelect) {
        var self = this;
        self._eventNode = $('<div></div>');
        self._frameworks = [];
        self._reset();

        self._pageContextId = pageContextId;
        self._pageContextIncludes = pageContextIncludes || 'children';
        self._multiSelect = (typeof multiSelect === 'undefined' || multiSelect === true);
        if (singleFramework) {
            self._frameworkId = singleFramework;
            self._singleFramework = true;
        }
    };

    /** @type {Array} The competencies fetched. */
    Picker.prototype._competencies = null;
    /** @type {Array} The competencies that cannot be picked. */
    Picker.prototype._disallowedCompetencyIDs = null;
    /** @type {Node} The node we attach the events to. */
    Picker.prototype._eventNode = null;
    /** @type {Array} The list of frameworks fetched. */
    Picker.prototype._frameworks = null;
    /** @type {Number} The current framework ID. */
    Picker.prototype._frameworkId = null;
    /** @type {Number} The page context ID. */
    Picker.prototype._pageContextId = null;
    /** @type {Number} Relevant contexts inclusion. */
    Picker.prototype._pageContextIncludes = null;
    /** @type {Dialogue} The reference to the dialogue. */
    Picker.prototype._popup = null;
    /** @type {String} The string we filter the competencies with. */
    Picker.prototype._searchText = '';
    /** @type {Object} The competency that was selected. */
    Picker.prototype._selectedCompetencies = null;
    /** @type {Boolean} Whether we can browse frameworks or not. */
    Picker.prototype._singleFramework = false;
    /** @type {Boolean} Do we allow multi select? */
    Picker.prototype._multiSelect = true;
    /** @type {Boolean} Do we allow to display hidden framework? */
    Picker.prototype._onlyVisible = true;

    /**
     * Hook to executed after the view is rendered.
     *
     * @method _afterRender
     */
    Picker.prototype._afterRender = function() {
        var self = this;

        // Initialise the tree.
        var tree = new Tree(self._find('[data-enhance=linktree]'), self._multiSelect);

        // To prevent jiggling we only show the tree after it is enhanced.
        self._find('[data-enhance=linktree]').show();

        tree.on('selectionchanged', function(evt, params) {
            var selected = params.selected;
            evt.preventDefault();
            var validIds = [];
            $.each(selected, function(index, item) {
                var compId = $(item).data('id'),
                    valid = true;

                if (typeof compId === 'undefined') {
                    // Do not allow picking nodes with no id.
                    valid = false;
                } else {
                    $.each(self._disallowedCompetencyIDs, function(i, id) {
                        if (id == compId) {
                            valid = false;
                        }
                    });
                }
                if (valid) {
                    validIds.push(compId);
                }
            }.bind(self));

            self._selectedCompetencies = validIds;

            // TODO Implement disabling of nodes in the tree module somehow.
            if (!self._selectedCompetencies.length) {
                self._find('[data-region="competencylinktree"] [data-action="add"]').attr('disabled', 'disabled');
            } else {
                self._find('[data-region="competencylinktree"] [data-action="add"]').removeAttr('disabled');
            }
        }.bind(self));

        // Add listener for framework change.
        if (!self._singleFramework) {
            self._find('[data-action="chooseframework"]').change(function(e) {
                self._frameworkId = $(e.target).val();
                self._loadCompetencies().then(self._refresh.bind(self));
            }.bind(self));
        }

        // Add listener for search.
        self._find('[data-region="filtercompetencies"] button').click(function(e) {
            e.preventDefault();
            $(e.target).attr('disabled', 'disabled');
            self._searchText = self._find('[data-region="filtercompetencies"] input').val() || '';
            return self._refresh().always(function() {
                $(e.target).removeAttr('disabled');
            });
        }.bind(self));

        // Add listener for cancel.
        self._find('[data-region="competencylinktree"] [data-action="cancel"]').click(function(e) {
            e.preventDefault();
            self.close();
        }.bind(self));

        // Add listener for add.
        self._find('[data-region="competencylinktree"] [data-action="add"]').click(function(e) {
            e.preventDefault();
            if (!self._selectedCompetencies.length) {
                return;
            }

            if (self._multiSelect) {
                self._trigger('save', { competencyIds: self._selectedCompetencies });
            } else {
                // We checked above that the array has at least one value.
                self._trigger('save', { competencyId: self._selectedCompetencies[0] });
            }

            self.close();
        }.bind(self));

        // The list of selected competencies will be modified while looping (because of the listeners above).
        var currentItems = self._selectedCompetencies.slice(0);

        $.each(currentItems, function(index, id) {
            var node = self._find('[data-id=' + id + ']');
            if (node.length) {
                tree.toggleItem(node);
                tree.updateFocus(node);
            }
        }.bind(self));

    };

    /**
     * Close the dialogue.
     *
     * @method close
     */
    Picker.prototype.close = function() {
        var self = this;
        self._popup.close();
        self._reset();
    };

    /**
     * Opens the picker.
     *
     * @method display
     * @return {Promise}
     */
    Picker.prototype.display = function() {
        var self = this;
        return self._render().then(function(html) {
            return Str.get_string('competencypicker', 'tool_lp').then(function(title) {
                self._popup = new Dialogue(
                    title,
                    html,
                    self._afterRender.bind(self)
                );
            }.bind(self));
        }.bind(self)).fail(Notification.exception);
    };

    /**
     * Fetch the competencies.
     *
     * @param {Number} frameworkId The frameworkId.
     * @param {String} searchText Limit the competencies to those matching the text.
     * @method _fetchCompetencies
     * @return {Promise}
     */
    Picker.prototype._fetchCompetencies = function(frameworkId, searchText) {
        var self = this;

        return Ajax.call([
            { methodname: 'core_competency_search_competencies', args: {
                searchtext: searchText,
                competencyframeworkid: frameworkId
            }}
        ])[0].done(function(competencies) {

            function addCompetencyChildren(parent, competencies) {
                for (var i = 0; i < competencies.length; i++) {
                    if (competencies[i].parentid == parent.id) {
                        parent.haschildren = true;
                        competencies[i].children = [];
                        competencies[i].haschildren = false;
                        parent.children[parent.children.length] = competencies[i];
                        addCompetencyChildren(competencies[i], competencies);
                    }
                }
            }

            // Expand the list of competencies into a tree.
            var i, tree = [], comp;
            for (i = 0; i < competencies.length; i++) {
                comp = competencies[i];
                if (comp.parentid == "0") { // Loose check for now, because WS returns a string.
                    comp.children = [];
                    comp.haschildren = 0;
                    tree[tree.length] = comp;
                    addCompetencyChildren(comp, competencies);
                }
            }

            self._competencies = tree;

        }.bind(self)).fail(Notification.exception);
    };

    /**
     * Find a node in the dialogue.
     *
     * @param {String} selector
     * @method _find
     */
    Picker.prototype._find = function(selector) {
        return $(this._popup.getContent()).find(selector);
    };

    /**
     * Convenience method to get a framework object.
     *
     * @param {Number} fid The framework ID.
     * @method _getFramework
     */
    Picker.prototype._getFramework = function(fid) {
        var frm;
        $.each(this._frameworks, function(i, f) {
            if (f.id == fid) {
                frm = f;
                return false;
            }
        });
        return frm;
    };

    /**
     * Load the competencies.
     *
     * @method _loadCompetencies
     * @return {Promise}
     */
    Picker.prototype._loadCompetencies = function() {
        return this._fetchCompetencies(this._frameworkId, this._searchText);
    };

    /**
     * Load the frameworks.
     *
     * @method _loadFrameworks
     * @return {Promise}
     */
    Picker.prototype._loadFrameworks = function() {
        var promise,
            self = this;

        // Quit early because we already have the data.
        if (self._frameworks.length > 0) {
            return $.when();
        }

        if (self._singleFramework) {
            promise = Ajax.call([
                { methodname: 'core_competency_read_competency_framework', args: {
                    id: this._frameworkId
                }}
            ])[0].then(function(framework) {
                return [framework];
            });
        } else {
            promise = Ajax.call([
                { methodname: 'core_competency_list_competency_frameworks', args: {
                    sort: 'shortname',
                    context: { contextid: self._pageContextId },
                    includes: self._pageContextIncludes,
                    onlyvisible: self._onlyVisible
                }}
            ])[0];
        }

        return promise.done(function(frameworks) {
            self._frameworks = frameworks;
        }).fail(Notification.exception);
    };

    /**
     * Register an event listener.
     *
     * @param {String} type The event type.
     * @param {Function} handler The event listener.
     * @method on
     */
    Picker.prototype.on = function(type, handler) {
        this._eventNode.on(type, handler);
    };

    /**
     * Hook to executed before render.
     *
     * @method _preRender
     * @return {Promise}
     */
    Picker.prototype._preRender = function() {
        var self = this;
        return self._loadFrameworks().then(function() {
            if (!self._frameworkId && self._frameworks.length > 0) {
                self._frameworkId = self._frameworks[0].id;
            }

            // We could not set a framework ID, that probably means there are no frameworks accessible.
            if (!self._frameworkId) {
                self._frameworks = [];
                return $.when();
            }

            return self._loadCompetencies();
        }.bind(self));
    };

    /**
     * Refresh the view.
     *
     * @method _refresh
     * @return {Promise}
     */
    Picker.prototype._refresh = function() {
        var self = this;
        return self._render().then(function(html) {
            self._find('[data-region="competencylinktree"]').replaceWith(html);
            self._afterRender();
        }.bind(self));
    };

    /**
     * Render the dialogue.
     *
     * @method _render
     * @return {Promise}
     */
    Picker.prototype._render = function() {
        var self = this;
        return self._preRender().then(function() {

            if (!self._singleFramework) {
                $.each(self._frameworks, function(i, framework) {
                    if (framework.id == self._frameworkId) {
                        framework.selected = true;
                    } else {
                        framework.selected = false;
                    }
                });
            }

            var context = {
                competencies: self._competencies,
                framework: self._getFramework(self._frameworkId),
                frameworks: self._frameworks,
                search: self._searchText,
                singleFramework: self._singleFramework,
            };

            return Templates.render('tool_lp/competency_picker', context);
        }.bind(self));
    };

    /**
     * Reset the dialogue properties.
     *
     * This does not reset everything, just enough to reset the UI.
     *
     * @method _reset
     */
    Picker.prototype._reset = function() {
        this._competencies = [];
        this._disallowedCompetencyIDs = [];
        this._popup = null;
        this._searchText = '';
        this._selectedCompetencies = [];
    };

    /**
     * Set what competencies cannot be picked.
     *
     * This needs to be set after reset/close.
     *
     * @params {Number[]} The IDs.
     * @method _setDisallowedCompetencyIDs
     */
    Picker.prototype.setDisallowedCompetencyIDs = function(ids) {
        this._disallowedCompetencyIDs = ids;
    };

    /**
     * Trigger an event.
     *
     * @param {String} type The type of event.
     * @param {Object} The data to pass to the listeners.
     * @method _reset
     */
    Picker.prototype._trigger = function(type, data) {
        this._eventNode.trigger(type, [data]);
    };

    return /** @alias module:tool_lp/competencypicker */ Picker;

});
