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

namespace core_adminpresets;

use memory_xml_output;
use moodle_exception;
use stdClass;
use xml_writer;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/adminlib.php');

/**
 * Admin tool presets manager class.
 *
 * @package          core_adminpresets
 * @copyright        2021 Pimenko <support@pimenko.com><pimenko.com>
 * @author           Jordan Kesraoui | Sylvain Revenu | Pimenko based on David Monllaó <david.monllao@urv.cat> code
 * @license          http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {

    /** @var \admin_root The admin root tree with the settings. **/
    private $adminroot;

    /** @var array Setting classes mapping, to associated the local/setting class that should be used when there is
     * no specific class. */
    protected static $settingclassesmap = [
            'adminpresets_admin_setting_agedigitalconsentmap' => 'adminpresets_admin_setting_configtext',
            'adminpresets_admin_setting_configcolourpicker' => 'adminpresets_admin_setting_configtext',
            'adminpresets_admin_setting_configdirectory' => 'adminpresets_admin_setting_configtext',
            'adminpresets_admin_setting_configduration_with_advanced' => 'adminpresets_admin_setting_configtext_with_advanced',
            'adminpresets_admin_setting_configduration' => 'adminpresets_admin_setting_configtext',
            'adminpresets_admin_setting_configempty' => 'adminpresets_admin_setting_configtext',
            'adminpresets_admin_setting_configexecutable' => 'adminpresets_admin_setting_configtext',
            'adminpresets_admin_setting_configfile' => 'adminpresets_admin_setting_configtext',
            'adminpresets_admin_setting_confightmleditor' => 'adminpresets_admin_setting_configtext',
            'adminpresets_admin_setting_configmixedhostiplist' => 'adminpresets_admin_setting_configtext',
            'adminpresets_admin_setting_configmultiselect_modules' => 'adminpresets_admin_setting_configmultiselect_with_loader',
            'adminpresets_admin_setting_configpasswordunmask' => 'adminpresets_admin_setting_configtext',
            'adminpresets_admin_setting_configportlist' => 'adminpresets_admin_setting_configtext',
            'adminpresets_admin_setting_configselect_with_lock' => 'adminpresets_admin_setting_configselect',
            'adminpresets_admin_setting_configtext_trim_lower' => 'adminpresets_admin_setting_configtext',
            'adminpresets_admin_setting_configtext_with_maxlength' => 'adminpresets_admin_setting_configtext',
            'adminpresets_admin_setting_configtextarea' => 'adminpresets_admin_setting_configtext',
            'adminpresets_admin_setting_configthemepreset' => 'adminpresets_admin_setting_configselect',
            'adminpresets_admin_setting_countrycodes' => 'adminpresets_admin_setting_configtext',
            'adminpresets_admin_setting_courselist_frontpage' => 'adminpresets_admin_setting_configmultiselect_with_loader',
            'adminpresets_admin_setting_description' => 'adminpresets_admin_setting_configtext',
            'adminpresets_admin_setting_enablemobileservice' => 'adminpresets_admin_setting_configcheckbox',
            'adminpresets_admin_setting_filetypes' => 'adminpresets_admin_setting_configtext',
            'adminpresets_admin_setting_forcetimezone' => 'adminpresets_admin_setting_configselect',
            'adminpresets_admin_setting_grade_profilereport' => 'adminpresets_admin_setting_configmultiselect_with_loader',
            'adminpresets_admin_setting_langlist' => 'adminpresets_admin_setting_configtext',
            'adminpresets_admin_setting_my_grades_report' => 'adminpresets_admin_setting_configselect',
            'adminpresets_admin_setting_pickroles' => 'adminpresets_admin_setting_configmulticheckbox',
            'adminpresets_admin_setting_question_behaviour' => 'adminpresets_admin_setting_configmultiselect_with_loader',
            'adminpresets_admin_setting_regradingcheckbox' => 'adminpresets_admin_setting_configcheckbox',
            'adminpresets_admin_setting_scsscode' => 'adminpresets_admin_setting_configtext',
            'adminpresets_admin_setting_servertimezone' => 'adminpresets_admin_setting_configselect',
            'adminpresets_admin_setting_sitesetcheckbox' => 'adminpresets_admin_setting_configcheckbox',
            'adminpresets_admin_setting_sitesetselect' => 'adminpresets_admin_setting_configselect',
            'adminpresets_admin_setting_special_adminseesall' => 'adminpresets_admin_setting_configcheckbox',
            'adminpresets_admin_setting_special_backup_auto_destination' => 'adminpresets_admin_setting_configtext',
            'adminpresets_admin_setting_special_coursecontact' => 'adminpresets_admin_setting_configmulticheckbox',
            'adminpresets_admin_setting_special_coursemanager' => 'adminpresets_admin_setting_configmulticheckbox',
            'adminpresets_admin_setting_special_debug' => 'adminpresets_admin_setting_configmultiselect_with_loader',
            'adminpresets_admin_setting_special_frontpagedesc' => 'adminpresets_admin_setting_sitesettext',
            'adminpresets_admin_setting_special_gradebookroles' => 'adminpresets_admin_setting_configmulticheckbox',
            'adminpresets_admin_setting_special_gradeexport' => 'adminpresets_admin_setting_configmulticheckbox',
            'adminpresets_admin_setting_special_gradelimiting' => 'adminpresets_admin_setting_configcheckbox',
            'adminpresets_admin_setting_special_grademinmaxtouse' => 'adminpresets_admin_setting_configselect',
            'adminpresets_admin_setting_special_gradepointdefault' => 'adminpresets_admin_setting_configtext',
            'adminpresets_admin_setting_special_gradepointmax' => 'adminpresets_admin_setting_configtext',
            'adminpresets_admin_setting_special_registerauth' => 'adminpresets_admin_setting_configmultiselect_with_loader',
            'adminpresets_admin_setting_special_selectsetup' => 'adminpresets_admin_setting_configselect',
            'adminpresets_admin_settings_country_select' => 'adminpresets_admin_setting_configmultiselect_with_loader',
            'adminpresets_admin_settings_coursecat_select' => 'adminpresets_admin_setting_configmultiselect_with_loader',
            'adminpresets_admin_settings_h5plib_handler_select' => 'adminpresets_admin_setting_configselect',
            'adminpresets_admin_settings_num_course_sections' => 'adminpresets_admin_setting_configmultiselect_with_loader',
            'adminpresets_admin_settings_sitepolicy_handler_select' => 'adminpresets_admin_setting_configselect',
            'adminpresets_antivirus_clamav_pathtounixsocket_setting' => 'adminpresets_admin_setting_configtext',
            'adminpresets_antivirus_clamav_runningmethod_setting' => 'adminpresets_admin_setting_configselect',
            'adminpresets_antivirus_clamav_tcpsockethost_setting' => 'adminpresets_admin_setting_configtext',
            'adminpresets_auth_db_admin_setting_special_auth_configtext' => 'adminpresets_admin_setting_configtext',
            'adminpresets_auth_ldap_admin_setting_special_lowercase_configtext' => 'adminpresets_admin_setting_configtext',
            'adminpresets_auth_ldap_admin_setting_special_ntlm_configtext' => 'adminpresets_admin_setting_configtext',
            'adminpresets_auth_shibboleth_admin_setting_convert_data' => 'adminpresets_admin_setting_configtext',
            'adminpresets_auth_shibboleth_admin_setting_special_idp_configtextarea' => 'adminpresets_admin_setting_configtext',
            'adminpresets_auth_shibboleth_admin_setting_special_wayf_select' => 'adminpresets_admin_setting_configselect',
            'adminpresets_editor_atto_toolbar_setting' => 'adminpresets_admin_setting_configtext',
            'adminpresets_editor_tinymce_json_setting_textarea' => 'adminpresets_admin_setting_configtext',
            'adminpresets_enrol_database_admin_setting_category' => 'adminpresets_admin_setting_configselect',
            'adminpresets_enrol_flatfile_role_setting' => 'adminpresets_admin_setting_configtext',
            'adminpresets_enrol_ldap_admin_setting_category' => 'adminpresets_admin_setting_configselect',
            'adminpresets_format_singleactivity_admin_setting_activitytype' => 'adminpresets_admin_setting_configselect',
            'adminpresets_qtype_multichoice_admin_setting_answernumbering' => 'adminpresets_admin_setting_configselect',
    ];

    /** @var array Relation between database fields and XML files. **/
    protected static $dbxmlrelations = [
        'name' => 'NAME',
        'comments' => 'COMMENTS',
        'timecreated' => 'PRESET_DATE',
        'site' => 'SITE_URL',
        'author' => 'AUTHOR',
        'moodleversion' => 'MOODLE_VERSION',
        'moodlerelease' => 'MOODLE_RELEASE'
    ];

    /** @var int Non-core preset */
    public const NONCORE_PRESET = 0;

    /** @var int Starter preset */
    public const STARTER_PRESET = 1;

    /** @var int Full preset */
    public const FULL_PRESET = 2;

    /**
     * Gets the system settings
     *
     * Loads the DB $CFG->prefix.'config' values and the
     * $CFG->prefix.'config_plugins' values and redirects
     * the flow through $this->get_settings()
     *
     * @return array $settings Array format $array['plugin']['settingname'] = settings_types child class
     */
    public function get_site_settings(): array {
        global $DB;

        // Db configs (to avoid multiple queries).
        $dbconfig = $DB->get_records_select('config', '', [], '', 'name, value');

        // Adding site settings in course table.
        $frontpagevalues = $DB->get_record_select('course', 'id = 1',
                [], 'fullname, shortname, summary');
        foreach ($frontpagevalues as $field => $value) {
            $dbconfig[$field] = new stdClass();
            $dbconfig[$field]->name = $field;
            $dbconfig[$field]->value = $value;
        }
        $sitedbsettings['none'] = $dbconfig;

        // Config plugins.
        $configplugins = $DB->get_records('config_plugins');
        foreach ($configplugins as $configplugin) {
            $sitedbsettings[$configplugin->plugin][$configplugin->name] = new stdClass();
            $sitedbsettings[$configplugin->plugin][$configplugin->name]->name = $configplugin->name;
            $sitedbsettings[$configplugin->plugin][$configplugin->name]->value = $configplugin->value;
        }
        // Get an array with the common format.
        return $this->get_settings($sitedbsettings, true, []);
    }

    /**
     * Constructs an array with all the system settings
     *
     * If a setting value can't be found on the DB it considers
     * the default value as the setting value
     *
     * Settings without plugin are marked as 'none' in the plugin field
     *
     * Returns an standarized settings array format.
     *
     * @param array $dbsettings Standarized array,
     * format $array['plugin']['name'] = obj('name'=>'settingname', 'value'=>'settingvalue')
     * @param boolean $sitedbvalues Indicates if $dbsettings comes from the site db or not
     * @param array $settings Array format $array['plugin']['settingname'] = settings_types child class
     * @param array|false $children Array of admin_category children or false
     * @return    array Array format $array['plugin']['settingname'] = settings_types child class
     */
    public function get_settings(array $dbsettings, bool $sitedbvalues = false, array $settings = [], $children = false): array {
        global $DB;

        // If there are no children, load admin tree and iterate through.
        if (!$children) {
            $this->adminroot = admin_get_root(false, true);
            $children = $this->adminroot->children;
        }

        // Iteates through children.
        foreach ($children as $key => $child) {

            // We must search category children.
            if (is_a($child, 'admin_category')) {

                if ($child->children) {
                    $settings = $this->get_settings($dbsettings, $sitedbvalues, $settings, $child->children);
                }

                // Settings page.
            } else if (is_a($child, 'admin_settingpage')) {

                if (property_exists($child, 'settings')) {

                    foreach ($child->settings as $values) {
                        $settingname = $values->name;

                        unset($settingvalue);

                        // Look for his config value.
                        if ($values->plugin == '') {
                            $values->plugin = 'none';
                        }

                        if (!empty($dbsettings[$values->plugin][$settingname])) {
                            $settingvalue = $dbsettings[$values->plugin][$settingname]->value;
                        }

                        // If no db value found default value.
                        if ($sitedbvalues && !isset($settingvalue)) {
                            // For settings with multiple values.
                            if (is_array($values->defaultsetting)) {

                                if (isset($values->defaultsetting['value'])) {
                                    $settingvalue = $values->defaultsetting['value'];
                                    // Configtime case, does not have a 'value' default setting.
                                } else {
                                    $settingvalue = 0;
                                }
                            } else {
                                $settingvalue = $values->defaultsetting;
                            }
                        }

                        // If there aren't any value loaded, skip that setting.
                        if (!isset($settingvalue)) {
                            continue;
                        }
                        // If there is no setting class defined continue.
                        if (!$setting = $this->get_setting($values, $settingvalue)) {
                            continue;
                        }

                        // Settings_types childs with.
                        // attributes provides an attributes array.
                        if ($attributes = $setting->get_attributes()) {

                            // Look for settings attributes if it is a presets.
                            if (!$sitedbvalues) {
                                $itemid = $dbsettings[$values->plugin][$settingname]->itemid;
                                $attrs = $DB->get_records('adminpresets_it_a',
                                        ['itemid' => $itemid], '', 'name, value');
                            }
                            foreach ($attributes as $defaultvarname => $varname) {

                                unset($attributevalue);

                                // Settings from site.
                                if ($sitedbvalues) {
                                    if (!empty($dbsettings[$values->plugin][$varname])) {
                                        $attributevalue = $dbsettings[$values->plugin][$varname]->value;
                                    }

                                    // Settings from a preset.
                                } else if (!$sitedbvalues && isset($attrs[$varname])) {
                                    $attributevalue = $attrs[$varname]->value;
                                }

                                // If no value found, default value,
                                // But we may not have a default value for the attribute.
                                if (!isset($attributevalue) && !empty($values->defaultsetting[$defaultvarname])) {
                                    $attributevalue = $values->defaultsetting[$defaultvarname];
                                }

                                // If there is no even a default for this setting will be empty.
                                // So we do nothing in this case.
                                if (isset($attributevalue)) {
                                    $setting->set_attribute_value($varname, $attributevalue);
                                }
                            }
                        }

                        // Adding to general settings array.
                        $settings[$values->plugin][$settingname] = $setting;
                    }
                }
            }
        }

        return $settings;
    }

    /**
     * Returns the class type object
     *
     * @param object $settingdata Setting data
     * @param mixed $currentvalue
     * @return mixed
     */
    public function get_setting($settingdata, $currentvalue) {

        $classname = null;

        // Getting the appropiate class to get the correct setting value.
        $settingtype = get_class($settingdata);

        // Check if it is a setting from a plugin.
        $plugindata = explode('_', $settingtype);
        $types = \core_component::get_plugin_types();
        if (array_key_exists($plugindata[0], $types)) {
            $plugins = \core_component::get_plugin_list($plugindata[0]);
            if (array_key_exists($plugindata[1], $plugins)) {
                // Check if there is a specific class for this plugin admin setting.
                $settingname = 'adminpresets_' . $settingtype;
                $classname = "\\$plugindata[0]_$plugindata[1]\\adminpresets\\$settingname";
                if (!class_exists($classname)) {
                    $classname = null;
                }
            }
        } else {
            $settingname = 'adminpresets_' . $settingtype;
            $classname = '\\core_adminpresets\\local\\setting\\' . $settingname;
            if (!class_exists($classname)) {
                // Check if there is some mapped class that should be used for this setting.
                $classname = self::get_settings_class($settingname);
            }
        }

        if (is_null($classname)) {
            // Return the default setting class if there is no specific class for this setting.
            $classname = '\\core_adminpresets\\local\\setting\\adminpresets_setting';
        }

        return new $classname($settingdata, $currentvalue);
    }

    /**
     * Returns the settings class mapped to the defined $classname or null if it doesn't exist any associated class.
     *
     * @param string $classname The classname to get the mapped class.
     * @return string|null
     */
    public static function get_settings_class(string $classname): ?string {
        if (array_key_exists($classname, self::$settingclassesmap)) {
            return '\\core_adminpresets\\local\\setting\\' . self::$settingclassesmap[$classname];
        }

        return null;
    }

    /**
     * Gets the standarized settings array from DB records
     *
     * @param array $dbsettings Array of objects
     * @return   array Standarized array,
     * format $array['plugin']['name'] = obj('name'=>'settingname', 'value'=>'settingvalue')
     */
    public function get_settings_from_db(array $dbsettings): array {
        $settings = [];

        if (!$dbsettings) {
            return $settings;
        }

        foreach ($dbsettings as $dbsetting) {
            $settings[$dbsetting->plugin][$dbsetting->name] = new stdClass();
            $settings[$dbsetting->plugin][$dbsetting->name]->itemid = $dbsetting->id;
            $settings[$dbsetting->plugin][$dbsetting->name]->name = $dbsetting->name;
            $settings[$dbsetting->plugin][$dbsetting->name]->value = $dbsetting->value;
        }

        return $settings;
    }


    /**
     * Apply a given preset.
     *
     * @param int $presetid The preset identifier to apply.
     * @param bool $simulate Whether this is a simulation or not.
     * @return array List with an array with the applied settings and another with the skipped ones.
     */
    public function apply_preset(int $presetid, bool $simulate = false): array {
        global $DB;

        if (!$DB->get_record('adminpresets', ['id' => $presetid])) {
            throw new moodle_exception('errornopreset', 'core_adminpresets');
        }

        // Apply preset settings.
        [$settingsapplied, $settingsskipped, $appid] = $this->apply_settings($presetid, $simulate);

        // Set plugins visibility.
        [$pluginsapplied, $pluginsskipped] = $this->apply_plugins($presetid, $simulate, $appid);

        $applied = array_merge($settingsapplied, $pluginsapplied);
        $skipped = array_merge($settingsskipped, $pluginsskipped);

        if (!$simulate) {
            // Store it in a config setting as the last preset applied.
            set_config('lastpresetapplied', $presetid, 'adminpresets');
        }

        return [$applied, $skipped];
    }

    /**
     * Create a preset with the current settings and plugins information.
     *
     * @param \stdClass $data Preset info, such as name or description, to be used when creating the preset with the current
     *                 settings and plugins.
     * @return array List with an the presetid created (int), a boolean to define if any setting has been found and
     *               another boolean to specify if any plugin has been found.
     */
    public function export_preset(stdClass $data): array {
        global $DB;

        // Admin_preset record.
        $presetdata = [
            'name' => $data->name ?? '',
            'comments' => !empty($data->comments) ? $data->comments['text'] : '',
            'author' => $data->author ?? '',
        ];
        if (!$presetid = helper::create_preset($presetdata)) {
            throw new moodle_exception('errorinserting', 'core_adminpresets');
        }

        // Store settings.
        $settingsfound = false;

        // Site settings.
        $sitesettings = $this->get_site_settings();

        // Sensible settings.
        $sensiblesettings = explode(',', str_replace(' ', '', get_config('adminpresets', 'sensiblesettings')));
        $sensiblesettings = array_combine($sensiblesettings, $sensiblesettings);
        foreach ($sitesettings as $plugin => $pluginsettings) {
            foreach ($pluginsettings as $settingname => $sitesetting) {
                // Avoid sensible data.
                if (empty($data->includesensiblesettings) && !empty($sensiblesettings["$settingname@@$plugin"])) {
                    continue;
                }

                $setting = new stdClass();
                $setting->adminpresetid = $presetid;
                $setting->plugin = $plugin;
                $setting->name = $settingname;
                $setting->value = $sitesetting->get_value();
                if (!$setting->id = $DB->insert_record('adminpresets_it', $setting)) {
                    throw new moodle_exception('errorinserting', 'core_adminpresets');
                }

                // Setting attributes must also be exported.
                if ($attributes = $sitesetting->get_attributes_values()) {
                    foreach ($attributes as $attname => $attvalue) {
                        $attr = new stdClass();
                        $attr->itemid = $setting->id;
                        $attr->name = $attname;
                        $attr->value = $attvalue;

                        $DB->insert_record('adminpresets_it_a', $attr);
                    }
                }
                $settingsfound = true;
            }
        }

        // Store plugins visibility (enabled/disabled).
        $pluginsfound = false;
        $pluginmanager = \core_plugin_manager::instance();
        $types = $pluginmanager->get_plugin_types();
        foreach ($types as $plugintype => $notused) {
            $plugins = $pluginmanager->get_present_plugins($plugintype);
            $pluginclass = \core_plugin_manager::resolve_plugininfo_class($plugintype);
            if (!empty($plugins)) {
                foreach ($plugins as $pluginname => $plugin) {
                    $entry = new stdClass();
                    $entry->adminpresetid = $presetid;
                    $entry->plugin = $plugintype;
                    $entry->name = $pluginname;
                    $entry->enabled = $pluginclass::get_enabled_plugin($pluginname);

                    $DB->insert_record('adminpresets_plug', $entry);
                    $pluginsfound = true;
                }
            }
        }

        // If there are no settings nor plugins, the admin preset record should be removed.
        if (!$settingsfound && !$pluginsfound) {
            $DB->delete_records('adminpresets', ['id' => $presetid]);
            $presetid = null;
        }

        return [$presetid, $settingsfound, $pluginsfound];
    }

    /**
     * Create the XML content for a given preset.
     *
     * @param int $presetid The preset to download.
     * @return array List with the XML content (string) and a filename proposal based on the preset name (string).
     */
    public function download_preset(int $presetid): array {
        global $DB;

        if (!$preset = $DB->get_record('adminpresets', ['id' => $presetid])) {
            throw new moodle_exception('errornopreset', 'core_adminpresets');
        }

        // Start.
        $xmloutput = new memory_xml_output();
        $xmlwriter = new xml_writer($xmloutput);
        $xmlwriter->start();

        // Preset data.
        $xmlwriter->begin_tag('PRESET');
        foreach (static::$dbxmlrelations as $dbname => $xmlname) {
            $xmlwriter->full_tag($xmlname, $preset->$dbname);
        }

        // We ride through the settings array.
        $items = $DB->get_records('adminpresets_it', ['adminpresetid' => $preset->id]);
        $allsettings = $this->get_settings_from_db($items);
        if ($allsettings) {
            $xmlwriter->begin_tag('ADMIN_SETTINGS');

            foreach ($allsettings as $plugin => $settings) {
                $tagname = strtoupper($plugin);

                // To aviod xml slash problems.
                if (strstr($tagname, '/') != false) {
                    $tagname = str_replace('/', '__', $tagname);
                }

                $xmlwriter->begin_tag($tagname);

                // One tag for each plugin setting.
                if (!empty($settings)) {
                    $xmlwriter->begin_tag('SETTINGS');
                    foreach ($settings as $setting) {
                        // Unset the tag attributes string.
                        $attributes = [];

                        // Getting setting attributes, if present.
                        $attrs = $DB->get_records('adminpresets_it_a', ['itemid' => $setting->itemid]);
                        if ($attrs) {
                            foreach ($attrs as $attr) {
                                $attributes[$attr->name] = $attr->value;
                            }
                        }

                        $xmlwriter->full_tag(strtoupper($setting->name), $setting->value, $attributes);
                    }

                    $xmlwriter->end_tag('SETTINGS');
                }

                $xmlwriter->end_tag(strtoupper($tagname));
            }

            $xmlwriter->end_tag('ADMIN_SETTINGS');
        }

        // We ride through the plugins array.
        $data = $DB->get_records('adminpresets_plug', ['adminpresetid' => $preset->id]);
        if ($data) {
            $plugins = [];
            foreach ($data as $plugin) {
                $plugins[$plugin->plugin][] = $plugin;
            }

            $xmlwriter->begin_tag('PLUGINS');

            foreach ($plugins as $plugintype => $plugintypes) {
                $tagname = strtoupper($plugintype);
                $xmlwriter->begin_tag($tagname);

                foreach ($plugintypes as $plugin) {
                    $xmlwriter->full_tag(strtoupper($plugin->name), $plugin->enabled);
                }

                $xmlwriter->end_tag(strtoupper($tagname));
            }

            $xmlwriter->end_tag('PLUGINS');
        }

        // End.
        $xmlwriter->end_tag('PRESET');
        $xmlwriter->stop();
        $xmlstr = $xmloutput->get_allcontents();

        $filename = addcslashes($preset->name, '"') . '.xml';

        return [$xmlstr, $filename];
    }

    /**
     * Import a given XML preset.
     *
     * @param string $xmlcontent The XML context with the preset to be imported.
     * @param string|null $presetname The preset name that will overwrite the one given in the XML file.
     * @return array List with an the XML element (SimpleXMLElement|null), the imported preset (stdClass|null), a boolean
     *               to define if any setting has been found and another boolean to specify if any plugin has been found.
     */
    public function import_preset(string $xmlcontent, ?string $presetname = null): array {
        global $DB, $USER;

        $settingsfound = false;
        $pluginsfound = false;

        try {
            $xml = simplexml_load_string($xmlcontent);
        } catch (\Exception $exception) {
            $xml = false;
        }
        if (!$xml) {
            return [null, null, $settingsfound, $pluginsfound];
        }

        // Prepare the preset info.
        $preset = new stdClass();
        foreach (static::$dbxmlrelations as $dbname => $xmlname) {
            $preset->$dbname = (String) $xml->$xmlname;
        }
        $preset->userid = $USER->id;
        $preset->timeimported = time();

        // Overwrite preset name.
        if (!empty($presetname)) {
            $preset->name = $presetname;
        }

        // Create the preset.
        if (!$preset->id = $DB->insert_record('adminpresets', $preset)) {
            throw new moodle_exception('errorinserting', 'core_adminpresets');
        }

        // Process settings.
        $sitesettings = $this->get_site_settings();
        $xmladminsettings = $xml->ADMIN_SETTINGS[0];
        foreach ($xmladminsettings as $plugin => $settings) {
            $plugin = strtolower($plugin);
            if (strstr($plugin, '__') != false) {
                $plugin = str_replace('__', '/', $plugin);
            }

            $pluginsettings = $settings->SETTINGS[0];
            if ($pluginsettings) {
                foreach ($pluginsettings->children() as $name => $setting) {
                    $name = strtolower($name);

                    // Default to ''.
                    if ($setting->__toString() === false) {
                        $value = '';
                    } else {
                        $value = $setting->__toString();
                    }

                    if (empty($sitesettings[$plugin][$name])) {
                        debugging('Setting ' . $plugin . '/' . $name . ' not supported by this Moodle version', DEBUG_DEVELOPER);
                        continue;
                    }

                    // Cleaning the setting value.
                    if (!$presetsetting = $this->get_setting($sitesettings[$plugin][$name]->get_settingdata(), $value)) {
                        debugging('Setting ' . $plugin . '/' . $name . ' not implemented', DEBUG_DEVELOPER);
                        continue;
                    }

                    $settingsfound = true;

                    // New item.
                    $item = new stdClass();
                    $item->adminpresetid = $preset->id;
                    $item->plugin = $plugin;
                    $item->name = $name;
                    $item->value = $presetsetting->get_value();

                    // Insert preset item.
                    if (!$item->id = $DB->insert_record('adminpresets_it', $item)) {
                        throw new moodle_exception('errorinserting', 'core_adminpresets');
                    }

                    // Add setting attributes.
                    if ($setting->attributes() && ($itemattributes = $presetsetting->get_attributes())) {
                        foreach ($setting->attributes() as $attrname => $attrvalue) {
                            $itemattributenames = array_flip($itemattributes);

                            // Check the attribute existence.
                            if (!isset($itemattributenames[$attrname])) {
                                debugging('The ' . $plugin . '/' . $name . ' attribute ' . $attrname .
                                        ' is not supported by this Moodle version', DEBUG_DEVELOPER);
                                continue;
                            }

                            $attr = new stdClass();
                            $attr->itemid = $item->id;
                            $attr->name = $attrname;
                            $attr->value = $attrvalue->__toString();
                            $DB->insert_record('adminpresets_it_a', $attr);
                        }
                    }
                }
            }
        }

        // Process plugins.
        if ($xml->PLUGINS) {
            $xmlplugins = $xml->PLUGINS[0];
            foreach ($xmlplugins as $plugin => $plugins) {
                $pluginname = strtolower($plugin);
                foreach ($plugins->children() as $name => $plugin) {
                    $pluginsfound = true;

                    // New plugin.
                    $entry = new stdClass();
                    $entry->adminpresetid = $preset->id;
                    $entry->plugin = $pluginname;
                    $entry->name = strtolower($name);
                    $entry->enabled = $plugin->__toString();

                    // Insert plugin.
                    if (!$entry->id = $DB->insert_record('adminpresets_plug', $entry)) {
                        throw new moodle_exception('errorinserting', 'core_adminpresets');
                    }
                }
            }
        }

        // If there are no valid or selected settings we should delete the admin preset record.
        if (!$settingsfound && !$pluginsfound) {
            $DB->delete_records('adminpresets', ['id' => $preset->id]);
            $preset = null;
        }

        return [$xml, $preset, $settingsfound, $pluginsfound];
    }

    /**
     * Delete given preset.
     *
     * @param int $presetid Preset identifier to delete.
     * @return void
     */
    public function delete_preset(int $presetid): void {
        global $DB;

        // Check the preset exists.
        $preset = $DB->get_record('adminpresets', ['id' => $presetid]);
        if (!$preset) {
            throw new moodle_exception('errordeleting', 'core_adminpresets');
        }

        // Deleting the preset applications.
        if ($previouslyapplied = $DB->get_records('adminpresets_app', ['adminpresetid' => $presetid], 'id')) {
            $appids = array_keys($previouslyapplied);
            list($insql, $inparams) = $DB->get_in_or_equal($appids);
            $DB->delete_records_select('adminpresets_app_it', "adminpresetapplyid $insql", $inparams);
            $DB->delete_records_select('adminpresets_app_it_a', "adminpresetapplyid $insql", $inparams);
            $DB->delete_records_select('adminpresets_app_plug', "adminpresetapplyid $insql", $inparams);

            if (!$DB->delete_records('adminpresets_app', ['adminpresetid' => $presetid])) {
                throw new moodle_exception('errordeleting', 'core_adminpresets');
            }
        }

        // Getting items ids and remove advanced items associated to them.
        $items = $DB->get_records('adminpresets_it', ['adminpresetid' => $presetid], 'id');
        if (!empty($items)) {
            $itemsid = array_keys($items);
            list($insql, $inparams) = $DB->get_in_or_equal($itemsid);
            $DB->delete_records_select('adminpresets_it_a', "itemid $insql", $inparams);
        }

        if (!$DB->delete_records('adminpresets_it', ['adminpresetid' => $presetid])) {
            throw new moodle_exception('errordeleting', 'core_adminpresets');
        }

        // Delete plugins.
        if (!$DB->delete_records('adminpresets_plug', ['adminpresetid' => $presetid])) {
            throw new moodle_exception('errordeleting', 'core_adminpresets');
        }

        // Delete preset.
        if (!$DB->delete_records('adminpresets', ['id' => $presetid])) {
            throw new moodle_exception('errordeleting', 'core_adminpresets');
        }
    }

    /**
     * Revert a given preset applied previously.
     * It backs settings and plugins to their original state before applying the presset and removes
     * the applied preset information from DB.
     *
     * @param int $presetappid The appplied preset identifier to be reverted.
     * @return array List with the presetapp removed (or null if there was some error), an array with the rollback settings/plugins
     *               changed and an array with the failures.
     */
    public function revert_preset(int $presetappid): array {
        global $DB;

        // To store rollback results.
        $presetapp = null;
        $rollback = [];
        $failures = [];

        // Actual settings.
        $sitesettings = $this->get_site_settings();

        if (!$DB->get_record('adminpresets_app', ['id' => $presetappid])) {
            throw new moodle_exception('wrongid', 'core_adminpresets');
        }

        // Items.
        $itemsql = "SELECT cl.id, cl.plugin, cl.name, cl.value, cl.oldvalue, ap.adminpresetapplyid
                      FROM {adminpresets_app_it} ap
                      JOIN {config_log} cl ON cl.id = ap.configlogid
                     WHERE ap.adminpresetapplyid = :presetid";
        $itemchanges = $DB->get_records_sql($itemsql, ['presetid' => $presetappid]);
        if ($itemchanges) {
            foreach ($itemchanges as $change) {
                if ($change->plugin == '') {
                    $change->plugin = 'none';
                }

                // Admin setting.
                if (!empty($sitesettings[$change->plugin][$change->name])) {
                    $actualsetting = $sitesettings[$change->plugin][$change->name];
                    $oldsetting = $this->get_setting($actualsetting->get_settingdata(), $change->oldvalue);

                    $visiblepluginname = $oldsetting->get_settingdata()->plugin;
                    if ($visiblepluginname == 'none') {
                        $visiblepluginname = 'core';
                    }
                    $contextdata = [
                        'plugin' => $visiblepluginname,
                        'visiblename' => $oldsetting->get_settingdata()->visiblename,
                        'oldvisiblevalue' => $actualsetting->get_visiblevalue(),
                        'visiblevalue' => $oldsetting->get_visiblevalue()
                    ];

                    // Check if the actual value is the same set by the preset.
                    if ($change->value == $actualsetting->get_value()) {
                        $oldsetting->save_value();

                        // Output table.
                        $rollback[] = $contextdata;

                        // Deleting the adminpreset applied item instance.
                        $deletewhere = [
                            'adminpresetapplyid' => $change->adminpresetapplyid,
                            'configlogid' => $change->id,
                        ];
                        $DB->delete_records('adminpresets_app_it', $deletewhere);

                    } else {
                        $failures[] = $contextdata;
                    }
                }
            }
        }

        // Attributes.
        $attrsql = "SELECT cl.id, cl.plugin, cl.name, cl.value, cl.oldvalue, ap.itemname, ap.adminpresetapplyid
                      FROM {adminpresets_app_it_a} ap
                      JOIN {config_log} cl ON cl.id = ap.configlogid
                     WHERE ap.adminpresetapplyid = :presetid";
        $attrchanges = $DB->get_records_sql($attrsql, ['presetid' => $presetappid]);
        if ($attrchanges) {
            foreach ($attrchanges as $change) {
                if ($change->plugin == '') {
                    $change->plugin = 'none';
                }

                // Admin setting of the attribute item.
                if (!empty($sitesettings[$change->plugin][$change->itemname])) {
                    // Getting the attribute item.
                    $actualsetting = $sitesettings[$change->plugin][$change->itemname];

                    $oldsetting = $this->get_setting($actualsetting->get_settingdata(), $actualsetting->get_value());
                    $oldsetting->set_attribute_value($change->name, $change->oldvalue);

                    $varname = $change->plugin . '_' . $change->name;

                    // Check if the actual value is the same set by the preset.
                    $actualattributes = $actualsetting->get_attributes_values();
                    if ($change->value == $actualattributes[$change->name]) {
                        $oldsetting->save_attributes_values();

                        // Output table.
                        $visiblepluginname = $oldsetting->get_settingdata()->plugin;
                        if ($visiblepluginname == 'none') {
                            $visiblepluginname = 'core';
                        }
                        $rollback[] = [
                            'plugin' => $visiblepluginname,
                            'visiblename' => $oldsetting->get_settingdata()->visiblename,
                            'oldvisiblevalue' => $actualsetting->get_visiblevalue(),
                            'visiblevalue' => $oldsetting->get_visiblevalue()
                        ];

                        // Deleting the adminpreset applied item attribute instance.
                        $deletewhere = [
                            'adminpresetapplyid' => $change->adminpresetapplyid,
                            'configlogid' => $change->id,
                        ];
                        $DB->delete_records('adminpresets_app_it_a', $deletewhere);

                    } else {
                        $visiblepluginname = $oldsetting->get_settingdata()->plugin;
                        if ($visiblepluginname == 'none') {
                            $visiblepluginname = 'core';
                        }
                        $failures[] = [
                            'plugin' => $visiblepluginname,
                            'visiblename' => $oldsetting->get_settingdata()->visiblename,
                            'oldvisiblevalue' => $actualsetting->get_visiblevalue(),
                            'visiblevalue' => $oldsetting->get_visiblevalue()
                        ];
                    }
                }
            }
        }

        // Plugins.
        $plugins = $DB->get_records('adminpresets_app_plug', ['adminpresetapplyid' => $presetappid]);
        if ($plugins) {
            $pluginmanager = \core_plugin_manager::instance();
            foreach ($plugins as $plugin) {
                $pluginclass = \core_plugin_manager::resolve_plugininfo_class($plugin->plugin);
                $pluginclass::enable_plugin($plugin->name, (int) $plugin->oldvalue);

                // Get the plugininfo object for this plugin, to get its proper visible name.
                $plugininfo = $pluginmanager->get_plugin_info($plugin->plugin . '_' . $plugin->name);
                if ($plugininfo != null) {
                    $visiblename = $plugininfo->displayname;
                } else {
                    $visiblename = $plugin->plugin . '_' . $plugin->name;
                }

                // Output table.
                $rollback[] = [
                    'plugin' => $plugin->plugin,
                    'visiblename' => $visiblename,
                    'oldvisiblevalue' => $plugin->value,
                    'visiblevalue' => $plugin->oldvalue,
                ];
            }
            $DB->delete_records('adminpresets_app_plug', ['adminpresetapplyid' => $presetappid]);
        }

        // Delete application if no items nor attributes nor plugins of the application remains.
        if (!$DB->get_records('adminpresets_app_it', ['adminpresetapplyid' => $presetappid]) &&
                !$DB->get_records('adminpresets_app_it_a', ['adminpresetapplyid' => $presetappid]) &&
                !$DB->get_records('adminpresets_app_plug', ['adminpresetapplyid' => $presetappid])) {

            $presetapp = $DB->get_record('adminpresets_app', ['id' => $presetappid]);
            $DB->delete_records('adminpresets_app', ['id' => $presetappid]);
        }

        return [$presetapp, $rollback, $failures];
    }

    /**
     * Apply settings from a preset.
     *
     * @param int $presetid The preset identifier to apply.
     * @param bool $simulate Whether this is a simulation or not.
     * @param int|null $adminpresetapplyid The identifier of the adminpresetapply or null if it hasn't been created previously.
     * @return array List with an array with the applied settings, another with the skipped ones and the adminpresetapplyid.
     */
    protected function apply_settings(int $presetid, bool $simulate = false, ?int $adminpresetapplyid = null): array {
        global $DB, $USER;

        $applied = [];
        $skipped = [];
        if (!$items = $DB->get_records('adminpresets_it', ['adminpresetid' => $presetid])) {
            return [$applied, $skipped, $adminpresetapplyid];
        }

        $presetdbsettings = $this->get_settings_from_db($items);
        // Standarized format: $array['plugin']['settingname'] = child class.
        $presetsettings = $this->get_settings($presetdbsettings, false, []);

        // Standarized format: $array['plugin']['settingname'] = child class.
        $siteavailablesettings = $this->get_site_settings();

        // Set settings values.
        foreach ($presetsettings as $plugin => $pluginsettings) {
            foreach ($pluginsettings as $settingname => $presetsetting) {
                $updatesetting = false;

                // Current value (which will become old value if the setting is legit to be applied).
                $sitesetting = $siteavailablesettings[$plugin][$settingname];

                // Wrong setting, set_value() method has previously cleaned the value.
                if ($sitesetting->get_value() === false) {
                    debugging($presetsetting->get_settingdata()->plugin . '/' . $presetsetting->get_settingdata()->name .
                            ' setting has a wrong value!', DEBUG_DEVELOPER);
                    continue;
                }

                // If the new value is different the setting must be updated.
                if ($presetsetting->get_value() != $sitesetting->get_value()) {
                    $updatesetting = true;
                }

                // If one of the setting attributes values is different, setting must also be updated.
                if ($presetsetting->get_attributes_values()) {

                    $siteattributesvalues = $presetsetting->get_attributes_values();
                    foreach ($presetsetting->get_attributes_values() as $attributename => $attributevalue) {

                        if ($attributevalue !== $siteattributesvalues[$attributename]) {
                            $updatesetting = true;
                        }
                    }
                }

                $visiblepluginname = $presetsetting->get_settingdata()->plugin;
                if ($visiblepluginname == 'none') {
                    $visiblepluginname = 'core';
                }
                $data = [
                    'plugin' => $visiblepluginname,
                    'visiblename' => $presetsetting->get_settingdata()->visiblename,
                    'visiblevalue' => $presetsetting->get_visiblevalue(),
                ];

                // Saving data.
                if ($updatesetting) {
                    // The preset application it's only saved when differences (in their values) are found.
                    if (empty($applieditem)) {
                        // Save the preset application and store the preset applied id.
                        $presetapplied = new stdClass();
                        $presetapplied->adminpresetid = $presetid;
                        $presetapplied->userid = $USER->id;
                        $presetapplied->time = time();
                        if (!$simulate && !$adminpresetapplyid = $DB->insert_record('adminpresets_app', $presetapplied)) {
                            throw new moodle_exception('errorinserting', 'core_adminpresets');
                        }
                    }

                    // Implemented this way because the config_write method of admin_setting class does not return the
                    // config_log inserted id.
                    $applieditem = new stdClass();
                    $applieditem->adminpresetapplyid = $adminpresetapplyid;
                    if (!$simulate && $applieditem->configlogid = $presetsetting->save_value()) {
                        $DB->insert_record('adminpresets_app_it', $applieditem);
                    }

                    // For settings with multiple values.
                    if (!$simulate && $attributeslogids = $presetsetting->save_attributes_values()) {
                        foreach ($attributeslogids as $attributelogid) {
                            $applieditemattr = new stdClass();
                            $applieditemattr->adminpresetapplyid = $applieditem->adminpresetapplyid;
                            $applieditemattr->configlogid = $attributelogid;
                            $applieditemattr->itemname = $presetsetting->get_settingdata()->name;
                            $DB->insert_record('adminpresets_app_it_a', $applieditemattr);
                        }
                    }

                    // Added to changed values.
                    $data['oldvisiblevalue'] = $sitesetting->get_visiblevalue();
                    $applied[] = $data;
                } else {
                    // Unnecessary changes (actual setting value).
                    $skipped[] = $data;
                }
            }
        }
        return [$applied, $skipped, $adminpresetapplyid];
    }

    /**
     * Apply plugins from a preset.
     *
     * @param int $presetid The preset identifier to apply.
     * @param bool $simulate Whether this is a simulation or not.
     * @param int|null $adminpresetapplyid The identifier of the adminpresetapply or null if it hasn't been created previously.
     * @return array List with an array with the applied settings, another with the skipped ones and the adminpresetapplyid.
     */
    protected function apply_plugins(int $presetid, bool $simulate = false, ?int $adminpresetapplyid = null): array {
        global $DB, $USER;

        $applied = [];
        $skipped = [];

        $strenabled = get_string('enabled', 'core_adminpresets');
        $strdisabled = get_string('disabled', 'core_adminpresets');

        $plugins = $DB->get_records('adminpresets_plug', ['adminpresetid' => $presetid]);
        $pluginmanager = \core_plugin_manager::instance();
        foreach ($plugins as $plugin) {
            $pluginclass = \core_plugin_manager::resolve_plugininfo_class($plugin->plugin);
            $oldvalue = $pluginclass::get_enabled_plugin($plugin->name);

            // Get the plugininfo object for this plugin, to get its proper visible name.
            $plugininfo = $pluginmanager->get_plugin_info($plugin->plugin . '_' . $plugin->name);
            if ($plugininfo != null) {
                $visiblename = $plugininfo->displayname;
            } else {
                $visiblename = $plugin->plugin . '_' . $plugin->name;
            }

            if ($plugin->enabled > 0) {
                $visiblevalue = $strenabled;
            } else if ($plugin->enabled == 0) {
                $visiblevalue = $strdisabled;
            } else {
                $visiblevalue = get_string('disabledwithvalue', 'core_adminpresets', $plugin->enabled);
            }

            $data = [
                'plugin' => $plugin->plugin,
                'visiblename' => $visiblename,
                'visiblevalue' => $visiblevalue,
            ];

            if ($pluginclass == '\core\plugininfo\orphaned') {
                $skipped[] = $data;
                continue;
            }

            // Only change the plugin visibility if it's different to current value.
            if (($plugin->enabled != $oldvalue) && (($plugin->enabled > 0 && !$oldvalue) || ($plugin->enabled < 1 && $oldvalue))) {
                try {
                    if (!$simulate) {
                        $pluginclass::enable_plugin($plugin->name, $plugin->enabled);

                        // The preset application it's only saved when values differences are found.
                        if (empty($adminpresetapplyid)) {
                            // Save the preset application and store the preset applied id.
                            $presetapplied = new stdClass();
                            $presetapplied->adminpresetid = $presetid;
                            $presetapplied->userid = $USER->id;
                            $presetapplied->time = time();
                            if (!$adminpresetapplyid = $DB->insert_record('adminpresets_app', $presetapplied)) {
                                throw new moodle_exception('errorinserting', 'core_adminpresets');
                            }
                        }

                        // Add plugin to aplied plugins table (for being able to restore in the future if required).
                        $appliedplug = new stdClass();
                        $appliedplug->adminpresetapplyid = $adminpresetapplyid;
                        $appliedplug->plugin = $plugin->plugin;
                        $appliedplug->name = $plugin->name;
                        $appliedplug->value = $plugin->enabled;
                        $appliedplug->oldvalue = $oldvalue;
                        $DB->insert_record('adminpresets_app_plug', $appliedplug);
                    }

                    if ($oldvalue > 0) {
                        $oldvisiblevalue = $strenabled;
                    } else if ($oldvalue == 0) {
                        $oldvisiblevalue = $strdisabled;
                    } else {
                        $oldvisiblevalue = get_string('disabledwithvalue', 'core_adminpresets', $oldvalue);
                    }
                    $data['oldvisiblevalue'] = $oldvisiblevalue;
                    $applied[] = $data;
                } catch (\exception $e) {
                    $skipped[] = $data;
                }
            } else {
                $skipped[] = $data;
            }
        }

        return [$applied, $skipped, $adminpresetapplyid];
    }

}
