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

/**
 * CSS Selectors
 *
 * @type {Object}
 */
var SELECTORS = {
    GROUPS: '.atto_group',
    BUTTON: '.atto_collapse_button'
};

/**
 * Atto text editor collapse plugin.
 *
 * @package    atto_collapse
 * @copyright  2013 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
M.atto_collapse = M.atto_collapse || {

    /**
     * How many groups to show when collapsed.
     *
     * @property showgroups
     * @type {Integer}
     * @default 3
     */
    showgroups : 3,

    /**
     * Init.
     *
     * @param {Object} params
     *
     * @return {Void}
     */
    init : function(params) {
        var click = function(e, elementid) {
            e.preventDefault();
            M.atto_collapse.toggle(elementid);
        };

        this.showgroups = params.showgroups;

        var iconurl = M.util.image_url('icon', 'atto_collapse');

        // Add the button to the toolbar.
        M.editor_atto.add_toolbar_button(params.elementid, 'collapse', iconurl, params.group, click);
    },

    /**
     * Either hide or show the extra groups in the toolbar.
     *
     * @param {String} elementid
     *
     * @return {Void}
     */
    toggle : function(elementid) {
        var toolbar = M.editor_atto.get_toolbar_node(elementid);
        var button = toolbar.one(SELECTORS.BUTTON);
        var groups = toolbar.all(SELECTORS.GROUPS).slice(this.showgroups);

        if (button.getData('collapsed')) {
            button.set('title', M.util.get_string('showmore', 'atto_collapse'));
            groups.show();
            button.setData('collapsed', false);
        } else {
            button.set('title', M.util.get_string('showless', 'atto_collapse'));
            groups.hide();
            button.setData('collapsed', true);
        }
    },

    /**
     * After init function called after all plugins init() has been run.
     *
     * @param {Object} params
     *
     * @return {Void}
     */
    after_init : function(params) {
        var toolbar = M.editor_atto.get_toolbar_node(params.elementid);
        var button = toolbar.one(SELECTORS.BUTTON);

        // Set the state to "not collapsed" (which is the state when the page loads).
        button.setData('collapsed', false);
        // Call toggle to change the state when the page loads to "collapsed".
        M.atto_collapse.toggle(params.elementid);
    }

};


}, '@VERSION@', {"requires": ["node"]});
