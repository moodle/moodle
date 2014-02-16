/**
 * Provides the base tooltip class.
 *
 * @module moodle-core-tooltip
 */

/**
 * A base class for a tooltip.
 *
 * @param {Object} config Object literal specifying tooltip configuration properties.
 * @class M.core.tooltip
 * @constructor
 * @extends M.core.dialogue
 */
function TOOLTIP(config) {
    if (!config) {
        config = {};
    }

    // Override the default options provided by the parent class.
    if (typeof config.draggable === 'undefined') {
        config.draggable = true;
    }

    if (typeof config.constrain === 'undefined') {
        config.constrain = true;
    }

    if (typeof config.lightbox === 'undefined') {
        config.lightbox = false;
    }

    TOOLTIP.superclass.constructor.apply(this, [config]);
}

var SELECTORS = {
        CLOSEBUTTON: '.closebutton'
    },

    CSS = {
        PANELTEXT: 'tooltiptext'
    },
    RESOURCES = {
        WAITICON: {
            pix: 'i/loading_small',
            component: 'moodle'
        }
    },
    ATTRS = {};

/**
 * Static property provides a string to identify the JavaScript class.
 *
 * @property NAME
 * @type String
 * @static
 */
TOOLTIP.NAME = 'moodle-core-tooltip';

/**
 * Static property used to define the CSS prefix applied to tooltip dialogues.
 *
 * @property CSS_PREFIX
 * @type String
 * @static
 */
TOOLTIP.CSS_PREFIX = 'moodle-dialogue';

/**
 * Static property used to define the default attribute configuration for the Tooltip.
 *
 * @property ATTRS
 * @type String
 * @static
 */
TOOLTIP.ATTRS = ATTRS;

/**
 * The initial value of the header region before the content finishes loading.
 *
 * @attribute initialheadertext
 * @type String
 * @default ''
 * @writeOnce
 */
ATTRS.initialheadertext = {
    value: ''
};

/**
  * The initial value of the body region before the content finishes loading.
  *
  * The supplid string will be wrapped in a div with the CSS.PANELTEXT class and a standard Moodle spinner
  * appended.
  *
  * @attribute initialbodytext
  * @type String
  * @default ''
  * @writeOnce
  */
ATTRS.initialbodytext = {
    value: '',
    setter: function(content) {
        var parentnode,
            spinner;
        parentnode = Y.Node.create('<div />')
            .addClass(CSS.PANELTEXT);

        spinner = Y.Node.create('<img />')
            .setAttribute('src', M.util.image_url(RESOURCES.WAITICON.pix, RESOURCES.WAITICON.component))
            .addClass('spinner');

        if (content) {
            // If we have been provided with content, add it to the parent and make
            // the spinner appear correctly inline
            parentnode.set('text', content);
            spinner.addClass('iconsmall');
        } else {
            // If there is no loading message, just make the parent node a lightbox
            parentnode.addClass('content-lightbox');
        }

        parentnode.append(spinner);
        return parentnode;
    }
};

/**
 * The initial value of the footer region before the content finishes loading.
 *
 * If a value is supplied, it will be wrapped in a <div> first.
 *
 * @attribute initialfootertext
 * @type String
 * @default ''
 * @writeOnce
 */
ATTRS.initialfootertext = {
    value: null,
    setter: function(content) {
        if (content) {
            return Y.Node.create('<div />')
                .set('text', content);
        }
    }
};

/**
 * The function which handles setting the content of the title region.
 * The specified function will be called with a context of the tooltip instance.
 *
 * The default function will simply set the value of the title to object.heading as returned by the AJAX call.
 *
 * @attribute headerhandler
 * @type Function|String|null
 * @default set_header_content
 */
ATTRS.headerhandler = {
    value: 'set_header_content'
};

/**
 * The function which handles setting the content of the body region.
 * The specified function will be called with a context of the tooltip instance.
 *
 * The default function will simply set the value of the body area to a div containing object.text as returned
 * by the AJAX call.
 *
 * @attribute bodyhandler
 * @type Function|String|null
 * @default set_body_content
 */
ATTRS.bodyhandler = {
    value: 'set_body_content'
};

/**
 * The function which handles setting the content of the footer region.
 * The specified function will be called with a context of the tooltip instance.
 *
 * By default, the footer is not set.
 *
 * @attribute footerhandler
 * @type Function|String|null
 * @default null
 */
ATTRS.footerhandler = {
    value: null
};

/**
 * The function which handles modifying the URL that was clicked on.
 *
 * The default function rewrites '.php' to '_ajax.php'.
 *
 * @attribute urlmodifier
 * @type Function|String|null
 * @default null
 */
ATTRS.urlmodifier = {
    value: null
};

/**
 * Set the Y.Cache object to use.
 *
 * By default a new Y.Cache object will be created for each instance of the tooltip.
 *
 * In certain situations, where multiple tooltips may share the same cache, it may be preferable to
 * seed this cache from the calling method.
 *
 * @attribute textcache
 * @type Y.Cache|null
 * @default null
 */
ATTRS.textcache = {
    value: null
};

/**
 * Set the default size of the Y.Cache object.
 *
 * This is only used if no textcache is specified.
 *
 * @attribute textcachesize
 * @type Number
 * @default 10
 */
ATTRS.textcachesize = {
    value: 10
};

