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
    GROUPS = '.atto_group',
    ROWS = '.atto_toolbar_row';

Y.namespace('M.atto_collapse').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {
    initializer: function() {
        var toolbarGroupCount = Y.Object.size(this.get('host').get('plugins'));
        if (toolbarGroupCount <= 1 + parseInt(this.get(ATTRSHOWGROUPS), 10)) {
            Y.log("There are not enough groups to require toggling - not adding the button",
                'debug', 'moodle-atto_collapse');
            return;
        }

        if (this.toolbar.all(GROUPS).size() > this.get(ATTRSHOWGROUPS)) {
            Y.log("The collapse plugin is shown after it's cut-off - not adding the button",
                'debug', 'moodle-atto_collapse');
            return;
        }

        var button = this.addButton({
            icon: 'icon',
            iconComponent: PLUGINNAME,
            callback: this._toggle
        });

        // Perform a toggle after all plugins have been loaded for the first time.
        this.get('host').on('pluginsloaded', function(e, button) {
            // Add 2 rows in the toolbar.
            var toolbarRows = [
                Y.Node.create('<div class="atto_toolbar_row" role="group"></div>'),
                Y.Node.create('<div class="atto_toolbar_row" role="group" aria-label="' +
                    M.util.get_string('youareonsecondrow', PLUGINNAME) +
                    '" tabindex="-1"></div>'),
            ];
            this.toolbar.appendChild(toolbarRows[0]).insert(toolbarRows[1], 'after');

            // Split toolbar buttons between the 2 rows created above.
            var buttonGroups = this.toolbar.all(GROUPS);
            buttonGroups.slice(0, this.get(ATTRSHOWGROUPS)).each(function(buttonGroup) {
                toolbarRows[0].appendChild(buttonGroup);
            });
            buttonGroups.slice(this.get(ATTRSHOWGROUPS)).each(function(buttonGroup) {
                toolbarRows[1].appendChild(buttonGroup);
            });

            this._setVisibility(button);
            button.setAttribute('aria-expanded', 'false');
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
            this.highlightButtons(COLLAPSE);
            this._setVisibility(button, true);
            this.toolbar.all(ROWS).item(1).focus();
        } else {
            this.unHighlightButtons(COLLAPSE);
            this._setVisibility(button);
            this.buttons[this.name].focus();
        }
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
        var secondaryRow = this.toolbar.all(ROWS).item(1);

        if (visibility) {
            button.set('title', M.util.get_string('showfewer', PLUGINNAME));
            secondaryRow.show();
            button.setData(COLLAPSED, false);
            button.setAttribute('aria-expanded', 'true');
        } else {
            button.set('title', M.util.get_string('showmore', PLUGINNAME));
            secondaryRow.hide();
            button.setData(COLLAPSED, true);
            button.setAttribute('aria-expanded', 'false');
        }

        // We don't want to have both aria-pressed and aria-expanded set. So we remove aria-pressed here.
        button.removeAttribute('aria-pressed');
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
