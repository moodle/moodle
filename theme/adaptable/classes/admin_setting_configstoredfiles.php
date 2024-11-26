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
 * Adaptable theme.
 *
 * @package    theme_adaptable
 * @copyright  2024 G J Barnard
 * @author     G J Barnard -
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace theme_adaptable;

use context_system;
use context_user;
use core\url;

defined('MOODLE_INTERNAL') || die;

// Require admin library.
require_once($CFG->libdir.'/adminlib.php');

/**
 * Adaptable admin_setting_configstoredfiles
 */
class admin_setting_configstoredfiles extends \admin_setting_configstoredfile {

    /** @var array of strings used for detection of changes */
    protected $oldhashes;

    /** @var object the owner if set */
    protected $owner;

    /** @var string Added files */
    public const ADDEDFILES = 'added';

    /** @var string Removed files */
    public const REMOVEDFILES = 'removed';

    /**
     * Create new stored files setting.
     *
     * @param string $name low level setting name.
     * @param string $visiblename human readable setting name.
     * @param string $description description of setting.
     * @param mixed $filearea file area for file storage.
     * @param array $options file area options.
     * @param int $itemid itemid for file storage.
     */
    public function __construct($name, $visiblename, $description, $filearea, ?array $options, $itemid = 0) {
        $this->oldhashes = [];
        $this->owner = null;
        parent::__construct($name, $visiblename, $description, $filearea, $itemid, $options);
    }

    /**
     * Applies defaults and returns all options.
     * @return array Defaults.
     */
    protected function get_options() {
        global $CFG;

        require_once("$CFG->libdir/filelib.php");
        require_once("$CFG->dirroot/repository/lib.php");
        $defaults = [
            'mainfile' => '', 'subdirs' => 0, 'maxbytes' => -1, 'maxfiles' => 10,
            'accepted_types' => '*', 'return_types' => FILE_INTERNAL, 'areamaxbytes' => FILE_AREA_MAX_BYTES_UNLIMITED,
            'context' => context_system::instance()];
        foreach ($this->options as $k => $v) {
            $defaults[$k] = $v;
        }

        return $defaults;
    }

    /**
     * Set the owner.
     *
     * @param object The owner.
     */
    public function set_owner($owner) {
        $this->owner = $owner;
    }

    /**
     * Return part of form with setting
     * This function should always be overwritten
     *
     * @param mixed $data array or string depending on setting
     * @param string $query
     * @return string
     */
    public function output_html($data, $query='') {
        if ((!is_null($this->owner)) && (method_exists($this->owner, 'get_disabled'))) {
            if ($this->owner->get_disabled()) {
                global $OUTPUT;

                $this->write_setting('');

                $context = new \stdClass();
                $context->title = $this->visiblename;
                $context->description = $this->description;

                return $OUTPUT->render_from_template('core_admin/setting_description', $context);
            }
        }
        return parent::output_html($data, $query);
    }

    /**
     * Store new setting values.
     *
     * @param mixed $data string or array, must not be NULL.
     * @return string empty string if ok, string error message otherwise.
     */
    public function write_setting($data) {
        global $USER;

        // Let's not deal with validation here, this is for admins only.
        $current = $this->get_setting();
        if (empty($data) && ($current === null || $current === '')) {
            // This will be the case when applying default settings (installation).
            return ($this->config_write($this->name, '') ? '' : get_string('errorsetting', 'admin'));
        } else if (!is_number($data)) {
            // Draft item id is expected here!
            return get_string('errorsetting', 'admin');
        }

        $options = $this->get_options();
        $fs = get_file_storage();
        $component = is_null($this->plugin) ? 'core' : $this->plugin;

        $this->oldhashes = [];
        if ($current) {
            $files = $fs->get_area_files(
                $options['context']->id, $component, $this->filearea, $this->itemid, 'sortorder,filepath,filename', false);
            foreach ($files as $file) {
                $filepath = $file->get_filepath().$file->get_filename();
                $this->oldhashes[$filepath] = $file->get_contenthash().$file->get_pathnamehash();
            }
            unset($files);
        }

        if ($fs->file_exists($options['context']->id, $component, $this->filearea, $this->itemid, '/', '.')) {
            // Make sure the settings form was not open for more than 4 days and draft areas deleted in the meantime.
            // But we can safely ignore that if the destination area is empty, so that the user is not prompt
            // with an error because the draft area does not exist, as he did not use it.
            $usercontext = \context_user::instance($USER->id);
            if (!$fs->file_exists($usercontext->id, 'user', 'draft', $data, '/', '.') && $current !== '') {
                return get_string('errorsetting', 'admin');
            }
        }

        file_save_draft_area_files($data, $options['context']->id, $component, $this->filearea, $this->itemid, $options);
        $files = $fs->get_area_files(
            $options['context']->id, $component, $this->filearea, $this->itemid, 'sortorder,filepath,filename', false);

        $filepath = '';
        if ($files) {
            /** @var stored_file $file */
            $file = reset($files);
            $filepath = $file->get_filepath().$file->get_filename();
        }

        return ($this->config_write($this->name, $filepath) ? '' : get_string('errorsetting', 'admin'));
    }

