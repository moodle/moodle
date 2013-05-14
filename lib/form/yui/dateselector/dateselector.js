YUI.add('moodle-form-dateselector', function(Y) {

    /**
     * Add some custom methods to the node class to make our lives a little
     * easier within this module.
     */
    Y.mix(Y.Node.prototype, {
        /**
         * Gets the value of the first option in the select box
         */
        firstOptionValue : function() {
            if (this.get('nodeName').toLowerCase() != 'select') {
                return false;
            }
            return this.one('option').get('value');
        },
        /**
         * Gets the value of the last option in the select box
         */
        lastOptionValue : function() {
            if (this.get('nodeName').toLowerCase() != 'select') {
                return false;
            }
            return this.all('option').item(this.optionSize()-1).get('value');
        },
        /**
         * Gets the number of options in the select box
         */
        optionSize : function() {
            if (this.get('nodeName').toLowerCase() != 'select') {
                return false;
            }
            return parseInt(this.all('option').size());
        },
        /**
         * Gets the value of the selected option in the select box
         */
        selectedOptionValue : function() {
            if (this.get('nodeName').toLowerCase() != 'select') {
                return false;
            }
            return this.all('option').item(this.get('selectedIndex')).get('value');
        }
    });

    /**
     * Calendar class
     *
     * This is our main class
     */
    var CALENDAR = function(config) {
        CALENDAR.superclass.constructor.apply(this, arguments);
    };
    CALENDAR.prototype = {
        panel : null,
        yearselect : null,
        monthselect : null,
        dayselect : null,
        calendarimage : null,
        enablecheckbox : null,
        closepopup : true,
        initializer : function(config) {
            var controls = this.get('node').all('select');
            controls.each(function(node){
                if (node.get('name').match(/\[year]/)) {
                    this.yearselect = node;
                } else if (node.get('name').match(/\[month\]/)) {
                    this.monthselect = node;
                } else if (node.get('name').match(/\[day]/)) {
                    this.dayselect = node;
                }
                node.after('change', this.handle_select_change, this);
            }, this);

            // Loop through the input fields.
            var inputs = this.get('node').all('input');
            inputs.each(function(node) {
                // Check if the current node is a calendar image field.
                if (node.get('name').match(/\[calendar]/)) {
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
        focus_event : function(e) {
            M.form.dateselector.cancel_any_timeout();
            // If the current owner is set, then the pop-up is currently being displayed, so hide it.
            if (M.form.dateselector.currentowner == this) {
                this.release_calendar();
            } else if ((this.enablecheckbox == null)
                || (this.enablecheckbox.get('checked'))) { // Must be hidden. If the field is enabled display the pop-up.
                this.claim_calendar();
            }
            // Stop the input image field from submitting the form.
            e.preventDefault();
        },
        handle_select_change : function(e) {
            // It may seem as if the following variable is not used, however any call to set_date_from_selects will trigger a
            // call to set_selects_from_date if the calendar is open as the date has changed. Whenever the calendar is displayed
            // the set_selects_from_date function is set to trigger on any date change (see function connect_handlers).
            this.closepopup = false;
            this.set_date_from_selects();
            this.closepopup = true;
        },
        claim_calendar : function() {
            M.form.dateselector.cancel_any_timeout();
            if (M.form.dateselector.currentowner == this) {
                return;
            }
            if (M.form.dateselector.currentowner) {
                M.form.dateselector.currentowner.release_calendar();
            }
            if (M.form.dateselector.currentowner != this) {
                this.connect_handlers();
                this.set_date_from_selects();
            }
            M.form.dateselector.currentowner = this;
            M.form.dateselector.calendar.cfg.setProperty('mindate', new Date(this.yearselect.firstOptionValue(), 0, 1));
            M.form.dateselector.calendar.cfg.setProperty('maxdate', new Date(this.yearselect.lastOptionValue(), 11, 31));
            M.form.dateselector.panel.show();
            M.form.dateselector.fix_position();
            setTimeout(function(){M.form.dateselector.cancel_any_timeout()}, 100);
        },
        set_date_from_selects : function() {
            var year = parseInt(this.yearselect.get('value'));
            var month = parseInt(this.monthselect.get('value')) - 1;
            var day = parseInt(this.dayselect.get('value'));
            M.form.dateselector.calendar.select(new Date(year, month, day));
            M.form.dateselector.calendar.setMonth(month);
            M.form.dateselector.calendar.setYear(year);
            M.form.dateselector.calendar.render();
        },
        set_selects_from_date : function(eventtype, args) {
            var date = args[0][0];
            var newyear = date[0];
            var newindex = newyear - this.yearselect.firstOptionValue();
            this.yearselect.set('selectedIndex', newindex);
            this.monthselect.set('selectedIndex', date[1] - this.monthselect.firstOptionValue());
            this.dayselect.set('selectedIndex', date[2] - this.dayselect.firstOptionValue());
            if (M.form.dateselector.currentowner && this.closepopup) {
                this.release_calendar();
            }
        },
        connect_handlers : function() {
            M.form.dateselector.calendar.selectEvent.subscribe(this.set_selects_from_date, this, true);
        },
        release_calendar : function() {
            M.form.dateselector.panel.hide();
            M.form.dateselector.currentowner = null;
            M.form.dateselector.calendar.selectEvent.unsubscribe(this.set_selects_from_date, this);
        },
        toggle_calendar_image : function() {
            // If the enable checkbox is not checked, disable the image.
            if (!this.enablecheckbox.get('checked')) {
                this.calendarimage.set('disabled', 'disabled');
                this.calendarimage.setStyle('cursor', 'default');
                this.release_calendar();
            } else {
                this.calendarimage.set('disabled', false);
                this.calendarimage.setStyle('cursor', 'auto');
            }
        }
    };
    Y.extend(CALENDAR, Y.Base, CALENDAR.prototype, {
        NAME : 'Date Selector',
        ATTRS : {
            firstdayofweek  : {
                validator : Y.Lang.isString
            },
            node : {
                setter : function(node) {
                    return Y.one(node);
                }
            }
        }
    });

    M.form = M.form || {};
    M.form.dateselector = {
        panel : null,
        calendar : null,
        currentowner : null,
        hidetimeout : null,
        repositiontimeout : null,
        init_date_selectors : function(config) {
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
        initPanel : function(config) {
            this.panel = new Y.Overlay({
                visible : false,
                bodyContent : Y.Node.create('<div id="dateselector-calendar-content"></div>'),
                id : 'dateselector-calendar-panel'
            });
            this.panel.render(document.body);
            // zIndex is added by panel.render() and is set to 0.
            // Remove zIndex from panel, as this should be set by CSS. This can be done by removeAttr but
            // ie8 fails and there is know issue for it.
            Y.one('#dateselector-calendar-panel').setStyle('zIndex', null);
            this.panel.on('heightChange', this.fix_position, this);

            Y.one('#dateselector-calendar-panel').on('click', function(e){e.halt();});
            Y.one(document.body).on('click', this.document_click, this);

            this.calendar = new Y.YUI2.widget.Calendar(document.getElementById('dateselector-calendar-content'), {
                iframe: false,
                hide_blank_weeks: true,
                start_weekday: config.firstdayofweek,
                locale_weekdays: 'medium',
                locale_months: 'long',
                WEEKDAYS_MEDIUM: [
                    config.sun,
                    config.mon,
                    config.tue,
                    config.wed,
                    config.thu,
                    config.fri,
                    config.sat ],
                MONTHS_LONG: [
                    config.january,
                    config.february,
                    config.march,
                    config.april,
                    config.may,
                    config.june,
                    config.july,
                    config.august,
                    config.september,
                    config.october,
                    config.november,
                    config.december ]
            });
            this.calendar.changePageEvent.subscribe(function(){
                this.fix_position();
            }, this);
        },
        cancel_any_timeout : function() {
            if (this.hidetimeout) {
                clearTimeout(this.hidetimeout);
                this.hidetimeout = null;
            }
            if (this.repositiontimeout) {
                clearTimeout(this.repositiontimeout);
                this.repositiontimeout = null;
            }
        },
        delayed_reposition : function() {
            if (this.repositiontimeout) {
                clearTimeout(this.repositiontimeout);
                this.repositiontimeout = null;
            }
            this.repositiontimeout = setTimeout(this.fix_position, 500);
        },
        fix_position : function() {
            if (this.currentowner) {
                this.panel.set('align', {
                    node:this.currentowner.get('node').one('select'),
                    points:[Y.WidgetPositionAlign.BL, Y.WidgetPositionAlign.TL]
                });
            }
        },
        document_click : function(e) {
            if (this.currentowner) {
                if (this.currentowner.get('node').ancestor('div').contains(e.target)) {
                    setTimeout(function() {M.form.dateselector.cancel_any_timeout()}, 100);
                } else {
                    this.currentowner.release_calendar();
                }
            }
        }
    }

}, '@VERSION@', {requires:['base','node','overlay', 'yui2-calendar', 'moodle-form-dateselector-skin']});
