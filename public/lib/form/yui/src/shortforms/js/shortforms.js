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
        FHEADER: '.fheader',
        FCONTAINER: '.fcontainer',
        LEGENDFTOGGLER: 'legend.ftoggler'
    },
    CSS = {
        COLLAPSED: 'collapsed',
        SHOW: 'show'
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

        // Keep each fieldset's hidden "is it expanded" status field in sync with its actual
        // container visibility. This observes the container's own "show" class directly,
        // rather than reacting to a click or to a Bootstrap collapse event, so it works no
        // matter how the section was expanded/collapsed (a direct click, or "Expand
        // all"/"Collapse all") and regardless of Bootstrap version. Bootstrap 5 delegates its
        // own collapse click handling in the capture phase, so it always runs before a
        // bubble-phase handler attached here on the same click - but Bootstrap 4 (jQuery)
        // delegates in the bubble phase, where that order is reversed. Reacting to the click
        // itself is therefore not reliable across versions, whereas the "show" class, and what
        // it means, has been stable since Bootstrap 3.
        form.all(SELECTORS.FIELDSETCOLLAPSIBLE).each(function(fieldset) {
            var container = fieldset.one(SELECTORS.FCONTAINER);
            if (!container) {
                return;
            }
            var observer = new MutationObserver(function() {
                this.set_state(fieldset, !container.hasClass(CSS.SHOW));
            }.bind(this));
            observer.observe(container.getDOMNode(), {attributes: true, attributeFilter: ['class']});
        }, this);

        // Handle event, when there's an error in collapsed section.
        Y.Global.on(M.core.globalEvents.FORM_ERROR, this.expand_fieldset, this);
    },

    /**
     * Record the expanded state for the specified fieldset in its hidden status field.
     *
     * @method set_state
     * @param {Node} fieldset The Node relating to the fieldset to set state on.
     * @param {Boolean} collapsed Whether the fieldset is now collapsed.
     */
    set_state: function(fieldset, collapsed) {
        var statuselement = this.form.one('input[name=mform_isexpanded_' + fieldset.get('id') + ']');
        if (!statuselement) {
            Y.log("M.form.shortforms::set_state was called on an fieldset without a status field: '" +
                fieldset.get('id') + "'", 'debug', 'moodle-form-shortforms');
            return;
        }
        statuselement.set('value', collapsed ? 0 : 1);
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
