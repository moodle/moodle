/**
 * Some important namespaces that we really ought to have.
 * These just help us contain and organise our JavaScript.
 */
YAHOO.namespace("moodle.container");
YAHOO.namespace("moodle.container.calendarpanels");
YAHOO.namespace("moodle.container.effects");
YAHOO.namespace("moodle.calendar")
YAHOO.moodle.container.calendarpanels = [];

/**
 * This function adds the YUI animation lib at run time should be for some reason
 * not already have it.
 * This is a hack basically until MDL-19935 and should be removed once this issue
 * has been resolved.
 */
YAHOO.util.Event.onDOMReady(function () {
    //TODO: remove this hack MDL-20204
    var animationlib = document.createElement('script');
    animationlib.setAttribute('type','text/javascript');
    animationlib.setAttribute('src',moodle_cfg.wwwroot+'/lib/yui/2.8.0r4/animation/animation-min.js');
    document.getElementsByTagName('head')[0].appendChild(animationlib);
});

/**
 * This function simply attaches a listener to the date block we want to attach
 * the panel to. See the note on {@see YAHOO.moodle.calendar.show_calendar_panel} as
 * to why we don't build the panel here.
 */
YAHOO.moodle.calendar.attach_calendar_panel = function(properties) {
    YAHOO.util.Event.addListener(properties.id, 'mouseover', YAHOO.moodle.calendar.show_calendar_panel, properties);
}

/**
 * This method prepares the panel for display the first time. It is important to
 * note that we need to do this on a show as otherwise a bug in the way in which
 * YUI constrain to viewport works could cause the browser to force horizonal scroll!
 *
 * @param {event} e The event
 * @param {object} properties As passed in to the call to attach_calendar_event
 */
YAHOO.moodle.calendar.show_calendar_panel = function(e, properties) {
    YAHOO.util.Event.removeListener(properties.id, 'mouseover', YAHOO.moodle.calendar.show_calendar_panel);
     var panel = new YAHOO.widget.Panel(properties.id+'_panel', {
        width:"240px",
        visible:false,
        draggable:false,
        close:false,
        constraintoviewport:true,
        effect:{effect:YAHOO.moodle.container.effects.DELAY,duration:0.5}
    } );
    panel.setHeader(properties.title);
    panel.setBody(properties.content);
    panel.render(properties.id);
    panel.show();
    panel.data = [];
    panel.data['x'] = null;
    panel.data['y'] = null;
    YAHOO.moodle.container[properties.id] = panel;
    YAHOO.util.Event.addListener(properties.id, 'mouseover', YAHOO.moodle.calendar.show_panel, properties);
}

/**
 * This function calls the show method for the calendar panel
 * @param {event} e The event
 * @param {object} properties As passed in to the call to attach_calendar_event
 */
YAHOO.moodle.calendar.show_panel = function(e, properties) {
    YAHOO.util.Event.addListener(document.body, 'mousemove', YAHOO.moodle.calendar.hide_panel, properties);
    YAHOO.util.Event.removeListener(properties.id, 'mouseover', YAHOO.moodle.calendar.show_panel);
    YAHOO.moodle.container[properties.id].show(e, YAHOO.moodle.container[properties.id]);
}

/**
 * This function calls the hide method for the panel if it is appropriate to do so.
 * It is important to note that we first check the mouse position of the event to
 * ensure that the user is not hovering over either the date target or the associated
 * panel.
 * Also worth noting is that because the function is called on mouse move we do some
 * smarts to ensure that the function doesn't execute to often.
 * @param {event} e The event
 * @param {object} properties As passed in to the call to attach_calendar_event
 */
YAHOO.moodle.calendar.hide_panel = function(e, properties) {
    YAHOO.util.Event.removeListener(document.body, 'mousemove', YAHOO.moodle.calendar.hide_panel);
    var callback = function() {
        if (YAHOO.moodle.calendar.check_if_hiding(e, properties)) {
            YAHOO.moodle.container[properties.id].hide(e, YAHOO.moodle.container[properties.id]);
            YAHOO.util.Event.addListener(properties.id, 'mouseover', YAHOO.moodle.calendar.show_panel, properties);
        } else {
            YAHOO.util.Event.addListener(document.body, 'mousemove', YAHOO.moodle.calendar.hide_panel, properties);
        }
    }
    setTimeout(callback, 100);
}

