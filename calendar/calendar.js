/**
 * Define a blocks namespace if it has not already been defined
 * @namespace
 */
M = M || {};
M.blocks = M.blocks || {};

/**
 * A calendar namespace for the calendar block
 * @namespace
 */
M.blocks.calendar = {
    // The seconds to delay the show of a calendar event by
    showdelaysecs: 1,
    // The calendar event currently pending display
    showdelayevent: null,
    // An array containing all calendar events
    events : [],
    /**
     * Callback function for the showback method
     * @function
     */
    show_event_callback : function() {
        if (M.blocks.calendar.showdelayevent !== null)  {
            M.blocks.calendar.events[M.blocks.calendar.showdelayevent].show();
        }
    }
}

/**
 * A calendar event class
 * @class
 * @constructor
 */
M.blocks.calendar.event = function(properties) {
    this.id = properties.id;
    this.title = properties.title;
    this.content = properties.content;
    this.displayed = false;
    this.panel = null;
    this.node = Y.one('#'+this.id);
    this.node.on('mouseenter', this.show_init, this);
    this.node.on('mouseleave', this.hide, this);
}

/**
 * Hides the events panel if it is being displayed
 * @function
 */
M.blocks.calendar.event.prototype.hide = function(e) {
    M.blocks.calendar.showdelayevent = null;
    if (this.displayed) {
        this.displayed = false;
        this.panel.hide();
    }
}

/**
 * Initialises the calendar event to show after the given delay
 * @function
 * @param {event} e
 */
M.blocks.calendar.event.prototype.show_init = function(e) {
    if (M.blocks.calendar.showdelayevent !== this.id) {
        if (M.blocks.calendar.showdelayevent !== null) {
            M.blocks.calendar.events[M.blocks.calendar.showdelayevent].hide(e);
        }
        M.blocks.calendar.showdelayevent = this.id;
        setTimeout(M.blocks.calendar.show_event_callback, M.blocks.calendar.showdelaysecs*1000);
    }
}

/**
 * Shows the calendar event
 * @function
 */
M.blocks.calendar.event.prototype.show = function() {
    this.panel = new YAHOO.widget.Panel(this.id+'_panel', {
        width:"240px",
        visible:false,
        draggable:false,
        close:false,
        constraintoviewport:true
    });
    this.panel.setHeader(this.title);
    this.panel.setBody(this.content);
    this.panel.render(this.id);
    this.panel.show();
    this.displayed = true;
};