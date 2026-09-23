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
    modalwatcher: null,
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

        // When the panel is shown inside a modal dialogue it becomes a descendant of the modal, so
        // an Escape keypress in the open calendar would otherwise bubble up to the modal's own
        // Escape handler and close the whole modal. Stop the Escape keydown here so that it only
        // closes the calendar (handled on keyup); do not interfere with any other keys.
        Y.one('#dateselector-calendar-panel').on('keydown', function(e) {
            if (this.currentowner && e.keyCode === 27) {
                e.stopPropagation();
            }
        }, this);

        Y.one(document.body).on('click', this.document_click, this);

        this.calendar = new MOODLECALENDAR({
            contentBox: "#dateselector-calendar-content",
            width: "300px",
            showPrevMonth: true,
            showNextMonth: true,
            firstdayofweek: parseInt(config.firstdayofweek, 10),
            headerRenderer: function(date) {
                if (!M.cfg.userId) {
                    // Anonymous context (e.g. signup page) — avoid a login-required AJAX call.
                    return date.toLocaleDateString(undefined, {month: 'long', year: 'numeric'});
                }

                var calendar = this;
                var headerNode = calendar.get('contentBox')
                    .one('#' + calendar._calendarId + '_header');
                var currentHeader = headerNode ? headerNode.getContent() : '';
                var placeholder = M.util.get_string('loading', 'moodle');

                // We fetch the current language's preferred time format from the language pack.
                require(['core/user_date', 'core/notification'], function(UserDate, Notification) {
                    UserDate.get([{
                        timestamp: Math.floor(date.getTime() / 1000),
                        format: M.util.get_string('strftimemonthyear', 'langconfig'),
                    }]).then(function(dateStrs) {
                        var headerNode = calendar.get('contentBox')
                            .one('#' + calendar._calendarId + '_header');
                        if (headerNode) {
                            headerNode.setContent(dateStrs[0]);
                        }
                        return dateStrs[0];
                    }).catch(Notification.exception);
                });
                return currentHeader || placeholder;
            },

            WEEKDAYS_MEDIUM: [
                config.sun,
                config.mon,
                config.tue,
                config.wed,
                config.thu,
                config.fri,
                config.sat,
            ],
        });
    },
    /**
     * Move the shared calendar panel into (or out of) the modal dialogue that owns the given
     * calendar trigger node so that Moodle's modal focus lock (which restricts tabbing to
     * descendants of the modal root) includes the popup calendar.
     *
     * Without this, the calendar panel remains a child of `document.body` (where it was originally
     * rendered), so it sits outside the modal's focus trap and cannot be reached or navigated
     * with the keyboard while a modal dialogue is open.
     *
     * @param {Y.Node} triggernode The calendar toggle button that was used to open the panel.
     */
    movePanelForContext: function(triggernode) {
        var panelnode = this.panel.get('boundingBox');
        var modal = triggernode.ancestor('.modal');
        var target = modal || Y.one(document.body);
        if (panelnode.get('parentNode') !== target) {
            target.append(panelnode);
            if (!modal) {
                // The panel is being moved back out to the document body. If it was previously
                // hosted by a modal that has since been hidden, that modal's Aria.hide() will have
                // forced tabindex="-1" on the panel's focusable controls (storing the originals in
                // a data attribute) and the matching Aria.unhide() never reached them because the
                // panel had already left the modal. Restore that focusability now, otherwise the
                // calendar's controls stay unreachable by keyboard on the next open.
                require(['core/aria'], function(Aria) {
                    Aria.unhide(panelnode.getDOMNode());
                });
            }
        }
        // Watch the hosting modal (if any) so the shared panel is released back to the document
        // body should the modal be dismissed while the calendar is still open.
        this.watchModalForContext(modal);
    },
    /**
     * Track the modal dialogue that currently hosts the shared calendar panel so that the
     * calendar is released the moment that modal is hidden or destroyed.
     *
     * The panel is a singleton that gets reparented into the modal (see movePanelForContext) so
     * that it lives inside the modal's focus lock. If the modal is dismissed directly (for
     * example via its own Cancel button) while the calendar is still open, the panel would
     * otherwise be removed from the DOM along with the modal, leaving every date field on the
     * page unusable until a page reload. Releasing the calendar on the modal's hide/destroy
     * events moves the panel back out to the document body before that happens.
     *
     * @param {Y.Node|null} modal The modal dialogue hosting the panel, or null when it is back on
     *     the document body.
     */
    watchModalForContext: function(modal) {
        // Detach any watcher registered for a previously hosting modal.
        if (this.modalwatcher) {
            this.modalwatcher();
            this.modalwatcher = null;
        }
        if (!modal) {
            return;
        }
        var modalnode = modal.getDOMNode();
        // Moodle's modal events are triggered via jQuery, so they can only be observed with it.
        require(['jquery', 'core/modal_events'], function($, ModalEvents) {
            var release = function() {
                if (M.form.dateselector.currentowner) {
                    M.form.dateselector.currentowner.release_calendar();
                }
            };
            var events = ModalEvents.hidden + ' ' + ModalEvents.destroyed;
            $(modalnode).on(events, release);
            M.form.dateselector.modalwatcher = function() {
                $(modalnode).off(events, release);
            };
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
