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
