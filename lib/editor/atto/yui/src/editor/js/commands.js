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
 * @module moodle-editor_atto-editor
 * @submodule commands
 */

/**
 * Selection functions for the Atto editor.
 *
 * See {{#crossLink "M.editor_atto.Editor"}}{{/crossLink}} for details.
 *
 * @namespace M.editor_atto
 * @class EditorCommand
 */

function EditorCommand() {}

EditorCommand.ATTRS= {
};

EditorCommand.prototype = {
    /**
     * Applies a callback method to editor if selection is uncollapsed or waits for input to select first.
     * @method applyFormat
     * @param e EventTarget Event to be passed to callback if selection is uncollapsed
     * @param method callback A callback method which changes editor when text is selected.
     * @param object context Context to be used for callback method
     * @param array args Array of arguments to pass to callback
     */
    applyFormat: function(e, callback, context, args) {
        function handleInsert(e, callback, context, args, anchorNode, anchorOffset) {
            // After something is inputed, select it and apply the formating function.
            Y.soon(Y.bind(function(e, callback, context, args, anchorNode, anchorOffset) {
                var selection = window.rangy.getSelection();

                // Set the start of the selection to where it was when the method was first called.
                var range = selection.getRangeAt(0);
                range.setStart(anchorNode, anchorOffset);
                selection.setSingleRange(range);

                // Now apply callback to the new text that is selected.
                callback.apply(context, [e, args]);

                // Collapse selection so cursor is at end of inserted material.
                selection.collapseToEnd();

                // Save save selection and editor contents.
                this.saveSelection();
                this.updateOriginal();
            }, this, e, callback, context, args, anchorNode, anchorOffset));
        }

        // Set default context for the method.
        context = context || this;

        // Check whether range is collapsed.
        var selection = window.rangy.getSelection();

        if (selection.isCollapsed) {
            // Selection is collapsed so listen for input into editor.
            var handle = this.editor.once('input', handleInsert, this, callback, context, args,
                    selection.anchorNode, selection.anchorOffset);

            // Cancel if selection changes before input.
            this.editor.onceAfter(['click', 'selectstart'], handle.detach, handle);

            return;
        }

        // The range is not collapsed; so apply callback method immediately.
        callback.apply(context, [e, args]);

        // Save save selection and editor contents.
        this.saveSelection();
        this.updateOriginal();
    },

    /**
     * Replaces all the tags in a node list with new type.
     * @method replaceTags
     * @param NodeList nodelist
     * @param String tag
     */
    replaceTags: function(nodelist, tag) {
        // We mark elements in the node list for iterations.
        nodelist.setAttribute('data-iterate', true);
        var node = this.editor.one('[data-iterate="true"]');
        while (node) {
            var clone = Y.Node.create('<' + tag + ' />')
                .setAttrs(node.getAttrs())
                .removeAttribute('data-iterate');
            // Copy class and style if not blank.
            if (node.getAttribute('style')) {
                clone.setAttribute('style', node.getAttribute('style'));
            }
            if (node.getAttribute('class')) {
                clone.setAttribute('class', node.getAttribute('class'));
            }
            // We use childNodes here because we are interested in both type 1 and 3 child nodes.
            var children = node.getDOMNode().childNodes, child;
            child = children[0];
            while (typeof child !== "undefined") {
                clone.append(child);
                child = children[0];
            }
            node.replace(clone);
            node = this.editor.one('[data-iterate="true"]');
        }
    },

    /**
     * Change all tags with given type to a span with CSS class attribute.
     * @method changeToCSS
     * @param String tag Tag type to be changed to span
     * @param String markerClass CSS class that corresponds to desired tag
     */
    changeToCSS: function(tag, markerClass) {
        // Save the selection.
        var selection = window.rangy.saveSelection();

        // Remove display:none from rangy markers so browser doesn't delete them.
        this.editor.all('.rangySelectionBoundary').setStyle('display', null);

        // Replace tags with CSS classes.
        this.editor.all(tag).addClass(markerClass);
        this.replaceTags(this.editor.all('.' + markerClass), 'span');

        // Restore selection and toggle class.
        window.rangy.restoreSelection(selection);
    },

    /**
     * Change spans with CSS classes in editor into elements with given tag.
     * @method changeToCSS
     * @param String markerClass CSS class that corresponds to desired tag
     * @param String tag New tag type to be created
     */
    changeToTags: function(markerClass, tag) {
        // Save the selection.
        var selection = window.rangy.saveSelection();

        // Remove display:none from rangy markers so browser doesn't delete them.
        this.editor.all('.rangySelectionBoundary').setStyle('display', null);

        // Replace spans with given tag.
        this.replaceTags(this.editor.all('span[class="' + markerClass + '"]'), tag);
        this.editor.all(tag + '[class="' + markerClass + '"]').removeAttribute('class');
        this.editor.all('.' + markerClass).each(function(n) {
            n.wrap('<' + tag + '/>');
            n.removeClass(markerClass);
        });

        // Remove CSS classes.
        this.editor.all('[class="' + markerClass + '"]').removeAttribute('class');
        this.editor.all(tag).removeClass(markerClass);

        // Restore selection.
        window.rangy.restoreSelection(selection);
    }
};

Y.Base.mix(Y.M.editor_atto.Editor, [EditorCommand]);
