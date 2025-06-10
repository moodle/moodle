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
 * Reset grade dialogue.
 *
 * @module     report_lpmonitoring/resetgrade_dialogue
 * @author     Marie-Eve Lévesque <marie-eve.levesque.8@umontreal.ca>
 * @copyright  2019 Université de Montréal
 */

define(['jquery',
    'core/notification',
    'core/templates',
    'tool_lp/dialogue',
    'tool_lp/event_base',
    'core/str'],
    function($, Notification, Templates, Dialogue, EventBase, Str) {

        /**
         * Grade dialogue class.
         * @param {Boolean} allCompetencies
         */
        var ResetGradeDialogue = function(allCompetencies) {
            EventBase.prototype.constructor.apply(this, []);
            this.allCompetencies = allCompetencies;
        };
        ResetGradeDialogue.prototype = Object.create(EventBase.prototype);

        /** @var {Boolean} True if the popup is to reset all competencies, false for only one competency. */
        ResetGradeDialogue.prototype.allCompetencies = false;

        /** @type {Dialogue} The dialogue. */
        ResetGradeDialogue.prototype._popup = null;

        /**
         * After render hook.
         *
         * @method _afterRender
         * @protected
         */
        ResetGradeDialogue.prototype._afterRender = function() {
            var btnRate = this._find('[data-action="reset"]'),
                txtComment = this._find('[name="comment"]');

            this._find('[data-action="cancel"]').click(function(e) {
                e.preventDefault();
                this._trigger('cancelled');
                this.close();
            }.bind(this));

            btnRate.click(function(e) {
                e.preventDefault();
                this._trigger('rated', {
                    'note': txtComment.val()
                });
                this.close();
            }.bind(this));
        };

        /**
         * Close the dialogue.
         *
         * @method close
         */
        ResetGradeDialogue.prototype.close = function() {
            if(this._popup !== null) {
                this._popup.close();
                this._popup = null;
            }
        };

        /**
         * Opens the reset grade dialogue.
         *
         * @method display
         * @return {Promise}
         */
        ResetGradeDialogue.prototype.display = function() {
            var strname = 'reset';
            var strmodule = 'core';
            if (this.allCompetencies) {
                strname = 'resetallratings';
                strmodule = 'report_lpmonitoring';
            }
            return this._render().then(function(html) {
                return Str.get_string(strname, strmodule).then(function(title) {
                    this._popup = new Dialogue(
                        title,
                        html,
                        this._afterRender.bind(this),
                        this.close.bind(this)
                    );
                }.bind(this));
            }.bind(this)).fail(Notification.exception);
        };

        /**
         * Find a node in the dialogue.
         *
         * @param {String} selector
         * @method _find
         * @returns {node} The node
         * @protected
         */
        ResetGradeDialogue.prototype._find = function(selector) {
            return $(this._popup.getContent()).find(selector);
        };

        /**
         * Render the dialogue.
         *
         * @method _render
         * @protected
         * @return {Promise}
         */
        ResetGradeDialogue.prototype._render = function() {
            var context = {};
            context.allcompetencies = this.allCompetencies;
            return Templates.render('report_lpmonitoring/competency_reset', context);
        };

        return ResetGradeDialogue;
    });