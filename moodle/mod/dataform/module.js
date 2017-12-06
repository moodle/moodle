// This file is part of Moodle - http://moodle.org/.
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
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * @package mod-dataform
 * @copyright 2013 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * The Dataform has been developed as an enhanced counterpart
 * of Moodle's Database activity module (1.9.11+  (20110323)).
 * To the extent that Dataform code corresponds to Database code,
 * certain copyrights on the Database module may obtain.
 */

// Define a name space to call.
M.mod_dataform = M.mod_dataform || {};
M.mod_dataform.util = M.mod_dataform.util || {};


/**
 * insert the field tags into the textarea.
 * Used when editing a dataform view
 */
M.mod_dataform.util.init_tags_selector = function(Y, selectorname, editorname) {
    Y.use('node', function () {
        if (!Y.one('#id_' + editorname) || !Y.one('#id_' + selectorname)) {
            return;
        }

        Y.one('#id_' + selectorname).on('change', function (e) {
            var selector = e.target;
            var value = selector.get('options').item(selector.get('selectedIndex')).get('value');
            switch (value){
                case '9':
                    value = String.fromCharCode(9);
                    break;

                case '10':
                    value = String.fromCharCode(10);
                    break;
            }
            if (Y.one('#id_' + editorname).getStyle('display') != 'none') {
                editor = Y.one('#id_' + editorname).getDOMNode();
                insertAtCursor(editor, value);

            // TinyMCE displayed.
            } else {
                var editorid = 'id_' + editorname;
                tinyMCE.execInstanceCommand(editorid, 'mceInsertContent', false, value);
            }
            selector.set('selectedIndex', 0);
        });
    });
};

/**
 * Checks/unchecks selector checkboxes on page for bulk actions.
 */
M.mod_dataform.util.init_select_allnone = function(Y, elemname) {
    Y.use('node', function (Y) {
        if (!Y.one('#id_' + elemname + 'selectallnone')) {
            return;
        }
        Y.one('#id_' + elemname + 'selectallnone').on('click', function (e) {
            var selectorclass = '.' + elemname + 'selector';
            if (e.target.get('checked')) {
                Y.all(selectorclass).set('checked', 'checked');
            } else {
                Y.all(selectorclass).set('checked', '');
            }
        });
    });
};


/**
 * construct url for multiactions
 * Used when editing dataform entries
 */
M.mod_dataform.util.init_bulk_action = function(Y, elemname, action, url, defaultval) {
    Y.use('node', function (Y) {
        if (!Y.one('#id_' + elemname + '_bulkaction_' + action)) {
            return;
        }

        Y.one('#id_' + elemname + '_bulkaction_' + action).on('click', function (e) {
            e.preventDefault();

            var selected = [];
            Y.all('.' + elemname + 'selector').each(function(selector) {
                if (selector.get('checked')) {
                    selected.push(selector.get('value'));
                }
            });

            // Send selected item ids to processing.
            if (selected.length) {
                location.href = url + '&' + action + '=' + selected.join(',');

            // If no items selected but there is default, send it.
            } else if (defaultval) {
                location.href = url + '&' + action + '=' + defaultval;
            }
        });
    });
};
