/**
 * The generic dialogue class for use in Moodle.
 *
 * @module moodle-core-notification
 * @submodule moodle-core-notification-dialogue
 */

var DIALOGUE_NAME = 'Moodle dialogue',
    DIALOGUE,
    DIALOGUE_FULLSCREEN_CLASS = DIALOGUE_PREFIX + '-fullscreen',
    DIALOGUE_HIDDEN_CLASS = DIALOGUE_PREFIX + '-hidden',
    DIALOGUE_SELECTOR =' [role=dialog]',
    MENUBAR_SELECTOR = '[role=menubar]',
    DOT = '.',
    HAS_ZINDEX = 'moodle-has-zindex',
    CAN_RECEIVE_FOCUS_SELECTOR = 'input:not([type="hidden"]), a[href], button, textarea, select, [tabindex]';

/**
 * A re-usable dialogue box with Moodle classes applied.
 *
 * @param {Object} c Object literal specifying the dialogue configuration properties.
 * @constructor
 * @class M.core.dialogue
 * @extends Panel
 */
DIALOGUE = function(c) {
    var config = Y.clone(c);
    config.COUNT = Y.stamp(this);
    var id = 'moodle-dialogue-' + config.COUNT;
    config.notificationBase =
        Y.Node.create('<div class="'+CSS.BASE+'">')
              .append(Y.Node.create('<div id="' + id + '" role="dialog" ' +
                                    'aria-labelledby="' + id + '-header-text" class="' + CSS.WRAP + '"></div>')
              .append(Y.Node.create('<div id="' + id + '-header-text" class="'+CSS.HEADER+' yui3-widget-hd"></div>'))
              .append(Y.Node.create('<div class="'+CSS.BODY+' yui3-widget-bd"></div>'))
              .append(Y.Node.create('<div class="'+CSS.FOOTER+' yui3-widget-ft"></div>')));
    Y.one(document.body).append(config.notificationBase);

    if (config.additionalBaseClass) {
        config.notificationBase.addClass(config.additionalBaseClass);
    }

    config.srcNode =    '#'+id;

    // closeButton param to keep the stable versions API.
    if (config.closeButton === false) {
        config.buttons = null;
    } else {
        config.buttons = [
            {
                section: Y.WidgetStdMod.HEADER,
                classNames: 'closebutton',
                action: function () {
                    this.hide();
                }
            }
        ];
    }
    DIALOGUE.superclass.constructor.apply(this, [config]);

    if (config.closeButton !== false) {
        // The buttons constructor does not allow custom attributes
        this.get('buttons').header[0].setAttribute('title', this.get('closeButtonTitle'));
    }
};
Y.extend(DIALOGUE, Y.Panel, {
    // Window resize event listener.
    _resizeevent : null,
    // Orientation change event listener.
    _orientationevent : null,
    _calculatedzindex : false,

    /**
     * The original position of the dialogue before it was reposition to
     * avoid browser jumping.
     *
     * @property _originalPosition
     * @protected
     * @type Array
     */
    _originalPosition: null,

    /**
     * Initialise the dialogue.
     *
     * @method initializer
     */
    initializer : function() {
        var bb;

        if (this.get('render')) {
            this.render();
        }
        this.after('visibleChange', this.visibilityChanged, this);
        if (this.get('center')) {
            this.centerDialogue();
        }

        if (this.get('modal')) {
            this.plug(Y.M.core.LockScroll);
        }

        // Workaround upstream YUI bug http://yuilibrary.com/projects/yui3/ticket/2532507
        // and allow setting of z-index in theme.
        bb = this.get('boundingBox');
        bb.addClass(HAS_ZINDEX);

        // Add any additional classes that were specified.
        Y.Array.each(this.get('extraClasses'), bb.addClass, bb);

        if (this.get('visible')) {
            this.applyZIndex();
        }
        // Recalculate the zIndex every time the modal is altered.
        this.on('maskShow', this.applyZIndex);

        this.on('maskShow', function() {
            // When the mask shows, position the boundingBox at the top-left of the window such that when it is
            // focused, the position does not change.
            var w = Y.one(Y.config.win),
                bb = this.get('boundingBox');

            if (!this.get('center')) {
                this._originalPosition = bb.getXY();
            }

            if (bb.getStyle('position') !== 'fixed') {
                // If the boundingBox has been positioned in a fixed manner, then it will not position correctly to scrollTop.
                bb.setStyles({
                    top: w.get('scrollTop'),
                    left: w.get('scrollLeft')
                });
            }
        }, this);

        // Remove the dialogue from the DOM when it is destroyed.
        this.after('destroyedChange', function(){
            this.get(BASE).remove(true);
        }, this);
    },

    /**
     * Either set the zindex to the supplied value, or set it to one more than the highest existing
     * dialog in the page.
     *
     * @method applyZIndex
     */
    applyZIndex : function() {
        var highestzindex = 1,
            zindexvalue = 1,
            bb = this.get('boundingBox'),
            ol = this.get('maskNode'),
            zindex = this.get('zIndex');
        if (zindex !== 0 && !this._calculatedzindex) {
            // The zindex was specified so we should use that.
            bb.setStyle('zIndex', zindex);
        } else {
            // Determine the correct zindex by looking at all existing dialogs and menubars in the page.
            Y.all(DIALOGUE_SELECTOR + ', ' + MENUBAR_SELECTOR + ', ' + DOT + HAS_ZINDEX).each(function (node) {
                var zindex = this.findZIndex(node);
                if (zindex > highestzindex) {
                    highestzindex = zindex;
                }
            }, this);
            // Only set the zindex if we found a wrapper.
            zindexvalue = (highestzindex + 1).toString();
            bb.setStyle('zIndex', zindexvalue);
            this.set('zIndex', zindexvalue);
            if (this.get('modal')) {
                ol.setStyle('zIndex', zindexvalue);

                // In IE8, the z-indexes do not take effect properly unless you toggle
                // the lightbox from 'fixed' to 'static' and back. This code does so
                // using the minimum setTimeouts that still actually work.
                if (Y.UA.ie && Y.UA.compareVersions(Y.UA.ie, 9) < 0) {
                    setTimeout(function() {
                        ol.setStyle('position', 'static');
                        setTimeout(function() {
                            ol.setStyle('position', 'fixed');
                        }, 0);
                    }, 0);
                }
            }
            this._calculatedzindex = true;
        }
    },

    /**
     * Finds the zIndex of the given node or its parent.
     *
     * @method findZIndex
     * @param {Node} node The Node to apply the zIndex to.
     * @return {Number} Either the zIndex, or 0 if one was not found.
     */
    findZIndex : function(node) {
        // In most cases the zindex is set on the parent of the dialog.
        var zindex = node.getStyle('zIndex') || node.ancestor().getStyle('zIndex');
        if (zindex) {
            return parseInt(zindex, 10);
        }
        return 0;
    },

    /**
     * Event listener for the visibility changed event.
     *
     * @method visibilityChanged
     * @param {EventFacade} e
     */
    visibilityChanged : function(e) {
        var titlebar, bb;
        if (e.attrName === 'visible') {
            this.get('maskNode').addClass(CSS.LIGHTBOX);
            if (e.prevVal && !e.newVal) {
                bb = this.get('boundingBox');
                if (this._resizeevent) {
                    this._resizeevent.detach();
                    this._resizeevent = null;
                }
                if (this._orientationevent) {
                    this._orientationevent.detach();
                    this._orientationevent = null;
                }
                bb.detach('key', this.keyDelegation);
            }
            if (!e.prevVal && e.newVal) {
                // This needs to be done each time the dialog is shown as new dialogs may have been opened.
                this.applyZIndex();
                // This needs to be done each time the dialog is shown as the window may have been resized.
                this.makeResponsive();
                if (!this.shouldResizeFullscreen()) {
                    if (this.get('draggable')) {
                        titlebar = '#' + this.get('id') + ' .' + CSS.HEADER;
                        this.plug(Y.Plugin.Drag, {handles : [titlebar]});
                        Y.one(titlebar).setStyle('cursor', 'move');
                    }
                }
                this.keyDelegation();
            }
            if (this.get('center') && !e.prevVal && e.newVal) {
                this.centerDialogue();
            }
        }
    },
    /**
     * If the responsive attribute is set on the dialog, and the window size is
     * smaller than the responsive width - make the dialog fullscreen.
     *
     * @method makeResponsive
     */
    makeResponsive : function() {
        var bb = this.get('boundingBox');

        if (this.shouldResizeFullscreen()) {
            // Make this dialogue fullscreen on a small screen.
            // Disable the page scrollbars.

            // Size and position the fullscreen dialog.

            bb.addClass(DIALOGUE_FULLSCREEN_CLASS);
            bb.setStyles({'left' : null,
                          'top' : null,
                          'width' : null,
                          'height' : null,
                          'right' : null,
                          'bottom' : null});
        } else {
            if (this.get('responsive')) {
                // We must reset any of the fullscreen changes.
                bb.removeClass(DIALOGUE_FULLSCREEN_CLASS)
                    .setStyles({'width' : this.get('width'),
                                'height' : this.get('height')});
            }
        }
    },
    /**
     * Center the dialog on the screen.
     *
     * @method centerDialogue
     */
    centerDialogue : function() {
        var bb = this.get('boundingBox'),
            hidden = bb.hasClass(DIALOGUE_HIDDEN_CLASS),
            x,
            y;

        // Don't adjust the position if we are in full screen mode.
        if (this.shouldResizeFullscreen()) {
            return;
        }
        if (hidden) {
            bb.setStyle('top', '-1000px').removeClass(DIALOGUE_HIDDEN_CLASS);
        }
        x = Math.max(Math.round((bb.get('winWidth') - bb.get('offsetWidth'))/2), 15);
        y = Math.max(Math.round((bb.get('winHeight') - bb.get('offsetHeight'))/2), 15) + Y.one(window).get('scrollTop');
        bb.setStyles({ 'left' : x, 'top' : y});

        if (hidden) {
            bb.addClass(DIALOGUE_HIDDEN_CLASS);
        }
        this.makeResponsive();
    },
    /**
     * Return whether this dialogue should be fullscreen or not.
     *
     * Responsive attribute must be true and we should not be in an iframe and the screen width should
     * be less than the responsive width.
     *
     * @method shouldResizeFullscreen
     * @return {Boolean}
     */
    shouldResizeFullscreen : function() {
        return (window === window.parent) && this.get('responsive') &&
               Math.floor(Y.one(document.body).get('winWidth')) < this.get('responsiveWidth');
    },

    show: function() {
        var result = null,
            header = this.headerNode,
            content = this.bodyNode,
            focusSelector = this.get('focusOnShowSelector'),
            focusNode = null;

        result = DIALOGUE.superclass.show.call(this);

        if (!this.get('center') && this._originalPosition) {
            // Restore the dialogue position to it's location before it was moved at show time.
            this.get('boundingBox').setXY(this._originalPosition);
        }

        // Lock scroll if the plugin is present.
        if (this.lockScroll) {
            // We need to force the scroll locking for full screen dialogues, even if they have a small vertical size to
            // prevent the background scrolling while the dialogue is open.
            this.lockScroll.enableScrollLock(this.shouldResizeFullscreen());
        }

        // Try and find a node to focus on using the focusOnShowSelector attribute.
        if (focusSelector !== null) {
            focusNode = this.get('boundingBox').one(focusSelector);
        }
        if (!focusNode) {
            // Fall back to the header or the content if no focus node was found yet.
            if (header && header !== '') {
                focusNode = header;
            } else if (content && content !== '') {
                focusNode = content;
            }
        }
        if (focusNode) {
            focusNode.focus();
        }
        return result;
    },

    hide: function(e) {
        if (e) {
            // If the event was closed by an escape key event, then we need to check that this
            // dialogue is currently focused to prevent closing all dialogues in the stack.
            if (e.type === 'key' && e.keyCode === 27 && !this.get('focused')) {
                return;
            }
        }

        // Unlock scroll if the plugin is present.
        if (this.lockScroll) {
            this.lockScroll.disableScrollLock();
        }

        return DIALOGUE.superclass.hide.call(this, arguments);
    },
    /**
     * Setup key delegation to keep tabbing within the open dialogue.
     *
     * @method keyDelegation
     */
    keyDelegation : function() {
        var bb = this.get('boundingBox');
        bb.delegate('key', function(e){
            var target = e.target;
            var direction = 'forward';
            if (e.shiftKey) {
                direction = 'backward';
            }
            if (this.trapFocus(target, direction)) {
                e.preventDefault();
            }
        }, 'down:9', CAN_RECEIVE_FOCUS_SELECTOR, this);
    },

    /**
     * Trap the tab focus within the open modal.
     *
     * @method trapFocus
     * @param {string} target the element target
     * @param {string} direction tab key for forward and tab+shift for backward
     * @return {Boolean} The result of the focus action.
     */
    trapFocus : function(target, direction) {
        var bb = this.get('boundingBox'),
            firstitem = bb.one(CAN_RECEIVE_FOCUS_SELECTOR),
            lastitem = bb.all(CAN_RECEIVE_FOCUS_SELECTOR).pop();

        if (target === lastitem && direction === 'forward') { // Tab key.
            return firstitem.focus();
        } else if (target === firstitem && direction === 'backward') {  // Tab+shift key.
            return lastitem.focus();
        }
    }
}, {
    NAME : DIALOGUE_NAME,
    CSS_PREFIX : DIALOGUE_PREFIX,
    ATTRS : {
        notificationBase : {

        },

        /**
         * Whether to display the dialogue modally and with a
         * lightbox style.
         *
         * @attribute lightbox
         * @type Boolean
         * @default true
         * @deprecated Since Moodle 2.7. Please use modal instead.
         */
        lightbox: {
            lazyAdd: false,
            setter: function(value) {
                Y.log("The lightbox attribute of M.core.dialogue has been deprecated since Moodle 2.7, " +
                      "please use the modal attribute instead",
                    'warn', 'moodle-core-notification-dialogue');
                this.set('modal', value);
            }
        },

        /**
         * Whether to display a close button on the dialogue.
         *
         * Note, we do not recommend hiding the close button as this has
         * potential accessibility concerns.
         *
         * @attribute closeButton
         * @type Boolean
         * @default true
         */
        closeButton : {
            validator : Y.Lang.isBoolean,
            value : true
        },

        /**
         * The title for the close button if one is to be shown.
         *
         * @attribute closeButtonTitle
         * @type String
         * @default 'Close'
         */
        closeButtonTitle : {
            validator : Y.Lang.isString,
            value: M.util.get_string('closebuttontitle', 'moodle')
        },

        /**
         * Whether to display the dialogue centrally on the screen.
         *
         * @attribute center
         * @type Boolean
         * @default true
         */
        center : {
            validator : Y.Lang.isBoolean,
            value : true
        },

        /**
         * Whether to make the dialogue movable around the page.
         *
         * @attribute draggable
         * @type Boolean
         * @default false
         */
        draggable : {
            validator : Y.Lang.isBoolean,
            value : false
        },

        /**
         * Used to generate a unique id for the dialogue.
         *
         * @attribute COUNT
         * @type String
         * @default null
         */
        COUNT: {
            value: null
        },

        /**
         * Used to disable the fullscreen resizing behaviour if required.
         *
         * @attribute responsive
         * @type Boolean
         * @default true
         */
        responsive : {
            validator : Y.Lang.isBoolean,
            value : true
        },

        /**
         * The width that this dialogue should be resized to fullscreen.
         *
         * @attribute responsiveWidth
         * @type Number
         * @default 768
         */
        responsiveWidth : {
            value : 768
        },

        /**
         * Selector to a node that should recieve focus when this dialogue is shown.
         *
         * The default behaviour is to focus on the header.
         *
         * @attribute focusOnShowSelector
         * @default null
         * @type String
         */
        focusOnShowSelector: {
            value: null
        }

    }
});

