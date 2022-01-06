/**
 * Add a confirmation dialogue when cancelling a backup.
 *
 * @module moodle-backup-confirmcancel
 */

/**
 * Add a confirmation dialogue when cancelling a backup.
 *
 * @class M.core_backup.confirmcancel
 */


// Namespace for the backup.
M.core_backup = M.core_backup || {};

M.core_backup.confirmcancel = {
    /**
     * An array of EventHandlers which call the confirm_cancel dialogue.
     *
     * @property listeners
     * @protected
     * @type Array
     */
    listeners: [],

    /**
     * The configuration supplied to this instance.
     *
     * @property config
     * @protected
     * @type Object
     */
    config: {},

    /**
     * Initializer to watch all cancel buttons.
     *
     * @method watch_cancel_buttons
     * @param {Object} config The configuration for the confirmation dialogue.
     */
    watch_cancel_buttons: function(config) {
        this.config = config;

        this.listeners.push(
            Y.one(Y.config.doc.body).delegate('click', this.confirm_cancel, '.confirmcancel', this)
        );
    },

    /**
     * Display the confirmation dialogue.
     *
     * @method confirm_cancel
     * @protected
     * @param {EventFacade} e
     */
    confirm_cancel: function(e) {
        // Prevent the default event (submit) from firing.
        e.preventDefault();

        // Create the confirmation dialogue.
        var confirm = new M.core.confirm(this.config);

        // If the user clicks yes.
        confirm.on('complete-yes', function() {
            // Detach the listeners for the confirm box so they don't fire again.
            new Y.EventHandle(M.core_backup.confirmcancel.listeners).detach();

            // The currentTarget is a div surrounding the form elements. Simulating a click on the div is
            // not going to submit a form so we need to find the form element to click.
            var element = e.currentTarget.one('input, select, button');

            // Simulate the original cancel button click.
            if (element) {
                element.simulate('click');
            } else {
                // Backwards compatibility only.
                e.currentTarget.simulate('click');
            }
        }, this);


        // Show the confirm box.
        confirm.show();
    }
};
