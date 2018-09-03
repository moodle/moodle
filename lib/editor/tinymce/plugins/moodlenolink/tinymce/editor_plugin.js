/**
 * @author Mathieu Petit-Clair
 */

(function() {
    // Do not load language pack in moodle plugins.

    tinymce.create('tinymce.plugins.moodlenolinkPlugin', {
        /**
         * Initializes the plugin, this will be executed after the plugin has been created.
         * This call is done before the editor instance has finished it's initialization so use the onInit event
         * of the editor instance to intercept that event.
         *
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
        init : function(ed, url) {
            // Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceMoodleNolink');
            ed.addCommand('mceMoodleNolink', function() {
                var n, p;

                n = ed.selection.getNode();
                p = ed.dom.getParent(n, function(t) {
                    return ed.dom.getAttrib(t, 'class') == 'nolink';
                });

                if (p) {
                    ed.dom.remove(p, true);
                } else {
                    ed.selection.setContent('<span class="nolink">' + ed.selection.getContent() + '</span>');
                }

            });

            // Register moodlenolink button
            ed.addButton('moodlenolink', {
                title : 'moodlenolink.desc',
                cmd : 'mceMoodleNolink',
                image : url + '/img/prevent_autolink.png'
            });

            // Add a node change handler, selects the button in the UI when a image is selected
            ed.onNodeChange.add(function(ed, cm, n) {
                var p, c;
                c = cm.get('moodlenolink');
                if (!c) {
                    // Button not used.
                    return;
                }
                p = ed.dom.getParent(n, 'SPAN');

                c.setActive(p && ed.dom.hasClass(p, 'nolink'));

                if (p && ed.dom.hasClass(p, 'nolink') || ed.selection.getContent()) {
                    c.setDisabled(false);
                } else {
                    c.setDisabled(true);
                }
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
                longname : 'moodlenolink plugin',
                author : 'Mathieu Petit-Clair',
                authorurl : 'http://moodle.com/hq',
                infourl : 'http://docs.moodle.org/en/TinyMCE',
                version : "1.0"
            };
        }
    });

    // Register plugin.
    tinymce.PluginManager.add('moodlenolink', tinymce.plugins.moodlenolinkPlugin);
})();
