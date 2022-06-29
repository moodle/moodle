/**
 * A utility to check for form changes before navigating away from a page.
 *
 * @module moodle-core-formchangechecker
 */

/**
 * A utility to check for form changes before navigating away from a page.
 *
 * Please note that this YUI module has been deprecated in favour of the core_form/changechecker AMD module.
 *
 * @class M.core.formchangechecker
 * @deprecated
 */

window.console.warn(
    'The moodle-core-formchangechecker has been deprecated ' +
    'and replaced with core_form/changechecker. ' +
    'Please update your code to make use of the new module.'
);

require(['core_form/changechecker'], function(ChangeChecker) {
    ChangeChecker.startWatching();
});

// The following are provided to prevent race conditions.
// Because the AMD module is loaded asynchronously after the YUI module is loaded, there is a possibility that the
// calling code may call the YUI function calls before the AMD module has finished loading.
// These will be removed in future and are automatically overwritten by the legacy helper provided as part of the new
// changechecker AMD module.
// eslint-disable-next-line camelcase
M.core_formchangechecker = M.core_formchangechecker || {
    init: function(config) {
        require(['core_form/changechecker'], function(ChangeChecker) {
            ChangeChecker.watchFormById(config.formid);
        });
    },

    /**
     * Set the form changed state to true
     */
    // eslint-disable-next-line camelcase
    set_form_changed: function() {
        require(['core_form/changechecker'], function(ChangeChecker) {
            ChangeChecker.markAllFormsAsDirty();
        });
    },

    /**
     * Set the form submitted state to true
     */
    // eslint-disable-next-line camelcase
    set_form_submitted: function() {
        require(['core_form/changechecker'], function(ChangeChecker) {
            ChangeChecker.markAllFormsSubmitted();
        });
    },

    /**
     * Reset the form state
     */
    // eslint-disable-next-line camelcase
    reset_form_dirty_state: function() {
        require(['core_form/changechecker'], function(ChangeChecker) {
            ChangeChecker.resetAllFormDirtyStates();
        });
    },
};
