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
            COLLAPSEEXPAND : '.collapsible-actions .collapseexpand',
            FIELDSETCOLLAPSIBLE : 'fieldset.collapsible',
            FIELDSETLEGENDLINK : 'fieldset.collapsible .fheader',
            LEGENDFTOGGLER : 'legend.ftoggler'
        },
        CSS = {
            COLLAPSEALL : 'collapse-all',
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
                fieldlist,
                btn,
                link,
                idlist;
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
            form.delegate('click', this.switch_state, SELECTORS.FIELDSETLEGENDLINK, this);
            form.delegate('key', this.switch_state, 'down:enter,32', SELECTORS.FIELDSETLEGENDLINK, this);

            // Make the collapse/expand a link.
            btn = form.one(SELECTORS.COLLAPSEEXPAND);
            if (btn) {
                link = Y.Node.create('<a href="#"></a>');
                link.setHTML(btn.getHTML());
                link.setAttribute('class', btn.getAttribute('class'));
                link.setAttribute('role', 'button');

                // Get list of IDs controlled by this button to set the aria-controls attribute.
                idlist = [];
                form.all(SELECTORS.FIELDSETLEGENDLINK).each(function(node) {
                    idlist[idlist.length] = node.generateID();
                });
                link.setAttribute('aria-controls', idlist.join(' '));

                // Placing the button and binding the event.
                link.on('click', this.set_state_all, this, true);
                link.on('key', this.set_state_all, 'down:enter,32', this, true);
                btn.replace(link);
                this.update_btns(form);
            }
        },
        process_fieldset : function(fieldset) {
            // Get legend element.
            var legendelement = fieldset.one(SELECTORS.LEGENDFTOGGLER);

            // Turn headers to links for accessibility.
            var headerlink = Y.Node.create('<a href="#"></a>');
            headerlink.addClass(CSS.FHEADER);
            headerlink.appendChild(legendelement.get('firstChild'));
            headerlink.setAttribute('role', 'button');
            headerlink.setAttribute('aria-controls', fieldset.generateID());
            legendelement.prepend(headerlink);
        },
        set_state: function(fieldset, collapsed) {
            if (collapsed) {
                fieldset.addClass(CSS.COLLAPSED);
            } else {
                fieldset.removeClass(CSS.COLLAPSED);
            }
            var statuselement = this.form.one('input[name=mform_isexpanded_'+fieldset.get('id')+']');
            if (!statuselement) {
                Y.log("M.form.shortforms::switch_state was called on an fieldset without a status field: '" +
                    fieldset.get('id') + "'", 'debug');
                return;
            }
            statuselement.set('value', collapsed ? 0 : 1);
        },
        set_state_all: function(e) {
            e.preventDefault();
            var collapsed = e.target.hasClass(CSS.COLLAPSEALL),
                fieldlist = this.form.all(SELECTORS.FIELDSETCOLLAPSIBLE);
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
                expandbtn = false,
                fieldlist;

            btn = this.form.one(SELECTORS.COLLAPSEEXPAND);
            if (!btn) {
                return;
            }

            // Counting the number of collapsed sections.
            fieldlist = this.form.all(SELECTORS.FIELDSETCOLLAPSIBLE);
            fieldlist.each(function(node) {
                if (node.hasClass(CSS.COLLAPSED)) {
                    collapsed++;
                }
            });

            if (collapsed !== 0) {
                expandbtn = true;
            }

            // Updating the button.
            if (expandbtn) {
                btn.removeClass(CSS.COLLAPSEALL);
                btn.setHTML(M.util.get_string('expandall', 'moodle'));
            } else {
                btn.addClass(CSS.COLLAPSEALL);
                btn.setHTML(M.util.get_string('collapseall', 'moodle'));
            }
        }
    });

    M.form = M.form || {};
    M.form.shortforms = M.form.shortforms || function(params) {
        return new SHORTFORMS(params);
    };
}, '@VERSION@', {requires:['base', 'node', 'selector-css3']});
