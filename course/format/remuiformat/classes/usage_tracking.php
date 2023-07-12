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
 * Edwiser Usage Tracking.
 *
 * We send anonymous user data to imporve our product compatibility with various plugins and systems.
 *
 * Cards Format - A topics based format that uses card layout to diaply the content.
 * @package    format_remuiformat
 * @copyright  (c) 2020 WisdmLabs (https://wisdmlabs.com/)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_remuiformat;

/**
 * Edwiser Usage Tracking
 */
class usage_tracking {

    /**
     * Send usage analytics to Edwiser, only anonymous data is sent.
     *
     * every 7 days the data is sent, function runs for admin user only
     */
    public function send_usage_analytics() {

        global $DB, $CFG;

        // Execute code only if current user is site admin.
        // Reduces calls to DB.
        if (is_siteadmin()) {

            // Check consent to send tracking data.
            $consent = get_config('format_remuiformat', 'enableusagetracking');
            if ($consent) {

                // TODO: A check needs to be added here, that user has agreed to send this data.
                // TODO: We will have to add a settings checkbox for that or something similar.
                $lastsentdata = isset($CFG->usage_data_last_sent_format_remuiformat) ?
                                $CFG->usage_data_last_sent_format_remuiformat :
                                false;

                // If current time is greater then saved time, send data again.
                if (!$lastsentdata || time() > $lastsentdata) {
                    $resultarr = [];

                    $analyticsdata = json_encode($this->prepare_usage_analytics());

                    $url = "https://edwiser.org/wp-json/edwiser_customizations/send_usage_data";
                    // Call api endpoint with data.
                    $ch = curl_init();

                    // Set the url, number of POST vars, POST data.
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $analyticsdata);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($analyticsdata))
                    );

                    // Execute post.
                    $result = curl_exec($ch);
                    if ($result) {
                        $resultarr = json_decode($result, 1);
                    }
                    // Close connection.
                    curl_close($ch);

                    // Save new timestamp, 7 days --- save only if api returned success.
                    if (isset($resultarr['success']) && $resultarr['success']) {
                        set_config('usage_data_last_sent_format_remuiformat', time() + 604800);
                    }
                }
            }
        }
    }

     /**
      * Prepare usage analytics
      */
    private function prepare_usage_analytics() {

        global $CFG, $DB;

        // Suppressing all the errors here, just in case the setting does not exists, to avoid many if statements.
        $analyticsdata = array(
            // Replace protocol and trailing slash.
            'siteurl' => $this->detect_site_type() . preg_replace('#^https?://#', '', rtrim(@$CFG->wwwroot, '/')),
            'product_name' => "Edwiser Course Format",
            // All settings in json, of current product which you are tracking.
            'product_settings' => $this->get_plugin_settings('format_remuiformat'),
            'active_theme' => @$CFG->theme,
            // Include only with format type remuicourseformat.
            'total_courses' => $DB->count_records('course', array('format' => 'remuiformat')),
            'total_users' => $DB->count_records('user', array('deleted' => 0)), // Exclude deleted.
            'system_version' => @$CFG->release, // Moodle version.
            'system_lang' => @$CFG->lang,
            'system_settings' => array(
                // Enabling blogs. 'blog_active' => @$CFG->enableblogs,.
                'cachejs_active' => @$CFG->cachejs,
                'messaging_active' => @$CFG->messaging,
                'theme_designermode_active' => @$CFG->themedesignermode,
                'multilang_filter_active' => @$CFG->filter_multilang_converted,
                'moodle_debug_mode' => @$CFG->debug,
                'moodle_debug_debugdisplay' => @$CFG->debugdisplay,
                'moodle_memory_limit' => @$CFG->extramemorylimit,
                'moodle_maxexec_time_limit' => @$CFG->maxtimelimit,
                'moodle_curlcache_ttl' => @$CFG->curlcache,
            ),
            'server_os' => @$CFG->os,
            'server_ip' => @$_SERVER['REMOTE_ADDR'],
            'web_server' => @$_SERVER['SERVER_SOFTWARE'],
            'databasename' => @$CFG->dbtype,
            'php_version' => phpversion(),
            'php_settings' => array(
                'memory_limit' => ini_get("memory_limit"),
                'max_execution_time' => ini_get("max_execution_time"),
                'post_max_size' => ini_get("post_max_size"),
                'upload_max_filesize' => ini_get("upload_max_filesize"),
                'memory_limit' => ini_get("memory_limit")
            ),
        );
        return $analyticsdata;
    }

    /**
     * Get plugins installed by user excluding the default plugins
     * @return object All plugins object
     */
    private function get_user_installed_plugins() {
        // All plugins - "external/installed by user".
        $allplugins = array();

        $pluginman = \core_plugin_manager::instance();
        $plugininfos = $pluginman->get_plugins();

        foreach ($plugininfos as $key => $modtype) {
            foreach ($modtype as $key => $plug) {
                if (!$plug->is_standard() && !$plug->is_subplugin()) {
                    // Each plugin data, // can be different structuer in case of wordpress product.
                    $allplugins[] = array(
                        'name' => $plug->displayname,
                        'versiondisk' => $plug->versiondisk,
                        'versiondb' => $plug->versiondb,
                        'versiondisk' => $plug->versiondisk,
                        'release' => $plug->release
                    );
                }
            }
        }

        return $allplugins;
    }

    /**
     * Get specific settings of the current plugin, eg: remui
     * @param  string $plugin pluginname
     * @return object         plugin object
     */
    private function get_plugin_settings($plugin) {
        global $DB;
        // Get complete config.
        $pluginconfig = get_config($plugin);
        $filteredpluginconfig = array();

        // Suppressing all the errors here, just in case the setting does not exists, to avoid many if statements.
        $filteredpluginconfig['defaultsectionsummarymaxlength'] = @$pluginconfig->defaultsectionsummarymaxlength;
        $filteredpluginconfig['enableusagetracking'] = @$pluginconfig->enableusagetracking;
        $filteredpluginconfig['version'] = @$pluginconfig->version;

        return $filteredpluginconfig;
    }

    /**
     * Check if site is running on localhost or not.
     */
    private function detect_site_type() {
        $whitelist = array(
            '127.0.0.1',
            '::1'
        );

        // Check if site is running on localhost or not.
        if (in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
            return 'localsite--';
        }
        return '';
    }
}
