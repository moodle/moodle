YUI.add('moodle-calendar-eventmanager', function(Y) {

    var ENAME = 'Calendar event',
        EVENTID = 'eventId',
        EVENTNODE = 'node',
        EVENTTITLE = 'title',
        EVENTCONTENT = 'content',
        EVENTDELAY = 'delay',
        SHOWTIMEOUT = 'showTimeout',
        HIDETIMEOUT = 'hideTimeout';

    var EVENT = function(config) {
        EVENT.superclass.constructor.apply(this, arguments);
    }
    Y.extend(EVENT, Y.Base, {
        initpanelcalled : false,
        initializer : function(config){
            var id = this.get(EVENTID), node = this.get(EVENTNODE);
            if (!node) {
                return false;
            }
            var td = node.ancestor('td');
            this.publish('showevent');
            this.publish('hideevent');
            td.on('mouseenter', this.startShow, this);
            td.on('mouseleave', this.startHide, this);
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
                panel.get('boundingBox').addClass('calendar-event-panel');
                this.on('showevent', panel.show, panel);
                this.on('hideevent', panel.hide, panel);
            }
        },
        startShow : function() {
            if (this.get(SHOWTIMEOUT) !== null) {
                this.cancelShow();
            }
            var self = this;
            this.set(SHOWTIMEOUT, setTimeout(function(){self.show();}, this.get(EVENTDELAY)));
        },
        cancelShow : function() {
            clearTimeout(this.get(SHOWTIMEOUT));
        },
        show : function() {
            this.initPanel();
            this.fire('showevent');
        },
        startHide : function() {
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
                    var n = Y.one(node);
                    if (!n) {
                        Y.fail(ENAME+': invalid event node set');
                    }
                    return n;
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

    var EVENTMANAGER = {
        add_event : function(config) {
            new EVENT(config);
        },
        init_basic_export : function(allowthisweek, allownextweek, allownextmonth, username, authtoken) {
            var params = {
                preset_what : (Y.one('#pw_course').get('checked'))?'courses':'all',
                preset_time : 'recentupcoming',
                username : username,
                authtoken : authtoken

            }
            if (allowthisweek && Y.one('#pt_wknow').get('checked')) {
                params.presettime = 'weeknow';
            } else if (allownextweek && Y.one('#pt_wknext').get('checked')) {
                params.presettime = 'weeknext';
            } else if (allownextmonth && Y.one('#pt_monnext').get('checked')) {
                params.presettime = 'monthnext';
            } else if (Y.one('#pt_monnow').get('checked')) {
                params.presettime = 'monthnow';
            }
            Y.one('#url').setContent(M.cfg.wwwroot+'/calendar/export_execute.php?'+build_querystring(params));
            Y.one('#urlbox').setStyle('display', 'block');
        }
    }

    M.core_calendar = M.core_calendar || {}
    Y.mix(M.core_calendar, EVENTMANAGER);

}, '@VERSION@', {requires:['base', 'node', 'event-mouseenter', 'overlay', 'moodle-calendar-eventmanager-skin', 'test']});