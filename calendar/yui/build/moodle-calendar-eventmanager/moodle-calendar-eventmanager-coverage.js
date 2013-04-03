if (typeof _yuitest_coverage == "undefined"){
    _yuitest_coverage = {};
    _yuitest_coverline = function(src, line){
        var coverage = _yuitest_coverage[src];
        if (!coverage.lines[line]){
            coverage.calledLines++;
        }
        coverage.lines[line]++;
    };
    _yuitest_coverfunc = function(src, name, line){
        var coverage = _yuitest_coverage[src],
            funcId = name + ":" + line;
        if (!coverage.functions[funcId]){
            coverage.calledFunctions++;
        }
        coverage.functions[funcId]++;
    };
}
_yuitest_coverage["build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js"] = {
    lines: {},
    functions: {},
    coveredLines: 0,
    calledLines: 0,
    coveredFunctions: 0,
    calledFunctions: 0,
    path: "build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js",
    code: []
};
_yuitest_coverage["build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js"].code=["YUI.add('moodle-calendar-eventmanager', function (Y, NAME) {","","var ENAME = 'Calendar event',","    EVENTID = 'eventId',","    EVENTNODE = 'node',","    EVENTTITLE = 'title',","    EVENTCONTENT = 'content',","    EVENTDELAY = 'delay',","    SHOWTIMEOUT = 'showTimeout',","    HIDETIMEOUT = 'hideTimeout',","","    EVENT = function() {","        EVENT.superclass.constructor.apply(this, arguments);","    },","    EVENTMANAGER;","","Y.extend(EVENT, Y.Base, {","    initpanelcalled : false,","    initializer : function(){","        this.get(EVENTID);","        var node = this.get(EVENTNODE),","            td;","        if (!node) {","            return false;","        }","        td = node.ancestor('td');","        this.publish('showevent');","        this.publish('hideevent');","        td.on('mouseenter', this.startShow, this);","        td.on('mouseleave', this.startHide, this);","        td.on('focus', this.startShow, this);","        td.on('blur', this.startHide, this);","        return true;","    },","    initPanel : function() {","        if (!this.initpanelcalled) {","            this.initpanelcalled = true;","            var node = this.get(EVENTNODE),","                td = node.ancestor('td'),","                constraint = td.ancestor('div'),","                panel;","            panel = new Y.Overlay({","                constrain : constraint,","                align : {","                    node : td,","                    points:[Y.WidgetPositionAlign.TL, Y.WidgetPositionAlign.BC]","                },","                headerContent : Y.Node.create('<h2 class=\"eventtitle\">'+this.get(EVENTTITLE)+'</h2>'),","                bodyContent : Y.Node.create('<div class=\"eventcontent\">'+this.get(EVENTCONTENT)+'</div>'),","                visible : false,","                id : this.get(EVENTID)+'_panel',","                width : Math.floor(constraint.get('offsetWidth')*0.9)+\"px\"","            });","            panel.render(td);","            panel.get('boundingBox').addClass('calendar-event-panel');","            panel.get('boundingBox').setAttribute('aria-live', 'off');","            this.on('showevent', panel.show, panel);","            this.on('showevent', this.setAriashow, panel);","            this.on('hideevent', this.setAriahide, panel);","            this.on('hideevent', panel.hide, panel);","        }","    },","    startShow : function() {","        if (this.get(SHOWTIMEOUT) !== null) {","            this.cancelShow();","        }","        var self = this;","        this.set(SHOWTIMEOUT, setTimeout(function(){self.show();}, this.get(EVENTDELAY)));","    },","    cancelShow : function() {","        clearTimeout(this.get(SHOWTIMEOUT));","    },","    setAriashow : function() {","        this.get('boundingBox').setAttribute('aria-live', 'assertive');","    },","    setAriahide : function() {","          this.get('boundingBox').setAttribute('aria-live', 'off');","    },","    show : function() {","        this.initPanel();","        this.fire('showevent');","    },","    startHide : function() {","        if (this.get(HIDETIMEOUT) !== null) {","            this.cancelHide();","        }","        var self = this;","        this.set(HIDETIMEOUT, setTimeout(function(){self.hide();}, this.get(EVENTDELAY)));","    },","    hide : function() {","        this.fire('hideevent');","    },","    cancelHide : function() {","        clearTimeout(this.get(HIDETIMEOUT));","    }","}, {","    NAME : ENAME,","    ATTRS : {","        eventId : {","            setter : function(nodeid) {","                this.set(EVENTNODE, Y.one('#'+nodeid));","                return nodeid;","            },","            validator : Y.Lang.isString","        },","        node : {","            setter : function(node) {","                if (typeof(node) === 'string') {","                    node = Y.one('#'+node);","                }","                return node;","            }","        },","        title : {","            validator : Y.Lang.isString","        },","        content : {","            validator : Y.Lang.isString","        },","        delay : {","            value : 300,","            validator : Y.Lang.isNumber","        },","        showTimeout : {","            value : null","        },","        hideTimeout : {","            value : null","        }","    }","});","Y.augment(EVENT, Y.EventTarget);","","EVENTMANAGER = {","    add_event : function(config) {","        new EVENT(config);","    }","};","","M.core_calendar = M.core_calendar || {};","Y.mix(M.core_calendar, EVENTMANAGER);","","","}, '@VERSION@', {\"requires\": [\"base\", \"node\", \"event-mouseenter\", \"overlay\", \"moodle-calendar-eventmanager-skin\"]});"];
_yuitest_coverage["build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js"].lines = {"1":0,"3":0,"13":0,"17":0,"20":0,"21":0,"23":0,"24":0,"26":0,"27":0,"28":0,"29":0,"30":0,"31":0,"32":0,"33":0,"36":0,"37":0,"38":0,"42":0,"54":0,"55":0,"56":0,"57":0,"58":0,"59":0,"60":0,"64":0,"65":0,"67":0,"68":0,"71":0,"74":0,"77":0,"80":0,"81":0,"84":0,"85":0,"87":0,"88":0,"91":0,"94":0,"101":0,"102":0,"108":0,"109":0,"111":0,"132":0,"134":0,"136":0,"140":0,"141":0};
_yuitest_coverage["build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js"].functions = {"EVENT:12":0,"initializer:19":0,"initPanel:35":0,"(anonymous 2):68":0,"startShow:63":0,"cancelShow:70":0,"setAriashow:73":0,"setAriahide:76":0,"show:79":0,"(anonymous 3):88":0,"startHide:83":0,"hide:90":0,"cancelHide:93":0,"setter:100":0,"setter:107":0,"add_event:135":0,"(anonymous 1):1":0};
_yuitest_coverage["build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js"].coveredLines = 52;
_yuitest_coverage["build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js"].coveredFunctions = 17;
_yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 1);
YUI.add('moodle-calendar-eventmanager', function (Y, NAME) {

_yuitest_coverfunc("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", "(anonymous 1)", 1);
_yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 3);
var ENAME = 'Calendar event',
    EVENTID = 'eventId',
    EVENTNODE = 'node',
    EVENTTITLE = 'title',
    EVENTCONTENT = 'content',
    EVENTDELAY = 'delay',
    SHOWTIMEOUT = 'showTimeout',
    HIDETIMEOUT = 'hideTimeout',

    EVENT = function() {
        _yuitest_coverfunc("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", "EVENT", 12);
_yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 13);
EVENT.superclass.constructor.apply(this, arguments);
    },
    EVENTMANAGER;

_yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 17);
Y.extend(EVENT, Y.Base, {
    initpanelcalled : false,
    initializer : function(){
        _yuitest_coverfunc("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", "initializer", 19);
_yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 20);
this.get(EVENTID);
        _yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 21);
var node = this.get(EVENTNODE),
            td;
        _yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 23);
if (!node) {
            _yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 24);
return false;
        }
        _yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 26);
td = node.ancestor('td');
        _yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 27);
this.publish('showevent');
        _yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 28);
this.publish('hideevent');
        _yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 29);
td.on('mouseenter', this.startShow, this);
        _yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 30);
td.on('mouseleave', this.startHide, this);
        _yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 31);
td.on('focus', this.startShow, this);
        _yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 32);
td.on('blur', this.startHide, this);
        _yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 33);
return true;
    },
    initPanel : function() {
        _yuitest_coverfunc("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", "initPanel", 35);
_yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 36);
if (!this.initpanelcalled) {
            _yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 37);
this.initpanelcalled = true;
            _yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 38);
var node = this.get(EVENTNODE),
                td = node.ancestor('td'),
                constraint = td.ancestor('div'),
                panel;
            _yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 42);
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
            _yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 54);
panel.render(td);
            _yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 55);
panel.get('boundingBox').addClass('calendar-event-panel');
            _yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 56);
