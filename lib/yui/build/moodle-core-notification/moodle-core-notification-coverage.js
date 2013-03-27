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
_yuitest_coverage["build/moodle-core-notification/moodle-core-notification.js"] = {
    lines: {},
    functions: {},
    coveredLines: 0,
    calledLines: 0,
    coveredFunctions: 0,
    calledFunctions: 0,
    path: "build/moodle-core-notification/moodle-core-notification.js",
    code: []
};
_yuitest_coverage["build/moodle-core-notification/moodle-core-notification.js"].code=["YUI.add('moodle-core-notification', function (Y, NAME) {","","var DIALOGUE_NAME = 'Moodle dialogue',","    DIALOGUE_PREFIX = 'moodle-dialogue',","    CONFIRM_NAME = 'Moodle confirmation dialogue',","    EXCEPTION_NAME = 'Moodle exception',","    AJAXEXCEPTION_NAME = 'Moodle AJAX exception',","    ALERT_NAME = 'Moodle alert',","    BASE = 'notificationBase',","    COUNT = 0,","    CONFIRMYES = 'yesLabel',","    CONFIRMNO = 'noLabel',","    TITLE = 'title',","    QUESTION = 'question',","    CSS = {","        BASE : 'moodle-dialogue-base',","        WRAP : 'moodle-dialogue-wrap',","        HEADER : 'moodle-dialogue-hd',","        BODY : 'moodle-dialogue-bd',","        CONTENT : 'moodle-dialogue-content',","        FOOTER : 'moodle-dialogue-ft',","        HIDDEN : 'hidden',","        LIGHTBOX : 'moodle-dialogue-lightbox'","    },","    EXCEPTION,","    ALERT,","    CONFIRM,","    AJAXEXCEPTION,","    DIALOGUE;","","DIALOGUE = function(config) {","    COUNT++;","    var id = 'moodle-dialogue-'+COUNT;","    config.notificationBase =","        Y.Node.create('<div class=\"'+CSS.BASE+'\">')","              .append(Y.Node.create('<div id=\"'+id+'\" role=\"dialog\" aria-labelledby=\"'+id+'-header-text\" class=\"'+CSS.WRAP+'\"></div>')","              .append(Y.Node.create('<div class=\"'+CSS.HEADER+' yui3-widget-hd\"></div>'))","              .append(Y.Node.create('<div class=\"'+CSS.BODY+' yui3-widget-bd\"></div>'))","              .append(Y.Node.create('<div class=\"'+CSS.FOOTER+' yui3-widget-ft\"></div>')));","    Y.one(document.body).append(config.notificationBase);","    config.srcNode =    '#'+id;","    config.width =      config.width || '400px';","    config.visible =    config.visible || false;","    config.center =     config.centered || true;","    config.centered =   false;","","    // lightbox param to keep the stable versions API.","    if (config.lightbox !== false) {","        config.modal = true;","    }","    delete config.lightbox;","","    // closeButton param to keep the stable versions API.","    if (config.closeButton === false) {","        config.buttons = null;","    } else {","        config.buttons = [","            {","                section: Y.WidgetStdMod.HEADER,","                classNames: 'closebutton',","                action: function () {","                    this.hide();","                }","            }","        ];","    }","    DIALOGUE.superclass.constructor.apply(this, [config]);","","    if (config.closeButton !== false) {","        // The buttons constructor does not allow custom attributes","        this.get('buttons').header[0].setAttribute('title', this.get('closeButtonTitle'));","    }","};","Y.extend(DIALOGUE, Y.Panel, {","    initializer : function() {","        this.after('visibleChange', this.visibilityChanged, this);","        this.render();","        this.show();","","        // Workaround upstream YUI bug http://yuilibrary.com/projects/yui3/ticket/2532507","        // and allow setting of z-index in theme.","        this.get('boundingBox').setStyle('zIndex', null);","    },","    visibilityChanged : function(e) {","        var titlebar;","        if (e.attrName === 'visible') {","            this.get('maskNode').addClass(CSS.LIGHTBOX);","            if (this.get('center') && !e.prevVal && e.newVal) {","                this.centerDialogue();","            }","            if (this.get('draggable')) {","                titlebar = '#' + this.get('id') + ' .' + CSS.HEADER;","                this.plug(Y.Plugin.Drag, {handles : [titlebar]});","                Y.one(titlebar).setStyle('cursor', 'move');","            }","        }","    },","    centerDialogue : function() {","        var bb = this.get('boundingBox'),","            hidden = bb.hasClass(DIALOGUE_PREFIX+'-hidden'),","            x, y;","        if (hidden) {","            bb.setStyle('top', '-1000px').removeClass(DIALOGUE_PREFIX+'-hidden');","        }","        x = Math.max(Math.round((bb.get('winWidth') - bb.get('offsetWidth'))/2), 15);","        y = Math.max(Math.round((bb.get('winHeight') - bb.get('offsetHeight'))/2), 15) + Y.one(window).get('scrollTop');","","        if (hidden) {","            bb.addClass(DIALOGUE_PREFIX+'-hidden');","        }","        bb.setStyle('left', x).setStyle('top', y);","    }","}, {","    NAME : DIALOGUE_NAME,","    CSS_PREFIX : DIALOGUE_PREFIX,","    ATTRS : {","        notificationBase : {","","        },","        lightbox : {","            validator : Y.Lang.isBoolean,","            value : true","        },","        closeButton : {","            validator : Y.Lang.isBoolean,","            value : true","        },","        closeButtonTitle : {","            validator : Y.Lang.isString,","            value : 'Close'","        },","        center : {","            validator : Y.Lang.isBoolean,","            value : true","        },","        draggable : {","            validator : Y.Lang.isBoolean,","            value : false","        }","    }","});","","ALERT = function(config) {","    config.closeButton = false;","    ALERT.superclass.constructor.apply(this, [config]);","};","Y.extend(ALERT, DIALOGUE, {","    _enterKeypress : null,","    initializer : function() {","        this.publish('complete');","        var yes = Y.Node.create('<input type=\"button\" id=\"id_yuialertconfirm-' + this.COUNT + '\" value=\"'+this.get(CONFIRMYES)+'\" />'),","            content = Y.Node.create('<div class=\"confirmation-dialogue\"></div>')","                    .append(Y.Node.create('<div class=\"confirmation-message\">'+this.get('message')+'</div>'))","                    .append(Y.Node.create('<div class=\"confirmation-buttons\"></div>')","                            .append(yes));","        this.get(BASE).addClass('moodle-dialogue-confirm');","        this.setStdModContent(Y.WidgetStdMod.BODY, content, Y.WidgetStdMod.REPLACE);","        this.setStdModContent(Y.WidgetStdMod.HEADER, '<h1 id=\"moodle-dialogue-'+COUNT+'-header-text\">' + this.get(TITLE) + '</h1>', Y.WidgetStdMod.REPLACE);","        this.after('destroyedChange', function(){this.get(BASE).remove();}, this);","        this._enterKeypress = Y.on('key', this.submit, window, 'down:13', this);","        yes.on('click', this.submit, this);","    },","    submit : function() {","        this._enterKeypress.detach();","        this.fire('complete');","        this.hide();","        this.destroy();","    }","}, {","    NAME : ALERT_NAME,","    CSS_PREFIX : DIALOGUE_PREFIX,","    ATTRS : {","        title : {","            validator : Y.Lang.isString,","            value : 'Alert'","        },","        message : {","            validator : Y.Lang.isString,","            value : 'Confirm'","        },","        yesLabel : {","            validator : Y.Lang.isString,","            setter : function(txt) {","                if (!txt) {","                    txt = 'Ok';","                }","                return txt;","            },","            value : 'Ok'","        }","    }","});","","CONFIRM = function(config) {","    CONFIRM.superclass.constructor.apply(this, [config]);","};","Y.extend(CONFIRM, DIALOGUE, {","    _enterKeypress : null,","    _escKeypress : null,","    initializer : function() {","        this.publish('complete');","        this.publish('complete-yes');","        this.publish('complete-no');","        var yes = Y.Node.create('<input type=\"button\" id=\"id_yuiconfirmyes-' + this.COUNT + '\" value=\"'+this.get(CONFIRMYES)+'\" />'),","            no = Y.Node.create('<input type=\"button\" id=\"id_yuiconfirmno-' + this.COUNT + '\" value=\"'+this.get(CONFIRMNO)+'\" />'),","            content = Y.Node.create('<div class=\"confirmation-dialogue\"></div>')","                        .append(Y.Node.create('<div class=\"confirmation-message\">'+this.get(QUESTION)+'</div>'))","                        .append(Y.Node.create('<div class=\"confirmation-buttons\"></div>')","                            .append(yes)","                            .append(no));","        this.get(BASE).addClass('moodle-dialogue-confirm');","        this.setStdModContent(Y.WidgetStdMod.BODY, content, Y.WidgetStdMod.REPLACE);","        this.setStdModContent(Y.WidgetStdMod.HEADER, '<h1 id=\"moodle-dialogue-'+COUNT+'-header-text\">' + this.get(TITLE) + '</h1>', Y.WidgetStdMod.REPLACE);","        this.after('destroyedChange', function(){this.get(BASE).remove();}, this);","        this._enterKeypress = Y.on('key', this.submit, window, 'down:13', this, true);","        this._escKeypress = Y.on('key', this.submit, window, 'down:27', this, false);","        yes.on('click', this.submit, this, true);","        no.on('click', this.submit, this, false);","    },","    submit : function(e, outcome) {","        this._enterKeypress.detach();","        this._escKeypress.detach();","        this.fire('complete', outcome);","        if (outcome) {","            this.fire('complete-yes');","        } else {","            this.fire('complete-no');","        }","        this.hide();","        this.destroy();","    }","}, {","    NAME : CONFIRM_NAME,","    CSS_PREFIX : DIALOGUE_PREFIX,","    ATTRS : {","        yesLabel : {","            validator : Y.Lang.isString,","            value : 'Yes'","        },","        noLabel : {","            validator : Y.Lang.isString,","            value : 'No'","        },","        title : {","            validator : Y.Lang.isString,","            value : 'Confirm'","        },","        question : {","            validator : Y.Lang.isString,","            value : 'Are you sure?'","        }","    }","});","Y.augment(CONFIRM, Y.EventTarget);","","EXCEPTION = function(config) {","    config.width = config.width || (M.cfg.developerdebug)?Math.floor(Y.one(document.body).get('winWidth')/3)+'px':null;","    config.closeButton = true;","    EXCEPTION.superclass.constructor.apply(this, [config]);","};","Y.extend(EXCEPTION, DIALOGUE, {","    _hideTimeout : null,","    _keypress : null,","    initializer : function(config) {","        var content,","            self = this,","            delay = this.get('hideTimeoutDelay');","        this.get(BASE).addClass('moodle-dialogue-exception');","        this.setStdModContent(Y.WidgetStdMod.HEADER, '<h1 id=\"moodle-dialogue-'+COUNT+'-header-text\">' + config.name + '</h1>', Y.WidgetStdMod.REPLACE);","        content = Y.Node.create('<div class=\"moodle-exception\"></div>')","                .append(Y.Node.create('<div class=\"moodle-exception-message\">'+this.get('message')+'</div>'))","                .append(Y.Node.create('<div class=\"moodle-exception-param hidden param-filename\"><label>File:</label> '+this.get('fileName')+'</div>'))","                .append(Y.Node.create('<div class=\"moodle-exception-param hidden param-linenumber\"><label>Line:</label> '+this.get('lineNumber')+'</div>'))","                .append(Y.Node.create('<div class=\"moodle-exception-param hidden param-stacktrace\"><label>Stack trace:</label> <pre>'+this.get('stack')+'</pre></div>'));","        if (M.cfg.developerdebug) {","            content.all('.moodle-exception-param').removeClass('hidden');","        }","        this.setStdModContent(Y.WidgetStdMod.BODY, content, Y.WidgetStdMod.REPLACE);","","        if (delay) {","            this._hideTimeout = setTimeout(function(){self.hide();}, delay);","        }","        this.after('visibleChange', this.visibilityChanged, this);","        this.after('destroyedChange', function(){this.get(BASE).remove();}, this);","        this._keypress = Y.on('key', this.hide, window, 'down:13,27', this);","        this.centerDialogue();","    },","    visibilityChanged : function(e) {","        if (e.attrName === 'visible' && e.prevVal && !e.newVal) {","            if (this._keypress) {","                this._keypress.detach();","            }","            var self = this;","            setTimeout(function(){self.destroy();}, 1000);","        }","    }","}, {","    NAME : EXCEPTION_NAME,","    CSS_PREFIX : DIALOGUE_PREFIX,","    ATTRS : {","        message : {","            value : ''","        },","        name : {","            value : ''","        },","        fileName : {","            value : ''","        },","        lineNumber : {","            value : ''","        },","        stack : {","            setter : function(str) {","                var lines = str.split(\"\\n\"),","                    pattern = new RegExp('^(.+)@('+M.cfg.wwwroot+')?(.{0,75}).*:(\\\\d+)$'),","                    i;","                for (i in lines) {","                    lines[i] = lines[i].replace(pattern,","                            \"<div class='stacktrace-line'>ln: $4</div><div class='stacktrace-file'>$3</div><div class='stacktrace-call'>$1</div>\");","                }","                return lines.join('');","            },","            value : ''","        },","        hideTimeoutDelay : {","            validator : Y.Lang.isNumber,","            value : null","        }","    }","});","","AJAXEXCEPTION = function(config) {","    config.name = config.name || 'Error';","    config.closeButton = true;","    AJAXEXCEPTION.superclass.constructor.apply(this, [config]);","};","Y.extend(AJAXEXCEPTION, DIALOGUE, {","    _keypress : null,","    initializer : function(config) {","        var content,","            self = this,","            delay = this.get('hideTimeoutDelay');","        this.get(BASE).addClass('moodle-dialogue-exception');","        this.setStdModContent(Y.WidgetStdMod.HEADER, '<h1 id=\"moodle-dialogue-'+COUNT+'-header-text\">' + config.name + '</h1>', Y.WidgetStdMod.REPLACE);","        content = Y.Node.create('<div class=\"moodle-ajaxexception\"></div>')","                .append(Y.Node.create('<div class=\"moodle-exception-message\">'+this.get('error')+'</div>'))","                .append(Y.Node.create('<div class=\"moodle-exception-param hidden param-debuginfo\"><label>URL:</label> '+this.get('reproductionlink')+'</div>'))","                .append(Y.Node.create('<div class=\"moodle-exception-param hidden param-debuginfo\"><label>Debug info:</label> '+this.get('debuginfo')+'</div>'))","                .append(Y.Node.create('<div class=\"moodle-exception-param hidden param-stacktrace\"><label>Stack trace:</label> <pre>'+this.get('stacktrace')+'</pre></div>'));","        if (M.cfg.developerdebug) {","            content.all('.moodle-exception-param').removeClass('hidden');","        }","        this.setStdModContent(Y.WidgetStdMod.BODY, content, Y.WidgetStdMod.REPLACE);","","        if (delay) {","            this._hideTimeout = setTimeout(function(){self.hide();}, delay);","        }","        this.after('visibleChange', this.visibilityChanged, this);","        this._keypress = Y.on('key', this.hide, window, 'down:13, 27', this);","        this.centerDialogue();","    },","    visibilityChanged : function(e) {","        if (e.attrName === 'visible' && e.prevVal && !e.newVal) {","            var self = this;","            this._keypress.detach();","            setTimeout(function(){self.destroy();}, 1000);","        }","    }","}, {","    NAME : AJAXEXCEPTION_NAME,","    CSS_PREFIX : DIALOGUE_PREFIX,","    ATTRS : {","        error : {","            validator : Y.Lang.isString,","            value : 'Unknown error'","        },","        debuginfo : {","            value : null","        },","        stacktrace : {","            value : null","        },","        reproductionlink : {","            setter : function(link) {","                if (link !== null) {","                    link = '<a href=\"'+link+'\">'+link.replace(M.cfg.wwwroot, '')+'</a>';","                }","                return link;","            },","            value : null","        },","        hideTimeoutDelay : {","            validator : Y.Lang.isNumber,","            value : null","        }","    }","});","","M.core = M.core || {};","M.core.dialogue = DIALOGUE;","M.core.alert = ALERT;","M.core.confirm = CONFIRM;","M.core.exception = EXCEPTION;","M.core.ajaxException = AJAXEXCEPTION;","","","}, '@VERSION@', {\"requires\": [\"base\", \"node\", \"panel\", \"event-key\", \"dd-plugin\"]});"];
_yuitest_coverage["build/moodle-core-notification/moodle-core-notification.js"].lines = {"1":0,"3":0,"31":0,"32":0,"33":0,"34":0,"40":0,"41":0,"42":0,"43":0,"44":0,"45":0,"48":0,"49":0,"51":0,"54":0,"55":0,"57":0,"62":0,"67":0,"69":0,"71":0,"74":0,"76":0,"77":0,"78":0,"82":0,"85":0,"86":0,"87":0,"88":0,"89":0,"91":0,"92":0,"93":0,"94":0,"99":0,"102":0,"103":0,"105":0,"106":0,"108":0,"109":0,"111":0,"143":0,"144":0,"145":0,"147":0,"150":0,"151":0,"156":0,"157":0,"158":0,"159":0,"160":0,"161":0,"164":0,"165":0,"166":0,"167":0,"184":0,"185":0,"187":0,"194":0,"195":0,"197":0,"201":0,"202":0,"203":0,"204":0,"211":0,"212":0,"213":0,"214":0,"215":0,"216":0,"217":0,"218":0,"221":0,"222":0,"223":0,"224":0,"225":0,"227":0,"229":0,"230":0,"254":0,"256":0,"257":0,"258":0,"259":0,"261":0,"265":0,"268":0,"269":0,"270":0,"275":0,"276":0,"278":0,"280":0,"281":0,"283":0,"284":0,"285":0,"286":0,"289":0,"290":0,"291":0,"293":0,"294":0,"315":0,"318":0,"319":0,"322":0,"333":0,"334":0,"335":0,"336":0,"338":0,"341":0,"344":0,"345":0,"346":0,"351":0,"352":0,"354":0,"356":0,"357":0,"359":0,"360":0,"361":0,"364":0,"365":0,"366":0,"367":0,"386":0,"387":0,"389":0,"400":0,"401":0,"402":0,"403":0,"404":0,"405":0};
_yuitest_coverage["build/moodle-core-notification/moodle-core-notification.js"].functions = {"action:61":0,"DIALOGUE:31":0,"initializer:75":0,"visibilityChanged:84":0,"centerDialogue:98":0,"ALERT:143":0,"(anonymous 2):159":0,"initializer:149":0,"submit:163":0,"setter:183":0,"CONFIRM:194":0,"(anonymous 3):214":0,"initializer:200":0,"submit:220":0,"EXCEPTION:256":0,"(anonymous 4):281":0,"(anonymous 5):284":0,"initializer:264":0,"(anonymous 6):294":0,"visibilityChanged:288":0,"setter:314":0,"AJAXEXCEPTION:333":0,"(anonymous 7):357":0,"initializer:340":0,"(anonymous 8):367":0,"visibilityChanged:363":0,"setter:385":0,"(anonymous 1):1":0};
_yuitest_coverage["build/moodle-core-notification/moodle-core-notification.js"].coveredLines = 144;
_yuitest_coverage["build/moodle-core-notification/moodle-core-notification.js"].coveredFunctions = 28;
_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 1);
YUI.add('moodle-core-notification', function (Y, NAME) {

_yuitest_coverfunc("build/moodle-core-notification/moodle-core-notification.js", "(anonymous 1)", 1);
_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 3);
var DIALOGUE_NAME = 'Moodle dialogue',
    DIALOGUE_PREFIX = 'moodle-dialogue',
    CONFIRM_NAME = 'Moodle confirmation dialogue',
    EXCEPTION_NAME = 'Moodle exception',
    AJAXEXCEPTION_NAME = 'Moodle AJAX exception',
    ALERT_NAME = 'Moodle alert',
    BASE = 'notificationBase',
    COUNT = 0,
    CONFIRMYES = 'yesLabel',
    CONFIRMNO = 'noLabel',
    TITLE = 'title',
    QUESTION = 'question',
    CSS = {
        BASE : 'moodle-dialogue-base',
        WRAP : 'moodle-dialogue-wrap',
        HEADER : 'moodle-dialogue-hd',
        BODY : 'moodle-dialogue-bd',
        CONTENT : 'moodle-dialogue-content',
        FOOTER : 'moodle-dialogue-ft',
        HIDDEN : 'hidden',
        LIGHTBOX : 'moodle-dialogue-lightbox'
    },
    EXCEPTION,
    ALERT,
    CONFIRM,
    AJAXEXCEPTION,
    DIALOGUE;

_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 31);
DIALOGUE = function(config) {
    _yuitest_coverfunc("build/moodle-core-notification/moodle-core-notification.js", "DIALOGUE", 31);
_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 32);
COUNT++;
    _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 33);
