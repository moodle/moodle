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
            FIELDSETCOLLAPSIBLE : 'fieldset.collapsible',
            LEGENDFTOGGLER : 'legend.ftoggler'
        },
        CSS = {
            COLLAPSED : 'collapsed',
            FHEADER : 'fheader',
            JSPROCESSED : 'jsprocessed'
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
        initializer : function() {
            var fieldlist = Y.Node.all('#'+this.get('formid')+' '+SELECTORS.FIELDSETCOLLAPSIBLE);
            // Look through collapsible fieldset divs.
            fieldlist.each(this.process_fieldset, this);
            // Subscribe collapsible fieldsets to click event.
            Y.one('#'+this.get('formid')).delegate('click', this.switch_state, SELECTORS.FIELDSETCOLLAPSIBLE+' .'+CSS.FHEADER);
        },
        process_fieldset : function(fieldset) {
            fieldset.addClass(CSS.JSPROCESSED);
            // Get legend element.
            var legendelement = fieldset.one(SELECTORS.LEGENDFTOGGLER);

            // Turn headers to links for accessibility.
            var headerlink = Y.Node.create('<a href="#"></a>');
            headerlink.addClass(CSS.FHEADER);
            headerlink.appendChild(legendelement.get('firstChild'));
            legendelement.prepend(headerlink);
        },
        switch_state : function(e) {
            e.preventDefault();
            var fieldset = this.ancestor(SELECTORS.FIELDSETCOLLAPSIBLE);
            // Toggle collapsed class.
            fieldset.toggleClass(CSS.COLLAPSED);
            // Get corresponding hidden variable
            // - and invert it.
            var statuselement = Y.one('input[name=mform_isexpanded_'+fieldset.get('id')+']');
            if (!statuselement) {
                Y.log("M.form.shortforms::switch_state was called on an fieldset without a status field: '" +
                    fieldset.get('id') + "'", 'debug');
                return;
            }
            statuselement.set('value', Math.abs(Number(statuselement.get('value'))-1));
        }
    });

    M.form = M.form || {};
    M.form.shortforms = M.form.shortforms || function(params) {
        return new SHORTFORMS(params);
    };
}, '@VERSION@', {requires:['base', 'node', 'selector-css3']});
