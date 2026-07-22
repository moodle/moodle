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
        FIELDSETCOLLAPSIBLE: 'fieldset.collapsible',
        FHEADER: '.fheader',
        FCONTAINER: '.fcontainer',
        LEGENDFTOGGLER: 'legend.ftoggler'
    },
    CSS = {
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

        // Expand a section if it contains a field which failed validation.
        require(['core_form/events'], function(FormEvents) {
            form.getDOMNode().addEventListener(FormEvents.eventTypes.formError, this.expand_fieldset.bind(this));
        }.bind(this));
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
     * Expand the fieldset which contains an errored field, using Bootstrap's own collapse API
     * so that the container's real visibility (and hence the hidden status field, via the
     * observer set up in initializer) stays correctly in sync.
     *
     * @method expand_fieldset
     * @param {CustomEvent} e The core_form/events "formError" event; e.target is the errored field.
     */
    expand_fieldset: function(e) {
        var errorfieldset = this.get_error_fieldset(e);
        if (errorfieldset) {
            var container = errorfieldset.one(SELECTORS.FCONTAINER);
            var headerlink = errorfieldset.one(SELECTORS.FHEADER);
            // Check the container's own "show" class directly: it's the one thing that
            // reliably reflects whether the section is actually visible, regardless of how it
            // last got that way (a direct click, or "Expand all"/"Collapse all") and regardless
            // of Bootstrap version (see the note in initializer).
            if (container && !container.hasClass(CSS.SHOW) && headerlink) {
                headerlink.getDOMNode().click();
                return;
            }
            this.set_state(errorfieldset, false);
        }
    },

    /**
     * Get a fieldset containing an error from a DOM event.
     *
     * @method get_error_fieldset
     * @param {CustomEvent} e
     * @return {Node|null}
     */
    get_error_fieldset: function(e) {
        var formid = this.form.getAttribute('id');
        if (e.target) {
            var errorelementdom = Y.one(e.target);
            if (!errorelementdom) {
                return null;
            }
            var errorfieldset = errorelementdom.ancestor('fieldset');
            if (!errorfieldset) {
                return null;
            }
            var errorform = errorfieldset.ancestor('form');
            if (errorform && errorform.getAttribute('id') === formid) {
                return errorfieldset;
            }
        }
        return null;
    }
}, {
    NAME: 'moodle-form-shortforms',
    ATTRS: ATTRS
});

M.form = M.form || {};
M.form.shortforms = M.form.shortforms || function(params) {
    return new SHORTFORMS(params);
};
