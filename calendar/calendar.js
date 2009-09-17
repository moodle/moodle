function attach_calendar_panel(properties) {
    YAHOO.namespace("moodle.container");
    YAHOO.moodle.container[properties.id] = new YAHOO.widget.Panel(properties.id+'_panel', {
        width:"320px",
        visible:false,
        draggable:false,
        close:false,
        constraintoviewport:true
    } );
    YAHOO.moodle.container[properties.id].setHeader(properties.title);
    YAHOO.moodle.container[properties.id].setBody(properties.content);
    YAHOO.moodle.container[properties.id].render(properties.id);
    YAHOO.util.Event.addListener(properties.id, 'mouseover', YAHOO.moodle.container[properties.id].show, YAHOO.moodle.container[properties.id], true);
    YAHOO.util.Event.addListener(properties.id, 'mouseout', YAHOO.moodle.container[properties.id].hide, YAHOO.moodle.container[properties.id], true);
}