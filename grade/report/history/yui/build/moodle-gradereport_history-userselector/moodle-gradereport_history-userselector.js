YUI.add('moodle-gradereport_history-userselector', function (Y, NAME) {

var COMPONENT = 'gradereport_history';
var USP = {
    NAME : 'User Selector Manager',
    /** Properties **/
    BASE : 'base',
    SEARCH : 'search',
    SEARCHBTN : 'searchbtn',
    PARAMS : 'params',
    URL : 'url',
    AJAXURL : 'ajaxurl',
    MULTIPLE : 'multiple',
    PAGE : 'page',
    COURSEID : 'courseid',
    SELECTEDUSERS : 'selectedusers',
    USERFULLNAMES : 'userfullnames',
    USERS : 'users',
    USERCOUNT : 'userCount',
    LASTSEARCH : 'lastPreSearchValue',
    PERPAGE : 'perPage'
};
/** CSS classes for nodes in structure **/
var CSS = {
    ACCESSHIDE : 'accesshide',
    AJAXCONTENT : 'usp-ajax-content',
    CLOSE : 'close',
    CLOSEBTN : 'close-button',
    CONTENT : 'usp-content',
    COUNT : 'count',
    DESELECT : 'deselect',
    DETAILS : 'details',
    EVEN : 'even',
    EXTRAFIELDS : 'extrafields',
    FOOTER : 'usp-footer',
    FULLNAME : 'fullname',
    HEADER : 'usp-header',
    HIDDEN : 'hidden',
    LIGHTBOX : 'usp-loading-lightbox',
    LOADINGICON : 'loading-icon',
    MORERESULTS : 'usp-more-results',
    ODD  : 'odd',
    OPTIONS : 'options',
    PANEL : 'user-selector-panel',
    PICTURE : 'picture',
    SEARCH : 'usp-search',
    SEARCHBTN : 'usp-search-btn',
    SEARCHFIELD : 'usp-search-field',
    SEARCHRESULTS : 'usp-search-results',
    SELECT : 'select',
    SELECTED : 'selected',
    TOTALUSERS : 'totalusers',
    USER : 'user',
    USERS : 'users',
    WRAP : 'usp-wrap'
};
var SELECTORS = {
    AJAXCONTENT: '.' + CSS.CONTENT + ' .' + CSS.AJAXCONTENT,
    DESELECT: '.' + CSS.DESELECT,
    FOOTERCLOSE: '.' + CSS.FOOTER + ' .' + CSS.CLOSEBTN + ' input',
    FULLNAME: '.' + CSS.FULLNAME,
    HEADERCLOSE: '.' + CSS.HEADER + ' .' + CSS.CLOSE,
    HEADING: '.' + CSS.HEADER + ' h2',
    LIGHTBOX: '.' + CSS.CONTENT + ' .' + CSS.LIGHTBOX,
    MORERESULTS: '.' + CSS.MORERESULTS,
    OPTIONS: '.' + CSS.OPTIONS,
    RESULTSUSERS: '.' + CSS.SEARCHRESULTS + ' .' + CSS.USERS,
    SEARCHBTN: '.' + CSS.SEARCHBTN,
    SEARCHFIELD: '.' + CSS.SEARCHFIELD,
    SELECT: '.' + CSS.SELECT,
    SELECTEDNAMES: '.felement .selectednames',
    TRIGGER: '.gradereport_history_plugin input.selectortrigger',
    USERDESELECT: '.' + CSS.USER + ' .' + CSS.DESELECT,
    USERFULLNAMES: 'input[name="userfullnames"]',
    USERIDS: 'input[name="userids"]',
    USERSELECT: '.' + CSS.USER + ' .' + CSS.SELECT
};
var create = Y.Node.create;

var USERSELECTOR = function() {
    USERSELECTOR.superclass.constructor.apply(this, arguments);
};
Y.namespace('M.gradereport_history').UserSelector = Y.extend(USERSELECTOR, Y.Base, {
    _searchTimeout : null,
    _loadingNode : null,
    _escCloseEvent : null,
    _userTemplate : null,
    initializer : function() {
        var list,
            params,
            tpl;

        tpl = Y.Handlebars.compile('<div class="{{CSS.PANEL}} {{CSS.HIDDEN}}">' +
                '<div class="{{CSS.WRAP}}">' +
                    '<div class="{{CSS.HEADER}}">' +
                        '<div class="{{CSS.CLOSE}}"></div>' +
                        '<h2>{{get_string "selectuser" COMPONENT}}</h2>' +
                    '</div>' +
                    '<div class="{{CSS.CONTENT}}">' +
                        '<div class="{{CSS.AJAXCONTENT}}"></div>' +
                        '<div class="{{CSS.LIGHTBOX}} {{CSS.HIDDEN}}">' +
                            '<img class="{{CSS.LOADINGICON}}" alt="{{get_string "loading" "admin"}}"' +
                                'src="{{{loadingIcon}}}">' +
                        '</div>' +
                    '</div>' +
                    '<div class="{{CSS.FOOTER}}">' +
                        '<div class="{{CSS.SEARCH}}">' +
                            '<label for="{{CSS.IDENROLUSERSEARCH}}" class="{{CSS.ACCESSHIDE}}">' +
                                '{{get_string "usersearch" "enrol"}}' +
                            '</label>' +
                            '<input type="text" class="{{CSS.SEARCHFIELD}}" value="" />' +
                            '<input type="button" class="{{CSS.SEARCHBTN}}"' +
                                'value="{{get_string "usersearch" "enrol"}}">' +
                        '</div>' +
                        '<div class="{{CSS.CLOSEBTN}}">' +
                            '<input type="button" value="{{get_string "finishselectingusers" COMPONENT}}">' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>');

        this.set(USP.BASE, create(
            tpl({
                COMPONENT: COMPONENT,
                CSS: CSS,
                loadingIcon: M.util.image_url('i/loading', 'moodle')
            })
        ));

        this.set(USP.SEARCH, this.get(USP.BASE).one(SELECTORS.SEARCHFIELD));
        this.set(USP.SEARCHBTN, this.get(USP.BASE).one(SELECTORS.SEARCHBTN));
        list = Y.one(SELECTORS.USERIDS).get('value').split(',');
        if (list[0] === '') {
            list = [];
        }
        this.set(USP.SELECTEDUSERS, list);

        list = [];
        if (this.get(USP.USERFULLNAMES) !== null) {
            Y.each(this.get(USP.USERFULLNAMES), function(value, key) {
                list[key] = value;
            });
        }
        this.set(USP.USERFULLNAMES, list);

        Y.one(SELECTORS.TRIGGER).on('click', this.show, this);

        this.get(USP.BASE).one(SELECTORS.HEADERCLOSE).on('click', this.hide, this);
        this.get(USP.BASE).one(SELECTORS.FOOTERCLOSE).on('click', this.hide, this);
        this._loadingNode = this.get(USP.BASE).one(SELECTORS.LIGHTBOX);
        params = this.get(USP.PARAMS);
        params.id = this.get(USP.COURSEID);
        this.set(USP.PARAMS, params);

        Y.on('key', this.preSearch, this.get(USP.SEARCH), 'down:13', this);
        this.get(USP.SEARCHBTN).on('click', this.preSearch, this);

        Y.one(document.body).append(this.get(USP.BASE));

        var base = this.get(USP.BASE);
        base.plug(Y.Plugin.Drag);
        base.dd.addHandle(SELECTORS.HEADING);
        base.one(SELECTORS.HEADING).setStyle('cursor', 'move');

    },
    preSearch : function(e) {
        this.search(null, false);
    },
    show : function(e) {
        e.preventDefault();
        e.halt();

        var base = this.get(USP.BASE);
        base.removeClass(CSS.HIDDEN);
        var x = (base.get('winWidth') - 400)/2;
        var y = (parseInt(base.get('winHeight'), 10)-base.get('offsetHeight'))/2 + parseInt(base.get('docScrollY'), 10);
        if (y < parseInt(base.get('winHeight'), 10)*0.1) {
            y = parseInt(base.get('winHeight'), 10)*0.1;
        }
        base.setXY([x,y]);

        if (this.get(USP.USERS)===null) {
            this.search(e, false);
        }

        this._escCloseEvent = Y.on('key', this.hide, document.body, 'down:27', this);
    },
    hide : function(e) {
        if (this._escCloseEvent) {
            this._escCloseEvent.detach();
            this._escCloseEvent = null;
        }
        this.get(USP.BASE).addClass(CSS.HIDDEN);
    },
    search : function(e, append) {
        if (e) {
            e.halt();
            e.preventDefault();
        }
        var params;
        if (append) {
            this.set(USP.PAGE, this.get(USP.PAGE)+1);
        } else {
            this.set(USP.USERCOUNT, 0);
            this.set(USP.PAGE, 0);
        }
        params = this.get(USP.PARAMS);
        params.sesskey = M.cfg.sesskey;
        params.action = 'searchusers';
        params.search = this.get(USP.SEARCH).get('value');
        params.page = this.get(USP.PAGE);
        params.perpage = this.get(USP.PERPAGE);

        Y.io(M.cfg.wwwroot+this.get(USP.AJAXURL), {
            method:'POST',
            data:build_querystring(params),
            on : {
                start : this.displayLoading,
                complete: this.processSearchResults,
                end : this.removeLoading
            },
            context:this,
            arguments:{
                append:append
            }
        });
    },
    displayLoading : function() {
        this._loadingNode.removeClass(CSS.HIDDEN);
    },
    removeLoading : function() {
        this._loadingNode.addClass(CSS.HIDDEN);
    },
    processSearchResults : function(tid, outcome, args) {
        var result = false,
            users,
            userTemplate,
            count,
            selected,
            i,
            actionClass,
            actionStr,
            actionComponent,
            node,
            usersstr,
            content,
            fetchmore;
        try {
            result = Y.JSON.parse(outcome.responseText);
            if (result.error) {
                return new M.core.ajaxException(result);
            }
        } catch (e) {
            new M.core.exception(e);
        }
        if (!result.success) {
            this.setContent = M.util.get_string('errajaxsearch', 'enrol');
        }

        if (!args.append) {
            users = create('<div class="'+CSS.USERS+'"></div>');
        } else {
            users = this.get(USP.BASE).one(SELECTORS.RESULTSUSERS);
        }

        if (!this._userTemplate) {
            this._userTemplate = Y.Handlebars.compile(
                '<div class="{{CSS.USER}} {{selected}} clearfix" rel="{{userId}}">' +
                    '<div class="{{CSS.COUNT}}">{{count}}</div>' +
                    '<div class="{{CSS.PICTURE}}">{{{picture}}}</div>' +
                    '<div class="{{CSS.DETAILS}}">' +
                        '<div class="{{CSS.FULLNAME}}">{{fullname}}</div>' +
                        '<div class="{{CSS.EXTRAFIELDS}}">{{extrafields}}</div>' +
                    '</div>' +
                    '<div class="{{CSS.OPTIONS}}">' +
                        '<input type="button" class="{{actionClass}}" value="{{get_string actionStr actionComponent}}">' +
                    '</div>' +
                '</div>'
            );
        }
        userTemplate = this._userTemplate;

        count = this.get(USP.USERCOUNT);
        selected = '';
        for (i in result.response.users) {
            count++;
            user = result.response.users[i];

            // If already selected, add class.
            if (this.get(USP.SELECTEDUSERS).indexOf(user.userid) >= 0) {
                selected = ' '+CSS.SELECTED;
            } else {
                selected = '';
            }

            if (selected === '') {
                actionClass = CSS.SELECT;
                actionStr = 'select';
                actionComponent = 'moodle';
            } else {
                actionClass = CSS.DESELECT;
                actionStr = 'deselect';
                actionComponent = COMPONENT;
            }

            node = create(userTemplate({
                actionClass: actionClass,
                actionComponent: actionComponent,
                actionStr: actionStr,
                count: count,
                CSS: CSS,
                extrafields: user.extrafields,
                fullname: user.fullname,
                picture: user.picture,
                selected: selected,
                userId: user.userid,
            }));
            users.append(node);
        }
        this.set(USP.USERCOUNT, count);
        if (!args.append) {
            if (result.response.totalusers == '1') {
                usersstr = M.util.get_string('ajaxoneuserfound', 'enrol');
            } else {
                usersstr = M.util.get_string('ajaxxusersfound','enrol', result.response.totalusers);
            }
            content = create('<div class="'+CSS.SEARCHRESULTS+'"></div>')
                .append(create('<div class="'+CSS.TOTALUSERS+'">'+usersstr+'</div>'))
                .append(users);
            if (result.response.totalusers > (this.get(USP.PAGE)+1)*this.get(USP.PERPAGE)) {
                fetchmore = create('<div class="'+CSS.MORERESULTS+'"><a href="#">'+M.util.get_string('ajaxnext25', 'enrol')+'</a></div>');
                fetchmore.on('click', this.search, this, true);
                content.append(fetchmore);
            }
            this.setContent(content);
            Y.delegate("click", this.selectUser, users, SELECTORS.USERSELECT, this, args);
            Y.delegate("click", this.deselectUser, users, SELECTORS.USERDESELECT, this, args);
        } else {
            if (result.response.totalusers <= (this.get(USP.PAGE)+1)*this.get(USP.PERPAGE)) {
                this.get(USP.BASE).one(SELECTORS.MORERESULTS).remove();
            }
        }
    },
    deselectUser : function(e, args) {
        var user = e.currentTarget.ancestor('.'+CSS.USER);
        var list = this.get(USP.SELECTEDUSERS);

        // Find and remove item from the array.
        var i = list.indexOf(user.getAttribute('rel'));
        if (i != -1) {
            list.splice(i, 1);
        }
        this.set(USP.SELECTEDUSERS, list);
        Y.one(SELECTORS.USERIDS).set('value', list.join());

        var namelist = this.get(USP.USERFULLNAMES);
        delete namelist[user.getAttribute('rel')];
        this.set(USP.USERFULLNAMES, namelist);
        this.setnamedisplay();

        user.removeClass(CSS.SELECTED);
        user.one(SELECTORS.DESELECT).remove();
        user.one(SELECTORS.OPTIONS).append(create('<input type="button" class="'+CSS.SELECT+'" value="'+Y.Escape.html(M.util.get_string('select', 'moodle'))+'" />'));
    },
    selectUser : function(e, args) {
        var user = e.currentTarget.ancestor('.'+CSS.USER);

        // Add id to the userids element and internal js list.
        var list = this.get(USP.SELECTEDUSERS);
        list.push(user.getAttribute('rel'));
        this.set(USP.SELECTEDUSERS, list);

        var fullname = user.one(SELECTORS.FULLNAME).get('innerHTML');
        var namelist = this.get(USP.USERFULLNAMES);
        namelist[user.getAttribute('rel')] = fullname;
        this.set(USP.USERFULLNAMES, namelist);
        this.setnamedisplay();

        Y.one(SELECTORS.USERIDS).set('value', list.join());

        // Add name to selected list.

        user.addClass(CSS.SELECTED);
        user.one(SELECTORS.SELECT).remove();
        user.one(SELECTORS.OPTIONS).append(create('<input type="button" class="'+CSS.DESELECT+'" value="'+Y.Escape.html(M.util.get_string('deselect', COMPONENT))+'" />'));
    },
    setContent: function(content) {
        this.get(USP.BASE).one(SELECTORS.AJAXCONTENT).setContent(content);
    },
    setnamedisplay: function() {
        var namelist = this.get(USP.USERFULLNAMES);
        namelist = namelist.filter(function(x) {
             return x;
        });
        Y.one(SELECTORS.SELECTEDNAMES).set('innerHTML', namelist.join(', '));
        Y.one(SELECTORS.USERFULLNAMES).set('value', namelist.join());
    }
}, {
    NAME : USP.NAME,
    ATTRS : {
        url : {
            validator : Y.Lang.isString
        },
        ajaxurl : {
            validator : Y.Lang.isString
        },
        base : {
            setter : function(node) {
                var n = Y.one(node);
                if (!n) {
                    Y.fail(USP.NAME+': invalid base node set');
                }
                return n;
            }
        },
        users : {
            validator : Y.Lang.isArray,
            value : null
        },
        selectedusers : {
            validator : Y.Lang.isArray,
            value : null
        },
        userfullnames : {
            validator : Y.Lang.isObject,
            value : null
        },
        courseid : {
            value : null
        },
        params : {
            validator : Y.Lang.isArray,
            value : []
        },
        multiple : {
            validator : Y.Lang.isBool,
            value : false
        },
        page : {
            validator : Y.Lang.isNumber,
            value : 0
        },
        userCount : {
            value : 0,
            validator : Y.Lang.isNumber
        },
        requiresRefresh : {
            value : false,
            validator : Y.Lang.isBool
        },
        search : {
            setter : function(node) {
                var n = Y.one(node);
                if (!n) {
                    Y.fail(USP.NAME+': invalid search node set');
                }
                return n;
            }
        },
        lastPreSearchValue : {
            value : '',
            validator : Y.Lang.isString
        },
        strings  : {
            value : {},
            validator : Y.Lang.isObject
        },
        perPage : {
            value: 25,
            Validator: Y.Lang.isNumber
        }
    }
});
Y.augment(Y.namespace('M.gradereport_history').UserSelector, Y.EventTarget);

Y.namespace('M.gradereport_history.UserSelector').init = function(cfg) {
    return new USERSELECTOR(cfg);
};


}, '@VERSION@', {
    "requires": [
        "dd-plugin",
        "escape",
        "event-delegate",
        "event-key",
        "handlebars",
        "io-base",
        "json-parse",
        "moodle-core-notification-dialogue",
        "overlay"
    ]
});
