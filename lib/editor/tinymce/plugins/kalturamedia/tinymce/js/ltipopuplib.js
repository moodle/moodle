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
 * Kaltura media ltippopup javascript file.  This code is based off of the word done for the Moodle media plug-in.
 * @see editor/tinymce/plugins/moodlemedia.
 *
 * @package    tinymce_kalturamedia
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

var ed, url;

if (url = tinyMCEPopup.getParam("media_external_list_url")) {
    document.write('<script language="javascript" type="text/javascript" src="'+tinyMCEPopup.editor.documentBaseURI.toAbsolute(url)+'"></script>');
}

/**
 * Initialization function to set a global to the current tinyMCE popup editor instance; and to set the editor context hidden element.
 */
function init() {
    ed = tinyMCEPopup.editor;
    var contextid = ed.getParam('lti_launch_context_id');
    document.getElementById('lti_launch_context_id').value = contextid;
}

/**
 * Insert the selected media into the editor.
 */
function insertMedia() {
    var form = document.forms[0];
    var kafuri = form.kafuri.value;
    var sourceUrl = form.source.value;

    var url = removeProtocolFromUrl(kafuri);
    sourceUrl = removeProtocolFromUrl(sourceUrl);
    url = sourceUrl.replace(kafuri, url);
    var parser = document.createElement('a');
    parser.href = form.source.value;
    url += parser.search;

    var content = '<a href="http://'+url+'">tinymce-kalturamedia-embed||'+form.video_title.value+'||'+form.width.value+'||'+form.height.value+'</a>';
    ed.execCommand('mceInsertContent', false, content);
    ed.newWindow.close();
}

function removeProtocolFromUrl(fullUrl) {
    return fullUrl.replace(/^https?:\/\//,'');
}

tinyMCEPopup.onInit.add(init);
