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

EditorClean.ATTRS= {
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
        // What are we doing ?
        // We are cleaning random HTML from all over the shop into a set of useful html suitable for content.
        // We are allowing styles etc, but not e.g. font tags, class="MsoNormal" etc.

        var rules = [
            // Source: "http://stackoverflow.com/questions/2875027/clean-microsoft-word-pasted-text-using-javascript"
            // Source: "http://stackoverflow.com/questions/1068280/javascript-regex-multiline-flag-doesnt-work"

            // Remove all HTML comments.
            {regex: /<!--[\s\S]*?-->/gi, replace: ""},
            // Source: "http://www.1stclassmedia.co.uk/developers/clean-ms-word-formatting.php"
            // Remove <?xml>, <\?xml>.
            {regex: /<\\?\?xml[^>]*>/gi, replace: ""},
            // Remove <o:blah>, <\o:blah>.
            {regex: /<\/?\w+:[^>]*>/gi, replace: ""}, // e.g. <o:p...
            // Remove MSO-blah, MSO:blah (e.g. in style attributes)
            {regex: /\s*MSO[-:][^;"']*;?/gi, replace: ""},
            // Remove empty spans
            {regex: /<span[^>]*>(&nbsp;|\s)*<\/span>/gi, replace: ""},
            // Remove class="Msoblah"
            {regex: /class="Mso[^"]*"/gi, replace: ""},
            // Remove any open HTML comment opens that are not followed by a close. This can completely break page layout.
            {regex: /<!--(?![\s\S]*?-->)/gi, replace: ""},

            // Source: "http://www.codinghorror.com/blog/2006/01/cleaning-words-nasty-html.html"
            // Remove forbidden tags for content, title, meta, style, st0-9, head, font, html, body, link.
            {regex: /<(\/?title|\/?meta|\/?style|\/?st\d|\/?head|\/?font|\/?html|\/?body|\/?link|!\[)[^>]*?>/gi, replace: ""},

            // Source: "http://www.tim-jarrett.com/labs_javascript_scrub_word.php"
            // Replace extended chars with simple text.
            {regex: new RegExp(String.fromCharCode(8220), 'gi'), replace: '"'},
            {regex: new RegExp(String.fromCharCode(8216), 'gi'), replace: "'"},
            {regex: new RegExp(String.fromCharCode(8217), 'gi'), replace: "'"},
            {regex: new RegExp(String.fromCharCode(8211), 'gi'), replace: '-'},
            {regex: new RegExp(String.fromCharCode(8212), 'gi'), replace: '--'},
            {regex: new RegExp(String.fromCharCode(189), 'gi'), replace: '1/2'},
            {regex: new RegExp(String.fromCharCode(188), 'gi'), replace: '1/4'},
            {regex: new RegExp(String.fromCharCode(190), 'gi'), replace: '3/4'},
            {regex: new RegExp(String.fromCharCode(169), 'gi'), replace: '(c)'},
            {regex: new RegExp(String.fromCharCode(174), 'gi'), replace: '(r)'},
            {regex: new RegExp(String.fromCharCode(8230), 'gi'), replace: '...'}
        ];

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
            if (event && event.clipboardData && event.clipboardData.getData) {
                // Check if there is HTML type to be pasted, this is all we care about.
                var types = event.clipboardData.types;
                var isHTML = false;
                // Different browsers use different things to hold the types, so test various functions.
                if (!types) {
                    isHTML = false;
                } else if (typeof types.contains === 'function') {
                    isHTML = types.contains('text/html');
                } else if (typeof types.indexOf === 'function') {
                    isHTML = (types.indexOf('text/html') > -1);
                    if (!isHTML) {
                        if ((types.indexOf('com.apple.webarchive') > -1) || (types.indexOf('com.apple.iWork.TSPNativeData') > -1)) {
                            // This is going to be a specialized Apple paste paste. We cannot capture this, so clean everything.
                            this.fallbackPasteCleanupDelayed();
                            return true;
                        }
                    }
                } else {
                    // We don't know how to handle the clipboard info, so wait for the clipboard event to finish then fallback.
                    this.fallbackPasteCleanupDelayed();
                    return true;
                }

                if (isHTML) {
                    // Get the clipboard content.
                    var content;
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
                    content = this._cleanHTML(content);

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
                    // This is a non-html paste event, we can just let this continue on and call updateOriginalDelayed.
                    this.updateOriginalDelayed();
                    return true;
                }
            } else {
                // If we reached a here, this probably means the browser has limited (or no) clipboard support.
                // Wait for the clipboard event to finish then fallback.
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
        this.editor.set('innerHTML', this._cleanHTML(content));

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
    }
};

Y.Base.mix(Y.M.editor_atto.Editor, [EditorClean]);
