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
 * Content type manager class
 *
 * @package    core_contentbank
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_contentbank;

use core\event\contentbank_content_created;
use core\event\contentbank_content_deleted;
use core\event\contentbank_content_viewed;
use stored_file;
use Exception;
use moodle_url;

/**
 * Content type manager class
 *
 * @package    core_contentbank
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class contenttype {

    /** @var string Constant representing whether the plugin implements uploading feature */
    const CAN_UPLOAD = 'upload';

    /** @var string Constant representing whether the plugin implements edition feature */
    const CAN_EDIT = 'edit';

    /**
     * @var string Constant representing whether the plugin implements download feature
     * @since  Moodle 3.10
     */
    const CAN_DOWNLOAD = 'download';

    /** @var \context This contenttype's context. **/
    protected $context = null;

    /**
     * Content type constructor
     *
     * @param \context $context Optional context to check (default null)
     */
    public function __construct(\context $context = null) {
        if (empty($context)) {
            $context = \context_system::instance();
        }
        $this->context = $context;
    }

    /**
     * Fills content_bank table with appropiate information.
     *
     * @throws dml_exception A DML specific exception is thrown for any creation error.
     * @param \stdClass $record An optional content record compatible object (default null)
     * @return content  Object with content bank information.
     */
    public function create_content(\stdClass $record = null): content {
        global $USER, $DB;

        $entry = new \stdClass();
        $entry->contenttype = $this->get_contenttype_name();
        $entry->contextid = $this->context->id;
        $entry->name = $record->name ?? '';
        $entry->usercreated = $record->usercreated ?? $USER->id;
        $entry->timecreated = time();
        $entry->usermodified = $entry->usercreated;
        $entry->timemodified = $entry->timecreated;
        $entry->configdata = $record->configdata ?? '';
        $entry->instanceid = $record->instanceid ?? 0;
        $entry->id = $DB->insert_record('contentbank_content', $entry);
        $classname = '\\'.$entry->contenttype.'\\content';
        $content = new $classname($entry);
        // Trigger an event for creating the content.
        $event = contentbank_content_created::create_from_record($content->get_content());
        $event->trigger();
        return $content;
    }

    /**
     * Create a new content from an uploaded file.
     *
     * @throws file_exception If file operations fail
     * @throws dml_exception if the content creation fails
     * @param stored_file $file the uploaded file
     * @param \stdClass|null $record an optional content record
     * @return content  Object with content bank information.
     */
    public function upload_content(stored_file $file, \stdClass $record = null): content {
        if (empty($record)) {
            $record = new \stdClass();
            $record->name = $file->get_filename();
        }
        $content = $this->create_content($record);
        try {
            $content->import_file($file);
        } catch (Exception $e) {
            $this->delete_content($content);
            throw $e;
        }

        return $content;
    }

    /**
     * Delete this content from the content_bank.
     * This method can be overwritten by the plugins if they need to delete specific information.
     *
     * @param  content $content The content to delete.
     * @return boolean true if the content has been deleted; false otherwise.
     */
    public function delete_content(content $content): bool {
        global $DB;

        // Delete the file if it exists.
        if ($file = $content->get_file()) {
            $file->delete();
        }

        // Delete the contentbank DB entry.
        $result = $DB->delete_records('contentbank_content', ['id' => $content->get_id()]);
        if ($result) {
            // Trigger an event for deleting this content.
            $record = $content->get_content();
            $event = contentbank_content_deleted::create([
                'objectid' => $content->get_id(),
                'relateduserid' => $record->usercreated,
                'context' => \context::instance_by_id($record->contextid),
                'other' => [
                    'contenttype' => $content->get_content_type(),
                    'name' => $content->get_name()
                ]
            ]);
            $event->add_record_snapshot('contentbank_content', $record);
            $event->trigger();
        }
        return $result;
    }

    /**
     * Rename this content from the content_bank.
     * This method can be overwritten by the plugins if they need to change some other specific information.
     *
     * @param  content $content The content to rename.
     * @param  string $name  The name of the content.
     * @return boolean true if the content has been renamed; false otherwise.
     */
    public function rename_content(content $content, string $name): bool {
        return $content->set_name($name);
    }

    /**
     * Move content to another context.
     * This method can be overwritten by the plugins if they need to change some other specific information.
     *
     * @param  content $content The content to rename.
     * @param  \context $context  The new context.
     * @return boolean true if the content has been renamed; false otherwise.
     */
    public function move_content(content $content, \context $context): bool {
        return $content->set_contextid($context->id);
    }

    /**
     * Returns the contenttype name of this content.
     *
     * @return string   Content type of the current instance
     */
    public function get_contenttype_name(): string {
        $classname = get_class($this);
        $contenttype = explode('\\', $classname);
        return array_shift($contenttype);
    }

    /**
     * Returns the plugin name of the current instance.
     *
     * @return string   Plugin name of the current instance
     */
    public function get_plugin_name(): string {
        $contenttype = $this->get_contenttype_name();
        $plugin = explode('_', $contenttype);
        return array_pop($plugin);
    }

    /**
     * Returns the URL where the content will be visualized.
     *
     * @param  content $content The content to be displayed.
     * @return string           URL where to visualize the given content.
     */
    public function get_view_url(content $content): string {
        return new moodle_url('/contentbank/view.php', ['id' => $content->get_id()]);
    }

    /**
     * Returns the HTML content to add to view.php visualizer.
     *
     * @param  content $content The content to be displayed.
     * @return string           HTML code to include in view.php.
     */
    public function get_view_content(content $content): string {
        // Trigger an event for viewing this content.
        $event = contentbank_content_viewed::create_from_record($content->get_content());
        $event->trigger();

        return '';
    }

    /**
     * Returns the URL to download the content.
     *
     * @since  Moodle 3.10
     * @param  content $content The content to be downloaded.
     * @return string           URL with the content to download.
     */
    public function get_download_url(content $content): string {
        $downloadurl = '';
        $file = $content->get_file();
        if (!empty($file)) {
            $url = \moodle_url::make_pluginfile_url(
                $file->get_contextid(),
                $file->get_component(),
                $file->get_filearea(),
                $file->get_itemid(),
                $file->get_filepath(),
                $file->get_filename()
            );
            $downloadurl = $url->out(false);
        }

        return $downloadurl;
    }

    /**
     * Returns the HTML code to render the icon for content bank contents.
     *
     * @param  content $content The content to be displayed.
     * @return string               HTML code to render the icon
     */
    public function get_icon(content $content): string {
        global $OUTPUT;
        return $OUTPUT->image_url('f/unknown-64', 'moodle')->out(false);
    }

    /**
     * Returns user has access capability for the main content bank and the content itself (base on is_access_allowed from plugin).
     *
     * @return bool     True if content could be accessed. False otherwise.
     */
    final public function can_access(): bool {
        $classname = 'contenttype/'.$this->get_plugin_name();
        $capability = $classname.":access";
        $hascapabilities = has_capability('moodle/contentbank:access', $this->context)
            && has_capability($capability, $this->context);
        return $hascapabilities && $this->is_access_allowed();
    }

    /**
     * Returns user has access capability for the content itself.
     *
     * @return bool     True if content could be accessed. False otherwise.
     */
    protected function is_access_allowed(): bool {
        // Plugins can overwrite this function to add any check they need.
        return true;
    }

    /**
     * Returns the user has permission to upload new content.
     *
     * @return bool     True if content could be uploaded. False otherwise.
     */
    final public function can_upload(): bool {
        if (!$this->is_feature_supported(self::CAN_UPLOAD)) {
            return false;
        }
        if (!$this->can_access()) {
            return false;
        }

        $classname = 'contenttype/'.$this->get_plugin_name();
        $uploadcap = $classname.':upload';
        $hascapabilities = has_capability('moodle/contentbank:upload', $this->context)
            && has_capability($uploadcap, $this->context);
        return $hascapabilities && $this->is_upload_allowed();
    }

    /**
     * Returns plugin allows uploading.
     *
     * @return bool     True if plugin allows uploading. False otherwise.
     */
    protected function is_upload_allowed(): bool {
        // Plugins can overwrite this function to add any check they need.
        return true;
    }

    /**
     * Check if the user can delete this content.
     *
     * @param  content $content The content to be deleted.
     * @return bool True if content could be uploaded. False otherwise.
     */
    final public function can_delete(content $content): bool {
        global $USER;

        if ($this->context->id != $content->get_content()->contextid) {
            // The content has to have exactly the same context as this contenttype.
            return false;
        }

        $hascapability = has_capability('moodle/contentbank:deleteanycontent', $this->context);
        if ($content->get_content()->usercreated == $USER->id) {
            // This content has been created by the current user; check if she can delete her content.
            $hascapability = $hascapability || has_capability('moodle/contentbank:deleteowncontent', $this->context);
        }

        return $hascapability && $this->is_delete_allowed($content);
    }

    /**
     * Returns if content allows deleting.
     *
     * @param  content $content The content to be deleted.
     * @return bool True if content allows uploading. False otherwise.
     */
    protected function is_delete_allowed(content $content): bool {
        // Plugins can overwrite this function to add any check they need.
        return true;
    }

    /**
     * Check if the user can managed this content.
     *
     * @param  content $content The content to be managed.
     * @return bool     True if content could be managed. False otherwise.
     */
    public final function can_manage(content $content): bool {
        global $USER;

        if ($this->context->id != $content->get_content()->contextid) {
            // The content has to have exactly the same context as this contenttype.
            return false;
        }

        // Check main contentbank management permission.
        $hascapability = has_capability('moodle/contentbank:manageanycontent', $this->context);
        if ($content->get_content()->usercreated == $USER->id) {
            // This content has been created by the current user; check if they can manage their content.
            $hascapability = $hascapability || has_capability('moodle/contentbank:manageowncontent', $this->context);
        }

        return $hascapability && $this->is_manage_allowed($content);
    }

    /**
     * Returns if content allows managing.
     *
     * @param  content $content The content to be managed.
     * @return bool True if content allows uploading. False otherwise.
     */
    protected function is_manage_allowed(content $content): bool {
        // Plugins can overwrite this function to add any check they need.
        return true;
    }

    /**
     * Returns whether or not the user has permission to use the editor.
     * This function will be called with the content to be edited as parameter,
     * or null when is checking permission to create a new content using the editor.
     *
     * @param  content $content The content to be edited or null when creating a new content.
     * @return bool     True if the user can edit content. False otherwise.
     */
    final public function can_edit(?content $content = null): bool {
        if (!$this->is_feature_supported(self::CAN_EDIT)) {
            return false;
        }

        if (!$this->can_access()) {
            return false;
        }

        if (!is_null($content) && !$this->can_manage($content)) {
            return false;
        }

        $classname = 'contenttype/'.$this->get_plugin_name();

        $editioncap = $classname.':useeditor';
        $hascapabilities = has_all_capabilities(['moodle/contentbank:useeditor', $editioncap], $this->context);
        return $hascapabilities && $this->is_edit_allowed($content);
    }

    /**
     * Returns plugin allows edition.
     *
     * @param  content $content The content to be edited.
     * @return bool     True if plugin allows edition. False otherwise.
     */
    protected function is_edit_allowed(?content $content): bool {
        // Plugins can overwrite this function to add any check they need.
        return true;
    }

    /**
     * Returns whether or not the user has permission to download the content.
     *
     * @since  Moodle 3.10
     * @param  content $content The content to be downloaded.
     * @return bool    True if the user can download the content. False otherwise.
     */
    final public function can_download(content $content): bool {
        if (!$this->is_feature_supported(self::CAN_DOWNLOAD)) {
            return false;
        }

        if (!$this->can_access()) {
            return false;
        }

        $hascapability = has_capability('moodle/contentbank:downloadcontent', $this->context);
        return $hascapability && $this->is_download_allowed($content);
    }

    /**
     * Returns plugin allows downloading.
     *
     * @since  Moodle 3.10
     * @param  content $content The content to be downloaed.
     * @return bool    True if plugin allows downloading. False otherwise.
     */
    protected function is_download_allowed(content $content): bool {
        // Plugins can overwrite this function to add any check they need.
        return true;
    }

    /**
     * Returns the plugin supports the feature.
     *
     * @param string $feature Feature code e.g CAN_UPLOAD
     * @return bool     True if content could be uploaded. False otherwise.
     */
    final public function is_feature_supported(string $feature): bool {
        return in_array($feature, $this->get_implemented_features());
    }

    /**
     * Return an array of implemented features by the plugins.
     *
     * @return array
     */
    abstract protected function get_implemented_features(): array;

    /**
     * Return an array of extensions the plugins could manage.
     *
     * @return array
     */
    abstract public function get_manageable_extensions(): array;

    /**
     * Returns the list of different types of the given content type.
     *
     * A content type can have one or more options for creating content. This method will report all of them or only the content
     * type itself if it has no other options.
     *
     * @return array An object for each type:
     *     - string typename: descriptive name of the type.
     *     - string typeeditorparams: params required by this content type editor.
     *     - url typeicon: this type icon.
     */
    abstract public function get_contenttype_types(): array;
}
