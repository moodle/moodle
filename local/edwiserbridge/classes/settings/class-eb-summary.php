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
 * Settings mod form
 *
 * @package     local_edwiserbridge
 * @copyright   2021 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author      Wisdmlabs
 */

defined('MOODLE_INTERNAL') || die();
require_once("$CFG->libdir/formslib.php");

/**
 * form shown while adding Edwiser Bridge settings.
 */
class edwiserbridge_summary_form extends moodleform {
    /**
     * Defining summary form.
     */
    public function definition() {
        global $CFG;

        $servicename   = '';
        $pluginsvdata  = $this->get_plugin_version_data();
        $mform         = $this->_form;
        $token         = isset($CFG->edwiser_bridge_last_created_token) ? $CFG->edwiser_bridge_last_created_token : ' - ';
        $service       = isset($CFG->ebexistingserviceselect) ? $CFG->ebexistingserviceselect : '';
        $missingcapmsg = '<span class="summ_success" style="font-weight: bolder; color: #7ad03a; font-size: 22px;">&#10003;
        </span>';
        $url           = $CFG->wwwroot . "/admin/webservice/service_users.php?id=$service";
        $functionspage = "<a href='$url' target='_blank'>here</a>";

        // Check web service user have a capability to use the web service.
        $webservicemanager = new webservice();
        if (!empty($service)) {
            $allowedusers = $webservicemanager->get_ws_authorised_users($service);
            $usersmissingcaps = $webservicemanager->get_missing_capabilities_by_users($allowedusers, $service);
            $webservicemanager->get_external_service_by_id($service);
            foreach ($allowedusers as &$alloweduser) {
                if (!is_siteadmin($alloweduser->id) and array_key_exists($alloweduser->id, $usersmissingcaps)) {
                    $missingcapmsg = "<span class='summ_error'>User don't have web service access capabilities,
                     click $functionspage to know more.</span>";
                }
            }

            // Get the web service name.
            $serviceobj = $webservicemanager->get_external_service_by_id($service);
            if (isset($serviceobj->name)) {
                $servicename = $serviceobj->name;
            }

            // If service is empty then show just the blank text with dash.
            $tokenfield = $token;
            if (!empty($service)) {
                // If the token available then show the token.
                $tokenfield = eb_create_token_field($service, $token);
            }
        } else {
            $missingcapmsg = "<span class='summ_error'>User don't have web service access capabilities,
            click $functionspage to know more.</span>";
        }

        $summaryarray = array(
            'summary_setting_section' => array(
                'webserviceprotocols' => array(
                    'label'          => get_string('sum_rest_proctocol', 'local_edwiserbridge'),
                    'expected_value' => 'dynamic',
                    'value'          => 1,
                    'error_msg'      => get_string('sum_error_rest_proctocol', 'local_edwiserbridge'),
                    'error_link'     => $CFG->wwwroot . "/local/edwiserbridge/edwiserbridge.php?tab=settings"
                ),
                'enablewebservices'   => array(
                    'expected_value' => 1,
                    'label'          => get_string('sum_web_services', 'local_edwiserbridge'),
                    'error_msg'      => get_string('sum_error_web_services', 'local_edwiserbridge'),
                    'error_link'     => $CFG->wwwroot . "/local/edwiserbridge/edwiserbridge.php?tab=settings"
                ),
                'passwordpolicy'     => array(
                    'expected_value' => 0,
                    'label'          => get_string('sum_pass_policy', 'local_edwiserbridge'),
                    'error_msg'      => get_string('sum_error_pass_policy', 'local_edwiserbridge'),
                    'error_link'     => $CFG->wwwroot . "/local/edwiserbridge/edwiserbridge.php?tab=settings"
                ),
                'extendedusernamechars' => array(
                    'expected_value' => 1,
                    'label'          => get_string('sum_extended_char', 'local_edwiserbridge'),
                    'error_msg'      => get_string('sum_error_extended_char', 'local_edwiserbridge'),
                    'error_link'     => $CFG->wwwroot . "/local/edwiserbridge/edwiserbridge.php?tab=settings"
                ),
                'uptodatewebservicefunction' => array(
                    'expected_value' => 'static',
                    'label'          => get_string('web_service_status', 'local_edwiserbridge'),
                    'value'             => "<div id='web_service_status' data-serviceid='$service'>Checking...</div>"
                ),
                'webservicecap' => array(
                    'expected_value' => 'static',
                    'label'          => get_string('web_service_cap', 'local_edwiserbridge'),
                    'value'          => "<div id='web_service_status'>$missingcapmsg</div>"
                )
            ),
            'summary_connection_section'  => array(
                'url' => array(
                    'label'          => get_string('mdl_url', 'local_edwiserbridge'),
                    'expected_value' => 'static',
                    'value'          => '<div class="eb_copy_text_wrap" data> <span class="eb_copy_text" title="'
                        . get_string('click_to_copy', 'local_edwiserbridge') . '">' . $CFG->wwwroot . '</span>'
                        . ' <span class="eb_copy_btn">' . get_string('copy', 'local_edwiserbridge') . '</span></div>'

                ),
                'service_name' => array(
                    'label'          => get_string('web_service_name', 'local_edwiserbridge'),
                    'expected_value' => 'static',
                    'value'          => '<div class="eb_copy_text_wrap"> <span class="eb_copy_text" title="'
                        . get_string('click_to_copy', 'local_edwiserbridge') . '">' . $servicename . '</span>'
                        . ' <span class="eb_copy_btn">' . get_string('copy', 'local_edwiserbridge') . '</span></div>'
                ),
                'token' => array(
                    'label'          => get_string('token', 'local_edwiserbridge'),
                    'expected_value' => 'static',
                    'value'          => '<div class="eb_copy_text_wrap"> <span class="eb_copy_text" title="'
                        . get_string('click_to_copy', 'local_edwiserbridge') . '">' . $token
                        . '</span> <span class="eb_copy_btn">' . get_string('copy', 'local_edwiserbridge') . '</span></div>'
                ),
                'lang_code' => array(
                    'label'          => get_string('lang_label', 'local_edwiserbridge'),
                    'expected_value' => 'static',
                    'value'         => '<div class="eb_copy_text_wrap"> <span class="eb_copy_text" title="'
                        . get_string('click_to_copy', 'local_edwiserbridge') . '">' . $CFG->lang
                        . '</span> <span class="eb_copy_btn">' . get_string('copy', 'local_edwiserbridge') . '</span></div>'
                ),
            ),
            'edwiser_bridge_plugin_summary'  => array(
                '' => array(
                    'label'          => '',
                    'expected_value' => 'static',
                    'value'          => $this->get_plugin_fetch_link(),
                ),
                'mdl_edwiser_bridge' => array(
                    'label'          => get_string('mdl_edwiser_bridge_lbl', 'local_edwiserbridge'),
                    'expected_value' => 'static',
                    'value'          => $pluginsvdata['edwiserbridge'],
                ),
                'mdl_edwiser_bridge_sso' => array(
                    'label'          => get_string('mdl_edwiser_bridge_sso_lbl', 'local_edwiserbridge'),
                    'expected_value' => 'static',
                    'value'          => $pluginsvdata['wdmwpmoodle'],
                ),
                'mdl_edwiser_bridge_bp' => array(
                    'label'          => get_string('mdl_edwiser_bridge_bp_lbl', 'local_edwiserbridge'),
                    'expected_value' => 'static',
                    'value'          => $pluginsvdata['wdmgroupregistration'],
                ),
            )
        );

        $html = '';

        foreach ($summaryarray as $sectionkey => $section) {
            $html .= '<div class="summary_section"> <div class="summary_section_title">'
                . get_string($sectionkey, 'local_edwiserbridge') . '</div>';
            $html .= '<table class="summary_section_tbl">';

            foreach ($section as $key => $value) {
                $html .= "<tr id='$key'><td class='sum_label'>";
                $html .= $value['label'];
                $html .= '</td>';

                if ($value['expected_value'] === 'static') {
                    $html .= '<td class="sum_status">' . $value['value'] . '<td>';
                } else if ($value['expected_value'] === 'dynamic') {
                    if ($key == 'webserviceprotocols') {
                        $activewebservices = empty($CFG->webserviceprotocols) ? array() : explode(',', $CFG->webserviceprotocols);
                        if (!in_array('rest', $activewebservices)) {
                            $html .= '<td class="sum_status">
								<span class="summ_error"> ' . $value['error_msg'] . '<a href="' . $value['error_link'] . '" target="_blank" >'
                                . get_string('here', 'local_edwiserbridge') . '</a> </span>
							</td>';
                            $error = 1;
                        } else {
                            $successmsg = 'Disabled';
                            if ($value['expected_value']) {
                                $successmsg = 'Enabled';
                            }

                            $html .= '<td class="sum_status">
                                <span class="summ_success" style="font-weight: bolder; color: #7ad03a; font-size: 22px;">&#10003;
                                </span>
                                <span style="color: #7ad03a;"> ' . $successmsg . ' </span>
							</td>';
                        }
                    }
                } else if (isset($CFG->$key) && $value['expected_value'] == $CFG->$key) {

                    $successmsg = 'Disabled';
                    if ($value['expected_value']) {
                        $successmsg = 'Enabled';
                    }

                    $html .= '<td class="sum_status">
								<span class="summ_success" style="font-weight: bolder; color: #7ad03a; font-size: 22px;">&#10003; </span>
								<span style="color: #7ad03a;"> ' . $successmsg . ' </span>
							</td>';
                } else {
                    $html .= '<td class="sum_status" id="' . $key . '">
								<span class="summ_error"> ' . $value['error_msg'] . '<a href="' . $value['error_link']
                        . '" target="_blank" >' . get_string('here', 'local_edwiserbridge') . '</a> </span>
							</td>';
                    $error = 1;
                }
                $html .= '</td>
						</tr>';
            }

            $html .= '</table>';
            $html .= ' </div>';
        }

        $mform->addElement(
            'html',
            $html
        );
    }

