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
 * Kaltura media ltidialog javascript file. This code is based off of the work done for tinymce_kalturamedia plugin.
 * @see editor/tinymce/plugins/kalturamedia.
 *
 * @module      tiny_kalturamedia/plugin
 * @copyright   2023 Roi Levi <roi.levi@kaltura.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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

    var content = '<a href="http://'+url+'">tinymce-kalturamedia-embed||'+
    form.video_title.value+'||'+form.width.value+'||'+form.height.value+'</a>';
    // send post message to insert content into the editor and close the dialog
    window.parent.postMessage({
       mceAction: 'insertContent',
       content: content
       },'*');
    window.parent.postMessage({
       mceAction: 'close',
       },'*');
}

function removeProtocolFromUrl(fullUrl) {
    return fullUrl.replace(/^https?:\/\//,'');
}
