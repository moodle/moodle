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

/*
 * @package    atto_indent
 * @copyright  2013 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module     moodle-atto_indent-button
 */

/**
 * Atto text editor indent plugin.
 *
 * @namespace M.atto_indent
 * @class button
 * @extends M.editor_atto.EditorPlugin
 */

Y.namespace('M.atto_indent').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {
    initializer: function() {

        this.addButton({
            icon: 'e/increase_indent',
            title: 'indent',
            buttonName: 'indent',
            callback: this.indent
        });

        this.addButton({
            icon: 'e/decrease_indent',
            title: 'outdent',
            buttonName: 'outdent',
            callback: this.outdent
        });
    },

    /**
     * Indents the currently selected content.
     *
     * @method indent
     */
    indent: function() {
        // Save the current selection - we want to restore this.
        var selection = rangy.saveSelection(),
            blockquotes = this.editor.all('blockquote'),
            count = blockquotes.size();

        // Remove display:none from rangy markers so browser doesn't delete them.
        this.editor.all('.rangySelectionBoundary').setStyle('display', null);

        // Mark all existing block quotes in case the user has actually added some.
        blockquotes.addClass('pre-existing');

        // Run the indent command.
        document.execCommand('indent', false, null);

        // Get all blockquotes, both existing and new.
        blockquotes = this.editor.all('blockquote');

        if (blockquotes.size() !== count) {
            // There are new block quotes, the indent exec has wrapped some content in block quotes in order
            // to indent the selected content.
            // We don't want blockquotes, we're going to convert them to divs.
            this.replaceBlockquote(this.editor);
            // Finally restore the seelction. The content has changed - sometimes this works - but not always :(
            rangy.restoreSelection(selection);
        } else if (blockquotes.size() > 0) {
            // There were no new blockquotes, this happens if the user is indenting/outdenting a list.
            blockquotes.removeClass('pre-existing');
        }

        // Remove the selection markers - a clean up really.
        rangy.removeMarkers(selection);

        // Mark the text as having been updated.
        this.markUpdated();
    },

    /**
     * Outdents the currently selected content.
     *
     * @method outdent
     */
    outdent: function() {
        // Save the selection we will want to restore it.
        var selection = rangy.saveSelection(),
            blockquotes = this.editor.all('blockquote'),
            count = blockquotes.size();

        // Mark existing blockquotes so that we don't convert them later.
        blockquotes.addClass('pre-existing');

        // Replace all div indents with blockquote indents so that we can rely on the browser functionality.
        this.replaceEditorIndents(this.editor);

        // Restore the users selection - otherwise the next outdent operation won't work!
        rangy.restoreSelection(selection);
        // And save it once more.
        selection = rangy.saveSelection();

        // Outdent.
        document.execCommand('outdent', false, null);

        // Get all blockquotes so that we can work out what happened.
        blockquotes = this.editor.all('blockquote');

        if (blockquotes.size() !== count) {
            // The number of blockquotes hasn't changed.
            // This occurs when the user has outdented a list item.
            this.replaceBlockquote(this.editor);
            rangy.restoreSelection(selection);
        } else if (blockquotes.size() > 0) {
            // The number of blockquotes is the same and is more than 0 we just need to clean up the class
            // we added to mark pre-existing blockquotes.
            blockquotes.removeClass('pre-existing');
        }

        // Clean up any left over selection markers.
        rangy.removeMarkers(selection);

        // Mark the text as having been updated.
        this.markUpdated();
    },

    /**
     * Replaces all blockquotes within an editor with div indents.
     * @method replaceBlockquote
     * @param Editor editor
     */
    replaceBlockquote: function(editor) {
        editor.all('blockquote').setAttribute('data-iterate', true);
        var blockquote = editor.one('blockquote'),
            margindir = (Y.one('body.dir-ltr')) ? 'marginLeft' : 'marginRight';
        while (blockquote) {
            blockquote.removeAttribute('data-iterate');
            if (blockquote.hasClass('pre-existing')) {
                blockquote.removeClass('pre-existing');
            } else {
                var clone = Y.Node.create('<div></div>').setAttrs(blockquote.getAttrs()).setStyle(margindir, '30px').addClass('editor-indent');
                // We use childNodes here because we are interested in both type 1 and 3 child nodes.
                var children = blockquote.getDOMNode().childNodes, child;
                child = children[0];
                while (typeof child !== "undefined") {
                    clone.append(child);
                    child = children[0];
                }
                blockquote.replace(clone);
            }
            blockquote = editor.one('blockquote[data-iterate]');
        }
    },

    /**
     * Replaces all div indents with blockquotes.
     * @method replaceEditorIndents
     * @param Editor editor
     */
    replaceEditorIndents: function(editor) {
        // We use the editor-indent class because it is preserved between saves.
        var indent = editor.one('.editor-indent');
        while (indent) {
            var clone = Y.Node.create('<blockquote></blockquote>').setAttrs(indent.getAttrs()).removeClass('editor-indent');
            // We use childNodes here because we are interested in both type 1 and 3 child nodes.
            var children = indent.getDOMNode().childNodes, child;
            child = children[0];
            while (typeof child !== "undefined") {
                clone.append(child);
                child = children[0];
            }
            indent.replace(clone);
            indent = editor.one('.editor-indent');
        }
    }
});
