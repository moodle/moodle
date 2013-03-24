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
_yuitest_coverage["build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js"] = {
    lines: {},
    functions: {},
    coveredLines: 0,
    calledLines: 0,
    coveredFunctions: 0,
    calledFunctions: 0,
    path: "build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js",
    code: []
};
_yuitest_coverage["build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js"].code=["YUI.add('moodle-filter_glossary-autolinker', function (Y, NAME) {","","var AUTOLINKERNAME = 'Glossary filter autolinker',","    WIDTH = 'width',","    HEIGHT = 'height',","    MENUBAR = 'menubar',","    LOCATION = 'location',","    SCROLLBARS = 'scrollbars',","    RESIZEABLE = 'resizable',","    TOOLBAR = 'toolbar',","    STATUS = 'status',","    DIRECTORIES = 'directories',","    FULLSCREEN = 'fullscreen',","    DEPENDENT = 'dependent',","    AUTOLINKER;","","AUTOLINKER = function() {","    AUTOLINKER.superclass.constructor.apply(this, arguments);","};","Y.extend(AUTOLINKER, Y.Base, {","    overlay : null,","    initializer : function() {","        var self = this;","        Y.delegate('click', function(e){","            e.preventDefault();","","            //display a progress indicator","            var title = '',","                content = Y.Node.create('<div id=\"glossaryfilteroverlayprogress\"><img src=\"'+M.cfg.loadingicon+'\" class=\"spinner\" /></div>'),","                o = new Y.Overlay({","                    headerContent :  title,","                    bodyContent : content","                }),","                fullurl,","                cfg;","            self.overlay = o;","            o.render(Y.one(document.body));","","            //Switch over to the ajax url and fetch the glossary item","            fullurl = this.getAttribute('href').replace('showentry.php','showentry_ajax.php');","            cfg = {","                method: 'get',","                context : self,","                on: {","                    success: function(id, o) {","                        this.display_callback(o.responseText);","                    },","                    failure: function(id, o) {","                        var debuginfo = o.statusText;","                        if (M.cfg.developerdebug) {","                            o.statusText += ' (' + fullurl + ')';","                        }","                        this.display_callback('bodyContent',debuginfo);","                    }","                }","            };","            Y.io(fullurl, cfg);","","        }, Y.one(document.body), 'a.glossary.autolink.concept');","    },","    display_callback : function(content) {","        var data,","            key,","            alertpanel;","        try {","            data = Y.JSON.parse(content);","            if (data.success){","                this.overlay.hide(); //hide progress indicator","","                for (key in data.entries) {","                    definition = data.entries[key].definition + data.entries[key].attachments;","                    alertpanel = new M.core.alert({title:data.entries[key].concept, message:definition, lightbox:false});","                    Y.Node.one('#id_yuialertconfirm-' + alertpanel.COUNT).focus();","                }","","                return true;","            } else if (data.error) {","                new M.core.ajaxException(data);","            }","        } catch(e) {","            new M.core.exception(e);","        }","        return false;","    }","}, {","    NAME : AUTOLINKERNAME,","    ATTRS : {","        url : {","            validator : Y.Lang.isString,","            value : M.cfg.wwwroot+'/mod/glossary/showentry.php'","        },","        name : {","            validator : Y.Lang.isString,","            value : 'glossaryconcept'","        },","        options : {","            getter : function() {","                return {","                    width : this.get(WIDTH),","                    height : this.get(HEIGHT),","                    menubar : this.get(MENUBAR),","                    location : this.get(LOCATION),","                    scrollbars : this.get(SCROLLBARS),","                    resizable : this.get(RESIZEABLE),","                    toolbar : this.get(TOOLBAR),","                    status : this.get(STATUS),","                    directories : this.get(DIRECTORIES),","                    fullscreen : this.get(FULLSCREEN),","                    dependent : this.get(DEPENDENT)","                };","            },","            readOnly : true","        },","        width : {value : 600},","        height : {value : 450},","        menubar : {value : false},","        location : {value : false},","        scrollbars : {value : true},","        resizable : {value : true},","        toolbar : {value : true},","        status : {value : true},","        directories : {value : false},","        fullscreen : {value : false},","        dependent : {value : true}","    }","});","","M.filter_glossary = M.filter_glossary || {};","M.filter_glossary.init_filter_autolinking = function(config) {","    return new AUTOLINKER(config);","};","","","}, '@VERSION@', {","    \"requires\": [","        \"base\",","        \"node\",","        \"io-base\",","        \"json-parse\",","        \"event-delegate\",","        \"overlay\",","        \"moodle-core-notification\"","    ]","});"];
_yuitest_coverage["build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js"].lines = {"1":0,"3":0,"17":0,"18":0,"20":0,"23":0,"24":0,"25":0,"28":0,"36":0,"37":0,"40":0,"41":0,"46":0,"49":0,"50":0,"51":0,"53":0,"57":0,"62":0,"65":0,"66":0,"67":0,"68":0,"70":0,"71":0,"72":0,"73":0,"76":0,"77":0,"78":0,"81":0,"83":0,"98":0,"128":0,"129":0,"130":0};
_yuitest_coverage["build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js"].functions = {"AUTOLINKER:17":0,"success:45":0,"failure:48":0,"(anonymous 2):24":0,"initializer:22":0,"display_callback:61":0,"getter:97":0,"init_filter_autolinking:129":0,"(anonymous 1):1":0};
_yuitest_coverage["build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js"].coveredLines = 37;
_yuitest_coverage["build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js"].coveredFunctions = 9;
_yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 1);
YUI.add('moodle-filter_glossary-autolinker', function (Y, NAME) {

_yuitest_coverfunc("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", "(anonymous 1)", 1);
_yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 3);
var AUTOLINKERNAME = 'Glossary filter autolinker',
    WIDTH = 'width',
    HEIGHT = 'height',
    MENUBAR = 'menubar',
    LOCATION = 'location',
    SCROLLBARS = 'scrollbars',
    RESIZEABLE = 'resizable',
    TOOLBAR = 'toolbar',
    STATUS = 'status',
    DIRECTORIES = 'directories',
    FULLSCREEN = 'fullscreen',
    DEPENDENT = 'dependent',
    AUTOLINKER;

_yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 17);
AUTOLINKER = function() {
    _yuitest_coverfunc("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", "AUTOLINKER", 17);
_yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 18);
AUTOLINKER.superclass.constructor.apply(this, arguments);
};
_yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 20);
Y.extend(AUTOLINKER, Y.Base, {
    overlay : null,
    initializer : function() {
        _yuitest_coverfunc("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", "initializer", 22);
_yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 23);
var self = this;
        _yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 24);
Y.delegate('click', function(e){
            _yuitest_coverfunc("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", "(anonymous 2)", 24);
_yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 25);
e.preventDefault();

            //display a progress indicator
            _yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 28);
var title = '',
                content = Y.Node.create('<div id="glossaryfilteroverlayprogress"><img src="'+M.cfg.loadingicon+'" class="spinner" /></div>'),
                o = new Y.Overlay({
                    headerContent :  title,
                    bodyContent : content
                }),
                fullurl,
                cfg;
            _yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 36);
