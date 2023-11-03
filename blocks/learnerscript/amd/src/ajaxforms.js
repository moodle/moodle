/**
 * Add a create new group modal to the page.
 *
 * @module     block_learnerscript/ajaxforms
 * @class      AjaxForms
 * @package    block_learnerscript
 * @copyright  2017 Mukka Arun Kumar <arun@eabyas.in>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery',
        'core/str',
        'core/modal_factory',
        'core/modal_events',
        'core/fragment',
        'block_learnerscript/ajax',
        'block_learnerscript/schedule',
        'block_learnerscript/select2',
        'core/yui',
        'core/templates',
        'core/modal',
        'block_learnerscript/helper',
        'jqueryui'
        ],
    function($, Str, ModalFactory, ModalEvents, Fragment, Ajax, schedule,select2, Y, Templates, Modal, helper) {

        /**
         * Constructor
         *
         * @param {Object} args Contains list of parameters.
         * @param {string} url Ajax request URL.
         *
         * Each call to init gets it's own instance of this class.
         */
        var AjaxForms = function(args, url) {
            this.args = args;
            this.contextid = 1;
            this.url = url;
            this.nodeContent = args.nodeContent || 'ajaxForm';
            this.init(this.args);
        };

        /**
         * @var {Modal} modal
         * @private
         */
        AjaxForms.prototype.modal = null;

        /**
         * @var {int} contextid
         * @private
         */
        AjaxForms.prototype.contextid = -1;

        /**
         * Initialise the class.
         *
         * @param {String} selector used to find triggers for the new group modal.
         * @private
         * @return {Promise}
         */
        AjaxForms.prototype.init = function(args) {
            var resp = this.getBody();
            var self = this;
            var dialogueclass = "";
            resp.done(function(data) {
                $('body').append("<div class='" + self.nodeContent + "'></div>");
                Templates.replaceNodeContents('.' + self.nodeContent, data.html, '');
                var title_img = '';
                var position = '#viewreport' + args.reportid;
                var width = '80%';
                var my = "center top";
                var at =  "center top";
                if (args.action == 'schreportform') {
                    dialogueclass = 'schreportform';
                    title_img = "<img class='dialog_title_icon' alt='Schedule' src='" +
                        M.util.image_url("schreportform", "block_learnerscript") + "' />";
                    position = "#inst" + args.instanceid;
                    width = '60%';
                    my = "center";
                    at =  "center";
                } else if (args.action == 'advancedcolumns') {
                    dialogueclass = 'advancedcolumns';
                    position = ".ls_components";                   
                    width = '60%';
                    my = "center top";
                    at =  "center top";
                } else if (args.action == 'sendreportemail') {
                    dialogueclass = 'sendreportemail';
                    title_img = "<img class='dialog_title_icon' alt='Email' src='" + M.util.image_url("email_icon", "block_learnerscript") + "'/>";
                    position = "#inst" + args.instanceid;
                    width = '60%';
                    my = "center";
                    at =  "center";
                }

                $('head').append(data.javascript);

                var dlg = $("." + self.nodeContent).dialog({
                    resizable: true,
                    autoOpen: false,
                    width: width,
                    dialogClass: dialogueclass,
                    // dialogClass: 'schedule-popup',
                    title: self.args.title,
                    modal: true,
                    appendTo: position,
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
                        $(this).closest(".ui-dialog")
                               .find('.ui-dialog-title').html(title_img + self.args.title);
                    }
                });
                if (args.action == 'schreportform') {
                    $("." + self.nodeContent + " select[data-select2='1']").select2();
                    schedule.SelectRoleUsers();
                } else if (args.action == 'sendreportemail') {
                    helper = require('block_learnerscript/helper');
                    helper.Select2Ajax({
                        'action': 'userlist',
                        'reportid': args.reportid,
                        'maximumSelectionLength': 0
                    });
                }
                $('.' + self.nodeContent + ' .mform').bind('submit', function(e) {
                    e.preventDefault();
                    return self.submitFormAjax(this);
                });

                if (args.action == 'plotforms' && (args.pname == 'bar' || args.pname == 'column' || args.pname == 'line' || args.pname == 'combination')) {
                    
                    $("." + self.nodeContent + " select[data-select2='1']").select2({
                        theme: "classic"
                    });
                    $(document).on('click','.fa-eye', function(e){
                        e.stopImmediatePropagation();
                        $(e.target).parents('.form-group').find('*').attr('disabled', true);
                        $(e.target).switchClass("fa-eye", "fa-eye-slash");
                        $(e.target).attr('title', 'Enable');
                    });
                    $(document).on('click','.fa-eye-slash', function(e){
                        e.stopImmediatePropagation();
                        $(e.target).parents('.form-group').find('*').removeAttr('disabled');
                        $(e.target).switchClass( "fa-eye-slash", "fa-eye");
                        $(e.target).attr('title', 'Disable');
                    });
                    $(document).on('change', '#id_calcs', function() {
                        var calcval = $(this).val();
                        if (calcval) {
                            $('#id_columnsort').prop("disabled", true);
                            $('#id_sorting').prop("disabled", true);
                            $('#id_limit').prop("disabled", true);
                        } else {
                            $('#id_columnsort').prop("disabled", false);
                            $('#id_sorting').prop("disabled", false);
                            $('#id_limit').prop("disabled", false);
                        }
                    });
                }

                dlg.dialog("open");
            }).fail(function(ex) {

            });

        };

        /**
         * @method getBody
         * @private
         * @return {Promise}
         */
        AjaxForms.prototype.getBody = function(formdata) {
            if (typeof formdata === "undefined") {
                formdata = null;
            } else {
                // Get the content of the modal.
                this.args.jsonformdata = JSON.stringify(formdata);
            }

            var promise = Ajax.call({
                args: this.args,
                url: this.url
            }, false);

            return promise;
        };

        /**
         * @method handleFormSubmissionResponse
         * @private
         * @return {Promise}
         */
        AjaxForms.prototype.handleFormSubmissionResponse = function(data) {
            var helper = require('block_learnerscript/helper');
            if (data.formerror) {
                Templates.replaceNodeContents('.' + this.nodeContent, data.html, '');
                $('head').append(data.javascript);

                var self = this;
                $('.' + this.nodeContent + ' .mform').bind('submit', function(e) {
                    e.preventDefault();
                    self.submitFormAjax(this);
                });
                if (this.args.action == 'schreportform') {
                    $("." + self.nodeContent+" select[data-select2='1']").select2();
                    schedule.SelectRoleUsers();
                } else if (this.args.action == 'sendreportemail') {
                    helper = require('block_learnerscript/helper');
                    helper.Select2Ajax({
                        'action': 'userlist',
                        'reportid': this.args.reportid,
                        'maximumSelectionLength': 0
                    });
                }else{
                    $("." + self.nodeContent + " select[data-select2='1']").select2({
                        theme: "classic"
                    });
                }
            } else {
                if (this.args.component == 'columns') {
                    var app = angular.element(".simpleDemo").scope();
                    var pluginname = this.args.advancedcolumn;
                    app.$apply(function() {
                        var plugindata = {};
                        plugindata.id = data.data.id;
                        delete data.data.id;
                        plugindata.pluginname = pluginname;
                        plugindata.pluginfullname = pluginname;
                        plugindata.summary = '';
                        plugindata.type = 'selectedcolumns';
                        plugindata.formdata = data.data;
                        app.lists.columns.elements.push(plugindata);
                    });

                    $("." + this.nodeContent).dialog('destroy').remove();
                } else {
                    if (this.args.action == 'schreportform') {
                        $("." + this.nodeContent).dialog('close');
                        Str.get_string('reportschedule', 'block_learnerscript').then(function(s) {
                            helper.notifications(s);
                        });
                    } else if (this.args.action == 'plotforms') {
                        var self = this;
                        $("." + this.nodeContent).dialog('close');
                        Str.get_strings([{
                            key: 'graphcreated',
                            component: 'block_learnerscript'
                        }, {
                            key: 'graphupdated',
                            component: 'block_learnerscript'
                        }]).then(function(s) {
                            var message;
                            var cid;
                            var templatename;
                            var tabdata;
                            if (self.args.type == 'add') {
                                helper.notifications(s[0]);
                                // $("#report_plottabs").removeClass("ui-tabs-vertical ui-helper-clearfix");
                                // $("#report_plottabs li").removeClass("ui-corner-left");
                                // var num_tabs = $("div#report_plottabs ul li").length + 1;
                                var reportid = self.args.reportid;
                                cid = data.data.id;
                                var CreateDashboardwidget = {};
                                CreateDashboardwidget.reportid = reportid;
                                CreateDashboardwidget.chartid = data.data.id;
                                CreateDashboardwidget.reporttype = data.data.id;
                                CreateDashboardwidget.pluginname = self.args.pname;
                                CreateDashboardwidget.chartname = data.data.chartname;
                                CreateDashboardwidget.issiteadmin = true;
                                CreateDashboardwidget.editicon = M.util.image_url("t/edit");
                                Str.get_string(self.args.pname , 'block_learnerscript').then(function(s){
                                    CreateDashboardwidget.title = s ;
                                });
                                CreateDashboardwidget.cid = data.data.id;
                                CreateDashboardwidget.pname = self.args.pname;
                                CreateDashboardwidget.type = 'edit';
                                CreateDashboardwidget.deleteicon = M.util.image_url("t/delete") ;
                                CreateDashboardwidget.pname = self.args.pname;
                                CreateDashboardwidget.type = 'edit';
                                if ($('#report_plottabs').length > 0) {
                                    templatename = 'block_learnerscript/tabs';
                                    tabdata = CreateDashboardwidget;
                                } else {
                                    templatename = 'block_learnerscript/plottabs';
                                    var CreateDashboardwidgetargs = {};
                                    CreateDashboardwidgetargs.reportid = reportid;
                                    CreateDashboardwidgetargs.multiplot = true;
                                    CreateDashboardwidgetargs.enableplots = false;
                                    CreateDashboardwidgetargs.plottabs = CreateDashboardwidget;
                                    tabdata = CreateDashboardwidgetargs;
                                }

                                Templates.render(templatename, tabdata).done(function(tab) {
                                    // $( "#report_plottabs" ).tabs( "destroy" );
                                    if ($('#report_plottabs').length > 0) {
                                        $("span#report_plottabs ul").append(tab);
                                    } else {
                                        $("span.filter_buttons").prepend(tab);
                                    }

                                    $('.plotgraphcontainer').removeClass('show').addClass('hide');
                                    $('#plotreportcontainer' + reportid).html('');

                                    // $('#report_plottabs').tabs().addClass("ui-tabs-vertical ui-helper-clearfix");
                                    // $("#report_plottabs li").removeClass("ui-corner-top");
                                    // $("#report_plottabs li").addClass("ui-corner-left");
                                    // $('#report_plottabs').tabs("refresh");
                                    // $('#' + data.data.id).trigger('click');
                                });
                            } else if (self.args.type == 'edit') {
                                helper.notifications(s[1]);
                                cid = self.args.cid;
                                $('#lschartname' + cid).text(data.data.chartname);
                                $('#' + cid).trigger('click');
                            }
                        });
                    }  else if (this.args.action == 'sendreportemail') {
                        $("." + this.nodeContent).dialog('close');
                        Str.get_string('mailscheduled', 'block_learnerscript').then(function(s) {
                            helper.notifications(s);
                        });
                    } else {
                        window.location.reload();
                    }
                }
            }

        };

        /**
         * @method handleFormSubmissionFailure
         * @private
         * @return {Promise}
         */
        AjaxForms.prototype.handleFormSubmissionFailure = function(data) {

        };

        /**
         * Private method
         *
         * @method submitFormAjax
         * @private
         * @param {Event} e Form submission event.
         */
        AjaxForms.prototype.submitFormAjax = function(form) {
            if (skipClientValidation) {
                $('.' + this.nodeContent).dialog('destroy').remove();
                return true;
            }
            // We don't want to do a real form submission.
            // Convert all the form elements values to a serialised string.
            this.args.jsonformdata = $(form).serialize();
            var self = this;
            var promise = Ajax.call({
                args: this.args,
                url: this.url
            });
            promise.done(function(response) {
                return self.handleFormSubmissionResponse(response);
            }).fail(function(ex) {
                console.log(ex);
            });
        };

        /**
         * This triggers a form submission, so that any mform elements can do final tricks before the form submission is processed.
         *
         * @method submitForm
         * @param {Event} e Form submission event.
         * @private
         */
        AjaxForms.prototype.submitForm = function(e) {
            e.preventDefault();
            this.modal.getRoot().find('form').submit();
        };

        return /** @alias module:core_group/AjaxForms */ {
            // Public variables and functions.
            /**
             * Attach event listeners to initialise this module.
             *
             * @method init
             * @param {object} args The CSS selector used to find nodes that will trigger this module.
             * @param {string} url Ajax request URL.
             * @return {Promise}
             */
            init: function(args, url) {
                return new AjaxForms(args, url);
            }
        };
    });