Y.Base.modifyAttrs(DIALOGUE, {
    /**
     * String with units, or number, representing the width of the Widget.
     * If a number is provided, the default unit, defined by the Widgets
     * DEF_UNIT, property is used.
     *
     * If a value of 'auto' is used, then an empty String is instead
     * returned.
     *
     * @attribute width
     * @default '400px'
     * @type {String|Number}
     */
    width: {
        value: '400px',
        setter: function(value) {
            if (value === 'auto') {
                return '';
            }
            return value;
        }
    },

    /**
     * Boolean indicating whether or not the Widget is visible.
     *
     * We override this from the default Widget attribute value.
     *
     * @attribute visible
     * @default false
     * @type Boolean
     */
    visible: {
        value: false
    },

    /**
     * A convenience Attribute, which can be used as a shortcut for the
     * `align` Attribute.
     *
     * Note: We override this in Moodle such that it sets a value for the
     * `center` attribute if set. The `centered` will always return false.
     *
     * @attribute centered
     * @type Boolean|Node
     * @default false
     */
    centered: {
        setter: function(value) {
            if (value) {
                this.set('center', true);
            }
            return false;
        }
    },

    /**
     * Boolean determining whether to render the widget during initialisation.
     *
     * We override this to change the default from false to true for the dialogue.
     * We then proceed to early render the dialogue during our initialisation rather than waiting
     * for YUI to render it after that.
     *
     * @attribute render
     * @type Boolean
     * @default true
     */
    render : {
        value : true,
        writeOnce : true
    },

    /**
     * Any additional classes to add to the boundingBox.
     *
     * @attribute extraClasses
     * @type Array
     * @default []
     */
    extraClasses: {
        value: []
    }
});

Y.Base.mix(DIALOGUE, [Y.M.core.WidgetFocusAfterHide]);

M.core.dialogue = DIALOGUE;
