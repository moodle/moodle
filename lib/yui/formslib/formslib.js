YUI.add('moodle-core-formslib',
    function(Y) {
        // The CSS selectors we use
        var CSS = {
        };

        var FORMSLIBNAME = 'core-formslib';

        var FORMSLIB = function() {
            FORMSLIB.superclass.constructor.apply(this, arguments);
        }

        Y.extend(FORMSLIB, Y.Base, {
                /**
                * Initialize the module
                */
                initializer : function(config) {
                    var formid = 'form#' + this.get('formid');

                    // Add change events to the form elements
                    Y.all(formid + ' input').on('change', M.util.set_form_changed, this);
                    Y.all(formid + ' textarea').on('change', M.util.set_form_changed, this);
                    Y.all(formid + ' select').on('change', M.util.set_form_changed, this);

                    // We need any submit buttons on the form to set the submitted flag
                    Y.one(formid).on('submit', M.util.set_form_submitted, this);

                    // YUI doesn't support onbeforeunload properly so we must use the DOM to set the onbeforeunload. As
                    // a result, the has_changed must stay in the DOM too
                    window.onbeforeunload = M.util.report_form_dirty_state;
                },

                /**
                 * Unset the form dirty state and also set the form submitted flag to true
                 */
                unset_changed : function(e) {
                    M.util.set_form_changed();
                }
            },
            {
                NAME : FORMSLIBNAME,
                ATTRS : {
                    formid : {
                        'value' : ''
                    }
                }
            }
        );

        M.core = M.core || {};
        M.core.init_formslib = function(config) {
            return new FORMSLIB(config);
        }

    },
    '@VERSION@', {
        requires : ['base']
    }
);
