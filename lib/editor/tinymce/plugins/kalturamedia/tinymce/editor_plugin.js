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
 * Kaltura media javascript file.
 *
 * @package    tinymce_kalturamedia
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

(function() {
    tinymce.create('tinymce.plugins.kalturamediaPlugin', {
        /**
         * Initializes the plugin, this will be executed after the plugin has been created.
         * This call is done before the editor instance has finished it's initialization so use the onInit event
         * of the editor instance to intercept that event.
         *
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
        init : function(ed, url) {
            lang = tinyMCE.activeEditor.getParam('language');

            // Register the command to open a popup window
            ed.addCommand('mcekalturamedia', function() {
                ed.windowManager.open({
                    file : ed.getParam("moodle_plugin_base")+'kalturamedia/tinymce/ltibrowse.php?lang=' + lang,
                    width : 1128 + parseInt(ed.getLang('media.delta_width', 0)),
                    height : 583 + parseInt(ed.getLang('media.delta_height', 0)),
                    inline : 1
                }, {
                    plugin_url : url
                });
            });

            // Register kalturaMedia button
            ed.addButton('kalturamedia', {
                title : 'kalturamedia.desc',
                cmd : 'mcekalturamedia',
                image : url+'/img/icon.gif'
            });
        },

        /**
         * Returns information about the plugin as a name/value array.
         * The current keys are longname, author, authorurl, infourl and version.
         *
         * @return {Object} Name/value array containing information about the plugin.
         */
        getInfo : function() {
            return {
                longname : 'Kaltura media plugin',
                author : 'Remote-Learner.net Inc',
                authorurl : 'http://remote-learner.net',
                infourl : 'http://docs.moodle.org/en/TinyMCE',
                version : "1.0"
            };
        }
    });

    // Register plugin.
    tinymce.PluginManager.add('kalturamedia', tinymce.plugins.kalturamediaPlugin);
})();
