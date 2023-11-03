define(['jquery',
    'core/ajax',
    'block_learnerscript/ajax',
    'core/event',
    'block_learnerscript/select2',
    'block_learnerscript/report',
    'core/str'
], function($, Ajax, ajax, Event, select2, report,Str) {

    return schedule = {
        /**
         * To display scheduled timings for report
         */
        /**
         * Schedule report form in popup in dashboard
         * @param  object args reportid
         * @return Popup with schedule form
         */
        schreportform: function(args) {
            var promise = Ajax.call([{
                methodname: 'block_learnerscript_schreportform',
                args: {
                    // action: 'schreportform',
                    reportid: args.reportid,
                    instance: args.instanceid
                },
                // url: M.cfg.wwwroot + "/blocks/learnerscript/ajax.php"
            }]);
            promise[0].done(function(response) {
                response = $.parseJSON(response);
                $('body').append("<div class='schreportform" + args.instanceid + "'>" + response + "</div>");
                var title_img = "<img class='dialog_title_icon' alt='Scheduled Report' src='" +
                    M.util.image_url("schedule_icon", "block_learnerscript") + "'/>";
                var dlg = $(".schreportform" + args.instanceid).dialog({
                    resizable: true,
                    autoOpen: false,
                    width: "90%",
                    title: 'Add schedule report',
                    modal: true,
                    appendTo: "#inst" + args.instanceid,
                    position: {
                        my: "center",
                        at: "center",
                        of: "#reportcontainer" + args.instanceid
                    },
                    open: function(event, ui) {
                        $(this).closest(".ui-dialog").find(".ui-dialog-titlebar-close")
                            .removeClass("ui-dialog-titlebar-close")
                            .html("<span class='ui-button-icon-primary ui-icon ui-icon-closethick'></span>");
                            var Closebutton = $('.ui-icon-closethick').parent();
                            $(Closebutton).attr({
                                "title" : "Close"
                            });
                        $(this).closest(".ui-dialog").find('.ui-dialog-title')
                            .html(title_img + 'Schedule report');
                    },
                    close: function(event, ui) {
                        $(this).dialog('destroy').remove();
                    }
                });
                dlg.dialog("open");
                $("#id_role" + args.instanceid).select2();
                this.SelectRoleUsers();
                this.schformvalidation({
                    reportid: args.reportid,
                    form: 'schform' + args.instanceid,
                    reqclass: 'schformreq' + args.instanceid,
                    instanceid: args.instanceid
                });
            }).fail(function(ex) {
                // do something with the exception
                //  console.log(ex);
            });
        },
        ScheduledTimings: function(args) {
            $("select[data-select2='1']").select2({
                theme: "classic"
            });
            this.SelectRoleUsers();
            $('#scheduledtimings').dataTable({
                "processing": true,
                "serverSide": true,
                "lengthMenu": [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "All"]
                ],
                "idisplaylength": 5,
                'ordering': false,
                "ajax": {
                    "method": "GET",
                    "url": M.cfg.wwwroot + "/blocks/learnerscript/components/scheduler/ajax.php",
                    "data": args
                }
            });
        },
        ViewSchUsersTable: function(args) {
            $('#scheduledusers').dataTable({
                "processing": true,
                "serverSide": true,
                "lengthMenu": [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "All"]
                ],
                "idisplaylength": 5,
                'ordering': false,
                "ajax": {
                    "method": "GET",
                    "url": M.cfg.wwwroot + "/blocks/learnerscript/components/scheduler/ajax.php",
                    "data": {
                        action: 'viewschusersdata',
                        reportid: args.reportid,
                        scheduleid: args.scheduleid,
                        schuserslist: args.schuserslist
                    }
                }
            });
        },
        /**
         * Add validation script to AJAX reponse MOODLE form
         * @param  object args Form name and required classname
         * @return {[type]}      [description]
         */
        schformvalidation: function(args) {
            document.getElementById(args.form).addEventListener('submit', function(ev) {
                try {
                    var myValidator = report.validate_scheduled_reports_form(args);
                } catch (e) {
                    return true;
                }
                if (typeof window.tinyMCE !== 'undefined') {
                    window.tinyMCE.triggerSave();
                }
                if (!myValidator) {
                    ev.preventDefault();
                }
                return false;
            });
        },
        /**
         * Validates and return error message for each element of given form
         * @param  object args Formname and required classname
         * @return Returns list of error messages from validation
         */
        validate_scheduled_reports_form: function(args) {
            var self = this;
            if (skipClientValidation) {
                $('.schreportform' + args.instanceid).dialog('destroy').remove();
            }
            var ret = true;
            var frm = document.getElementById(args.form);
            var first_focus = false;
            $("[data-class='" + args.reqclass + "']").each(function(index, value) {
                var element = $(value).data('element');
                ret = self.validate_scheduled_reports_form_element(value, element, args) && ret;
                if (!ret && !first_focus) {
                    first_focus = true;
                    Y.use('moodle-core-event', function() {
                        Y.Global.fire(M.core.globalEvents.FORM_ERROR, {
                            formid: args.form,
                            elementid: 'id_error_' + element + args.instanceid
                        });
                        document.getElementById('id_error_' + element + args.instanceid).focus();
                    });
                }
            });
            return ret;
        },
        /**
         * Format error message string for each element
         * @param  object element Element object
         * @param  string escapedName Element name
         * @return Formatted error messages for each element
         */
        validate_scheduled_reports_form_element: function(element, escapedName, args) {
            if (undefined == element) {
                //required element was not found, then let form be submitted without client side validation
                return true;
            }
            var value = '';
            var errFlag = new Array();
            var _qfGroups = {};
            var _qfMsg = '';
            var frm = element.parentNode;
            if ((undefined != element.name) && (frm != undefined)) {
                while (frm && frm.nodeName.toUpperCase() != 'FORM') {
                    frm = frm.parentNode;
                }
                value = new Array();
                var valueIdx = 0;
                for (var i = 0; i < element.options.length; i++) {
                    if (element.options[i].selected) {
                        value[valueIdx++] = element.options[i].value;
                    }
                }
                if (value == '' && !errFlag[escapedName]) {
                    errFlag[escapedName] = true;
                    _qfMsg = _qfMsg + ' - You must supply a value here.';
                }
                return this.qf_errorHandler(element, _qfMsg, escapedName, args);
            } else {
                //element name should be defined else error msg will not be displayed.
                return true;
            }
        },
        /**
         * Render and display error message for each element
         * @param  object element  Element object
         * @param  string _qfMsg  Error message
         * @param  string escapedName Element name
         * @return Render and display error message for each element
         */
        qf_errorHandler: function(element, _qfMsg, escapedName, args) {
            var event = $.Event(Event.Events.FORM_FIELD_VALIDATION);
            $(element).trigger(event, _qfMsg);
            if (event.isDefaultPrevented()) {
                return _qfMsg == '';
            } else {
                // Legacy mforms.
                var div = element.parentNode;
                if ((div == undefined) || (element.name == undefined)) {
                    // No checking can be done for undefined elements so let server handle it.
                    return true;
                }
                if (_qfMsg != '') {
                    var errorSpan = document.getElementById('id_error_' + escapedName + args.instanceid);
                    if (!errorSpan) {
                        errorSpan = document.createElement('span');
                        errorSpan.id = 'id_error_' + escapedName + args.instanceid;
                        errorSpan.className = 'error';
                        element.parentNode.insertBefore(errorSpan, element.parentNode.firstChild);
                        document.getElementById(errorSpan.id).setAttribute('TabIndex', '0');
                        document.getElementById(errorSpan.id).focus();
                    }
                    while (errorSpan.firstChild) {
                        errorSpan.removeChild(errorSpan.firstChild);
                    }
                    errorSpan.appendChild(document.createTextNode(_qfMsg.substring(3)));
                    if (div.className.substr(div.className.length - 6, 6) != ' error' && div.className != 'error') {
                        div.className += ' error';
                        linebreak = document.createElement('br');
                        linebreak.className = 'error';
                        linebreak.id = 'id_error_break_' + escapedName + args.instanceid;
                        errorSpan.parentNode.insertBefore(linebreak, errorSpan.nextSibling);
                    }
                    return false;
                } else {
                    var errorSpan = document.getElementById('id_error_' + escapedName + args.instanceid);
                    if (errorSpan) {
                        errorSpan.parentNode.removeChild(errorSpan);
                    }
                    var linebreak = document.getElementById('id_error_break_' + escapedName + args.instanceid);
                    if (linebreak) {
                        linebreak.parentNode.removeChild(linebreak);
                    }
                    if (div.className.substr(div.className.length - 6, 6) == ' error') {
                        div.className = div.className.substr(0, div.className.length - 6);
                    } else if (div.className == 'error') {
                        div.className = '';
                    }
                    return true;
                }
            }
        },
        frequency_schedule: function(args) {
            var promise = Ajax.call([{
                methodname: 'block_learnerscript_frequency_schedule',
                args: {
                    frequency: $("#id_frequency" + args.reportinstance).val()
                    // action: 'frequency_schedule'
                }
            }]);
            promise[0].done(function(resp) {
                resp = $.parseJSON(resp);
                var template = '';
                if (resp) {
                    $.each(resp, function(index, value) {
                        template += '<option value = ' + index + ' >' + value + '</option>';
                    });
                } else {
                    template += '<option value=null > --SELECT-- </option>';
                }
                $("#id_updatefrequency" + args.reportinstance).html(template);
            }).fail(function(ex) {
                // do something with the exception
                //  console.log(ex);
            });
        },
        /**
         * Manage more users to schedule report
         * @param  object args reportid,scheduleid,selectedroleid and userslist
         * @return Display popup with manage users for scheduled/Scheduling report
         */
        manageschusers: function(args) {
            Str.get_string('manageschusers','block_learnerscript').then(function(s) {
            var promise = Ajax.call([{
                methodname: 'block_learnerscript_manageschusers',
                args: {
                    // action: 'manageschusers',
                    reportid: args.reportid,
                    scheduleid: args.scheduleid,
                    selectedroleid: JSON.stringify(args.selectedroleid),
                    // selectedroleid: args.selectedroleid,
                    schuserslist: args.schuserslist,
                    reportinstance: args.reportinstance
                }
                // url: M.cfg.wwwroot + "/blocks/learnerscript/ajax.php"
            }]);

            promise[0].done(function(response) {
                response = $.parseJSON(response);
                $('body').append("<div class='manageschusers'>" + response + "</div>");
                var bodyattb = $('body').attr('id');
                var position, my, at;
                if(bodyattb == 'page-blocks-learnerscript-components-scheduler-schedule'){
                    position = '#scheduleform';
                    my = "center top";
                    at = "center top";
                }else{
                    position = window;
                    my = "center";
                    at = "center";
                    var cid = $(".manageschusers").addClass('notschuserspage');
                }                       
                var dlg = $(".manageschusers").dialog({
                    resizable: true,
                    autoOpen: false,
                    width: "60%",
                    title: 'Manage Schedule Users',
                    modal: true,
                    position: {
                        my: my,
                        at: at,
                        of: position,
                        within: position
                    },
                    close: function(event, ui) {
                        $(this).dialog('destroy').remove();
                    },
                    open: function() {
                        $(this).closest(".ui-dialog")
                            .find(".ui-dialog-titlebar-close")
                            .removeClass("ui-dialog-titlebar-close")
                            .html("<span class='ui-button-icon-primary ui-icon ui-icon-closethick'></span>");
                            var Closebutton = $('.ui-icon-closethick').parent();
                            $(Closebutton).attr({
                                "title" : "Close"
                            });
                        // $(this).closest(".ui-dialog")
                        //     .find('.ui-dialog-title').html(title_img + self.args.title);
                    }
                });
                if($(".manageschusers").hasClass("notschuserspage")){
                    var parentdialog = $(".manageschusers").parent();
                    parentdialog.addClass('notinschpage');
                }
                // dlg.closest(".ui-dialog").draggable({containment: "parent"});
                $(".selectrole" + args.reportid).select2();
                dlg.dialog("open");
            }).fail(function(ex) {
                // do something with the exception
                //  console.log(ex);
            });
        });
        },
        /**
         * Get users for selected roles in bulk user form while searching
         * @param  object args reportid and selected roles
         * @return object List of users for selected roles
         */
        getroleusers: function(args) {
            var roles = $('.selectrole' + args.reportid).val();
            this.validate_scheduled_reports_form({
                reportid: args.reportid,
                form: 'assignform',
                reqclass: 'rolereq'
            });
            var bullkselectedusers = $('.removeselect').val();
            if ($('#addselect_searchtext').val().length < 1) {
                template = "<optgroup label='Enter a value in search.'></optgroup>";
                $('#' + args.type + 'select' + args.reportid).html(template);
            } else {
                var roleid = roles.split('_');
                args.roleid = roleid[0];
                args.contextlevel = roleid[1];
                var promise = Ajax.call([{
                    methodname: 'block_learnerscript_roleusers',
                    args: {
                        // action: 'roleusers',
                        term: $('#addselect_searchtext').val(),
                        type: args.type,
                        reportid: args.reportid,
                        roleid: args.roleid,
                        contextlevel: args.contextlevel,
                        scheduleid: args.scheduleid,
                        bullkselectedusers: JSON.stringify(bullkselectedusers)
                    },
                    // url: M.cfg.wwwroot + "/blocks/learnerscript/ajax.php"
                }]);
                promise[0].done(function(response) {
                    var template = '';
                    response = $.parseJSON(response);
                    if (response.total_count > 0) {
                        $.each(response.items, function(index, value) {
                            template += '<option value = ' + value.id + ' >' + value.fullname + '</option>';
                        });
                    } else {
                        template += "<optgroup label='No results found.'></optgroup>";
                    }
                    $('#' + args.type + 'select' + args.reportid).html(template);
                }).fail(function(ex) {
                    // do something with the exception
                    // console.log(ex);
                });
            }
        },
        /**
         * Add/remove bulk users to schedule reports
         * @param  object args reportid
         * @return Add/remove users to schedule report form
         */
        bulkmanageschusers: function(args) {
            var bullkselectedusers = $.map($('.removeselect option'), function(e) {
                return e.value;
            });
            $('#schuserslist' + args.reportinstance).val(bullkselectedusers);
            var selectedusers = $('.removeselect').find('option').clone();
            $('#id_users_data' + args.reportinstance).children('option').remove();
            if (selectedusers.length > 10) {
                var tenusers = selectedusers.slice(0, 10);
                tenusers.attr('selected', 'selected').appendTo('#id_users_data' + args.reportinstance);
                var opt = document.createElement('option');
                opt.value = '-1';
                opt.innerHTML = 'View More';
                opt.selected = true;
                document.getElementById('id_users_data' + args.reportinstance).appendChild(opt);
            } else {
                $('.removeselect').find('option').clone().attr('selected', 'selected').appendTo('#id_users_data' + args.reportinstance);
            }
            $('.manageschusers').dialog('close');
        },
        /**
         * Preview selected users to schedule report
         * @param  object args reportid,scheduleid and userslist
         * @return Preview users in dialog
         */
        viewschusers: function(args) {
            Str.get_string('viewschusers','block_learnerscript').then(function(s) {
                var self = this;
                args.schuserslist = $('#schuserslist' + args.reportinstance).val();
                var promise = ajax.call({
                    methodname: 'viewschuserstable',
                    args: {
                        action: 'viewschuserstable',
                        reportid: args.reportid,
                        scheduleid: args.scheduleid,
                        schuserslist: args.schuserslist
                    },
                    url: M.cfg.wwwroot + "/blocks/learnerscript/ajax.php",
                });
                promise.done(function(response) {
                    $('body').append("<div class='viewschuserstable'>" + response + "</div>");
                    var dlg = $(".viewschuserstable").dialog({
                        resizable: true,
                        autoOpen: false,
                        width: "60%",
                        title: 'View Scheduled Users',
                        modal: true,
                        close: function(event, ui) {
                            $(this).dialog('destroy').remove();
                        },
                        open: function() {
                            $(this).closest(".ui-dialog")
                                .find(".ui-dialog-titlebar-close")
                                .removeClass("ui-dialog-titlebar-close")
                                .html("<span class='ui-button-icon-primary ui-icon ui-icon-closethick'></span>");
                                var Closebutton = $('.ui-icon-closethick').parent();
                                $(Closebutton).attr({
                                    "title" : "Close"
                                });
                            // $(this).closest(".ui-dialog")
                            //     .find('.ui-dialog-title').html(title_img + self.args.title);
                        }
                    });
                    schedule.ViewSchUsersTable(args);
                    dlg.dialog("open").prev(".ui-dialog-titlebar").css("color", "#0C75B6");
                }).fail(function(ex) {
                    // do something with the exception
                    //console.log(ex);
                });
            });
        },
        /**
         * Add users to schedule report
         * @param  object args reportid
         * @return Adds users to schedule report form
         */
        addschusers: function(args) {
            var selschusers = $('.schforms' + args.reportinstance + ' #id_users_data' + args.reportinstance).val();
            var selusers = $('.schforms' + args.reportinstance + ' #schuserslist' + args.reportinstance).val();
            var total;
            if (selusers) {
                selusers = selusers.split(',');
                total = selusers.concat(selschusers);
            } else {
                total = selschusers;
            }
            if (total && total.includes('-1')) {
                var index = total.indexOf('-1');
                total.splice(index, 1);
            }
            $('#id_users_data' + args.reportinstance).find('option').not(':selected').each(function(k, v) {
                if (total && total.includes(v.value)) {
                    var index = total.indexOf(v.value);
                    total.splice(index, 1);
                }
            });
            var d = total && total.filter(function(item, pos) {
                return total.indexOf(item) == pos;
            });
            $('#schuserslist' + args.reportinstance).val(d);
        },
        /**
         * Get roleusers for selected report
         */
        SelectRoleUsers: function() {
            var reportid = $(".schform").data('reportid');
            require(['block_learnerscript/helper'], function(helper) {
                helper.Select2Ajax({
                    reportid: reportid,
                    action: 'rolewiseusers',
                    maximumSelectionLength: 10
                });
            })
        },
        rolewiseusers: function(args) {
            $('#id_users_data' + args.reportinstance).val(null).trigger('change');
        }
    }
});