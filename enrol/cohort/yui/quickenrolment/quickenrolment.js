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
        MORERESULTS = 'moreresults',
        FIRSTPAGE = 'firstpage',
        OFFSET = 'offset',
        PANELID = 'qce-panel-',
        REQUIREREFRESH = 'requiresRefresh',
        SEARCH = 'search',
        URL = 'url',
        AJAXURL = 'ajaxurl',
        MANUALENROLMENT = 'manualEnrolment',
        CSS = {
            CLOSEBTN : 'close-button',
            COHORT : 'qce-cohort',
            COHORTS : 'qce-cohorts',
            COHORTBUTTON : 'qce-cohort-button',
            COHORTENROLLED : 'qce-cohort-enrolled',
            COHORTNAME : 'qce-cohort-name',
            COHORTUSERS : 'qce-cohort-users',
            ENROLUSERS : 'canenrolusers',
            FOOTER : 'qce-footer',
            HIDDEN : 'hidden',
            LIGHTBOX : 'qce-loading-lightbox',
            LOADINGICON : 'loading-icon',
            MORERESULTS : 'qce-more-results',
            PANEL : 'qce-panel',
            PANELCONTENT : 'qce-panel-content',
            PANELCOHORTS : 'qce-enrollable-cohorts',
            PANELROLES : 'qce-assignable-roles',
            PANELCONTROLS : 'qce-panel-controls',
            SEARCH : 'qce-search'
        },
        COUNT = 0;


    var CONTROLLER = function(config) {
        CONTROLLER.superclass.constructor.apply(this, arguments);
    };
    CONTROLLER.prototype = {
        initializer : function(config) {
            COUNT ++;
            this.publish('assignablerolesloaded', {fireOnce:true});
            this.publish('cohortsloaded');
            this.publish('defaultcohortroleloaded', {fireOnce:true});

            var finishbutton = Y.Node.create('<div class="'+CSS.CLOSEBTN+'"></div>')
                                   .append(Y.Node.create('<input type="button" value="'+M.str.enrol.finishenrollingusers+'" />'));
            var base = Y.Node.create('<div class="'+CSS.PANELCONTENT+'"></div>')
                .append(Y.Node.create('<div class="'+CSS.PANELROLES+'"></div>'))
                .append(Y.Node.create('<div class="'+CSS.PANELCOHORTS+'"></div>'))
                .append(Y.Node.create('<div class="'+CSS.FOOTER+'"></div>')
                    .append(Y.Node.create('<div class="'+CSS.SEARCH+'"><label for="enrolcohortsearch">'+M.str.enrol_cohort.cohortsearch+':</label></div>')
                        .append(Y.Node.create('<input type="text" id="enrolcohortsearch" value="" />'))
                    )
                    .append(finishbutton)
                )
                .append(Y.Node.create('<div class="'+CSS.LIGHTBOX+' '+CSS.HIDDEN+'"></div>')
                    .append(Y.Node.create('<img alt="loading" class="'+CSS.LOADINGICON+'" />')
                        .setAttribute('src', M.util.image_url('i/loading', 'moodle')))
                    .setStyle('opacity', 0.5)
                );

            var close = Y.Node.create('<div class="close"></div>');
            var panel = new Y.Overlay({
                headerContent : Y.Node.create('<div></div>').append(Y.Node.create('<h2>'+M.str.enrol.enrolcohort+'</h2>')).append(close),
                bodyContent : base,
                constrain : true,
                centered : true,
                id : PANELID+COUNT,
                visible : false
            });

            // display the wheel on ajax events
            Y.on('io:start', function() {
                base.one('.'+CSS.LIGHTBOX).removeClass(CSS.HIDDEN);
            }, this);
            Y.on('io:end', function() {
                base.one('.'+CSS.LIGHTBOX).addClass(CSS.HIDDEN);
            }, this);

            this.set(SEARCH, base.one('#enrolcohortsearch'));
            Y.on('key', this.getCohorts, this.get(SEARCH), 'down:13', this, false);

            panel.get('boundingBox').addClass(CSS.PANEL);
            panel.render(Y.one(document.body));
            this.on('show', function(){
                this.set('centered', true);
                this.show();
            }, panel);
            this.on('hide', panel.hide, panel);
            this.on('assignablerolesloaded', this.updateContent, this, panel);
            this.on('cohortsloaded', this.updateContent, this, panel);
            this.on('defaultcohortroleloaded', this.updateContent, this, panel);
            Y.on('key', this.hide, document.body, 'down:27', this);
            close.on('click', this.hide, this);
            finishbutton.on('click', this.hide, this);

            Y.all('.enrol_cohort_plugin input').each(function(node){
                if (node.getAttribute('type', 'submit')) {
                    node.on('click', this.show, this);
                }
            }, this);

            base = panel.get('boundingBox');
            base.plug(Y.Plugin.Drag);
            base.dd.addHandle('.yui3-widget-hd h2');
            base.one('.yui3-widget-hd h2').setStyle('cursor', 'move');
        },
        show : function(e) {
            e.preventDefault();
            // prepare the data and display the window
            this.getCohorts(e, false);
            this.getAssignableRoles();
            this.fire('show');

            var rolesselect = Y.one('#id_enrol_cohort_assignable_roles');
            if (rolesselect) {
                rolesselect.focus();
            }
        },
        updateContent : function(e, panel) {
            var content, i, roles, cohorts, count=0, supportmanual = this.get(MANUALENROLMENT), defaultrole;
            switch (e.type.replace(/^[^:]+:/, '')) {
                case 'cohortsloaded' :
                    if (this.get(FIRSTPAGE)) {
                        // we are on the page 0, create new element for cohorts list
                        content = Y.Node.create('<div class="'+CSS.COHORTS+'"></div>');
                        if (supportmanual) {
                            content.addClass(CSS.ENROLUSERS);
                        }
                    } else {
                        // we are adding cohorts to existing list
                        content = Y.Node.one('.'+CSS.PANELCOHORTS+' .'+CSS.COHORTS);
                        content.one('.'+CSS.MORERESULTS).remove();
                    }
                    // add cohorts items to the content
                    cohorts = this.get(COHORTS);
                    for (i in cohorts) {
                        count++;
                        cohorts[i].on('enrolchort', this.enrolCohort, this, cohorts[i], panel.get('contentBox'), false);
                        cohorts[i].on('enrolusers', this.enrolCohort, this, cohorts[i], panel.get('contentBox'), true);
                        content.append(cohorts[i].toHTML(supportmanual).addClass((count%2)?'even':'odd'));
                    }
                    // add the next link if there are more items expected
                    if (this.get(MORERESULTS)) {
                        var fetchmore = Y.Node.create('<div class="'+CSS.MORERESULTS+'"><a href="#">'+M.str.enrol_cohort.ajaxmore+'</a></div>');
                        fetchmore.on('click', this.getCohorts, this, true);
                        content.append(fetchmore);
                    }
                    // finally assing the content to the block
                    if (this.get(FIRSTPAGE)) {
                        panel.get('contentBox').one('.'+CSS.PANELCOHORTS).setContent(content);
                    }
                    break;
                case 'assignablerolesloaded':
                    roles = this.get(ASSIGNABLEROLES);
                    content = Y.Node.create('<select id="id_enrol_cohort_assignable_roles"></select>');
                    for (i in roles) {
                        content.append(Y.Node.create('<option value="'+i+'">'+roles[i]+'</option>'));
                    }
                    panel.get('contentBox').one('.'+CSS.PANELROLES).setContent(Y.Node.create('<div><label for="id_enrol_cohort_assignable_roles">'+M.str.role.assignroles+':</label></div>').append(content));

                    this.getDefaultCohortRole();
                    Y.one('#id_enrol_cohort_assignable_roles').focus();
                    break;
                case 'defaultcohortroleloaded':
                    defaultrole = this.get(DEFAULTCOHORTROLE);
                    panel.get('contentBox').one('.'+CSS.PANELROLES+' select').set('value', defaultrole);
                    break;
            }
        },
        hide : function() {
            if (this.get(REQUIREREFRESH)) {
                window.location = this.get(URL);
            }
            this.fire('hide');
        },
        getCohorts : function(e, append) {
            if (e) {
                e.preventDefault();
            }
            if (append) {
                this.set(FIRSTPAGE, false);
            } else {
                this.set(FIRSTPAGE, true);
                this.set(OFFSET, 0);
            }
            var params = [];
            params['id'] = this.get(COURSEID);
            params['offset'] = this.get(OFFSET);
            params['search'] = this.get(SEARCH).get('value');
            params['action'] = 'getcohorts';
            params['sesskey'] = M.cfg.sesskey;

            Y.io(M.cfg.wwwroot+this.get(AJAXURL), {
                method:'POST',
                data:build_querystring(params),
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
                        this.fire('cohortsloaded');
                    }
                },
                context:this
            });
        },
        setCohorts : function(response) {
            this.set(MORERESULTS, response.more);
            this.set(OFFSET, response.offset);
            var rawcohorts = response.cohorts;
            var cohorts = [], i=0;
            for (i in rawcohorts) {
                cohorts[i] = new COHORT(rawcohorts[i]);
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
                                if (result.response && result.response.message) {
                                    var alertpanel = new M.core.alert(result.response);
                                    Y.Node.one('#id_yuialertconfirm-' + alertpanel.COUNT).focus();
                                }
                                var enrolled = Y.Node.create('<div class="'+CSS.COHORTBUTTON+' alreadyenrolled">'+M.str.enrol.synced+'</div>');
                                node.one('.'+CSS.COHORT+' #cohortid_'+cohort.get(COHORTID)).replace(enrolled);
                                this.set(REQUIREREFRESH, true);
                            }
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
            },
            requiresRefresh : {
                value : false,
                validator : Y.Lang.isBool
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
                button = Y.Node.create('<div id="cohortid_'+this.get(COHORTID)+'"></div>');

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

}, '@VERSION@', {requires:['base','node', 'overlay', 'io-base', 'test', 'json-parse', 'event-delegate', 'dd-plugin', 'event-key', 'moodle-core-notification']});
