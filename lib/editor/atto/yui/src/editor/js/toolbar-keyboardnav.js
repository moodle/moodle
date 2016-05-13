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
        this._wrapper.delegate('focus',
                function(e) {
                    this._setTabFocus(e.currentTarget);
                }, '.' + CSS.TOOLBAR + ' button', this);

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

        // On cursor moves we loops through the buttons.
        var buttons = this.toolbar.all('button'),
            direction = 1,
            button,
            current = e.target.ancestor('button', true);

        if (e.keyCode === 37) {
            // Moving left so reverse the direction.
            direction = -1;
        }

        button = this._findFirstFocusable(buttons, current, direction);
        if (button) {
            button.focus();
            this._setTabFocus(button);
        } else {
            Y.log("Unable to find a button to focus on", 'debug', LOGNAME);
        }
    },

    /**
     * Find the first focusable button.
     *
     * @param {NodeList} buttons A list of nodes.
     * @param {Node} startAt The node in the list to start the search from.
     * @param {Number} direction The direction in which to search (1 or -1).
     * @return {Node | Undefined} The Node or undefined.
     * @method _findFirstFocusable
     * @private
     */
    _findFirstFocusable: function(buttons, startAt, direction) {
        var checkCount = 0,
            group,
            candidate,
            button,
            index;

        // Determine which button to start the search from.
        index = buttons.indexOf(startAt);
        if (index < -1) {
            Y.log("Unable to find the button in the list of buttons", 'debug', LOGNAME);
            index = 0;
        }

        // Try to find the next.
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

        return button;
    },

    /**
     * Check the tab focus.
     *
     * When we disable or hide a button, we should call this method to ensure that the
     * focus is not currently set on an inaccessible button, otherwise tabbing to the toolbar
     * would be impossible.
     *
     * @method checkTabFocus
     * @chainable
     */
    checkTabFocus: function() {
        if (this._tabFocus) {
            if (this._tabFocus.hasAttribute('disabled') || this._tabFocus.hasAttribute('hidden')
                    || this._tabFocus.ancestor('.atto_group').hasAttribute('hidden')) {
                // Find first available button.
                var button = this._findFirstFocusable(this.toolbar.all('button'), this._tabFocus, -1);
                if (button) {
                    if (this._tabFocus.compareTo(document.activeElement)) {
                        // We should also move the focus, because the inaccessible button also has the focus.
                        button.focus();
                    }
                    this._setTabFocus(button);
                }
            }
        }
        return this;
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
