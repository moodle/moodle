YUI.add('moodle-core-formchangechecker',
    function(Y) {
        // The CSS selectors we use
        var CSS = {
        };

        var FORMCHANGECHECKERNAME = 'core-formchangechecker';

        var FORMCHANGECHECKER = function() {
            FORMCHANGECHECKER.superclass.constructor.apply(this, arguments);
        }

        Y.extend(FORMCHANGECHECKER, Y.Base, {
                /**
                 * Initialize the module
                 */
                initializer : function(config) {
                    var formid = 'form#' + this.get('formid');

                    // Add change events to the form elements
                    Y.all(formid + ' input').once('change', M.core_formchangechecker.set_form_changed, this);
                    Y.all(formid + ' textarea').once('change', M.core_formchangechecker.set_form_changed, this);
                    Y.all(formid + ' select').once('change', M.core_formchangechecker.set_form_changed, this);

                    // We need any submit buttons on the form to set the submitted flag
                    Y.one(formid).on('submit', M.core_formchangechecker.set_form_submitted, this);

                    // YUI doesn't support onbeforeunload properly so we must use the DOM to set the onbeforeunload. As
                    // a result, the has_changed must stay in the DOM too
                    window.onbeforeunload = M.core_formchangechecker.report_form_dirty_state;
                },
            },
            {
                NAME : FORMCHANGECHECKERNAME,
                ATTRS : {
                    formid : {
                        'value' : ''
                    }
                }
            }
        );

        M.core_formchangechecker = M.core_formchangechecker || {};

        // We might have multiple instances of the form change protector
        M.core_formchangechecker.instances = M.core_formchangechecker.instances || [];
        M.core_formchangechecker.init = function(config) {
            var formchangechecker = new FORMCHANGECHECKER(config);
            M.core_formchangechecker.instances.push(formchangechecker);
            return formchangechecker;
        }

        // Store state information
        M.core_formchangechecker.stateinformation = [];

        /**
         * Set the form changed state to true
         */
        M.core_formchangechecker.set_form_changed = function() {
            M.core_formchangechecker.stateinformation.formchanged = 1;
        }

        /**
         * Set the form submitted state to true
         */
        M.core_formchangechecker.set_form_submitted = function() {
            M.core_formchangechecker.stateinformation.formsubmitted = 1;
        }

        /**
         * Attempt to determine whether the form has been modified in any way and
         * is thus 'dirty'
         *
         * @return Integer 1 is the form is dirty; 0 if not
         */
        M.core_formchangechecker.get_form_dirty_state = function() {
            var state = M.core_formchangechecker.stateinformation;

            // If the form was submitted, then return a non-dirty state
            if (state.formsubmitted) {
                return 0;
            }

            // If any fields have been marked dirty, return a dirty state
            if (state.formchanged) {
                return 1;
            }

            // Handle TinyMCE editor instances
            // We can't add a listener in the initializer as the editors may not have been created by that point
            // so we do so here instead
            if (typeof tinyMCE != 'undefined') {
                for (var editor in tinyMCE.editors) {
                    if (tinyMCE.editors[editor].isDirty()) {
                        return 1;
                    }
                }
            }

            // If we reached here, then the form hasn't met any of the dirty conditions
            return 0;
        };

        /**
         * Return a suitable message if changes have been made to a form
         */
        M.core_formchangechecker.report_form_dirty_state = function(e) {
            if (!M.core_formchangechecker.get_form_dirty_state()) {
                // the form is not dirty, so don't display any message
                return;
            }

            // This is the error message that we'll show to browsers which support it
            var warningmessage = M.util.get_string('changesmadereallygoaway', 'moodle');

            // Most browsers are happy with the returnValue being set on the event
            // But some browsers do not consistently pass the event
            if (e) {
                e.returnValue = warningmessage;
            }

            // But some require it to be returned instead
            return warningmessage;
        };
    },
    '@VERSION@', {
        requires : ['base']
    }
);
