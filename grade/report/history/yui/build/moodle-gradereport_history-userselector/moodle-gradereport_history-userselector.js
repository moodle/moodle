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
    PANEL : 'user-selector-panel',
    WRAP : 'usp-wrap',
    HEADER : 'usp-header',
    CONTENT : 'usp-content',
    AJAXCONTENT : 'usp-ajax-content',
    SEARCHRESULTS : 'usp-search-results',
    TOTALUSERS : 'totalusers',
    USERS : 'users',
    USER : 'user',
    MORERESULTS : 'usp-more-results',
    LIGHTBOX : 'usp-loading-lightbox',
    LOADINGICON : 'loading-icon',
    FOOTER : 'usp-footer',
    DESELECT : 'deselect',
    SELECT : 'select',
    SELECTED : 'selected',
    COUNT : 'count',
    PICTURE : 'picture',
    DETAILS : 'details',
    FULLNAME : 'fullname',
    EXTRAFIELDS : 'extrafields',
    OPTIONS : 'options',
    ODD  : 'odd',
    EVEN : 'even',
    HIDDEN : 'hidden',
    SEARCH : 'usp-search',
    SEARCHBTN : 'usp-search-btn',
    CLOSE : 'close',
    CLOSEBTN : 'close-button'
};
var create = Y.Node.create;

