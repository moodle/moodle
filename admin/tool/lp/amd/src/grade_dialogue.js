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
 * Grade dialogue.
 *
 * @package    tool_lp
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
     * @param {Array} ratingOptions
     */
    var Grade = function(ratingOptions) {
        EventBase.prototype.constructor.apply(this, []);
        this._ratingOptions = ratingOptions;
    };
    Grade.prototype = Object.create(EventBase.prototype);

    /** @type {Dialogue} The dialogue. */
    Grade.prototype._popup = null;
    /** @type {Array} Array of objects containing, 'value', 'name' and optionally 'selected'. */
    Grade.prototype._ratingOptions = null;

    /**
     * After render hook.
     *
     * @method _afterRender
     * @protected
     */
    Grade.prototype._afterRender = function() {
        var btnRate = this._find('[data-action="rate"]'),
            lstRating = this._find('[name="rating"]'),
            txtComment = this._find('[name="comment"]');

        this._find('[data-action="cancel"]').click(function(e) {
            e.preventDefault();
            this._trigger('cancelled');
            this.close();
        }.bind(this));

        lstRating.change(function() {
            var node = $(this);
            if (!node.val()) {
                btnRate.prop('disabled', true);
            } else {
                btnRate.prop('disabled', false);
            }
        }).change();

        btnRate.click(function(e) {
            e.preventDefault();
            var val = lstRating.val();
            if (!val) {
                return;
            }
            this._trigger('rated', {
                'rating': val,
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
    Grade.prototype.close = function() {
        this._popup.close();
        this._popup = null;
    };

    /**
     * Opens the picker.
     *
     * @param {Number} competencyId The competency ID of the competency to work on.
     * @method display
     * @return {Promise}
     */
    Grade.prototype.display = function() {
        M.util.js_pending('tool_lp/grade_dialogue:display');
        return $.when(
            Str.get_string('rate', 'tool_lp'),
            this._render()
        )
        .then(function(title, templateResult) {
            this._popup = new Dialogue(
                title,
                templateResult[0],
                function() {
                    this._afterRender();
                    M.util.js_complete('tool_lp/grade_dialogue:display');
                }.bind(this)
            );

            return this._popup;
        }.bind(this))
        .catch(Notification.exception);
    };

    /**
     * Find a node in the dialogue.
     *
     * @param {String} selector
     * @method _find
     * @returns {node} The node
     * @protected
     */
    Grade.prototype._find = function(selector) {
        return $(this._popup.getContent()).find(selector);
    };

    /**
     * Render the dialogue.
     *
     * @method _render
     * @protected
     * @return {Promise}
     */
    Grade.prototype._render = function() {
        var context = {
            cangrade: this._canGrade,
            ratings: this._ratingOptions
        };
        return Templates.render('tool_lp/competency_grader', context);
    };

    return /** @alias module:tool_lp/grade_dialogue */ Grade;

});
