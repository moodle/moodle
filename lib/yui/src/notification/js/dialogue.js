/**
 * The generic dialogue class for use in Moodle.
 *
 * @module moodle-core-notification
 * @submodule moodle-core-notification-dialogue
 */

var DIALOGUE_NAME = 'Moodle dialogue',
    DIALOGUE;

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
    var id = 'moodle-dialogue-'+COUNT, inputSrcNode = false, contentNode;

    if (config.srcNode) {
        // We need to reparent this source node to the dialog.
        inputSrcNode = config.srcNode;
    }
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
    config.center =     config.centered || true;
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
    if (inputSrcNode) {
        contentNode = Y.one(inputSrcNode);
        // Do we need to remove it?
        contentNode.get('parentNode').removeChild(contentNode);
        Y.one('#' + id + ' .' + CSS.BODY).append(contentNode);
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

    initializer : function() {
        var bb, classes, extraclass;
        this.after('visibleChange', this.visibilityChanged, this);
        this.render();
        this.show();
        this.set('COUNT', COUNT);

        // Workaround upstream YUI bug http://yuilibrary.com/projects/yui3/ticket/2532507
        // and allow setting of z-index in theme.
        bb = this.get('boundingBox');
        bb.setStyle('zIndex', null);

        // Add the list of extra classes to the bounding box for this dialog (for styling).
        classes = this.get('extraClasses').split(' ');
        extraclass = classes.pop();
        while (typeof extraclass !== "undefined") {
            bb.addClass(extraclass);
            extraclass = classes.shift();
        }
    },
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
            if (this.get('center') && !e.prevVal && e.newVal) {
                this.centerDialogue();
            }
            if (this.get('draggable')) {
                titlebar = '#' + this.get('id') + ' .' + CSS.HEADER;
                this.plug(Y.Plugin.Drag, {handles : [titlebar]});
                Y.one(titlebar).setStyle('cursor', 'move');
            }
        }
    },
    centerDialogue : function() {
        var bb = this.get('boundingBox'),
            hidden = bb.hasClass(DIALOGUE_PREFIX+'-hidden'),
            content, x, y;
        if (hidden) {
            bb.setStyle('top', '-1000px').removeClass(DIALOGUE_PREFIX+'-hidden');
        }
        if (this.get('fullscreen')) {
            // Make this dialogue fullscreen on a small screen.
            // Disable the page scrollbars.
            if (Y.UA.ie > 0) {
                Y.one('html').setStyle('overflow', 'hidden');
            } else {
                Y.one('body').setStyle('overflow', 'hidden');
            }
            // Size and position the fullscreen dialog.

            bb.addClass(DIALOGUE_PREFIX+'-fullscreen');
            bb.setStyle('left', '0px')
                .setStyle('top', '0px')
                .setStyle('width', '100%')
                .setStyle('height', '100%')
                .setStyle('overflow', 'auto');

            content = Y.one('#' + this.get('id') + ' .' + CSS.BODY);
            content.setStyle('overflow', 'auto');
            window.scrollTo(0, 0);
        } else {
            if (this.get('responsive')) {
                // We must reset any of the fullscreen changes.
                bb.removeClass(DIALOGUE_PREFIX+'-fullscreen')
                    .setStyle('overflow', 'inherit')
                    .setStyle('width', this.get('width'))
                    .setStyle('height', this.get('height'));
                content = Y.one('#' + this.get('id') + ' .' + CSS.BODY);
                content.setStyle('overflow', 'inherit');

                if (Y.UA.ie > 0) {
                    Y.one('html').setStyle('overflow', 'auto');
                } else {
                    Y.one('body').setStyle('overflow', 'auto');
                }
            }
            x = Math.max(Math.round((bb.get('winWidth') - bb.get('offsetWidth'))/2), 15);
            y = Math.max(Math.round((bb.get('winHeight') - bb.get('offsetHeight'))/2), 15) + Y.one(window).get('scrollTop');
            bb.setStyle('left', x).setStyle('top', y);
        }

        if (hidden) {
            bb.addClass(DIALOGUE_PREFIX+'-hidden');
        }
    },
    hide : function() {
        if (Y.UA.ie > 0) {
            Y.one('html').setStyle('overflow', 'auto');
        } else {
            Y.one('body').setStyle('overflow', 'auto');
        }
        return this.set("visible", false);
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
        COUNT: {
            value: 0
        },
        responsive : {
            validator : Y.Lang.isBoolean,
            value : true
        },
        responsiveWidth : {
            value : 768
        },
        extraClasses : {
            validator : Y.Lang.isString,
            value : ''
        },
        fullscreen : {
            getter : function() {
                return this.get('responsive') &&
                       Math.floor(Y.one(document.body).get('winWidth')) < this.get('responsiveWidth');
            }
        }
    }
});

M.core.dialogue = DIALOGUE;
