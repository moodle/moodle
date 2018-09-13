/* global YUI */
// eslint-disable-next-line new-cap
YUI().use('yui2-container', 'yui2-calendar', function(Y) {
    var YAHOO = Y.YUI2;

    document.body.className += ' yui-skin-sam';

    YAHOO.util.Event.onDOMReady(function() {

        var Event = YAHOO.util.Event,
            Dom = YAHOO.util.Dom,
            dialog, calendar;

        var showBtn = Dom.get("show");

        Event.on(showBtn, "click", function() {
            /**
             * Reset handler and set current day.
             */
            function resetHandler() {
                calendar.cfg.setProperty("pagedate", calendar.today);
                calendar.render();
            }

            /**
             * Close dialog.
             */
            function closeHandler() {
                dialog.hide();
            }

            // Lazy Dialog Creation - Wait to create the Dialog, and setup document click listeners,
            // until the first time the button is clicked.
            if (!dialog) {

                // Hide Calendar if we click anywhere in the document other than the calendar.
                Event.on(document, "click", function(e) {
                    var el = Event.getTarget(e);
                    var dialogEl = dialog.element;
                    if (el != dialogEl && !Dom.isAncestor(dialogEl, el) && el != showBtn && !Dom.isAncestor(showBtn, el)) {
                        dialog.hide();
                    }
                });

                dialog = new YAHOO.widget.Dialog("attcalendarcontainer", {
                    visible: false,
                    context: ["show", "tl", "bl"],
                    buttons: [{text: M.util.get_string('caltoday', 'attendance'), handler: resetHandler, isDefault: true},
                             {text: M.util.get_string('calclose', 'attendance'), handler: closeHandler}],
                    draggable: false,
                    close: false
                });
                dialog.setHeader('');
                dialog.setBody('<div id="cal"></div>');
                dialog.render(document.body);

                dialog.showEvent.subscribe(function() {
                    if (YAHOO.env.ua.ie) {
                        // Since we're hiding the table using yui-overlay-hidden, we
                        // want to let the dialog know that the content size has changed, when
                        // shown.
                        dialog.fireEvent("changeContent");
                    }
                });
            }

            // Lazy Calendar Creation - Wait to create the Calendar until the first time the button is clicked.
            if (!calendar) {

                calendar = new YAHOO.widget.Calendar("cal", {
                    iframe: false, // Turn iframe off, since container has iframe support.
                    // eslint-disable-next-line camelcase
                    hide_blank_weeks: true // Enable, to demonstrate how we handle changing height, using changeContent.
                });

                calendar.cfg.setProperty("start_weekday", M.attendance.cal_start_weekday);
                calendar.cfg.setProperty("MONTHS_LONG", M.attendance.cal_months);
                calendar.cfg.setProperty("WEEKDAYS_SHORT", M.attendance.cal_week_days);
                calendar.select(new Date(M.attendance.cal_cur_date * 1000));
                calendar.render();

                calendar.selectEvent.subscribe(function() {
                    if (calendar.getSelectedDates().length > 0) {

                        Dom.get("curdate").value = calendar.getSelectedDates()[0] / 1000;

                        Dom.get("currentdate").submit();
                    }
                    dialog.hide();
                });

                calendar.renderEvent.subscribe(function() {
                    // Tell Dialog it's contents have changed, which allows
                    // container to redraw the underlay (for IE6/Safari2).
                    dialog.fireEvent("changeContent");
                });
            }

            var seldate = calendar.getSelectedDates();

            if (seldate.length > 0) {
                // Set the pagedate to show the selected date if it exists.
                calendar.cfg.setProperty("pagedate", seldate[0]);
                calendar.render();
            }

            dialog.show();
        });
    });
});