var id = 'moodle-dialogue-'+COUNT;
    _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 34);
config.notificationBase =
        Y.Node.create('<div class="'+CSS.BASE+'">')
              .append(Y.Node.create('<div id="'+id+'" role="dialog" aria-labelledby="'+id+'-header-text" class="'+CSS.WRAP+'"></div>')
              .append(Y.Node.create('<div class="'+CSS.HEADER+' yui3-widget-hd"></div>'))
              .append(Y.Node.create('<div class="'+CSS.BODY+' yui3-widget-bd"></div>'))
              .append(Y.Node.create('<div class="'+CSS.FOOTER+' yui3-widget-ft"></div>')));
    _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 40);
Y.one(document.body).append(config.notificationBase);
    _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 41);
config.srcNode =    '#'+id;
    _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 42);
config.width =      config.width || '400px';
    _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 43);
config.visible =    config.visible || false;
    _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 44);
config.center =     config.centered || true;
    _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 45);
config.centered =   false;

    // lightbox param to keep the stable versions API.
    _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 48);
if (config.lightbox !== false) {
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 49);
config.modal = true;
    }
    _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 51);
delete config.lightbox;

    // closeButton param to keep the stable versions API.
    _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 54);
if (config.closeButton === false) {
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 55);
config.buttons = null;
    } else {
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 57);
config.buttons = [
            {
                section: Y.WidgetStdMod.HEADER,
                classNames: 'closebutton',
                action: function () {
                    _yuitest_coverfunc("build/moodle-core-notification/moodle-core-notification.js", "action", 61);
_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 62);
this.hide();
                }
            }
        ];
    }
    _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 67);
