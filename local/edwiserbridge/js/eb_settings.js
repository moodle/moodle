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
/**
 * Js file to handle settings.
 *
 * @package     local_edwiserbridge
 * @copyright   2021 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author      Wisdmlabs
 */
"use strict";
define("local_edwiserbridge/eb_settings", [
    "jquery",
    "core/ajax",
    "core/url",
    "core/str",
], function($, ajax, url, str) {
    function load_settings() {
        var translation = str.get_strings([
            { key: "dailog_title", component: "local_edwiserbridge" },
            { key: "site_url", component: "local_edwiserbridge" },
            { key: "token", component: "local_edwiserbridge" },
            { key: "copy", component: "local_edwiserbridge" },
            { key: "copied", component: "local_edwiserbridge" },
            { key: "link", component: "local_edwiserbridge" },
            { key: "create", component: "local_edwiserbridge" },
            { key: "eb_empty_name_err", component: "local_edwiserbridge" },
            { key: "eb_empty_user_err", component: "local_edwiserbridge" },
            { key: "eb_service_select_err", component: "local_edwiserbridge" },
            { key: "click_to_copy", component: "local_edwiserbridge" },
            { key: "pop_up_info", component: "local_edwiserbridge" },
            { key: "eb_settings_msg", component: "local_edwiserbridge" },
            { key: "click_here", component: "local_edwiserbridge" },
            // {key: 'manualsuccessuser', component: 'local_notifications'}
        ]);

        /*translation.then(function (results) {
                console.log(results);
            });*/

        $(document).ready(function () {

            $(document).on('click', '.eb_test_connection_log_open', function (event) {
                $('.eb_test_connection_log_open').addClass('eb_test_connection_log_close');
                $('.eb_test_connection_log_close').removeClass('eb_test_connection_log_open');
                $(".eb_test_connection_log").slideDown();
            });

            $(document).on('click', '.eb_test_connection_log_close', function (event) {
                $('.eb_test_connection_log_close').addClass('eb_test_connection_log_open');
                $('.eb_test_connection_log_open').removeClass('eb_test_connection_log_close');
                $(".eb_test_connection_log").slideUp();
            });

            function checkMissingServices(service_id, messge_ele = false) {
                var promises = ajax.call([{
                    methodname: "eb_get_service_info",
                    args: { service_id: service_id },
                }, ]);

                promises[0]
                    .done(function(response) {
                        var message = "";
                        $("body").css("cursor", "default");
                        if (!response.status) {
                            $(".eb_summary_tab").removeClass("summary_tab_sucess");
                            $(".eb_summary_tab").addClass("summary_tab_error");
                            if (!messge_ele) {
                                $("#eb_common_err").text(response.msg);
                                $("#eb_common_err").css("display", "block");
                            } else if (messge_ele) {
                                var link =
                                    window.location.origin +
                                    window.location.pathname +
                                    "?tab=service";
                                var fix_link =
                                    " Check more detials <a href='" +
                                    link +
                                    "'  target='_blank'>here</a>.";
                                message =
                                    "<span class='summ_error'>" +
                                    response.msg +
                                    fix_link +
                                    "</span>";
                                $(messge_ele).empty().append(message);
                            }
                        } else {
                            if (jQuery("#web_service_status span").hasClass("summ_error")) {
                                $(".eb_summary_tab").removeClass("summary_tab_sucess");
                                $(".eb_summary_tab").addClass("summary_tab_error");
                            } else {
                                $(".eb_summary_tab").addClass("summary_tab_sucess");
                                $(".eb_summary_tab").removeClass("summary_tab_error");
                            }
                            if (messge_ele) {
                                message =
                                    '<span style="color: #7ad03a;"><span class="summ_success" style="font-weight: bolder; color: #7ad03a; font-size: 22px;">&#10003;</span></span>';
                                $(messge_ele).empty().append(message);
                            }
                        }
                        return response;
                    })
                    .fail(function(response) {
                        $("body").css("cursor", "default");
                        return 0;
                    });
            }
            /**
             * Check if the user is on edwiser bridge settings page.
             */
            if (window.location.href.indexOf("edwiserbridge.php") > 1) {
                let searchParams = new URLSearchParams(window.location.search);
                if (searchParams.has("tab") && "service" === searchParams.get("tab")) {
                    var service_id = $("#id_eb_sevice_list").val();
                    if ("" != service_id && "create" != service_id) {
                        checkMissingServices(service_id);
                    }
                }
                if (searchParams.has("tab") && "summary" === searchParams.get("tab")) {
                    $("#web_service_status").empty();
                    var service_id = $("#web_service_status").data("serviceid");
                    checkMissingServices(service_id, "#web_service_status");
                }
            }

            /*
             * Functionality to show only tokens which are asscoiated with the service.
             */
            $("#id_eb_sevice_list").change(function() {
                var service_id = $(this).val();
                $("#eb_common_success").css("display", "none");
                $("#eb_common_err").css("display", "none");

                $("#id_eb_token option:selected").removeAttr("selected");

                $('#id_eb_token option[value=""]').attr("selected", true);

                handlefieldsdisplay(
                    "create",
                    service_id,
                    ".eb_service_field",
                    "#id_eb_mform_create_service"
                );

                if ($(this).val() != "") {
                    $("#id_eb_token").children("option").hide();
                    $("#id_eb_token")
                        .children("option[data-id^=" + $(this).val() + "]")
                        .show();

                    if ($(this).val() != "create") {
                        $("body").css("cursor", "progress");
                        checkMissingServices(service_id);
                    }
                }
            });

            /*****************    Change Form Action URL   *******************/

            $("#conne_submit_continue").click(function() {
                $(this)
                    .closest("form")
                    .attr(
                        "action",
                        M.cfg.wwwroot +
                        "/local/edwiserbridge/edwiserbridge.php?tab=synchronization"
                    );
            });

            $("#sync_submit_continue").click(function() {
                $(this)
                    .closest("form")
                    .attr(
                        "action",
                        M.cfg.wwwroot + "/local/edwiserbridge/edwiserbridge.php?tab=summary"
                    );
            });

            $("#settings_submit_continue").click(function() {
                $(this)
                    .closest("form")
                    .attr(
                        "action",
                        M.cfg.wwwroot + "/local/edwiserbridge/edwiserbridge.php?tab=service"
                    );
            });

            /*********** END *********/
            // Add Settings field.
            if (!$(".eb_settings_btn_cont").length) {
                $("#admin-eb_setup_wizard_field").before(
                    '<div class="eb_settings_btn_cont" style="padding: 30px;"> ' +
                    M.util.get_string("eb_settings_msg", "local_edwiserbridge") +
                    ' <a target="_blank" style="border-radius: 4px;margin-left: 5px;padding: 7px 18px;" class="eb_settings_btn btn btn-primary" href="' +
                    M.cfg.wwwroot +
                    '/local/edwiserbridge/setup_wizard.php"> ' +
                    M.util.get_string("click_here", "local_edwiserbridge") +
                    " </a></div>"
                );
            }
            $("#admin-eb_setup_wizard_field").css("display", "none");

            //Adds the link and create button on the set-up wizard
            if ($("#admin-ebnewserviceuserselect").length) {
                if (!$("#eb_create_service").length) {
                    $("#admin-ebnewserviceuserselect").after(
                        '<div class="row eb_create_service_wrap">' +
                        '  <div class="offset-sm-3 col-sm-3">' +
                        '    <button type="submit" id="eb_create_service" class="btn">' +
                        M.util.get_string("link", "local_edwiserbridge") +
                        "</button>" +
                        "  </div>" +
                        "</div>"
                    );
                }
            }

            //This adds the error succes messages divs on the set-up wizard.
            if ($(".eb_create_service_wrap").length) {
                $(".eb_create_service_wrap").before(
                    '<div class="row eb_common_err_wrap">' +
                    '  <div class="offset-sm-3 col-sm-3">' +
                    '    <span id="eb_common_err" class="btn"></span>' +
                    '    <span id="eb_common_success" class="btn"></span>' +
                    "  </div>" +
                    "</div>"
                );
            }

            $("#id_eb_mform_create_service").click(function(event) {
                event.preventDefault();
                var error = 0;
                var web_service_name = $("#id_eb_service_inp").val();
                var user_id = $("#id_eb_auth_users_list").val();
                var service_id = $("#id_eb_sevice_list").val();
                var token = $("#id_eb_token").val();

                $(".eb_settings_err").remove();
                $("#eb_common_success").css("display", "none");
                $("#eb_common_err").css("display", "none");

                if (user_id == "") {
                    $("#eb_common_err").text(
                        M.util.get_string("eb_empty_user_err", "local_edwiserbridge")
                    );
                    $("#eb_common_err").css("display", "block");
                    error = 1;
                }

                //If the select box has a value to create the web service the create web service else
                if (service_id == "create") {
                    if (web_service_name == "") {
                        $("#eb_common_err").css("display", "block");
                        $("#eb_common_err").text(
                            M.util.get_string("eb_empty_name_err", "local_edwiserbridge")
                        );
                        error = 1;
                    }

                    if (error) {
                        return;
                    }

                    create_web_service(
                        web_service_name,
                        user_id,
                        "#id_eb_sevice_list",
                        "#eb_common_err",
                        1
                    );
                } else {
                    if ($("#id_eb_token").val() == "") {
                        $("#eb_common_err").css("display", "block");
                        $("#eb_common_err").text(
                            M.util.get_string("token_empty", "local_edwiserbridge")
                        );
                        error = 1;
                        return 0;
                    }

                    if (error) {
                        return;
                    }

                    //If select has selected existing web service
                    if (service_id != "") {
                        link_web_service(
                            service_id,
                            token,
                            "#eb_common_err",
                            "#eb_common_success"
                        );
                    } else {
                        //If the select box has been selected with the placeholder
                        $("#eb_common_err").text(
                            M.util.get_string("eb_service_select_err", "local_edwiserbridge")
                        );
                    }
                }
            }); // event end

            /************************ Web service creation click handlers *******************************/

            /* -------------------------------------------
             *  Copy to clipboard functionality handler
             *---------------------------------------*/

            /**
             * This shows the copy test on the side
             */
            $(document).on("mouseenter", ".eb_copy_text_wrap", function() {
                // hover starts code here
                var parent = $(this).find(".eb_copy_btn");
                parent.css("visibility", "visible");
            });

            $(document).on("mouseleave", ".eb_copy_text_wrap", function() {
                // hover ends code here
                var parent = $(this).find(".eb_copy_btn");
                parent.css("visibility", "hidden");
            });

            /**
             * Copy to clipboard functionality.
             */
            $(document).on("click", ".eb_copy_text_wrap", function(event) {
                event.preventDefault();

                var copyText = $(this).find(".eb_copy_text").html();
                var temp = document.createElement("textarea");
                temp.textContent = copyText;

                document.body.appendChild(temp);
                var selection = document.getSelection();
                var range = document.createRange();
                //  range.selectNodeContents(textarea);
                range.selectNode(temp);
                selection.removeAllRanges();
                selection.addRange(range);

                document.execCommand("copy");

                temp.remove();
                toaster("Title", 400);
            });

            $(document).on("click", ".eb_primary_copy_btn", function(event) {
                event.preventDefault();

                // var copyText     = $(this).html();

                var parent = $(this).parent().parent();

                parent = parent.find(".eb_copy");

                if (parent.attr("id") == "id_eb_token") {
                    var copyText = parent.val();
                } else {
                    var copyText = parent.text();
                }

                var temp = document.createElement("textarea");
                temp.textContent = copyText;

                document.body.appendChild(temp);
                var selection = document.getSelection();
                var range = document.createRange();
                //  range.selectNodeContents(textarea);
                range.selectNode(temp);
                selection.removeAllRanges();
                selection.addRange(range);

                document.execCommand("copy");

                temp.remove();
                toaster("Title", 200);
            });

            /*************   Copy to clipboard functionality handler  **************/

            /*----------------------------------------------------
             * Below are alll js functions
             *---------------------------------------------------*/

            /**
             * Toatser adde to show the successful copy message.
             */
            function toaster(title, time = 2000) {
                const id = "local_edwiserbridge_copy";
                const toast = $(
                    '<div id="' +
                    id +
                    '">' +
                    M.util.get_string("copied", "local_edwiserbridge") +
                    "<div>"
                ).get(0);
                document.querySelector("body").appendChild(toast);
                toast.classList.add("show");
                setTimeout(function() {
                    toast.classList.add("fade");
                    setTimeout(function() {
                        toast.classList.remove("fade");
                        setTimeout(function() {
                            toast.remove();
                        }, time);
                    }, time);
                });
            }

            /**
             * This function adds newly created web service in the drop down
             */
            function add_new_service_in_select(element, name, id) {
                $(element + "option:selected").removeAttr("selected");
                $(element).append(
                    '<option value="' + id + '" selected> ' + name + " </option>"
                );
            }

            /**
             * This function adds newly created web service in the drop down
             */
            function add_new_token_in_select(element, token, id) {
                $(element + "option:selected").removeAttr("selected");
                $(element).append(
                    '<option data-id="' +
                    id +
                    '" value="' +
                    token +
                    '" selected> ' +
                    token +
                    " </option>"
                );
            }

            /**
             * This function handles the display of the service creation form depending on the drop down value.
             */
            function handlefieldsdisplay(
                condition,
                condition_var,
                element,
                btn = ""
            ) {
                if (condition == condition_var) {
                    $(btn).text(M.util.get_string("create", "local_edwiserbridge"));
                    $(element).css("display", "flex");
                } else {
                    $(btn).text(M.util.get_string("link", "local_edwiserbridge"));
                    $(element).css("display", "none");
                }
            }

            /**
             * This functions link the existing wervices
             */
            function link_web_service(
                service_id,
                token,
                common_errr_fld,
                common_success_fld
            ) {
                $("body").css("cursor", "progress");
                $("#eb_common_err").css("display", "none");

                var promises = ajax.call([{
                    methodname: "eb_link_service",
                    args: { service_id: service_id, token: token },
                }, ]);

                promises[0]
                    .done(function(response) {
                        $("body").css("cursor", "default");
                        if (response.status) {
                            $(common_success_fld).text(response.msg);
                            $(common_success_fld).css("display", "block");
                        } else {
                            $(common_errr_fld).text(response.msg);
                            $(common_success_fld).css("display", "block");
                        }

                        return response;
                    })
                    .fail(function(response) {
                        $("body").css("cursor", "default");
                        return 0;
                    }); //promise end
            }

            $(document).on("click", ".eb_service_pop_up_close", function() {
                $(".eb_service_pop_up").hide();
            });

            /**
             * This functions regiters new web service.
             */
            function create_web_service(
                web_service_name,
                user_id,
                service_select_fld,
                common_errr_fld,
                is_mform
            ) {
                $("body").css("cursor", "progress");
                $("#eb_common_err").css("display", "none");

                $("#id_eb_token option:selected").removeAttr("selected");

                $('#id_eb_token option[value=""]').attr("selected", true);

                var promises = ajax.call([{
                    methodname: "eb_create_service",
                    args: { web_service_name: web_service_name, user_id: user_id },
                }, ]);

                var validation_error = 0;

                if (!validation_error) {
                    promises[0]
                        .done(function(response) {
                            $("body").css("cursor", "default");
                            if (response.status) {
                                //Dialog box content.
                                var eb_dialog_content =
                                    "<div> " +
                                    M.util.get_string("pop_up_info", "local_edwiserbridge") +
                                    " </div>" +
                                    '<table class="eb_toke_detail_tbl">' +
                                    "  <tr>" +
                                    '     <th width="17%">' +
                                    M.util.get_string("site_url", "local_edwiserbridge") +
                                    "</th>" +
                                    '     <td> : <span class="eb_copy_text" title="' +
                                    M.util.get_string("click_to_copy", "local_edwiserbridge") +
                                    '">' +
                                    response.site_url +
                                    "</span>" +
                                    '        <span class="eb_copy_btn">' +
                                    M.util.get_string("copy", "local_edwiserbridge") +
                                    "</span></td>" +
                                    "  </tr>" +
                                    "  <tr>" +
                                    '     <th width="17%">' +
                                    M.util.get_string("token", "local_edwiserbridge") +
                                    "</th>" +
                                    '     <td> : <span class="eb_copy_text" title="' +
                                    M.util.get_string("click_to_copy", "local_edwiserbridge") +
                                    '">' +
                                    response.token +
                                    "</span>" +
                                    '        <span class="eb_copy_btn">' +
                                    M.util.get_string("copy", "local_edwiserbridge") +
                                    "</span></td>" +
                                    "  </tr>" +
                                    "</table>";

                                $("body").append(
                                    '<div class="eb_service_pop_up_cont">' +
                                    '<div class="eb_service_pop_up">' +
                                    '<span class="helper"></span>' +
                                    "<div>" +
                                    '<div class="eb_service_pop_up_close">&times;</div>' +
                                    "<div>" +
                                    '<div class="eb_service_pop_up_title"></div>' +
                                    '<div class="eb_service_pop_up_content"></div>' +
                                    "</div>" +
                                    "</div>" +
                                    "</div>" +
                                    "</div>"
                                );

                                $(".eb_service_pop_up_content").html(eb_dialog_content);
                                $(".eb_service_pop_up").show();

                                add_new_service_in_select(
                                    service_select_fld,
                                    web_service_name,
                                    response.service_id
                                );
                                add_new_token_in_select(
                                    "#id_eb_token",
                                    response.token,
                                    response.service_id
                                );
                            } else {
                                $("#eb_common_err").css("display", "block");
                                $(common_errr_fld).text(response.msg);
                            }

                            return response;
                        })
                        .fail(function(response) {
                            $("body").css("cursor", "default");
                            return 0;
                        }); //promise end
                }
            }

            /************************  Functions END  ****************************/





            /******************    SETUP wizard   *****************/

            var loader = '<div id="eb-lading-parent" class="eb-lading-parent-wrap"><div class="eb-loader-progsessing-anim"></div></div>';
            $("body").append(loader);



            function change_url( step ) {
                var url = new URL(document.location);
                url.searchParams.set('current_step', step);
                window.history.replaceState( null, null, url );
            }

            function handle_step_progress( current_step, next_step, is_next_sub_step, parent_step ) {
                /**
                 * 1. Mark current step as active and 
                 * 2. Mark previous step as completed.
                 */
                // Add completed class to the sidebar steps
                var temp1 = $('.eb-setup-step-' + current_step).addClass('eb-setup-step-completed-wrap');
                if( $('.eb-setup-step-' + current_step).hasClass('eb-setup-step-active-wrap') ) {
                    $('.eb-setup-step-' + current_step).removeClass('eb-setup-step-active-wrap');
                }

                // Chnage step names class
                var step_title = $('.eb-setup-step-' + current_step).children('.eb-setup-steps-title');
                step_title.addClass('eb-setup-step-completed');
                if(step_title.hasClass('eb-setup-step-active')){
                    step_title.removeClass('eb-setup-step-active');
                }

                // Change icons class
                var icon = $('.eb-setup-step-' + current_step).children('.eb_setup_sidebar_progress_icons');
                icon.addClass('fa-circle-check');

                if( icon.hasClass('fa-circle-chevron-right') ) {
                    icon.removeClass('fa-circle-chevron-right');
                }


                var temp2 = $('.eb-setup-step-' + next_step).addClass('eb-setup-step-active-wrap');
                
                var step_title1 = $('.eb-setup-step-' + next_step).children('.eb-setup-steps-title');
                step_title1.addClass('eb-setup-step-active');

                var icon = $('.eb-setup-step-' + next_step).children('.eb_setup_sidebar_progress_icons');
                icon.addClass('fa-solid fa-circle-chevron-right');

                if(icon.hasClass('eb-setup-step-circle')){
                    icon.removeClass('eb-setup-step-circle');
                }

            }

            // ajax xall to save data and get new tab at the same time.
        
            // Clicking save continue
            // 
            $(document).on('click', '.eb_setup_save_and_continue', function (event) {
                // Create loader.
                var current = $(this);
                var current_step = $(this).data('step');
                var next_step = $(this).data('next-step');
                var is_next_sub_step = $(this).data('is-next-sub-step');



                // get current step.
                // get next step.
                // get data which will be saved.
                // Creating swicth case.
                var data = { current_step : current_step, next_step : next_step, is_next_sub_step : is_next_sub_step };

                switch ( current_step ) {
                    case 'installtion_guide':
                        $("#eb-lading-parent").show();

                        // Get required data and create array
                        data = { current_step : current_step, next_step : next_step, is_next_sub_step : is_next_sub_step };

                        break;

                    case 'mdl_plugin_config':
                        $("#eb-lading-parent").show();

                        data = { current_step : current_step, next_step : next_step, is_next_sub_step : is_next_sub_step };
                        
                        break;
                
                    case 'web_service':
                        var service_name = $('.eb_setup_web_service_list').val();

                        // Course sync process.
                        // Call course sync callback and after completing the process, call this callback.
                        if( service_name == 'create' && '' == $('#eb_setup_web_service_name').val() ){
                            event.preventDefault();
                            $('#eb_setup_web_service_name').css('border-color', 'red');
                            return;

                        } else {
                            $("#eb-lading-parent").show();

                            var existing_service = 1;

                            if ( service_name == 'create' && service_name != '' ) {
                                service_name = $('.eb_setup_web_service_name').val();
                                existing_service = 0;
                            }

                            data = { current_step : current_step, next_step : next_step, is_next_sub_step : is_next_sub_step, service_name : service_name, existing_service : existing_service /*mdl_url : mdl_url, mdl_token : mdl_token, mdl_lng_code : mdl_lng_code*/ };
                        }
                        break;


                    case 'wordpress_site_details':

                        if( '' != site_name && ( '' == $('#eb_setup_site_name').val() || '' == $('#eb_setup_site_url').val() ) ){
                            event.preventDefault();

                            if ( '' == $('#eb_setup_site_name').val() ) {
                                $('#eb_setup_site_name').css('border-color', 'red');
                            } else {
                                $('#eb_setup_site_name').css('border-color', '#E5E5E5');
                            }

                            if ( '' == $('#eb_setup_site_url').val() ) {
                                $('#eb_setup_site_url').css('border-color', 'red');
                            } else {
                                $('#eb_setup_site_url').css('border-color', '#E5E5E5');
                            }

                            return;
                        } else {
                            $("#eb-lading-parent").show();

                            // Course sync process.
                            // Call course sync callback and after completing the process, call this callback.

                            var site_name = $('.eb_setup_wp_sites').val();
                            var url       = '';

                            if ( '' != site_name ) {
                                site_name = $('.eb_setup_site_name').val();
                                url       = $('.eb_setup_site_url').val();
                            }

                            data = { current_step : current_step, next_step : next_step, is_next_sub_step : is_next_sub_step, site_name : site_name, url : url /*mdl_url : mdl_url, mdl_token : mdl_token, mdl_lng_code : mdl_lng_code*/ };
                        }

                        break;


                    case 'user_and_course_sync':
                        $("#eb-lading-parent").show();

                        var user_enrollment   = $('#eb_setup_sync_user_enrollment').prop('checked') ? 1 : 0;
                        var user_unenrollment = $('#eb_setup_sync_user_unenrollment').prop('checked') ? 1 : 0;
                        var user_creation     = $('#eb_setup_sync_user_creation').prop('checked') ? 1 : 0;
                        var user_deletion     = $('#eb_setup_sync_user_deletion').prop('checked') ? 1 : 0;
                        var user_update       = $('#eb_setup_sync_user_update').prop('checked') ? 1 : 0;
                        var course_creation   = $('#eb_setup_sync_course_creation').prop('checked') ? 1 : 0;
                        var course_deletion   = $('#eb_setup_sync_course_deletion').prop('checked') ? 1 : 0;



                        // If user checkbox is clicked start user sync otherwise just procedd to next screen.
                        data = { current_step : current_step, next_step : next_step, is_next_sub_step : is_next_sub_step, user_enrollment: user_enrollment, user_unenrollment: user_unenrollment, user_creation: user_creation, user_deletion: user_deletion, user_update: user_update, course_creation: course_creation, course_deletion: course_deletion };

                        break;


                    default:
                        $("#eb-lading-parent").show();

                        break;
                }



            
                data = JSON.stringify(data);


                var promises = ajax.call([{
                    methodname: "edwiserbridge_local_setup_wizard_save_and_continue",
                    args: { data : data },
                }, ]);

                promises[0].done(function(response) {
                    $("#eb-lading-parent").hide();

                    change_url( next_step );


                    // Dummy value.
                    var parent_step = 1;

                    handle_step_progress( current_step, next_step, is_next_sub_step, parent_step );

                    $('.eb-setup-header-title').html(response.title);
                    $('.eb-setup-content').html(response.html_data);


                    if ( 'complete_details' == next_step ) {
                        $('.eb-setup-content').append('<div class="eb_setup_popup"> ' + $('.eb_setup_wp_completion_success_popup').html() + ' </div>');


                        setTimeout(function(){
                            $('.eb_setup_popup').remove();
                        }, 2000);
                    }


                    return response;
                }).fail(function(response) {
                    $("#eb-lading-parent").hide();
                    $("body").css("cursor", "default");
                    return 0;
                }); //promise end


            });



            // Adding for refresh page condition
            if ( $(".eb_setup_wp_completion_success_popup").length) {
                $('.eb-setup-content').append('<div class="eb_setup_popup"> ' + $('.eb_setup_wp_completion_success_popup').html() + ' </div>');

                setTimeout(function(){
                    $('.eb_setup_popup').remove();
                }, 2000);
            }





            /*
            * Ajax call to enable settings. 
            */
            $(document).on('click', '.eb_enable_plugin_settings', function (event) {
                // start loader
                $("#eb-lading-parent").show();

                var promises = ajax.call([{
                    methodname: 'edwiserbridge_local_enable_plugin_settings',
                    args: {},
                }, ]);

                promises[0].done(function(response) {
                    $("body").css("cursor", "default");
                    $("#eb-lading-parent").hide();

                    // stop loader.
                    // change icon colors
                    $('.eb_enable_rest_protocol').css( 'color', '#1AB900' );
                    $('.eb_enable_web_service').css( 'color', '#1AB900' );
                    $('.eb_disable_pwd_policy').css( 'color', '#1AB900' );
                    $('.eb_allow_extended_char').css( 'color', '#1AB900' );

                    // show success message.
                    $('.eb_setup_settings_success_msg').css( 'display', 'block' );


                    // Hide current button and show save and continue button
                    $('.eb_enable_plugin_settings').css( 'display', 'none' );
                    $('.eb_enable_plugin_settings_label').css( 'display', 'none' );
                    $('.eb_setup_save_and_continue').css( 'display', 'initial' );

                    return response;
                }).fail(function(response) {
                    $("#eb-lading-parent").hide();

                    $("body").css("cursor", "default");
                    return 0;
                }); //promise end


            });



            var acc = document.getElementsByClassName("accordion");
            var i;

            for (i = 0; i < acc.length; i++) {
              acc[i].addEventListener("click", function() {
                /* Toggle between adding and removing the "active" class,
                to highlight the button that controls the panel */
                this.classList.toggle("active");

                /* Toggle between hiding and showing the active panel */
                var panel = this.nextElementSibling;
                if (panel.style.display === "block") {
                  panel.style.display = "none";
                } else {
                  panel.style.display = "block";
                }
              });
            }



            // Handle Setup web service dropdown.
            // $(".eb_setup_web_service_list").change(function() {
            $(document).on('change', '.eb_setup_web_service_list', function (event) {

                if('' != $(".eb_setup_web_service_list").val()){
                    $('.eb_setup_web_service_btn').removeClass('disabled');
                    $('.eb_setup_web_service_btn').removeAttr("disabled");
                } else {
                    $('.eb_setup_web_service_btn').attr("disabled", "disabled");
                    $('.eb_setup_web_service_btn').addClass('disabled');
                }


                if('create' == $(".eb_setup_web_service_list").val()){
                    $('.eb_setup_web_service_name_wrap').css('display', 'block');
                } else {
                    $('.eb_setup_web_service_name_wrap').css('display', 'none');
                }
            });


            // Handle Wp site drop down
            // $(".eb_setup_wp_sites").change(function() {
            $(document).on('change', '.eb_setup_wp_sites', function (event) {

                if('' != $(".eb_setup_web_service_list").val()){
                    $('.eb_setup_wp_details_btn').removeClass('disabled');
                    $('.eb_setup_wp_details_btn').removeAttr("disabled");
                } else {
                    $('.eb_setup_wp_details_btn').attr("disabled", "disabled");
                    $('.eb_setup_wp_details_btn').addClass('disabled');
                }

                if('' == $(".eb_setup_wp_sites").val()){
                    $('.eb_setup_wp_site_details_inp').addClass('eb_setup_wp_site_details_wrap');
                } else {
                    $('.eb_setup_wp_site_details_inp').removeClass('eb_setup_wp_site_details_wrap');

                    var option = $(this).find(":selected");

                    $('.eb_setup_site_name').val(option.data('name'));
                    $('.eb_setup_site_url').val(option.data('url'));    
                }
            });




            $(document).on('click', '.eb_setup_test_connection_btn', function (event) {

                var url = $('.eb_setup_site_url').val();
                $("body").css("cursor", "wait");

                $("#eb-lading-parent").show();


                var promises = ajax.call([{
                    methodname: 'edwiserbridge_local_setup_test_connection',
                    args: { wp_url: url },
                }, ]);

                promises[0].done(function(response) {
                    $("#eb-lading-parent").hide();
                    $("body").css("cursor", "default");


                    $('.eb_setup_test_conn_resp_msg').css('display', 'block');

                    // Parse response.
                    if(response.status == 1){
                        $('.eb_setup_test_connection_continue_btn').css('display', 'inline-block');
                        $('.eb_setup_test_connection_btn').css('display', 'none');

                        $('.eb_setup_test_conn_resp_msg').addClass('eb_setup_settings_success_msg');
                        $('.eb_setup_test_conn_resp_msg').html('<i class="fa-solid fa-circle-check"></i> ' + response.msg);
                        $('.eb_setup_test_conn_resp_msg').removeClass('eb_setup_error_msg_box');
                    }else{
                        $('.eb_setup_test_conn_resp_msg').addClass('eb_setup_error_msg_box');
                        $('.eb_setup_test_conn_resp_msg').html('<i class="fa-solid fa-circle-check"></i> ' + response.msg);
                        $('.eb_setup_test_conn_resp_msg').removeClass('eb_setup_settings_success_msg');
                    }

                    return response;
                }).fail(function(response) {
                    $("body").css("cursor", "default");
                    $("#eb-lading-parent").hide();

                    return 0;
                });


            });


            
            $(document).on('click', '#eb_setup_sync_all', function (event) {
                if(this.checked){
                    $('.eb_setup_sync_cb').prop('checked', true);
                } else{
                    $('.eb_setup_sync_cb').prop('checked', false);
                }
            });

            $(document).on('click', '.eb_setup_sync_cb', function (event) {
                if(this.checked){
                    var all_checked = 1;
                    $(".eb_setup_sync_cb").each(function() {

                        if( ! this.checked ){
                            all_checked = 0;
                        }

                    });

                    if ( all_checked ) {
                        $('#eb_setup_sync_all').prop('checked', true);
                    }

                } else{
                    $('#eb_setup_sync_all').prop('checked', false);
                }
            });

            



            /**
             * Copy to clipboard functionality.
             */
            $(document).on("click", ".eb_setup_copy", function(event) {
                event.preventDefault();

                var copyText = $(this).data('copy');
                var temp = document.createElement("textarea");
                temp.textContent = copyText;

                document.body.appendChild(temp);
                var selection = document.getSelection();
                var range = document.createRange();
                //  range.selectNodeContents(textarea);
                range.selectNode(temp);
                selection.removeAllRanges();
                selection.addRange(range);

                document.execCommand("copy");
                temp.remove();
                // toaster("Title", 400);
                // var parent = $(this).parent();
                var copy_success = '<p class="eb_setup_copy_success"><i class="fa fa-check" aria-hidden="true"></i>Copied !!</p>';
                $(this).append(copy_success);
                setTimeout(function(){
                    $('.eb_setup_copy_success').remove();
                }, 2000);
            });



            // Code to create json file and download it.
            // $(".").click(function() {
            $(document).on("click", ".eb_setup_download_creds", function(event) {

                var obj = {
                    url: $('.eb_setup_copy_url').html(),
                    token: $('.eb_setup_copy_token').html(),
                    lang_code: $('.eb_setup_copy_lang').html(),
                };


                $("<a />", {
                    "download": "creds.json",
                    "href" : "data:application/json," + encodeURIComponent(JSON.stringify( obj ) )
                }).appendTo("body").click(function() {
                    $(this).remove();
                })[0].click()
            });

        /**
         * Close setup.
         */
        $('.eb-setup-close-icon').click(function(){
            // Create loader.
            $('.eb-setup-content').append('<div class="eb_setup_popup"> ' + $('.eb_setup_popup_content_wrap').html() + ' </div>');

        });


        $(document).on('click', '.eb_setup_do_not_close', function (event) {
            $('.eb_setup_popup').remove();
        });


        $(document).on('change', '.eb_setup_wp_sites', function (event) {
            var option = $(this).find(":selected");

            $('.eb_setup_site_name').val(option.data('name'));
            $('.eb_setup_site_url').val(option.data('url'));

        });





        $(document).on('click', '.eb_redirect_to_wp', function (event) {

            event.preventDefault();


            // Sending one js request to unset the progress.
            var current = $(this);
            var current_step = $(this).data('step');
            var next_step = $(this).data('next-step');
            var is_next_sub_step = $(this).data('is-next-sub-step');


            var data = { current_step : current_step, next_step : next_step, is_next_sub_step : is_next_sub_step };

            data = JSON.stringify(data);


            var promises = ajax.call([{
                methodname: "edwiserbridge_local_setup_wizard_save_and_continue",
                args: { data : data },
            }, ]);

            promises[0].done(function(response) {

                return response;
            }).fail(function(response) {
                return 0;
            }); //promise end












            // Create loader.
            $('.eb-setup-content').append('<div class="eb_setup_popup"> ' + $('.eb_setup_wp_redirection_popup').html() + ' </div>');

            setTimeout( function(){
                $('.eb_setup_popup').remove();
                // window.location.replace($(this).attr('href'));
                $('.eb_redirect_to_wp_btn').trigger('click');

                var redirect = window.open($('.eb_redirect_to_wp').attr('href'), "_blank");
                redirect.focus();
            }, 2000 );

        });



        /***************************/


        });
    }
    return { init: load_settings };
});
