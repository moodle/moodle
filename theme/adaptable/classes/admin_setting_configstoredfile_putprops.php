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
 * Upload a properties file but don't store.
 *
 * @package    theme_adaptable
 * @copyright  2024 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace theme_adaptable;

use context_user;

/**
 * Upload a properties file but don't store.
 */
class admin_setting_configstoredfile_putprops extends \admin_setting_configstoredfile {
    /** @var string Name of the plugin. */
    private $pluginname;
    /** @var string Frankenstyle of the plugin. */
    private $pluginfrankenstyle;
    /** @var string Name of the 'callable' function to call with the name of the theme and the properties as an array. */
    private $callme;
    /** @var string Setting name (same plugin) for the report to be written to. */
    private $reportsettingname;
    /** @var admin_setting_putprops Pointer to admin_setting_putprops setting instance for writing the report. */
    private $adminsettingputprops = null;

    /**
     * Create new stored files setting.
     *
     * @param string $name :ow level setting name.
     * @param string $visiblename Human readable setting name.
     * @param string $description Description of setting.
     * @param mixed $filearea File area for file storage.
     * @param string $pluginname Name of the plugin.
     * @param string $pluginfrankenstyle Frankenstyle of the plugin.
     * @param string $callme Name of the 'callable' function to call with the name of the theme and the properties as an array.
     * @param string $reportsettingname Setting name (same plugin) for the report to be written to
     * @param array $options File area options.
     * @param int $itemid itemid for file storage.
     */
    public function __construct(
        $name, $visiblename, $description, $filearea, $pluginname, $pluginfrankenstyle, $callme, $reportsettingname,
            ?array $options, $itemid = 0) {
        $this->nosave = true;
        $this->pluginname = $pluginname;
        $this->pluginfrankenstyle = $pluginfrankenstyle;
        $this->callme = $callme;
        $this->reportsettingname = $reportsettingname;
        parent::__construct($name, $visiblename, $description, $filearea, $itemid, $options);
    }

    /**
     * Get setting method.
     * @return none
     */
    public function get_setting() {
        return '';
    }

    /**
     * Get default settings.
     * @return string ''
     */
    public function get_defaultsetting() {
        return '';
    }

    /**
     * Tell us the admin_setting_putprops instance.
     */
    public function set_admin_setting_putprops($adminsettingputprops) {
        $this->adminsettingputprops = $adminsettingputprops;
    }

    /**
     * Process the uploaded file.
     *
     * @param string $data Draft item id.
     *
     * @return mixed Result of processing.
     */
    public function write_setting($data) {
        global $USER;

        if (!empty($data)) {
            if (!is_number($data)) {
                // Draft item id is expected here!
                return get_string('errorsetting', 'admin');
            }
            $fs = get_file_storage();
            $component = is_null($this->plugin) ? 'core' : $this->plugin;
            // Make sure the settings form was not open for more than 4 days and draft areas deleted in the meantime.
            // But we can safely ignore that if the destination area is empty, so that the user is not prompt
            // with an error because the draft area does not exist, as they did not use it.
            $usercontext = context_user::instance($USER->id);
            if (!$fs->file_exists($usercontext->id, 'user', 'draft', $data, '/', '.')) {
                // Draft file does not exist for some reason!
                return '';
            }
            $files = $fs->get_area_files($usercontext->id, 'user', 'draft', $data, 'sortorder,filepath,filename', false);
            if ($files) {
                /** @var stored_file $file */
                $file = reset($files);
            }
            if ($file) {
                // Only attempt decode if we have the start of a JSON string, otherwise will certainly be the saved report.
                $filedata = $file->get_content();
                if ((!empty($filedata)) && ($filedata[0] == '{')) {
                    $props = json_decode($filedata, true);
                    if ($props === null) {
                        if (function_exists('json_last_error_msg')) {
                            $validated = json_last_error_msg();
                        } else {
                            // Fall back to numeric error for older PHP version.
                            $validated = json_last_error();
                        }
                    } else {
                        $report = call_user_func($this->callme, $this->pluginname, $this->pluginfrankenstyle, $props);
                        if (!empty($this->adminsettingputprops)) {
                            $this->adminsettingputprops->set_filereport($report);
                        }

                        // Success.
                        $callbackfunction = $this->updatedcallback;
                        if (!empty($callbackfunction) && function_exists($callbackfunction)) {
                            $callbackfunction($this->get_full_name());
                        }
                    }
                } else {
                    return get_string('errorsetting', 'admin');
                }
            } else {
                return get_string('errorsetting', 'admin');
            }
        }

        return '';
    }
}
