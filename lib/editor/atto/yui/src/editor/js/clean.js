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
/* global LOGNAME */

/**
 * @module moodle-editor_atto-editor
 * @submodule clean
 */

/**
 * Functions for the Atto editor to clean the generated content.
 *
 * See {{#crossLink "M.editor_atto.Editor"}}{{/crossLink}} for details.
 *
 * @namespace M.editor_atto
 * @class EditorClean
 */

function EditorClean() {}

EditorClean.ATTRS = {
};

EditorClean.prototype = {
    /**
     * Clean the generated HTML content without modifying the editor content.
     *
     * This includes removes all YUI ids from the generated content.
     *
     * @return {string} The cleaned HTML content.
     */
    getCleanHTML: function() {
        // Clone the editor so that we don't actually modify the real content.
        var editorClone = this.editor.cloneNode(true),
            html;

        // Remove all YUI IDs.
        Y.each(editorClone.all('[id^="yui"]'), function(node) {
            node.removeAttribute('id');
        });

        editorClone.all('.atto_control').remove(true);
        html = editorClone.get('innerHTML');

        // Revert untouched editor contents to an empty string.
        if (html === '<p></p>' || html === '<p><br></p>') {
            return '';
        }

        // Remove any and all nasties from source.
       return this._cleanHTML(html);
    },

    /**
     * Clean the HTML content of the editor.
     *
     * @method cleanEditorHTML
     * @chainable
     */
    cleanEditorHTML: function() {
        var startValue = this.editor.get('innerHTML');
        this.editor.set('innerHTML', this._cleanHTML(startValue));

        return this;
    },

    /**
     * Clean the specified HTML content and remove any content which could cause issues.
     *
     * @method _cleanHTML
     * @private
     * @param {String} content The content to clean
     * @return {String} The cleaned HTML
     */
    _cleanHTML: function(content) {
        // Removing limited things that can break the page or a disallowed, like unclosed comments, style blocks, etc.

        var rules = [
            // Remove any style blocks. Some browsers do not work well with them in a contenteditable.
            // Plus style blocks are not allowed in body html, except with "scoped", which most browsers don't support as of 2015.
            // Reference: "http://stackoverflow.com/questions/1068280/javascript-regex-multiline-flag-doesnt-work"
            {regex: /<style[^>]*>[\s\S]*?<\/style>/gi, replace: ""},

            // Remove any open HTML comment opens that are not followed by a close. This can completely break page layout.
            {regex: /<!--(?![\s\S]*?-->)/gi, replace: ""},

            // Source: "http://www.codinghorror.com/blog/2006/01/cleaning-words-nasty-html.html"
            // Remove forbidden tags for content, title, meta, style, st0-9, head, font, html, body, link.
            {regex: /<\/?(?:title|meta|style|st\d|head\b|font|html|body|link)[^>]*?>/gi, replace: ""}
        ];

        return this._filterContentWithRules(content, rules);
    },

    /**
     * Take the supplied content and run on the supplied regex rules.
     *
     * @method _filterContentWithRules
     * @private
     * @param {String} content The content to clean
     * @param {Array} rules An array of structures: [ {regex: /something/, replace: "something"}, {...}, ...]
     * @return {String} The cleaned content
     */
    _filterContentWithRules: function(content, rules) {
        var i = 0;
        for (i = 0; i < rules.length; i++) {
            content = content.replace(rules[i].regex, rules[i].replace);
        }

        return content;
    },

    /**
     * Intercept and clean html paste events.
     *
     * @method pasteCleanup
     * @param {Object} sourceEvent The YUI EventFacade  object
     * @return {Boolean} True if the passed event should continue, false if not.
     */
    pasteCleanup: function(sourceEvent) {
        // We only expect paste events, but we will check anyways.
        if (sourceEvent.type === 'paste') {
            // The YUI event wrapper doesn't provide paste event info, so we need the underlying event.
            var event = sourceEvent._event;
            // Check if we have a valid clipboardData object in the event.
            // IE has a clipboard object at window.clipboardData, but as of IE 11, it does not provide HTML content access.
            if (event && event.clipboardData && event.clipboardData.getData && event.clipboardData.types) {
                // Check if there is HTML type to be pasted, if we can get it, we want to scrub before insert.
                var types = event.clipboardData.types;
                var isHTML = false;
                // Different browsers use different containers to hold the types, so test various functions.
                if (typeof types.contains === 'function') {
                    isHTML = types.contains('text/html');
                } else if (typeof types.indexOf === 'function') {
                    isHTML = (types.indexOf('text/html') > -1);
                }

                var content;
                if (isHTML) {
                    // Get the clipboard content.
                    try {
                        content = event.clipboardData.getData('text/html');
                    } catch (error) {
                        // Something went wrong. Fallback.
                        this.fallbackPasteCleanupDelayed();
                        return true;
                    }

                    // Stop the original paste.
                    sourceEvent.preventDefault();

                    // Scrub the paste content.
                    content = this._cleanPasteHTML(content);

                    // Save the current selection.
                    // Using saveSelection as it produces a more consistent experience.
                    var selection = window.rangy.saveSelection();

                    // Insert the content.
                    this.insertContentAtFocusPoint(content);

                    // Restore the selection, and collapse to end.
                    window.rangy.restoreSelection(selection);
                    window.rangy.getSelection().collapseToEnd();

                    // Update the text area.
                    this.updateOriginal();
                    return false;
                } else {
                    try {
                        // Plaintext clipboard content can only be retrieved this way.
                        content = event.clipboardData.getData('text');
                    } catch (error) {
                        // Something went wrong. Fallback.
                        // Due to poor cross browser clipboard compatibility, the failure to find html doesn't mean it isn't there.
                        // Wait for the clipboard event to finish then fallback clean the entire editor.
                        this.fallbackPasteCleanupDelayed();
                        return true;
                    }
                }
            } else {
                // If we reached a here, this probably means the browser has limited (or no) clipboard support.
                // Wait for the clipboard event to finish then fallback clean the entire editor.
                this.fallbackPasteCleanupDelayed();
                return true;
            }
        }

        // We should never get here - we must have received a non-paste event for some reason.
        // Um, just call updateOriginalDelayed() - it's safe.
        this.updateOriginalDelayed();
        return true;
    },

    /**
     * Cleanup code after a paste event if we couldn't intercept the paste content.
     *
     * @method fallbackPasteCleanup
     * @chainable
     */
    fallbackPasteCleanup: function() {
        Y.log('Using fallbackPasteCleanup for atto cleanup', 'debug', LOGNAME);

        // Save the current selection (cursor position).
        var selection = window.rangy.saveSelection();

        // Get, clean, and replace the content in the editable.
        var content = this.editor.get('innerHTML');
        this.editor.set('innerHTML', this._cleanPasteHTML(content));

        // Update the textarea.
        this.updateOriginal();

        // Restore the selection (cursor position).
        window.rangy.restoreSelection(selection);

        return this;
    },

    /**
     * Calls fallbackPasteCleanup on a short timer to allow the paste event handlers to complete.
     *
     * @method fallbackPasteCleanupDelayed
     * @chainable
     */
    fallbackPasteCleanupDelayed: function() {
        Y.soon(Y.bind(this.fallbackPasteCleanup, this));

        return this;
    },

    /**
     * Cleanup html that comes from WYSIWYG paste events. These are more likely to contain messy code that we should strip.
     *
     * @method _cleanPasteHTML
     * @private
     * @param {String} content The html content to clean
     * @return {String} The cleaned HTML
     */
    _cleanPasteHTML: function(content) {
        // Return an empty string if passed an invalid or empty object.
        if (!content || content.length === 0) {
            return "";
        }

        // Rules that get rid of the real-nasties and don't care about normalize code (correct quotes, white spaces, etc).
        var rules = [
            // Stuff that is specifically from MS Word and similar office packages.
            // Remove all garbage after closing html tag.
            {regex: /<\s*\/html\s*>([\s\S]+)$/gi, replace: ""},
            // Remove if comment blocks.
            {regex: /<!--\[if[\s\S]*?endif\]-->/gi, replace: ""},
            // Remove start and end fragment comment blocks.
            {regex: /<!--(Start|End)Fragment-->/gi, replace: ""},
            // Remove any xml blocks.
            {regex: /<xml[^>]*>[\s\S]*?<\/xml>/gi, replace: ""},
            // Remove any <?xml><\?xml> blocks.
            {regex: /<\?xml[^>]*>[\s\S]*?<\\\?xml>/gi, replace: ""},
            // Remove <o:blah>, <\o:blah>.
            {regex: /<\/?\w+:[^>]*>/gi, replace: ""}
        ];

        // Apply the first set of harsher rules.
        content = this._filterContentWithRules(content, rules);

        // Apply the standard rules, which mainly cleans things like headers, links, and style blocks.
        content = this._cleanHTML(content);

        // Check if the string is empty or only contains whitespace.
        if (content.length === 0 || !content.match(/\S/)) {
            return content;
        }

        // Now we let the browser normalize the code by loading it into the DOM and then get the html back.
        // This gives us well quoted, well formatted code to continue our work on. Word may provide very poorly formatted code.
        var holder = document.createElement('div');
        holder.innerHTML = content;
        content = holder.innerHTML;
        // Free up the DOM memory.
        holder.innerHTML = "";

        // Run some more rules that care about quotes and whitespace.
        rules = [
            // Get all class attributes so we can work on them.
            {regex: /(<[^>]*?class\s*?=\s*?")([^>"]*)(")/gi, replace: function(match, group1, group2, group3) {
                    // Remove MSO classes.
                    group2 = group2.replace(/(?:^|[\s])[\s]*MSO[_a-zA-Z0-9\-]*/gi, "");
                    // Remove Apple- classes.
                    group2 = group2.replace(/(?:^|[\s])[\s]*Apple-[_a-zA-Z0-9\-]*/gi, "");
                    return group1 + group2 + group3;
                }},
            // Remove OLE_LINK# anchors that may litter the code.
            {regex: /<a [^>]*?name\s*?=\s*?"OLE_LINK\d*?"[^>]*?>\s*?<\/a>/gi, replace: ""}
        ];

        // Clean all style attributes from the text.
        content = this._cleanStyles(content);

        // Apply the rules.
        content = this._filterContentWithRules(content, rules);

        // Reapply the standard cleaner to the content.
        content = this._cleanHTML(content);

        // Clean unused spans out of the content.
        content = this._cleanSpans(content);

        return content;
    },

    /**
     * Clean all inline styles from pasted text.
     *
     * This code intentionally doesn't use YUI Nodes. YUI was quite a bit slower at this, so using raw DOM objects instead.
     *
     * @method _cleanStyles
     * @private
     * @param {String} content The content to clean
     * @return {String} The cleaned HTML
     */
    _cleanStyles: function(content) {
        var holder = document.createElement('div');
        holder.innerHTML = content;
        var elementsWithStyle = holder.querySelectorAll('[style]');
        var i = 0;

        for (i = 0; i < elementsWithStyle.length; i++) {
            elementsWithStyle[i].removeAttribute('style');
        }

        var elementsWithClass = holder.querySelectorAll('[class]');
        for (i = 0; i < elementsWithClass.length; i++) {
            elementsWithClass[i].removeAttribute('class');
        }

        return holder.innerHTML;
    },
    /**
     * Clean empty or un-unused spans from passed HTML.
     *
     * This code intentionally doesn't use YUI Nodes. YUI was quite a bit slower at this, so using raw DOM objects instead.
     *
     * @method _cleanSpans
     * @private
     * @param {String} content The content to clean
     * @return {String} The cleaned HTML
     */
    _cleanSpans: function(content) {
        // Return an empty string if passed an invalid or empty object.
        if (!content || content.length === 0) {
            return "";
        }
        // Check if the string is empty or only contains whitespace.
        if (content.length === 0 || !content.match(/\S/)) {
            return content;
        }

        var rules = [
            // Remove unused class, style, or id attributes. This will make empty tag detection easier later.
            {regex: /(<[^>]*?)(?:[\s]*(?:class|style|id)\s*?=\s*?"\s*?")+/gi, replace: "$1"}
        ];
        // Apply the rules.
        content = this._filterContentWithRules(content, rules);

        // Reference: "http://stackoverflow.com/questions/8131396/remove-nested-span-without-id"

        // This is better to run detached from the DOM, so the browser doesn't try to update on each change.
        var holder = document.createElement('div');
        holder.innerHTML = content;
        var spans = holder.getElementsByTagName('span');

        // Since we will be removing elements from the list, we should copy it to an array, making it static.
        var spansarr = Array.prototype.slice.call(spans, 0);

        spansarr.forEach(function(span) {
            if (!span.hasAttributes()) {
                // If no attributes (id, class, style, etc), this span is has no effect.
                // Move each child (if they exist) to the parent in place of this span.
                while (span.firstChild) {
                    span.parentNode.insertBefore(span.firstChild, span);
                }

                // Remove the now empty span.
                span.parentNode.removeChild(span);
            }
        });

        return holder.innerHTML;
    }
};

Y.Base.mix(Y.M.editor_atto.Editor, [EditorClean]);
