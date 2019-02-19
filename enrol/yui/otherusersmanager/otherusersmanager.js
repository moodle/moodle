YUI.add('moodle-enrol-otherusersmanager', function(Y) {

    var OUMANAGERNAME = 'Other users manager',
        OTHERUSERNAME = 'Other user (not enroled in course)',
        COURSEID = 'courseId',
        USERID = 'userId',
        BASE = 'base',
        SEARCH = 'search',
        REQUIREREFRESH = 'requiresRefresh',
        PAGE = 'page',
        USERCOUNT = 'userCount',
        PICTURE = 'picture',
        FULLNAME = 'fullname',
        EXTRAFIELDS = 'extrafields',
        ASSIGNABLEROLES = 'assignableRoles',
        USERS = 'users',
        URL = 'url',
        AJAXURL = 'ajaxUrl';

    CSS = {
        PANEL : 'other-user-manager-panel',
        WRAP : 'oump-wrap',
        HEADER : 'oump-header',
        CONTENT : 'oump-content',
        AJAXCONTENT : 'oump-ajax-content',
        SEARCHRESULTS : 'oump-search-results',
        TOTALUSERS : 'oump-total-users',
        USERS : 'oump-users',
        USER : 'oump-user',
        USERDETAILS : 'oump-user-details',
        MORERESULTS : 'oump-more-results',
        LIGHTBOX : 'oump-loading-lightbox',
        LOADINGICON : 'loading-icon',
        FOOTER : 'oump-footer',
        COUNT : 'count',
        PICTURE : 'oump-user-picture',
        DETAILS : 'oump-user-specifics',
        FULLNAME : 'oump-user-fullname',
        EXTRAFIELDS : 'oump-user-extrafields',
        OPTIONS : 'oump-role-options',
        ROLEOPTION : 'oump-assignable-role',
        ODD  : 'odd',
        EVEN : 'even',
        HIDDEN : 'hidden',
        SEARCH : 'oump-search',
        CLOSE : 'oump-panel-close',
        ALLROLESASSIGNED : 'oump-has-all-roles'
    };

    var OUMANAGER = function(config) {
        OUMANAGER.superclass.constructor.apply(this, arguments);
    };
    Y.extend(OUMANAGER, Y.Base, {
        _loadingNode : null,
        _escCloseEvent : null,
        initializer : function(config) {
            this.set(BASE, Y.Node.create('<div class="'+CSS.PANEL+' '+CSS.HIDDEN+'"></div>')
                .append(Y.Node.create('<div class="'+CSS.WRAP+'"></div>')
                    .append(Y.Node.create('<div class="'+CSS.HEADER+' header"></div>')
                        .append(Y.Node.create('<div class="'+CSS.CLOSE+'"></div>'))
                        .append(Y.Node.create('<h2>'+M.util.get_string('usersearch', 'enrol')+'</h2>')))
                    .append(Y.Node.create('<div class="'+CSS.CONTENT+'"></div>')
                        .append(Y.Node.create('<div class="'+CSS.AJAXCONTENT+'"></div>'))
                        .append(Y.Node.create('<div class="'+CSS.LIGHTBOX+' '+CSS.HIDDEN+'"></div>')
                            .append(Y.Node.create('<img alt="loading" class="'+CSS.LOADINGICON+'" />')
                                .setAttribute('src', M.util.image_url('i/loading', 'moodle')))
                            .setStyle('opacity', 0.5)))
                    .append(Y.Node.create('<div class="'+CSS.FOOTER+'"></div>')
                        .append(Y.Node.create('<div class="'+CSS.SEARCH+'"><label>'+M.util.get_string('usersearch', 'enrol')+'</label></div>')
                            .append(Y.Node.create('<input type="text" id="oump-usersearch" value="" />'))
                        )
                    )
                )
            );
            this.set(SEARCH, this.get(BASE).one('#oump-usersearch'));
            Y.all('.assignuserrole input').each(function(node){
                if (node.getAttribute('type', 'submit')) {
                    node.on('click', this.show, this);
                }
            }, this);
            this.get(BASE).one('.'+CSS.HEADER+' .'+CSS.CLOSE).on('click', this.hide, this);
            this._loadingNode = this.get(BASE).one('.'+CSS.CONTENT+' .'+CSS.LIGHTBOX);
            Y.on('key', this.getUsers, this.get(SEARCH), 'down:13', this);
            Y.one(document.body).append(this.get(BASE));

            var base = this.get(BASE);
            base.plug(Y.Plugin.Drag);
            base.dd.addHandle('.'+CSS.HEADER+' h2');
            base.one('.'+CSS.HEADER+' h2').setStyle('cursor', 'move');

            this.getAssignableRoles();
        },
        show : function(e) {
            e.preventDefault();
            e.halt();

            var base = this.get(BASE);
            base.removeClass(CSS.HIDDEN);
            var x = (base.get('winWidth') - 400)/2;
            var y = (parseInt(base.get('winHeight'))-base.get('offsetHeight'))/2 + parseInt(base.get('docScrollY'));
            if (y < parseInt(base.get('winHeight'))*0.1) {
                y = parseInt(base.get('winHeight'))*0.1;
            }
            base.setXY([x,y]);

            if (this.get(USERS)===null) {
                this.getUsers(e, false);
            }

            this._escCloseEvent = Y.on('key', this.hide, document.body, 'down:27', this);
        },
        hide : function() {
            if (this._escCloseEvent) {
                this._escCloseEvent.detach();
                this._escCloseEvent = null;
            }
            this.get(BASE).addClass(CSS.HIDDEN);
            if (this.get(REQUIREREFRESH)) {
                window.location = this.get(URL);
            }
        },
        getUsers : function(e, append) {
            if (e) {
                e.halt();
                e.preventDefault();
            }
            var on, params;
            if (append) {
                this.set(PAGE, this.get(PAGE)+1);
            } else {
                this.set(USERCOUNT, 0);
            }

            params = [];
            params['id'] = this.get(COURSEID);
            params['sesskey'] = M.cfg.sesskey;
            params['action'] = 'searchotherusers';
            params['search'] = this.get(SEARCH).get('value');
            params['page'] = this.get(PAGE);

            Y.io(M.cfg.wwwroot+this.get(AJAXURL), {
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
                    params:params
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
            var result;
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
            var usersnode, users = [], i=0, count=0, user;
            if (!args.append) {
                usersnode = Y.Node.create('<div class="'+CSS.USERS+'"></div>');
            } else {
                usersnode = this.get(BASE).one('.'+CSS.SEARCHRESULTS+' .'+CSS.USERS);
            }
            count = this.get(USERCOUNT);
            for (i in result.response.users) {
                count++;
                user = new OTHERUSER(result.response.users[i], count, this);
                usersnode.append(user.toHTML());
                users[user.get(USERID)] = user;
            }
            this.set(USERCOUNT, count);
            if (!args.append) {
                var usersstr = '';
                if (this.get(USERCOUNT) === 1) {
                    usersstr = M.util.get_string('ajaxoneuserfound', 'enrol');
                } else if (result.response.moreusers) {
                    usersstr = M.util.get_string('ajaxxmoreusersfound', 'enrol', this.get(USERCOUNT));
                } else {
                    usersstr = M.util.get_string('ajaxxusersfound', 'enrol', this.get(USERCOUNT));
                }

                var content = Y.Node.create('<div class="'+CSS.SEARCHRESULTS+'"></div>')
                    .append(Y.Node.create('<div class="'+CSS.TOTALUSERS+'">'+usersstr+'</div>'))
                    .append(usersnode);
                if (result.response.moreusers) {
                    var fetchmore = Y.Node.create('<div class="'+CSS.MORERESULTS+'"><a href="#">'+M.util.get_string('ajaxnext25', 'enrol')+'</a></div>');
                    fetchmore.on('click', this.getUsers, this, true);
                    content.append(fetchmore)
                }
                this.setContent(content);
            } else {
                if (!result.response.moreusers) {
                    this.get(BASE).one('.'+CSS.MORERESULTS).remove();
                }
            }
        },
        setContent : function(content) {
            this.get(BASE).one('.'+CSS.CONTENT+' .'+CSS.AJAXCONTENT).setContent(content);
        },
        getAssignableRoles : function() {
            Y.io(M.cfg.wwwroot+'/enrol/ajax.php', {
                method:'POST',
                data:'id='+this.get(COURSEID)+'&action=getassignable&otherusers=true&sesskey='+M.cfg.sesskey,
                on: {
                    complete: function(tid, outcome, args) {
                        try {
                            var roles = Y.JSON.parse(outcome.responseText);
                            if (roles.error) {
                                new M.core.ajaxException(roles);
                            } else {
                                this.set(ASSIGNABLEROLES, roles.response);
                            }
                        } catch (e) {
                            new M.core.exception(e);
                        }
                        this.getAssignableRoles = function() {
                            this.fire('assignablerolesloaded');
                        };
                        this.getAssignableRoles();
                    }
                },
                context:this
            });
        }
    }, {
        NAME : OUMANAGERNAME,
        ATTRS : {
            courseId : {

            },
            ajaxUrl : {
                validator : Y.Lang.isString
            },
            url : {
                validator : Y.Lang.isString
            },
            roles : {
                validator :Y.Lang.isArray,
                value : []
            },
            base : {
                setter : function(node) {
                    var n = Y.one(node);
                    if (!n) {
                        Y.fail(OUMANAGERNAME+': invalid base node set');
                    }
                    return n;
                }
            },
            search : {
                setter : function(node) {
                    var n = Y.one(node);
                    if (!n) {
                        Y.fail(OUMANAGERNAME+': invalid base node set');
                    }
                    return n;
                }
            },
            requiresRefresh : {
                validator : Y.Lang.isBoolean,
                value : false
            },
            users : {
                validator : Y.Lang.isArray,
                value : null
            },
            page : {
                validator : Y.Lang.isNumber,
                value : 0
            },
            userCount : {
                validator : Y.Lang.isNumber,
                value : 0
            },
            assignableRoles : {
                value : []
            }
        }
    });

    var OTHERUSER = function(config, count, manager) {
        this._count = count;
        this._manager = manager;
        OTHERUSER.superclass.constructor.apply(this, arguments);
    };
    Y.extend(OTHERUSER, Y.Base, {
        _count : 0,
        _manager : null,
        _node : null,
        _assignmentInProgress : false,
        initializer : function(config) {
            this.publish('assignrole:success');
            this.publish('assignrole:failure');
        },
        toHTML : function() {
            this._node = Y.Node.create('<div class="'+CSS.USER+' clearfix" rel="'+this.get(USERID)+'"></div>')
                .addClass((this._count%2)?CSS.ODD:CSS.EVEN)
                .append(Y.Node.create('<div class="'+CSS.COUNT+'">'+this._count+'</div>'))
                .append(Y.Node.create('<div class="'+CSS.USERDETAILS+'"></div>')
                    .append(Y.Node.create('<div class="'+CSS.PICTURE+'"></div>')
                        .append(Y.Node.create(this.get(PICTURE)))
                    )
                    .append(Y.Node.create('<div class="'+CSS.DETAILS+'"></div>')
                        .append(Y.Node.create('<div class="'+CSS.FULLNAME+'">'+this.get(FULLNAME)+'</div>'))
                        .append(Y.Node.create('<div class="'+CSS.EXTRAFIELDS+'">'+this.get(EXTRAFIELDS)+'</div>'))
                    )
                    .append(Y.Node.create('<div class="'+CSS.OPTIONS+'"><span class="label">'+M.util.get_string('assignrole', 'role')+': </span></div>'))
                );
            var roles = this._manager.get(ASSIGNABLEROLES);
            for (var i in roles) {
                var role = Y.Node.create('<a href="#" class="' + CSS.ROLEOPTION + '">' + roles[i].name + '</a>');
                role.on('click', this.assignRoleToUser, this, roles[i].id, role);
                this._node.one('.'+CSS.OPTIONS).append(role);
            }
            return this._node;
        },
        assignRoleToUser : function(e, roleid, node) {
            e.halt();
            if (this._assignmentInProgress) {
                return true;
            }
            this._node.addClass('assignment-in-progress');
            this._assignmentInProgress = true;
            Y.io(M.cfg.wwwroot+'/enrol/ajax.php', {
                method:'POST',
                data:'id='+this._manager.get(COURSEID)+'&action=assign&sesskey='+M.cfg.sesskey+'&roleid='+roleid+'&user='+this.get(USERID),
                on: {
                    complete: function(tid, outcome, args) {
                        try {
                            var o = Y.JSON.parse(outcome.responseText);
                            if (o.success) {
                                var options = args.node.ancestor('.'+CSS.OPTIONS);
                                if (options.all('.'+CSS.ROLEOPTION).size() == 1) {
                                    // This is the last node so remove the options div
                                    if (options.ancestor('.'+CSS.USER)) {
                                        options.ancestor('.'+CSS.USER).addClass(CSS.ALLROLESASSIGNED);
                                    }
                                    options.remove();
                                } else {
                                    // There are still more assignable roles
                                    args.node.remove();
                                }
                                this._manager.set(REQUIREREFRESH, true);
                            }
                        } catch (e) {
                            new M.core.exception(e);
                        }
                        this._assignmentInProgress = false;
                        this._node.removeClass('assignment-in-progress');
                    }
                },
                context:this,
                arguments:{
                    roleid : roleid,
                    node : node
                }
            });
            return true;
        }
    }, {
        NAME : OTHERUSERNAME,
        ATTRS : {
            userId : {

            },
            fullname : {
                validator : Y.Lang.isString
            },
            extrafields : {
                validator : Y.Lang.isString
            },
            picture : {
                validator : Y.Lang.isString
            }
        }
    });
    Y.augment(OTHERUSER, Y.EventTarget);

    M.enrol = M.enrol || {};
    M.enrol.otherusersmanager = {
        init : function(cfg) {
            new OUMANAGER(cfg);
        }
    }

}, '@VERSION@', {requires:['base','node', 'overlay', 'io-base', 'test', 'json-parse', 'event-delegate', 'dd-plugin', 'event-key', 'moodle-core-notification']});
