YUI.add('moodle-form-showadvanced', function(Y) {
    /**
     * Provides the form showadvanced class.
     *
     * @module moodle-form-showadvanced
     */

    /**
     * A class for a showadvanced.
     *
     * @param {Object} config Object literal specifying showadvanced configuration properties.
     * @class M.form.showadvanced
     * @constructor
     * @extends Y.Base
     */
    function SHOWADVANCED(config) {
        SHOWADVANCED.superclass.constructor.apply(this, [config]);
    }

    var SELECTORS = {
            FIELDSETCONTAINSADVANCED : 'fieldset.containsadvancedelements',
            DIVFITEMADVANCED : 'div.fitem.advanced',
            DIVFCONTAINER : 'div.fcontainer',
            MORELESSLINK : 'fieldset.containsadvancedelements .moreless-toggler'
        },
        CSS = {
            SHOW : 'show',
            MORELESSACTIONS: 'moreless-actions',
            MORELESSTOGGLER : 'moreless-toggler',
            SHOWLESS : 'moreless-less'
        },
        WRAPPERS = {
            FITEM : '<div class="fitem"></div>',
            FELEMENT : '<div class="felement"></div>'
        },
        ATTRS = {};

    /**
     * Static property provides a string to identify the JavaScript class.
     *
     * @property NAME
     * @type String
     * @static
     */
    SHOWADVANCED.NAME = 'moodle-form-showadvanced';

    /**
     * Static property used to define the default attribute configuration for the Showadvanced.
     *
     * @property ATTRS
     * @type String
     * @static
     */
    SHOWADVANCED.ATTRS = ATTRS;

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

    Y.extend(SHOWADVANCED, Y.Base, {
        initializer : function() {
            var form = Y.one('#'+this.get('formid')),
                fieldlist = form.all(SELECTORS.FIELDSETCONTAINSADVANCED);
            // Look through fieldset divs that contain advanced elements.
            fieldlist.each(this.process_fieldset, this);
            // Subscribe more/less links to click event.
            form.delegate('click', this.switch_state, SELECTORS.MORELESSLINK);
            form.delegate('key', this.switch_state, 'down:enter,32', SELECTORS.MORELESSLINK);
        },
        process_fieldset : function(fieldset) {
            var statuselement = Y.one('input[name=mform_showmore_'+fieldset.get('id')+']');
            if (!statuselement) {
                Y.log("M.form.showadvanced::process_fieldset was called on an fieldset without a status field: '" +
                    fieldset.get('id') + "'", 'debug');
                return;
            }

            var morelesslink = Y.Node.create('<a href="#"></a>');
            morelesslink.addClass(CSS.MORELESSTOGGLER);
            if (statuselement.get('value') === '0') {
                morelesslink.setHTML(M.str.form.showmore);
            } else {
                morelesslink.setHTML(M.str.form.showless);
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
        },
        switch_state : function(e) {
            e.preventDefault();
            var fieldset = this.ancestor(SELECTORS.FIELDSETCONTAINSADVANCED);
            // Toggle collapsed class.
            fieldset.all(SELECTORS.DIVFITEMADVANCED).toggleClass(CSS.SHOW);
            // Get corresponding hidden variable.
            var statuselement = new Y.one('input[name=mform_showmore_'+fieldset.get('id')+']');
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
    });

    M.form = M.form || {};
    M.form.showadvanced = M.form.showadvanced || function(params) {
        return new SHOWADVANCED(params);
    };
}, '@VERSION@', {requires:['base', 'node', 'selector-css3']});
