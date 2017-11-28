YUI.add('moodle-filter_glossary-autolinker', function (Y, NAME) {

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

AUTOLINKER = function() {
    AUTOLINKER.superclass.constructor.apply(this, arguments);
};
Y.extend(AUTOLINKER, Y.Base, {
    overlay: null,
    alertpanels: {},
    initializer: function() {
        var self = this;
        require(['core/event'], function(event) {
            Y.delegate('click', function(e) {
                e.preventDefault();

                // display a progress indicator
                var title = '',
                    content = Y.Node.create('<div id="glossaryfilteroverlayprogress">' +
                                            '</div>'),
                    o = new Y.Overlay({
                        headerContent:  title,
                        bodyContent: content
                    }),
                    fullurl,
                    cfg;

                window.require(['core/templates'], function(Templates) {
                    Templates.renderPix('i/loading', 'core').then(function(html) {
                        content.append(html);
                    });
                });

                self.overlay = o;
                o.render(Y.one(document.body));

                // Switch over to the ajax url and fetch the glossary item
                fullurl = this.getAttribute('href').replace('showentry.php', 'showentry_ajax.php');
                cfg = {
                    method: 'get',
                    context: self,
                    on: {
                        success: function(id, o) {
                            this.display_callback(o.responseText, event);
                        },
                        failure: function(id, o) {
                            var debuginfo = o.statusText;
                            if (M.cfg.developerdebug) {
                                o.statusText += ' (' + fullurl + ')';
                            }
                            new M.core.exception({message: debuginfo});
                        }
                    }
                };
                Y.io(fullurl, cfg);

            }, Y.one(document.body), 'a.glossary.autolink.concept');
        });
    },
    /**
     * @method display_callback
     * @param {String} content - Content to display
     * @param {Object} event The amd event module used to fire events for jquery and yui.
     */
    display_callback: function(content, event) {
        var data,
            key,
            alertpanel,
            alertpanelid,
            definition,
            position;
        try {
            data = Y.JSON.parse(content);
            if (data.success) {
                this.overlay.hide(); // hide progress indicator

                for (key in data.entries) {
                    definition = data.entries[key].definition + data.entries[key].attachments;
                    alertpanel = new M.core.alert({title: data.entries[key].concept, draggable: true,
                        message: definition, modal: false, yesLabel: M.util.get_string('ok', 'moodle')});
                    // Notify the filters about the modified nodes.
                    event.notifyFilterContentUpdated(alertpanel.get('boundingBox').getDOMNode());
                    Y.Node.one('#id_yuialertconfirm-' + alertpanel.get('COUNT')).focus();

                    // Register alertpanel for stacking.
                    alertpanelid = '#moodle-dialogue-' + alertpanel.get('COUNT');
                    alertpanel.on('complete', this._deletealertpanel, this, alertpanelid);

                    // We already have some windows opened, so set the right position...
                    if (!Y.Object.isEmpty(this.alertpanels)) {
                        position = this._getLatestWindowPosition();
                        Y.Node.one(alertpanelid).setXY([position[0] + 10, position[1] + 10]);
                    }

                    this.alertpanels[alertpanelid] = Y.Node.one(alertpanelid).getXY();
                }

                return true;
            } else if (data.error) {
                new M.core.ajaxException(data);
            }
        } catch (e) {
            new M.core.exception(e);
        }
        return false;
    },
    _getLatestWindowPosition: function() {
        var lastPosition = [0, 0];
        Y.Object.each(this.alertpanels, function(position) {
            if (position[0] > lastPosition[0]) {
                lastPosition = position;
            }
        });
        return lastPosition;
    },
    _deletealertpanel: function(ev, alertpanelid) {
        delete this.alertpanels[alertpanelid];
    }
}, {
    NAME: AUTOLINKERNAME,
    ATTRS: {
        url: {
            validator: Y.Lang.isString,
            value: M.cfg.wwwroot + '/mod/glossary/showentry.php'
        },
        name: {
            validator: Y.Lang.isString,
            value: 'glossaryconcept'
        },
        options: {
            getter: function() {
                return {
                    width: this.get(WIDTH),
                    height: this.get(HEIGHT),
                    menubar: this.get(MENUBAR),
                    location: this.get(LOCATION),
                    scrollbars: this.get(SCROLLBARS),
                    resizable: this.get(RESIZEABLE),
                    toolbar: this.get(TOOLBAR),
                    status: this.get(STATUS),
                    directories: this.get(DIRECTORIES),
                    fullscreen: this.get(FULLSCREEN),
                    dependent: this.get(DEPENDENT)
                };
            },
            readOnly: true
        },
        width: {value: 600},
        height: {value: 450},
        menubar: {value: false},
        location: {value: false},
        scrollbars: {value: true},
        resizable: {value: true},
        toolbar: {value: true},
        status: {value: true},
        directories: {value: false},
        fullscreen: {value: false},
        dependent: {value: true}
    }
});

M.filter_glossary = M.filter_glossary || {};
M.filter_glossary.init_filter_autolinking = function(config) {
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
        "moodle-core-event",
        "moodle-core-notification-alert",
        "moodle-core-notification-exception",
        "moodle-core-notification-ajaxexception"
    ]
});