self.overlay = o;
            _yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 37);
o.render(Y.one(document.body));

            //Switch over to the ajax url and fetch the glossary item
            _yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 40);
fullurl = this.getAttribute('href').replace('showentry.php','showentry_ajax.php');
            _yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 41);
cfg = {
                method: 'get',
                context : self,
                on: {
                    success: function(id, o) {
                        _yuitest_coverfunc("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", "success", 45);
_yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 46);
this.display_callback(o.responseText);
                    },
                    failure: function(id, o) {
                        _yuitest_coverfunc("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", "failure", 48);
_yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 49);
var debuginfo = o.statusText;
                        _yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 50);
if (M.cfg.developerdebug) {
                            _yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 51);
o.statusText += ' (' + fullurl + ')';
                        }
                        _yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 53);
this.display_callback('bodyContent',debuginfo);
                    }
                }
            };
            _yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 57);
Y.io(fullurl, cfg);

        }, Y.one(document.body), 'a.glossary.autolink.concept');
    },
    display_callback : function(content) {
        _yuitest_coverfunc("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", "display_callback", 61);
_yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 62);
var data,
            key,
            alertpanel;
        _yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 65);
try {
            _yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 66);
data = Y.JSON.parse(content);
            _yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 67);
if (data.success){
                _yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 68);
this.overlay.hide(); //hide progress indicator

                _yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 70);
for (key in data.entries) {
                    _yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 71);
definition = data.entries[key].definition + data.entries[key].attachments;
                    _yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 72);
alertpanel = new M.core.alert({title:data.entries[key].concept, message:definition, lightbox:false});
                    _yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 73);
Y.Node.one('#id_yuialertconfirm-' + alertpanel.COUNT).focus();
                }

                _yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 76);
return true;
            } else {_yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 77);
if (data.error) {
                _yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 78);
new M.core.ajaxException(data);
            }}
        } catch(e) {
            _yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 81);
new M.core.exception(e);
        }
        _yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 83);
return false;
    }
}, {
    NAME : AUTOLINKERNAME,
    ATTRS : {
        url : {
            validator : Y.Lang.isString,
            value : M.cfg.wwwroot+'/mod/glossary/showentry.php'
        },
        name : {
            validator : Y.Lang.isString,
            value : 'glossaryconcept'
        },
        options : {
            getter : function() {
                _yuitest_coverfunc("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", "getter", 97);
_yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 98);
return {
                    width : this.get(WIDTH),
                    height : this.get(HEIGHT),
                    menubar : this.get(MENUBAR),
                    location : this.get(LOCATION),
                    scrollbars : this.get(SCROLLBARS),
                    resizable : this.get(RESIZEABLE),
                    toolbar : this.get(TOOLBAR),
                    status : this.get(STATUS),
                    directories : this.get(DIRECTORIES),
                    fullscreen : this.get(FULLSCREEN),
                    dependent : this.get(DEPENDENT)
                };
            },
            readOnly : true
        },
        width : {value : 600},
        height : {value : 450},
        menubar : {value : false},
        location : {value : false},
        scrollbars : {value : true},
        resizable : {value : true},
        toolbar : {value : true},
        status : {value : true},
        directories : {value : false},
        fullscreen : {value : false},
        dependent : {value : true}
    }
});

_yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 128);
M.filter_glossary = M.filter_glossary || {};
_yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 129);
M.filter_glossary.init_filter_autolinking = function(config) {
    _yuitest_coverfunc("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", "init_filter_autolinking", 129);
_yuitest_coverline("build/moodle-filter_glossary-autolinker/moodle-filter_glossary-autolinker.js", 130);
return new AUTOLINKER(config);
};


}, '@VERSION@', {
    "requires": [
        "base",
        "node",
        "io-base",
        "json-parse",
        "event-delegate",
        "overlay",
        "moodle-core-notification"
    ]
});
