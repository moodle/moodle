/**
 * Provides the Calendar class.
 *
 * @module moodle-form-dateselector
 */

/**
 * Calendar class
 */
var CALENDAR = function() {
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
        controls.each(function(node){
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
        M.form.dateselector.calendar.set('mindate', new Date(this.yearselect.firstOptionValue(), 0, 1));
        M.form.dateselector.calendar.set('maxdate', new Date(this.yearselect.lastOptionValue(), 11, 31));
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
        // If the enable checkbox is det checked, disable the image.
        if (!this.enablecheckbox.get('checked')) {
            this.calendarimage.set('disabled', 'disabled');
            this.calendarimage.setStyle('cursor', 'default');
            this.release_calendar();
        } else {
            this.calendarimage.set('disabled', false);
            this.calendarimage.setStyle('cursor', null);
        }
    }
};
Y.extend(CALENDAR, Y.Base, CALENDAR.prototype, {
    NAME: 'Date Selector',
    ATTRS: {
        firstdayofweek : {
            validator: Y.Lang.isString
        },
        node: {
            setter: function(node) {
                return Y.one(node);
            }
        }
    }
});
