(function() {
    tinymce.create('tinymce.plugins.CtrlHelpPlugin', {

        init : function(ed, url) {
            ed.onContextMenu.add(function(ed, e) {
                var m = ed.plugins.contextmenu._menu;
                m.add({title : 'ctrlhelp.desc', icon : '', cmd : ''});
            });
        },

        getInfo : function() {
            return {
                longname :  'Moodle CTRL + right click helper plugin',
                author :    'Petr Skoda',
                authorurl : 'http://skodak.org/',
                infourl :   'http://moodle.org',
                version :   '1.0'
            };
        }
    });

    tinymce.PluginManager.add('ctrlhelp', tinymce.plugins.CtrlHelpPlugin);
})();