/**
 * This function actually checks the mouse position against the bounds of the panel
 * and target elements.
 * @param {event} e The event
 * @param {object} properties As passed in to the call to attach_calendar_event
 */
YAHOO.moodle.calendar.check_if_hiding = function(e, properties) {
    if (!YAHOO.moodle.check_bounds(e, YAHOO.moodle.container[properties.id].element, 2) && !YAHOO.moodle.check_bounds(e, document.getElementById(properties.id), 2)) {
        return true;
    } else {
        return false;
    }
}

/**
 * This awesome little function takes an event and an element and works out wether
 * the mouse was over the element when the event was triggered.
 * You can also supply a fuzz integer which allows you to determine a `safe` distance
 * from the object before returning true.
 */
YAHOO.moodle.check_bounds = function(e, element, fuzz) {
    if (fuzz === null) fuzz = 0;
    var m = YAHOO.util.Event.getXY(e);
    var p = YAHOO.util.Dom.getXY(element);
    var o = [element.offsetWidth + p[0],element.offsetHeight + p[1]];
    if ( m[0]>=(p[0]-fuzz) && m[1]>=(p[1]-fuzz) && m[0]<=(o[0]+fuzz) && m[1]<=(o[1]+fuzz) ) {
        return true;
    }
    return false;
}

/**
 * Thats right a delay effect !
 *
 * This is essentially a remodelling of the YAHOO.widget.ContainerEffect.FADE
 * with modifications to support delay rather than fade.
 *
 * You will notice a couple of yui-effect-fade css classes. This class simply adds
 * display:none for ie and given this is based of the FADE effect I figured it was
 * ok to reuse classes.
 */
YAHOO.moodle.container.effects.DELAY = function(overlay, duration) {
    var Easing = YAHOO.util.Easing;
    var fin = {
        attributes: null,
        duration: duration,
        method: Easing.easeIn
    };
    var fout = {
        attributes: null,
        duration: duration,
        method: Easing.easeOut
    };
    var delay = new YAHOO.widget.ContainerEffect(overlay, fin, fout, overlay.element);

    delay.handleUnderlayStart = function() {
        var underlay = this.overlay.underlay;
        if (underlay && YAHOO.env.ua.ie) {
            var hasFilters = (underlay.filters && underlay.filters.length > 0);
            if(hasFilters) {
                YAHOO.util.Dom.addClass(overlay.element, "yui-effect-fade");
            }
        }
    };

    delay.handleUnderlayComplete = function() {
        var underlay = this.overlay.underlay;
        if (underlay && YAHOO.env.ua.ie) {
            YAHOO.util.Dom.removeClass(overlay.element, "yui-effect-fade");
        }
    };

    delay.handleStartAnimateIn = function (type, args, obj) {
        YAHOO.util.Dom.addClass(obj.overlay.element, "hide-select");

        if (!obj.overlay.underlay) {
            obj.overlay.cfg.refireEvent("underlay");
        }

        obj.handleUnderlayStart();
        obj.overlay._setDomVisibility(true);
        YAHOO.util.Dom.setStyle(obj.overlay.element, "opacity", 0);
    };

    delay.handleCompleteAnimateIn = function (type,args,obj) {
        YAHOO.util.Dom.removeClass(obj.overlay.element, "hide-select");
        if (obj.overlay.element.style.filter) {
            obj.overlay.element.style.filter = null;
        }
        var visible = function(){
            YAHOO.util.Dom.setStyle(obj.overlay.element, "opacity", 1);
        }
        setTimeout(visible, this.duration*1000);
        obj.handleUnderlayComplete();
        obj.overlay.cfg.refireEvent("iframe");
        obj.animateInCompleteEvent.fire();
    };

    delay.handleStartAnimateOut = function (type, args, obj) {
        YAHOO.util.Dom.addClass(obj.overlay.element, "hide-select");
        obj.handleUnderlayStart();
    };

    delay.handleCompleteAnimateOut =  function (type, args, obj) {
        YAHOO.util.Dom.removeClass(obj.overlay.element, "hide-select");
        if (obj.overlay.element.style.filter) {
            obj.overlay.element.style.filter = null;
        }
        obj.overlay._setDomVisibility(false);
        obj.handleUnderlayComplete();
        obj.overlay.cfg.refireEvent("iframe");
        obj.animateOutCompleteEvent.fire();
    };

    delay.init();
    return delay;
}