    /**
     * get plugin fetch link.
     *
     * @return string
     */
    private function get_plugin_fetch_link() {
        global $CFG;
        $url = $CFG->wwwroot . '/local/edwiserbridge/edwiserbridge.php?tab=summary&fetch_data=true';
        return "<a href='{$url}'><i class='fa fa-refresh'></i> "
            . get_string('mdl_edwiser_bridge_fetch_info', 'local_edwiserbridge')
            . "</a>";
    }

    /**
     * Default methods of moodleform class to get method version.
     *
     * @return string
     */
    private function get_plugin_version_data() {
        $pluginsdata = array();
        $pluginman   = \core_plugin_manager::instance();
        $localplugin = $pluginman->get_plugins_of_type('local');

        $pluginsdata['edwiserbridge'] = get_string('mdl_edwiser_bridge_txt_not_avbl', 'local_edwiserbridge');
        if (isset($localplugin['edwiserbridge'])) {
            $pluginsdata['edwiserbridge'] = $localplugin['edwiserbridge']->release;
        }

        $pluginsdata['wdmgroupregistration'] = get_string('mdl_edwiser_bridge_txt_not_avbl', 'local_edwiserbridge');
        if (isset($localplugin['wdmgroupregistration'])) {
            $pluginsdata['wdmgroupregistration'] = $localplugin['wdmgroupregistration']->release;
        }

        $authplugin                 = $pluginman->get_plugins_of_type('auth');
        $pluginsdata['wdmwpmoodle'] = get_string('mdl_edwiser_bridge_txt_not_avbl', 'local_edwiserbridge');
        if (isset($authplugin['wdmwpmoodle'])) {
            $pluginsdata['wdmwpmoodle'] = $authplugin['wdmwpmoodle']->release;
        }

        $fetchdata = optional_param('tab', '', PARAM_RAW);
        $fetchdata  = 'true' === $fetchdata ? true : false;
        $remotedata = $this->get_remote_plugins_data($fetchdata);

        $versioninfo = array(
            'edwiserbridge'        => $pluginsdata['edwiserbridge'] . "<span style='padding-left:1rem;color:limegreen;'>"
                . get_string('mdl_edwiser_bridge_txt_latest', 'local_edwiserbridge') . " </span>",
            'wdmgroupregistration' => $pluginsdata['wdmgroupregistration'] . "<span style='padding-left:1rem;color:limegreen;'>"
                . get_string('mdl_edwiser_bridge_txt_latest', 'local_edwiserbridge') . " </span>",
            'wdmwpmoodle'          => $pluginsdata['wdmwpmoodle'] . "<span style='padding-left:1rem;color:limegreen;'>"
                . get_string('mdl_edwiser_bridge_txt_latest', 'local_edwiserbridge') . " </span>",
        );

        if (false !== $remotedata) {
            if (
                isset($remotedata->moodle_edwiser_bridge->version) &&
                version_compare($pluginsdata['edwiserbridge'], $remotedata->moodle_edwiser_bridge->version, "<")
            ) {
                $versioninfo['edwiserbridge'] = $pluginsdata['edwiserbridge'] . "<span  style='padding-left:1rem;'>("
                    . $remotedata->moodle_edwiser_bridge->version . ")<a href='"
                    . $remotedata->moodle_edwiser_bridge->url . "' title='"
                    . get_string('mdl_edwiser_bridge_txt_download_help', 'local_edwiserbridge') . "'>"
                    . get_string('mdl_edwiser_bridge_txt_download', 'local_edwiserbridge') . "</a></span>";
            }
            if (
                isset($remotedata->moodle_edwiser_bridge_bp->version) &&
                version_compare($pluginsdata['wdmgroupregistration'], $remotedata->moodle_edwiser_bridge_bp->version, "<")
            ) {
                $versioninfo['wdmgroupregistration'] = $pluginsdata['wdmgroupregistration'] . "<span  style='padding-left:1rem;'>("
                    . $remotedata->moodle_edwiser_bridge_bp->version . ")<a href='" . $remotedata->moodle_edwiser_bridge_bp->url
                    . "' title='" . get_string('mdl_edwiser_bridge_txt_download_help', 'local_edwiserbridge') . "'>"
                    . get_string('mdl_edwiser_bridge_txt_download', 'local_edwiserbridge') . "</a></span>";
            }
            if (
                isset($remotedata->moodle_edwiser_bridge_sso->version) &&
                version_compare($pluginsdata['wdmwpmoodle'], $remotedata->moodle_edwiser_bridge_sso->version, "<")
            ) {
                $versioninfo['wdmwpmoodle'] = $pluginsdata['wdmwpmoodle'] . "<span  style='padding-left:1rem;'>("
                    . $remotedata->moodle_edwiser_bridge_sso->version . ")<a href='" . $remotedata->moodle_edwiser_bridge_sso->url
                    . "' title='" . get_string('mdl_edwiser_bridge_txt_download_help', 'local_edwiserbridge') . "'>"
                    . get_string('mdl_edwiser_bridge_txt_download', 'local_edwiserbridge') . "</a></span>";
            }
        }
        return $versioninfo;
    }

