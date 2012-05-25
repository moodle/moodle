YUI.add('moodle-mod_glossary-autolinker', function(Y) {

    var AUTOLINKERNAME = 'Glossary autolinker',
        URL = 'url',
        POPUPNAME = 'name',
        POPUPOPTIONS = 'options',
        TITLE = 'title',
        COURSEID = 'courseid',
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
        DEPENDENT = 'dependent';

    var AUTOLINKER = function() {
        AUTOLINKER.superclass.constructor.apply(this, arguments);
    };
    Y.extend(AUTOLINKER, Y.Base, {
        overlay : null,
        initializer : function(config) {
            var popupname = this.get(POPUPNAME),
                popupoptions = this.get(POPUPOPTIONS),
                self = this;
            Y.delegate('click', function(e){

                e.preventDefault();

                //display a progress indicator
                var title = '';
                var content = Y.Node.create('<div id="glossaryoverlayprogress"><img src="'+M.cfg.loadingicon+'" class="spinner" /></div>');
                var o = new Y.Overlay({
                    headerContent :  title,
                    bodyContent : content
                });
                self.overlay = o;
                o.render(Y.one(document.body));

                //Switch over to the ajax url and fetch the glossary item
                var fullurl = this.getAttribute('href').replace('showentry.php','showentry_ajax.php');
                var cfg = {
                    method: 'get',
                    context : self,
                    on: {
                        success: function(id, o, node) {
                            this.display_callback(o.responseText);
                        },
                        failure: function(id, o, node) {
                            var debuginfo = o.statusText;
                            if (M.cfg.developerdebug) {
                                o.statusText += ' (' + fullurl + ')';
                            }
                            this.display_callback('bodyContent',debuginfo);
                        }
                    }
                };
                Y.io(fullurl, cfg);

            }, Y.one(document.body), 'a.glossary.autolink');
        },
        display_callback : function(content) {
            try {
                var data = Y.JSON.parse(content);
                if (data.success){
                    this.overlay.hide(); //hide progress indicator

                    for (key in data.entries) {
                        definition = data.entries[key].definition + data.entries[key].attachments
                        new M.core.alert({title:data.entries[key].concept, message:definition, lightbox:false});
                    }

                    return true;
                } else if (data.error) {
                    new M.core.ajaxException(data);
                }
            }catch(e) {
                new M.core.exception(e);
            }
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
                getter : function(val) {
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
                    }
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
            dependent : {value : true},
            courseid : {value : 1}
        }
    });

    M.mod_glossary = M.mod_glossary || {};
    M.mod_glossary.init_filter_autolinking = function(config) {
        return new AUTOLINKER(config);
    }

}, '@VERSION@', {requires:['base','node','event-delegate','overlay','moodle-enrol-notification']});
