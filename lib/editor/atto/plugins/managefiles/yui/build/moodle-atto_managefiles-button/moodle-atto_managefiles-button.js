YUI.add('moodle-atto_managefiles-button', function (Y, NAME) {

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
 * Atto text editor managefiles plugin.
 *
 * @package    atto_managefiles
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

M.atto_managefiles = M.atto_managefiles || {

    /**
     * The ID of the current editor.
     *
     * @type {String}
     */
    currentElementId: null,

    /**
     * The dialogue to select a character.
     *
     * @type {M.core.dialogue}
     */
    dialogue: null,

    /**
     * The parameters for each instance of Atto.
     *
     * @type {Object} Where keys are the element ID of each editor.
     */
    params: {},

    /**
     * Init.
     *
     * @param {Object} params
     *
     * @return {Void}
     */
    init : function(params) {

        if (params.disabled) {
            return;
        }

        // Get the itemid from the filepicker options.
        if (!params.area.itemid
                && M.editor_atto.filepickeroptions[params.elementid]
                && M.editor_atto.filepickeroptions[params.elementid].image
                && M.editor_atto.filepickeroptions[params.elementid].image.itemid) {
            params.area.itemid = M.editor_atto.filepickeroptions[params.elementid].image.itemid;
        } else {
            console.log('Plugin managefiles not available because itemid is missing.');
            return;
        }
        M.atto_managefiles.params[params.elementid] = params;

        var click = function(e, elementid) {
            var dialogue,
                iframe;

            e.preventDefault();
            M.atto_managefiles.currentElementId = elementid;

            // Initialising the dialogue.
            if (!M.atto_managefiles.dialogue) {
                dialogue = new M.core.dialogue({
                    visible: false,
                    modal: true,
                    close: true,
                    draggable: true,
                    width: '800px'
                });

                // Setting up the basics of the dialogue.
                dialogue.set('headerContent', M.util.get_string('managefiles', 'atto_managefiles'));
                M.atto_managefiles.dialogue = dialogue;
                dialogue.render();
                dialogue.centerDialogue();
            } else {
                dialogue = M.atto_managefiles.dialogue;
            }


            iframe = Y.Node.create('<iframe></iframe>');
            // We set the height here because otherwise it is really small. That might not look
            // very nice on mobile devices, but we considered that enough for now.
            iframe.setStyle('height', '700px');
            iframe.setStyle('border', 'none');
            iframe.setStyle('width', '100%');
            iframe.setAttribute('src', M.atto_managefiles.getIframeURL());

            dialogue.set('bodyContent', iframe);
            dialogue.show();

            // Clean the YUI ids from the HTML.
            M.editor_atto.text_updated(elementid);
        };

        // Add toolbar button.
        var iconurl = M.util.image_url('e/manage_files', 'core');
        M.editor_atto.add_toolbar_button(params.elementid, 'managefiles', iconurl, params.group, click);
    },

    /**
     * Returns the URL to the file manager.
     *
     * @return {String} URL
     */
    getIframeURL: function() {
        var key,
            params,
            url = '';

        url = M.cfg.wwwroot + '/lib/editor/atto/plugins/managefiles/manage.php?';
        params = M.atto_managefiles.params[M.atto_managefiles.currentElementId];
        for (key in params.area) {
            url += encodeURIComponent(key) + '=' + encodeURIComponent(params.area[key]) + '&';
        }

        return url;
    },

    /**
     * Return the list of files used in the area.
     *
     * @return {Object} List of files used where the keys are the name of the files, the value is true.
     */
    getUsedFiles: function() {
        var elementid = M.atto_managefiles.currentElementId,
            editableNode = M.editor_atto.get_editable_node(elementid),
            content = editableNode.getHTML(),
            params = M.atto_managefiles.params[elementid],
            baseUrl = M.cfg.wwwroot + '/draftfile.php/' + params.usercontext + '/user/draft/' + params.area.itemid + '/',
            pattern = new RegExp(baseUrl.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&') + "(.+?)[\\?\"']", 'gm'),
            filename = '',
            match = '',
            usedFiles = {};

        while ((match = pattern.exec(content)) !== null) {
            filename = unescape(match[1]);
            usedFiles[filename] = true;
        }

        return usedFiles;
    }

};


}, '@VERSION@', {"requires": ["node"]});