Y.extend(TOOLTIP, M.core.dialogue, {
    // The bounding box.
    bb: null,

    // Any event listeners we may need to cancel later.
    listenevents: [],

    // Cache of objects we've already retrieved.
    textcache: null,

    // The align position. This differs for RTL languages so we calculate once and store.
    alignpoints: [
        Y.WidgetPositionAlign.TL,
        Y.WidgetPositionAlign.RC
    ],

    initializer: function() {
        // Set the initial values for the handlers.
        // These cannot be set in the attributes section as context isn't present at that time.
        if (!this.get('headerhandler')) {
            this.set('headerhandler', this.set_header_content);
        }
        if (!this.get('bodyhandler')) {
            this.set('bodyhandler', this.set_body_content);
        }
        if (!this.get('footerhandler')) {
            this.set('footerhandler', function() {});
        }
        if (!this.get('urlmodifier')) {
            this.set('urlmodifier', this.modify_url);
        }

        // Set up the dialogue with initial content.
        this.setAttrs({
            headerContent: this.get('initialheadertext'),
            bodyContent: this.get('initialbodytext'),
            footerContent: this.get('initialfootertext')
        });

        // Hide and then render the dialogue.
        this.hide();
        this.render();

        // Hook into a few useful areas.
        this.bb = this.get('boundingBox');

        // Add an additional class to the boundingbox to allow tooltip-specific style to be
        // set.
        this.bb.addClass('moodle-dialogue-tooltip');

        // Change the alignment if this is an RTL language.
        if (right_to_left()) {
            this.alignpoints = [
                Y.WidgetPositionAlign.TR,
                Y.WidgetPositionAlign.LC
            ];
        }

        // Set up the text cache if it's not set up already.
        if (!this.get('textcache')) {
            this.set('textcache', new Y.Cache({
                // Set a reasonable maximum cache size to prevent memory growth.
                max: this.get('textcachesize')
            }));
        }

        // Disable the textcache when in developerdebug.
        if (M.cfg.developerdebug) {
            this.get('textcache').set('max', 0);
        }

        return this;
    },

    /**
     * Display the tooltip for the clicked link.
     *
     * The anchor for the clicked link is used.
     *
     * @method display_panel
     * @param {EventFacade} e The event from the clicked link. This is used to determine the clicked URL.
     */
    display_panel: function(e) {
        var clickedlink, thisevent, ajaxurl, config, cacheentry;

        // Prevent the default click action and prevent the event triggering anything else.
        e.preventDefault();
        e.stopPropagation();

        // Cancel any existing listeners and close the panel if it's already open.
        this.cancel_events();

        // Grab the clickedlink - this contains the URL we fetch and we align the panel to it.
        clickedlink = e.target.ancestor('a', true);

        // Reset the initial text to a spinner while we retrieve the text.
        this.setAttrs({
            headerContent: this.get('initialheadertext'),
            bodyContent: this.get('initialbodytext'),
            footerContent: this.get('initialfootertext')
        });

        // Now that initial setup has begun, show the panel.
        this.show();

        // Align with the link that was clicked.
        this.align(clickedlink, this.alignpoints);

        // Add some listen events to close on.
        thisevent = this.bb.delegate('click', this.close_panel, SELECTORS.CLOSEBUTTON, this);
        this.listenevents.push(thisevent);

        thisevent = Y.one('body').on('key', this.close_panel, 'esc', this);
        this.listenevents.push(thisevent);

        // Listen for mousedownoutside events - clickoutside is broken on IE.
        thisevent = this.bb.on('mousedownoutside', this.close_panel, this);
        this.listenevents.push(thisevent);

        // Modify the URL as required.
        ajaxurl = Y.bind(this.get('urlmodifier'), this, clickedlink.get('href'))();

        cacheentry = this.get('textcache').retrieve(ajaxurl);
        if (cacheentry) {
            // The data from this help call was already cached so use that and avoid an AJAX call.
            this._set_panel_contents(cacheentry.response);
        } else {
            // Retrieve the actual help text we should use.
            config = {
                method: 'get',
                context: this,
                sync: false,
                on: {
                    complete: function(tid, response) {
                        this._set_panel_contents(response.responseText, ajaxurl);
                    }
                }
            };

            Y.io(ajaxurl, config);
        }
    },

    _set_panel_contents: function(response, ajaxurl) {
        var responseobject;

        // Attempt to parse the response into an object.
        try {
            responseobject = Y.JSON.parse(response);
            if (responseobject.error) {
                this.close_panel();
                return new M.core.ajaxException(responseobject);
            }
        } catch (error) {
            this.close_panel();
            return new M.core.exception(error);
        }

        // Set the contents using various handlers.
        // We must use Y.bind to ensure that the correct context is used when the default handlers are overridden.
        Y.bind(this.get('headerhandler'), this, responseobject)();
        Y.bind(this.get('bodyhandler'), this, responseobject)();
        Y.bind(this.get('footerhandler'), this, responseobject)();

        if (ajaxurl) {
            // Ensure that this data is added to the cache.
            this.get('textcache').add(ajaxurl, response);
        }

        this.get('buttons').header[0].focus();
    },

    set_header_content: function(responseobject) {
        this.set('headerContent', responseobject.heading);
    },

    set_body_content: function(responseobject) {
        var bodycontent = Y.Node.create('<div />')
            .set('innerHTML', responseobject.text)
            .setAttribute('role', 'alert')
            .addClass(CSS.PANELTEXT);
        this.set('bodyContent', bodycontent);
    },

    modify_url: function(url) {
        return url.replace(/\.php\?/, '_ajax.php?');
    },

    close_panel: function(e) {
        // Hide the panel first.
        this.hide(e);

        // Cancel the listeners that we added in display_panel.
        this.cancel_events();

        // Prevent any default click that the close button may have.
        if (e) {
            e.preventDefault();
        }
    },

    cancel_events: function() {
        // Detach all listen events to prevent duplicate triggers.
        var thisevent;
        while (this.listenevents.length) {
            thisevent = this.listenevents.shift();
            thisevent.detach();
        }
    }
});
M.core = M.core || {};
M.core.tooltip = M.core.tooltip = TOOLTIP;
