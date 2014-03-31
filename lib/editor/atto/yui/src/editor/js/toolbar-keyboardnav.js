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
 * @module moodle-editor_atto-editor
 * @submodule toolbarnav
 */

/**
 * Toolbar Navigation functions for the Atto editor.
 *
 * See {{#crossLink "M.editor_atto.Editor"}}{{/crossLink}} for details.
 *
 * @namespace M.editor_atto
 * @class EditorToolbarNav
 */

function EditorToolbarNav() {}

EditorToolbarNav.ATTRS= {
};

EditorToolbarNav.prototype = {
    /**
     * The current focal point for tabbing.
     *
     * @property _tabFocus
     * @type Node
     * @default null
     * @private
     */
    _tabFocus: null,

    /**
     * Set up the watchers for toolbar navigation.
     *
     * @method setupToolbarNavigation
     * @chainable
     */
    setupToolbarNavigation: function() {
        // Listen for Arrow left and Arrow right keys.
        this._wrapper.delegate('key',
                this.toolbarKeyboardNavigation,
                'down:37,39',
                '.' + CSS.TOOLBAR,
                this);

        return this;
    },

    /**
     * Implement arrow key navigation for the buttons in the toolbar.
     *
     * @method toolbarKeyboardNavigation
     * @param {EventFacade} e - the keyboard event.
     */
    toolbarKeyboardNavigation: function(e) {
        // Prevent the default browser behaviour.
        e.preventDefault();

        var buttons = this.toolbar.all('button');

        // On cursor moves we loops through the buttons.
        var found = false,
            index = 0,
            direction = 1,
            checkCount = 0,
            group,
            candidate,
            button,
            current = e.target.ancestor('button', true);

        // Determine which button is currently selected.
        while (!found && index < buttons.size()) {
            if (buttons.item(index) === current) {
                found = true;
            } else {
                index++;
            }
        }

        if (!found) {
            Y.log("Unable to find this button in the list of buttons", 'debug', LOGNAME);
            return;
        }

        if (e.keyCode === 37) {
            // Moving left so reverse the direction.
            direction = -1;
        }

        // Try to find the next
        while (checkCount < buttons.size()) {
            index += direction;
            if (index < 0) {
                index = buttons.size() - 1;
            } else if (index >= buttons.size()) {
                // Handle wrapping.
                index = 0;
            }

            candidate = buttons.item(index);

            // Add a counter to ensure we don't get stuck in a loop if there's only one visible menu item.
            checkCount++;

            // Loop while:
            // * we haven't checked every button;
            // * the button is hidden or disabled;
            // * the group is hidden.
            if (candidate.hasAttribute('hidden') || candidate.hasAttribute('disabled')) {
                continue;
            }
            group = candidate.ancestor('.atto_group');
            if (group.hasAttribute('hidden')) {
                continue;
            }

            button = candidate;
            break;
        }

        if (button) {
            button.focus();
            this._setTabFocus(button);
        } else {
            Y.log("Unable to find a button to focus on", 'debug', LOGNAME);
        }
    },

    /**
     * Sets tab focus for the toolbar to the specified Node.
     *
     * @method _setTabFocus
     * @param {Node} button The node that focus should now be set to
     * @chainable
     * @private
     */
    _setTabFocus: function(button) {
        if (this._tabFocus) {
            // Unset the previous entry.
            this._tabFocus.setAttribute('tabindex', '-1');
        }

        // Set up the new entry.
        this._tabFocus = button;
        this._tabFocus.setAttribute('tabindex', 0);

        // And update the activedescendant to point at the currently selected button.
        this.toolbar.setAttribute('aria-activedescendant', this._tabFocus.generateID());

        return this;
    }
};

Y.Base.mix(Y.M.editor_atto.Editor, [EditorToolbarNav]);
