YUI.add('moodle-atto_collapse-button', function (Y, NAME) {

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

/*
 * @package    atto_collapse
 * @copyright  2013 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto_collapse-button
 */

/**
 * Atto text editor collapse plugin.
 *
 * @namespace M.atto_collapse
 * @class button
 * @extends M.editor_atto.EditorPlugin
 */

var PLUGINNAME = 'atto_collapse',
    ATTRSHOWGROUPS = 'showgroups',
    COLLAPSE = 'collapse',
    COLLAPSED = 'collapsed',
    GROUPS = '.atto_group';

Y.namespace('M.atto_collapse').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {
    initializer: function() {
        var toolbarGroupCount = Y.Object.size(this.get('host').get('plugins'));
        if (toolbarGroupCount <= 1 + parseInt(this.get(ATTRSHOWGROUPS), 10)) {
            return;
        }

        if (this.toolbar.all(GROUPS).size() > this.get(ATTRSHOWGROUPS)) {
            return;
        }

        var button = this.addButton({
            icon: M.util.image_url('icon', PLUGINNAME),
            callback: this._toggle
        });

        // Perform a toggle after all plugins have been loaded for the first time.
        this.get('host').on('pluginsloaded', function(e, button) {
            this._setVisibility(button);

            // Set the toolbar to break after the initial those displayed by default.
            var firstGroup = this.toolbar.all(GROUPS).item(this.get(ATTRSHOWGROUPS));
            firstGroup.insert('<div class="toolbarbreak"></div>', 'before');
        }, this, button);
    },

    /**
     * Toggle the visibility of the extra groups in the toolbar.
     *
     * @method _toggle
     * @param {EventFacade} e
     * @private
     */
    _toggle: function(e) {
        e.preventDefault();
        var button = this.buttons[COLLAPSE];

        if (button.getData(COLLAPSED)) {
            this._setVisibility(button, true);
        } else {
            this._setVisibility(button);
        }

        this.buttons[this.name].focus();
    },

    /**
     * Set the visibility of the toolbar groups.
     *
     * @method _setVisibility
     * @param {Node} button The collapse button
     * @param {Booelan} visibility Whether the groups should be made visible
     * @private
     */
    _setVisibility: function(button, visibility) {
        var groups = this.toolbar.all(GROUPS).slice(this.get(ATTRSHOWGROUPS));

        if (visibility) {
            button.set('title', M.util.get_string('showfewer', PLUGINNAME));
            groups.show();
            button.setData(COLLAPSED, false);
        } else {
            button.set('title', M.util.get_string('showmore', PLUGINNAME));
            groups.hide();
            button.setData(COLLAPSED, true);
        }

    }
}, {
    ATTRS: {
        /**
         * How many groups to show when collapsed.
         *
         * @attribute showgroups
         * @type Number
         * @default 3
         */
        showgroups: {
            value: 3
        }
    }
});


}, '@VERSION@', {"requires": ["moodle-editor_atto-plugin"]});
