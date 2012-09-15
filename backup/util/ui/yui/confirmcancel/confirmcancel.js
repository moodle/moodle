YUI.add('moodle-backup-confirmcancel', function(Y) {

// Namespace for the backup
M.core_backup = M.core_backup || {};
/**
 * Adds confirmation dialogues to the cancel buttons on the page.
 *
 * @param {object} config
 */
M.core_backup.watch_cancel_buttons = function(config) {
    Y.all('.confirmcancel').each(function(){
        this._confirmationListener = this._confirmationListener || this.on('click', function(e){
            // Prevent the default event (sumbit) from firing
            e.preventDefault();
            // Create the confirm box
            var confirm = new M.core.confirm(config);
            // If the user clicks yes
            confirm.on('complete-yes', function(e){
                // Detach the listener for the confirm box so it doesn't fire again.
                this._confirmationListener.detach();
                // Simulate the original cancel button click
                this.simulate('click');
            }, this);
            // Show the confirm box
            confirm.show();
        }, this);
    });
}

}, '@VERSION@', {'requires':['base','node','node-event-simulate','moodle-core-notification']});
