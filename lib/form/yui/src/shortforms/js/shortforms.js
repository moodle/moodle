/**
 * Provides the form shortforms class.
 *
 * @module moodle-form-shortforms
 */

/**
 * A class for a shortforms.
 *
 * @class M.form.shortforms
 * @constructor
 * @extends Base
 */
function SHORTFORMS() {
    SHORTFORMS.superclass.constructor.apply(this, arguments);
}

var SELECTORS = {
        COLLAPSED: '.collapsed',
        FIELDSETCOLLAPSIBLE: 'fieldset.collapsible',
        FIELDSETLEGENDLINK: 'fieldset.collapsible .fheader',
        FHEADER: '.fheader',
        LEGENDFTOGGLER: 'legend.ftoggler'
    },
    CSS = {
        COLLAPSEALL: 'collapse-all',
        COLLAPSED: 'collapsed',
        FHEADER: 'fheader'
    },
    ATTRS = {};

/**
 * The form ID attribute definition.
 *
 * @attribute formid
 * @type String
 * @default ''
 * @writeOnce
 */
ATTRS.formid = {
    value: null
};

Y.extend(SHORTFORMS, Y.Base, {
    /**
     * A reference to the form.
     *
     * @property form
     * @protected
     * @type Node
     * @default null
     */
    form: null,

    /**
     * The initializer for the shortforms instance.
     *
     * @method initializer
     * @protected
     */
    initializer: function() {
        var form = Y.one('#' + this.get('formid'));
        if (!form) {
            Y.log('Could not locate the form', 'warn', 'moodle-form-shortforms');
            return;
        }
        // Stores the form in the object.
        this.form = form;

        // Subscribe collapsible fieldsets and buttons to click events.
        form.delegate('click', this.switch_state, SELECTORS.FIELDSETLEGENDLINK, this);

        // Handle event, when there's an error in collapsed section.
        Y.Global.on(M.core.globalEvents.FORM_ERROR, this.expand_fieldset, this);
    },

    /**
     * Set the collapsed state for the specified fieldset.
     *
     * @method set_state
     * @param {Node} fieldset The Node relating to the fieldset to set state on.
     * @param {Boolean} [collapsed] Whether the fieldset is collapsed.
     * @chainable
     */
    set_state: function(fieldset, collapsed) {
        var headerlink = fieldset.one(SELECTORS.FHEADER);
        if (collapsed) {
            fieldset.addClass(CSS.COLLAPSED);
            if (headerlink) {
                headerlink.setAttribute('aria-expanded', 'false');
            }
        } else {
            fieldset.removeClass(CSS.COLLAPSED);
            if (headerlink) {
                headerlink.setAttribute('aria-expanded', 'true');
            }
        }
        var statuselement = this.form.one('input[name=mform_isexpanded_' + fieldset.get('id') + ']');
        if (!statuselement) {
            Y.log("M.form.shortforms::switch_state was called on an fieldset without a status field: '" +
                fieldset.get('id') + "'", 'debug', 'moodle-form-shortforms');
            return this;
        }
        statuselement.set('value', collapsed ? 0 : 1);

        return this;
    },

    /**
     * Toggle the state for the fieldset that was clicked.
     *
     * @method switch_state
     * @param {EventFacade} e
     */
    switch_state: function(e) {
        e.preventDefault();
        var fieldset = e.target.ancestor(SELECTORS.FIELDSETCOLLAPSIBLE);
        this.set_state(fieldset, !fieldset.hasClass(CSS.COLLAPSED));
    },

    /**
     * Expand the fieldset, which contains an error.
     *
     * @method expand_fieldset
     * @param {EventFacade} e
     */
    expand_fieldset: function(e) {
        e.stopPropagation();
        var formid = e.formid;
        if (formid === this.form.getAttribute('id')) {
            var errorfieldset = Y.one('#' + e.elementid).ancestor('fieldset');
            if (errorfieldset) {
                this.set_state(errorfieldset, false);
            }

        }
   }
}, {
    NAME: 'moodle-form-shortforms',
    ATTRS: ATTRS
});

M.form = M.form || {};
M.form.shortforms = M.form.shortforms || function(params) {
    return new SHORTFORMS(params);
};
