/**
 * Define a blocks namespace if it has not already been defined
 * @namespace
 */
var blocks = blocks || {};

/**
 * A calendar namespace for the calendar block
 * @namespace
 */
blocks.calendar = {
    // The seconds to delay the show of a calendar event by
    showdelaysecs: 1,
    // The calendar event currently pending display
    showdelayevent: null,
    // An array containing all calendar events
    events : [],
    /**
     * Adds a new event
     * @function
     * @param {object} properties
     */
    add_event: function(properties) {
        Y.use('dom', 'event', 'node', function(){
            blocks.calendar.events[properties.id] = new blocks.calendarevent(properties);
        });
    },
    /**
     * Callback function for the showback method
     * @function
     */
    show_event_callback : function() {
        if (blocks.calendar.showdelayevent !== null)  {
            blocks.calendar.events[blocks.calendar.showdelayevent].show();
        }
    }
}

/**
 * A calendar event class
 * @class
 * @constructor
 */
blocks.calendarevent = function(properties) {
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
blocks.calendarevent.prototype.hide = function(e) {
    blocks.calendar.showdelayevent = null;
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
blocks.calendarevent.prototype.show_init = function(e) {
    if (blocks.calendar.showdelayevent !== this.id) {
        if (blocks.calendar.showdelayevent !== null) {
            blocks.calendar.events[blocks.calendar.showdelayevent].hide(e);
        }
        blocks.calendar.showdelayevent = this.id;
        setTimeout(blocks.calendar.show_event_callback, blocks.calendar.showdelaysecs*1000);
    }
}

/**
 * Shows the calendar event
 * @function
 */
blocks.calendarevent.prototype.show = function() {
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