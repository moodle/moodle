// shifter-ify
YUI.add('moodle-gradereport_history-quickselect', function(Y) {

    var UEP = {
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
        WRAP : 'uep-wrap',
        HEADER : 'uep-header',
        CONTENT : 'uep-content',
        AJAXCONTENT : 'uep-ajax-content',
        SEARCHRESULTS : 'uep-search-results',
        TOTALUSERS : 'totalusers',
        USERS : 'users',
        USER : 'user',
        MORERESULTS : 'uep-more-results',
        LIGHTBOX : 'uep-loading-lightbox',
        LOADINGICON : 'loading-icon',
        FOOTER : 'uep-footer',
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
        SEARCHOPTIONS : 'uep-searchoptions',
        ACTIVE : 'active',
        SEARCH : 'uep-search',
        SEARCHBTN : 'uep-search-btn',
        CLOSE : 'close',
        CLOSEBTN : 'close-button'
    };
    var create = Y.Node.create;

    var USERSELECTOR = function(config) {
        USERSELECTOR.superclass.constructor.apply(this, arguments);
    };
    Y.extend(USERSELECTOR, Y.Base, {
        _searchTimeout : null,
        _loadingNode : null,
        _escCloseEvent : null,
        initializer : function(config) {
            this.set(UEP.BASE, create('<div class="'+CSS.PANEL+' '+CSS.HIDDEN+'"></div>')
                .append(create('<div class="'+CSS.WRAP+'"></div>')
                    .append(create('<div class="'+CSS.HEADER+' header"></div>')
                        .append(create('<div class="'+CSS.CLOSE+'"></div>'))
                        .append(create('<h2>'+M.str.gradereport_history.selectuser+'</h2>')))
                    .append(create('<div class="'+CSS.CONTENT+'"></div>')
                        .append(create('<div class="'+CSS.AJAXCONTENT+'"></div>'))
                        .append(create('<div class="'+CSS.LIGHTBOX+' '+CSS.HIDDEN+'"></div>')
                            .append(create('<img alt="loading" class="'+CSS.LOADINGICON+'" />')
                                .setAttribute('src', M.util.image_url('i/loading', 'moodle')))
                            .setStyle('opacity', 0.5)))
                    .append(create('<div class="'+CSS.FOOTER+'"></div>')
                        .append(create('<div class="'+CSS.SEARCH+'"><label for="enrolusersearch" class="accesshide">'+M.str.enrol.usersearch+'</label></div>')
                            .append(create('<input type="text" id="enrolusersearch" value="" />'))
                                .append(create('<input type="button" id="searchbtn" class="'+CSS.SEARCHBTN+'" value="'+M.str.enrol.usersearch+'" />'))
                        )
                        .append(create('<div class="'+CSS.CLOSEBTN+'"></div>')
                            .append(create('<input type="button" value="'+M.str.gradereport_history.finishselectingusers+'" />'))
                        )
                    )
                )
            );

            this.set(UEP.SEARCH, this.get(UEP.BASE).one('#enrolusersearch'));
            this.set(UEP.SEARCHBTN, this.get(UEP.BASE).one('#searchbtn'));
            var list = Y.one('input[name="userids"]').get('value').split(',');
            if (list[0] == '') {
                list = [];
            }
            this.set(UEP.SELECTEDUSERS, list);

            var list = [];
            if (this.get(UEP.USERFULLNAMES) != null) {
                Y.each(this.get(UEP.USERFULLNAMES), function(value, key) {
                    list[key] = value;
                });
            }
            this.set(UEP.USERFULLNAMES, list);

            Y.all('.gradereport_history_plugin input').each(function(node){
                if (node.hasClass('selectortrigger')) {
                    node.on('click', this.show, this);
                }
            }, this);
            this.get(UEP.BASE).one('.'+CSS.HEADER+' .'+CSS.CLOSE).on('click', this.hide, this);
            this.get(UEP.BASE).one('.'+CSS.FOOTER+' .'+CSS.CLOSEBTN+' input').on('click', this.hide, this);
            this._loadingNode = this.get(UEP.BASE).one('.'+CSS.CONTENT+' .'+CSS.LIGHTBOX);
            var params = this.get(UEP.PARAMS);
            params['id'] = this.get(UEP.COURSEID);
            this.set(UEP.PARAMS, params);

            Y.on('key', this.preSearch, this.get(UEP.SEARCH), 'down:13', this);
            this.get(UEP.SEARCHBTN).on('click', this.preSearch, this);

            Y.one(document.body).append(this.get(UEP.BASE));

            var base = this.get(UEP.BASE);
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

            var base = this.get(UEP.BASE);
            base.removeClass(CSS.HIDDEN);
            var x = (base.get('winWidth') - 400)/2;
            var y = (parseInt(base.get('winHeight'))-base.get('offsetHeight'))/2 + parseInt(base.get('docScrollY'));
            if (y < parseInt(base.get('winHeight'))*0.1) {
                y = parseInt(base.get('winHeight'))*0.1;
            }
            base.setXY([x,y]);

            if (this.get(UEP.USERS)===null) {
                this.search(e, false);
            }

            this._escCloseEvent = Y.on('key', this.hide, document.body, 'down:27', this);
        },
        hide : function(e) {
            if (this._escCloseEvent) {
                this._escCloseEvent.detach();
                this._escCloseEvent = null;
            }
            this.get(UEP.BASE).addClass(CSS.HIDDEN);
        },
        search : function(e, append) {
            if (e) {
                e.halt();
                e.preventDefault();
            }
            var on, params;
            if (append) {
                this.set(UEP.PAGE, this.get(UEP.PAGE)+1);
            } else {
                this.set(UEP.USERCOUNT, 0);
                this.set(UEP.PAGE, 0);
            }
            params = this.get(UEP.PARAMS);
            params['sesskey'] = M.cfg.sesskey;
            params['action'] = 'searchusers';
            params['search'] = this.get(UEP.SEARCH).get('value');
            params['page'] = this.get(UEP.PAGE);
            params['perpage'] = this.get(UEP.PERPAGE);

            Y.io(M.cfg.wwwroot+this.get(UEP.AJAXURL), {
                method:'POST',
                data:build_querystring(params),
                on : {
                    start : this.displayLoading,
                    complete: this.processSearchResults,
                    end : this.removeLoading
                },
                context:this,
                arguments:{
                    append:append,
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
            try {
                var result = Y.JSON.parse(outcome.responseText);
                if (result.error) {
                    return new M.core.ajaxException(result);
                }
            } catch (e) {
                new M.core.exception(e);
            }
            if (!result.success) {
                this.setContent = M.str.enrol.errajaxsearch;
            }
            var users;
            if (!args.append) {
                users = create('<div class="'+CSS.USERS+'"></div>');
            } else {
                users = this.get(UEP.BASE).one('.'+CSS.SEARCHRESULTS+' .'+CSS.USERS);
            }
            var count = this.get(UEP.USERCOUNT);
            var selected = '';
            for (var i in result.response.users) {
                count++;
                var user = result.response.users[i];

                // If already selected, add class.
                if (this.get(UEP.SELECTEDUSERS).indexOf(user.userid) >= 0) {
                    selected = ' '+CSS.SELECTED;
                } else {
                    selected = '';
                }

                if (selected == '') {
                    var actionnode = create('<input type="button" class="'+CSS.SELECT+'" value="'+M.str.moodle.select+'" />');
                } else {
                    var actionnode = create('<input type="button" class="'+CSS.DESELECT+'" value="'+M.str.gradereport_history.deselect+'" />');
                }

                var node = create('<div class="'+CSS.USER+selected+' clearfix" rel="'+user.userid+'"></div>')
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
            this.set(UEP.USERCOUNT, count);
            if (!args.append) {
                var usersstr = (result.response.totalusers == '1')?M.str.enrol.ajaxoneuserfound:M.util.get_string('ajaxxusersfound','enrol', result.response.totalusers);
                var content = create('<div class="'+CSS.SEARCHRESULTS+'"></div>')
                    .append(create('<div class="'+CSS.TOTALUSERS+'">'+usersstr+'</div>'))
                    .append(users);
                if (result.response.totalusers > (this.get(UEP.PAGE)+1)*this.get(UEP.PERPAGE)) {
                    var fetchmore = create('<div class="'+CSS.MORERESULTS+'"><a href="#">'+M.str.enrol.ajaxnext25+'</a></div>');
                    fetchmore.on('click', this.search, this, true);
                    content.append(fetchmore)
                }
                this.setContent(content);
                Y.delegate("click", this.selectUser, users, '.'+CSS.USER+' .'+CSS.SELECT, this, args);
                Y.delegate("click", this.deselectUser, users, '.'+CSS.USER+' .'+CSS.DESELECT, this, args);
            } else {
                if (result.response.totalusers <= (this.get(UEP.PAGE)+1)*this.get(UEP.PERPAGE)) {
                    this.get(UEP.BASE).one('.'+CSS.MORERESULTS).remove();
                }
            }
        },
        deselectUser : function(e, args) {
            var user = e.currentTarget.ancestor('.'+CSS.USER);
            var list = this.get(UEP.SELECTEDUSERS);

            // Find and remove item from the array.
            var i = list.indexOf(user.getAttribute('rel'));
            if (i != -1) {
                list.splice(i, 1);
            }
            this.set(UEP.SELECTEDUSERS, list);
            Y.one('input[name="userids"]').set('value', list.join());

            var namelist = this.get(UEP.USERFULLNAMES);
            delete namelist[user.getAttribute('rel')];
            this.set(UEP.USERFULLNAMES, namelist);
            this.setnamedisplay();

            user.removeClass(CSS.SELECTED);
            user.one('.'+CSS.DESELECT).remove();
            user.one('.'+CSS.OPTIONS).append(create('<input type="button" class="'+CSS.SELECT+'" value="'+M.str.moodle.select+'" />'));
        },
        selectUser : function(e, args) {
            var user = e.currentTarget.ancestor('.'+CSS.USER);

            // Add id to the userids element and internal js list.
            var list = this.get(UEP.SELECTEDUSERS);
            list.push(user.getAttribute('rel'));
            this.set(UEP.SELECTEDUSERS, list);

            var fullname = user.one('.fullname').get('innerHTML');
            var namelist = this.get(UEP.USERFULLNAMES);
            namelist[user.getAttribute('rel')] = fullname;
            this.set(UEP.USERFULLNAMES, namelist);
            this.setnamedisplay();

            Y.one('input[name="userids"]').set('value', list.join());

            // Add name to selected list.

            user.addClass(CSS.SELECTED);
            user.one('.'+CSS.SELECT).remove();
            user.one('.'+CSS.OPTIONS).append(create('<input type="button" class="'+CSS.DESELECT+'" value="'+M.str.gradereport_history.deselect+'" />'));
        },
        setContent: function(content) {
            this.get(UEP.BASE).one('.'+CSS.CONTENT+' .'+CSS.AJAXCONTENT).setContent(content);
        },
        setnamedisplay: function() {
            var namelist = this.get(UEP.USERFULLNAMES);
            namelist = namelist.filter(function(x) {
                 return x;
            });
            Y.one('.felement .selectednames').set('innerHTML', namelist.join(', '));
            Y.one('input[name="userfullnames"]').set('value', namelist.join());
        }
    }, {
        NAME : UEP.NAME,
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
                        Y.fail(UEP.NAME+': invalid base node set');
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
                        Y.fail(UEP.NAME+': invalid search node set');
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
    Y.augment(USERSELECTOR, Y.EventTarget);

    M.gradereport_history = M.gradereport_history || {};
    M.gradereport_history.quickselect = {
        init : function(cfg) {
            new USERSELECTOR(cfg);
        }
    }

}, '@VERSION@', {requires:['base','node', 'overlay', 'io-base', 'test', 'json-parse', 'event-delegate', 'dd-plugin', 'event-key', 'moodle-core-notification']});
