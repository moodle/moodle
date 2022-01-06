/**
 * TinyMCE plugin ManageFiles - provides UI to edit files embedded in the text editor.
 *
 * @author  Marina Glancy
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

(function() {
    tinymce.create('tinymce.plugins.MoodleManageFiles', {
        /**
         * Initializes the plugin, this will be executed after the plugin has been created.
         * This call is done before the editor instance has finished it's initialization so use the onInit event
         * of the editor instance to intercept that event.
         *
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
        init : function(ed, url) {
            ed.addCommand('mceForceRepaint', function() {
                var root = ed.dom.getRoot();
                items = root.getElementsByTagName("img");
                for (var i = 0; i < items.length; i++) {
                    src = items[i].getAttribute('src').replace(/\?\d+$/, '');
                    items[i].setAttribute('src', src+'?'+(new Date().getTime()))
                }
                ed.execCommand('mceRepaint');
                ed.focus();
            });

            ed.addCommand('mceMaximizeWindow', function(w) {
                // This function duplicates the TinyMCE windowManager code when 'maximize' button is pressed.
                var vp = ed.dom.getViewPort(),
                    id = w.id;
                // Reduce viewport size to avoid scrollbars
                vp.w -= 2;
                vp.h -= 2;

                w.oldPos = w.element.getXY();
                w.oldSize = w.element.getSize();

                w.element.moveTo(vp.x, vp.y);
                w.element.resizeTo(vp.w, vp.h);
                ed.dom.setStyles(id + '_ifr', {width : vp.w - w.deltaWidth, height : vp.h - w.deltaHeight});
                ed.dom.addClass(id + '_wrapper', 'mceMaximized');
            });

            ed.addCommand('mceManageFiles', function() {
                var managefiles = ed.getParam('managefiles', {}), key, cnt = 0,
                    fileurl = ed.getParam("moodle_plugin_base") + 'managefiles/manage.php?';
                for (key in managefiles) {
                    fileurl += (cnt++ ? '&' : '') + encodeURIComponent(key) + "=" + encodeURIComponent(managefiles[key]) + "&";
                }
                var onClose = function() {
                   ed.windowManager.onClose.remove(onClose);
                   ed.execCommand('mceForceRepaint');
                };
                ed.windowManager.onClose.add(onClose);
                var vp = ed.dom.getViewPort(),
                        width = 900 + parseInt(ed.getLang('advimage.delta_width', 0)),
                        height = 600 + parseInt(ed.getLang('advimage.delta_height', 0)),
                        maximizedmode = (width >= vp.w - 2 || height >= vp.h - 2);
                if (maximizedmode) {
                    width = vp.w;
                    height = vp.h;
                }
                w = ed.windowManager.open({
                    file : fileurl ,
                    width : width,
                    height : height,
                    inline : 1
                }, {
                    plugin_url : url // Plugin absolute URL
                });
                if (maximizedmode) {
                    ed.execCommand('mceMaximizeWindow', w);
                }
            });

            ed.addCommand('mceManageFilesUsedFiles', function() {
                var managefiles = ed.getParam('managefiles', {}),
                    text = ed.dom.getRoot().innerHTML,
                    base = ed.getParam('document_base_url') + '/draftfile.php/' + managefiles['usercontext'] + '/user/draft/' + managefiles['itemid'] + '/',
                    patt = new RegExp(base.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&') + "(.+?)[\\?\"']", 'gm'),
                    arr = [], match, filename;
                while ((match = patt.exec(text)) !== null) {
                    filename = decodeURI(match[1]);
                    if (arr.indexOf(filename) === -1) {
                        arr[arr.length] = filename;
                    }
                }
                return arr;
            });

            var managefiles = ed.getParam('managefiles', {});
            // Get draft area id from filepicker options.
            if (!managefiles.itemid && M.editor_tinymce.filepicker_options
                    && M.editor_tinymce.filepicker_options[ed.id]
                    && M.editor_tinymce.filepicker_options[ed.id].image
                    && M.editor_tinymce.filepicker_options[ed.id].image.itemid) {
                managefiles.itemid = M.editor_tinymce.filepicker_options[ed.id].image.itemid;
                ed.settings['managefiles'].itemid = managefiles.itemid;
            }

            // Register buttons
            if (managefiles.itemid) {
                ed.addButton('managefiles', {
                    title : 'managefiles.desc',
                    cmd : 'mceManageFiles',
                    image : url + '/img/managefiles.png'
                });
            }
        },
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
                longname : 'Moodle Manage embedded files plugin',
                author : 'Marina Glancy',
                infourl : 'http://moodle.org',
                version : "1.0"
            };
        }
    });

    // Register plugin.
    tinymce.PluginManager.add('managefiles', tinymce.plugins.MoodleManageFiles);
})();
