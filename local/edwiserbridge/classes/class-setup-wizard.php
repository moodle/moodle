<?php
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
 * Edwiser Bridge - WordPress and Moodle integration.
 * This file is responsible for WordPress connection related functionality.
 *
 * @package     local_edwiserbridge
 * @copyright   2021 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author      Wisdmlabs
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Handles API requests and response from WordPress.
 *
 * @package     local_edwiserbridge
 * @copyright   2021 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class eb_setup_wizard {

    /**
     * Hook in tabs.
     */
    public function __construct() {

    }


    /**
     * 
     */
    public function eb_setup_wizard_get_steps() {

        /**
         * Loop through the steps.
         * Ajax call for each of the steps and save.
         * step change logic.
         * load data on step change.
         * 
         */
        $steps = array(
            'installation_guide' => array(
                'name'        => 'Edwiser Bridge FREE plugin installation guide',
                'title'       => 'Edwiser Bridge FREE plugin installation guide',
                'function'    => 'eb_setup_installation_guide',
                'parent_step' => 'installation_guide',
                'priority'    => 10,
                'sub_step'    => 0,
            ),
            'mdl_plugin_config' => array(
                'name'        => 'Edwiser Bridge Moodle Plugin configuration',
                'title'       => 'Edwiser Bridge - Moodle Plugin configuration',
                'function'    => 'eb_setup_plugin_configuration',
                'parent_step' => 'mdl_plugin_config',
                'priority'    => 20,
                'sub_step'    => 0,
            ),
            'web_service' => array(
                'name'        => 'Setting up Web service',
                'title'       => 'Setting up Web service',
                'function'    => 'eb_setup_web_service',
                'parent_step' => 'web_service',
                'priority'    => 30,
                'sub_step'    => 0,
            ),
            'wordpress_site_details' => array(
                'name'        => 'WordPress site details',
                'title'       => 'WordPress site details',
                'function'    => 'eb_setup_wordpress_site_details',
                'parent_step' => 'wordpress_site_details',
                'priority'    => 40,
                'sub_step'    => 0,
            ),
            'check_permalink' => array(
                'name'        => 'Check permalink structure',
                'title'       => 'Check permalink structure',
                'function'    => 'eb_setup_check_permalink',
                'parent_step' => 'wordpress_site_details',
                'priority'    => 50,
                'sub_step'    => 0,
            ),
            'test_connection' => array(
                'name'        => 'Test connection between Moodle and WordPress',
                'title'       => 'Test connection between Moodle and WordPress',
                'function'    => 'eb_setup_test_connection',
                'parent_step' => 'wordpress_site_details',
                'priority'    => 60,
                'sub_step'    => 0,
            ),
            'user_and_course_sync' => array(
                'name'        => 'Setting up User and course sync',
                'title'       => 'Setting up User and course sync',
                'function'    => 'eb_setup_user_and_course_sync',
                'parent_step' => 'user_and_course_sync',
                'priority'    => 70,
                'sub_step'    => 0,
            ),
            'complete_details' => array(
                'name'        => 'Edwiser Bridge FREE Moodle plugin setup complete',
                'title'       => 'Edwiser Bridge FREE Moodle plugin setup complete',
                'function'    => 'eb_setup_complete_details',
                'parent_step' => 'user_and_course_sync',
                'priority'    => 80,
                'sub_step'    => 0,
            )
        );


        return $steps;
    }



    /**
     * Setup Wizard Steps HTML content
     */
    public function eb_setup_steps_html( $current_step = '' ) {
        global $CFG;

        $steps = $this->eb_setup_wizard_get_steps();

        /**
         * Get completed steps data.
         */
        $progress  = isset( $CFG->eb_setup_progress ) ? $CFG->eb_setup_progress : '';
        $completed = 1;

        if ( empty( $progress ) ) {
            $completed = 0;
        }

        if ( ! empty( $steps ) && is_array( $steps ) ) {

            ?>
            <ul class="eb-setup-steps">

                <?php
                foreach ( $steps as $key => $step ) {
                    if ( ! $step['sub_step'] ) {
                        $class = '';
                        $html  = '<span class="eb-setup-step-circle eb_setup_sidebar_progress_icons" > </span>';

                        if ( 1 === $completed ) {
                            $class = 'eb-setup-step-completed';
                            $html  = '<i class="fa-solid fa-circle-check eb_setup_sidebar_progress_icons"></i>';
                        }

                        if ( $current_step === $key ) {
                            $class = 'eb-setup-step-active';
                            $html  = '</i><i class="fa-solid fa-circle-chevron-right eb_setup_sidebar_progress_icons"></i>';
                            // $completed = 0;
                            
                        } 

                        if ( /*empty( $current_step ) &&*/ $key === $progress ) {
                            $completed = 0;
                        }

                        ?>
                        <li class='eb-setup-step  <?php echo ' eb-setup-step-' . $key . ' ' . $class . '-wrap'; ?>' >
                            <?php echo $html; ?>
                            <span class='eb-setup-steps-title <?php echo $class; ?>' data-step="<?php echo $key; ?>">
                                <?php echo $step['name']; ?>
                            </span>
                        </li>

                        <?php
                    } else{
                        if ( $key === $progress ) {
                            $completed = 0;
                        }
                    }
                }
                ?>
            </ul>
            <?php
        }
    }


    /**
     * Setup Wizard get step title.
     *
     * @param string $step Step name.
     */
    public function eb_get_step_title( $step ) {
        $steps = $this->eb_setup_wizard_get_steps();
        return isset( $steps[ $step ]['title'] ) ? $steps[ $step ]['title'] : '';
    }



    /**
     * Setup Wizard Page submission or refresh handler
     */
    public function eb_setup_handle_page_submission_or_refresh() {
        global $CFG;
        $steps = $this->eb_setup_wizard_get_steps();
        $step  = 'installation_guide';

        /**
         * Handle page refresh.
         */
        if ( isset( $_GET['current_step'] ) && ! empty( $_GET['current_step'] ) ) {
            $step = $_GET['current_step'];
        } elseif ( isset($CFG->eb_setup_progress) && !empty($CFG->eb_setup_progress) && !isset($step) ) {
            $step = $this->get_next_step($CFG->eb_setup_progress);
        } else{
            $step = 'installation_guide';
        }

        return $step;
    }



    /**
     * 
     */
    public function eb_setup_wizard_template( $step = 'installation_guide' ) {
        // Get current step.
        $content_class = "";

        $steps = $this->eb_setup_wizard_get_steps();
        $step  = $this->eb_setup_handle_page_submission_or_refresh();
        $title = $this->eb_get_step_title( $step );

        $this->setup_wizard_header( $title );

            // content area.
            // sidebar.
            ?>

            <div class='eb-setup-content-area'>

                <!-- Sidebar -->
                <div class='eb-setup-sidebar'>

                    <?php
                    $this->eb_setup_steps_html( $step );
                    ?>

                </div>

                <!-- content -->
                <div class="eb-setup-content <?php echo $content_class; ?>">

                    <?php
                    $function = $steps[ $step ]['function'];
                    $this->$function( 0 );
                    ?>

                </div>

            </div>

            <?php
                // sidebar progress.
            // Content.

        // Footer part.
        $this->setup_wizard_footer();
    }



    /**
     * Setup Wizard Header.
     */
    public function setup_wizard_header( $title = '' ) {
        global $CFG;

        $eb_plugin_url = '';

        ?>
        <!DOCTYPE html>
        <html >
        <head>
            <title><?php echo get_string( 'edwiserbridge', 'local_edwiserbridge' ); ?></title>
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" rel="stylesheet">
            <meta name="viewport" content="width=device-width" />
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        </head>


        <body class="wc-setup wp-core-ui ">

            <header class="eb-setup-wizard-header">

                <div class="eb-setup-header-logo">
                    <div class="eb-setup-header-logo-img-wrap">
                        <img src="<?php echo 'images/moodle-logo.png' ?>" />
                        <!-- <img src="<?php echo $CFG->dirroot . '/local/edwiserbridge/images/moodle-logo.png' ?>" /> -->
                        <!-- <img src="<?php echo  '../images/moodle-logo.png' ?>" /> -->
                    </div>
                </div>

                <div class="eb-setup-header-title-wrap">
                    <div class="eb-setup-header-title"><?php echo $title; ?></div>
                    <div class='eb-setup-close-icon'> <i class="fa-solid fa-xmark"></i> </div>

                </div>
            
            </header>
        <?php
    }

    /**
     * Setup Wizard Footer.
     */
    public function setup_wizard_footer() {
        ?>
            <footer class='eb-setup-wizard-footer'>

                <div class='eb-setup-footer-copyright'>
                    <?php echo get_string( 'setup_footer', 'local_edwiserbridge' ); ?>
                </div>

                <div class='eb-setup-footer-button'>
                    <a href='https://edwiser.org/contact-us/' target='_blank'>
                        <?php echo get_string( 'setup_contact_us', 'local_edwiserbridge' ); ?>
                    </a>
                </div>

                <div> <?php echo $this->eb_setup_close_setup(); ?> </div>

            </footer>

        </body>
    </html>


        <?php
    }


    public function get_next_step( $current_step ) {
        $steps = $this->eb_setup_wizard_get_steps();
        $step = '';
        $found_step = 0;

        foreach ($steps as $key => $value) {
            if ( $found_step ) {
                $step = $key;
                break;
            }

            if ( $current_step == $key ) {
                $found_step = 1;
            }
        }

        return $step;
    }


    public function get_prev_step( $current_step ) {

        $steps = $this->eb_setup_wizard_get_steps();
        $step = '';
        $found_step = 0;
        $prevkey = '';
        foreach ($steps as $key => $value) {
            if ( $current_step == $key ) {
                $found_step = 1;
            }

            if ( $found_step ) {
                $step = $prevkey;
                break;
            }

            $prevkey = $key;
        }

        return $step;
    }




    public function eb_setup_installation_guide( $ajax = 1 ) {

        if ( $ajax ) {
            ob_start();
        }
        $step = 'installation_guide';
        $is_next_sub_step  = 0;


        $next_step = $this->get_next_step( $step );
        ?>
        <div class="eb_setup_installation_guide es-w-80">
            <div>
                <p class="eb_setup_p"> <?php echo get_string( 'setup_installation_note1', 'local_edwiserbridge' ); ?> </p>

                <div class="eb_setup_p_wrap">

                    <p class="eb_setup_h2"> <i class="fa-solid fa-circle-chevron-right"></i> <?php echo get_string( 'modulename', 'local_edwiserbridge') . ' ' . get_string( 'setup_free', 'local_edwiserbridge') . ' ' . get_string( 'setup_wp_plugin', 'local_edwiserbridge' ); ?> </p>

                    <p class="eb_setup_h2"> <i class="fa-solid fa-circle-chevron-right"></i> <?php echo get_string( 'modulename', 'local_edwiserbridge') . ' ' . get_string( 'setup_free', 'local_edwiserbridge' ) . ' ' . get_string( 'setup_mdl_plugin', 'local_edwiserbridge' ); ?> </p>

                </div>


                <span class="eb_setup_p"> <?php echo get_string( 'setup_installation_note2', 'local_edwiserbridge' ); ?> </span>

                <div class="eb_setup_btn_wrap">
                    <button class="eb_setup_btn eb_setup_save_and_continue" data-step='<?php echo $step ?>' data-next-step='<?php echo $next_step ?>' data-is-next-sub-step='<?php echo $is_next_sub_step ?>'> <?php echo get_string( 'setup_continue_btn', 'local_edwiserbridge' ); ?> </button>
                </div>

            </div>

            <div>
                <div class='es-p-t-10'>
                    <div class='accordion'> <i class="fa-solid fa-circle-question"></i> <!-- <span class="dashicons dashicons-editor-help"></span> --> <?php echo get_string( 'setup_installation_faq', 'local_edwiserbridge' ); ?> <i class="fa-solid fa-chevron-down"></i> <i class="fa-solid fa-chevron-up"></i> <!-- <span class="dashicons dashicons-arrow-down-alt2"></span><span class="dashicons dashicons-arrow-up-alt2"></span> --></div>

                    <div class="panel">

                        <div>
                            <!-- <button class="eb_setup_sec_btn"> <?php echo get_string( 'setup_faq_download_plugin', 'local_edwiserbridge' ); ?> </button> -->
                            <a class="eb_setup_sec_btn" href='https://downloads.wordpress.org/plugin/edwiser-bridge.zip'> <?php echo get_string( 'setup_faq_download_plugin', 'local_edwiserbridge' ); ?> </a>
                        </div>

                        <p>
                            <p class='es-p-t-10'> <?php echo get_string( 'setup_faq_steps', 'local_edwiserbridge' ); ?> </p>

                            <ol>
                                <li class='es-p-b-10'> <?php echo get_string( 'setup_faq_step1', 'local_edwiserbridge' ); ?></li>
                                <li class='es-p-b-10'><?php echo get_string( 'setup_faq_step2', 'local_edwiserbridge' ); ?></li>
                                <li class='es-p-b-10'><?php echo get_string( 'setup_faq_step3', 'local_edwiserbridge' ); ?></li>
                                <li class='es-p-b-10'><?php echo get_string( 'setup_faq_step4', 'local_edwiserbridge' ); ?> 🙂</li>
                            </ol>

                        </p>
                    </div>
                </div>
            </div>
    
    
        </div>

        <?php

        if ( $ajax ) {
            return ob_get_clean();
             
        }
    }

    
   



    public function eb_setup_plugin_configuration($ajax = 1){
        global $CFG;

        if ( $ajax ) {
            ob_start();
        }

        $step = 'mdl_plugin_config';
        $is_next_sub_step  = 0;

        $setting_enabled = "style='color:#1AB900;'";
        $protocols = $CFG->webserviceprotocols;
        if ( in_array( 'rest', explode(',', $protocols) ) ) {
            $protocols = 1;
        }
        else{
            $protocols = 0;
        }
        $webservice = $CFG->enablewebservices === '1' ? 1 : 0;
        $password_policy = $CFG->passwordpolicy === '0' ? 1 : 0;
        $extended_char = $CFG->extendedusernamechars === '1' ? 1 : 0;

        if($protocols == 1 && $webservice == 1 && $password_policy == 1 && $extended_char == 1){
            $all_enabled = 1;
        }
        else{
            $all_enabled = 0;
        }

        $next_step = $this->get_next_step( $step );


        ?>
        <div class="eb_plugin_configuration es-w-80">
            <div>
                <p> <?php echo get_string( 'setup_mdl_plugin_note1', 'local_edwiserbridge' ); ?> </p>

                <div class="eb_plugin_configuration_checks">

                    <div class="eb_setup_h3 es-p-b-10">
                        <i class="fa-solid fa-circle-check eb_enable_rest_protocol" <?php echo $protocols === 1 ? $setting_enabled : '' ?> ></i> <?php echo get_string( 'no_1', 'local_edwiserbridge' ) . ". " . get_string( 'setup_mdl_plugin_check1', 'local_edwiserbridge'); ?>
                        <i class="fa-solid fa-info eb-tooltip es-info-icon"><span class='eb-tooltiptext'><?php echo get_string( 'enabling_rest_tip', 'local_edwiserbridge'); ?></span></i> 
                     </div>

                    <div class="eb_setup_h3 es-p-b-10">
                        <i class="fa-solid fa-circle-check eb_enable_web_service" <?php echo $webservice === 1 ? $setting_enabled : '' ?> ></i> <?php echo get_string( 'no_2', 'local_edwiserbridge' ) . ". " . get_string( 'setup_mdl_plugin_check2', 'local_edwiserbridge'); ?>
                        <i class="fa-solid fa-info eb-tooltip es-info-icon"><span class='eb-tooltiptext'><?php echo get_string( 'enabling_service_tip', 'local_edwiserbridge'); ?></span></i> 
                    </div>

                    <div class="eb_setup_h3 es-p-b-10">
                        <i class="fa-solid fa-circle-check eb_disable_pwd_policy" <?php echo $password_policy === 1 ? $setting_enabled : '' ?> ></i> <?php echo get_string( 'no_3', 'local_edwiserbridge' ) . ". " . get_string( 'setup_mdl_plugin_check3', 'local_edwiserbridge'); ?>
                        <i class="fa-solid fa-info eb-tooltip es-info-icon"><span class='eb-tooltiptext'><?php echo get_string( 'disable_passw_policy_tip', 'local_edwiserbridge'); ?></span></i>
                    </div>

                    <div class="eb_setup_h3">
                        <i class="fa-solid fa-circle-check eb_allow_extended_char" <?php echo $extended_char === 1 ? $setting_enabled : '' ?> ></i> <?php echo get_string( 'no_4', 'local_edwiserbridge' ) . ". " . get_string( 'setup_mdl_plugin_check4', 'local_edwiserbridge'); ?> 
                        <i class="fa-solid fa-info eb-tooltip es-info-icon"><span class='eb-tooltiptext'><?php echo get_string( 'allow_exte_char_tip', 'local_edwiserbridge'); ?></span></i>
                    </div>

                    <div class="eb_setup_settings_success_msg"> <i class="fa-solid fa-circle-check"></i> <?php echo get_string( 'setup_mdl_settings_success_msg', 'local_edwiserbridge' ); ?> </div>

                </div>

                <div <?php echo $all_enabled === 1 ? 'style="display:none;"' : '' ?> >
                    <span class="eb_enable_plugin_settings_label"> <?php echo get_string( 'setup_mdl_plugin_note2', 'local_edwiserbridge' ); ?> </span>

                    <button class="eb_setup_btn eb_enable_plugin_settings" data-step='<?php echo $step ?>' data-next-step='<?php echo $next_step ?>' data-is-next-sub-step='<?php echo $is_next_sub_step ?>' > <?php echo get_string( 'setup_enble_settings', 'local_edwiserbridge' ); ?> </button>
                </div>

                <div class="eb_setup_btn_wrap">
                    

                    <button class="eb_setup_btn eb_setup_save_and_continue" <?php echo $all_enabled === 1 ? 'style="display:initial;"' : '' ?> data-step='<?php echo $step ?>' data-next-step='<?php echo $next_step ?>' data-is-next-sub-step='<?php echo $is_next_sub_step ?>' > <?php echo get_string( 'setup_continue_btn', 'local_edwiserbridge' ); ?> </button>


                </div>

            </div>

        </div>

        <?php

        if ( $ajax ) {
            return ob_get_clean();
        }
    }




    /**
     *
     */
    public function eb_setup_web_service( $ajax = 1 ){
        global $CFG;

        if ( $ajax ) {
            ob_start();
        }

        $step = 'web_service';
        $disable = 'disabled';
        $is_next_sub_step  = 0;

        $next_step = $this->get_next_step( $step );

        $existingservices = eb_get_existing_services();
        $selectedservice =  isset( $CFG->ebexistingserviceselect ) ? $CFG->ebexistingserviceselect : '';
 

        ?>
        <div class="eb_setup_web_service es-w-80">
            <div>
                <p> <?php echo get_string( 'setup_web_service_note1', 'local_edwiserbridge' ); ?> </p>

                <div class="eb_setup_p_wrap">

                    <div class="eb_setup_h2"> <i class="fa-solid fa-circle-chevron-right"></i> <?php echo get_string( 'setup_web_service_h1', 'local_edwiserbridge'); ?> </div>

                    <div class="eb_setup_separator">
                        <div class="eb_setup_hr"><hr></div>
                        <div> <span> <?php echo get_string( 'or', 'local_edwiserbridge'); ?> </span> </div>
                        <div class="eb_setup_hr"><hr></div>
                    </div>

                    <div class="eb_setup_h2"> <i class="fa-solid fa-circle-chevron-right"></i> <?php echo get_string( 'setup_web_service_h2', 'local_edwiserbridge'); ?> </div>

                </div>

                <div>

                    <div class="eb_setup_conn_url_inp_wrap">
                        <p>
                            <label class="eb_setup_h2"> <?php echo get_string( 'sum_web_services', 'local_edwiserbridge' ); ?></label>
                            <i class="fa-solid fa-info eb-tooltip es-info-icon"><span class='eb-tooltiptext'><?php echo get_string( 'web_service_tip', 'local_edwiserbridge'); ?></span></i>
                        </p>

                        <select name="eb_setup_web_service_list" class="eb_setup_inp eb_setup_web_service_list" >
                            <?php
                            foreach ( $existingservices as $key => $value ) {
                                $selected = '';
                                if ( $key == $selectedservice ) {
                                    $selected = 'selected';
                                    $disable  = '';
                                }
                            ?>
                                <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        

                    </div>

                    <div class="eb_setup_conn_url_inp_wrap eb_setup_web_service_name_wrap">
                        <p>
                            <label class="eb_setup_h2"> <?php echo get_string( 'new_service_inp_lbl', 'local_edwiserbridge' ); ?></label>
                            <i class="fa-solid fa-info eb-tooltip es-info-icon"><span class='eb-tooltiptext'><?php echo get_string( 'name_web_service_tip', 'local_edwiserbridge'); ?></span></i>
                        </p>
                        <input class="eb_setup_inp eb_setup_web_service_name" id="eb_setup_web_service_name" name="eb_setup_web_service_name" type="text" >
                    </div>

                    <div class="eb_setup_btn_wrap">
                        <button class="eb_setup_btn eb_setup_web_service_btn eb_setup_save_and_continue <?php echo $disable; ?>" data-step='<?php echo $step ?>' data-next-step='<?php echo $next_step ?>' data-is-next-sub-step='<?php echo $is_next_sub_step ?>' <?php echo $disable; ?>> <?php echo get_string( 'setup_continue_btn', 'local_edwiserbridge' ); ?> </button>
                    </div>

                </div>

            </div>

        </div>

        <?php
        if ( $ajax ) {
            return ob_get_clean();
        }
    }





    public function eb_setup_wordpress_site_details( $ajax = 1 ) {
        global $CFG;
        if ( $ajax ) {
            ob_start();
        }
        $step     = 'wordpress_site_details';
        $class    = 'eb_setup_wp_site_details_wrap';
        $btnclass = 'disabled';
        $is_next_sub_step  = 1;
        $sites = get_site_list();
        // $sites1 = get_connection_settings();

        $next_step = $this->get_next_step( $step );
        $prevstep = $this->get_prev_step( $step );
        $prevurl = $CFG->wwwroot . '/local/edwiserbridge/setup_wizard.php?current_step=' . $prevstep;

        $sitename =  isset( $CFG->eb_setup_wp_site_name ) ? $CFG->eb_setup_wp_site_name : '';

        $wpsites = get_connection_settings();
        $wpsites = $wpsites['eb_connection_settings'];

        if ( ! empty( $sitename ) ) {
            $slectedname = '';
            $selectedurl = '';

            if ( isset( $wpsites[$sitename] ) ) {
                $slectedname = $sitename;
                $selectedurl = $wpsites[$sitename]['wp_url'];
                $class       = '';
                $disabled    = '';
            }
        }

        $options = '';
        foreach ( $sites as $key => $value ) {
            $selected = '';
            if ( $key == $sitename ) {
                $selected = 'selected';
            }

            $name = '';
            $url  = '';
            if ( isset( $wpsites[$key] ) ) {
                $name  = $value;
                $url   = $wpsites[$key]['wp_url'];
                $class = '';
            }




            $options .=  '<option data-name="'. $value .'" data-url="'. $url .'" value="' . $key . '" '. $selected . '>'. $value .'</option>';
        }
        


        ?>
        <div class="eb_setup_wordpress_site_details es-w-80">
            <div>

                <div>

                    <div class="eb_setup_conn_url_inp_wrap">
                        <p> <?php echo get_string( 'setup_wp_site_note1', 'local_edwiserbridge' ); ?> </p>
                        <p>
                            <label class="eb_setup_h2"> <?php echo get_string( 'setup_wp_site_dropdown', 'local_edwiserbridge' ); ?></label>
                            <i class="fa-solid fa-info eb-tooltip es-info-icon"><span class='eb-tooltiptext'><?php echo get_string( 'wp_site_tip', 'local_edwiserbridge'); ?></span></i>
                        </p>

                        <select name="eb_setup_wp_sites" class="eb_setup_inp eb_setup_wp_sites" >
                            <option value=""><?php echo get_string( 'select', 'local_edwiserbridge' ); ?></option>
                            <option value="create"><?php echo get_string( 'create_wp_site', 'local_edwiserbridge' ); ?></option>
                            <?php echo $options; ?>
                        </select>
                        

                    </div>

                    <div class="eb_setup_wp_site_details_inp eb_setup_conn_url_inp_wrap <?php echo $class; ?>">
                        <span> <?php echo get_string( 'setup_wp_site_note2', 'local_edwiserbridge' ); ?> </span>

                        <p>
                            <label class="eb_setup_h2"> <?php echo get_string( 'name', 'local_edwiserbridge' ); ?></label>
                            <i class="fa-solid fa-info eb-tooltip es-info-icon"><span class='eb-tooltiptext'><?php echo get_string( 'wp_site_name_tip', 'local_edwiserbridge'); ?></span></i>
                        </p>
                        <input class="eb_setup_inp eb_setup_site_name" id="eb_setup_site_name" name="eb_setup_site_name" type="text" value='<?php echo $slectedname; ?>' >
                    </div>

                    <div class="eb_setup_wp_site_details_inp eb_setup_conn_url_inp_wrap <?php echo $class; ?>">
                        <p>
                            <label class="eb_setup_h2"> <?php echo get_string( 'url', 'local_edwiserbridge' ); ?></label>
                            <i class="fa-solid fa-info eb-tooltip es-info-icon"><span class='eb-tooltiptext'><?php echo get_string( 'wp_site_url_tip', 'local_edwiserbridge'); ?></span></i>
                        </p>
                        <input class="eb_setup_inp eb_setup_site_url" id="eb_setup_site_url" name="eb_setup_site_url" type="text" value='<?php echo $selectedurl; ?>' >
                    </div>

                    <div class="eb_setup_btn_wrap">
                        <a class="eb_setup_sec_btn" href="<?php echo $prevurl; ?>"> <?php echo get_string( 'back', 'local_edwiserbridge' ); ?> </a>
                        <button class="eb_setup_btn  eb_setup_wp_details_btn eb_setup_save_and_continue" data-step='<?php echo $step ?>' data-next-step='<?php echo $next_step ?>' data-is-next-sub-step='<?php echo $is_next_sub_step ?>' > <?php echo get_string( 'setup_continue_btn', 'local_edwiserbridge' ); ?> </button>
                    </div>

                </div>

            </div>

        </div>

        <?php

        if ( $ajax ) {
            return ob_get_clean();
        }
    }



    public function eb_setup_check_permalink( $ajax = 1 ){
        global $CFG;
        if ( $ajax ) {
            ob_start();
        }

        $step      = 'check_permalink';
        $is_next_sub_step  = 0;
        $next_step = $this->get_next_step( $step );
        $prevstep = $this->get_prev_step( $step );
        $prevurl = $CFG->wwwroot . '/local/edwiserbridge/setup_wizard.php?current_step=' . $prevstep;

        $sitename =  $CFG->eb_setup_wp_site_name;

        // $sites = get_site_list();
        $sites = get_connection_settings();
        $sites = $sites['eb_connection_settings'];

        $url = '';
        if ( isset($sites[$sitename])) {
            $url = $sites[$sitename]['wp_url'];
        }

        if(substr($url , -1)=='/') {
            $url = $url . 'wp-admin/options-permalink.php';
        } else {
            $url = $url . '/wp-admin/options-permalink.php';
        }


        // $url = $url . '/wp-admin/options-permalink.php';

        ?>
        <div class='eb_setup_check_permalink es-w-80'>
            <div>

                <div>
                    <p class=""> <?php echo get_string( 'setup_permalink_note1', 'local_edwiserbridge') . '<b>' . get_string( 'es_postname', 'local_edwiserbridge') . '</b>' ; ?> </p>
                    <p class="">
                    <?php echo get_string( 'setup_permalink_click', 'local_edwiserbridge') . '  <a class="es_text_links" target="_blank" href="' . $url . '">' . $url . '</a>  ' . get_string( 'setup_permalink_note2', 'local_edwiserbridge') ; ?> </p>
                    <div class=""> <?php echo get_string( 'setup_permalink_note3', 'local_edwiserbridge'); ?> </div>
                </div>


                <div>

                    <div class="eb_setup_btn_wrap">
                        <a class="eb_setup_sec_btn" href="<?php echo $prevurl; ?>"> <?php echo get_string( 'back', 'local_edwiserbridge' ); ?> </a>
                        <!-- <button class="eb_setup_sec_btn"> <?php echo get_string( 'back', 'local_edwiserbridge' ); ?> </button> -->
                        <button class="eb_setup_btn eb_setup_save_and_continue" data-step='<?php echo $step ?>' data-next-step='<?php echo $next_step ?>' data-is-next-sub-step='<?php echo $is_next_sub_step ?>' > <?php echo get_string( 'confirmed', 'local_edwiserbridge' ); ?> </button>
                    </div>

                </div>

            </div>

        </div>

        <?php

        if ( $ajax ) {
            return ob_get_clean();
            
        }
    }



    public function eb_setup_test_connection($ajax = 1){
        global $CFG;
        if ( $ajax ) {
            ob_start();
        }
        $step = 'test_connection';
        $is_next_sub_step  = 1;
        $sitename =  $CFG->eb_setup_wp_site_name;

        // $sites = get_site_list();
        $sites = get_connection_settings();
        $sites = $sites['eb_connection_settings'];

        $name = '';
        $url = '';
        if ( isset($sites[$sitename])) {
            $name = $sitename;
            $url = $sites[$sitename]['wp_url'];
        }

        $next_step = $this->get_next_step( $step );

        $prevstep = $this->get_prev_step( $step );
        $prevurl = $CFG->wwwroot . '/local/edwiserbridge/setup_wizard.php?current_step=' . $prevstep;

        ?>
        <div class="eb_setup_wordpress_site_details es-w-80">
            <div>

                <div class=" eb_setup_conn_url_inp_wrap">
                    <span> <?php echo get_string( 'wp_site_details_note', 'local_edwiserbridge' ); ?> </span>

                    <p>
                        <label class="eb_setup_h2"> <?php echo get_string( 'name', 'local_edwiserbridge' ); ?></label>
                        <i class="fa-solid fa-info eb-tooltip es-info-icon"><span class='eb-tooltiptext'><?php echo get_string( 'wp_site_name_tip', 'local_edwiserbridge'); ?></span></i>
                    </p>
                    <input class="eb_setup_inp eb_setup_site_name" name="eb_setup_site_name" type="text" value="<?php echo $name; ?>" disabled>
                </div>

                <div class="eb_setup_conn_url_inp_wrap">
                    <p>
                        <label class="eb_setup_h2"> <?php echo get_string( 'url', 'local_edwiserbridge' ); ?></label>
                        <i class="fa-solid fa-info eb-tooltip es-info-icon"><span class='eb-tooltiptext'><?php echo get_string( 'wp_site_url_tip', 'local_edwiserbridge'); ?></span></i>
                    </p>
                    <input class="eb_setup_inp eb_setup_site_url" name="eb_setup_site_url" type="url" value="<?php echo $url; ?>" disabled>

                    <div class="eb_setup_test_conn_resp_msg"></div>

                </div>

                <div class="eb_setup_btn_wrap">
                    <a class="eb_setup_sec_btn" href="<?php echo $prevurl; ?>"> <?php echo get_string( 'back', 'local_edwiserbridge' ); ?> </a>

                    <!-- <button class="eb_setup_sec_btn"> <?php echo get_string( 'back', 'local_edwiserbridge' ); ?> </button> -->

                    <button class="eb_setup_btn eb_setup_test_connection_btn" data-step='<?php echo $step ?>' data-next-step='<?php echo $next_step ?>' data-is-next-sub-step='<?php echo $is_next_sub_step ?>' > <?php echo get_string( 'wp_test_conn_btn', 'local_edwiserbridge' ); ?> </button>

                    <button class="eb_setup_btn eb_setup_save_and_continue eb_setup_test_connection_continue_btn" data-step='<?php echo $step ?>' data-next-step='<?php echo $next_step ?>' data-is-next-sub-step='<?php echo $is_next_sub_step ?>' > <?php echo get_string( 'setup_continue_btn', 'local_edwiserbridge' ); ?> </button>
                </div>

            </div>

        </div>

        <?php

        if ( $ajax ) {
            return ob_get_clean();
        }
    }




    public function eb_setup_user_and_course_sync($ajax = 1) {
        global $CFG;

        if ( $ajax ) {
            ob_start();
        }
        $step     = 'user_and_course_sync';
        $is_next_sub_step  = 1;

        $next_step = $this->get_next_step( $step );

        $prevstep = $this->get_prev_step( $step );
        $prevurl = $CFG->wwwroot . '/local/edwiserbridge/setup_wizard.php?current_step=' . $prevstep;
        $nexturl = $CFG->wwwroot . '/local/edwiserbridge/setup_wizard.php?current_step=' . $next_step;

        $synchsettings = isset($CFG->eb_synch_settings) ? unserialize($CFG->eb_synch_settings) : array();
        $sitename =  $CFG->eb_setup_wp_site_name;
        if(isset($synchsettings[$sitename])){
        
            $data = $synchsettings[$sitename];
            $oldsettings = array(
                "course_enrollment"    => isset($data['course_enrollment']) ? $data['course_enrollment'] : 0,
                "course_un_enrollment" => isset($data['course_un_enrollment']) ? $data['course_un_enrollment'] : 0,
                "user_creation"        => isset($data['user_creation']) ? $data['user_creation'] : 0,
                "user_deletion"        => isset($data['user_deletion']) ? $data['user_deletion'] : 0,
                "course_creation"      => isset($data['course_creation']) ? $data['course_creation'] : 0,
                "course_deletion"      => isset($data['course_deletion']) ? $data['course_deletion'] : 0,
                "user_updation"        => isset($data['user_updation']) ? $data['user_updation'] : 0,
            );
            $sum = array_sum($oldsettings);
        }
        else{
            $oldsettings = array(
                "course_enrollment"    => 1,
                "course_un_enrollment" => 1,
                "user_creation"        => 1,
                "user_deletion"        => 1,
                "course_creation"      => 1,
                "course_deletion"      => 1,
                "user_updation"        => 1,
            );
            $sum = 7;
            $recomended = 1;
        }
        
        ?>
        <div class="eb_setup_user_and_course_sync es-p-t-b-30 es-w-80">
            <div>

                <div>

                    <p> <?php echo get_string( 'setup_sync_note1', 'local_edwiserbridge' ); ?> </p>

                    <div class="eb_setup_inp_wrap">
                        <!-- <input type="checkbox" name='eb_setup_sync_all' id='eb_setup_sync_all' > -->
                        <label class='esw-cb-container' >
                            <input type='checkbox' name='eb_setup_sync_all' id='eb_setup_sync_all' <?php echo $sum == 7 ? 'checked' : '' ?>>
                            <span class='esw-cb-checkmark'></span>
                            <span class="eb_setup_h2"> <?php echo get_string( 'select_all', 'local_edwiserbridge' ); ?> <?php echo get_string( 'recommended', 'local_edwiserbridge' ); ?></span>
                        </label>

                    </div>

                    <hr>

                    <div class="eb_setup_inp_wrap">
                        <!-- <input type="checkbox" class="eb_setup_sync_cb" name='eb_setup_sync_user_enrollment' id='eb_setup_sync_user_enrollment'> -->
                        <label class='esw-cb-container' >
                            <input type='checkbox' class="eb_setup_sync_cb" name='eb_setup_sync_user_enrollment' id='eb_setup_sync_user_enrollment' <?php echo $oldsettings['course_enrollment'] ? 'checked' : '' ?>>
                            <span class='esw-cb-checkmark'></span>
                            <span class="eb_setup_h2"> <?php echo get_string( 'user_enrollment', 'local_edwiserbridge' ); ?></span>
                            <i class="fa-solid fa-info eb-tooltip es-info-icon"><span class='eb-tooltiptext'><?php echo get_string( 'user_enrollment_tip', 'local_edwiserbridge'); ?></span></i>
                        </label>                     
                        
                    </div>

                    <div class="eb_setup_inp_wrap">
                        <!-- <input type="checkbox" class="eb_setup_sync_cb" name='eb_setup_sync_user_unenrollment' id='eb_setup_sync_user_unenrollment'> -->
                        <label class='esw-cb-container' >
                            <input type='checkbox' class='eb_setup_sync_cb' name='eb_setup_sync_user_unenrollment' id='eb_setup_sync_user_unenrollment' <?php echo $oldsettings['course_un_enrollment'] ? 'checked' : '' ?>>
                            <span class='esw-cb-checkmark'></span>
                            <span class="eb_setup_h2"> <?php echo get_string( 'user_unenrollment', 'local_edwiserbridge' ); ?></span>
                            <i class="fa-solid fa-info eb-tooltip es-info-icon"><span class='eb-tooltiptext'><?php echo get_string( 'user_unenrollment_tip', 'local_edwiserbridge'); ?></span></i>
                        </label>
                        
                    </div>

                    <div class="eb_setup_inp_wrap">
                        <!-- <input type="checkbox" class="eb_setup_sync_cb" name='eb_setup_sync_user_creation' id='eb_setup_sync_user_creation'> -->
                        <label class='esw-cb-container' >
                            <input type='checkbox' class="eb_setup_sync_cb" name='eb_setup_sync_user_creation' id='eb_setup_sync_user_creation' <?php echo $oldsettings['user_creation'] ? 'checked' : '' ?>>
                            <span class='esw-cb-checkmark'></span>
                            <span class="eb_setup_h2"> <?php echo get_string( 'user_creation', 'local_edwiserbridge' ); ?></span>
                            <i class="fa-solid fa-info eb-tooltip es-info-icon"><span class='eb-tooltiptext'><?php echo get_string( 'user_creation_tip', 'local_edwiserbridge'); ?></span></i>
                        </label>
                        
                    </div>

                    <div class="eb_setup_inp_wrap">
                        <!-- <input type="checkbox" class="eb_setup_sync_cb" name='eb_setup_sync_user_deletion' id='eb_setup_sync_user_deletion'> -->
                        <label class='esw-cb-container' >
                            <input type='checkbox' class="eb_setup_sync_cb" name='eb_setup_sync_user_deletion' id='eb_setup_sync_user_deletion' <?php echo $oldsettings['user_deletion'] ? 'checked' : '' ?>>
                            <span class='esw-cb-checkmark'></span>
                            <span class="eb_setup_h2"> <?php echo get_string( 'user_deletion', 'local_edwiserbridge' ); ?></span>
                            <i class="fa-solid fa-info eb-tooltip es-info-icon"><span class='eb-tooltiptext'><?php echo get_string( 'user_deletion_tip', 'local_edwiserbridge'); ?></span></i>
                        </label>

                    </div>

                    <div class="eb_setup_inp_wrap">
                        <!-- <input type="checkbox" class="eb_setup_sync_cb" name='eb_setup_sync_user_update' id='eb_setup_sync_user_update'> -->
                        <label class='esw-cb-container' >
                            <input type='checkbox' class="eb_setup_sync_cb" name='eb_setup_sync_user_update' id='eb_setup_sync_user_update' <?php echo $oldsettings['user_updation'] ? 'checked' : '' ?>>
                            <span class='esw-cb-checkmark'></span>
                            <span class="eb_setup_h2"> <?php echo get_string( 'user_update', 'local_edwiserbridge' ); ?></span>
                            <i class="fa-solid fa-info eb-tooltip es-info-icon"><span class='eb-tooltiptext'><?php echo get_string( 'user_update_tip', 'local_edwiserbridge'); ?></span></i>
                        </label>

                        
                    </div>

                    <div class="eb_setup_inp_wrap">
                        <!-- <input type="checkbox" class="eb_setup_sync_cb" name='eb_setup_sync_course_creation' id='eb_setup_sync_course_creation'> -->
                        <label class='esw-cb-container' >
                            <input type='checkbox' class='eb_setup_sync_cb' name='eb_setup_sync_course_creation' id='eb_setup_sync_course_creation' <?php echo $oldsettings['course_creation'] ? 'checked' : '' ?>>
                            <span class='esw-cb-checkmark'></span>
                            <span class="eb_setup_h2"> <?php echo get_string( 'course_creation', 'local_edwiserbridge' ); ?></span>
                            <i class="fa-solid fa-info eb-tooltip es-info-icon"><span class='eb-tooltiptext'><?php echo get_string( 'course_creation_tip', 'local_edwiserbridge'); ?></span></i>
                        </label>
                        

                    </div>

                    <div class="eb_setup_inp_wrap">
                        <!-- <input type="checkbox" class="eb_setup_sync_cb" name='eb_setup_sync_course_deletion' id='eb_setup_sync_course_deletion'> -->
                        <label class='esw-cb-container' >
                            <input type='checkbox' class="eb_setup_sync_cb" name='eb_setup_sync_course_deletion' id='eb_setup_sync_course_deletion' <?php echo $oldsettings['course_deletion'] ? 'checked' : '' ?>>
                            <span class='esw-cb-checkmark'></span>
                            <span class="eb_setup_h2"> <?php echo get_string( 'course_deletion', 'local_edwiserbridge' ); ?></span>
                            <i class="fa-solid fa-info eb-tooltip es-info-icon"><span class='eb-tooltiptext'><?php echo get_string( 'course_deletion_tip', 'local_edwiserbridge'); ?></span></i>
                        </label>

                    </div>


                    <div class="eb_setup_btn_wrap">
                        <a class="eb_setup_sec_btn" href="<?php echo $prevurl; ?>"> <?php echo get_string( 'back', 'local_edwiserbridge' ); ?> </a>
                        <a class="eb_setup_sec_btn" href="<?php echo $nexturl; ?>"> <?php echo get_string( 'skip', 'local_edwiserbridge' ); ?> </a>

                        <!-- <button class="eb_setup_sec_btn"> <?php echo get_string( 'back', 'local_edwiserbridge' ); ?> </button> -->
                        <!-- <button class="eb_setup_sec_btn"> <?php echo get_string( 'skip', 'local_edwiserbridge' ); ?> </button> -->
                        <button class="eb_setup_btn eb_setup_save_and_continue" data-step='<?php echo $step ?>' data-next-step='<?php echo $next_step ?>' data-is-next-sub-step='<?php echo $is_next_sub_step ?>' > <?php echo get_string( 'setup_continue_btn', 'local_edwiserbridge' ); ?> </button>
                    </div>

                </div>

            </div>

        </div>

        <?php

        if ( $ajax ) {
            return ob_get_clean();            
        }

    }



    public function eb_setup_complete_details( $ajax = 1 ) {
        global $CFG;

        if ( $ajax ) {
            ob_start();
        }
        $step = 'complete_details';
        $is_next_sub_step  = 0;

        $next_step = $this->get_next_step( $step );
        $sitename =  $CFG->eb_setup_wp_site_name;

        $sites = get_connection_settings();
        $sites = $sites['eb_connection_settings'];


        $url    = $CFG->wwwroot;
        $wp_url = '';
        $token  = '';
        if (isset($sites[$sitename])) {
            $wp_url = $sites[$sitename]['wp_url'];
            $token  = $sites[$sitename]['wp_token'];
        }

        $prevstep = $this->get_prev_step( $step );
        $prevurl = $CFG->wwwroot . '/local/edwiserbridge/setup_wizard.php?current_step=' . $prevstep;


        if(substr($wp_url , -1)=='/') {
            $wp_url = $wp_url . 'wp-admin/admin.php?page=eb-setup-wizard&current_step=test_connection';
        } else {
            $wp_url = $wp_url . '/wp-admin/admin.php?page=eb-setup-wizard&current_step=test_connection';
        }


        ?>
        <div class="eb_setup_complete_details es-w-80">
            <div>

                <div>

                    <span class='eb_setup_h2' > <?php echo get_string( 'what_next', 'local_edwiserbridge'); ?> </span>
                    <div class='' > <?php echo get_string( 'setup_completion_note1', 'local_edwiserbridge'); ?> </div>

                </div>


                <div class="eb_setup_complete_card_wrap">

                    <div class="eb_setup_h2"> <i class="fa-solid fa-circle-chevron-right"></i> <?php echo get_string( 'setup_completion_note2', 'local_edwiserbridge'); ?> </div>

                    <div class="eb_setup_complete_cards" >

                        <div class="eb_setup_complete_card eb_setup_copy" data-copy='<?php echo $url; ?>'>
                            <div>
                                <span class="eb_setup_h2"><?php echo get_string( 'mdl_url', 'local_edwiserbridge'); ?></span>
                                <div class="eb_setup_copy_url"> <?php echo $url; ?> </div>
                            </div>
                            <div class="eb_setup_copy_icon" data-copy='<?php echo $url; ?>' ><i class="fa-solid fa-copy"></i></div>
                        </div>

                        <div class="eb_setup_complete_card eb_setup_copy" data-copy='<?php echo $token; ?>'>
                            <div>
                                <span class="eb_setup_h2"><?php echo get_string( 'wp_token', 'local_edwiserbridge'); ?></span>
                                <div class="eb_setup_copy_token" style="word-break:break-all;"> <?php echo $token; ?> </div>
                            </div>
                            <div class="eb_setup_copy_icon" data-copy='<?php echo $token; ?>' ><i class="fa-solid fa-copy"></i></div>

                        </div>

                        <div class='eb_setup_complete_card eb_setup_copy' data-copy='<?php echo $CFG->lang; ?>'>
                            <div>
                                <span class='eb_setup_h2'><?php echo get_string( 'eb_mform_lang_desc', 'local_edwiserbridge'); ?></span>
                                <div class="eb_setup_copy_lang" > <?php echo $CFG->lang; ?> </div>
                            </div>
                            <div class="eb_setup_copy_icon" data-copy='<?php echo $CFG->lang; ?>' ><i class="fa-solid fa-copy"></i></div>

                        </div>

                    </div>


                    <div class="eb_setup_separator">
                        <div class="eb_setup_hr"><hr></div>
                        <div> <span> <?php echo get_string( 'or', 'local_edwiserbridge'); ?> </span> </div>
                        <div class="eb_setup_hr"><hr></div>
                    </div>


                    <p class="eb_setup_h2"> <i class="fa-solid fa-circle-chevron-right"></i> <?php echo get_string( 'setup_completion_note3', 'local_edwiserbridge'); ?> </p>

                    <button class="eb_setup_sec_btn eb_setup_download_creds"> <?php echo get_string( 'mdl_edwiser_bridge_txt_download', 'local_edwiserbridge' ); ?> </button>

                </div>


                <div>

                    <p class=""> <?php echo get_string( 'setup_completion_note4', 'local_edwiserbridge'); ?> </p>
                    

                    <div class="eb_setup_btn_wrap">

                        <!-- <form method='POST'> -->
                        <a class="eb_setup_sec_btn" href="<?php echo $prevurl; ?>"> <?php echo get_string( 'back', 'local_edwiserbridge' ); ?> </a>
                        <a class='eb_setup_btn eb_redirect_to_wp' target='_blank' href="<?php echo $wp_url; ?>" data-step='<?php echo $step ?>' data-next-step='<?php echo $next_step ?>' data-is-next-sub-step='<?php echo $is_next_sub_step ?>'> <?php echo get_string( 'continue_wp_wizard_btn', 'local_edwiserbridge' ); ?> </a>

                        <a style='display: none;' class='eb_setup_btn eb_redirect_to_wp_btn' target='_blank' href="<?php echo $wp_url; ?>"> <?php echo get_string( 'setup_continue_btn', 'local_edwiserbridge' ); ?> </a>
                    </div>

                </div>

            </div>

            <div> <?php echo $this->eb_setup_redirection_popup(); ?> </div>
            <div> <?php echo $this->eb_setup_completion_popup(); ?> </div>


        </div>

        <?php

        if ( $ajax ) {
            return ob_get_clean();
        }
    }






    /**
     * Setup Wizard close setup.
     */
    public function eb_setup_close_setup() {
        global $CFG;
        ob_start();
        ?>
        <div class='eb_setup_popup_content_wrap' style='display: none;'>
            <div class='eb_setup_popup_content'>

                <div class=''>
                    <p> <i class="fa-solid fa-triangle-exclamation eb_setup_pupup_warning_icon"></i> </p>

                    <p class='eb_setup_h2'> <?php echo get_string( 'close_quest', 'local_edwiserbridge'); ?></p>

                    <div class="eb_setup_user_sync_btn_wrap">
                        <a href=' <?php echo $CFG->wwwroot; ?>' class='eb_setup_sec_btn' > <?php echo get_string( 'yes', 'local_edwiserbridge'); ?> </a>
                        <button class='eb_setup_sec_btn eb_setup_do_not_close'> <?php echo get_string( 'no', 'local_edwiserbridge'); ?> </button>
                    </div>

                </div>

                <div>
                    <fieldset>
                        <legend> <?php echo get_string( 'note', 'local_edwiserbridge' ); ?> </legend>
                        <div>
                            <?php echo get_string( 'close_note', 'local_edwiserbridge' ); ?>
                        </div>
                    </fieldset>
                </div>

            </div>
        </div>

        <?php
        return ob_get_clean();
    }



    /**
     * Setup Wizard close setup.
     */
    public function eb_setup_redirection_popup() {
        global $CFG;
        ob_start();
        ?>
        <div class='eb_setup_wp_redirection_popup' style='display: none;'>
            <div class='eb_setup_popup_content'>

                <div class='eb_setup_h2 es-p-t-20'>
                    <?php echo 'Redirecting to WordPress Setup wizard...'; ?>
                </div>

                <div class='eb_setup_product_sync_progress_images'>

                    <div class='eb_setup_users_sync_wp_img'>
                        <img src="<?php echo 'images/moodle-logo.png'; ?>" />
                    </div>

                    <div class='eb_setup_product_sync_progress_arrows'>
                        <div class="animated  animated--on-hover mt-2">
                            <span class="animated__text">
                                <i class="fa-solid fa-angle-right" style='color:#bedbe2;width:7px;'></i>
                                <i class="fa-solid fa-angle-right" style='color:#76bccc;width:7px;'></i>
                                <i class="fa-solid fa-angle-right" style='color:#5abec3;width:7px;'></i>
                                <i class="fa-solid fa-angle-right" style='color:#14979d;width:7px;'></i>
                                <i class="fa-solid fa-angle-right" style='color:#007075;width:7px;'></i>
                            </span>
                        </div>
                    </div>

                    <div class='eb_setup_users_sync_mdl_img'>
                        <img src="<?php echo 'images/wp-logo.png'; ?>" />
                    </div>

                </div>
            </div>

        </div>

        <?php
        return ob_get_clean();
    }




    /**
     * Setup Wizard close setup.
     */
    public function eb_setup_completion_popup() {
        global $CFG;
        ob_start();
        ?>
        <div class='eb_setup_wp_completion_success_popup' style='display: none;'>
            <div class='eb_setup_popup_content'>

                <div class=''>
                    <p><i class="fa-solid fa-circle-check eb_setup_pupup_success_icon"></i> </p>

                    <p class="eb-completion-succ-h"> <?php echo get_string( 'setup_completion_note5', 'local_edwiserbridge' ); ?></p>

                </div>

            </div>

        </div>

        <?php
        return ob_get_clean();
    }



}
