YUI.add('moodle-core-popuphelp', function (Y, NAME) {

function POPUPHELP() {
    POPUPHELP.superclass.constructor.apply(this, arguments);
}

var SELECTORS = {
        CLICKABLELINKS: 'span.helptooltip > a',
        FOOTER: 'div.moodle-dialogue-ft'
    },

    CSS = {
        ICON: 'icon',
        ICONPRE: 'icon-pre'
    },
    ATTRS = {};

// Set the modules base properties.
POPUPHELP.NAME = 'moodle-core-popuphelp';
POPUPHELP.ATTRS = ATTRS;

Y.extend(POPUPHELP, Y.Base, {
    panel: null,

    initializer: function() {
        Y.one('body').delegate('click', this.display_panel, SELECTORS.CLICKABLELINKS, this);
    },

    display_panel: function(e) {
        if (!this.panel) {
            this.panel = new M.core.tooltip({
                bodyhandler: this.set_body_content,
                footerhandler: this.set_footer,
                initialheadertext: M.util.get_string('loadinghelp', 'moodle'),
                initialfootertext: ''
            });
        }

        // Call the tooltip setup.
        this.panel.display_panel(e);
    },

    /**
      * Override the footer handler to add a 'More help' link where relevant.
      *
      * @param {Object} helpobject The object returned from the AJAX call.
      */
    set_footer: function(helpobject) {
        // Check for an optional link to documentation on moodle.org.
        if (helpobject.doclink) {
            // Wrap a help icon and the morehelp text in an anchor. The class of the anchor should
            // determine whether it's opened in a new window or not.
            doclink = Y.Node.create('<a />')
                .setAttrs({
                    'href': helpobject.doclink.link
                })
                .addClass(helpobject.doclink['class']);
            helpicon = Y.Node.create('<img />')
                .setAttrs({
                    'src': M.util.image_url('docs', 'core')
                })
                .addClass(CSS.ICON)
                .addClass(CSS.ICONPRE);
            doclink.appendChild(helpicon);
            doclink.appendChild(helpobject.doclink.linktext);

            // Set the footerContent to the contents of the doclink.
            this.set('footerContent', doclink);
            this.bb.one(SELECTORS.FOOTER).show();
        } else {
            this.bb.one(SELECTORS.FOOTER).hide();
        }
    }
});
M.core = M.core || {};
M.core.popuphelp = M.core.popuphelp || null;
M.core.init_popuphelp = M.core.init_popuphelp || function(config) {
    // Only set up a single instance of the popuphelp.
    if (!M.core.popuphelp) {
        M.core.popuphelp = new POPUPHELP(config);
    }
    return M.core.popuphelp;
};


}, '@VERSION@', {"requires": ["moodle-core-tooltip"]});
