/**
 * TinyMCE plugin MoodleEmoticon - provides GUI to insert emoticon images.
 *
 * Based on the example plugin (c) 2009 Moxiecode Systems AB.
 *
 * @author  David Mudrak <david@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

(function() {
    tinymce.create('tinymce.plugins.MoodleEmoticon', {

        /**
         * Holds the list of emoticons provided by emoticon_manager.
         *
         * @private
         */
        _emoticons : {},

        /**
         * Initializes the plugin, this will be executed after the plugin has been created.
         * This call is done before the editor instance has finished it's initialization so use the onInit event
         * of the editor instance to intercept that event.
         *
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
        init : function(ed, url) {
            // Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceMoodleEmoticon');
            ed.addCommand('mceMoodleEmoticon', function() {
                lang = ed.getParam('language');
                ed.windowManager.open({
                    file : ed.getParam("moodle_plugin_base") + 'moodleemoticon/dialog.php?lang=' + lang ,
                    width : 250 + parseInt(ed.getLang('moodleemoticon.delta_width', 0)),
                    height : 400 + parseInt(ed.getLang('moodleemoticon.delta_height', 0)),
                    inline : 1
                }, {
                    plugin_url : url // Plugin absolute URL
                });
            });

            // Add an observer to the onInit event to convert emoticon texts to images.
            ed.onInit.add(function(ed) {
                var data = ed.getContent();
                this._emoticons = tinymce.util.JSON.parse(ed.getParam('moodleemoticon_emoticons'));
                for (var emotxt in this._emoticons) {
                    // escape the metacharacters so we can use it as regexp
                    search = emotxt.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, '\\$&');
                    // convert to regexp
                    search = new RegExp(search, 'g');
                    // and replace all occurrences of it with the image
                    data = data.replace(search, this._emoticons[emotxt]);
                }
                ed.setContent(data);
            });

            // Add an observer to the onPreProcess event to convert emoticon images to texts.
            ed.onPreProcess.add(function(ed, o) {
                if (o.save) {
                    tinymce.each(ed.dom.select('img.emoticon', o.node), function(image) {
                        var emoticontxt = '';
                        var matches = /^emoticon emoticon-index-([0-9]+)$/.exec(image.className);
                        if (matches.length != 2) {
                            // this is not valid emoticon image inserted via dialog
                            // return true so that each() does not halt
                            return true;
                        }
                        var index = matches[1];
                        var search = new RegExp('class="emoticon emoticon-index-'.concat(index, '"'));
                        for (var emotxt in this._emoticons) {
                            if (search.test(this._emoticons[emotxt])) {
                                emoticontxt = emotxt;
                                break;
                            }
                        }
                        if (emoticontxt) {
                            ed.dom.setOuterHTML(image, emoticontxt);
                        }
                    }, this);
                }
            });

            // Register moodleemoticon button.
            ed.addButton('moodleemoticon', {
                title : 'moodleemoticon.desc',
                cmd : 'mceMoodleEmoticon',
                image : url + '/img/moodleemoticon.png'
            });
        },

        /**
         * Creates control instances based in the incomming name. This method is normally not
         * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
         * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
         * method can be used to create those.
         *
         * @param {String} n Name of the control to create.
         * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
         * @return {tinymce.ui.Control} New control instance or null if no control was created.
         */
        createControl : function(n, cm) {
            return null;
        },

        /**
         * Returns information about the plugin as a name/value array.
         * The current keys are longname, author, authorurl, infourl and version.
         *
         * @return {Object} Name/value array containing information about the plugin.
         */
        getInfo : function() {
            return {
                longname : 'Moodle Emoticon plugin',
                author : 'David Mudrak',
                authorurl : 'http://mudrak.name',
                infourl : 'http://moodle.org',
                version : "1.0"
            };
        }
    });

    // Register plugin.
    tinymce.PluginManager.add('moodleemoticon', tinymce.plugins.MoodleEmoticon);
})();
