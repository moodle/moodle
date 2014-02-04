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
 * Atto text editor noautolink plugin.
 *
 * @package    editor-atto
 * @copyright  2014 Andrew Davis  <andrew@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
M.atto_noautolink = M.atto_noautolink || {
    /**
     * Initialise the plugin - Allow the user to mark text to be ignored by the glossary autolink filter.
     *
     * @method init
     * @param {Object} Initialisation settings for this plugin
     * @param {string} params.elementid The editor identifier
     * @param {string} params.group The button group
     */
    init: function(params) {
        var click = function(e, elementid) {
            e.preventDefault();
            if (!M.editor_atto.is_active(elementid)) {
                M.editor_atto.focus(elementid);
            }

            M.editor_atto.toggle_inline_selection_class(elementid, ['nolink']);

            // Clean the YUI ids from the HTML.
            M.editor_atto.text_updated(elementid);
        };

        var iconurl = M.util.image_url('e/prevent_autolink', 'core');
        M.editor_atto.add_toolbar_button(params.elementid, 'noautolink', iconurl, params.group, click);
    }
};
