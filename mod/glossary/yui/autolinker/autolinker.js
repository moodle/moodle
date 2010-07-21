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
    }
    Y.extend(AUTOLINKER, Y.Base, {
        initializer : function(config) {
            var popupname = this.get(POPUPNAME),
                popupoptions = this.get(POPUPOPTIONS);
            Y.delegate('click', function(e){
                openpopup(e, {
                    url : this.getAttribute('href')+'&popup=1',
                    name : popupname,
                    options : build_querystring(popupoptions)
                })
            }, Y.one(document.body), 'a.glossary.autolink');
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

}, '@VERSION@', {requires:['base','node','event-delegate']});