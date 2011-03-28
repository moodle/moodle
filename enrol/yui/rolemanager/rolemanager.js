YUI.add('moodle-enrol-rolemanager', function(Y) {

    var MOD_NAME                    = 'Moodle role manager',
        MOD_USER                    = 'Moodle role user',
        MOD_PANEL                   = 'Moodle role assignment panel',
        USERIDS                     = 'userIds',
        COURSEID                    = 'courseId',
        USERID                      = 'userId',
        CONTAINER                   = 'container',
        CONTAINERID                 = 'containerId',
        ASSIGNABLEROLES             = 'assignableRoles',
        ASSIGNROLELINK              = 'assignRoleLink',
        ASSIGNROLELINKSELECTOR      = 'assignRoleLinkSelector',
        UNASSIGNROLELINKS           = 'unassignRoleLinks',
        UNASSIGNROLELINKSSELECTOR   = 'unassignRoleLinksSelector',
        MANIPULATOR                 = 'manipulator',
        CURRENTROLES                = 'currentroles',
        OTHERUSERS                  = 'otherusers';

    var ROLE = function(config) {
        ROLE.superclass.constructor.apply(this, arguments);
    };
    ROLE.NAME = MOD_NAME;
    ROLE.ATTRS = {
        containerId : {
            validator: Y.Lang.isString
        },
        container : {
            setter : function(node) {
                var n = Y.one(node);
                if (!n) {
                    Y.fail(MOD_NAME+': invalid container set');
                }
                return n;
            }
        },
        courseId : {
            value: 0,
            setter : function(courseId) {
                if (!(/^\d+$/.test(courseId))) {
                    Y.fail(MOD_NAME+': Invalid course id specified');
                }
                return courseId;
            }
        },
        userIds : {
            validator: Y.Lang.isArray
        },
        assignableRoles : {
            value : []
        },
        otherusers : {
            value : false
        }
    };
    Y.extend(ROLE, Y.Base, {
        users : [],
        roleAssignmentPanel : null,
        panelsubmitevent : null,
        rolesLoadedEvent : null,
        escCloseEvent  : null,
        initializer : function(config) {
            var i;
            var container = Y.one('#'+this.get(CONTAINERID));
            container.addClass('ajaxactive');
            this.set(CONTAINER, container);

            var userids = this.get(USERIDS);
            for (i in userids) {
                this.users[userids[i]] = new ROLEUSER({userId:userids[i],manipulator:this}).wire();
            }
        },
        addRole : function(e, user) {
            e.halt();
            this.rolesLoadedEvent = this.on('assignablerolesloaded', function(){
                this.rolesLoadedEvent.detach();
                var panel = this._getRoleAssignmentPanel();
                this.panelsubmitevent = panel.on('submit', this.addRoleCallback, this);
                panel.hide().display(user);
            }, this);
            this._loadAssignableRoles();
        },
        addRoleCallback : function(e, roleid, userid) {
            this.panelsubmitevent.detach();
            var panel = this._getRoleAssignmentPanel();
            Y.io(M.cfg.wwwroot+'/enrol/ajax.php', {
                method:'POST',
                data:'id='+this.get(COURSEID)+'&action=assign&sesskey='+M.cfg.sesskey+'&roleid='+roleid+'&user='+userid,
                on: {
                    complete: function(tid, outcome, args) {
                        try {
                            var o = Y.JSON.parse(outcome.responseText);
                            if (o.error) {
                                new M.core.ajaxException(o);
                            } else {
                                panel.user.addRoleToDisplay(args.roleid, this.get(ASSIGNABLEROLES)[args.roleid]);
                            }
                        } catch (e) {
                            new M.core.exception(e);
                        }
                        panel.hide();
                    }
                },
                context:this,
                arguments:{
                    roleid : roleid
                }
            });
        },
        removeRole : function(e, user, roleid) {
            e.halt();
            var event = this.on('assignablerolesloaded', function(){
                event.detach();
                var s = M.str.role, confirmation = {
                    lightbox :  true,
                    title    :  s.confirmunassigntitle,
                    question :  s.confirmunassign,
                    yesLabel :  s.confirmunassignyes,
                    noLabel  :  s.confirmunassignno
                };
                new M.core.confirm(confirmation).on('complete-yes', this.removeRoleCallback, this, user.get(USERID), roleid);
            }, this);
            this._loadAssignableRoles();
        },
        removeRoleCallback : function(e, userid, roleid) {
            Y.io(M.cfg.wwwroot+'/enrol/ajax.php', {
                method:'POST',
                data:'id='+this.get(COURSEID)+'&action=unassign&sesskey='+M.cfg.sesskey+'&role='+roleid+'&user='+userid,
                on: {
                    complete: function(tid, outcome, args) {
                        var o;
                        try {
                            o = Y.JSON.parse(outcome.responseText);
                            if (o.error) {
                                new M.core.ajaxException(o);
                            } else {
                                this.users[userid].removeRoleFromDisplay(args.roleid);
                            }
                        } catch (e) {
                            new M.core.exception(e);
                        }
                    }
                },
                context:this,
                arguments:{
                    roleid : roleid
                }
            });
        },
        _loadAssignableRoles : function() {
            var c = this.get(COURSEID), params = {
                id : this.get(COURSEID),
                otherusers : (this.get(OTHERUSERS))?'true':'false',
                action : 'getassignable',
                sesskey : M.cfg.sesskey
            };
            Y.io(M.cfg.wwwroot+'/enrol/ajax.php', {
                method:'POST',
                data:build_querystring(params),
                on: {
                    complete: function(tid, outcome, args) {
                        try {
                            var roles = Y.JSON.parse(outcome.responseText);
                            this.set(ASSIGNABLEROLES, roles.response);
                        } catch (e) {
                            new M.core.exception(e);
                        }
                        this._loadAssignableRoles = function() {
                            this.fire('assignablerolesloaded');
                        };
                        this._loadAssignableRoles();
                    }
                },
                context:this
            });
        },
        _getRoleAssignmentPanel : function() {
            if (this.roleAssignmentPanel === null) {
                this.roleAssignmentPanel = new ROLEPANEL({manipulator:this});
            }
            return this.roleAssignmentPanel;
        }
    });
    Y.augment(ROLE, Y.EventTarget);

    var ROLEUSER = function(config) {
        ROLEUSER.superclass.constructor.apply(this, arguments);
    };
    ROLEUSER.NAME = MOD_USER;
    ROLEUSER.ATTRS = {
        userId  : {
            validator: Y.Lang.isNumber
        },
        manipulator : {
            validator: Y.Lang.isObject
        },
        container : {
            setter : function(node) {
                var n = Y.one(node);
                if (!n) {
                    Y.fail(MOD_USER+': invalid container set '+node);
                }
                return n;
            }
        },
        assignableroles : {
            value : []
        },
        currentroles : {
            value : [],
            validator: Y.Lang.isArray
        },
        assignRoleLink : {
            setter : function(node) {
                if (node===false) {
                    return node;
                }
                var n = Y.one(node);
                if (!n) {
                    Y.fail(MOD_NAME+': invalid assign role link given '+node);
                }
                return n;
            },
            value : false
        },
        assignRoleLinkSelector : {
            value : '.assignrolelink',
            validator : Y.Lang.isString
        },
        unassignRoleLinks : {
        },
        unassignRoleLinksSelector : {
            value : '.unassignrolelink',
            validator : Y.Lang.isString
        }
    };
    Y.extend(ROLEUSER, Y.Base, {
        initializer : function() {
            var container = this.get(MANIPULATOR).get(CONTAINER).one('#user_'+this.get(USERID));
            this.set(CONTAINER,        container);
            var assignrole = container.one(this.get(ASSIGNROLELINKSELECTOR));
            if (assignrole) {
                this.set(ASSIGNROLELINK, assignrole.ancestor());
            }
            this.set(UNASSIGNROLELINKS , container.all(this.get(UNASSIGNROLELINKSSELECTOR)));
        },
        wire : function() {
            var container = this.get(MANIPULATOR).get(CONTAINER).one('#user_'+this.get(USERID));
            var arl = this.get(ASSIGNROLELINK);
            var uarls = this.get(UNASSIGNROLELINKS);
            var m = this.get(MANIPULATOR);
            if (arl) {
                arl.ancestor().on('click', m.addRole, m, this);
            }
            var currentroles = [];
            if (uarls.size() > 0) {
                uarls.each(function(link){
                    link.roleId = link.getAttribute('rel');
                    link.on('click', m.removeRole, m, this, link.roleId);
                    currentroles[link.roleId] = true;
                }, this);
            }
            container.all('.role.unchangeable').each(function(node){
                currentroles[node.getAttribute('rel')] = true;
            }, this);

            this.set(CURRENTROLES, currentroles);
            return this;
        },
        _checkIfHasAllRoles : function() {
            var roles = this.get(MANIPULATOR).get(ASSIGNABLEROLES);
            var current = this.get(CURRENTROLES);
            var allroles = true, i = 0;
            for (i in roles) {
                if (!current[i]) {
                    allroles = false;
                    break;
                }
            }
            var link = this.get(ASSIGNROLELINK);
            if (allroles) {
                this.get(CONTAINER).addClass('hasAllRoles');
            } else {
                if (!link) {
                    var m = this.get(MANIPULATOR);
                    link = Y.Node.create('<div class="addrole"></div>').append(
                        Y.Node.create('<img alt="" />').setAttribute('src', M.util.image_url('t/enroladd', 'moodle'))
                    );
                    link.on('click', m.addRole, m, this);
                    this.get(CONTAINER).one('.col_role').insert(link, 0);
                    this.set(ASSIGNROLELINK, link);
                }
                this.get(CONTAINER).removeClass('hasAllRoles');
            }
        },
        addRoleToDisplay : function(roleId, roleTitle) {
            var m = this.get(MANIPULATOR);
            var container = this.get(CONTAINER);
            var role = Y.Node.create('<div class="role role_'+roleId+'">'+roleTitle+'<a class="unassignrolelink"><img src="'+M.util.image_url('t/delete', 'moodle')+'" alt="" /></a></div>');
            var link = role.one('.unassignrolelink');
            link.roleId = roleId;
            link.on('click', m.removeRole, m, this, link.roleId);
            container.one('.col_role .roles').append(role);
            this._toggleCurrentRole(link.roleId, true);
        },
        removeRoleFromDisplay : function(roleId) {
            var container = this.get(CONTAINER);
            container.all('.role_'+roleId).remove();
            this._toggleCurrentRole(roleId, false);
        },
        _toggleCurrentRole : function(roleId, hasRole) {
            var roles = this.get(CURRENTROLES);
            if (hasRole) {
                roles[roleId] = true;
            } else {
                roles[roleId] = false;
            }
            this.set(CURRENTROLES, roles);
            this._checkIfHasAllRoles();
        }
    });

    var ROLEPANEL = function(config) {
        ROLEPANEL.superclass.constructor.apply(this, arguments);
    };
    ROLEPANEL.NAME = MOD_PANEL;
    ROLEPANEL.ATTRS = {
        elementNode : {
            setter : function(node) {
                var n = Y.one(node);
                if (!n) {
                    Y.fail(MOD_PANEL+': Invalid element node');
                }
                return n;
            }
        },
        contentNode : {
            setter : function(node) {
                var n = Y.one(node);
                if (!n) {
                    Y.fail(MOD_PANEL+': Invalid content node');
                }
                return n;
            }
        },
        manipulator : {
            validator: Y.Lang.isObject
        }
    };
    Y.extend(ROLEPANEL, Y.Base, {
        user : null,
        roles : [],
        initializer : function() {
            var i, m = this.get(MANIPULATOR);
            var element = Y.Node.create('<div class="enrolpanel roleassign"><div class="container"><div class="header"><h2>'+M.str.role.assignroles+'</h2><div class="close"></div></div><div class="content"></div></div></div>');
            var content = element.one('.content');
            var roles = m.get(ASSIGNABLEROLES);
            for (i in roles) {
                var button = Y.Node.create('<input type="button" value="'+roles[i]+'" id="add_assignable_role_'+i+'" />');
                button.on('click', this.submit, this, i);
                content.append(button);
            }
            Y.one(document.body).append(element);
            this.set('elementNode', element);
            this.set('contentNode', content);
            element.one('.header .close').on('click', this.hide, this);
        },
        display : function(user) {
            var currentroles = user.get(CURRENTROLES), node = null;
            for (var i in currentroles) {
                if (currentroles[i] === true) {
                    if (node = this.get('contentNode').one('#add_assignable_role_'+i)) {
                        node.setAttribute('disabled', 'disabled');
                    }
                    this.roles.push(i);
                }
            }
            this.user = user;
            var roles = this.user.get(CONTAINER).one('.col_role .roles');
            var x = roles.getX() + 10;
            var y = roles.getY() + this.user.get(CONTAINER).get('offsetHeight') - 10;
            this.get('elementNode').setStyle('left', x).setStyle('top', y);
            this.get('elementNode').addClass('visible');
            this.escCloseEvent = Y.on('key', this.hide, document.body, 'down:27', this);
            this.displayed = true;
        },
        hide : function() {
            if (this._escCloseEvent) {
                this._escCloseEvent.detach();
                this._escCloseEvent = null;
            }
            var node = null;
            for (var i in this.roles) {
                if (node = this.get('contentNode').one('#add_assignable_role_'+this.roles[i])) {
                    node.removeAttribute('disabled');
                }
            }
            this.roles = [];
            this.user = null;
            this.get('elementNode').removeClass('visible');
            this.displayed = false;
            return this;
        },
        submit : function(e, roleid) {
            this.fire('submit', roleid, this.user.get(USERID));
        }
    });
    Y.augment(ROLEPANEL, Y.EventTarget);

    M.enrol = M.enrol || {};
    M.enrol.rolemanager = {
        instance : null,
        init : function(config) {
            M.enrol.rolemanager.instance = new ROLE(config);
            return M.enrol.rolemanager.instance;
        }
    }

}, '@VERSION@', {requires:['base','node','io','json-parse','test','moodle-enrol-notification']});