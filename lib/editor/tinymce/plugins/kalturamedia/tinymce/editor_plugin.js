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

        panel: null,

        init : function(ed, url) {
            lang = tinyMCE.activeEditor.getParam('language');

            // Register the command to open a popup window
            ed.addCommand('mcekalturamedia', function() {

                var height = 580;
                var width = 1100;
                var panelHeight = height + 90;
                var panelWidth = width + 23 + 'px';
                width += 'px';

                if (Y.UA.ipod !== 0 || Y.UA.ipad !== 0 || Y.UA.iphone !== 0 || Y.UA.android !== 0 || Y.UA.mobile !== null) {
                    panelWidth = '90%';
                    width = '100%';
                }

                var iframe = "<iframe id='panelcontentframe' height='" + height + "px' width='" + width + "' src='"+ ed.getParam("moodle_plugin_base")+'kalturamedia/tinymce/ltibrowse.php?lang=' + lang +"'></iframe>";
                var panelbodycontent = iframe;
                
                if (Y.UA.ipod !== 0 || Y.UA.ipad !== 0 || Y.UA.iphone !== 0) {
                    // This outer div will constrain the iframe from overlapping over its content region on iOS devices.
                    panelbodycontent = "<div id='panelcontentframecontainer'>" + iframe + "</div>";
                }    

                var panel = new Y.Panel({
                    srcNode : Y.Node.create('<div id="dialog" />'),
                    headerContent : '',
                    bodyContent : panelbodycontent,
                    width : panelWidth,
                    height : panelHeight+"px",
                    zIndex : 6,
                    centered : true,
                    modal : true,
                    visible : false,
                    render : true,
                });
                
                panel.show();
                panel.getButton("close").detachAll().on("click", function() { panel.destroy(); });

                ed.myPanel = panel;

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