panel.get('boundingBox').setAttribute('aria-live', 'off');
            _yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 57);
this.on('showevent', panel.show, panel);
            _yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 58);
this.on('showevent', this.setAriashow, panel);
            _yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 59);
this.on('hideevent', this.setAriahide, panel);
            _yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 60);
this.on('hideevent', panel.hide, panel);
        }
    },
    startShow : function() {
        _yuitest_coverfunc("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", "startShow", 63);
_yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 64);
if (this.get(SHOWTIMEOUT) !== null) {
            _yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 65);
this.cancelShow();
        }
        _yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 67);
var self = this;
        _yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 68);
this.set(SHOWTIMEOUT, setTimeout(function(){_yuitest_coverfunc("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", "(anonymous 2)", 68);
self.show();}, this.get(EVENTDELAY)));
    },
    cancelShow : function() {
        _yuitest_coverfunc("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", "cancelShow", 70);
_yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 71);
clearTimeout(this.get(SHOWTIMEOUT));
    },
    setAriashow : function() {
        _yuitest_coverfunc("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", "setAriashow", 73);
_yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 74);
this.get('boundingBox').setAttribute('aria-live', 'assertive');
    },
    setAriahide : function() {
          _yuitest_coverfunc("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", "setAriahide", 76);
_yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 77);
this.get('boundingBox').setAttribute('aria-live', 'off');
    },
    show : function() {
        _yuitest_coverfunc("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", "show", 79);
_yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 80);
this.initPanel();
        _yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 81);
this.fire('showevent');
    },
    startHide : function() {
        _yuitest_coverfunc("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", "startHide", 83);
_yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 84);
if (this.get(HIDETIMEOUT) !== null) {
            _yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 85);
this.cancelHide();
        }
        _yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 87);
var self = this;
        _yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 88);
this.set(HIDETIMEOUT, setTimeout(function(){_yuitest_coverfunc("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", "(anonymous 3)", 88);
self.hide();}, this.get(EVENTDELAY)));
    },
    hide : function() {
        _yuitest_coverfunc("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", "hide", 90);
_yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 91);
this.fire('hideevent');
    },
    cancelHide : function() {
        _yuitest_coverfunc("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", "cancelHide", 93);
_yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 94);
clearTimeout(this.get(HIDETIMEOUT));
    }
}, {
    NAME : ENAME,
    ATTRS : {
        eventId : {
            setter : function(nodeid) {
                _yuitest_coverfunc("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", "setter", 100);
_yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 101);
this.set(EVENTNODE, Y.one('#'+nodeid));
                _yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 102);
return nodeid;
            },
            validator : Y.Lang.isString
        },
        node : {
            setter : function(node) {
                _yuitest_coverfunc("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", "setter", 107);
_yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 108);
if (typeof(node) === 'string') {
                    _yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 109);
node = Y.one('#'+node);
                }
                _yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 111);
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
_yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 132);
Y.augment(EVENT, Y.EventTarget);

_yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 134);
EVENTMANAGER = {
    add_event : function(config) {
        _yuitest_coverfunc("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", "add_event", 135);
_yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 136);
new EVENT(config);
    }
};

_yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 140);
M.core_calendar = M.core_calendar || {};
_yuitest_coverline("build/moodle-calendar-eventmanager/moodle-calendar-eventmanager.js", 141);
Y.mix(M.core_calendar, EVENTMANAGER);


}, '@VERSION@', {"requires": ["base", "node", "event-mouseenter", "overlay", "moodle-calendar-eventmanager-skin"]});
