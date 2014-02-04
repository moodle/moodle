YUI.add('moodle-core-notification-dialogue', function (Y, NAME) {

var DIALOGUE_PREFIX,
    BASE,
    COUNT,
    CONFIRMYES,
    CONFIRMNO,
    TITLE,
    QUESTION,
    CSS;

DIALOGUE_PREFIX = 'moodle-dialogue',
BASE = 'notificationBase',
COUNT = 0,
CONFIRMYES = 'yesLabel',
CONFIRMNO = 'noLabel',
TITLE = 'title',
QUESTION = 'question',
CSS = {
    BASE : 'moodle-dialogue-base',
    WRAP : 'moodle-dialogue-wrap',
    HEADER : 'moodle-dialogue-hd',
    BODY : 'moodle-dialogue-bd',
    CONTENT : 'moodle-dialogue-content',
    FOOTER : 'moodle-dialogue-ft',
    HIDDEN : 'hidden',
    LIGHTBOX : 'moodle-dialogue-lightbox'
};

// Set up the namespace once.
M.core = M.core || {};
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
    HAS_ZINDEX = '.moodle-has-zindex',
    CAN_RECEIVE_FOCUS_SELECTOR = 'input:not([type="hidden"]), a[href], button, textarea, select, [tabindex]';

/**
 * A re-usable dialogue box with Moodle classes applied.
 *
 * @param {Object} c Object literal specifying the dialogue configuration properties.
 * @constructor
 * @class M.core.dialogue
 * @extends Y.Panel
 */
DIALOGUE = function(c) {
    var config = Y.clone(c);
    COUNT++;
    var id = 'moodle-dialogue-'+COUNT;
    config.notificationBase =
        Y.Node.create('<div class="'+CSS.BASE+'">')
              .append(Y.Node.create('<div id="'+id+'" role="dialog" aria-labelledby="'+id+'-header-text" class="'+CSS.WRAP+'"></div>')
              .append(Y.Node.create('<div id="'+id+'-header-text" class="'+CSS.HEADER+' yui3-widget-hd"></div>'))
              .append(Y.Node.create('<div class="'+CSS.BODY+' yui3-widget-bd"></div>'))
              .append(Y.Node.create('<div class="'+CSS.FOOTER+' yui3-widget-ft"></div>')));
    Y.one(document.body).append(config.notificationBase);

    if (config.additionalBaseClass) {
        config.notificationBase.addClass(config.additionalBaseClass);
    }

    config.srcNode =    '#'+id;
    config.render =     (typeof config.render !== 'undefined') ? config.render : true;
    config.width =      config.width || '400px';
    if (typeof config.center === 'undefined') {
        config.center = true;
    } else {
        config.center = config.centered && true;
    }
    config.centered =   false;
    config.COUNT = COUNT;

    if (config.width === 'auto') {
        delete config.width;
    }

    // lightbox param to keep the stable versions API.
    if (config.lightbox !== false) {
        config.modal = true;
    }
    delete config.lightbox;

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
     * Initialise the dialogue.
     *
     * @method initializer
     * @return void
     */
    initializer : function(config) {
        var bb;

        if (config.render && !this.get('rendered')) {
            this.render();
        }

        this.makeResponsive();
        this.after('visibleChange', this.visibilityChanged, this);
        if (config.center) {
            this.centerDialogue();
        }
        this.set('COUNT', COUNT);

        if (this.get('modal')) {
            this.plug(Y.M.core.LockScroll);
        }

        // Workaround upstream YUI bug http://yuilibrary.com/projects/yui3/ticket/2532507
        // and allow setting of z-index in theme.
        bb = this.get('boundingBox');

        if (config.extraClasses) {
            Y.Array.each(config.extraClasses, bb.addClass, bb);
        }
        if (config.visible) {
            this.applyZIndex();
        }
        // Recalculate the zIndex every time the modal is altered.
        this.on('maskShow', this.applyZIndex);
        // We must show - after the dialogue has been positioned,
        // either by centerDialogue or makeResonsive. This is because the show() will trigger
        // a focus on the dialogue, which will scroll the page. If the dialogue has not
        // been positioned it will scroll back to the top of the page.
        if (config.visible) {
            this.show();
            this.keyDelegation();
        }
    },

    /**
     * Either set the zindex to the supplied value, or set it to one more than the highest existing
     * dialog in the page.
     *
     * @method visibilityChanged
     * @return void
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
            Y.all(DIALOGUE_SELECTOR+', '+MENUBAR_SELECTOR+', '+HAS_ZINDEX).each(function (node) {
                var zindex = this.findZIndex(node);
                if (zindex > highestzindex) {
                    highestzindex = zindex;
                }
            }, this);
            // Only set the zindex if we found a wrapper.
            zindexvalue = (highestzindex + 1).toString();
            bb.setStyle('zIndex', zindexvalue);
            ol.setStyle('zIndex', zindexvalue);
            this.set('zIndex', zindexvalue);
            this._calculatedzindex = true;
        }
    },

    /**
     * Finds the zIndex of the given node or its parent.
     *
     * @method findZIndex
     * @param Node node
     * @returns int Return either the zIndex of 0 if one was not found.
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
     * @return void
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
     * @return void
     */
    makeResponsive : function() {
        var bb = this.get('boundingBox'),
            content;

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

            content = Y.one('#' + this.get('id') + ' .' + CSS.BODY);
        } else {
            if (this.get('responsive')) {
                // We must reset any of the fullscreen changes.
                bb.removeClass(DIALOGUE_FULLSCREEN_CLASS)
                    .setStyles({'width' : this.get('width'),
                                'height' : this.get('height')});
                content = Y.one('#' + this.get('id') + ' .' + CSS.BODY);
            }
        }
    },
    /**
     * Center the dialog on the screen.
     *
     * @method centerDialogue
     * @return void
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
    },
    /**
     * Return if this dialogue should be fullscreen or not.
     * Responsive attribute must be true and we should not be in an iframe and the screen width should
     * be less than the responsive width.
     *
     * @method shouldResizeFullscreen
     * @return Boolean
     */
    shouldResizeFullscreen : function() {
        return (window === window.parent) && this.get('responsive') &&
               Math.floor(Y.one(document.body).get('winWidth')) < this.get('responsiveWidth');
    },

    /**
     * Override the show method to set keyboard focus on the dialogue.
     *
     * @method show
     * @return void
     */
    show : function() {
        var result = null,
            header = this.headerNode,
            content = this.bodyNode;

        // Lock scroll if the plugin is present.
        if (this.lockScroll) {
            this.lockScroll.enableScrollLock();
        }

        result = DIALOGUE.superclass.show.call(this);
        if (header && header !== '') {
            header.focus();
        } else if (content && content !== '') {
            content.focus();
        }
        return result;
    },

    hide: function() {
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
     * @param string target the element target
     * @param string direction tab key for forward and tab+shift for backward
     * @returns bool
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
         */
        lightbox : {
            validator : Y.Lang.isBoolean,
            value : true
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
            value : 'Close'
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
         * @type Integer
         * @default 0
         */
        COUNT: {
            value: 0
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
         * @type Integer
         * @default 768
         */
        responsiveWidth : {
            value : 768
        }
    }
});

M.core.dialogue = DIALOGUE;


}, '@VERSION@', {"requires": ["base", "node", "panel", "event-key", "dd-plugin", "moodle-core-lockscroll"]});
