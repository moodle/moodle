YUI.add('moodle-calendar-eventmanager', function (Y, NAME) {

var ENAME = 'Calendar event',
    EVENTID = 'eventId',
    EVENTNODE = 'node',
    EVENTTITLE = 'title',
    EVENTCONTENT = 'content',
    EVENTDELAY = 'delay',
    SHOWTIMEOUT = 'showTimeout',
    HIDETIMEOUT = 'hideTimeout',

    EVENT = function() {
        EVENT.superclass.constructor.apply(this, arguments);
    },
    EVENTMANAGER;

Y.extend(EVENT, Y.Base, {
    initpanelcalled : false,
    initializer : function(){
        this.get(EVENTID);
        var node = this.get(EVENTNODE),
            td;
        if (!node) {
            return false;
        }
        td = node.ancestor('td');
        this.publish('showevent');
        this.publish('hideevent');
        td.on('mouseenter', this.startShow, this);
        td.on('mouseleave', this.startHide, this);
        td.on('focus', this.startShow, this);
        td.on('blur', this.startHide, this);
        return true;
    },
    initPanel : function() {
        if (!this.initpanelcalled) {
            this.initpanelcalled = true;
            var node = this.get(EVENTNODE),
                td = node.ancestor('td'),
                constraint = td.ancestor('div'),
                panel;
            panel = new Y.Overlay({
                constrain : constraint,
                align : {
                    node : td,
                    points:[Y.WidgetPositionAlign.TL, Y.WidgetPositionAlign.BC]
                },
                headerContent : Y.Node.create('<h2 class="eventtitle">'+this.get(EVENTTITLE)+'</h2>'),
                bodyContent : Y.Node.create('<div class="eventcontent">'+this.get(EVENTCONTENT)+'</div>'),
                visible : false,
                id : this.get(EVENTID)+'_panel',
                width : Math.floor(constraint.get('offsetWidth')*0.9)+"px"
            });
            panel.render(td);
            node.setAttribute('aria-controls', panel.get('id'));
            panel.get('boundingBox').addClass('calendar-event-panel');
            panel.get('boundingBox').setAttribute('aria-live', 'off');
            this.on('showevent', panel.show, panel);
            this.on('showevent', this.setAriashow, panel);
            this.on('hideevent', this.setAriahide, panel);
            this.on('hideevent', panel.hide, panel);
        }
    },
    startShow : function() {
        this.cancelHide();
        if (this.get(SHOWTIMEOUT) !== null) {
            this.cancelShow();
        }
        var self = this;
        this.set(SHOWTIMEOUT, setTimeout(function(){self.show();}, this.get(EVENTDELAY)));
    },
    cancelShow : function() {
        clearTimeout(this.get(SHOWTIMEOUT));
    },
    setAriashow : function() {
        this.get('boundingBox').setAttribute('aria-live', 'assertive');
    },
    setAriahide : function() {
          this.get('boundingBox').setAttribute('aria-live', 'off');
    },
    show : function() {
        this.initPanel();
        this.fire('showevent');
    },
    startHide : function() {
        this.cancelShow();
        if (this.get(HIDETIMEOUT) !== null) {
            this.cancelHide();
        }
        var self = this;
        this.set(HIDETIMEOUT, setTimeout(function(){self.hide();}, this.get(EVENTDELAY)));
    },
    hide : function() {
        this.fire('hideevent');
    },
    cancelHide : function() {
        clearTimeout(this.get(HIDETIMEOUT));
    }
}, {
    NAME : ENAME,
    ATTRS : {
        eventId : {
            setter : function(nodeid) {
                this.set(EVENTNODE, Y.one('#'+nodeid));
                return nodeid;
            },
            validator : Y.Lang.isString
        },
        node : {
            setter : function(node) {
                if (typeof(node) === 'string') {
                    node = Y.one('#'+node);
                }
                return node;
            }
        },
        title : {
            validator : Y.Lang.isString
        },
        content : {
            validator : Y.Lang.isString
        },
        delay : {
            value : 300,
            validator : Y.Lang.isNumber
        },
        showTimeout : {
            value : null
        },
        hideTimeout : {
            value : null
        }
    }
});
Y.augment(EVENT, Y.EventTarget);

EVENTMANAGER = {
    add_event : function(config) {
        new EVENT(config);
    }
};

M.core_calendar = M.core_calendar || {};
Y.mix(M.core_calendar, EVENTMANAGER);


}, '@VERSION@', {"requires": ["base", "node", "event-mouseenter", "overlay", "moodle-calendar-eventmanager-skin"]});
