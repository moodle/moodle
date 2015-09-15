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
        return this.all('option').item(this.optionSize()-1).get('value');
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
            id: 'dateselector-calendar-panel'
        });
        this.panel.render(document.body);
        // zIndex is added by panel.render() and is set to 0.
        // Remove zIndex from panel, as this should be set by CSS. This can be done by removeAttr but
        // ie8 fails and there is know issue for it.
        Y.one('#dateselector-calendar-panel').setStyle('zIndex', null);
        this.panel.on('heightChange', this.fix_position, this);

        Y.one('#dateselector-calendar-panel').on('click', function(e){e.halt();});
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
                config.sat ]
        });
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
