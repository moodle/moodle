YUI.add('moodle-atto_phonetic-button', function (Y, NAME) {

/*
 *
 *************************************************************************
 **                         Moodle Terms of uses                        **
 *************************************************************************
 * @author     David Lowe
 * @co-author  Disha Devaiya
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
 *************************************************************************
 *
 * Atto text editor phonetic plugin.
 * @namespace M.atto_phonetic
 * @class Button`
 * @extends M.editor_atto.EditorPlugin
 */
var COMPONENTNAME = 'atto_phonetic',
    LOGNAME = 'atto_phonetic',
    CSS = {
        PHONETIC_TEXT: 'atto_phonetic_phonetic',
        PHONETIC_PREVIEW: 'atto_phonetic_preview',
        PHONETIC_PREVIEW_EACH_CHAR: 'atto_phonetic_preview_each_char',
        PHONETIC_PREVIEW_TEXTAREA: 'atto_phonetic_preview_textarea',
        SUBMIT: 'atto_phonetic_submit',
        LIBRARY: 'atto_phonetic_library',
        LIBRARY_GROUPS: 'atto_phonetic_groups',
        LIBRARY_GROUP_PREFIX: 'atto_phonetic_group',
        LIBRARY_BASE_SYMBOL: 'atto_phonetic_base_symbol'
    },
    SELECTORS = {
        LIBRARY: '.' + CSS.LIBRARY,
        LIBRARY_GROUP: '.' + CSS.LIBRARY_GROUPS + ' > div > div',
        PHONETIC_TEXT: '.' + CSS.PHONETIC_TEXT,
        PHONETIC_PREVIEW: '.' + CSS.PHONETIC_PREVIEW,
        PHONETIC_PREVIEW_EACH_CHAR: '.' + CSS.PHONETIC_PREVIEW_EACH_CHAR,
        SUBMIT: '.' + CSS.SUBMIT,
        LIBRARY_BUTTON: '.' + CSS.LIBRARY + ' button'
    },
    DELIMITERS = {
        // START: '\\[',
        // END: '\\]'
        START: '',
        END: ''
    },
    TEMPLATES = {
        FORM: '' +
            '<form class="atto_form">' +
                '{{{library}}}' +
                '<label for="{{elementid}}_{{CSS.PHONETIC_PREVIEW}}">{{get_string "preview" component}}</label>' +
                '<div class="well well-small fullwidth {{CSS.PHONETIC_PREVIEW}}" >' +
                    '<div class="{{CSS.PHONETIC_PREVIEW_TEXTAREA}}""><textarea class="fullwidth {{CSS.PHONETIC_TEXT}}" ' +
                        'id="{{elementid}}_{{CSS.PHONETIC_TEXT}}" rows="4"></textarea></div>' +
                    '<div class="{{CSS.PHONETIC_PREVIEW_EACH_CHAR}}" id="{{elementid}}_{{CSS.PHONETIC_PREVIEW_EACH_CHAR}}"></div>' +
                '</div>' +
                '<div class="mdl-align">' +
                    '<br/>' +
                    '<button class="{{CSS.SUBMIT}}">{{get_string "savephonetic" component}}</button>' +
                '</div>' +
            '</form>',
        LIBRARY: '' +
            '<div class="{{CSS.LIBRARY}}">' +
                '<ul>' +
                    '{{#each library}}' +
                        '<li><a href="#{{../elementid}}_{{../CSS.LIBRARY_GROUP_PREFIX}}_{{@key}}">' +
                            '{{get_string groupname ../component}}' +
                        '</a></li>' +
                    '{{/each}}' +
                '</ul>' +
                '<div class="{{CSS.LIBRARY_GROUPS}}">' +
                    '{{#each library}}' +
                        '<div id="{{../elementid}}_{{../CSS.LIBRARY_GROUP_PREFIX}}_{{@key}}">' +
                            '<div role="toolbar" data-tex="{{get_string groupname ../component}}">' +
                            '{{#split-phonetic "\n" elements}}' +
                                '<button tabindex="-1" data-tex="{{this}}" aria-label="{{this}}" title="{{this}}" >' +
                                    '{{this}}' +
                                '</button>' +
                            '{{/split-phonetic}}' +
                            '</div>' +
                            '{{#if-phonetic groupname "librarygroup6"}}' +
                            '<div><label class="atto_phonetic_base_symbol">Base Symbol</label>&nbsp;' +
                                '<input size="1" type="text" id="combinatory_input" maxlength="1"/></div>' +
                            '{{/if-phonetic}}' +
                        '</div>' +
                    '{{/each}}' +
                '</div>' +
            '</div>'
    };

Y.namespace('M.atto_phonetic').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {

    /**
     * The selection object returned by the browser.
     *
     * @property _currentSelection
     * @type Range
     * @default null
     * @private
     */
    _currentSelection: null,

    /**
     * A reference to the dialogue content.
     *
     * @property _content
     * @type Node
     * @private
     */
    _content: null,


    /**
     * A reference to the tab focus set on each group.
     *
     * The keys are the IDs of the group, the value is the Node on which the focus is set.
     *
     * @property _groupFocus
     * @type Object
     * @private
     */
    _groupFocus: null,

    /**
     * Regular Expression patterns used to pick out the phonetics in a String.
     *
     * @property _phoneticPatterns
     * @type Array
     * @private
     */
    _phoneticPatterns: [
        // E.g. "\[ blah \]".
        /\\\[([\S\s]+?)\\\]/
    ],

    initializer: function() {
        this._groupFocus = {};

        // If there is a tex filter active - enable this button.
        if (this.get('texfilteractive')) {
            // Add the button to the toolbar.
            this.addButton({
                icon: 'phonetic',
                iconComponent: COMPONENTNAME,
                callback: this._displayDialogue
            });

            // We need custom highlight logic for this button.
            this.get('host').on('atto:selectionchanged', function() {
                var iconurl = M.util.image_url("phonetic", COMPONENTNAME);
                if (this._resolvePhonetic()) {
                    this.highlightButtons();
                    iconurl = M.util.image_url("phonetic-highlight", COMPONENTNAME);
                } else {
                    this.unHighlightButtons();
                }
                var selectedNode = Y.one(".atto_phonetic_button .icon");
                if (selectedNode) {
                    selectedNode.setAttribute("src", iconurl);
                }
            }, this);

            // We need to convert these to a non dom node based format.
            this.editor.all('tex').each(function(texNode) {
                var replacement = Y.Node.create('<span class="wacka_wacka_ding_dong">' +
                        // DELIMITERS.START + ' ' +
                        texNode.get('text') +
                         // ' ' + DELIMITERS.END +
                        '</span>');
                texNode.replace(replacement);
            });
        }
    },
    /**
     * Display the phonetic editor.
     *
     * @method _displayDialogue
     * @private
     */
    _displayDialogue: function() {
        this._currentSelection = this.get('host').getSelection();

        if (this._currentSelection === false) {
            return;
        }

        // This needs to be done before the dialogue is opened because the focus will shift to the dialogue.
        var phonetic = this._resolvePhonetic();

        var dialogue = this.getDialogue({
            headerContent: M.util.get_string('pluginname', COMPONENTNAME),
            focusAfterHide: true,
            width: 600,
            focusOnShowSelector: SELECTORS.PHONETIC_TEXT
        });

        var content = this._getDialogueContent();
        dialogue.set('bodyContent', content);

        var library = content.one(SELECTORS.LIBRARY);

        var tabview = new Y.TabView({
            srcNode: library
        });

        tabview.render();
        dialogue.show();
        // Notify the filters about the modified nodes.
        require(['core/event'], function(event) {
            event.notifyFilterContentUpdated(dialogue.get('boundingBox').getDOMNode());
        });

        if (phonetic) {
            content.one(SELECTORS.PHONETIC_TEXT).set('text', phonetic);
        }
    },

    /**
     * If there is selected text and it is part of an phonetic,
     * extract the phonetic (and set it in the form).
     *
     * @method _resolvePhonetic
     * @private
     * @return {String|Boolean} The phonetic or false.
     */
    _resolvePhonetic: function() {

        // Find the phonetic in the surrounding text.
        var selectedNode = this.get('host').getSelectionParentNode(),
            selection = this.get('host').getSelection(),
            text,
            returnValue = false;

        // Prevent resolving phonetics when we don't have focus.
        if (!this.get('host').isActive()) {
            return false;
        }

        // Note this is a document fragment and YUI doesn't like them.
        if (!selectedNode) {
            return false;
        }

        // We don't yet have a cursor selection somehow so we can't possible be resolving an phonetic that has selection.
        if (!selection || selection.length === 0) {
            return false;
        }

        this.sourcePhonetic = null;

        selection = selection[0];

        text = Y.one(selectedNode).get('text');

        // For each of these patterns we have a RegExp which captures the inner component of the phonetic but also
        // includes the delimiters.
        // We first run the RegExp adding the global flag ("g"). This ignores the capture, instead matching the entire
        // phonetic including delimiters and returning one entry per match of the whole phonetic.
        // We have to deal with multiple occurences of the same phonetic in a String so must be able to loop on the
        // match results.
        Y.Array.find(this._phoneticPatterns, function (pattern) {
            // For each pattern in turn, find all whole matches (including the delimiters).
            var patternMatches = text.match(new RegExp(pattern.source, "g"));

            if (patternMatches && patternMatches.length) {
                // This pattern matches at least once. See if this pattern matches our current position.
                // Note: We return here to break the Y.Array.find loop - any truthy return will stop any subsequent
                // searches which is the required behaviour of this function.
                return Y.Array.find(patternMatches, function (match) {
                    // Check each occurrence of this match.
                    var startIndex = 0;
                    while (text.indexOf(match, startIndex) !== -1) {
                        // Determine whether the cursor is in the current occurrence of this string.
                        // Note: We do not support a selection exceeding the bounds of an phonetic.
                        var startOuter = text.indexOf(match, startIndex),
                            endOuter = startOuter + match.length,
                            startMatch = (selection.startOffset >= startOuter && selection.startOffset < endOuter),
                            endMatch = (selection.endOffset <= endOuter && selection.endOffset > startOuter);

                        if (startMatch && endMatch) {
                            // This match is in our current position - fetch the innerMatch data.
                            var innerMatch = match.match(pattern);
                            if (innerMatch && innerMatch.length) {
                                // We need the start and end of the inner match for later.
                                var startInner = text.indexOf(innerMatch[1], startOuter),
                                    endInner = startInner + innerMatch[1].length;

                                // We'll be returning the inner match for use in the editor itself.
                                returnValue = innerMatch[1];

                                // Save all data for later.
                                this.sourcePhonetic = {
                                    // Outer match data.
                                    startOuterPosition: startOuter,
                                    endOuterPosition: endOuter,
                                    outerMatch: match,

                                    // Inner match data.
                                    startInnerPosition: startInner,
                                    endInnerPosition: endInner,
                                    innerMatch: innerMatch
                                };

                                // This breaks out of both Y.Array.find functions.
                                return true;
                            }
                        }

                        // Update the startIndex to match the end of the current match so that we can continue hunting
                        // for further matches.
                        startIndex = endOuter;
                    }
                }, this);
            }
        }, this);

        // We trim the phonetic when we load it and then add spaces when we save it.
        if (returnValue !== false) {
            returnValue = returnValue.trim();
        }
        return returnValue;
    },

    /**
     * Handle insertion of a new phonetic, or update of an existing one.
     *
     * @method _setPhonetic
     * @param {EventFacade} e
     * @private
     */
    _setPhonetic: function(e) {
        var input,
            selectedNode,
            text,
            value,
            host,
            newText;

        host = this.get('host');

        e.preventDefault();
        this.getDialogue({
            focusAfterHide: null
        }).hide();

        input = e.currentTarget.ancestor('.atto_form').one('textarea');

        value = input.get('value');

        if (value !== '') {
            host.setSelection(this._currentSelection);

            if (this.sourcePhonetic) {
                // Replace the phonetic.
                selectedNode = Y.one(host.getSelectionParentNode());
                text = selectedNode.get('text');
                // value = ' ' + value + ' ';
                newText = text.slice(0, this.sourcePhonetic.startInnerPosition) +
                            value +
                            text.slice(this.sourcePhonetic.endInnerPosition);

                selectedNode.set('text', newText);
            } else {
                // Insert the new phonetic.
                // value = DELIMITERS.START + ' ' + value + ' ' + DELIMITERS.END;
                host.insertContentAtFocusPoint(value);
            }

            // Clean the YUI ids from the HTML.
            this.markUpdated();
        }
    },

    /**
     * Smart throttle, only call a function every delay milli seconds,
     * and always run the last call. Y.throttle does not work here,
     * because it calls the function immediately, the first time, and then
     * ignores repeated calls within X seconds. This does not guarantee
     * that the last call will be executed (which is required here).
     *
     * @param {function} fn
     * @param {Number} delay Delay in milliseconds
     * @method _throttle
     * @private
     */
    _throttle: function(fn, delay) {
        var timer = null;
        return function() {
            var context = this,
                args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function() {
              fn.apply(context, args);
            }, delay);
        };
    },

    /**
     * Load returned preview text into preview
     */
    _loadPreview: function(e) {

        var tex = e.currentTarget.getAttribute('data-tex');
        var selectedGroupTab = e.currentTarget.get('parentNode').getAttribute('data-tex');
        if (selectedGroupTab == "Combining") {
            tex = tex.substring(1, tex.length);
        }
        e.preventDefault();
        // Set Phonetic in preview node
        var previewNode = this._content.one(SELECTORS.PHONETIC_PREVIEW_EACH_CHAR);
        previewNode.setHTML(tex);
    },

    /**
     * Return the dialogue content for the tool, attaching any required
     * events.
     *
     * @method _getDialogueContent
     * @return {Node}
     * @private
     */
    _getDialogueContent: function() {
        var library = this._getLibraryContent(),
            template = Y.Handlebars.compile(TEMPLATES.FORM);

        this._content = Y.Node.create(template({
            elementid: this.get('host').get('elementid'),
            component: COMPONENTNAME,
            library: library,
            texdocsurl: this.get('texdocsurl'),
            CSS: CSS
        }));

        // Sets the default focus.
        this._content.all(SELECTORS.LIBRARY_GROUP).each(function(group) {
            // The first button gets the focus.
            this._setGroupTabFocus(group, group.one('button'));
            // Sometimes the filter adds an anchor in the button, no tabindex on that.
            group.all('button a').setAttribute('tabindex', '-1');
        }, this);

        // Keyboard navigation in groups.
        this._content.delegate('key', this._groupNavigation, 'down:37,39', SELECTORS.LIBRARY_BUTTON, this);

        this._content.one(SELECTORS.SUBMIT).on('click', this._setPhonetic, this);
        this._content.delegate('click', this._selectLibraryItem, SELECTORS.LIBRARY_BUTTON, this);
        this._content.delegate('mouseenter', this._loadPreview, SELECTORS.LIBRARY_BUTTON, this);

        return this._content;
    },

    /**
     * Callback handling the keyboard navigation in the groups of the library.
     *
     * @param {EventFacade} e The event.
     * @method _groupNavigation
     * @private
     */
    _groupNavigation: function(e) {
        e.preventDefault();

        var current = e.currentTarget,
            parent = current.get('parentNode'), // This must be the <div> containing all the buttons of the group.
            buttons = parent.all('button'),
            direction = e.keyCode !== 37 ? 1 : -1,
            index = buttons.indexOf(current),
            nextButton;

        if (index < 0) {
            Y.log('Unable to find the current button in the list of buttons', 'debug', LOGNAME);
            index = 0;
        }

        index += direction;
        if (index < 0) {
            index = buttons.size() - 1;
        } else if (index >= buttons.size()) {
            index = 0;
        }
        nextButton = buttons.item(index);

        this._setGroupTabFocus(parent, nextButton);
        nextButton.focus();
    },

    /**
     * Sets tab focus for the group.
     *
     * @method _setGroupTabFocus
     * @param {Node} button The node that focus should now be set to.
     * @private
     */
    _setGroupTabFocus: function(parent, button) {
        var parentId = parent.generateID();

        // Unset the previous entry.
        if (typeof this._groupFocus[parentId] !== 'undefined') {
            this._groupFocus[parentId].setAttribute('tabindex', '-1');
        }

        // Set on the new entry.
        if (button !== null) {
            this._groupFocus[parentId] = button;
            button.setAttribute('tabindex', 0);
            parent.setAttribute('aria-activedescendant', button.generateID());
        }
    },

    /**
     * Reponse to button presses in the TeX library panels.
     *
     * @method _selectLibraryItem
     * @param {EventFacade} e
     * @return {string}
     * @private
     */
    _selectLibraryItem: function(e) {
        var tex = e.currentTarget.getAttribute('data-tex'),
        focusPoint = 0;
        var selectedGroupTab = e.currentTarget.get('parentNode').getAttribute('data-tex');
        e.preventDefault();

        // Set the group focus on the button.
        this._setGroupTabFocus(e.currentTarget.get('parentNode'), e.currentTarget);

        // Set Phonetic in preview
        var previewNode = this._content.one(SELECTORS.PHONETIC_PREVIEW_EACH_CHAR);

        var input = e.currentTarget.ancestor('.atto_form').one('textarea');

        var oldValue = input.get('value');

        var newValue = "";
        if (selectedGroupTab == "Combining") {
            tex = tex.substring(1, tex.length);
            previewNode.setHTML(tex);
            var inputSymbol = document.getElementById("combinatory_input").value;
            if (inputSymbol != "") {
                tex = inputSymbol + tex;
            }
            newValue = oldValue + tex;
        } else {
            previewNode.setHTML(tex);
            // newValue = oldValue + " " + tex;
            newValue = oldValue + tex;
        }

        input.set('value', newValue);
        input.focus();
        focusPoint = newValue.length;

        var realInput = input.getDOMNode();
        if (typeof realInput.selectionStart === "number") {
            // Modern browsers have selectionStart and selectionEnd to control the cursor position.
            realInput.selectionStart = realInput.selectionEnd = focusPoint;
        } else if (typeof realInput.createTextRange !== "undefined") {
            // Legacy browsers (IE<=9) use createTextRange().
            var range = realInput.createTextRange();
            range.moveToPoint(focusPoint);
            range.select();
        }

    },
    /**
     * Return the HTML for rendering the library of predefined buttons.
     *
     * @method _getLibraryContent
     * @return {string}
     * @private
     */
    _getLibraryContent: function() {

        var template = Y.Handlebars.compile(TEMPLATES.LIBRARY),
            library = this.get('library'),
            content = '';

        // Helper to iterate over a newline separated string.
        Y.Handlebars.registerHelper('split-phonetic', function(delimiter, str, options) {
            var parts,
                current,
                out;
            if (typeof delimiter === "undefined" || typeof str === "undefined") {
                Y.log(
                    'Handlebars split-phonetic helper: String and delimiter are required.',
                    'debug',
                    'moodle-atto_phonetic-button'
                );
                return '';
            }

            out = '';
            parts = str.trim().split(delimiter);
            while (parts.length > 0) {
                current = parts.shift().trim();
                if (options.data.key == "group6") {
                    current = "X" + current;
                }
                out += options.fn(current);
            }

            return out;
        });

        Y.Handlebars.registerHelper('if-phonetic', function(v1, v2, options) {
        if (v1 === v2) {
            return options.fn(this);
        }
            return options.inverse(this);
        });

        content = template({
            elementid: this.get('host').get('elementid'),
            component: COMPONENTNAME,
            library: library,
            CSS: CSS,
            DELIMITERS: DELIMITERS
        });
        return content;
    }
}, {
    ATTRS: {
        /**
         * Whether the TeX filter is currently active.
         *
         * @attribute texfilteractive
         * @type Boolean
         */
        texfilteractive: {
            value: true
        },

        /**
         * The contextid to use when generating this preview.
         *
         * @attribute contextid
         * @type String
         */
        contextid: {
            value: null
        },

        /**
         * The content of the example library.
         *
         * @attribute library
         * @type object
         */
        library: {
            value: {}
        },

        /**
         * The link to the Moodle Docs page about TeX.
         *
         * @attribute texdocsurl
         * @type string
         */
        texdocsurl: {
            value: null
        }

    }
});


}, '@VERSION@', {
    "requires": [
        "moodle-editor_atto-plugin",
        "moodle-core-event",
        "io",
        "event-valuechange",
        "tabview",
        "array-extras"
    ]
});
