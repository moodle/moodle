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
     * @param {Number} pageContextId The page context ID.
     */
    var ParentCompetencyForm = function(buttonSelector,
                                        inputHiddenSelector,
                                        staticElementSelector,
                                        frameworkId,
                                        pageContextId) {
        this.buttonSelector = buttonSelector;
        this.inputHiddenSelector = inputHiddenSelector;
        this.staticElementSelector = staticElementSelector;
        this.frameworkId = frameworkId;
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
    /** @var {Number} The page context ID. */
    ParentCompetencyForm.prototype.pageContextId = null;

    /**
     * Set the parent competency in the competency form.
     *
     * @param {Object} data Data containing selected competency.
     * @method setParent
     */
    ParentCompetencyForm.prototype.setParent = function(data) {
        var self = this;

        if (data.competencyId !== 0) {
            ajax.call([
                {methodname: 'core_competency_read_competency', args: {
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
                return;
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
                });
            };

            // On selected competency.
            picker.on('save', function(e, data) {
                self.setParent(data);
            });

            picker.display();
        });
    };

    return {

        /**
         * Main initialisation.
         * @param {String} buttonSelector The parent competency button selector.
         * @param {String} inputSelector The hidden input field selector.
         * @param {String} staticElementSelector The static element displaying the parent competency.
         * @param {Number} frameworkId The competency framework ID.
         * @param {Number} pageContextId The page context ID.
         * @method init
         */
        init: function(buttonSelector,
                        inputSelector,
                        staticElementSelector,
                        frameworkId,
                        pageContextId) {
            // Create instance.
            new ParentCompetencyForm(buttonSelector,
                                    inputSelector,
                                    staticElementSelector,
                                    frameworkId,
                                    pageContextId);
        }
    };
});
