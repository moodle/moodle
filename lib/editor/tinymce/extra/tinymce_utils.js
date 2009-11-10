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
 * TinyMCE helper javascript functions
 *
 * @package    moodlecore
 * @subpackage editor
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function mce_init_editor(elementid, params) {
	tinyMCE.init(params);
}

function mce_toggleEditor(id) {
    tinyMCE.execCommand('mceToggleEditor',false,id);
}

function mce_saveOnSubmit(id) {
    var prevOnSubmit = document.getElementById(id).form.onsubmit;
    document.getElementById(id).form.onsubmit = function() {
        tinyMCE.triggerSave();
        var ret = true;
        if (prevOnSubmit != undefined) {
          if (prevOnSubmit()) {
            ret = true;
            prevOnSubmit = null;
          } else {
            ret = false;
          }
        }
        return ret;
    };
}

function mce_moodlefilemanager(field_name, url, type, win) {
    var client_id = id2clientid[tinyMCE.selectedInstance.editorId];
    var picker = document.createElement('DIV');
    picker.className = "file-picker";
    picker.id = 'file-picker-'+client_id;
    document.body.appendChild(picker);
    var el = win.document.getElementById(field_name);
    open_filepicker(client_id, {"env":"editor","target":el,"filetype":type, "savepath":"/"});
}
