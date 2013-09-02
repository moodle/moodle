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
    DIALOGUE_MODAL_CLASS = 'yui3-widget-modal',
    DIALOGUE_SELECTOR =' [role=dialog]',
    MENUBAR_SELECTOR = '[role=menubar]',
    NOSCROLLING_CLASS = 'no-scrolling';

/**
 * A re-usable dialogue box with Moodle classes applied.
 *
 * @param {Object} config Object literal specifying the dialogue configuration properties.
 * @constructor
 * @class M.core.dialogue
 * @extends Y.Panel
 */
DIALOGUE = function(config) {
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
    config.width =      config.width || '400px';
    config.visible =    config.visible || false;
    config.center =     config.centered && true;
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

    /**
     * Initialise the dialogue.
     *
     * @method initializer
     * @return void
     */
    initializer : function(config) {
        var bb;

        this.render();
        this.show();
        this.after('visibleChange', this.visibilityChanged, this);
        if (config.center) {
            this.centerDialogue();
        }
        if (!config.visible) {
            this.hide();
        }
        this.set('COUNT', COUNT);

        // Workaround upstream YUI bug http://yuilibrary.com/projects/yui3/ticket/2532507
        // and allow setting of z-index in theme.
        bb = this.get('boundingBox');

        if (config.extraClasses) {
            Y.Array.each(config.extraClasses, bb.addClass, bb);
        }
        if (config.visible) {
            this.applyZIndex();
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
        var highestzindex = 0,
            bb = this.get('boundingBox'),
            zindex = this.get('zIndex');
        if (zindex) {
            // The zindex was specified so we should use that.
            bb.setStyle('zIndex', zindex);
        } else {
            // Determine the correct zindex by looking at all existing dialogs and menubars in the page.
            Y.all(DIALOGUE_SELECTOR+', '+MENUBAR_SELECTOR).each(function (node) {
                var zindex = this.findZIndex(node);
                if (zindex > highestzindex) {
                    highestzindex = zindex;
                }
            }, this);
            // Only set the zindex if we found a wrapper.
            if (highestzindex > 0) {
                bb.setStyle('zIndex', (highestzindex + 1).toString());
            }
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
     * Enable or disable document scrolling (see if there are any modal or fullscreen popups).
     *
     * @method toggleDocumentScrolling
     * @param Boolean scroll - If true, allow document scrolling.
     * @return void
     */
    toggleDocumentScrolling : function() {
        var windowroot = Y.one(Y.config.doc.body),
            scroll = true,
            search;

        search = '.' + DIALOGUE_FULLSCREEN_CLASS + ', .' + DIALOGUE_MODAL_CLASS;
        Y.all(search).each(function (node) {
            if (!node.hasClass(DIALOGUE_HIDDEN_CLASS)) {
                scroll = false;
            }
        });

        if (Y.UA.ie > 0) {
            // Remember the previous value:
            windowroot = Y.one('html');
        }
        if (scroll) {
            if (windowroot.hasClass(NOSCROLLING_CLASS)) {
                windowroot.removeClass(NOSCROLLING_CLASS);
            }
        } else {
            windowroot.addClass(NOSCROLLING_CLASS);
        }
    },

    /**
     * Event listener for the visibility changed event.
     *
     * @method visibilityChanged
     * @return void
     */
    visibilityChanged : function(e) {
        var titlebar;
        if (e.attrName === 'visible') {
            this.get('maskNode').addClass(CSS.LIGHTBOX);
            if (e.prevVal && !e.newVal) {
                if (this._resizeevent) {
                    this._resizeevent.detach();
                    this._resizeevent = null;
                }
                if (this._orientationevent) {
                    this._orientationevent.detach();
                    this._orientationevent = null;
                }
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
            }
            if (this.get('center') && !e.prevVal && e.newVal) {
                this.centerDialogue();
            }
            this.toggleDocumentScrolling();
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

            bb.addClass(DIALOGUE_PREFIX+'-fullscreen');
            bb.setStyles({'left' : null, 'top' : null, 'width' : null, 'height' : null});

            content = Y.one('#' + this.get('id') + ' .' + CSS.BODY);
            content.setStyle('overflow', 'auto');
        } else {
            if (this.get('responsive')) {
                // We must reset any of the fullscreen changes.
                bb.removeClass(DIALOGUE_PREFIX+'-fullscreen')
                    .setStyles({'overflow' : 'inherit',
                                'width' : this.get('width'),
                                'height' : this.get('height')});
                content = Y.one('#' + this.get('id') + ' .' + CSS.BODY);
                content.setStyle('overflow', 'inherit');

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

        result = DIALOGUE.superclass.show.call(this);
        if (header && header !== '') {
            header.focus();
        } else if (content && content !== '') {
            content.focus();
        }
        return result;
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
