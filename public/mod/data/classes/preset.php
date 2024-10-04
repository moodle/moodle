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

namespace mod_data;

use core_component;
use invalid_parameter_exception;
use data_field_base;
use moodle_exception;
use SimpleXMLElement;
use stdClass;
use stored_file;

/**
 * Class preset for database activity.
 *
 * @package    mod_data
 * @copyright  2022 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class preset {

    /** @var manager manager instance. */
    private $manager;

    /** @var bool whether the preset is a plugin or has been saved by the user. */
    public $isplugin;

    /** @var string The preset name. */
    public $name;

    /** @var string The preset shortname. For datapreset plugins that is the folder; for saved presets, that's the preset name. */
    public $shortname;

    /** @var string The preset description. */
    public $description;

    /** @var stored_file For saved presets that's the file object for the root folder. It's null for plugins or for presets that
     *  haven't been saved yet. */
    public $storedfile;

    /** @var array|null the field sample instances. */
    private $fields = null;

    /** @var stdClass|null the preset.xml parsed information. */
    protected $xmlinfo = null;

    /**
     * Class constructor.
     *
     * @param manager|null $manager the current instance manager
     * @param bool $isplugin whether the preset is a plugin or has been saved by the user
     * @param string $name the preset name
     * @param string $shortname the preset shortname
     * @param string|null $description the preset description
     * @param stored_file|null $storedfile for saved presets, that's the file for the root folder
     * @throws invalid_parameter_exception
     */
    protected function __construct(
        ?manager $manager,
        bool $isplugin,
        string $name,
        string $shortname,
        ?string $description = '',
        ?stored_file $storedfile = null
    ) {
        if (!$isplugin && is_null($manager)) {
            throw new invalid_parameter_exception('The $manager parameter can only be null for plugin presets.');
        }
        $this->manager = $manager;
        $this->isplugin = $isplugin;
        $this->name = $name;
        $this->shortname = $shortname;
        $this->description = $description;
        $this->storedfile = $storedfile;
    }

    /**
     * Create a preset instance from a stored file.
     *
     * @param manager $manager the current instance manager
     * @param stored_file $file the preset root folder
     * @return preset|null If the given file doesn't belong to the expected component/filearea/context, null will be returned
     */
    public static function create_from_storedfile(manager $manager, stored_file $file): ?self {
        if ($file->get_component() != DATA_PRESET_COMPONENT
                || $file->get_filearea() != DATA_PRESET_FILEAREA
                || $file->get_contextid() != DATA_PRESET_CONTEXT) {
            return null;
        }

        $isplugin = false;
        $name = trim($file->get_filepath(), '/');
        $description = static::get_attribute_value($file->get_filepath(), 'description');

        return new self($manager, $isplugin, $name, $name, $description, $file);
    }

    /**
     * Create a preset instance from a plugin.
     *
     * @param manager|null $manager the current instance manager
     * @param string $pluginname the datapreset plugin name
     * @return preset|null The plugin preset or null if there is no datapreset plugin with the given name.
     */
    public static function create_from_plugin(?manager $manager, string $pluginname): ?self {
        $found = false;

        $plugins = array_keys(core_component::get_plugin_list('datapreset'));
        foreach ($plugins as $plugin) {
            if ($plugin == $pluginname) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            // If there is no datapreset plugin with this name, return null.
            return null;
        }

        $name = static::get_name_from_plugin($pluginname);
        $description = static::get_description_from_plugin($pluginname);

        return new self($manager, true, $name, $pluginname, $description);
    }

    /**
     * Create a preset instance from a data_record entry, a preset name and a description.
     *
     * @param manager $manager the current instance manager
     * @param string $presetname the preset name
     * @param string|null $description the preset description
     * @return preset
     */
    public static function create_from_instance(manager $manager, string $presetname, ?string $description = ''): self {
        $isplugin = false;

        $path = '/' . $presetname . '/';
        $file = static::get_file($path, '.');
        if (!is_null($file)) {
            // If the file is not empty, create the instance based on the storedfile.
            return self::create_from_storedfile($manager, $file);
        }

        return new self($manager, $isplugin, $presetname, $presetname, $description, $file);
    }

    /**
     * Create a preset instance from the preset fullname.
     *
     * The preset fullname is a concatenation of userid and pluginname|presetname used by most
     * preset pages. Plugins uses userid zero while preset instances has the owner as identifier.
     *
     * This method will throw an exception if the preset instance has a different userid thant the one
     * from the $fullname. However, it won't check the current user capabilities.
     *
     * @param manager $manager the current instance manager
     * @param string $fullname the preset full name
     * @return preset
     */
    public static function create_from_fullname(manager $manager, string $fullname): self {
        $parts = explode('/', $fullname, 2);
        $userid = empty($parts[0]) ? 0 : (int)$parts[0];
        $shortname = empty($parts[1]) ? '' : $parts[1];

        // Shortnames with userid zero are plugins.
        if ($userid == 0) {
            return static::create_from_plugin($manager, $shortname);
        }

        $path = '/' . $shortname . '/';
        $file = static::get_file($path, '.');
        $result = static::create_from_storedfile($manager, $file);
        if ($result->get_userid() != $userid) {
            throw new moodle_exception('invalidpreset', manager::PLUGINNAME);
        }
        return $result;
    }

    /**
     * Save this preset.
     *
     * @return bool true if the preset has been saved; false otherwise.
     */
    public function save(): bool {
        global $USER;

        if ($this->isplugin) {
            // Plugin presets can't be saved.
            return false;
        }

        if (!is_null($this->storedfile)) {
            // It's a pre-existing preset, so it needs to be updated.
            return $this->update_user_preset();
        }

        // The preset hasn't been saved before.
        $fs = get_file_storage();

        // Create and save the preset.xml file, with the description, settings, fields...
        $filerecord = static::get_filerecord('preset.xml', $this->get_path(), $USER->id);
        $fs->create_file_from_string($filerecord, $this->generate_preset_xml());

        // Create and save the template files.
        $instance = $this->manager->get_instance();
        foreach (manager::TEMPLATES_LIST as $templatename => $templatefile) {
            $filerecord->filename = $templatefile;
            $fs->create_file_from_string($filerecord, $instance->{$templatename});
        }
        // Update the storedfile with the one we've just saved.
        $this->storedfile = static::get_file($this->get_path(), '.');

        return true;
    }

    /**
     * Update the stored user preset.
     * This method is used internally by the save method.
     *
     * @return bool true if the preset has been saved; false otherwise.
     */
    private function update_user_preset(): bool {
        global $USER;

        $result = false;
        $shouldbesaved = false;

        // Update description (if required).
        $oldpresetfile = static::get_file($this->storedfile->get_filepath(), 'preset.xml');
        $presetxml = $oldpresetfile->get_content();
        $parsedxml = simplexml_load_string($presetxml);
        if (property_exists($parsedxml, 'description')) {
            if ($parsedxml->description != $this->description) {
                $parsedxml->description = $this->description;
                $shouldbesaved = true;
            }
        } else {
            if (!is_null($this->description)) {
                $parsedxml->addChild('description', $this->description);
                $shouldbesaved = true;
            }
        }

        // Update name (if required).
        $oldname = trim($this->storedfile->get_filepath(), '/');
        $newpath = '/' . $this->name . '/';
        if ($oldname != $this->name) {
            // Preset name has changed, so files need to be updated too because the preset name is saved in the filepath.
            foreach (manager::TEMPLATES_LIST as $templatename => $templatefile) {
                $oldfile = static::get_file($this->storedfile->get_filepath(), $templatefile);
                $oldfile->rename($newpath, $templatefile);
            }
            // The root folder should also be renamed.
            $this->storedfile->rename($newpath, $this->storedfile->get_filename());
            $shouldbesaved = true;
        }

        // Only save the new preset.xml if there are changes.
        if ($shouldbesaved) {
            // Before saving preset.xml, the old preset.xml file should be removed.
            $oldpresetfile->delete();
            // Create the new file with the new content.
            $filerecord = static::get_filerecord('preset.xml', $newpath, $USER->id);
            $presetcontent = $parsedxml->asXML();
            $fs = get_file_storage();
            $fs->create_file_from_string($filerecord, $presetcontent);
            $result = true;
        }

        return $result;
    }

    /**
     * Export this preset.
     *
     * @return string the full path to the exported preset file.
     */
    public function export(): string {
        if ($this->isplugin) {
            // For now, only saved presets can be exported.
            return '';
        }

        $presetname = clean_filename($this->name) . '-preset-' . gmdate("Ymd_Hi");
        $exportsubdir = "mod_data/presetexport/$presetname";
        $exportdir = make_temp_directory($exportsubdir);

        // Generate and write the preset.xml file.
        $presetxmldata = static::generate_preset_xml();
        $presetxmlfile = fopen($exportdir . '/preset.xml', 'w');
        fwrite($presetxmlfile, $presetxmldata);
        fclose($presetxmlfile);

        // Write the template files.
        $instance = $this->manager->get_instance();
        foreach (manager::TEMPLATES_LIST as $templatename => $templatefilename) {
            $templatefile = fopen("$exportdir/$templatefilename", 'w');
            fwrite($templatefile, $instance->{$templatename} ?? '');
            fclose($templatefile);
        }

        // Check if all files have been generated.
        if (! static::is_directory_a_preset($exportdir)) {
            throw new \moodle_exception('generateerror', 'data');
        }

        $presetfilenames = array_merge(array_values(manager::TEMPLATES_LIST), ['preset.xml']);

        $filelist = [];
        foreach ($presetfilenames as $filename) {
            $filelist[$filename] = $exportdir . '/' . $filename;
        }

        $exportfile = $exportdir.'.zip';
        file_exists($exportfile) && unlink($exportfile);

        $fp = get_file_packer('application/zip');
        $fp->archive_to_pathname($filelist, $exportfile);

        foreach ($filelist as $file) {
            unlink($file);
        }
        rmdir($exportdir);

        return $exportfile;
    }

    /**
     * Return the preset author.
     *
     * Preset plugins do not have any user id.
     *
     * @return int|null the userid or null if it is a plugin
     */
    public function get_userid(): ?int {
        if (!empty($this->storedfile)) {
            return $this->storedfile->get_userid();
        }
        return null;
    }

    /**
     * Return the preset fullname.
     *
     * Preset fullname is used mostly for urls.
     *
     * @return string the preset fullname
     */
    public function get_fullname(): string {
        $userid = $this->get_userid() ?? '0';
        return "{$userid}/{$this->shortname}";
    }

    /**
     * Returns the preset path.
     *
     * @return string|null the preset path is null for plugins and /presetname/ for saved presets.
     */
    public function get_path(): ?string {
        if ($this->isplugin) {
            return null;
        }

        if (!empty($this->storedfile)) {
            return $this->storedfile->get_filepath();
        }

        return '/' . $this->name . '/';
    }

    /**
     * Return the field instances of the preset.
     *
     * @param bool $forpreview if the fields are only for preview
     * @return data_field_base[]  and array with field objects
     */
    public function get_fields(bool $forpreview = false): array {
        if ($this->fields !== null) {
            return $this->fields;
        }
        // Parse the preset.xml file.
        $this->load_preset_xml();
        if (empty($this->xmlinfo) || empty($this->xmlinfo->field)) {
            $this->fields = [];
            return $this->fields;
        }
        // Generate field instances.
        $result = [];
        foreach ($this->xmlinfo->field as $fieldinfo) {
            $result[(string) $fieldinfo->name] = $this->get_field_instance($fieldinfo, count($result), $forpreview);
        }
        $this->fields = $result;
        return $result;
    }

    /**
     * Convert a preset.xml field data into field instance.
     *
     * @param SimpleXMLElement $fieldinfo the field xml information
     * @param int $id the field id to use
     * @param bool $forpreview if the field should support preview
     * @return data_field_base the field instance
     */
    private function get_field_instance(
        SimpleXMLElement $fieldinfo,
        int $id = 0,
        bool $forpreview = false
    ): data_field_base {
        global $CFG; // Some old field plugins require $CFG to be in the  scope.

        $fieldrecord = $this->get_fake_field_record($fieldinfo, $id);
        $instance = $this->manager->get_instance();
        $cm = $this->manager->get_coursemodule();

        // Include the plugin.
        $filepath = "{$this->manager->path}/field/{$fieldrecord->type}/field.class.php";
        if (file_exists($filepath)) {
            require_once($filepath);
        }
        $classname = "data_field_{$fieldrecord->type}";
        $newfield = null;
        if (class_exists($classname)) {
            $newfield = new $classname($fieldrecord, $instance, $cm);
            if ($forpreview && !$newfield->supports_preview()) {
                $newfield = new data_field_base($fieldrecord, $instance, $cm);
            }
        } else {
            $newfield = new data_field_base($fieldrecord, $instance, $cm);
        }
        if ($forpreview) {
            $newfield->set_preview(true);
        }
        return $newfield;
    }

    /**
     * Generate a fake field record fomr the preset.xml field data.
     *
     * @param SimpleXMLElement $fieldinfo the field xml information
     * @param int $id the field id to use
     * @return stdClass the fake record
     */
    private function get_fake_field_record(SimpleXMLElement $fieldinfo, int $id = 0): stdClass {
        $instance = $this->manager->get_instance();
        // Generate stub record.
        $fieldrecord = (object)[
            'id' => $id,
            'dataid' => $instance->id,
            'type' => (string) $fieldinfo->type,
            'name' => (string) $fieldinfo->name,
            'description' => (string) $fieldinfo->description ?? '',
            'required' => (int) $fieldinfo->required ?? 0,
        ];
        for ($i = 1; $i < 11; $i++) {
            $name = "param{$i}";
            $fieldrecord->{$name} = null;
            if (property_exists($fieldinfo, $name)) {
                $fieldrecord->{$name} = (string) $fieldinfo->{$name};
            }
        }
        return $fieldrecord;
    }

    /**
     * Return sample entries to preview this preset.
     *
     * @param int $count the number of entries to generate.
     * @return array of sample entries
     */
    public function get_sample_entries(int $count = 1): array {
        global $USER;
        $fields = $this->get_fields();
        $instance = $this->manager->get_instance();
        $entries = [];
        for ($current = 1; $current <= $count; $current++) {
            $entry = (object)[
                'id' => $current,
                'userid' => $USER->id,
                'groupid' => 0,
                'dataid' => $instance->id,
                'timecreated' => time(),
                'timemodified' => time(),
                'approved' => 1,
            ];
            // Add all necessary user fields.
            $userfieldsapi = \core_user\fields::for_userpic()->excluding('id');
            $fields = $userfieldsapi->get_required_fields();
            foreach ($fields as $field) {
                $entry->{$field} = $USER->{$field};
            }
            $entries[$current] = $entry;
        }
        return $entries;
    }

    /**
     * Load all the information from the preset.xml.
     */
    protected function load_preset_xml() {
        if (!empty($this->xmlinfo)) {
            return;
        }
        // Load everything from the XML.
        $presetxml = null;
        if ($this->isplugin) {
            $path = $this->manager->path . '/preset/' . $this->shortname . '/preset.xml';
            $presetxml = file_get_contents($path);
        } else {
            $presetxml = static::get_content_from_file($this->storedfile->get_filepath(), 'preset.xml');
        }
        $this->xmlinfo = simplexml_load_string($presetxml);
    }

    /**
     * Return the template content from the preset.
     *
     * @param string $templatename the template name
     * @return string the template content
     */
    public function get_template_content(string $templatename): string {
        $filename = "{$templatename}.html";
        if ($templatename == 'csstemplate') {
            $filename = "{$templatename}.css";
        }
        if ($templatename == 'jstemplate') {
            $filename = "{$templatename}.js";
        }
        if ($this->isplugin) {
            $path = $this->manager->path . '/preset/' . $this->shortname . '/' . $filename;
            $result = file_get_contents($path);
        } else {
            $result = static::get_content_from_file($this->storedfile->get_filepath(), $filename);
        }
        if (empty($result)) {
            return '';
        }
        return $result;
    }

    /**
     * Checks if a directory contains all the required files to define a preset.
     *
     * @param string $directory The patch to check if it contains the preset files or not.
     * @return bool True if the directory contains all the preset files; false otherwise.
     */
    public static function is_directory_a_preset(string $directory): bool {
        $status = true;
        $directory = rtrim($directory, '/\\') . '/';
        $presetfilenames = array_merge(array_values(manager::TEMPLATES_LIST), ['preset.xml']);
        foreach ($presetfilenames as $filename) {
            $status &= file_exists($directory.$filename);
        }

        return $status;
    }

    /**
     * Returns the best name to show for a datapreset plugin.
     *
     * @param string $pluginname The datapreset plugin name.
     * @return string The plugin preset name to display.
     */
    public static function get_name_from_plugin(string $pluginname): string {
        $pos = strpos($pluginname, '/');
        if ($pos !== false) {
             $pluginname = substr($pluginname, $pos + 1);
        }
        if (!strpos(trim($pluginname), ' ') && get_string_manager()->string_exists('modulename', 'datapreset_'.$pluginname)) {
            return get_string('modulename', 'datapreset_'.$pluginname);
        } else {
            return $pluginname;
        }
    }

    /**
     * Returns the description to show for a datapreset plugin.
     *
     * @param string $pluginname The datapreset plugin name.
     * @return string The plugin preset description to display.
     */
    public static function get_description_from_plugin(string $pluginname): string {
        if (get_string_manager()->string_exists('modulename_help', 'datapreset_'.$pluginname)) {
            return get_string('modulename_help', 'datapreset_'.$pluginname);
        } else {
            return '';
        }
    }

    /**
     * Helper to get the value of one of the elements in the presets.xml file.
     *
     * @param string $filepath The preset filepath.
     * @param string $name Attribute name to return.
     * @return string|null The attribute value; null if the it doesn't exist or the file is not a valid XML.
     */
    protected static function get_attribute_value(string $filepath, string $name): ?string {
        $value = null;
        $presetxml = static::get_content_from_file($filepath, 'preset.xml');
        $parsedxml = simplexml_load_string($presetxml);
        if ($parsedxml) {
            switch ($name) {
                case 'description':
                    if (property_exists($parsedxml, 'description')) {
                        $value = $parsedxml->description;
                    }
                    break;
            }
        }

        return $value;
    }

    /**
     * Helper method to get a file record given a filename, a filepath and a userid, for any of the preset files.
     *
     * @param string $filename The filename for the filerecord that will be returned.
     * @param string $filepath The filepath for the filerecord that will be returned.
     * @param int $userid The userid for the filerecord that will be returned.
     * @return stdClass A filerecord object with the datapreset context, component and filearea and the given information.
     */
    protected static function get_filerecord(string $filename, string $filepath, int $userid): stdClass {
        $filerecord = new stdClass;
        $filerecord->contextid = DATA_PRESET_CONTEXT;
        $filerecord->component = DATA_PRESET_COMPONENT;
        $filerecord->filearea = DATA_PRESET_FILEAREA;
        $filerecord->itemid = 0;
        $filerecord->filepath = $filepath;
        $filerecord->userid = $userid;
        $filerecord->filename = $filename;

        return $filerecord;
    }

    /**
     * Helper method to retrieve a file.
     *
     * @param string $filepath the directory to look in
     * @param string $filename the name of the file we want
     * @return stored_file|null the file or null if the file doesn't exist.
     */
    public static function get_file(string $filepath, string $filename): ?stored_file {
        $file = null;
        $fs = get_file_storage();
        $fileexists = $fs->file_exists(
            DATA_PRESET_CONTEXT,
            DATA_PRESET_COMPONENT,
            DATA_PRESET_FILEAREA,
            0,
            $filepath,
            $filename
        );
        if ($fileexists) {
            $file = $fs->get_file(
                DATA_PRESET_CONTEXT,
                DATA_PRESET_COMPONENT,
                DATA_PRESET_FILEAREA,
                0,
                $filepath,
                $filename
            );
        }

        return $file;
    }

    /**
     * Helper method to retrieve the contents of a file.
     *
     * @param string $filepath the directory to look in
     * @param string $filename the name of the file we want
     * @return string|null the contents of the file or null if the file doesn't exist.
     */
    protected static function get_content_from_file(string $filepath, string $filename): ?string {
        $templatefile = static::get_file($filepath, $filename);
        if ($templatefile) {
            return $templatefile->get_content();
        }

        return null;
    }

    /**
     * Helper method to generate the XML for this preset.
     *
     * @return string The XML for the preset
     */
    protected function generate_preset_xml(): string {
        global $DB;

        if ($this->isplugin) {
            // Only saved presets can generate the preset.xml file.
            return '';
        }

        $presetxmldata = "<preset>\n\n";

        // Add description.
        $presetxmldata .= '<description>' . htmlspecialchars($this->description ?? '', ENT_COMPAT) . "</description>\n\n";

        // Add settings.
        // Raw settings are not preprocessed during saving of presets.
        $rawsettings = [
            'intro',
            'comments',
            'requiredentries',
            'requiredentriestoview',
            'maxentries',
            'rssarticles',
            'approval',
            'manageapproved',
            'defaultsortdir',
        ];
        $presetxmldata .= "<settings>\n";
        $instance = $this->manager->get_instance();
        // First, settings that do not require any conversion.
        foreach ($rawsettings as $setting) {
            $presetxmldata .= "<$setting>" . htmlspecialchars($instance->$setting, ENT_COMPAT) . "</$setting>\n";
        }

        // Now specific settings.
        if ($instance->defaultsort > 0 && $sortfield = data_get_field_from_id($instance->defaultsort, $instance)) {
            $presetxmldata .= '<defaultsort>' . htmlspecialchars($sortfield->field->name, ENT_COMPAT) . "</defaultsort>\n";
        } else {
            $presetxmldata .= "<defaultsort>0</defaultsort>\n";
        }
        $presetxmldata .= "</settings>\n\n";

        // Add fields. Grab all that are non-empty.
        $fields = $DB->get_records('data_fields', ['dataid' => $instance->id]);
        ksort($fields);
        if (!empty($fields)) {
            foreach ($fields as $field) {
                $presetxmldata .= "<field>\n";
                foreach ($field as $key => $value) {
                    if ($value != '' && $key != 'id' && $key != 'dataid') {
                        $presetxmldata .= "<$key>" . htmlspecialchars($value, ENT_COMPAT) . "</$key>\n";
                    }
                }
                $presetxmldata .= "</field>\n\n";
            }
        }
        $presetxmldata .= '</preset>';

        // Check this content is a valid XML.
        $preset = new SimpleXMLElement($presetxmldata);

        return $preset->asXML();
    }

    /**
     * Checks to see if the user has permission to manage the preset.
     *
     * @return bool  Returns true if the user can manage this preset, false otherwise.
     */
    public function can_manage(): bool {
        global $USER;

        if ($this->isplugin) {
            // Plugin presets can't be removed or edited.
            return false;
        }

        $context = $this->manager->get_context();
        if (has_capability('mod/data:manageuserpresets', $context)) {
            return true;
        } else {
            if ($this->get_userid() == $USER->id) {
                return true;
            }
        }
        return false;
    }

    /**
     * Deletes all files related to a saved preset.
     *
     * @return bool True if the preset is a saved preset and the file exists in the file system; false otherwise.
     */
    public function delete(): bool {
        if ($this->isplugin) {
            // Plugin presets can't be removed.
            return false;
        }

        $exists = false;
        $filepath = $this->get_path();

        $dir = self::get_file($filepath, '.');
        if (!empty($dir)) {
            $exists = true;

            $fs = get_file_storage();
            $files = $fs->get_directory_files(
                $dir->get_contextid(),
                $dir->get_component(),
                $dir->get_filearea(),
                $dir->get_itemid(),
                $filepath
            );
            if (!empty($files)) {
                foreach ($files as $file) {
                    $file->delete();
                }
            }
            $dir->delete();
            // Reseting storedfile property because the file has been removed.
            $this->storedfile = null;
        }

        return $exists;
    }
}
