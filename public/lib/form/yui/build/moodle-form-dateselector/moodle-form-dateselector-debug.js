YUI.add('moodle-form-dateselector', function (Y, NAME) {

var CALENDAR;
var MOODLECALENDAR;
var DIALOGUE_SELECTOR = ' [role=dialog]',
    MENUBAR_SELECTOR = '[role=menubar]',
    DOT = '.',
    HAS_ZINDEX = 'moodle-has-zindex';

/**
 * Add some custom methods to the node class to make our lives a little
 * easier within this module.
 */
Y.mix(Y.Node.prototype, {
    /**
     * Gets the value of the first option in the select box
     */
    firstOptionValue: function() {
        if (this.get('nodeName').toLowerCase() !== 'select') {
            return false;
        }
        return this.one('option').get('value');
    },
    /**
     * Gets the value of the last option in the select box
     */
    lastOptionValue: function() {
        if (this.get('nodeName').toLowerCase() !== 'select') {
            return false;
        }
        return this.all('option').item(this.optionSize() - 1).get('value');
    },
    /**
     * Gets the number of options in the select box
     */
    optionSize: function() {
        if (this.get('nodeName').toLowerCase() !== 'select') {
            return false;
        }
        return parseInt(this.all('option').size(), 10);
    },
    /**
     * Gets the value of the selected option in the select box
     */
    selectedOptionValue: function() {
        if (this.get('nodeName').toLowerCase() !== 'select') {
            return false;
        }
        return this.all('option').item(this.get('selectedIndex')).get('value');
    }
});

M.form = M.form || {};
M.form.dateselector = {
    panel: null,
    calendar: null,
    currentowner: null,
    hidetimeout: null,
    repositiontimeout: null,
    init_date_selectors: function(config) {
        if (this.panel === null) {
            this.initPanel(config);
        }
        Y.all('.fdate_time_selector').each(function() {
            config.node = this;
            new CALENDAR(config);
        });
        Y.all('.fdate_selector').each(function() {
            config.node = this;
            new CALENDAR(config);
        });
    },
    initPanel: function(config) {
        this.panel = new Y.Overlay({
            visible: false,
            bodyContent: Y.Node.create('<div id="dateselector-calendar-content"></div>'),
            id: 'dateselector-calendar-panel',
            constrain: true // constrain panel to viewport.
        });
        this.panel.render(document.body);

        // Determine the correct zindex by looking at all existing dialogs and menubars in the page.
        this.panel.on('focus', function() {
            var highestzindex = 0;
            Y.all(DIALOGUE_SELECTOR + ', ' + MENUBAR_SELECTOR + ', ' + DOT + HAS_ZINDEX).each(function(node) {
                var zindex = this.findZIndex(node);
                if (zindex > highestzindex) {
                    highestzindex = zindex;
                }
            }, this);
            // Only set the zindex if we found a wrapper.
            var zindexvalue = (highestzindex + 1).toString();
            Y.one('#dateselector-calendar-panel').setStyle('zIndex', zindexvalue);
        }, this);

        this.panel.on('heightChange', this.fix_position, this);

        Y.one('#dateselector-calendar-panel').on('click', function(e) {
            e.halt();
        });
        Y.one(document.body).on('click', this.document_click, this);

        this.calendar = new MOODLECALENDAR({
            contentBox: "#dateselector-calendar-content",
            width: "300px",
            showPrevMonth: true,
            showNextMonth: true,
            firstdayofweek: parseInt(config.firstdayofweek, 10),
            WEEKDAYS_MEDIUM: [
                config.sun,
                config.mon,
                config.tue,
                config.wed,
                config.thu,
                config.fri,
                config.sat]
        });
    },
    findZIndex: function(node) {
        // In most cases the zindex is set on the parent of the dialog.
        var zindex = node.getStyle('zIndex') || node.ancestor().getStyle('zIndex');
        if (zindex) {
            return parseInt(zindex, 10);
        }
        return 0;
    },
    cancel_any_timeout: function() {
        if (this.hidetimeout) {
            clearTimeout(this.hidetimeout);
            this.hidetimeout = null;
        }
        if (this.repositiontimeout) {
            clearTimeout(this.repositiontimeout);
            this.repositiontimeout = null;
        }
    },
    delayed_reposition: function() {
        if (this.repositiontimeout) {
            clearTimeout(this.repositiontimeout);
            this.repositiontimeout = null;
        }
        this.repositiontimeout = setTimeout(this.fix_position, 500);
    },
    fix_position: function() {
        if (this.currentowner) {
            var alignpoints = [
                Y.WidgetPositionAlign.BL,
                Y.WidgetPositionAlign.TL
            ];

            // Change the alignment if this is an RTL language.
            if (window.right_to_left()) {
                alignpoints = [
                    Y.WidgetPositionAlign.BR,
                    Y.WidgetPositionAlign.TR
                ];
            }

            this.panel.set('align', {
                node: this.currentowner.get('node').one('select'),
                points: alignpoints
            });
        }
    },
    document_click: function(e) {
        if (this.currentowner) {
            if (this.currentowner.get('node').ancestor('div').contains(e.target)) {
                setTimeout(function() {
                    M.form.dateselector.cancel_any_timeout();
                }, 100);
            } else {
                this.currentowner.release_calendar(e);
            }
        }
    }
};
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
/**
 * Provides the Calendar class.
 *
 * @module moodle-form-dateselector
 */

/**
 * Calendar class
 */
CALENDAR = function() {
    CALENDAR.superclass.constructor.apply(this, arguments);
};
CALENDAR.prototype = {
    panel: null,
    yearselect: null,
    monthselect: null,
    dayselect: null,
    calendarimage: null,
    enablecheckbox: null,
    closepopup: true,
    initializer: function() {
        var controls = this.get('node').all('select');
        controls.each(function(node) {
            if (node.get('name').match(/\[year\]/)) {
                this.yearselect = node;
            } else if (node.get('name').match(/\[month\]/)) {
                this.monthselect = node;
            } else if (node.get('name').match(/\[day\]/)) {
                this.dayselect = node;
            }
            node.after('change', this.handle_select_change, this);
        }, this);

        // Loop through the input fields.
        var inputs = this.get('node').all('input, a');
        inputs.each(function(node) {
            // Check if the current node is a calendar image field.
            if (node.get('name').match(/\[calendar\]/)) {
                // Set it so that when the image is clicked the pop-up displays.
                node.on('click', this.focus_event, this);
                // Set the node to the calendarimage variable.
                this.calendarimage = node;
            } else { // Must be the enabled checkbox field.
                // If the enable checkbox is clicked we want to either disable/enable the calendar image.
                node.on('click', this.toggle_calendar_image, this);
                // Set the node to the enablecheckbox variable.
                this.enablecheckbox = node;
            }
            // Ensure that the calendarimage and enablecheckbox values have been set.
            if (this.calendarimage && this.enablecheckbox) {
                // Set the calendar icon status depending on the value of the checkbox.
                this.toggle_calendar_image();
            }
        }, this);

        // Get the calendarimage element by its ID and check if any of its parents have the modal-dialog class to
        // know if the link is inside a modal, if so, set the aria-hidden and tabindex properties to the indicated values.
        var calendarimageelement = document.getElementById(this.calendarimage.get('id'));
        if (calendarimageelement.closest('.modal-dialog')) {
            this.calendarimage.set('aria-hidden', true);
            this.calendarimage.set('tabIndex', '-1');
        }
    },
    focus_event: function(e) {
        M.form.dateselector.cancel_any_timeout();
        // If the current owner is set, then the pop-up is currently being displayed, so hide it.
        if (M.form.dateselector.currentowner === this) {
            this.release_calendar();
        } else if ((this.enablecheckbox === null)
            || (this.enablecheckbox.get('checked'))) { // Must be hidden. If the field is enabled display the pop-up.
            this.claim_calendar();
        }
        // Stop the input image field from submitting the form.
        e.preventDefault();
    },
    handle_select_change: function() {
        // It may seem as if the following variable is not used, however any call to set_date_from_selects will trigger a
        // call to set_selects_from_date if the calendar is open as the date has changed. Whenever the calendar is displayed
        // the set_selects_from_date function is set to trigger on any date change (see function connect_handlers).
        this.closepopup = false;
        this.set_date_from_selects();
        this.closepopup = true;
    },
    claim_calendar: function() {
        M.form.dateselector.cancel_any_timeout();
        if (M.form.dateselector.currentowner === this) {
            return;
        }
        if (M.form.dateselector.currentowner) {
            M.form.dateselector.currentowner.release_calendar();
        }
        if (M.form.dateselector.currentowner !== this) {
            this.connect_handlers();
            this.set_date_from_selects();
        }
        M.form.dateselector.currentowner = this;
        M.form.dateselector.calendar.set('minimumDate', new Date(this.yearselect.firstOptionValue(), 0, 1));
        M.form.dateselector.calendar.set('maximumDate', new Date(this.yearselect.lastOptionValue(), 11, 31));
        M.form.dateselector.panel.show();
        M.form.dateselector.calendar.show();
        M.form.dateselector.fix_position();
        setTimeout(function() {
            M.form.dateselector.cancel_any_timeout();
        }, 100);

        // Focus on the calendar.
        M.form.dateselector.calendar.focus();

        // When the user tab out the calendar, close it.
        Y.one(document.body).on('keyup', function(e) {
            // If the calendar is open and we try to access it by pressing tab, we check if it is inside a Bootstrap dropdown-menu,
            // if so, we keep the dropdown open while navigation takes place in the calendar.
            if (M.form.dateselector.currentowner && e.keyCode === 9) {
                e.stopPropagation();
                var calendarimageelement = document.getElementById(M.form.dateselector.currentowner.calendarimage.get('id'));
                if (M.form.dateselector.calendar.get('focused') && calendarimageelement.closest('.dropdown-menu') &&
                    !calendarimageelement.closest('.dropdown-menu').classList.contains("show")) {
                    calendarimageelement.closest('.dropdown-menu').classList.add('show');
                }
            }

            // hide the calendar if we press a key and the calendar is not focussed, or if we press ESC in the calendar.
            if ((M.form.dateselector.currentowner === this && !M.form.dateselector.calendar.get('focused')) ||
                ((e.keyCode === 27) && M.form.dateselector.calendar.get('focused'))) {
                // Focus back on the calendar button.
                this.calendarimage.focus();
                this.release_calendar();
            }
        }, this);

    },
    set_date_from_selects: function() {
        var year = parseInt(this.yearselect.get('value'), 10);
        var month = parseInt(this.monthselect.get('value'), 10) - 1;
        var day = parseInt(this.dayselect.get('value'), 10);
        var date = new Date(year, month, day);
        M.form.dateselector.calendar.deselectDates();
        M.form.dateselector.calendar.selectDates([date]);
        M.form.dateselector.calendar.set("date", date);
        M.form.dateselector.calendar.render();
        if (date.getDate() !== day) {
            // Must've selected the 29 to 31st of a month that doesn't have such dates.
            this.dayselect.set('value', date.getDate());
            this.monthselect.set('value', date.getMonth() + 1);
        }
    },
    set_selects_from_date: function(ev) {
        var date = ev.newSelection[0];
        var newyear = Y.DataType.Date.format(date, {format: "%Y"});
        var newindex = newyear - this.yearselect.firstOptionValue();
        this.yearselect.set('selectedIndex', newindex);
        this.monthselect.set('selectedIndex', Y.DataType.Date.format(date, {format: "%m"}) - this.monthselect.firstOptionValue());
        this.dayselect.set('selectedIndex', Y.DataType.Date.format(date, {format: "%d"}) - this.dayselect.firstOptionValue());
        if (M.form.dateselector.currentowner && this.closepopup) {
            this.release_calendar();
        }
    },
    connect_handlers: function() {
        M.form.dateselector.calendar.on('selectionChange', this.set_selects_from_date, this, true);
    },
    release_calendar: function(e) {
        var wasOwner = M.form.dateselector.currentowner === this;
        M.form.dateselector.panel.hide();
        M.form.dateselector.calendar.detach('selectionChange', this.set_selects_from_date);
        M.form.dateselector.calendar.hide();
        M.form.dateselector.currentowner = null;

        // Put the focus back to the image calendar that we clicked, only if it was visible.
        if (wasOwner && (e === null || typeof e === "undefined" || e.type !== "click")) {
            this.calendarimage.focus();
        }
    },
    toggle_calendar_image: function() {
        // If the enable checkbox is not checked, disable the calendar image and prevent focus.
        if (!this.enablecheckbox.get('checked')) {
            this.calendarimage.addClass('disabled');
            this.release_calendar();
        } else {
            this.calendarimage.removeClass('disabled');
        }
    }
};
Y.extend(CALENDAR, Y.Base, CALENDAR.prototype, {
    NAME: 'Date Selector',
    ATTRS: {
        firstdayofweek: {
            validator: Y.Lang.isString
        },
        node: {
            setter: function(node) {
                return Y.one(node);
            }
        }
    }
});


}, '@VERSION@', {"requires": ["base", "node", "overlay", "calendar"]});
