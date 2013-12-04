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

DIALOGUE = function(config) {
    COUNT++;
    var id = 'moodle-dialogue-'+COUNT;
    config.notificationBase =
        Y.Node.create('<div class="'+CSS.BASE+'">')
              .append(Y.Node.create('<div id="'+id+'" role="dialog" aria-labelledby="'+id+'-header-text" class="'+CSS.WRAP+'"></div>')
              .append(Y.Node.create('<div id="'+id+'-header-text" class="'+CSS.HEADER+' yui3-widget-hd"></div>'))
              .append(Y.Node.create('<div class="'+CSS.BODY+' yui3-widget-bd"></div>'))
              .append(Y.Node.create('<div class="'+CSS.FOOTER+' yui3-widget-ft"></div>')));
    Y.one(document.body).append(config.notificationBase);
    config.srcNode =    '#'+id;
    config.width =      config.width || '400px';
    config.visible =    config.visible || false;
    config.center =     config.centered || true;
    config.centered =   false;

    // lightbox param to keep the stable versions API.
    if (config.lightbox !== false) {
        config.modal = true;
    }
    delete config.lightbox;

    // closeButton param to keep the stable versions API.
    if (config.closeButton === false) {
        config.buttons = null;
    } else {
        config.buttons = [
            {
                section: Y.WidgetStdMod.HEADER,
                classNames: 'closebutton',
                action: function () {
                    this.hide();
                }
            }
        ];
    }
    DIALOGUE.superclass.constructor.apply(this, [config]);

    if (config.closeButton !== false) {
        // The buttons constructor does not allow custom attributes
        this.get('buttons').header[0].setAttribute('title', this.get('closeButtonTitle'));
    }
};
Y.extend(DIALOGUE, Y.Panel, {
    initializer : function() {
        this.after('visibleChange', this.visibilityChanged, this);
        this.render();
        this.show();

        // Workaround upstream YUI bug http://yuilibrary.com/projects/yui3/ticket/2532507
        // and allow setting of z-index in theme.
        this.get('boundingBox').setStyle('zIndex', null);
    },
    visibilityChanged : function(e) {
        var titlebar;
        if (e.attrName === 'visible') {
            this.get('maskNode').addClass(CSS.LIGHTBOX);
            if (this.get('center') && !e.prevVal && e.newVal) {
                this.centerDialogue();
            }
            if (this.get('draggable')) {
                titlebar = '#' + this.get('id') + ' .' + CSS.HEADER;
                this.plug(Y.Plugin.Drag, {handles : [titlebar]});
                Y.one(titlebar).setStyle('cursor', 'move');
            }
        }
    },
    centerDialogue : function() {
        var bb = this.get('boundingBox'),
            hidden = bb.hasClass(DIALOGUE_PREFIX+'-hidden'),
            x, y;
        if (hidden) {
            bb.setStyle('top', '-1000px').removeClass(DIALOGUE_PREFIX+'-hidden');
        }
        x = Math.max(Math.round((bb.get('winWidth') - bb.get('offsetWidth'))/2), 15);
        y = Math.max(Math.round((bb.get('winHeight') - bb.get('offsetHeight'))/2), 15) + Y.one(window).get('scrollTop');

        if (hidden) {
            bb.addClass(DIALOGUE_PREFIX+'-hidden');
        }
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

ALERT = function(config) {
    config.closeButton = false;
    ALERT.superclass.constructor.apply(this, [config]);
};
Y.extend(ALERT, DIALOGUE, {
    _enterKeypress : null,
    initializer : function() {
        this.publish('complete');
        var yes = Y.Node.create('<input type="button" id="id_yuialertconfirm-' + this.COUNT + '" value="'+this.get(CONFIRMYES)+'" />'),
            content = Y.Node.create('<div class="confirmation-dialogue"></div>')
                    .append(Y.Node.create('<div class="confirmation-message">'+this.get('message')+'</div>'))
                    .append(Y.Node.create('<div class="confirmation-buttons"></div>')
                            .append(yes));
        this.get(BASE).addClass('moodle-dialogue-confirm');
        this.setStdModContent(Y.WidgetStdMod.BODY, content, Y.WidgetStdMod.REPLACE);
        this.setStdModContent(Y.WidgetStdMod.HEADER, '<h1 id="moodle-dialogue-'+COUNT+'-header-text">'
                + this.get(TITLE) + '</h1>', Y.WidgetStdMod.REPLACE);
        this.after('destroyedChange', function(){this.get(BASE).remove();}, this);
        this._enterKeypress = Y.on('key', this.submit, window, 'down:13', this);
        yes.on('click', this.submit, this);
    },
    submit : function() {
        this._enterKeypress.detach();
        this.fire('complete');
        this.hide();
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
                if (!txt) {
                    txt = 'Ok';
                }
                return txt;
            },
            value : 'Ok'
        }
    }
});

CONFIRM = function(config) {
    CONFIRM.superclass.constructor.apply(this, [config]);
};
Y.extend(CONFIRM, DIALOGUE, {
    _enterKeypress : null,
    _escKeypress : null,
    initializer : function() {
        this.publish('complete');
        this.publish('complete-yes');
        this.publish('complete-no');
        var yes = Y.Node.create('<input type="button" id="id_yuiconfirmyes-' + this.COUNT + '" value="'+this.get(CONFIRMYES)+'" />'),
            no = Y.Node.create('<input type="button" id="id_yuiconfirmno-' + this.COUNT + '" value="'+this.get(CONFIRMNO)+'" />'),
            content = Y.Node.create('<div class="confirmation-dialogue"></div>')
                        .append(Y.Node.create('<div class="confirmation-message">'+this.get(QUESTION)+'</div>'))
                        .append(Y.Node.create('<div class="confirmation-buttons"></div>')
                            .append(yes)
                            .append(no));
        this.get(BASE).addClass('moodle-dialogue-confirm');
        this.setStdModContent(Y.WidgetStdMod.BODY, content, Y.WidgetStdMod.REPLACE);
        this.setStdModContent(Y.WidgetStdMod.HEADER, '<h1 id="moodle-dialogue-'+COUNT+'-header-text">'
                + this.get(TITLE) + '</h1>', Y.WidgetStdMod.REPLACE);
        this.after('destroyedChange', function(){this.get(BASE).remove();}, this);
        this._enterKeypress = Y.on('key', this.submit, window, 'down:13', this, true);
        this._escKeypress = Y.on('key', this.submit, window, 'down:27', this, false);
        yes.on('click', this.submit, this, true);
        no.on('click', this.submit, this, false);
    },
    submit : function(e, outcome) {
        this._enterKeypress.detach();
        this._escKeypress.detach();
        this.fire('complete', outcome);
        if (outcome) {
            this.fire('complete-yes');
        } else {
            this.fire('complete-no');
        }
        this.hide();
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
Y.augment(CONFIRM, Y.EventTarget);

EXCEPTION = function(config) {
    config.width = config.width || (M.cfg.developerdebug)?Math.floor(Y.one(document.body).get('winWidth')/3)+'px':null;
    config.closeButton = true;
    EXCEPTION.superclass.constructor.apply(this, [config]);
};
Y.extend(EXCEPTION, DIALOGUE, {
    _hideTimeout : null,
    _keypress : null,
    initializer : function(config) {
        var content,
            self = this,
            delay = this.get('hideTimeoutDelay');
        this.get(BASE).addClass('moodle-dialogue-exception');
        this.setStdModContent(Y.WidgetStdMod.HEADER, '<h1 id="moodle-dialogue-'+COUNT+'-header-text">'
                + config.name + '</h1>', Y.WidgetStdMod.REPLACE);
        content = Y.Node.create('<div class="moodle-exception"></div>')
                .append(Y.Node.create('<div class="moodle-exception-message">'+this.get('message')+'</div>'))
                .append(Y.Node.create('<div class="moodle-exception-param hidden param-filename"><label>File:</label> '
                        + this.get('fileName')+'</div>'))
                .append(Y.Node.create('<div class="moodle-exception-param hidden param-linenumber"><label>Line:</label> '
                        + this.get('lineNumber')+'</div>'))
                .append(Y.Node.create('<div class="moodle-exception-param hidden param-stacktrace"><label>Stack trace:</label> <pre>'
                        + this.get('stack')+'</pre></div>'));
        if (M.cfg.developerdebug) {
            content.all('.moodle-exception-param').removeClass('hidden');
        }
        this.setStdModContent(Y.WidgetStdMod.BODY, content, Y.WidgetStdMod.REPLACE);

        if (delay) {
            this._hideTimeout = setTimeout(function(){self.hide();}, delay);
        }
        this.after('visibleChange', this.visibilityChanged, this);
        this.after('destroyedChange', function(){this.get(BASE).remove();}, this);
        this._keypress = Y.on('key', this.hide, window, 'down:13,27', this);
        this.centerDialogue();
    },
    visibilityChanged : function(e) {
        if (e.attrName === 'visible' && e.prevVal && !e.newVal) {
            if (this._keypress) {
                this._keypress.detach();
            }
            var self = this;
            setTimeout(function(){self.destroy();}, 1000);
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
                var lines = str.split("\n"),
                    pattern = new RegExp('^(.+)@('+M.cfg.wwwroot+')?(.{0,75}).*:(\\d+)$'),
                    i;
                for (i in lines) {
                    lines[i] = lines[i].replace(pattern,
                            "<div class='stacktrace-line'>ln: $4</div><div class='stacktrace-file'>$3</div><div class='stacktrace-call'>$1</div>");
                }
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

AJAXEXCEPTION = function(config) {
    config.name = config.name || 'Error';
    config.closeButton = true;
    AJAXEXCEPTION.superclass.constructor.apply(this, [config]);
};
Y.extend(AJAXEXCEPTION, DIALOGUE, {
    _keypress : null,
    initializer : function(config) {
        var content,
            self = this,
            delay = this.get('hideTimeoutDelay');
        this.get(BASE).addClass('moodle-dialogue-exception');
        this.setStdModContent(Y.WidgetStdMod.HEADER, '<h1 id="moodle-dialogue-'+COUNT+'-header-text">'
                + config.name + '</h1>', Y.WidgetStdMod.REPLACE);
        content = Y.Node.create('<div class="moodle-ajaxexception"></div>')
                .append(Y.Node.create('<div class="moodle-exception-message">'+this.get('error')+'</div>'))
                .append(Y.Node.create('<div class="moodle-exception-param hidden param-debuginfo"><label>URL:</label> '
                        + this.get('reproductionlink')+'</div>'))
                .append(Y.Node.create('<div class="moodle-exception-param hidden param-debuginfo"><label>Debug info:</label> '
                        + this.get('debuginfo')+'</div>'))
                .append(Y.Node.create('<div class="moodle-exception-param hidden param-stacktrace"><label>Stack trace:</label> <pre>'
                        + this.get('stacktrace')+'</pre></div>'));
        if (M.cfg.developerdebug) {
            content.all('.moodle-exception-param').removeClass('hidden');
        }
        this.setStdModContent(Y.WidgetStdMod.BODY, content, Y.WidgetStdMod.REPLACE);

        if (delay) {
            this._hideTimeout = setTimeout(function(){self.hide();}, delay);
        }
        this.after('visibleChange', this.visibilityChanged, this);
        this._keypress = Y.on('key', this.hide, window, 'down:13, 27', this);
        this.centerDialogue();
    },
    visibilityChanged : function(e) {
        if (e.attrName === 'visible' && e.prevVal && !e.newVal) {
            var self = this;
            this._keypress.detach();
            setTimeout(function(){self.destroy();}, 1000);
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
                if (link !== null) {
                    link = '<a href="'+link+'">'+link.replace(M.cfg.wwwroot, '')+'</a>';
                }
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

M.core = M.core || {};
M.core.dialogue = DIALOGUE;
M.core.alert = ALERT;
M.core.confirm = CONFIRM;
M.core.exception = EXCEPTION;
M.core.ajaxException = AJAXEXCEPTION;
