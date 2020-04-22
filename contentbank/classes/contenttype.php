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

use coding_exception;
use moodle_url;

/**
 * Content type manager class
 *
 * @package    core_contentbank
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class contenttype {

    /** Plugin implements uploading feature */
    const CAN_UPLOAD = 'upload';

    /** @var context This content's context. **/
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
     * @param stdClass $record An optional content record compatible object (default null)
     * @return content  Object with content bank information.
     */
    public function create_content(\stdClass $record = null): ?content {
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
        $entry->id = $DB->insert_record('contentbank_content', $entry);
        if ($entry->id) {
            $classname = '\\'.$entry->contenttype.'\\content';
            return new $classname($entry);
        }
        return null;
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
        return $DB->delete_records('contentbank_content', ['id' => $content->get_id()]);
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
     * @param stdClass $record  The content to be displayed.
     * @return string           URL where to visualize the given content.
     */
    public function get_view_url(\stdClass $record): string {
        return new moodle_url('/contentbank/view.php', ['id' => $record->id]);
    }

    /**
     * Returns the HTML content to add to view.php visualizer.
     *
     * @param stdClass $record  The content to be displayed.
     * @return string           HTML code to include in view.php.
     */
    public function get_view_content(\stdClass $record): string {
        // Main contenttype class can visualize the content, but plugins could overwrite visualization.
        return '';
    }

    /**
     * Returns the HTML code to render the icon for content bank contents.
     *
     * @param string $contentname   The contentname to add as alt value to the icon.
     * @return string               HTML code to render the icon
     */
    public function get_icon(string $contentname): string {
        global $OUTPUT;
        return $OUTPUT->pix_icon('f/unknown-64', $contentname, 'moodle', ['class' => 'iconsize-big']);
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
}
