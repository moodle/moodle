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
define("local_edwiserbridge/eb_setup_wizard", [
    "jquery",
    "core/ajax",
    "core/url",
    "core/str",
], function($, ajax, url, str) {
	return {
        init: function($params) {
	// function load_settings() {
        // var translation = str.get_strings([
        //     { key: "dailog_title", component: "local_edwiserbridge" },
        //     { key: "site_url", component: "local_edwiserbridge" },
        //     { key: "token", component: "local_edwiserbridge" },
        //     { key: "copy", component: "local_edwiserbridge" },
        //     { key: "copied", component: "local_edwiserbridge" },
        //     { key: "link", component: "local_edwiserbridge" },
        //     { key: "create", component: "local_edwiserbridge" },
        //     { key: "eb_empty_name_err", component: "local_edwiserbridge" },
        //     { key: "eb_empty_user_err", component: "local_edwiserbridge" },
        //     { key: "eb_service_select_err", component: "local_edwiserbridge" },
        //     { key: "click_to_copy", component: "local_edwiserbridge" },
        //     { key: "pop_up_info", component: "local_edwiserbridge" },
        //     { key: "eb_settings_msg", component: "local_edwiserbridge" },
        //     { key: "click_here", component: "local_edwiserbridge" },
        //     // {key: 'manualsuccessuser', component: 'local_notifications'}
        // ]);





    $(document).ready(function () {


    // ----    ------
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

    // ----   ------

    // ajax call to change the tab.
        /**
         * Reload the Moodle course enrollment.
         */
        $('.eb-setup-step-completed').click(function(){

        // Create loader.
        var current = $(this);
        var step = $(this).data('step');

        // current.append(loader_html);


        $.ajax({
            method: "post",
            url: eb_setup_wizard.ajax_url,
            dataType: "json",
            data: {
                'action': 'eb_setup_' + step,
                // 'course_id': course_id,
                // '_wpnonce_field': eb_admin_js_object.nonce,
            },
            success: function (response) {

                current.find('.eb-load-response').remove();
                //prepare response for user
                if (response.success == 1) {
                    $('.eb-setup-content').html(response.data.content);

                } else {

                }
            }
        });

        });


        // Clicking save continue
        // 
        // 
        $('.eb_set_up_save_and_continue').click(function(){

            // Create loader.
            var current = $(this);
            var step = $(this).data('step');
            
            // get current step.
            // get next step.
            // get data which will be saved.

            // Creating swicth case.
            
            var data = {};




            $.ajax({
                method: "post",
                url: eb_setup_wizard.ajax_url,
                dataType: "json",
                data: {
                    'action': 'eb_setup_' + step,
                    // 'course_id': course_id,
                    // '_wpnonce_field': eb_admin_js_object.nonce,
                },
                success: function (response) {
    
                    //prepare response for user
                    if (response.success == 1) {
                        $('.eb-setup-content').html(response.data.content);

                    } else {
    
                    }
                }
            });
    
        });





        // ajax xall to save data and get new tab at the same time.
        // Clicking save continue
        $('.eb_setup_save_and_continue').click(function(){

            // Create loader.
            var current = $(this);
            var step = $(this).data('step');

            // get current step.
            // get next step.
            // get data which will be saved.

            // Creating swicth case.
            
            var data = {};

            switch ( step ) {
                case 'installtion_guide':
                    // Get required data and create array

                    data = { 'step' : step };

                    break;

                case 'mdl_plugin_config':
                    
                    data = { 'step' : step };
                    
                    break;
            
                case 'web_service':
                    // Course sync process.
                    // Call course sync callback and after completing the process, call this callback.

                    var mdl_url = '';
                    var mdl_token = '';
                    var mdl_lng_code = '';

                    data = { 'mdl_url' : mdl_url, 'mdl_token' : mdl_token, 'mdl_lng_code': mdl_lng_code, 'step' : step };

                    break;

                case 'user_and_course_sync':
                    // If user checkbox is clicked start user sync otherwise just procedd to next screen.
                    data = { 'step' : step };

                    break;


                default:
                    break;
            }



            var promises = ajax.call([{
                methodname: "eb_setup_save_and_continue",
                args: { 'data': data },
            }, ]);

            promises[0].done(function(response) {
                $("body").css("cursor", "default");
                

                $('.eb-setup-content').html(response.html);



                return response;
            }).fail(function(response) {
                $("body").css("cursor", "default");
                return 0;
            }); //promise end




        });








    // ajax xall to save data and get new tab at the same time.
        
            // Clicking save continue
        // 
        // 
        $('.eb_setup_save_and').click(function(){

            // Create loader.
            var current = $(this);
            var step = $(this).data('step');

            // get current step.
            // get next step.
            // get data which will be saved.

            // Creating swicth case.
            
            var data = {};

            switch ( step ) {
                case 'free_installtion_guide':
                    // Get required data and create array

                    data = { 'step' : step };



                    break;

                case 'test_connection':
                    var mdl_url = '';
                    var mdl_token = '';
                    var mdl_lng_code = '';

                    data = { 'mdl_url' : mdl_url, 'mdl_token' : mdl_token, 'mdl_lng_code': mdl_lng_code, 'step' : step };
                    
                    break;
            
                case 'course_sync':
                    // Course sync process.
                    // Call course sync callback and after completing the process, call this callback.

                    data = { 'step' : step };


                    break;

                case 'user_sync':
                    // If user checkbox is clicked start user sync otherwise just procedd to next screen.
                    data = { 'step' : step };

                    break;

                case 'free_recommended_settings':
                    // user account page selection and enable registration on user account
                    data = { 'step' : step };

                    break;


                case 'pro_initialize':
                    data = { 'step' : step };
                
                    break;


                case 'license':
                    data = { 'step' : step };
                
                    break;


                case 'wp_plugins':
                    data = { 'step' : step };
                
                    break;


                case 'mdl_plugins':
                    data = { 'step' : step };
                
                    break;


                case 'sso':
                    data = { 'step' : step };
                
                    break;


                case 'wi_products_sync':
                    data = { 'step' : step };
                
                    break;


                case 'pro_settings':
                    data = { 'step' : step };
                
                    break;


                default:
                    break;
            }



console.log( 'data ::: ' );
console.log( data );


            $.ajax({
                method: "post",
                url: eb_setup_wizard.ajax_url,
                dataType: "json",
                data: {
                    'action': 'eb_setup_save_and_continue',
                    // 'action': 'eb_setup_' + step,
                    'data': data,
                    // '_wpnonce_field': eb_admin_js_object.nonce,
                },
                success: function (response) {

                    //prepare response for user
                    if (response.success == 1) {
                        $('.eb-setup-content').html(response.data.content);

                    } else {
    
                    }
                }
            });


        });







    });
    

	// }
 //    return { init: load_settings };

        }
    };

});





