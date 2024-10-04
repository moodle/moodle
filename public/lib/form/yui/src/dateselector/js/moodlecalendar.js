/**
 * Provides the Moodle Calendar class.
 *
 * @module moodle-form-dateselector
 */

/**
 * A class to overwrite the YUI3 Calendar in order to change the strings..
 *
 * @class M.form_moodlecalendar
 * @constructor
 * @extends Calendar
 */
MOODLECALENDAR = function() {
    MOODLECALENDAR.superclass.constructor.apply(this, arguments);
};

Y.extend(MOODLECALENDAR, Y.Calendar, {
        initializer: function(cfg) {
            this.set("strings.very_short_weekdays", cfg.WEEKDAYS_MEDIUM);
            this.set("strings.first_weekday", cfg.firstdayofweek);
        }
    }, {
        NAME: 'Calendar',
        ATTRS: {}
    }
);

M.form_moodlecalendar = M.form_moodlecalendar || {};
M.form_moodlecalendar.initializer = function(params) {
    return new MOODLECALENDAR(params);
};
