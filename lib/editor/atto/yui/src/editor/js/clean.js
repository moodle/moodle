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
        var emptyContents = [
            // For FF and Chrome.
            '<p></p>',
            '<p><br></p>',
            '<br>',
            '<p dir="rtl" style="text-align: right;"></p>',
            '<p dir="rtl" style="text-align: right;"><br></p>',
            '<p dir="ltr" style="text-align: left;"></p>',
            '<p dir="ltr" style="text-align: left;"><br></p>',
            // For IE 9 and 10.
            '<p>&nbsp;</p>',
            '<p><br>&nbsp;</p>',
            '<p dir="rtl" style="text-align: right;">&nbsp;</p>',
            '<p dir="rtl" style="text-align: right;"><br>&nbsp;</p>',
            '<p dir="ltr" style="text-align: left;">&nbsp;</p>',
            '<p dir="ltr" style="text-align: left;"><br>&nbsp;</p>'
        ];
        if (emptyContents.includes(html)) {
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
     * @param {Boolean} deepClean If true, do a more in depth (and resource intensive) cleaning of the HTML.
     * @return {String} The cleaned HTML
     */
    _cleanHTML: function(content, deepClean) {
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

        content = this._filterContentWithRules(content, rules);

        if (deepClean) {
            content = this._cleanHTMLLists(content);
        }

        return content;
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
            // Register the delayed paste cleanup. We will cancel it if we register the fallback cleanup.
            var delayedCleanup = this.postPasteCleanupDelayed();
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
                        delayedCleanup.cancel();
                        this.fallbackPasteCleanupDelayed();
                        return true;
                    }

                    // Stop the original paste.
                    sourceEvent.preventDefault();

                    // Scrub the paste content.
                    content = this._cleanPasteHTML(content);

                    // Insert the content.
                    this.insertContentAtFocusPoint(content);

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
                        delayedCleanup.cancel();
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
     * Calls postPasteCleanup on a short timer to allow the paste event handlers to complete, then deep clean the content.
     *
     * @method postPasteCleanupDelayed
     * @return {object}
     * @chainable
     */
    postPasteCleanupDelayed: function() {
        Y.soon(Y.bind(this.postPasteCleanup, this));

        return this;
    },

    /**
     * Do additional cleanup after the paste is complete.
     *
     * @method postPasteCleanup
     * @return {object}
     * @chainable
     */
    postPasteCleanup: function() {
        Y.log('Executing delayed post paste cleanup', 'debug', LOGNAME);

        // Save the current selection (cursor position).
        var selection = window.rangy.saveSelection();

        // Get, clean, and replace the content in the editable.
        var content = this.editor.get('innerHTML');
        this.editor.set('innerHTML', this._cleanHTML(content, true));

        // Update the textarea.
        this.updateOriginal();

        // Restore the selection (cursor position).
        window.rangy.restoreSelection(selection);

        return this;
    },

    /**
     * Cleanup code after a paste event if we couldn't intercept the paste content.
     *
     * @method fallbackPasteCleanup
     * @return {object}
     * @chainable
     */
    fallbackPasteCleanup: function() {
        Y.log('Using fallbackPasteCleanup for atto cleanup', 'debug', LOGNAME);

        // Save the current selection (cursor position).
        var selection = window.rangy.saveSelection();

        // Get, clean, and replace the content in the editable.
        var content = this.editor.get('innerHTML');
        this.editor.set('innerHTML', this._cleanHTML(this._cleanPasteHTML(content), true));

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
    },

    /**
     * This is a function that searches for, and attempts to correct certain issues with ul/ol html lists.
     * This is needed because these lists are used heavily in page layout, and content with bad tags can
     * lead to broke course pages.
     *
     * The theory of operation here is to linearly process the incoming content, counting the opening and closing
     * of list tags, and determining when there is a mismatch.
     *
     * The specific issues this should be able to correct are:
     * - Orphaned li elements will be wrapped in a set of ul tags.
     * - li elements inside li elements.
     * - An extra closing ul, or ol tag will be discarded.
     * - An extra closing li tag will have an opening tag added if appropriate, or will be discarded.
     * - If there is an unmatched list open tag, a matching close tag will be inserted.
     *
     * It does it's best to match the case of corrected tags. Even though not required by html spec,
     * it seems like the safer route.
     *
     * A note on parent elements of li. This code assumes that li must have a ol or ul parent.
     * There are two other potential other parents of li. They are menu and dir. The dir tag was deprecated in
     * HTML4, and removed in HTML5. The menu tag is experimental as of this writing, and basically doesn't work
     * in any browsers, even Firefox, which theoretically has limited support for it. If other parents of li
     * become viable, they will need to be added to this code.
     *
     * @method _cleanHTMLLists
     * @private
     * @param {String} content The content to clean
     * @return {String} The cleaned content
     */
    _cleanHTMLLists: function(content) {
        var output = '',
            toProcess = content,
            match = null,
            openTags = [],
            currentTag = null,
            previousTag = null;

        // Use a regular expression to find the next open or close li, ul, or ol tag.
        // Keep going until there are no more matching tags left.
        while ((match = toProcess.match(/<(\/?)(li|ul|ol)[^>]*>/i))) {
            currentTag = {
                tag: match[2],
                tagLowerCase: match[2].toLowerCase(),
                fullTag: match[0],
                isOpen: (match[1].length == 1) ? false : true
            };

            // Get the most recent open tag.
            previousTag = (openTags.length) ? openTags[openTags.length - 1] : null;

            // Slice up the content based on the match and add content before the match to output.
            output += toProcess.slice(0, match.index);
            toProcess = toProcess.slice(match.index + match[0].length);

            // Now the full content is in output + currentTag.fullTag + toProcess. When making fixes, it is best to push the fix and
            // fullTag back onto the front or toProcess, then restart the loop. This allows processing to follow the normal path
            // most often. But sometimes we will need to modify output to insert or remove tags in the already complete code.

            if (currentTag.isOpen) {
                // We are at the opening phase of a tag.
                // We have to do special processing for list items, as they can only be children of ul and ol tags.
                if (currentTag.tagLowerCase === 'li') {
                    if (!previousTag) {
                        // This means we have are opening a li, but aren't in a list. This is not allowed!

                        // We are going to check for the count of open and close ol tags ahead to decide what to do.
                        var closeCount = (toProcess.match(/<\/(ol)[ >]/ig) || []).length;
                        var openCount = (toProcess.match(/<(ol)[ >]/ig) || []).length;

                        if (closeCount > openCount) {
                            // There are more close ol's ahead than opens ahead. So open the ol and try again.
                            Y.log('Adding an opening ol for orphan li', 'debug', LOGNAME);
                            toProcess = '<ol>' + currentTag.fullTag + toProcess;
                            continue;
                        }

                        // For the other cases, just open a ul and try again. Later the closing ul will get matched if it exists,
                        // or if it doesn't one will automatically get inserted.
                        Y.log('Adding an opening ul for orphan li', 'debug', LOGNAME);
                        toProcess = '<ul>' + currentTag.fullTag + toProcess;
                        continue;
                    }

                    if (previousTag.tagLowerCase === 'li') {
                        // You aren't allowed to nest li tags. Close the current one before starting the new one.
                        Y.log('Adding a closing ' + previousTag.tag + ' before opening a new one.', 'debug', LOGNAME);
                        toProcess = '</' + previousTag.tag + '>' + currentTag.fullTag + toProcess;
                        continue;
                    }

                    // Previous tag must be a list at this point, so we can continue.
                }

                // If we made it this far, record the tag to the open tags list.
                openTags.push({
                    tag: currentTag.tag,
                    tagLowerCase: currentTag.tagLowerCase,
                    position: output.length,
                    length: currentTag.fullTag.length
                });
            } else {
                // We are processing a closing tag.

                if (openTags.length == 0) {
                    // We are closing a tag that isn't open. That's a problem. Just discarding should be safe.
                    Y.log('Discarding extra ' + currentTag.fullTag + ' tag.', 'debug', LOGNAME);
                    continue;
                }

                if (previousTag.tagLowerCase === currentTag.tagLowerCase) {
                    // Closing a tag that matches the open tag. This is the nominal case. Pop it off, and update previousTag.
                    if (currentTag.tag != previousTag.tag) {
                        // This would mean cases don't match between the opening and closing tag.
                        // We are going to swap them to match, even though not required.
                        currentTag.fullTag = currentTag.fullTag.replace(currentTag.tag, previousTag.tag);
                    }

                    openTags.pop();
                    previousTag = (openTags.length) ? openTags[openTags.length - 1] : null;
                } else {
                    // We are closing a tag that isn't the most recent open one open, so we have a mismatch.
                    if (currentTag.tagLowerCase === 'li' && previousTag.liEnd && (previousTag.liEnd < output.length)) {
                        // We are closing an unopened li, but the parent list has complete li tags more than 0 chars ago.
                        // Assume we are missing an open li at the end of the previous li, and insert there.
                        Y.log('Inserting opening ' + currentTag.tag + ' after previous li.', 'debug', LOGNAME);
                        output = this._insertString(output, '<' + currentTag.tag + '>', previousTag.liEnd);
                    } else if (currentTag.tagLowerCase === 'li' && !previousTag.liEnd &&
                            ((previousTag.position + previousTag.length) < output.length)) {
                        // We are closing an unopened li, and the parent has no previous lis in it, but opened more than 0
                        // chars ago. Assume we are missing a starting li, and insert it right after the list opened.
                        Y.log('Inserting opening ' + currentTag.tag + ' at start of parent.', 'debug', LOGNAME);
                        output = this._insertString(output, '<' + currentTag.tag + '>', previousTag.position + previousTag.length);
                    } else if (previousTag.tagLowerCase === 'li') {
                        // We must be trying to close a ul/ol while in a li. Just assume we are missing a closing li.
                        Y.log('Adding a closing ' + previousTag.tag + ' before closing ' + currentTag.tag + '.', 'debug', LOGNAME);
                        toProcess = '</' + previousTag.tag + '>' + currentTag.fullTag + toProcess;
                        continue;
                    } else {
                        // Here we must be trying to close a tag that isn't open, or is open higher up. Just discard.
                        // If there ends up being a missing close tag later on, that will get fixed separately.
                        Y.log('Discarding incorrect ' + currentTag.fullTag + '.', 'debug', LOGNAME);
                        continue;
                    }
                }

                // If we have a valid closing li tag, and a list, record where the li ended.
                if (currentTag.tagLowerCase === 'li' && previousTag) {
                    previousTag.liEnd = output.length + currentTag.fullTag.length;
                }

            }

            // Now we can add the tag to the output.
            output += currentTag.fullTag;
        }

        // Add anything left in toProcess to the output.
        output += toProcess;

        // Anything still in the openTags list are extra and need to be dealt with.
        if (openTags.length) {
            // Work on the list in reverse order so positions stay correct.
            while ((currentTag = openTags.pop())) {
                if (currentTag.liEnd) {
                    // We have a position for the last list item in this element. Insert the closing it after that.
                    output = this._insertString(output, '</' + currentTag.tag + '>', currentTag.liEnd);
                    Y.log('Adding closing ' + currentTag.tag + ' based on last li location.', 'debug', LOGNAME);
                } else {
                    // If there weren't any children list items, then we should just remove the tag where it started.
                    // This will also remote an open li tag that runs to the end of the content, since it has no children lis.
                    output = output.slice(0, currentTag.position) + output.slice(currentTag.position + currentTag.length);
                    Y.log('Removing opening ' + currentTag.fullTag + ' because it was missing closing.', 'debug', LOGNAME);
                }
            }
        }

        return output;
    },

    /**
     * Insert a string in the middle of an existing string at the specified location.
     *
     * @method _insertString
     * @param {String} content The subject of the insertion.
     * @param {String} insert The string that will be inserted.
     * @param {Number} position The location to make the insertion.
     * @return {String} The string with the new content inserted.
     */
    _insertString: function(content, insert, position) {
        return content.slice(0, position) + insert + content.slice(position);
    }
};

Y.Base.mix(Y.M.editor_atto.Editor, [EditorClean]);
