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
 * Module to enable inline editing of a comptency grade.
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/notification', 'core/ajax', 'core/log'], function($, notification, ajax, log) {

    /**
     * InlineEditor
     *
     * @param {String} The id of the form element.
     * @param {Object} The scale config for this competency.
     * @param {Number} The id of the competency.
     * @param {Number} The id of the user.
     * @param {Number} The id of the plan.
     * @param {Number} The id of the course.
     * @param {String} Language string for choose a rating.
     */
    var InlineEditor = function(formId, scaleConfig, competencyId, userId, planId, courseId, chooseStr) {
        this._formId = formId;
        this._scaleConfig = scaleConfig;
        this._competencyId = competencyId;
        this._userId = userId;
        this._planId = planId;
        this._courseId = courseId;
        this._valid = true;
        this._chooseStr = chooseStr;
        this._buildSelect();
        this._addListeners();

        if (this._planId) {
            this._methodName = 'tool_lp_grade_competency_in_plan';
            this._args = {
                competencyid: this._competencyId,
                planid: this._planId
            };
        } else if (this._courseId) {
            this._methodName = 'tool_lp_grade_competency_in_course';
            this._args = {
                competencyid: this._competencyId,
                courseid: this._courseId,
                userid: this._userId
            };
        } else {
            this._methodName = 'tool_lp_grade_competency';
            this._args = {
                userid: this._userId,
                competencyid: this._competencyId
            };
        }
    };

    /**
     * Add all the options to the select.
     *
     * @method _buildSelect
     */
    InlineEditor.prototype._buildSelect = function() {
        var i = 1;

        var blankOption = $('<option></option>');
        blankOption.text(this._chooseStr);
        blankOption.attr('value', '');
        $(document.getElementById(this._formId)).find('select').append(blankOption);
        // The first item is the scaleid - we don't care about that.
        for (i = 1; i < this._scaleConfig.length; i++) {
            var optionConfig = this._scaleConfig[i];
            var optionEle = $('<option></option>');
            optionEle.text(optionConfig.name);
            optionEle.attr('value', optionConfig.id);

            $(document.getElementById(this._formId)).find('select').append(optionEle);
        }
    };

    /**
     * Handle grade button click
     *
     * @param {Event} event
     * @method _handleGrade
     */
    InlineEditor.prototype._handleGrade = function(event) {
        var currentthis = this;
        var grade = $(document.getElementById(this._formId)).find('select').val();
        event.preventDefault();
        if (this._valid && grade) {
            var args = this._args;
            args.grade = grade;
            args.override = true;

            ajax.call([{
                methodname: this._methodName,
                args: args,
                done: function(evidence) {
                    currentthis._trigger('competencyupdated', { args: args, evidence: evidence});
                },
                fail: notification.exception
            }]);
        }
    };

    /**
     * Handle suggest button click
     *
     * @param {Event} event
     * @method _handleSuggest
     */
    InlineEditor.prototype._handleSuggest = function(event) {
        var currentthis = this;
        var grade = $(document.getElementById(this._formId)).find('select').val();
        event.preventDefault();
        if (this._valid && grade) {
            var args = this._args;
            args.grade = grade;
            args.override = false;
            ajax.call([{
                methodname: this._methodName,
                args: args,
                done: function(evidence) {
                    currentthis._trigger('competencyupdated', { args: args, evidence: evidence});
                },
                fail: notification.exception
            }]);
        }
    };

    /**
     * Setup event listeners.
     *
     * @method _addListeners
     */
    InlineEditor.prototype._addListeners = function() {
        var currentthis = this;
        var form = $(document.getElementById(this._formId));
        var gradebutton = form.find('[data-action="grade"]');
        gradebutton.on('click', function(event) {
            currentthis._handleGrade.call(currentthis, event);
        });
        var suggestbutton = form.find('[data-action="suggest"]');
        suggestbutton.on('click', function(event) {
            currentthis._handleSuggest.call(currentthis, event);
        });
    };

    /**
     * Trigger an event from this module.
     *
     * @param {String} eventname - Only 'competencyupdated' is supported
     * @param {Object} arguments - Additional arguments for the event.
     * @return InlineEditor for chaining
     * @method _trigger
     */
    InlineEditor.prototype._trigger = function(eventname, data) {
        if (eventname != 'competencyupdated') {
            notification.exception('Invalid event name:' + eventname);
        }
        $(document.getElementById(this._formId)).trigger(eventname, data);
        return this;
    };

    /**
     * Attach a listener for events triggered from this module.
     *
     * @param {String} eventname - Only 'competencyupdated' is supported
     * @param {Function} handler - Event handler to call when this event is triggered.
     * @return InlineEditor for chaining
     * @method on
     */
    InlineEditor.prototype.on = function(eventname, handler) {
        if (eventname != 'competencyupdated') {
            notification.exception('Invalid event name:' + eventname);
        }
        $(document.getElementById(this._formId)).on(eventname, handler);
        return this;
    };


    /** @type {String} The id of the select element. */
    InlineEditor.prototype._formId = null;
    /** @type {Object} The scale config for this competency. */
    InlineEditor.prototype._scaleConfig = null;
    /** @type {Number} The id of the competency. */
    InlineEditor.prototype._competencyId = null;
    /** @type {Number} The id of the user. */
    InlineEditor.prototype._userId = null;
    /** @type {Number} The id of the plan. */
    InlineEditor.prototype._planId = null;
    /** @type {Number} The id of the course. */
    InlineEditor.prototype._courseId = null;
    /** @type {Boolean} Is this module valid. */
    InlineEditor.prototype._valid = null;

    return /** @alias module:tool_lp/grade_user_competency_inline */ InlineEditor;

});
