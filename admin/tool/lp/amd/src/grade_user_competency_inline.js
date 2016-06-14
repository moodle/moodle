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
        'tool_lp/scalevalues',
    ], function($, notification, ajax, log, GradeDialogue, EventBase, ScaleValues) {

    /**
     * InlineEditor
     *
     * @param {String} selector The selector to trigger the grading.
     * @param {Number} The id of the scale for this competency.
     * @param {Number} The id of the competency.
     * @param {Number} The id of the user.
     * @param {Number} The id of the plan.
     * @param {Number} The id of the course.
     * @param {String} Language string for choose a rating.
     */
    var InlineEditor = function(selector, scaleId, competencyId, userId, planId, courseId, chooseStr) {
        EventBase.prototype.constructor.apply(this, []);

        var trigger = $(selector);
        if (!trigger.length) {
            throw new Error('Could not find the trigger');
        }

        this._scaleId = scaleId;
        this._competencyId = competencyId;
        this._userId = userId;
        this._planId = planId;
        this._courseId = courseId;
        this._chooseStr = chooseStr;
        this._setUp();

        trigger.click(function(e) {
            e.preventDefault();
            this._dialogue.display();
        }.bind(this));

        if (this._planId) {
            this._methodName = 'core_competency_grade_competency_in_plan';
            this._args = {
                competencyid: this._competencyId,
                planid: this._planId
            };
        } else if (this._courseId) {
            this._methodName = 'core_competency_grade_competency_in_course';
            this._args = {
                competencyid: this._competencyId,
                courseid: this._courseId,
                userid: this._userId
            };
        } else {
            this._methodName = 'core_competency_grade_competency';
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

        var promise = ScaleValues.get_values(self._scaleId);
        promise.done(function(scalevalues) {
            options.push({
                value: '',
                name: self._chooseStr
            });

            for (var i = 0; i < scalevalues.length; i++) {
                var optionConfig = scalevalues[i];
                options.push({
                    value: optionConfig.id,
                    name: optionConfig.name
                });
            }

            self._dialogue = new GradeDialogue(options);
            self._dialogue.on('rated', function(e, data) {
                var args = self._args;
                args.grade = data.rating;
                args.note = data.note;
                ajax.call([{
                    methodname: self._methodName,
                    args: args,
                    done: function(evidence) {
                        self._trigger('competencyupdated', { args: args, evidence: evidence });
                    }.bind(self),
                    fail: notification.exception
                }]);
            }.bind(self));
        }).fail(notification.exception);
    };

    /** @type {Number} The scale id for this competency. */
    InlineEditor.prototype._scaleId = null;
    /** @type {Number} The id of the competency. */
    InlineEditor.prototype._competencyId = null;
    /** @type {Number} The id of the user. */
    InlineEditor.prototype._userId = null;
    /** @type {Number} The id of the plan. */
    InlineEditor.prototype._planId = null;
    /** @type {Number} The id of the course. */
    InlineEditor.prototype._courseId = null;
    /** @type {String} The text for Choose rating. */
    InlineEditor.prototype._chooseStr = null;
    /** @type {GradeDialogue} The grading dialogue. */
    InlineEditor.prototype._dialogue = null;

    return /** @alias module:tool_lp/grade_user_competency_inline */ InlineEditor;

});
