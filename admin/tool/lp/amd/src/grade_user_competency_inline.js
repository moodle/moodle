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

define(['jquery',
        'core/notification',
        'core/ajax',
        'core/log',
        'tool_lp/grade_dialogue',
        'tool_lp/event_base',
    ], function($, notification, ajax, log, GradeDialogue, EventBase) {

    /**
     * InlineEditor
     *
     * @param {String} selector The selector to trigger the grading.
     * @param {Object} The scale config for this competency.
     * @param {Number} The id of the competency.
     * @param {Number} The id of the user.
     * @param {Number} The id of the plan.
     * @param {Number} The id of the course.
     * @param {String} Language string for choose a rating.
     * @param {Boolean} canGrade Whether the user can grade.
     * @param {Boolean} canSuggest Whether the user can suggest.
     */
    var InlineEditor = function(selector, scaleConfig, competencyId, userId, planId, courseId, chooseStr, canGrade, canSuggest) {
        EventBase.prototype.constructor.apply(this, []);

        var trigger = $(selector);
        if (!trigger.length) {
            throw new Error('Could not find the trigger');
        }

        this._scaleConfig = scaleConfig;
        this._competencyId = competencyId;
        this._userId = userId;
        this._planId = planId;
        this._courseId = courseId;
        this._chooseStr = chooseStr;
        this._canGrade = canGrade;
        this._canSuggest = canSuggest;
        this._setUp();

        trigger.click(function(e) {
            e.preventDefault();
            this._dialogue.display();
        }.bind(this));

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
    InlineEditor.prototype = Object.create(EventBase.prototype);

    /**
     * Setup.
     *
     * @method _setUp
     */
    InlineEditor.prototype._setUp = function() {
        var options = [],
            self = this;

        options.push({
            value: '',
            name: this._chooseStr
        });

        for (var i = 1; i < this._scaleConfig.length; i++) {
            var optionConfig = this._scaleConfig[i];
            options.push({
                value: optionConfig.id,
                name: optionConfig.name
            });
        }

        this._dialogue = new GradeDialogue(options, this._canGrade, this._canSuggest);
        this._dialogue.on('rated', function(e, data) {
            var args = this._args;
            args.grade = data.rating;
            args.note = data.note;
            args.override = true;
            ajax.call([{
                methodname: this._methodName,
                args: args,
                done: function(evidence) {
                    this._trigger('competencyupdated', { args: args, evidence: evidence });
                }.bind(self),
                fail: notification.exception
            }]);
        }.bind(this));
        this._dialogue.on('suggested', function(e, data) {
            var args = this._args;
            args.grade = data.rating;
            args.note = data.note;
            args.override = false;
            ajax.call([{
                methodname: this._methodName,
                args: args,
                done: function(evidence) {
                    this._trigger('competencyupdated', { args: args, evidence: evidence });
                }.bind(self),
                fail: notification.exception
            }]);
        }.bind(this));
    };

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
    /** @type {GradeDialogue} The grading dialogue. */
    InlineEditor.prototype._dialogue = null;
    /** @type {Boolean} Can grade. */
    InlineEditor.prototype._canGrade = null;
    /** @type {Boolean} Can suggest. */
    InlineEditor.prototype._canSuggest = null;

    return /** @alias module:tool_lp/grade_user_competency_inline */ InlineEditor;

});
