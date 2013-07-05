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
 * Plugin for Moodle 'wrap' button.
 *
 * @package   tinymce_wrap
 * @copyright 2013 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
(function() {
    tinymce.create('tinymce.ui.Wrap:tinymce.ui.Control', {
        /**
         * Constructor for the tinymce.Wrap class.
         */
        Wrap : function(id, s) {
            this.parent(id, s);
            this.groupEndClass = 'mceToolbarEnd';
            this.groupStartClass = 'mceToolbarStart';
            this.wrapClass = 'mceWrap';
            this.setDisabled(true);
        },

        /**
         * Returns the HTML for this control. This control actually ends the current td
         * container and opens a new one so that the containers can be styled with CSS
         * to wrap at certain screen widths.
         * @return string HTML
         */
        renderHTML : function() {
            var separator = tinymce.DOM.createHTML('span', {role : 'separator',
                                                            'aria-orientation' : 'vertical',
                                                            tabindex : '-1'});
            return '</td>' +
                   '<td style="position: relative" class="' + this.groupEndClass + '">' + separator + '</td>' +
                   '<td style="position: relative" class="' + this.groupStartClass + ' ' + this.wrapClass + '">' + separator + '</td>';
        },


    });

    tinymce.create('tinymce.plugins.wrapPlugin', {
        /**
         * Returns a new instance of this control, in this case a custom Wrap class.
         *
         * @param string name - The name of the control to create. Return false if we can't create this control type.
         * @param tinymce.ControlManager cc - Tinymce control manager class.
         * @return mixed - false or the new control
         */
        createControl : function(name, cc) {
            if (name === "wrap" || name === "!") {
                return new tinymce.ui.Wrap();
            }
            return false;
        },

        /**
         * Returns information about the plugin as a name/value array.
         * The current keys are longname, author, authorurl, infourl and version.
         *
         * @return {Object} Name/value array containing information about the plugin.
         */
        getInfo : function() {
            return {
                longname : 'wrap plugin',
                author : 'Damyon Wiese',
                authorurl : 'http://moodle.com/hq',
                infourl : 'http://docs.moodle.org/en/TinyMCE',
                version : "1.0"
            };
        }
    });

    // Register plugin.
    tinymce.PluginManager.add('wrap', tinymce.plugins.wrapPlugin);
})();