DIALOGUE.superclass.constructor.apply(this, [config]);

    _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 69);
if (config.closeButton !== false) {
        // The buttons constructor does not allow custom attributes
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 71);
this.get('buttons').header[0].setAttribute('title', this.get('closeButtonTitle'));
    }
};
_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 74);
Y.extend(DIALOGUE, Y.Panel, {
    initializer : function() {
        _yuitest_coverfunc("build/moodle-core-notification/moodle-core-notification.js", "initializer", 75);
_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 76);
this.after('visibleChange', this.visibilityChanged, this);
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 77);
this.render();
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 78);
this.show();

        // Workaround upstream YUI bug http://yuilibrary.com/projects/yui3/ticket/2532507
        // and allow setting of z-index in theme.
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 82);
this.get('boundingBox').setStyle('zIndex', null);
    },
    visibilityChanged : function(e) {
        _yuitest_coverfunc("build/moodle-core-notification/moodle-core-notification.js", "visibilityChanged", 84);
_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 85);
var titlebar;
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 86);
if (e.attrName === 'visible') {
            _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 87);
this.get('maskNode').addClass(CSS.LIGHTBOX);
            _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 88);
if (this.get('center') && !e.prevVal && e.newVal) {
                _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 89);
this.centerDialogue();
            }
            _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 91);
