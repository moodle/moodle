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
 * Handle selecting parent competency in competency form.
 *
 * @module     tool_lp/parentcompetency_form
 * @package    tool_lp
 * @copyright  2015 Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax', 'core/str', 'tool_lp/competencypicker', 'core/templates', 'core/notification'],
    function($, ajax, Str, Picker, Templates, Notification) {

    /**
     * Parent Competency Form object.
     * @param {String} buttonSelector The parent competency button selector.
     * @param {String} inputHiddenSelector The hidden input field selector.
     * @param {String} staticElementSelector The static element displaying the parent competency.
     * @param {Number} frameworkId The competency framework ID.
     * @param {Number} frameworkMaxLevel The framework max level.
     * @param {Number} pageContextId The page context ID.
     */
    var ParentCompetencyForm = function(buttonSelector,
                                        inputHiddenSelector,
                                        staticElementSelector,
                                        frameworkId,
                                        frameworkMaxLevel,
                                        pageContextId) {
        this.buttonSelector = buttonSelector;
        this.inputHiddenSelector = inputHiddenSelector;
        this.staticElementSelector = staticElementSelector;
        this.frameworkId = frameworkId;
        this.frameworkMaxLevel = frameworkMaxLevel;
        this.pageContextId = pageContextId;

        // Register the events.
        this.registerEvents();
    };

    /** @var {String} The parent competency button selector. */
    ParentCompetencyForm.prototype.buttonSelector = null;
    /** @var {String} The hidden input field selector. */
    ParentCompetencyForm.prototype.inputHiddenSelector = null;
    /** @var {String} The static element displaying the parent competency. */
    ParentCompetencyForm.prototype.staticElementSelector = null;
    /** @var {Number} The competency framework ID. */
    ParentCompetencyForm.prototype.frameworkId = null;
    /** @var {Number} The framework max level. */
    ParentCompetencyForm.prototype.frameworkMaxLevel = null;
    /** @var {Number} The page context ID. */
    ParentCompetencyForm.prototype.pageContextId = null;

    /**
     * Set the parent competency in the competency form.
     *
     * @param {Object} Data containing selected cmpetency.
     * @method setParent
     */
    ParentCompetencyForm.prototype.setParent = function(data) {
        var self = this;

        if (data.competencyId !== 0) {
            ajax.call([
                { methodname: 'core_competency_read_competency', args: {
                    id: data.competencyId
                }}
            ])[0].done(function(competency) {
                $(self.staticElementSelector).html(competency.shortname);
                $(self.inputHiddenSelector).val(competency.id);
            }).fail(Notification.exception);
        } else {
            // Root of competency framework selected.
            Str.get_string('competencyframeworkroot', 'tool_lp').then(function(rootframework) {
                $(self.staticElementSelector).html(rootframework);
                $(self.inputHiddenSelector).val(data.competencyId);
            }).fail(Notification.exception);
        }
    };

    /**
     * Register the events of parent competency button click.
     *
     * @method registerEvents
     */
    ParentCompetencyForm.prototype.registerEvents = function() {
        var self = this;

        // Event on edit parent button.
        $(self.buttonSelector).on('click', function(e) {
            e.preventDefault();

            var picker = new Picker(self.pageContextId, self.frameworkId, 'self', false);
            var maxlevel = self.frameworkMaxLevel;
            // Override the fetchcompetencies method to filter by max level.
            picker._fetchCompetencies = function(frameworkId, searchText) {
                var self = this;

                return ajax.call([
                    { methodname: 'core_competency_search_competencies', args: {
                        searchtext: searchText,
                        competencyframeworkid: frameworkId
                    }}
                ])[0].done(function(competencies) {

                    var disabledcompetencies = [];
                    function addCompetencyChildren(parent, competencies) {
                        for (var i = 0; i < competencies.length; i++) {
                            // Check if competency does not exceed the framework max level.
                            var path = String(competencies[i].path),
                            level = path.split('/').length - 2;
                            if (level >= maxlevel && competencies[i].id !== "0") {
                                disabledcompetencies.push(competencies[i].id);
                            }

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
                    self.setDisallowedCompetencyIDs(disabledcompetencies);

                }.bind(self)).fail(Notification.exception);
            };
            // Override the render method to make framework selectable.
            picker._render = function() {
                var self = this;
                return self._preRender().then(function() {
                    var context = {
                        competencies: self._competencies,
                        framework: self._getFramework(self._frameworkId),
                        frameworks: self._frameworks,
                        search: self._searchText,
                        singleFramework: self._singleFramework,
                    };

                    return Templates.render('tool_lp/competency_picker_competencyform', context);
                }.bind(self));
            };

            // On selected competency.
            picker.on('save', function(e, data) {
                self.setParent(data);
            }.bind(self));

            picker.display();
        });
    };

    return {

        /**
         * Main initialisation.
         * @param {String} buttonSelector The parent competency button selector.
         * @param {String} inputHiddenSelector The hidden input field selector.
         * @param {String} staticElementSelector The static element displaying the parent competency.
         * @param {Number} frameworkId The competency framework ID.
         * @param {Number} frameworkMaxLevel The framework max level.
         * @param {Number} pageContextId The page context ID.
         * @method init
         */
        init: function(buttonSelector,
                        inputSelector,
                        staticElementSelector,
                        frameworkId,
                        frameworkMaxLevel,
                        pageContextId) {
            // Create instance.
            new ParentCompetencyForm(buttonSelector,
                                    inputSelector,
                                    staticElementSelector,
                                    frameworkId,
                                    frameworkMaxLevel,
                                    pageContextId);
        }
    };
});