    /**
     * Execute postupdatecallback if necessary.
     * @param mixed $original original value before write_setting().
     * @return bool true if changed, false if not.
     */
    public function post_write_settings($original) {
        $options = $this->get_options();
        $fs = get_file_storage();
        $component = is_null($this->plugin) ? 'core' : $this->plugin;

        $current = $this->get_setting();
        $newhashes = [];
        if ($current) {
            $files = $fs->get_area_files(
                $options['context']->id, $component, $this->filearea, $this->itemid, 'sortorder,filepath,filename', false);
            foreach ($files as $file) {
                $filepath = $file->get_filepath().$file->get_filename();
                $newhashes[$filepath] = $file->get_contenthash().$file->get_pathnamehash();;
            }
            unset($files);
        }

        $callcallback = false;
        if (count($this->oldhashes) != count($newhashes)) {
            // Definitely changed!
            $callcallback = true;
        } else {
            // Same number of files.
            foreach ($this->oldhashes as $oldhashkey => $oldhash) {
                if (array_key_exists($oldhashkey, $newhashes)) {
                    if ($newhashes[$oldhashkey] !== $oldhash) {
                        $callcallback = true;
                        break;
                    }
                } else {
                    // Different collection of files.
                    $callcallback = true;
                    break;
                }
            }
        }
        $this->oldhashes = [];
        if (!$callcallback) {
            return false;
        }

        $callbackfunction = $this->updatedcallback;
        if (!empty($callbackfunction) && function_exists($callbackfunction)) {
            $callbackfunction($this->get_full_name());
        }
        return true;
    }