if (this.get('draggable')) {
                _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 92);
titlebar = '#' + this.get('id') + ' .' + CSS.HEADER;
                _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 93);
this.plug(Y.Plugin.Drag, {handles : [titlebar]});
                _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 94);
Y.one(titlebar).setStyle('cursor', 'move');
            }
        }
    },
    centerDialogue : function() {
        _yuitest_coverfunc("build/moodle-core-notification/moodle-core-notification.js", "centerDialogue", 98);
_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 99);
var bb = this.get('boundingBox'),
            hidden = bb.hasClass(DIALOGUE_PREFIX+'-hidden'),
            x, y;
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 102);
if (hidden) {
            _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 103);
bb.setStyle('top', '-1000px').removeClass(DIALOGUE_PREFIX+'-hidden');
        }
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 105);
x = Math.max(Math.round((bb.get('winWidth') - bb.get('offsetWidth'))/2), 15);
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 106);
y = Math.max(Math.round((bb.get('winHeight') - bb.get('offsetHeight'))/2), 15) + Y.one(window).get('scrollTop');

        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 108);
if (hidden) {
            _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 109);
bb.addClass(DIALOGUE_PREFIX+'-hidden');
        }
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 111);
bb.setStyle('left', x).setStyle('top', y);
    }
}, {
    NAME : DIALOGUE_NAME,
    CSS_PREFIX : DIALOGUE_PREFIX,
    ATTRS : {
        notificationBase : {

        },
        lightbox : {
            validator : Y.Lang.isBoolean,
            value : true
        },
        closeButton : {
            validator : Y.Lang.isBoolean,
            value : true
        },
        closeButtonTitle : {
            validator : Y.Lang.isString,
            value : 'Close'
        },
        center : {
            validator : Y.Lang.isBoolean,
            value : true
        },
        draggable : {
            validator : Y.Lang.isBoolean,
            value : false
        }
    }
});

