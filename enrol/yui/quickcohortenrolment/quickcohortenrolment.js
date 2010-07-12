YUI.add('moodle-enrol-quickcohortenrolment', function(Y) {

    var CONTROLLERNAME = 'Quick cohort enrolment controller',
        COHORTNAME = 'Cohort',
        COHORTID = 'cohortid',
        ENROLLED = 'enrolled',
        NAME = 'name',
        USERS = 'users',
        COURSEID = 'courseid',
        ASSIGNABLEROLES = 'assignableRoles',
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
    }
    Y.extend(CONTROLLER, Y.Base, {
        initializer : function(config) {
            COUNT ++;
            this.publish('assignablerolesloaded');
            this.publish('cohortsloaded');
            
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
            this.on('show', panel.show, panel);
            this.on('hide', function() {
                this.set('bodyContent', Y.Node.create('<div class="loading"></div>').append(Y.Node.create('<img alt="loading" />').setAttribute('src', M.cfg.loadingicon)));
                this.hide();
            }, panel);
            this.on('assignablerolesloaded', this.updateContent, this, panel);
            this.on('cohortsloaded', this.updateContent, this, panel);
            close.on('click', this.hide, this);

            Y.all('.enrolcohortbutton input').each(function(node){
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
            var content, i, roles, cohorts, count=0, supportmanual = this.get(MANUALENROLMENT);
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
                    panel.get('contentBox').one('.'+CSS.PANELCOHORTS).append(content);
                    break;
                case 'assignablerolesloaded':
                    roles = this.get(ASSIGNABLEROLES);
                    content = Y.Node.create('<select></select>');
                    for (i in roles) {
                        content.append(Y.Node.create('<option value="'+i+'">'+roles[i]+'</option>'));
                    }
                    panel.get('contentBox').one('.'+CSS.PANELROLES).setContent(Y.Node.create('<div>'+M.str.role.assignroles+': </div>').append(content));
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
                            this.setCohorts(cohorts.response);
                        } catch (e) {
                            Y.fail(CONTROLLERNAME+': Failed to load cohorts');
                        }
                        this.getCohorts = function() {
                            this.fire('cohortsloaded');
                        }
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
                            Y.fail(CONTROLLERNAME+': Failed to load assignable roles');
                        }
                        this.getAssignableRoles = function() {
                            this.fire('assignablerolesloaded');
                        }
                        this.getAssignableRoles();
                    }
                },
                context:this
            });
        },
        enrolCohort : function(e, cohort, node, usersonly) {
            var params = {
                id : this.get(COURSEID),
                roleid : node.one('.'+CSS.PANELROLES+' select').get('value'),
                cohortid : cohort.get(COHORTID),
                action : (usersonly)?'enrolcohortusers':'enrolcohort',
                sesskey : M.cfg.sesskey
            }
            Y.io(M.cfg.wwwroot+this.get(AJAXURL), {
                method:'POST',
                data:build_querystring(params),
                on: {
                    complete: function(tid, outcome, args) {
                        try {
                            var result = Y.JSON.parse(outcome.responseText);
                            if (result.success) {
                                if (result.response && result.response.message) {
                                    alert(result.response.message);
                                }
                                if (result.response.users) {
                                    window.location.href = this.get(URL);
                                }
                            } else {
                                alert('Failed to enrol cohort');
                            }
                        } catch (e) {
                            Y.fail(CONTROLLERNAME+': Failed to enrol cohort');
                        }
                    }
                },
                context:this
            });
        }
    }, {
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
            }
        }
    });
    Y.augment(CONTROLLER, Y.EventTarget);

    var COHORT = function(config) {
        COHORT.superclass.constructor.apply(this, arguments);
    }
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

    M.enrol = M.enrol || {};
    M.enrol.quickcohortenrolment = {
        init : function(cfg) {
            new CONTROLLER(cfg);
        }
    }

}, '@VERSION@', {requires:['base','node', 'overlay', 'io', 'test', 'json-parse', 'event-delegate', 'dd-plugin', 'event-key']});