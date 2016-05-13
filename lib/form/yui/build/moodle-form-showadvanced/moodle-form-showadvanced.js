YUI.add('moodle-form-showadvanced', function (Y, NAME) {

/**
 * Provides the form showadvanced class.
 *
 * @module moodle-form-showadvanced
 */

/**
 * A class to help show and hide advanced form content.
 *
 * @class M.form.showadvanced
 * @constructor
 * @extends Base
 */
function SHOWADVANCED() {
    SHOWADVANCED.superclass.constructor.apply(this, arguments);
}

var SELECTORS = {
        FIELDSETCONTAINSADVANCED: 'fieldset.containsadvancedelements',
        DIVFITEMADVANCED: 'div.fitem.advanced',
        DIVFCONTAINER: 'div.fcontainer',
        MORELESSLINK: 'fieldset.containsadvancedelements .moreless-toggler'
    },
    CSS = {
        SHOW: 'show',
        MORELESSACTIONS: 'moreless-actions',
        MORELESSTOGGLER: 'moreless-toggler',
        SHOWLESS: 'moreless-less'
    },
    WRAPPERS = {
        FITEM: '<div class="fitem"></div>',
        FELEMENT: '<div class="felement"></div>'
    },
    ATTRS = {};

/**
 * The form ID attribute definition.
 *
 * @attribute formid
 * @type String
 * @default null
 * @writeOnce
 */
ATTRS.formid = {
    value: null
};

Y.extend(SHOWADVANCED, Y.Base, {
    /**
     * The initializer for the showadvanced instance.
     *
     * @method initializer
     * @protected
     */
    initializer: function() {
        var form = Y.one('#'+this.get('formid')),
            fieldlist = form.all(SELECTORS.FIELDSETCONTAINSADVANCED);

        // Look through fieldset divs that contain advanced elements.
        fieldlist.each(this.processFieldset, this);

        // Subscribe more/less links to click event.
        form.delegate('click', this.switchState, SELECTORS.MORELESSLINK);
        form.delegate('key', this.switchState, 'down:enter,32', SELECTORS.MORELESSLINK);
    },

    /**
     * Process the supplied fieldset to add appropriate links, and ARIA roles.
     *
     * @method processFieldset
     * @param {Node} fieldset The Node relating to the fieldset to add collapsing to.
     * @chainable
     */
    processFieldset: function(fieldset) {
        var statuselement = Y.one('input[name=mform_showmore_' + fieldset.get('id') + ']');
        if (!statuselement) {
            return this;
        }

        var morelesslink = Y.Node.create('<a href="#"></a>');
        morelesslink.addClass(CSS.MORELESSTOGGLER);
        if (statuselement.get('value') === '0') {
            morelesslink.setHTML(M.util.get_string('showmore', 'form'));
        } else {
            morelesslink.setHTML(M.util.get_string('showless', 'form'));
            morelesslink.addClass(CSS.SHOWLESS);
            fieldset.all(SELECTORS.DIVFITEMADVANCED).addClass(CSS.SHOW);
        }

        // Get list of IDs controlled by this button to set the aria-controls attribute.
        var idlist = [];
        fieldset.all(SELECTORS.DIVFITEMADVANCED).each(function(node) {
            idlist[idlist.length] = node.generateID();
        });
        morelesslink.setAttribute('role', 'button');
        morelesslink.setAttribute('aria-controls', idlist.join(' '));

        var fitem = Y.Node.create(WRAPPERS.FITEM);
        fitem.addClass(CSS.MORELESSACTIONS);
        var felement = Y.Node.create(WRAPPERS.FELEMENT);
        felement.append(morelesslink);
        fitem.append(felement);

        fieldset.one(SELECTORS.DIVFCONTAINER).append(fitem);

        return this;
    },

    /**
     * Toggle the state for the fieldset that was clicked.
     *
     * @method switchState
     * @param {EventFacade} e
     */
    switchState: function(e) {
        e.preventDefault();
        var fieldset = this.ancestor(SELECTORS.FIELDSETCONTAINSADVANCED);

        // Toggle collapsed class.
        fieldset.all(SELECTORS.DIVFITEMADVANCED).toggleClass(CSS.SHOW);

        // Get corresponding hidden variable.
        var statuselement = Y.one('input[name=mform_showmore_' + fieldset.get('id') + ']');

        // Invert it and change the link text.
        if (statuselement.get('value') === '0') {
            statuselement.set('value', 1);
            this.addClass(CSS.SHOWLESS);
            this.setHTML(M.util.get_string('showless', 'form'));
        } else {
            statuselement.set('value', 0);
            this.removeClass(CSS.SHOWLESS);
            this.setHTML(M.util.get_string('showmore', 'form'));
        }
    }
}, {
    NAME: 'moodle-form-showadvanced',
    ATTRS: ATTRS
});

M.form = M.form || {};
M.form.showadvanced = M.form.showadvanced || function(params) {
    return new SHOWADVANCED(params);
};


}, '@VERSION@', {"requires": ["node", "base", "selector-css3"]});