_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 143);
ALERT = function(config) {
    _yuitest_coverfunc("build/moodle-core-notification/moodle-core-notification.js", "ALERT", 143);
_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 144);
config.closeButton = false;
    _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 145);
ALERT.superclass.constructor.apply(this, [config]);
};
_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 147);
Y.extend(ALERT, DIALOGUE, {
    _enterKeypress : null,
    initializer : function() {
        _yuitest_coverfunc("build/moodle-core-notification/moodle-core-notification.js", "initializer", 149);
_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 150);
this.publish('complete');
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 151);
var yes = Y.Node.create('<input type="button" id="id_yuialertconfirm-' + this.COUNT + '" value="'+this.get(CONFIRMYES)+'" />'),
            content = Y.Node.create('<div class="confirmation-dialogue"></div>')
                    .append(Y.Node.create('<div class="confirmation-message">'+this.get('message')+'</div>'))
                    .append(Y.Node.create('<div class="confirmation-buttons"></div>')
                            .append(yes));
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 156);
this.get(BASE).addClass('moodle-dialogue-confirm');
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 157);
this.setStdModContent(Y.WidgetStdMod.BODY, content, Y.WidgetStdMod.REPLACE);
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 158);
this.setStdModContent(Y.WidgetStdMod.HEADER, '<h1 id="moodle-dialogue-'+COUNT+'-header-text">' + this.get(TITLE) + '</h1>', Y.WidgetStdMod.REPLACE);
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 159);
this.after('destroyedChange', function(){_yuitest_coverfunc("build/moodle-core-notification/moodle-core-notification.js", "(anonymous 2)", 159);
this.get(BASE).remove();}, this);
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 160);
this._enterKeypress = Y.on('key', this.submit, window, 'down:13', this);
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 161);
yes.on('click', this.submit, this);
    },
    submit : function() {
        _yuitest_coverfunc("build/moodle-core-notification/moodle-core-notification.js", "submit", 163);
_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 164);
this._enterKeypress.detach();
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 165);
this.fire('complete');
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 166);
this.hide();
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 167);
this.destroy();
    }
}, {
    NAME : ALERT_NAME,
    CSS_PREFIX : DIALOGUE_PREFIX,
    ATTRS : {
        title : {
            validator : Y.Lang.isString,
            value : 'Alert'
        },
        message : {
            validator : Y.Lang.isString,
            value : 'Confirm'
        },
        yesLabel : {
            validator : Y.Lang.isString,
            setter : function(txt) {
                _yuitest_coverfunc("build/moodle-core-notification/moodle-core-notification.js", "setter", 183);
_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 184);
if (!txt) {
                    _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 185);
txt = 'Ok';
                }
                _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 187);
return txt;
            },
            value : 'Ok'
        }
    }
});

