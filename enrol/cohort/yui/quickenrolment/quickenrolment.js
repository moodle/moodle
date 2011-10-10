YUI.add('moodle-enrol_cohort-quickenrolment', function(Y) {

    var CONTROLLERNAME = 'Quick cohort enrolment controller',
        COHORTNAME = 'Cohort',
        COHORTID = 'cohortid',
        ENROLLED = 'enrolled',
        NAME = 'name',
        USERS = 'users',
        COURSEID = 'courseid',
        ASSIGNABLEROLES = 'assignableRoles',
        DEFAULTCOHORTROLE = 'defaultCohortRole',
        COHORTS = 'cohorts',
        PANELID = 'qce-panel-',
        URL = 'url',
        AJAXURL = 'ajaxurl',
        MANUALENROLMENT = 'manualEnrolment',
        CSS = {
            COHORT : 'qce-cohort',
            COHORTS : 'qce-cohorts',
            COHORTBUTTON : 'qce-cohort-button',
            COHORTENROLLED : 'qce-cohort-enrolled',
            COHORTNAME : 'qce-cohort-name',
            COHORTUSERS : 'qce-cohort-users',
            PANEL : 'qce-panel',
            PANELCONTENT : 'qce-panel-content',
            PANELCOHORTS : 'qce-enrollable-cohorts',
            PANELROLES : 'qce-assignable-roles',
            PANELCONTROLS : 'qce-panel-controls',
            ENROLUSERS : 'canenrolusers'
        },
        COUNT = 0;


    var CONTROLLER = function(config) {
        CONTROLLER.superclass.constructor.apply(this, arguments);
    };
    CONTROLLER.prototype = {
        _preformingAction : false,
        initializer : function(config) {
            COUNT ++;
            this.publish('assignablerolesloaded', {fireOnce:true});
            this.publish('cohortsloaded');
            this.publish('performingaction');
            this.publish('actioncomplete');
            this.publish('defaultcohortroleloaded', {fireOnce:true});

            var close = Y.Node.create('<div class="close"></div>');
            var panel = new Y.Overlay({
                headerContent : Y.Node.create('<div></div>').append(Y.Node.create('<h2>'+M.str.enrol.enrolcohort+'</h2>')).append(close),
                bodyContent : Y.Node.create('<div class="loading"></div>').append(Y.Node.create('<img alt="loading" />').setAttribute('src', M.cfg.loadingicon)),
                constrain : true,
                centered : true,
                id : PANELID+COUNT,
                visible : false
            });
            panel.get('boundingBox').addClass(CSS.PANEL);
            panel.render(Y.one(document.body));
            this.on('show', function(){
                this.set('centered', true);
                this.show();
            }, panel);
            this.on('hide', panel.hide, panel);
            this.on('performingaction', function(){
                this.get('boundingBox').append(Y.Node.create('<div class="performing-action"></div>').append(Y.Node.create('<img alt="loading" />').setAttribute('src', M.cfg.loadingicon)).setStyle('opacity', 0.5));
            }, panel);
            this.on('actioncomplete', function(){
                this.get('boundingBox').one('.performing-action').remove();
            }, panel);
            this.on('assignablerolesloaded', this.updateContent, this, panel);
            this.on('cohortsloaded', this.updateContent, this, panel);
            this.on('defaultcohortroleloaded', this.updateContent, this, panel);
            close.on('click', this.hide, this);

            Y.all('.enrol_cohort_plugin input').each(function(node){
                if (node.getAttribute('type', 'submit')) {
                    node.on('click', this.show, this);
                }
            }, this);

            var base = panel.get('boundingBox');
            base.plug(Y.Plugin.Drag);
            base.dd.addHandle('.yui3-widget-hd h2');
            base.one('.yui3-widget-hd h2').setStyle('cursor', 'move');
        },
        show : function(e) {
            e.preventDefault();
            this.getCohorts();
            this.getAssignableRoles();
            this.fire('show');
        },
        updateContent : function(e, panel) {
            if (panel.get('contentBox').one('.loading')) {
                panel.set('bodyContent', Y.Node.create('<div class="'+CSS.PANELCONTENT+'"></div>')
                    .append(Y.Node.create('<div class="'+CSS.PANELCOHORTS+'"><div class="'+CSS.COHORT+' headings"><div class="'+CSS.COHORTBUTTON+'"></div><div class="'+CSS.COHORTNAME+'">'+M.str.cohort.cohort+'</div><div class="'+CSS.COHORTUSERS+'">'+M.str.moodle.users+'</div></div></div>'))
                    .append(Y.Node.create('<div class="'+CSS.PANELROLES+'"></div>')));
            }
            var content, i, roles, cohorts, count=0, supportmanual = this.get(MANUALENROLMENT), defaultrole;
            switch (e.type.replace(/^[^:]+:/, '')) {
                case 'cohortsloaded' :
                    cohorts = this.get(COHORTS);
                    content = Y.Node.create('<div class="'+CSS.COHORTS+'"></div>');
                    if (supportmanual) {
                        content.addClass(CSS.ENROLUSERS);
                    }
                    for (i in cohorts) {
                        count++;
                        cohorts[i].on('enrolchort', this.enrolCohort, this, cohorts[i], panel.get('contentBox'), false);
                        cohorts[i].on('enrolusers', this.enrolCohort, this, cohorts[i], panel.get('contentBox'), true);
                        content.append(cohorts[i].toHTML(supportmanual).addClass((count%2)?'even':'odd'));
                    }
                    panel.get('contentBox').one('.'+CSS.PANELCOHORTS).setContent(content);
                    break;
                case 'assignablerolesloaded':
                    roles = this.get(ASSIGNABLEROLES);
                    content = Y.Node.create('<select></select>');
                    for (i in roles) {
                        content.append(Y.Node.create('<option value="'+i+'">'+roles[i]+'</option>'));
                    }
                    panel.get('contentBox').one('.'+CSS.PANELROLES).setContent(Y.Node.create('<div>'+M.str.role.assignroles+': </div>').append(content));

                    this.getDefaultCohortRole();
                    break;
                case 'defaultcohortroleloaded':
                    defaultrole = this.get(DEFAULTCOHORTROLE);
                    panel.get('contentBox').one('.'+CSS.PANELROLES+' select').set('value', defaultrole);
                    break;
            }
        },
        hide : function() {
            this.fire('hide');
        },
        getCohorts : function() {
            Y.io(M.cfg.wwwroot+this.get(AJAXURL), {
                method:'POST',
                data:'id='+this.get(COURSEID)+'&action=getcohorts&sesskey='+M.cfg.sesskey,
                on: {
                    complete: function(tid, outcome, args) {
                        try {
                            var cohorts = Y.JSON.parse(outcome.responseText);
                            if (cohorts.error) {
                                new M.core.ajaxException(cohorts);
                            } else {
                                this.setCohorts(cohorts.response);
                            }
                        } catch (e) {
                            return new M.core.exception(e);
                        }
                        this.getCohorts = function() {
                            this.fire('cohortsloaded');
                        };
                        this.getCohorts();
                    }
                },
                context:this
            });
        },
        setCohorts : function(rawcohorts) {
            var cohorts = [], i=0;
            for (i in rawcohorts) {
                cohorts[rawcohorts[i].cohortid] = new COHORT(rawcohorts[i]);
            }
            this.set(COHORTS, cohorts);
        },
        getAssignableRoles : function() {
            Y.io(M.cfg.wwwroot+this.get(AJAXURL), {
                method:'POST',
                data:'id='+this.get(COURSEID)+'&action=getassignable&sesskey='+M.cfg.sesskey,
                on: {
                    complete: function(tid, outcome, args) {
                        try {
                            var roles = Y.JSON.parse(outcome.responseText);
                            this.set(ASSIGNABLEROLES, roles.response);
                        } catch (e) {
                            return new M.core.exception(e);
                        }
                        this.getAssignableRoles = function() {
                            this.fire('assignablerolesloaded');
                        };
                        this.getAssignableRoles();
                    }
                },
                context:this
            });
        },
        getDefaultCohortRole : function() {
            Y.io(M.cfg.wwwroot+this.get(AJAXURL), {
                method:'POST',
                data:'id='+this.get(COURSEID)+'&action=getdefaultcohortrole&sesskey='+M.cfg.sesskey,
                on: {
                    complete: function(tid, outcome, args) {
                        try {
                            var roles = Y.JSON.parse(outcome.responseText);
                            this.set(DEFAULTCOHORTROLE, roles.response);
                        } catch (e) {
                            return new M.core.exception(e);
                        }
                        this.fire('defaultcohortroleloaded');
                    }
                },
                context:this
            });
        },
        enrolCohort : function(e, cohort, node, usersonly) {
            if (this._preformingAction) {
                return true;
            }
            this._preformingAction = true;
            this.fire('performingaction');
            var params = {
                id : this.get(COURSEID),
                roleid : node.one('.'+CSS.PANELROLES+' select').get('value'),
                cohortid : cohort.get(COHORTID),
                action : (usersonly)?'enrolcohortusers':'enrolcohort',
                sesskey : M.cfg.sesskey
            };
            Y.io(M.cfg.wwwroot+this.get(AJAXURL), {
                method:'POST',
                data:build_querystring(params),
                on: {
                    complete: function(tid, outcome, args) {
                        try {
                            var result = Y.JSON.parse(outcome.responseText);
                            if (result.error) {
                                new M.core.ajaxException(result);
                            } else {
                                var redirecturl = this.get(URL), redirect = function() {
                                    if (!usersonly || result.response.users) {
                                        Y.one(document.body).append(
                                            Y.Node.create('<div class="corelightbox"></div>')
                                                .setStyle('height', Y.one(document.body).get('docHeight')+'px')
                                                .setStyle('opacity', '0.4')
                                                .append(Y.Node.create('<img alt="loading" />').setAttribute('src', M.cfg.loadingicon)));
                                        window.location.href = redirecturl;
                                    }
                                };
                                if (result.response && result.response.message) {
                                    new M.core.alert(result.response).on('complete', redirect, this);
                                } else {
                                    redirect();
                                }
                            }
                            this._preformingAction = false;
                            this.fire('actioncomplete');
                        } catch (e) {
                            new M.core.exception(e);
                        }
                    }
                },
                context:this
            });
            return true;
        }
    };
    Y.extend(CONTROLLER, Y.Base, CONTROLLER.prototype, {
        NAME : CONTROLLERNAME,
        ATTRS : {
            url : {
                validator : Y.Lang.isString
            },
            ajaxurl : {
                validator : Y.Lang.isString
            },
            courseid : {
                value : null
            },
            cohorts : {
                validator : Y.Lang.isArray,
                value : null
            },
            assignableRoles : {
                value : null
            },
            manualEnrolment : {
                value : false
            },
            defaultCohortRole : {
                value : null
            }
        }
    });
    Y.augment(CONTROLLER, Y.EventTarget);

    var COHORT = function(config) {
        COHORT.superclass.constructor.apply(this, arguments);
    };
    Y.extend(COHORT, Y.Base, {
        toHTML : function(supportmanualenrolment){
            var button, result, name, users, syncbutton, usersbutton;
            result = Y.Node.create('<div class="'+CSS.COHORT+'"></div>');
            if (this.get(ENROLLED)) {
                button = Y.Node.create('<div class="'+CSS.COHORTBUTTON+' alreadyenrolled">'+M.str.enrol.synced+'</div>');
            } else {
                button = Y.Node.create('<div></div>');

                syncbutton = Y.Node.create('<a class="'+CSS.COHORTBUTTON+' notenrolled enrolcohort">'+M.str.enrol.enrolcohort+'</a>');
                syncbutton.on('click', function(){this.fire('enrolchort');}, this);
                button.append(syncbutton);

                if (supportmanualenrolment) {
                    usersbutton = Y.Node.create('<a class="'+CSS.COHORTBUTTON+' notenrolled enrolusers">'+M.str.enrol.enrolcohortusers+'</a>');
                    usersbutton.on('click', function(){this.fire('enrolusers');}, this);
                    button.append(usersbutton);
                }
            }
            name = Y.Node.create('<div class="'+CSS.COHORTNAME+'">'+this.get(NAME)+'</div>');
            users = Y.Node.create('<div class="'+CSS.COHORTUSERS+'">'+this.get(USERS)+'</div>');
            if (this.get(ENROLLED)) {
                button.one(CSS.COHORTENROLLED);
            }
            return result.append(button).append(name).append(users);
        }
    }, {
        NAME : COHORTNAME,
        ATTRS : {
            cohortid : {

            },
            name : {
                validator : Y.Lang.isString
            },
            enrolled : {
                value : false
            },
            users : {
                value : 0
            }
        }
    });
    Y.augment(COHORT, Y.EventTarget);

    M.enrol_cohort = M.enrol || {};
    M.enrol_cohort.quickenrolment = {
        init : function(cfg) {
            new CONTROLLER(cfg);
        }
    }

}, '@VERSION@', {requires:['base','node', 'overlay', 'io', 'test', 'json-parse', 'event-delegate', 'dd-plugin', 'event-key', 'moodle-enrol-notification']});