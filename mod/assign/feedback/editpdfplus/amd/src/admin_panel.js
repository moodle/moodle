// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/*
 * @package    assignfeedback_editpdfplus
 * @copyright  2017 Université de Lausanne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * @module mod_assignfeedback_editpdfplus/admin_panel
 * @param {Jquery} $
 * @param {Jqueryui} $.ui
 * @param {core/notification} notification
 * @param {core/templates} templates
 * @param {core/fragment} fragment
 * @param {core/ajax} ajax
 * @param {core/str} str
 * @param {assignfeedback_editpdfplus/tool} Tool
 * @param {assignfeedback_editpdfplus/tooltype} ToolType
 * @param {assignfeedback_editpdfplus/annotationhighlightplus} AnnotationHighlightplus
 * @param {assignfeedback_editpdfplus/annotationstampplus} AnnotationStampplus
 * @param {assignfeedback_editpdfplus/annotationframe} AnnotationFrame
 * @param {assignfeedback_editpdfplus/annotationcommentplus} AnnotationCommentplus
 * @param {assignfeedback_editpdfplus/annotationverticalline} AnnotationVerticalline
 * @param {assignfeedback_editpdfplus/annotationstampcomment} AnnotationStampcomment
 */
define(['jquery', 'jqueryui', 'core/notification', 'core/templates', 'core/fragment',
    'core/ajax', 'core/str', 'core/modal_factory', 'core/modal_events',
    'assignfeedback_editpdfplus/tool', 'assignfeedback_editpdfplus/tooltype',
    'assignfeedback_editpdfplus/annotationhighlightplus',
    'assignfeedback_editpdfplus/annotationstampplus', 'assignfeedback_editpdfplus/annotationframe',
    'assignfeedback_editpdfplus/annotationcommentplus', 'assignfeedback_editpdfplus/annotationverticalline',
    'assignfeedback_editpdfplus/annotationstampcomment'],
        function ($, jqui, notification, templates, fragment, ajax, str, ModalFactory, ModalEvents, Tool, ToolType,
                AnnotationHighlightplus, AnnotationStampplus, AnnotationFrame,
                AnnotationCommentplus, AnnotationVerticalline, AnnotationStampcomment) {

            /********************
             * GLOBAL VARIABLES *
             ********************/

            /**
             * Context id
             * @type {Integer}
             */
            var contextid = null;
            /**
             * Current tool in process
             * @type {Tool}
             */
            var currentTool = null;
            /**
             * Current action
             * @type {String}
             */
            var action = null;
            /**
             * All type tools
             * @type {Array<assignfeedback_edipdfplus\typetool>}
             */
            var typetools = null;
            /**
             * Current annotation in process
             * @type {Annotation}
             */
            var annotationcurrent = null;

            /****************
             * CONSTRUCTOR  *
             ****************/

            /**
             * AdminPanel class.
             *
             * @class AdminPanel
             * @param {Integer} contextidP
             * @param {String} typetoolsP
             */
            var AdminPanel = function (contextidP, typetoolsP) {
                //this.registerEventListeners();
                contextid = contextidP;
                this.initTypeTool(typetoolsP);
                this.init();
            };

            /**************
             * Parameters *
             **************/

            //messages
            /**
             * Message ok in delete case
             */
            AdminPanel.messageDelOk = "";
            /**
             * Message ko in delete case
             */
            AdminPanel.messageDelKo = "";
            /**
             * Message ko in all case
             */
            AdminPanel.messageko = "";
            /**
             * Message ok in add case
             */
            AdminPanel.messageaddok = "";
            /**
             * Message ko in add case
             */
            AdminPanel.messageaddlibelleko = "";
            /**
             * Message ok in edit case
             */
            AdminPanel.messageEditOk = "";

            /**
             * Current select button
             * @type {Jquery node}
             */
            AdminPanel.prototype.selectTool = null;

            /*************
             * FUNCTIONS *
             *************/

            /**
             * Initalisation of all messages with ajax
             */
            var initMessages = function () {
                str.get_string('admindeltool_messageok', 'assignfeedback_editpdfplus').done(function (message) {
                    AdminPanel.messageDelOk = message;
                }).fail(notification.exception);
                str.get_string('admindeltool_messageko', 'assignfeedback_editpdfplus').done(function (message) {
                    AdminPanel.messageDelKo = message;
                }).fail(notification.exception);
                str.get_string('adminaddtool_messageok', 'assignfeedback_editpdfplus').done(function (message) {
                    AdminPanel.messageaddok = message;
                }).fail(notification.exception);
                str.get_string('admin_messageko', 'assignfeedback_editpdfplus').done(function (message) {
                    AdminPanel.messageko = message;
                }).fail(notification.exception);
                str.get_string('adminedittool_messageok', 'assignfeedback_editpdfplus').done(function (message) {
                    AdminPanel.messageEditOk = message;
                }).fail(notification.exception);
                str.get_string('adminaddtool_messagelibelleko', 'assignfeedback_editpdfplus').done(function (message) {
                    AdminPanel.messageaddlibelleko = message;
                }).fail(notification.exception);
            };

            /**
             * Set typetool list from json request
             * @param {object} typeToolsP
             */
            AdminPanel.prototype.initTypeTool = function (typeToolsP) {
                var typetoolsTmp = JSON.parse(typeToolsP);
                typetools = [];
                for (var i = 0; i < typetoolsTmp.length; i++) {
                    var typeToolTmp = new ToolType();
                    typeToolTmp.initAdmin(typetoolsTmp[i]);
                    typetools[i] = typeToolTmp;
                }
            };

            /**
             * Init IHM
             */
            AdminPanel.prototype.init = function () {
                $("#editpdlplus_axes").on("change", function () {
                    $(".toolbar").hide();
                    var selectAxis = $("#editpdlplus_axes").val();
                    if (selectAxis && selectAxis !== "") {
                        $("#editpdlplus_toolbar_" + selectAxis).show();
                        var canBeDelete = $("#editpdlplus_axes option:selected").data('delete');
                        if (canBeDelete) {
                            if (parseInt(canBeDelete) > 0) {
                                $('#assignfeedback_editpdfplus_widget_admin_button_delaxis').prop('disabled', true);
                            } else {
                                $('#assignfeedback_editpdfplus_widget_admin_button_delaxis').removeAttr('disabled');
                            }
                        } else {
                            $("#editpdlplus_axes option[value='" + selectAxis + "']").data('delete', 0);
                            $('#assignfeedback_editpdfplus_widget_admin_button_delaxis').removeAttr('disabled');
                        }
                    } else {
                        $("#assignfeedback_editpdfplus_widget_admin_workspace").hide();
                        $("#assignfeedback_editpdfplus_widget_admin_toolheader").hide();
                    }
                    $('#toolworkspace').html("");
                });
                $("#editpdlplus_axes").change();

                $(".editpdlplus_tool").on("click", refreshToolView);
                this.selectTool = $(".editpdlplus_tool").first();
                this.initToolUI();
                $("#assignfeedback_editpdfplus_widget_admin_button_addaxis").on("click", this.openDivAddAxis);
                $("#assignfeedback_editpdfplus_widget_admin_button_editaxis").on("click", this.openDivEditAxis);
                $("#assignfeedback_editpdfplus_widget_admin_button_delaxis").on("click", this.openDivDelAxis);
                $("#assignfeedback_editpdfplus_widget_admin_button_exportaxis").on("click", this.openDivExportAxis);
                $("#assignfeedback_editpdfplus_widget_admin_button_addtool").on("click", this.openDivAddTool);

                $(".btn-primary").click();

                $(".btnimport").on('click', this.importAxis);
                $(".btnimportdel").on('click', this.deleteModel);

                initMessages();
            };

            /**
             * Init too UI for select element
             */
            AdminPanel.prototype.initToolUI = function () {
                //$(this.selectTool).removeClass("btn-default");
                $(this.selectTool).addClass("btn-primary");

                initSortableToolBar();
            };

            /**
             * Init tool order by drag and drop
             */
            var initSortableToolBar = function () {
                $(".sortable").sortable({
                    placeholder: "alert-warning",
                    handle: 'button',
                    cancel: '',
                    stop: function (event, uiElement) {
                        var prevButtonId = $(uiElement.item).prev().find("button").val();
                        var nextButtonId = $(uiElement.item).next().find("button").val();
                        var currentButtonId = $(uiElement.item).find("button").val();
                        $("input[name^='previoustoolid']").val(prevButtonId);
                        $("input[name^='toolid']").val(currentButtonId);
                        $("input[name^='nexttoolid']").val(nextButtonId);
                        var form = $('#assignfeedback_editpdfplus_order_tool');
                        var data = form.serialize() + "&contextid=" + contextid;
                        ajax.call([
                            {
                                methodname: 'assignfeedback_editpdfplus_submit_tool_order_form',
                                args: {jsonformdata: JSON.stringify(data)}
                            }
                        ])[0].done(function (retour) {
                            if (retour.message === "ok") {
                                //mise à jour du message
                                AdminPanel.prototype.displayMessageInformation(
                                        'message_order_tool', AdminPanel.messageEditOk, 1, 0, 0);
                            } else {
                                AdminPanel.prototype.displayMessageInformation(
                                        'message_order_tool', AdminPanel.messageko, 0, 1, 0);
                            }
                        }).fail(notification.exception);
                    }
                });
                $(".sortable").disableSelection();
            };

            /**
             * Init tool form and preview
             */
            AdminPanel.prototype.refreshPrevisu = function () {
                currentTool.axis = $("#toolaxis").val();
                currentTool.typetool = $("#typetool").val();
                currentTool.colors = $("#color").val();
                currentTool.cartridge = $("#libelle").val();
                currentTool.cartridgeColor = $("#cartridgecolor").val();
                var res = "";
                $("input[name^='text[']").each(function () {
                    if ($(this).val() && ($(this).val()).length > 0) {
                        res += '"' + $(this).val().replace(/"/g, "") + '",';
                    }
                });
                if (res.length > 0) {
                    $("#texts").val(res.substring(0, res.length - 1));
                }
                currentTool.texts = $("#texts").val();
                currentTool.label = $("#button").val();
                currentTool.enabled = $("#enabled").val();
                currentTool.reply = 0;
                if ($("#reply").is(':checked')) {
                    currentTool.reply = 1;
                }
                currentTool.orderTool = $("#order").val();
                initCanevas();
                initToolDisplay();
            };

            /**
             * Get type tool object from an id
             * @param {Integer} toolid
             * @return {Typetool}
             */
            var getTypeTool = function (toolid) {
                for (var i = 0; i < typetools.length; i++) {
                    if (typetools[i].id == toolid) {
                        return typetools[i];
                    }
                }
            };

            /**
             * Init tool form display with custom configurable parameters
             */
            var initToolDisplay = function () {
                var typetool = parseInt($("#typetool").val());
                var typetoolEntity = getTypeTool(typetool);
                var confCartridge = false;
                var confCartridgeColor = false;
                if (typetoolEntity.configurableCartridge && parseInt(typetoolEntity.configurableCartridge) === 0) {
                    $("#libelle").hide();
                    $("label[for='libelle']").hide();
                    confCartridge = true;
                } else {
                    $("#libelle").show();
                    $("label[for='libelle']").show();
                }
                if (typetoolEntity.configurableCartridgeColor && parseInt(typetoolEntity.configurableCartridgeColor) === 0) {
                    $("#cartridgecolor").hide();
                    $("label[for='cartridgecolor']").hide();
                    confCartridgeColor = true;
                } else {
                    $("#cartridgecolor").show();
                    $("label[for='cartridgecolor']").show();
                }
                if (confCartridge && confCartridgeColor) {
                    $("#collapse3").parent().hide();
                } else {
                    $("#collapse3").parent().show();
                }
                var confAnnotColor = false,
                        confAnnotTexts = false,
                        confAnnotReply = false;
                if (typetoolEntity.configurableColor && parseInt(typetoolEntity.configurableColor) === 0) {
                    $("#color").hide();
                    $("label[for='color']").hide();
                    confAnnotColor = true;
                } else {
                    $("#color").show();
                    $("label[for='color']").show();
                }
                if (typetoolEntity.configurableTexts && parseInt(typetoolEntity.configurableTexts) === 0) {
                    $(".textform").hide();
                    $("label[for='texts']").hide();
                    confAnnotTexts = true;
                } else {
                    $(".textform").show();
                    $("label[for='texts']").show();
                }
                if (typetoolEntity.configurableQuestion && parseInt(typetoolEntity.configurableQuestion) === 0) {
                    $("#reply").hide();
                    $("label[for='reply']").hide();
                    confAnnotReply = true;
                } else {
                    $("#reply").show();
                    $("label[for='reply']").show();
                }
                if (confAnnotColor && confAnnotReply && confAnnotTexts) {
                    $("#collapse4").parent().hide();
                } else {
                    $("#collapse4").parent().show();
                }
            };

            /**
             * Init tool preview
             */
            var initCanevas = function () {
                $('#canevas').html("");
                annotationcurrent = null;
                var typetool = parseInt($("#typetool").val());
                if (typetool === 3 || typetool === 4 || typetool === 7) {
                    $('#canevas').css("background-image", "url(" + $("#map01").val() + ")");
                } else if (typetool === 1 || typetool === 6) {
                    $('#canevas').css("background-image", "url(" + $("#map02").val() + ")");
                } else if (typetool === 5) {
                    $('#canevas').css("background-image", "url(" + $("#map03").val() + ")");
                }
                if (typetool === 1) {
                    annotationcurrent = new AnnotationHighlightplus();
                } else if (typetool === 3) {
                    annotationcurrent = new AnnotationStampplus();
                } else if (typetool === 4) {
                    annotationcurrent = new AnnotationFrame();
                    var annotChild = new AnnotationFrame();
                } else if (typetool === 5) {
                    annotationcurrent = new AnnotationVerticalline();
                } else if (typetool === 6) {
                    annotationcurrent = new AnnotationStampcomment();
                } else if (typetool === 7) {
                    annotationcurrent = new AnnotationCommentplus();
                }
                if (annotationcurrent) {
                    var typetoolEntity = getTypeTool(typetool);
                    currentTool.type = typetoolEntity;
                    currentTool.reply = 0;
                    if ($("#reply").is(':checked')) {
                        currentTool.reply = 1;
                    }
                    annotationcurrent.initAdminDemo(currentTool);
                    annotationcurrent.draw($('#canevas'));
                    if (annotChild) {
                        annotChild.initChildAdminDemo(annotationcurrent);
                        annotChild.draw($('#canevas'));
                    }
                }
            };

            /**
             * Load content for adding an axis
             */
            AdminPanel.prototype.openDivAddAxis = function () {
                var selectAxis = $("#editpdlplus_axes").val();
                if (selectAxis && selectAxis !== "") {
                    $("#message_edit_tool").hide();
                    $("#axistool").hide();
                } else {
                    $("#assignfeedback_editpdfplus_widget_admin_workspace").show();
                    $("#editpdlplus_axes_worspace").hide();
                }
                $('#assignfeedback_editpdfplus_widget_admin_div_axis').show();
                $('#assignfeedback_editpdfplus_widget_admin_div_addaxis').show();
                $('#assignfeedback_editpdfplus_widget_admin_div_addaxis').html("");
                $('#assignfeedback_editpdfplus_widget_admin_toolheader').hide();
                $('#assignfeedback_editpdfplus_widget_admin_toolworkspace').hide();
                $("#editpdlplus_axes").prop('disabled', true);
                var params = {};
                fragment.loadFragment('assignfeedback_editpdfplus', 'axisadd', contextid, params)
                        .done(function (html, js) {
                            templates.appendNodeContents('#assignfeedback_editpdfplus_widget_admin_div_addaxis',
                                    html, js);
                        }.bind(this)).fail(notification.exception);
            };

            /**
             * Load content for editing an axis
             */
            AdminPanel.prototype.openDivEditAxis = function () {
                $("#message_edit_tool").hide();
                $("#axistool").hide();
                $('#assignfeedback_editpdfplus_widget_admin_div_axis').show();
                $('#assignfeedback_editpdfplus_widget_admin_div_editaxis').show();
                $('#assignfeedback_editpdfplus_widget_admin_div_editaxis').html("");
                $('#assignfeedback_editpdfplus_widget_admin_toolheader').hide();
                $('#assignfeedback_editpdfplus_widget_admin_toolworkspace').hide();
                $("#editpdlplus_axes").prop('disabled', true);
                var axeid = $("#editpdlplus_axes option:selected").val();
                var params = {axeid: axeid};
                fragment.loadFragment('assignfeedback_editpdfplus', 'axisedit', contextid, params)
                        .done(function (html, js) {
                            templates.appendNodeContents('#assignfeedback_editpdfplus_widget_admin_div_editaxis',
                                    html, js);
                        }.bind(this)).fail(notification.exception);
            };

            /**
             * Load content for exporting an axis
             */
            AdminPanel.prototype.openDivExportAxis = function () {
                $("#message_edit_tool").hide();
                $("#axistool").hide();
                $('#assignfeedback_editpdfplus_widget_admin_div_axis').show();
                $('#assignfeedback_editpdfplus_widget_admin_div_exportaxis').show();
                $('#assignfeedback_editpdfplus_widget_admin_div_exportaxis').html("");
                $('#assignfeedback_editpdfplus_widget_admin_toolheader').hide();
                $('#assignfeedback_editpdfplus_widget_admin_toolworkspace').hide();
                $("#editpdlplus_axes").prop('disabled', true);
                var axeid = $("#editpdlplus_axes option:selected").val();
                var params = {axeid: axeid};
                fragment.loadFragment('assignfeedback_editpdfplus', 'axisexport', contextid, params)
                        .done(function (html, js) {
                            templates.appendNodeContents('#assignfeedback_editpdfplus_widget_admin_div_exportaxis',
                                    html, js);
                            $("#axisExportSubmit").on("click", function () {
                                var form = $('#assignfeedback_editpdfplus_widget_admin_div_exportaxis form');
                                var data = form.serialize();
                                ajax.call([
                                    {
                                        methodname: 'assignfeedback_editpdfplus_submit_axis_export_form',
                                        args: {jsonformdata: JSON.stringify(data)}
                                    }
                                ])[0].done(function (message) {
                                    if (message[0].message === "") {
                                        //add model to export page
                                        var newRow = $('<tr></tr>');
                                        newRow.append('<td>' + message[0].axelabel + '</td>');
                                        var toolbar = message;
                                        var newCell = $('<td><div class="btn-group"></div></td>');
                                        for (var i = 0; i < toolbar.length; i++) {
                                            newCell.append('<button class="btn" style="' + toolbar[i].style + '">'
                                                    + toolbar[i].button
                                                    + '</button>');
                                        }
                                        newRow.append(newCell);
                                        newRow.append('<td>'
                                                + "<button class='btn btn-primary btn-sm btnimport' data-axis='"
                                                + message[0].axeid + "'>"
                                                + "<i class='fa fa-download'></i>"
                                                + "</button>"
                                                + '</td>');
                                        newRow.append('<td>'
                                                + "<button class='btn btn-danger btn-sm btnimportdel' data-model='"
                                                + message[0].modelid + "'>"
                                                + "<i class='fa fa-remove'></i>"
                                                + "</button>"
                                                + '</td>');
                                        $('div#import tbody').append(newRow);
                                        $(".btnimport").on('click', AdminPanel.prototype.importAxis);
                                        $(".btnimportdel").on('click', AdminPanel.prototype.deleteModel);

                                        AdminPanel.prototype
                                                .resetDivAction('assignfeedback_editpdfplus_widget_admin_div_exportaxis');
                                        $("#axistool").show();
                                        var selectAxis = $("#editpdlplus_axes").val();
                                        if (selectAxis && selectAxis !== "") {
                                            AdminPanel.prototype.showWorkspace();
                                        } else {
                                            $("#assignfeedback_editpdfplus_widget_admin_workspace").hide();
                                            $('#assignfeedback_editpdfplus_widget_admin_toolheader').hide();
                                            $('#assignfeedback_editpdfplus_widget_admin_toolworkspace').hide();
                                        }
                                        $("#editpdlplus_axes").removeAttr('disabled');
                                        //mise à jour du message
                                        var messageok = str.get_string('adminexport_messageok', 'assignfeedback_editpdfplus');
                                        AdminPanel.prototype.displayMessageInformation(
                                                'message_export_axis', messageok, 1, 0, 0);
                                    } else {
                                        $('#assignfeedback_editpdfplus_widget_admin_div_exportaxis')
                                                .append("<div class='alert alert-danger' style='margin-top: 5px;'>"
                                                        + message[0].message + "</div>");
                                    }
                                }).fail(notification.exception);
                            });
                            $("#axisExportCancel").on("click", function () {
                                AdminPanel.prototype.resetDivAction('assignfeedback_editpdfplus_widget_admin_div_exportaxis');
                                AdminPanel.prototype.showWorkspace();
                                $("#editpdlplus_axes").removeAttr('disabled');
                            });
                        }.bind(this)).fail(notification.exception);
            };

            /**
             * Load content for deleting an axis
             */
            AdminPanel.prototype.openDivDelAxis = function () {
                var canBeDelete = $("#editpdlplus_axes option:selected").data('delete');
                if (canBeDelete === null || parseInt(canBeDelete) > 0) {
                    return;
                }
                var axeid = $("#editpdlplus_axes option:selected").val();
                $("#assignfeedback_editpdfplus_del_axis input[name='axeid']").val(axeid);

                ModalFactory.create({
                    type: ModalFactory.types.SAVE_CANCEL,
                    title: str.get_string('adminaxisimport_delete', 'assignfeedback_editpdfplus'),
                    body: str.get_string('adminaxisdelete_question', 'assignfeedback_editpdfplus')
                }).then(function (modal) {
                    modal.setSaveButtonText(str.get_string('adminaxisimport_delete', 'assignfeedback_editpdfplus'));
                    var root = modal.getRoot();
                    root.on(ModalEvents.save, function () {
                        // Stop the default save button behaviour which is to close the modal.
                        //e.preventDefault();
                        //remove the current stream
                        var form = $('#assignfeedback_editpdfplus_del_axis');
                        var data = form.serialize() + "&contextid=" + contextid;
                        ajax.call([
                            {
                                methodname: 'assignfeedback_editpdfplus_submit_axis_del_form',
                                args: {jsonformdata: JSON.stringify(data)}
                            }
                        ])[0].done(function (message) {
                            if (message[0].message === "1") {
                                $("#editpdlplus_axes option:selected").remove();
                                $("#editpdlplus_axes").change();
                            } else {
                                $('#assignfeedback_editpdfplus_widget_admin_div_delaxis')
                                        .append("<div class='alert alert-danger' style='margin-top: 5px;'>"
                                                + message[0].message
                                                + "</div>");
                            }
                        }).fail(notification.exception);
                    });
                    modal.show();
                });

            };

            /**
             * Fade the dom node out, update it, and fade it back.
             *
             * @private
             * @method fillResultAjax
             * @param {JQuery} node
             * @param {String} html
             * @param {String} js
             * @return {Deferred} promise resolved when the animations are complete.
             */
            var fillResultAjax = function (node, html, js) {
                var promise = $.Deferred();
                node.fadeOut("fast", function () {
                    templates.replaceNodeContents(node, html, js);
                    node.fadeIn("fast", function () {
                        promise.resolve();
                    });
                });
                return promise.promise();
            };

            /**
             * Delete a model
             * - Display a moodle popup to delete a selected model
             * - Delete it and display ok/ko message
             */
            AdminPanel.prototype.deleteModel = function () {
                var tr = $(this).parents('tr');
                var modelid = $(this).data('model');
                if (!modelid || parseInt(modelid) <= 0) {
                    return;
                }
                $("#assignfeedback_editpdfplus_del_model input[name='modelid']").val(modelid);
                ModalFactory.create({
                    type: ModalFactory.types.SAVE_CANCEL,
                    title: str.get_string('adminaxisimport_delete', 'assignfeedback_editpdfplus'),
                    body: str.get_string('delete_model_question', 'assignfeedback_editpdfplus')
                }).then(function (modal) {
                    modal.setSaveButtonText(str.get_string('adminaxisimport_delete', 'assignfeedback_editpdfplus'));
                    var root = modal.getRoot();
                    root.on(ModalEvents.save, function () {
                        // Stop the default save button behaviour which is to close the modal.
                        //e.preventDefault();
                        //remove the current stream
                        var form = $('#assignfeedback_editpdfplus_del_model');
                        var data = form.serialize() + "&contextid=" + contextid;
                        ajax.call([
                            {
                                methodname: 'assignfeedback_editpdfplus_submit_model_del_form',
                                args: {jsonformdata: JSON.stringify(data)}
                            }
                        ])[0].done(function (message) {
                            if (message[0].message === "1") {
                                tr.remove();
                            } else {
                                AdminPanel.prototype.displayMessageInformation('message_del_modal', message[0].message, 1, 0, 0);
                            }
                        }).fail(notification.exception);
                    });
                    modal.show();
                });
            };

            /**
             * Import an axis to the current's user's toolbar
             */
            AdminPanel.prototype.importAxis = function () {
                var axisimportid = $(this).data('axis');
                if (!axisimportid || parseInt(axisimportid) <= 0) {
                    return;
                }
                $("#assignfeedback_editpdfplus_import_axis > div > input[name^='axeid']").val(axisimportid);
                var form = $('#assignfeedback_editpdfplus_import_axis');
                var data = form.serialize() + "&contextid=" + contextid;
                ajax.call([
                    {
                        methodname: 'assignfeedback_editpdfplus_submit_axis_import_form',
                        args: {jsonformdata: JSON.stringify(data)}
                    }
                ])[0].done(function (toolbar) {
                    if (toolbar[0].message === "") {
                        //mise à jour du message
                        var messageok = str.get_string('adminimport_messageok', 'assignfeedback_editpdfplus');
                        AdminPanel.prototype.displayMessageInformation('message_import_axis', messageok, 1, 0, 0);
                        //maj axis
                        var divAxis = "<div id='editpdlplus_toolbar_"
                                + toolbar[0].axeid
                                + "' class='btn-group toolbar' style='display: none;'>"
                                + "<ul class='sortable' style='list-style-type: none;margin: 0;padding: 0;width: 100%;'></ul>"
                                + "</div>";
                        $('#editpdlplus_toolbars').append(divAxis);
                        initSortableToolBar();
                        var option = new Option(toolbar[0].axelabel, toolbar[0].axeid, true, true);
                        $("#editpdlplus_axes").append(option);
                        var axeOption = $("#editpdlplus_axes option[value='" + toolbar[0].axeid + "']");
                        axeOption.data('delete', 0);
                        $('#editpdlplus_tool_item').html("");
                        //maj toolbar
                        if (toolbar[0].toolid && toolbar[0].toolid > 0) {
                            for (var i = 0; i < toolbar.length; i++) {
                                var toolTmp = new Tool();
                                toolTmp.initAdmin(toolbar[i]);
                                var buttonTmp = toolTmp.getButtonSortable(toolbar[i].selecttool);
                                $("#editpdlplus_toolbar_" + toolbar[0].axeid + " > ul").append(buttonTmp);
                            }
                        } else {
                            var axeid = toolbar[0].axeid;
                            var axeOption = $("#editpdlplus_axes option[value='" + axeid + "']");
                            axeOption.data('delete', 0);
                        }
                        $(".editpdlplus_tool").on("click", refreshToolView);
                        //maj visu
                        $("#editpdlplus_axes").change();
                        $("a[href^='#currenttoolbar'").click();
                        $("#axistool").show();
                        $('#assignfeedback_editpdfplus_widget_admin_toolheader').show();
                        $('#assignfeedback_editpdfplus_widget_admin_workspace').show();
                        $('#assignfeedback_editpdfplus_widget_admin_toolworkspace').show();
                    } else {
                        AdminPanel.prototype.displayMessageInformation('message_import_axis', toolbar[0].message, 0, 1, 0);
                    }
                }).fail(notification.exception);
            };

            /**
             * Refresh tool view, preview and form for editing
             */
            var refreshToolView = function () {
                var selectid = $(this).val();
                $(".editpdlplus_tool").each(function () {
                    $(this).removeClass("btn-primary");
                    //$(this).removeClass("btn-default");
                    $(this).css("background-image", "");
                    $(this).css("background-color", "");
                    var enabled = $(this).data('enable');
                    /*if (enabled === 1 && $(this).val() !== selectid) {
                     $(this).addClass("btn-default");
                     } else*/ if (enabled !== 1 && $(this).val() !== selectid) {
                        $(this).css("background-image", "none");
                        $(this).css("background-color", "#CCCCCC");
                    }
                });
                $(this).addClass("btn-primary");
                if (!currentTool || currentTool.id !== selectid) {
                    $("#message_edit_tool").hide();
                }
                //load proprieties
                $('#editpdlplus_tool_item').html("");
                var params = {toolid: selectid};
                fragment.loadFragment('assignfeedback_editpdfplus', 'tooledit', contextid, params)
                        .done(function (html, js) {
                            fillResultAjax($('#editpdlplus_tool_item'), html, js)
                                    .done(function () {
                                        currentTool = new Tool();
                                        currentTool.id = selectid;
                                        currentTool.axis = $("#toolaxis").val();
                                        currentTool.typetool = $("#typetool").val();
                                        var typetoolEntity = getTypeTool(currentTool.typetool);
                                        currentTool.type = typetoolEntity;
                                        var realcolor = $("#realcolor").val();
                                        if (realcolor.length > 0) {
                                            currentTool.colors = $("#color").val();
                                        } else {
                                            $("#color").val(typetoolEntity.color);
                                            currentTool.colors = null;
                                        }
                                        currentTool.cartridge = $("#libelle").val();
                                        if ($("#realcartridgecolor").val() && $("#realcartridgecolor").val().length > 0) {
                                            currentTool.cartridgeColor = $("#cartridgecolor").val();
                                        } else {
                                            $("#cartridgecolor").val(typetoolEntity.get_color_cartridge());
                                            currentTool.cartridgeColor = null;
                                        }
                                        currentTool.texts = $("#texts").val();
                                        currentTool.label = $("#button").val();
                                        currentTool.enabled = $("#enabled").val();
                                        currentTool.reply = $("#reply").val();
                                        currentTool.orderTool = $("#order").val();
                                        $("#typetool").on("change", function () {
                                            currentTool.typetool = $("#typetool").val();
                                            var typetoolEntity = getTypeTool(currentTool.typetool);
                                            currentTool.type = typetoolEntity;
                                            currentTool.colors = typetoolEntity.get_color();
                                            currentTool.cartridgeColor = typetoolEntity.get_color_cartridge();
                                            $("#color").val(currentTool.colors);
                                            $("#cartridgecolor").val(currentTool.cartridgeColor);
                                            initToolDisplay();
                                            initCanevas();
                                        });
                                        $("#toolFormSubmit").on("click", function () {
                                            var res = "";
                                            $("input[name^='text[']").each(function () {
                                                if ($(this).val() && ($(this).val()).length > 0) {
                                                    res += '"' + $(this).val().replace(/"/g, "") + '",';
                                                }
                                            });
                                            if (res.length > 0) {
                                                $("#texts").val(res.substring(0, res.length - 1));
                                            }
                                            var form = $('#assignfeedback_editpdfplus_edit_tool');
                                            var data = form.serialize();
                                            ajax.call([
                                                {
                                                    methodname: 'assignfeedback_editpdfplus_submit_tool_edit_form',
                                                    args: {jsonformdata: JSON.stringify(data)}
                                                }
                                            ])[0].done(function (toolbar) {
                                                if (toolbar[0].message === "") {
                                                    //mise à jour du message
                                                    $("#message_edit_tool").show();
                                                    $("#message_edit_tool").html(AdminPanel.messageEditOk);
                                                    $("#message_edit_tool").addClass("alert-success");
                                                    $("#message_edit_tool").removeClass("alert-danger");
                                                    $("#message_edit_tool").removeClass("alert-warning");
                                                    //mise à jour bar d'outils
                                                    $("#editpdlplus_tool_" + toolbar[0].selecttool).remove();
                                                    $("#editpdlplus_toolbar_" + toolbar[0].axeid + " > ul").html("");
                                                    for (var i = 0; i < toolbar.length; i++) {
                                                        var toolTmp = new Tool();
                                                        toolTmp.initAdmin(toolbar[i]);
                                                        var buttonTmp = toolTmp.getButtonSortable(toolbar[i].selecttool);
                                                        $("#editpdlplus_toolbar_" + toolbar[0].axeid + " > ul").append(buttonTmp);
                                                    }
                                                    $(".editpdlplus_tool").on("click", refreshToolView);
                                                    var oldaxeid = $("#axisid").val();
                                                    if (oldaxeid !== toolbar[0].axeid) {
                                                        $("#editpdlplus_axes").val(toolbar[0].axeid);
                                                        $("#editpdlplus_axes").change();
                                                    }
                                                    $("#editpdlplus_tool_" + toolbar[0].selecttool).click();
                                                } else {
                                                    $("#message_edit_tool").show();
                                                    $("#message_edit_tool").html(toolbar[0].message);
                                                    $("#message_edit_tool").addClass("alert-danger");
                                                    $("#message_edit_tool").removeClass("alert-success");
                                                }
                                            }).fail(notification.exception);
                                        });
                                        $("#toolEnabled").on("click", function () {
                                            var enabled = $("#toolenabled").val();
                                            if (enabled == 1) {
                                                $("#toolEnabled > i").addClass("fa-eye-slash");
                                                $("#toolEnabled > i").removeClass("fa-eye");
                                                $("#toolenabled").val(0);
                                            } else {
                                                $("#toolEnabled > i").addClass("fa-eye");
                                                $("#toolEnabled > i").removeClass("fa-eye-slash");
                                                $("#toolenabled").val(1);
                                            }
                                            $("#toolFormSubmit").click();
                                        });
                                        $("#toolClone").on("click", function () {
                                            action = "clone";
                                            $("#assignfeedback_editpdfplus_widget_admin_button_addtool").click();
                                        });
                                        $("#toolRemove").on("click", function () {
                                            if ($(this).prop("disabled")) {
                                                return;
                                            }
                                            var form = $('#assignfeedback_editpdfplus_edit_tool');
                                            var data = form.serialize();
                                            ajax.call([
                                                {
                                                    methodname: 'assignfeedback_editpdfplus_submit_tool_del_form',
                                                    args: {jsonformdata: JSON.stringify(data)}
                                                }
                                            ])[0].done(function (toolbar) {
                                                if (toolbar[0].message === "" || toolbar[0].message === "1") {
                                                    //mise à jour du message
                                                    $("#message_edit_tool").show();
                                                    $("#message_edit_tool").html(AdminPanel.messageDelOk);
                                                    $("#message_edit_tool").addClass("alert-success");
                                                    $("#message_edit_tool").removeClass("alert-danger");
                                                    $("#message_edit_tool").removeClass("alert-warning");
                                                    //mise à jour bar d'outils
                                                    $("#editpdlplus_toolbar_" + toolbar[0].axeid + " > ul").html("");
                                                    if (parseInt(toolbar[0].toolid) > 0) {
                                                        for (var i = 0; i < toolbar.length; i++) {
                                                            var toolTmp = new Tool();
                                                            toolTmp.initAdmin(toolbar[i]);
                                                            var bT = toolTmp.getButtonSortable(toolbar[i].selecttool);
                                                            $("#editpdlplus_toolbar_" + toolbar[0].axeid + " > ul").append(bT);
                                                        }
                                                        $(".editpdlplus_tool").on("click", refreshToolView);
                                                    }
                                                    $('#toolworkspace').html("");
                                                } else {
                                                    $("#message_edit_tool").show();
                                                    $("#message_edit_tool").html(toolbar[0].message);
                                                    $("#message_edit_tool").addClass("alert-danger");
                                                    $("#message_edit_tool").removeClass("alert-success");
                                                }
                                            }).fail(notification.exception);
                                        });
                                        $("#toolRefesh").on("click", function () {
                                            AdminPanel.prototype.refreshPrevisu();
                                        });
                                        //maj affichage previsu
                                        initCanevas();
                                        //maj tool worspkace
                                        initToolDisplay();
                                    }.bind(this)).fail(notification.exception);
                        }.bind(this)).fail(notification.exception);
            };

            /**
             * Load content for adding a tool
             */
            AdminPanel.prototype.openDivAddTool = function () {
                $("#message_edit_tool").hide();
                $('#editpdlplus_tool_item').html("");
                //$('.btn-primary').addClass("btn-default");
                $('.editpdlplus_tool').removeClass("btn-primary");
                var axeid = $("#editpdlplus_axes option:selected").val();
                var params = {axisid: axeid};
                fragment.loadFragment('assignfeedback_editpdfplus', 'tooladd', contextid, params)
                        .done(function (html, js) {
                            fillResultAjax($('#editpdlplus_tool_item'), html, js)
                                    .done(function () {
                                        $("#toolaxis").val(axeid);
                                        if (action === "clone") {
                                            $("#toolaxis").val(currentTool.axis);
                                            $("#typetool").val(currentTool.typetool);
                                            $("#color").val(currentTool.colors);
                                            $("#libelle").val(currentTool.cartridge);
                                            $("#cartridgecolor").val(currentTool.cartridgeColor);
                                            $("#texts").val(currentTool.texts);
                                            $("#button").val(currentTool.label);
                                            $("#enabled").val(currentTool.enabled);
                                            $("#reply").val(currentTool.reply);
                                            $("#order").val(currentTool.orderTool);
                                            currentTool = new Tool();
                                            action = null;
                                        } else {
                                            currentTool = new Tool();
                                            $("#typetool").on("change", function () {
                                                currentTool = new Tool();
                                                currentTool.axis = $("#toolaxis").val();
                                                currentTool.typetool = $("#typetool").val();
                                                var typetoolEntity = getTypeTool(currentTool.typetool);
                                                currentTool.type = typetoolEntity;
                                                currentTool.colors = typetoolEntity.get_color();
                                                currentTool.cartridgeColor = typetoolEntity.get_color_cartridge();
                                                $("#color").val(currentTool.colors);
                                                $("#cartridgecolor").val(currentTool.cartridgeColor);
                                                AdminPanel.prototype.refreshPrevisu();
                                            });
                                            $("#typetool").change();
                                        }
                                        $("#toolFormSubmit").on("click", function () {
                                            if ($("#button").val() === "") {
                                                //mise à jour du message
                                                $("#message_edit_tool").show();
                                                $("#message_edit_tool").html(AdminPanel.messageaddlibelleko);
                                                $("#message_edit_tool").addClass("alert-warning");
                                                $("#message_edit_tool").removeClass("alert-danger");
                                                $("#message_edit_tool").removeClass("alert-success");
                                            } else {
                                                var res = "";
                                                $("input[name^='text[']").each(function () {
                                                    if ($(this).val() && ($(this).val()).length > 0) {
                                                        res += '"' + $(this).val().replace(/"/g, "") + '",';
                                                    }
                                                });
                                                if (res.length > 0) {
                                                    $("#texts").val(res.substring(0, res.length - 1));
                                                }
                                                var form = $('#assignfeedback_editpdfplus_edit_tool');
                                                var data = form.serialize();
                                                ajax.call([
                                                    {
                                                        methodname: 'assignfeedback_editpdfplus_submit_tool_add_form',
                                                        args: {jsonformdata: JSON.stringify(data)}
                                                    }
                                                ])[0].done(function (toolbar) {
                                                    if (toolbar[0].message === "") {
                                                        //mise à jour du message
                                                        $("#message_edit_tool").show();
                                                        $("#message_edit_tool").html(AdminPanel.messageaddok);
                                                        $("#message_edit_tool").addClass("alert-success");
                                                        $("#message_edit_tool").removeClass("alert-danger");
                                                        $("#message_edit_tool").removeClass("alert-warning");
                                                        //mise à jour bar d'outils
                                                        $("#editpdlplus_toolbar_" + toolbar[0].axeid + " > ul").html("");
                                                        for (var i = 0; i < toolbar.length; i++) {
                                                            var toolTmp = new Tool();
                                                            toolTmp.initAdmin(toolbar[i]);
                                                            var btnTmp = toolTmp.getButtonSortable(toolbar[i].selecttool);
                                                            $("#editpdlplus_toolbar_" + toolbar[0].axeid + " > ul").append(btnTmp);
                                                        }
                                                        $(".editpdlplus_tool").on("click", refreshToolView);
                                                        $('#toolworkspace').html("");
                                                    } else {
                                                        $("#message_edit_tool").show();
                                                        $("#message_edit_tool").html(toolbar[0].message);
                                                        $("#message_edit_tool").addClass("alert-danger");
                                                        $("#message_edit_tool").removeClass("alert-success");
                                                    }
                                                }).fail(notification.exception);
                                            }
                                        });
                                    }.bind(this)).fail(notification.exception);
                        }.bind(this)).fail(notification.exception);
            };

            /**
             * Display fresh tool workspace
             */
            AdminPanel.prototype.showWorkspace = function () {
                $("#axistool").show();
                $('#assignfeedback_editpdfplus_widget_admin_toolheader').show();
                $('#assignfeedback_editpdfplus_widget_admin_workspace').show();
                $('#assignfeedback_editpdfplus_widget_admin_toolworkspace').show();
            };

            /**
             * remove html and hide a given div with its id
             * @param string divid
             */
            AdminPanel.prototype.resetDivAction = function (divid) {
                $('#' + divid).html();
                $('#' + divid).hide();
            };

            /**
             * Display a given information message
             * @param string divmessage div's id to display
             * @param string message message content
             * @param int success style to display {0,1}
             * @param int danger style to display {0,1}
             * @param int warning style to display {0,1}
             */
            AdminPanel.prototype.displayMessageInformation = function (divmessage, message, success, danger, warning) {
                $("#" + divmessage).show();
                $("#" + divmessage).html(message);
                if (success) {
                    $("#" + divmessage).addClass("alert-success");
                } else {
                    $("#" + divmessage).removeClass("alert-success");
                }
                if (danger) {
                    $("#" + divmessage).addClass("alert-danger");
                } else {
                    $("#" + divmessage).removeClass("alert-danger");
                }
                if (warning) {
                    $("#" + divmessage).addClass("alert-warning");
                } else {
                    $("#" + divmessage).removeClass("alert-warning");
                }
                $("#" + divmessage).fadeOut(5000);
            };

            return AdminPanel;
        });