_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 194);
CONFIRM = function(config) {
    _yuitest_coverfunc("build/moodle-core-notification/moodle-core-notification.js", "CONFIRM", 194);
_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 195);
CONFIRM.superclass.constructor.apply(this, [config]);
};
_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 197);
Y.extend(CONFIRM, DIALOGUE, {
    _enterKeypress : null,
    _escKeypress : null,
    initializer : function() {
        _yuitest_coverfunc("build/moodle-core-notification/moodle-core-notification.js", "initializer", 200);
_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 201);
this.publish('complete');
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 202);
this.publish('complete-yes');
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 203);
this.publish('complete-no');
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 204);
var yes = Y.Node.create('<input type="button" id="id_yuiconfirmyes-' + this.COUNT + '" value="'+this.get(CONFIRMYES)+'" />'),
            no = Y.Node.create('<input type="button" id="id_yuiconfirmno-' + this.COUNT + '" value="'+this.get(CONFIRMNO)+'" />'),
            content = Y.Node.create('<div class="confirmation-dialogue"></div>')
                        .append(Y.Node.create('<div class="confirmation-message">'+this.get(QUESTION)+'</div>'))
                        .append(Y.Node.create('<div class="confirmation-buttons"></div>')
                            .append(yes)
                            .append(no));
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 211);
this.get(BASE).addClass('moodle-dialogue-confirm');
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 212);
this.setStdModContent(Y.WidgetStdMod.BODY, content, Y.WidgetStdMod.REPLACE);
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 213);
this.setStdModContent(Y.WidgetStdMod.HEADER, '<h1 id="moodle-dialogue-'+COUNT+'-header-text">' + this.get(TITLE) + '</h1>', Y.WidgetStdMod.REPLACE);
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 214);
this.after('destroyedChange', function(){_yuitest_coverfunc("build/moodle-core-notification/moodle-core-notification.js", "(anonymous 3)", 214);
this.get(BASE).remove();}, this);
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 215);
this._enterKeypress = Y.on('key', this.submit, window, 'down:13', this, true);
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 216);
this._escKeypress = Y.on('key', this.submit, window, 'down:27', this, false);
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 217);
yes.on('click', this.submit, this, true);
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 218);
no.on('click', this.submit, this, false);
    },
    submit : function(e, outcome) {
        _yuitest_coverfunc("build/moodle-core-notification/moodle-core-notification.js", "submit", 220);
_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 221);
this._enterKeypress.detach();
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 222);
this._escKeypress.detach();
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 223);
this.fire('complete', outcome);
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 224);
if (outcome) {
            _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 225);
