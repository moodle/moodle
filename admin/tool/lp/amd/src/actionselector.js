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
 * Action selector.
 *
 * To handle 'save' events use: actionselector.on('save')
 * This will receive the information to display in popup.
 * The actions have the format [{'text': sometext, 'value' : somevalue}].
 *
 * @package    tool_lp
 * @copyright  2016 Serge Gauthier - <serge.gauthier.2@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery',
        'core/notification',
        'core/ajax',
        'core/templates',
        'tool_lp/dialogue',
        'tool_lp/event_base'],
        function($, Notification, Ajax, Templates, Dialogue, EventBase) {

    /**
     * Action selector class.
     * @param {String} title The title of popup.
     * @param {String} message The message to display.
     * @param {object} actions The actions that can be selected.
     * @param {String} confirm Text for confirm button.
     * @param {String} cancel Text for cancel button.
     */
    var ActionSelector = function(title, message, actions, confirm, cancel) {
        var self = this;

        EventBase.prototype.constructor.apply(this, []);
        self._title = title;
        self._message = message;
        self._actions = actions;
        self._confirm = confirm;
        self._cancel = cancel;
        self._selectedValue = null;
        self._reset();
    };

    ActionSelector.prototype = Object.create(EventBase.prototype);

    /** @type {String} The value that was selected. */
    ActionSelector.prototype._selectedValue = null;
    /** @type {Dialogue} The reference to the dialogue. */
    ActionSelector.prototype._popup = null;
    /** @type {String} The title of popup. */
    ActionSelector.prototype._title = null;
    /** @type {String} The message in popup. */
    ActionSelector.prototype._message = null;
    /** @type {object} The information for radion buttons. */
    ActionSelector.prototype._actions = null;
    /** @type {String} The text for confirm button. */
    ActionSelector.prototype._confirm = null;
    /** @type {String} The text for cancel button. */
    ActionSelector.prototype._cancel = null;

    /**
     * Hook to executed after the view is rendered.
     *
     * @method _afterRender
     */
    ActionSelector.prototype._afterRender = function() {
        var self = this;

        // Confirm button is disabled until a choice is done.
        self._find('[data-action="action-selector-confirm"]').attr('disabled', 'disabled');

        // Add listener for radio buttons change.
        self._find('[data-region="action-selector-radio-buttons"]').change(function() {
            self._selectedValue = $("input[type='radio']:checked").val();
            self._find('[data-action="action-selector-confirm"]').removeAttr('disabled');
            self._refresh.bind(self);
        }.bind(self));

        // Add listener for cancel.
        self._find('[data-action="action-selector-cancel"]').click(function(e) {
            e.preventDefault();
            self.close();
        }.bind(self));

        // Add listener for confirm.
        self._find('[data-action="action-selector-confirm"]').click(function(e) {
            e.preventDefault();
            if (!self._selectedValue.length) {
                return;
            }
            self._trigger('save', { action: self._selectedValue });
            self.close();
        }.bind(self));
    };

    /**
     * Close the dialogue.
     *
     * @method close
     */
    ActionSelector.prototype.close = function() {
        var self = this;
        self._popup.close();
        self._reset();
    };

    /**
     * Opens the action selector.
     *
     * @method display
     * @return {Promise}
     */
    ActionSelector.prototype.display = function() {
        var self = this;
        return self._render().then(function(html) {
            self._popup = new Dialogue(
                self._title,
                html,
                self._afterRender.bind(self)
            );
        }.bind(self)).fail(Notification.exception);
    };

    /**
     * Find a node in the dialogue.
     *
     * @param {String} selector
     * @method _find
     */
    ActionSelector.prototype._find = function(selector) {
        return $(this._popup.getContent()).find(selector);
    };

    /**
     * Refresh the view.
     *
     * @method _refresh
     * @return {Promise}
     */
    ActionSelector.prototype._refresh = function() {
        var self = this;
        return self._render().then(function(html) {
            self._find('[data-region="action-selector"]').replaceWith(html);
            self._afterRender();
        }.bind(self));
    };

    /**
     * Render the dialogue.
     *
     * @method _render
     * @return {Promise}
     */
    ActionSelector.prototype._render = function() {
        var self = this;
        var choices = [];
        for (var i in self._actions) {
            choices.push(self._actions[i]);
        }
        var content = {'message': self._message, 'choices' : choices,
            'confirm' : self._confirm, 'cancel' : self._cancel};

        return Templates.render('tool_lp/action_selector', content);
    };

    /**
     * Reset the dialogue properties.
     *
     * This does not reset everything, just enough to reset the UI.
     *
     * @method _reset
     */
    ActionSelector.prototype._reset = function() {
        this._popup = null;
        this._selectedValue = '';
    };

    return /** @alias module:tool_lp/actionselector */ ActionSelector;

});
