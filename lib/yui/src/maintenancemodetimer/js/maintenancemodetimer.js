/**
 * Maintenance mode timer display.
 *
 * @module moodle-core-maintenancemodetimer
 */
var MAINTENANCEMODETIMER = function() {
        MAINTENANCEMODETIMER.superclass.constructor.apply(this, arguments);
    };

Y.extend(MAINTENANCEMODETIMER, Y.Base, {
    timeleftinsec: 0,
    maintenancenode: Y.one('.box.maintenancewarning'),

    /**
     * Initialise timer if maintenancemode set.
     *
     * @method initializer
     * @param config {Array} array with timeleftinsec set.
     */
    initializer: function(config) {
        if (this.maintenancenode) {
            this.timeleftinsec = config.timeleftinsec;
            this.maintenancenode.setAttribute('aria-live', 'polite');
            Y.later(1000, this, 'updatetimer', null, true);
        }
    },

    /**
     * Decrement time left and update display text.
     *
     * @method updatetimer
     */
    updatetimer: function() {
        this.timeleftinsec -= 1;
        if (this.timeleftinsec <= 0) {
            this.maintenancenode.set('text', M.str.admin.sitemaintenance);
        } else {
            var a = {};
            a.sec = this.timeleftinsec % 60;
            a.min = Math.floor(this.timeleftinsec / 60);
            this.maintenancenode.set('text', M.util.get_string('maintenancemodeisscheduled', 'admin', a));
        }
        // Set error class to highlight the importance.
        if (this.timeleftinsec < 30) {
            this.maintenancenode.addClass('error')
                    .removeClass('warning');
        } else {
            this.maintenancenode.addClass('warning')
                    .removeClass('error');
        }
    }
});

M.core = M.core || {};
M.core.maintenancemodetimer = M.core.maintenancemodetimer || function(config) {
    return new MAINTENANCEMODETIMER(config);
};