this.fire('complete-yes');
        } else {
            _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 227);
this.fire('complete-no');
        }
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 229);
this.hide();
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 230);
this.destroy();
    }
}, {
    NAME : CONFIRM_NAME,
    CSS_PREFIX : DIALOGUE_PREFIX,
    ATTRS : {
        yesLabel : {
            validator : Y.Lang.isString,
            value : 'Yes'
        },
        noLabel : {
            validator : Y.Lang.isString,
            value : 'No'
        },
        title : {
            validator : Y.Lang.isString,
            value : 'Confirm'
        },
        question : {
            validator : Y.Lang.isString,
            value : 'Are you sure?'
        }
    }
});
_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 254);
Y.augment(CONFIRM, Y.EventTarget);

_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 256);
EXCEPTION = function(config) {
    _yuitest_coverfunc("build/moodle-core-notification/moodle-core-notification.js", "EXCEPTION", 256);
_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 257);
config.width = config.width || (M.cfg.developerdebug)?Math.floor(Y.one(document.body).get('winWidth')/3)+'px':null;
    _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 258);
config.closeButton = true;
    _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 259);
EXCEPTION.superclass.constructor.apply(this, [config]);
};
_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 261);
Y.extend(EXCEPTION, DIALOGUE, {
    _hideTimeout : null,
    _keypress : null,
    initializer : function(config) {
        _yuitest_coverfunc("build/moodle-core-notification/moodle-core-notification.js", "initializer", 264);
_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 265);
var content,
            self = this,
            delay = this.get('hideTimeoutDelay');
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 268);
this.get(BASE).addClass('moodle-dialogue-exception');
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 269);
this.setStdModContent(Y.WidgetStdMod.HEADER, '<h1 id="moodle-dialogue-'+COUNT+'-header-text">' + config.name + '</h1>', Y.WidgetStdMod.REPLACE);
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 270);
content = Y.Node.create('<div class="moodle-exception"></div>')
                .append(Y.Node.create('<div class="moodle-exception-message">'+this.get('message')+'</div>'))
                .append(Y.Node.create('<div class="moodle-exception-param hidden param-filename"><label>File:</label> '+this.get('fileName')+'</div>'))
                .append(Y.Node.create('<div class="moodle-exception-param hidden param-linenumber"><label>Line:</label> '+this.get('lineNumber')+'</div>'))
                .append(Y.Node.create('<div class="moodle-exception-param hidden param-stacktrace"><label>Stack trace:</label> <pre>'+this.get('stack')+'</pre></div>'));
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 275);
if (M.cfg.developerdebug) {
            _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 276);
content.all('.moodle-exception-param').removeClass('hidden');
        }
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 278);
this.setStdModContent(Y.WidgetStdMod.BODY, content, Y.WidgetStdMod.REPLACE);

        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 280);
if (delay) {
            _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 281);
this._hideTimeout = setTimeout(function(){_yuitest_coverfunc("build/moodle-core-notification/moodle-core-notification.js", "(anonymous 4)", 281);
self.hide();}, delay);
        }
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 283);
this.after('visibleChange', this.visibilityChanged, this);
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 284);
this.after('destroyedChange', function(){_yuitest_coverfunc("build/moodle-core-notification/moodle-core-notification.js", "(anonymous 5)", 284);
this.get(BASE).remove();}, this);
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 285);
this._keypress = Y.on('key', this.hide, window, 'down:13,27', this);
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 286);
this.centerDialogue();
    },
    visibilityChanged : function(e) {
        _yuitest_coverfunc("build/moodle-core-notification/moodle-core-notification.js", "visibilityChanged", 288);
_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 289);
if (e.attrName === 'visible' && e.prevVal && !e.newVal) {
            _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 290);
if (this._keypress) {
                _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 291);
this._keypress.detach();
            }
            _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 293);
var self = this;
            _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 294);