var USERSELECTOR = function() {
    USERSELECTOR.superclass.constructor.apply(this, arguments);
};
Y.namespace('M.gradereport_history').UserSelector = Y.extend(USERSELECTOR, Y.Base, {
    _searchTimeout : null,
    _loadingNode : null,
    _escCloseEvent : null,
    initializer : function() {
        var list,
            params;

        this.set(USP.BASE, create('<div class="'+CSS.PANEL+' '+CSS.HIDDEN+'"></div>')
            .append(create('<div class="'+CSS.WRAP+'"></div>')
                .append(create('<div class="'+CSS.HEADER+' header"></div>')
                    .append(create('<div class="'+CSS.CLOSE+'"></div>'))
                    .append(create('<h2>'+M.util.get_string('selectuser', COMPONENT)+'</h2>')))
                .append(create('<div class="'+CSS.CONTENT+'"></div>')
                    .append(create('<div class="'+CSS.AJAXCONTENT+'"></div>'))
                    .append(create('<div class="'+CSS.LIGHTBOX+' '+CSS.HIDDEN+'"></div>')
                        .append(create('<img alt="loading" class="'+CSS.LOADINGICON+'" />')
                            .setAttribute('src', M.util.image_url('i/loading', 'moodle')))
                        .setStyle('opacity', 0.5)))
                .append(create('<div class="'+CSS.FOOTER+'"></div>')
                    .append(create('<div class="'+CSS.SEARCH+'"><label for="enrolusersearch" class="accesshide">'+Y.Escape.html(M.util.get_string('usersearch', 'enrol'))+'</label></div>')
                        .append(create('<input type="text" id="enrolusersearch" value="" />'))
                            .append(create('<input type="button" id="searchbtn" class="'+CSS.SEARCHBTN+'" value="'+Y.Escape.html(M.util.get_string('usersearch', 'enrol'))+'" />'))
                    )
                    .append(create('<div class="'+CSS.CLOSEBTN+'"></div>')
                        .append(create('<input type="button" value="'+Y.Escape.html(M.util.get_string('finishselectingusers', COMPONENT))+'" />'))
                    )
                )
            )
        );

        this.set(USP.SEARCH, this.get(USP.BASE).one('#enrolusersearch'));
        this.set(USP.SEARCHBTN, this.get(USP.BASE).one('#searchbtn'));
        list = Y.one('input[name="userids"]').get('value').split(',');
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

        Y.all('.gradereport_history_plugin input').each(function(node){
            if (node.hasClass('selectortrigger')) {
                node.on('click', this.show, this);
            }
        }, this);
        this.get(USP.BASE).one('.'+CSS.HEADER+' .'+CSS.CLOSE).on('click', this.hide, this);
        this.get(USP.BASE).one('.'+CSS.FOOTER+' .'+CSS.CLOSEBTN+' input').on('click', this.hide, this);
        this._loadingNode = this.get(USP.BASE).one('.'+CSS.CONTENT+' .'+CSS.LIGHTBOX);
        params = this.get(USP.PARAMS);
        params.id = this.get(USP.COURSEID);
        this.set(USP.PARAMS, params);

        Y.on('key', this.preSearch, this.get(USP.SEARCH), 'down:13', this);
        this.get(USP.SEARCHBTN).on('click', this.preSearch, this);

        Y.one(document.body).append(this.get(USP.BASE));

        var base = this.get(USP.BASE);
        base.plug(Y.Plugin.Drag);
        base.dd.addHandle('.'+CSS.HEADER+' h2');
        base.one('.'+CSS.HEADER+' h2').setStyle('cursor', 'move');

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
            count,
            selected,
            i,
            actionnode,
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
            users = this.get(USP.BASE).one('.'+CSS.SEARCHRESULTS+' .'+CSS.USERS);
        }
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
                actionnode = create('<input type="button" class="'+CSS.SELECT+'" value="'+Y.Escape.html(M.util.get_string('select', 'moodle'))+'" />');
            } else {
                actionnode = create('<input type="button" class="'+CSS.DESELECT+'" value="'+Y.Escape.html(M.util.get_string('deselect', COMPONENT))+'" />');
            }

            node = create('<div class="'+CSS.USER+selected+' clearfix" rel="'+user.userid+'"></div>')
                .addClass((count%2)?CSS.ODD:CSS.EVEN)
                .append(create('<div class="'+CSS.COUNT+'">'+count+'</div>'))
                .append(create('<div class="'+CSS.PICTURE+'"></div>')
                    .append(create(user.picture)))
                .append(create('<div class="'+CSS.DETAILS+'"></div>')
                    .append(create('<div class="'+CSS.FULLNAME+'">'+user.fullname+'</div>'))
                    .append(create('<div class="'+CSS.EXTRAFIELDS+'">'+user.extrafields+'</div>')))
                .append(create('<div class="'+CSS.OPTIONS+'"></div>')
                    .append(actionnode));
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
            Y.delegate("click", this.selectUser, users, '.'+CSS.USER+' .'+CSS.SELECT, this, args);
            Y.delegate("click", this.deselectUser, users, '.'+CSS.USER+' .'+CSS.DESELECT, this, args);
        } else {
            if (result.response.totalusers <= (this.get(USP.PAGE)+1)*this.get(USP.PERPAGE)) {
                this.get(USP.BASE).one('.'+CSS.MORERESULTS).remove();
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
        Y.one('input[name="userids"]').set('value', list.join());

        var namelist = this.get(USP.USERFULLNAMES);
        delete namelist[user.getAttribute('rel')];
        this.set(USP.USERFULLNAMES, namelist);
        this.setnamedisplay();

        user.removeClass(CSS.SELECTED);
        user.one('.'+CSS.DESELECT).remove();
        user.one('.'+CSS.OPTIONS).append(create('<input type="button" class="'+CSS.SELECT+'" value="'+Y.Escape.html(M.util.get_string('select', 'moodle'))+'" />'));
    },
    selectUser : function(e, args) {
        var user = e.currentTarget.ancestor('.'+CSS.USER);

        // Add id to the userids element and internal js list.
        var list = this.get(USP.SELECTEDUSERS);
        list.push(user.getAttribute('rel'));
        this.set(USP.SELECTEDUSERS, list);

        var fullname = user.one('.fullname').get('innerHTML');
        var namelist = this.get(USP.USERFULLNAMES);
        namelist[user.getAttribute('rel')] = fullname;
        this.set(USP.USERFULLNAMES, namelist);
        this.setnamedisplay();

        Y.one('input[name="userids"]').set('value', list.join());

        // Add name to selected list.

        user.addClass(CSS.SELECTED);
        user.one('.'+CSS.SELECT).remove();
        user.one('.'+CSS.OPTIONS).append(create('<input type="button" class="'+CSS.DESELECT+'" value="'+Y.Escape.html(M.util.get_string('deselect', COMPONENT))+'" />'));
    },
    setContent: function(content) {
        this.get(USP.BASE).one('.'+CSS.CONTENT+' .'+CSS.AJAXCONTENT).setContent(content);
    },
    setnamedisplay: function() {
        var namelist = this.get(USP.USERFULLNAMES);
        namelist = namelist.filter(function(x) {
             return x;
        });
        Y.one('.felement .selectednames').set('innerHTML', namelist.join(', '));
        Y.one('input[name="userfullnames"]').set('value', namelist.join());
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
        "io-base",
        "json-parse",
        "moodle-core-notification-dialogue",
        "overlay"
    ]
});