    /**
     * Returns plugin details.
     *
     * @param string $fetchdata
     * @return object
     */
    private function get_remote_plugins_data($fetchdata) {
        $data         = get_config('local_edwiserbridge', 'edwiserbridge_plugins_versions');
        $requestdata = true;

        if ($data || $fetchdata) {
            $data = json_decode($data);
            if (isset($data->data) && isset($data->time) && $data->time > time()) {
                $output = json_decode($data->data);
                $requestdata = false;
            }
        }
        if ($requestdata) {
            if (!function_exists('curl_version')) {
                return false;
            }

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => "https://edwiser.org/edwiserdemoimporter/bridge-free-plugin-info.json",
                CURLOPT_TIMEOUT => 100,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
            ));
            //construct a user agent string
            global $CFG;
            $useragent = 'Moodle/' . $CFG->version . ' (' . $CFG->wwwroot . ') Edwiser Bridge Update Checker';

            curl_setopt($curl, CURLOPT_USERAGENT, $useragent);
            $output = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            if (200 === $httpcode) {
                $data = array(
                    'time' => time() + (60 * 60 * 24),
                    'data' => $output,
                );
                set_config('edwiserbridge_plugins_versions', json_encode($data), 'local_edwiserbridge');
            }
            $output = json_decode($output);
        }
        return $output;
    }
}