setTimeout(function(){_yuitest_coverfunc("build/moodle-core-notification/moodle-core-notification.js", "(anonymous 6)", 294);
self.destroy();}, 1000);
        }
    }
}, {
    NAME : EXCEPTION_NAME,
    CSS_PREFIX : DIALOGUE_PREFIX,
    ATTRS : {
        message : {
            value : ''
        },
        name : {
            value : ''
        },
        fileName : {
            value : ''
        },
        lineNumber : {
            value : ''
        },
        stack : {
            setter : function(str) {
                _yuitest_coverfunc("build/moodle-core-notification/moodle-core-notification.js", "setter", 314);
_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 315);
var lines = str.split("\n"),
                    pattern = new RegExp('^(.+)@('+M.cfg.wwwroot+')?(.{0,75}).*:(\\d+)$'),
                    i;
                _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 318);
for (i in lines) {
                    _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 319);
lines[i] = lines[i].replace(pattern,
                            "<div class='stacktrace-line'>ln: $4</div><div class='stacktrace-file'>$3</div><div class='stacktrace-call'>$1</div>");
                }
                _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 322);
return lines.join('');
            },
            value : ''
        },
        hideTimeoutDelay : {
            validator : Y.Lang.isNumber,
            value : null
        }
    }
});

_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 333);
AJAXEXCEPTION = function(config) {
    _yuitest_coverfunc("build/moodle-core-notification/moodle-core-notification.js", "AJAXEXCEPTION", 333);
_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 334);
config.name = config.name || 'Error';
    _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 335);
config.closeButton = true;
    _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 336);
AJAXEXCEPTION.superclass.constructor.apply(this, [config]);
};
_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 338);
Y.extend(AJAXEXCEPTION, DIALOGUE, {
    _keypress : null,
    initializer : function(config) {
        _yuitest_coverfunc("build/moodle-core-notification/moodle-core-notification.js", "initializer", 340);
_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 341);
var content,
            self = this,
            delay = this.get('hideTimeoutDelay');
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 344);
this.get(BASE).addClass('moodle-dialogue-exception');
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 345);
this.setStdModContent(Y.WidgetStdMod.HEADER, '<h1 id="moodle-dialogue-'+COUNT+'-header-text">' + config.name + '</h1>', Y.WidgetStdMod.REPLACE);
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 346);
content = Y.Node.create('<div class="moodle-ajaxexception"></div>')
                .append(Y.Node.create('<div class="moodle-exception-message">'+this.get('error')+'</div>'))
                .append(Y.Node.create('<div class="moodle-exception-param hidden param-debuginfo"><label>URL:</label> '+this.get('reproductionlink')+'</div>'))
                .append(Y.Node.create('<div class="moodle-exception-param hidden param-debuginfo"><label>Debug info:</label> '+this.get('debuginfo')+'</div>'))
                .append(Y.Node.create('<div class="moodle-exception-param hidden param-stacktrace"><label>Stack trace:</label> <pre>'+this.get('stacktrace')+'</pre></div>'));
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 351);
if (M.cfg.developerdebug) {
            _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 352);
content.all('.moodle-exception-param').removeClass('hidden');
        }
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 354);
this.setStdModContent(Y.WidgetStdMod.BODY, content, Y.WidgetStdMod.REPLACE);

        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 356);
if (delay) {
            _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 357);
this._hideTimeout = setTimeout(function(){_yuitest_coverfunc("build/moodle-core-notification/moodle-core-notification.js", "(anonymous 7)", 357);
self.hide();}, delay);
        }
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 359);
this.after('visibleChange', this.visibilityChanged, this);
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 360);
this._keypress = Y.on('key', this.hide, window, 'down:13, 27', this);
        _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 361);
this.centerDialogue();
    },
    visibilityChanged : function(e) {
        _yuitest_coverfunc("build/moodle-core-notification/moodle-core-notification.js", "visibilityChanged", 363);
_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 364);
if (e.attrName === 'visible' && e.prevVal && !e.newVal) {
            _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 365);
var self = this;
            _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 366);
this._keypress.detach();
            _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 367);
setTimeout(function(){_yuitest_coverfunc("build/moodle-core-notification/moodle-core-notification.js", "(anonymous 8)", 367);
self.destroy();}, 1000);
        }
    }
}, {
    NAME : AJAXEXCEPTION_NAME,
    CSS_PREFIX : DIALOGUE_PREFIX,
    ATTRS : {
        error : {
            validator : Y.Lang.isString,
            value : 'Unknown error'
        },
        debuginfo : {
            value : null
        },
        stacktrace : {
            value : null
        },
        reproductionlink : {
            setter : function(link) {
                _yuitest_coverfunc("build/moodle-core-notification/moodle-core-notification.js", "setter", 385);
_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 386);
if (link !== null) {
                    _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 387);
link = '<a href="'+link+'">'+link.replace(M.cfg.wwwroot, '')+'</a>';
                }
                _yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 389);
return link;
            },
            value : null
        },
        hideTimeoutDelay : {
            validator : Y.Lang.isNumber,
            value : null
        }
    }
});

_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 400);
M.core = M.core || {};
_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 401);
M.core.dialogue = DIALOGUE;
_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 402);
M.core.alert = ALERT;
_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 403);
M.core.confirm = CONFIRM;
_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 404);
M.core.exception = EXCEPTION;
_yuitest_coverline("build/moodle-core-notification/moodle-core-notification.js", 405);
M.core.ajaxException = AJAXEXCEPTION;


}, '@VERSION@', {"requires": ["base", "node", "panel", "event-key", "dd-plugin"]});