    /**
     * Base 64 encode.
     */
    public function base64encode() {
        global $PAGE, $USER;
        $component = is_null($this->plugin) ? 'theme_adaptable' : $this->plugin;
        $itemid = theme_get_revision();
        $syscontext = context_system::instance();

        $fs = get_file_storage();
        $files = $fs->get_area_files(
            $syscontext->id, $component, $this->filearea, 0, 'sortorder,filepath,filename', false);  // Item id could not be 0!

        $settingfiles = [];
        foreach ($files as $file) {
            $filename = $file->get_filename();
            $orginalcreated = $file->get_timecreated();
            $filecontent = $file->get_content();
            $base64enc = base64_encode($filecontent);

            $settingarrfile = [
                'filepath' => $file->get_filepath(),
                'filename' => $file->get_filename(),
                'author' => $file->get_author(),
                'license' => $file->get_license(),
                'timecreated' => $file->get_timecreated(),
                'timemodified' => $file->get_timemodified(),
                'mimetype' => $file->get_mimetype(),
                'content' => $base64enc,
            ];
            $settingfilejson = json_encode($settingarrfile, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
            $settingfileent = htmlentities($settingfilejson, ENT_COMPAT);
            $settingfiles[] = $settingfileent;
        }

        $settingfilesjson = json_encode($settingfiles, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
        $settingarr = [$this->filearea => $settingfilesjson];
        $settingarrjson = json_encode($settingarr, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);

        return $settingarrjson;
    }

    /**
     * Base 64 decode.
     *
     * @param string $settingarrjson JSON of setting.
     *
     * @return array Added and removed filenames.
     */
    public function base64decode($settingarrjson) {
        global $USER;

        $changed = [self::ADDEDFILES => [], self::REMOVEDFILES => []];

        $component = is_null($this->plugin) ? 'theme_adaptable' : $this->plugin;
        $syscontext = context_system::instance();

        $settingarrjsondec = json_decode($settingarrjson, true);
        $settingarrdec = $settingarrjsondec[$this->filearea];

        $settingsfilesjsondec = json_decode($settingarrdec, true);

        $fs = get_file_storage();
        $draftfiles = [];
        foreach ($settingsfilesjsondec as $settingsfilejsondec) {
            $settingfileentdec = html_entity_decode($settingsfilejsondec, ENT_COMPAT);
            $settingfilejsondec = json_decode($settingfileentdec, true);

            $base64dec = base64_decode($settingfilejsondec['content']);
            $filerecord = [
                'contextid' => context_user::instance($USER->id)->id,
                'component' => 'user',
                'filearea' => 'draft',
                'itemid' => file_get_unused_draft_itemid(),
                'filepath' => '/',
                'filename' => $settingfilejsondec['filename'],
                // Don't use userid as could be different!
                'author' => $settingfilejsondec['author'],
                'license' => $settingfilejsondec['license'],
                'timecreated' => time(),
                'timemodified' => time(),
                'mimetype' => $settingfilejsondec['mimetype'],
            ];
            $draftfiles[] = $fs->create_file_from_string($filerecord, $base64dec); // Draft.
        }

        // Got this far with all the draft files created...
        $files = $fs->get_area_files(
            $syscontext->id, $component, $this->filearea, 0, 'sortorder,filepath,filename', false);  // Item id could not be 0!
        foreach ($files as $file) {
            if (!empty($file)) {
                $changed[self::REMOVEDFILES][] = $file->get_filename();
                $file->delete();
            }
        }

        foreach ($draftfiles as $draftfile) {
            $filerecord = [
                'contextid' => $syscontext->id,
                'component' => $component,
                'filearea' => $this->filearea,
                'itemid' => '0',
                'filepath' => $draftfile->get_filepath(),
                'filename' => $draftfile->get_filename(),
                'author' => $draftfile->get_author(),
                'license' => $draftfile->get_license(),
                'timecreated' => $draftfile->get_timecreated(),
                'timemodified' => $draftfile->get_timemodified(),
                'mimetype' => $draftfile->get_mimetype(),
            ];
            $settingfile = $fs->create_file_from_string($filerecord, $draftfile->get_content()); // Replacement.
            $changed[self::ADDEDFILES][] = $settingfile->get_filename();

            $draftfile->delete(); // Finished with draft.
        }

        $files = $fs->get_area_files(
            $syscontext->id, $component, $this->filearea, 0, 'sortorder,filepath,filename', false);  // Item id could not be 0!
        $filepath = '';
        if ($files) {
            /** @var stored_file $file */
            $file = reset($files);
            $filepath = $file->get_filepath().$file->get_filename();
        }
        $result = ($this->config_write($this->filearea, $filepath) ? '' : get_string('errorsetting', 'admin'));
        $callbackfunction = $this->updatedcallback;
        if (!empty($callbackfunction) && is_callable($callbackfunction)) {
            $callbackfunction($this->get_full_name());
        }

        return $changed;
    }

    // Adapted from theme_config class.
    /**
     * Returns URL to the stored file via pluginfile.php.
     *
     * Note the theme must also implement pluginfile.php handler,
     * theme revision is used instead of the itemid.
     *
     * @param string $setting
     * @param string $filearea
     * @param theme_config $theme
     * @return array protocol relative URLs or empty if not present.
     */
    public static function setting_file_urls($setting, $filearea, $theme) {
        global $CFG;

        if (empty($theme->settings->$setting)) {
            return null;
        }

        $component = 'theme_'.$theme->name;
        $itemid = theme_get_revision();
        $syscontext = context_system::instance();
        $urls = [];

        $fs = get_file_storage();
        $files = $fs->get_area_files(
            $syscontext->id, $component, $filearea, 0, 'sortorder,filepath,filename', false);  // Item id could not be 0!
        foreach ($files as $file) {
            $filepath = $file->get_filepath().$file->get_filename();
            $url = url::make_file_url(
                "$CFG->wwwroot/pluginfile.php", "/$syscontext->id/$component/$filearea/$itemid".$filepath);
            // Now this is tricky because the we can not hardcode http or https here, lets use the relative link.
            // Note: unfortunately url does not support //urls yet.
            $url = preg_replace('|^https?://|i', '//', $url->out(false));
            $urls[] = $url;
        }

        return $urls;
    }
}
