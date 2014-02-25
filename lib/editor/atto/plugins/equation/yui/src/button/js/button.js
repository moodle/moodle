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
 * Atto text editor equation plugin.
 *
 * @package    editor-atto
 * @copyright  2013 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
M.atto_equation = M.atto_equation || {
    /**
     * The window used to get the equation details.
     *
     * @property dialogue
     * @type M.core.dialogue
     * @default null
     */
    dialogue : null,

    /**
     * The selection object returned by the browser.
     *
     * @property selection
     * @type Range
     * @default null
     */
    selection : null,

    /**
     * A mapping of elementids to contextids.
     *
     * @property contextids
     * @type Object
     * @default {}
     */
    contextids : {},

    /**
     * A nested object containing a the configured list of tex examples.
     *
     * @property library
     * @type Object
     * @default {}
     */
    library : {},

    /**
     * The last cursor index in the source.
     *
     * @property lastcursor
     * @type Integer
     * @default 0
     */
    lastcursor : 0,

    /**
     * Display the chooser dialogue.
     *
     * @method display_chooser
     * @param Event e
     * @param string elementid
     */
    display_chooser : function(e, elementid) {
        e.preventDefault();
        if (!M.editor_atto.is_active(elementid)) {
            M.editor_atto.focus(elementid);
        }
        M.atto_equation.selection = M.editor_atto.get_selection();
        if (M.atto_equation.selection !== false && (!M.atto_equation.selection.collapsed)) {
            var dialogue;
            if (!M.atto_equation.dialogue) {
                dialogue = new M.core.dialogue({
                    visible: false,
                    modal: true,
                    close: true,
                    draggable: true,
                    width: '800px'
                });
            } else {
                dialogue = M.atto_equation.dialogue;
            }

            dialogue.render();
            dialogue.set('bodyContent', M.atto_equation.get_form_content(elementid));
            dialogue.set('headerContent', M.util.get_string('pluginname', 'atto_equation'));

            var tabview = new Y.TabView({
                srcNode: '#atto_equation_library'
            });

            tabview.render();
            dialogue.show();
            var equation = M.atto_equation.resolve_equation();
            if (equation) {
                Y.one('#atto_equation_equation').set('text', equation);
            }
            M.atto_equation.update_preview(false, elementid);
            M.atto_equation.dialogue = dialogue;
        }
    },

    /**
     * Add this button to the form.
     *
     * @method init
     * @param {Object} params
     */
    init : function(params) {
        var iconurl = M.util.image_url('e/math', 'core');

        if (params.texfilteractive) {
            // Save the elementid/contextid mapping.
            this.contextids[params.elementid] = params.contextid;
            // Save the button library.
            this.library = params.library;

            // Add the button to the toolbar.
            M.editor_atto.add_toolbar_button(params.elementid, 'equation', iconurl, params.group, this.display_chooser);
            // Attach an event listner to watch for "changes" in the contenteditable.
            // This includes cursor changes, we check if the button should be active or not, based
            // on the text selection.
            var editable = M.editor_atto.get_editable_node(params.elementid);
            editable.on('atto:selectionchanged', function(e) {
                if (M.atto_equation.resolve_equation() !== false) {
                    M.editor_atto.add_widget_highlight(e.elementid, 'equation');
                } else {
                    M.editor_atto.remove_widget_highlight(e.elementid, 'equation');
                }
            });
        }
    },

    /**
     * If there is selected text and it is part of an equation,
     * extract the equation (and set it in the form).
     *
     * @method resolve_equation
     * @return {String|Boolean} The equation or false.
     */
    resolve_equation : function() {
        // Find the equation in the surrounding text.
        var selectednode = M.editor_atto.get_selection_parent_node(),
            text,
            equation;

        // Note this is a document fragment and YUI doesn't like them.
        if (!selectednode) {
            return false;
        }

        text = Y.one(selectednode).get('text');
        // We use space or not space because . does not match new lines.
        pattern = /\$\$[\S\s]*\$\$/;
        equation = pattern.exec(text);
        if (equation && equation.length) {
            equation = equation.pop();
            // Replace the equation.
            equation = equation.substring(2, equation.length - 2);
            return equation;
        }
        return false;
    },

    /**
     * The OK button has been pressed - make the changes to the source.
     *
     * @method set_equation
     * @param {Y.Event} e
     * @param {String} elementid
     */
    set_equation : function(e, elementid) {
        var input,
            selectednode,
            text,
            pattern,
            equation,
            value;

        e.preventDefault();
        M.atto_equation.dialogue.hide();
        M.editor_atto.set_selection(M.atto_equation.selection);

        input = e.currentTarget.ancestor('.atto_form').one('textarea');

        value = input.get('value');
        if (value !== '') {
            value = '$$ ' + value.trim() + ' $$';
            selectednode = Y.one(M.editor_atto.get_selection_parent_node()),
            text = selectednode.get('text');
            pattern = /\$\$[\S\s]*\$\$/;
            equation = pattern.exec(text);
            if (equation && equation.length) {
                // Replace the equation.
                equation = equation.pop();
                text = text.replace(equation, '$$' + value + '$$');
                selectednode.set('text', text);
            } else {
                // Insert the new equation.
                M.editor_atto.insert_html_at_focus_point(value);
            }

            // Clean the YUI ids from the HTML.
            M.editor_atto.text_updated(elementid);
        }
    },

    /**
     * Update the preview div to match the current equation.
     *
     * @param Event e - unused
     * @param String elementid - The editor elementid.
     * @method update_preview
     */
    update_preview : function(e, elementid) {
        var textarea = Y.one('#atto_equation_equation');
        var equation = textarea.get('value'), url, preview;
        var prefix = '';
        var cursorlatex = '\\square ' ;

        var currentpos = textarea.get('selectionStart');
        if (!currentpos) {
            currentpos = 0;
        }
        // Move the cursor so it does not break expressions.
        //
        while (equation.charAt(currentpos) === '\\' && currentpos > 0) {
            currentpos -= 1;
        }
        var ischar = /[\w\{\}]/;
        while (ischar.test(equation.charAt(currentpos)) && currentpos < equation.length) {
            currentpos += 1;
        }
        // Save the cursor position - for insertion from the library.
        this.lastcursorpos = currentpos;
        equation = prefix + equation.substring(0, currentpos) + cursorlatex + equation.substring(currentpos);
        if (e) {
            e.preventDefault();
        }
        url = M.cfg.wwwroot + '/lib/editor/atto/plugins/equation/ajax.php';
        params = {
            sesskey: M.cfg.sesskey,
            contextid: this.contextids[elementid],
            action : 'filtertext',
            text : '$$ ' + equation + ' $$'
        };


        preview = Y.io(url, { sync: true,
                              data: params });
        if (preview.status === 200) {
            Y.one('#atto_equation_preview').setHTML(preview.responseText);
        }
    },

    /**
     * Return the HTML of the form to show in the dialogue.
     *
     * @method get_form_content
     * @param string elementid
     * @return string
     */
    get_form_content : function(elementid) {
        var content = Y.Node.create('<form class="atto_form">' +
                             this.get_library_html(elementid) +
                             '<label for="atto_equation_equation">' + M.util.get_string('editequation', 'atto_equation') +
                             '</label>' +
                             '<textarea class="fullwidth" id="atto_equation_equation" rows="8"></textarea><br/>' +
                             '<p>' + M.util.get_string('editequation_desc', 'atto_equation') + '</p>' +
                             '<label for="atto_equation_preview">' + M.util.get_string('preview', 'atto_equation') +
                             '</label>' +
                             '<div class="fullwidth" id="atto_equation_preview"></div>' +
                             '<div class="mdl-align">' +
                             '<br/>' +
                             '<button id="atto_equation_submit">' +
                             M.util.get_string('saveequation', 'atto_equation') +
                             '</button>' +
                             '</div>' +
                             '</form>');

        content.one('#atto_equation_submit').on('click', M.atto_equation.set_equation, this, elementid);
        content.one('#atto_equation_equation').on('valuechange', M.atto_equation.update_preview, this, elementid);
        content.one('#atto_equation_equation').on('keyup', M.atto_equation.update_preview, this, elementid);
        content.one('#atto_equation_equation').on('mouseup', M.atto_equation.update_preview, this, elementid);

        content.delegate('click', M.atto_equation.select_library_item, '#atto_equation_library button', this, elementid);

        return content;
    },

    /**
     * Reponse to button presses in the tex library panels.
     *
     * @method select_library_item
     * @param Event event
     * @param string elementid
     * @return string
     */
    select_library_item : function(event, elementid) {
        var tex = event.currentTarget.getAttribute('data-tex');

        event.preventDefault();

        input = event.currentTarget.ancestor('.atto_form').one('textarea');

        value = input.get('value');

        value = value.substring(0, this.lastcursorpos) + tex + value.substring(this.lastcursorpos, value.length);

        input.set('value', value);
        M.atto_equation.update_preview(false, elementid);
        input.focus();
    },

    /**
     * Return the HTML for rendering the library of predefined buttons.
     *
     * @method get_library_html
     * @param string elementid
     * @return string
     */
    get_library_html : function(elementid) {
        var content = '<div id="atto_equation_library">', i = 0, group = 1;
        content += '<ul>';
        for (group = 1; group < 5; group++) {
            content += '<li><a href="#atto_equation_library' + group + '">' + M.util.get_string('librarygroup' + group, 'atto_equation') + '</a></li>';
        }
        content += '</ul>';
        content += '<div>';
        for (group = 1; group < 5; group++) {
            content += '<div id="atto_equation_library' + group + '">';
            var examples = this.library['group' + group].split("\n");
            for (i = 0; i < examples.length; i++) {
                if (examples[i]) {
                    examples[i] = Y.Escape.html(examples[i]);
                    content += '<button data-tex="' + examples[i] + '" title="' + examples[i] + '">$$' + examples[i] + '$$</button>';
                }
            }
            content += '</div>';
        }
        content += '</div>';
        content += '</div>';

        var url = M.cfg.wwwroot + '/lib/editor/atto/plugins/equation/ajax.php';
        var params = {
            sesskey: M.cfg.sesskey,
            contextid: this.contextids[elementid],
            action : 'filtertext',
            text : content
        };

        preview = Y.io(url, { sync: true, data: params, method: 'POST'});

        if (preview.status === 200) {
            content = preview.responseText;
        }
        return content;
    }
};
