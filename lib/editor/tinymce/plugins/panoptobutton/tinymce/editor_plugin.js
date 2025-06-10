(function () {
    // Load plugin specific language pack.
    tinymce.PluginManager.requireLangPack('panoptobutton');

    // Initialize plugin.
    tinymce.create('tinymce.plugins.AddPanoptoButton', {
        // ... ed: editor instance plugin is being called from.
        // ... url: absolute url of plugin.
        init: function (ed, url) {
            // Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample').
            ed.addCommand('mcePanopto', function () {
                var baseUrl = ed.getParam('moodle_plugin_base') + 'panoptobutton/tinymce/panoptowrapper.html#',
                    instanceParam = ed.getParam('instancename'),
                    instanceString = '';

                baseUrl += 'unprovisionerror=' + encodeURIComponent(ed.getParam('unprovisionederror'));
                
                if (instanceParam) {
                    instanceString = '&instance=' + instanceParam;
                }

                if (ed.getParam('panoptoservername') != null &&
                    ed.getParam('panoptoid') != null) {
                    baseUrl += '&servername=' + ed.getParam('panoptoservername') +
                               instanceString + 
                               '&panoptoid=' + ed.getParam('panoptoid');
                }

                ed.windowManager.open({
                    file: baseUrl,
                    width: 1024,
                    height: 720,
                    inline: 1
                }, {
                    plugin_url: url, // Plugin absolute URL.
                    filepath: ed.getParam('moodle_plugin_base') // Custom argument.
                });
            });

            // Register example button.
            ed.addButton('panoptobutton', {
                title: ed.getParam('panoptobuttondescription'),
                cmd: 'mcePanopto',
                image: url + '/img/panopto_logo_globe.png'
            });

            // Add a node change handler, selects the button in the UI when a image is selected.
            ed.onNodeChange.add(function (ed, cm, n) {
                cm.setActive('panoptobutton', n.nodeName == 'IMG');
            });
        },

        // Required function not utilized by this plugin.
        createControl: function (n, cm) {
            return null;
        },

        // Returns creator and version info about plugin.
        getInfo: function () {
            return {
                longname: ed.getParam('panoptobuttonlongdescription'),
                author: 'Panopto',
                version: "1.0"
            };
        }
    });

    // Register plugin.
    tinymce.PluginManager.add('panoptobutton', tinymce.plugins.AddPanoptoButton);
})();
