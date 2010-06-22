/**
 * Define a blocks namespace if it has not already been defined
 * @namespace
 */
M = M || {};

/**
 * A calendar namespace for the calendar block
 * @namespace
 */
M.core_calendar = {
    // The seconds to delay the show of a calendar event by
    showdelaysecs: 1,
    // The calendar event currently pending display
    showdelayevent: null,
    // An array containing all calendar events
    events : [],
    init : function(Y, properties) {
        var id = properties.id;
        this.events[id] = (function(Y, properties){
            // Prepares an event object that this function will return
            var event = {
                id : properties.id,
                title : properties.title,
                content : properties.content,
                displayed : false,
                panel : null,
                node : Y.one('#'+properties.id),
                /**
                 * Initialises the calendar event to show after the given delay
                 * @function
                 * @param {Event} e
                 */
                show_init : function(e) {
                    if (M.core_calendar.showdelayevent !== this.id) {
                        if (M.core_calendar.showdelayevent !== null) {
                            M.core_calendar.events[M.blocks.calendar.showdelayevent].hide(e);
                        }
                        M.core_calendar.showdelayevent = this.id;
                        setTimeout(M.core_calendar.show_event_callback, M.core_calendar.showdelaysecs*1000);
                    }
                },
                /**
                 * Hides the events panel if it is being displayed
                 * @function
                 * @param {Event} e
                 */
                hide : function(e) {
                    M.core_calendar.showdelayevent = null;
                    if (this.displayed) {
                        this.displayed = false;
                        this.panel.hide();
                    }
                },
                /**
                 * Shows the calendar event
                 * @function
                 */
                show : function() {
                    this.panel = new YAHOO.widget.Panel(this.id+'_panel', {
                        width:"240px",
                        visible:false,
                        draggable:false,
                        close:false,
                        constraintoviewport:true,
                        context: [this.id, 'tl', 'br', ["beforeShow", "windowResize"]]
                    });
                    this.panel.setHeader(this.title);
                    this.panel.setBody(this.content);
                    this.panel.render(Y.one(document.body));
                    this.panel.show();
                    this.displayed = true;
                }
            }
            event.node.on('mouseenter', event.show_init, event);
            event.node.on('mouseleave', event.hide, event);
            return event;
        })(Y, properties);
    },
    /**
     * Callback function for the showback method
     * @function
     */
    show_event_callback : function() {
        if (M.core_calendar.showdelayevent !== null)  {
            M.core_calendar.events[M.core_calendar.showdelayevent].show();
        }
    },
    init_basic_export : function(Y, allowthisweek, allownextweek, allownextmonth, username, authtoken) {
        Y.one('#generateurl').on('click', function(){
            var presetwhat = 'all';
            if (Y.one('#pw_course').get('checked')) {
                presetwhat = 'courses';
            }

            var presettime = 'recentupcoming';
            if (allowthisweek && Y.one('#pt_wknow').get('checked')) {
                presettime = 'weeknow';
            } else if (allownextweek && Y.one('#pt_wknext').get('checked')) {
                presettime = 'weeknext';
            } else if (allownextmonth && Y.one('#pt_monnext').get('checked')) {
                presettime = 'monthnext';
            } else if (Y.one('#pt_monnow').get('checked')) {
                presettime = 'monthnow';
            }

            var urlstr = M.cfg.wwwroot+'/calendar/export_execute.php?preset_what='+presetwhat+'&amp;preset_time='+presettime+'&amp;username='+username+'&amp;authtoken='+authtoken;
            Y.one('#url').setContent(urlstr);
            Y.one('#urlbox').setStyle('display', 'block');
        }, this);
    }
}