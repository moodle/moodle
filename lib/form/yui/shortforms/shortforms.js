YUI.add('moodle-form-shortforms', function(Y) {
    /**
     * Provides the form shortforms class.
     *
     * @module moodle-form-shortforms
     */

    /**
     * A class for a shortforms.
     *
     * @param {Object} config Object literal specifying shortforms configuration properties.
     * @class M.form.shortforms
     * @constructor
     * @extends Y.Base
     */
    function SHORTFORMS(config) {
        SHORTFORMS.superclass.constructor.apply(this, [config]);
    }

    var SELECTORS = {
            COLLAPSEBTN : '.collapsible-actions .btn-collapseall',
            EXPANDBTN : '.collapsible-actions .btn-expandall',
            FIELDSETCOLLAPSIBLE : 'fieldset.collapsible',
            FORM: 'form.mform',
            LEGENDFTOGGLER : 'legend.ftoggler'
        },
        CSS = {
            COLLAPSED : 'collapsed',
            FHEADER : 'fheader'
        },
        ATTRS = {};

    /**
     * Static property provides a string to identify the JavaScript class.
     *
     * @property NAME
     * @type String
     * @static
     */
    SHORTFORMS.NAME = 'moodle-form-shortforms';

    /**
     * Static property used to define the default attribute configuration for the Shortform.
     *
     * @property ATTRS
     * @type String
     * @static
     */
    SHORTFORMS.ATTRS = ATTRS;

    /**
     * The form ID attribute definition.
     *
     * @attribute formid
     * @type String
     * @default ''
     * @writeOnce
     */
    ATTRS.formid = {
        value : null
    };

    Y.extend(SHORTFORMS, Y.Base, {
        form: null,
        initializer : function() {
            var form = Y.one('#'+this.get('formid')),
                fieldlist;
            if (!form) {
                Y.log('Could not locate the form', 'debug');
                return;
            }
            // Stores the form in the object.
            this.form = form;
            // Look through collapsible fieldset divs.
            fieldlist = form.all(SELECTORS.FIELDSETCOLLAPSIBLE);
            fieldlist.each(this.process_fieldset, this);
            // Subscribe collapsible fieldsets and buttons to click events.
            form.delegate('click', this.switch_state, SELECTORS.FIELDSETCOLLAPSIBLE+' .'+CSS.FHEADER, this);
            form.delegate('click', this.set_state_all, SELECTORS.COLLAPSEBTN, this, true);
            form.delegate('click', this.set_state_all, SELECTORS.EXPANDBTN, this, false);
            this.update_btns(form);
        },
        process_fieldset : function(fieldset) {
            // Get legend element.
            var legendelement = fieldset.one(SELECTORS.LEGENDFTOGGLER);

            // Turn headers to links for accessibility.
            var headerlink = Y.Node.create('<a href="#"></a>');
            headerlink.addClass(CSS.FHEADER);
            headerlink.appendChild(legendelement.get('firstChild'));
            legendelement.prepend(headerlink);
        },
        set_state: function(fieldset, collapsed) {
            if (collapsed) {
                fieldset.addClass(CSS.COLLAPSED);
            } else {
                fieldset.removeClass(CSS.COLLAPSED);
            }
            var statuselement = Y.one('input[name=mform_isexpanded_'+fieldset.get('id')+']');
            if (!statuselement) {
                Y.log("M.form.shortforms::switch_state was called on an fieldset without a status field: '" +
                    fieldset.get('id') + "'", 'debug');
                return;
            }
            statuselement.set('value', collapsed ? 0 : 1);
        },
        set_state_all: function(e, collapsed) {
            e.preventDefault();
            var fieldlist = this.form.all(SELECTORS.FIELDSETCOLLAPSIBLE);
            fieldlist.each(function(node) {
                this.set_state(node, collapsed);
            }, this);
            this.update_btns();
        },
        switch_state : function(e) {
            e.preventDefault();
            var fieldset = e.target.ancestor(SELECTORS.FIELDSETCOLLAPSIBLE);
            this.set_state(fieldset, !fieldset.hasClass(CSS.COLLAPSED));
            this.update_btns();
        },
        update_btns: function() {
            var btn,
                collapsed = 0,
                collapsebtn = false,
                expandbtn = false,
                fieldlist;

            // Counting the number of collapsed sections.
            fieldlist = this.form.all(SELECTORS.FIELDSETCOLLAPSIBLE);
            fieldlist.each(function(node) {
                if (node.hasClass(CSS.COLLAPSED)) {
                    collapsed++;
                }
            });

            if (collapsed === 0) {
                expandbtn = true;
            } else if (collapsed === fieldlist.size()) {
                collapsebtn = true;
            }

            // Setting the new states of the buttons.
            btn = this.form.one(SELECTORS.COLLAPSEBTN);
            if (btn) {
                btn.set('disabled', collapsebtn);
            }
            btn = this.form.one(SELECTORS.EXPANDBTN);
            if (btn) {
                btn.set('disabled', expandbtn);
            }
        }
    });

    M.form = M.form || {};
    M.form.shortforms = M.form.shortforms || function(params) {
        return new SHORTFORMS(params);
    };
}, '@VERSION@', {requires:['base', 'node', 'selector-css3']});
