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
 * Content manager class
 *
 * @package    core_contentbank
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_contentbank;

use core_text;
use stored_file;
use stdClass;
use coding_exception;
use context;
use moodle_url;
use core\event\contentbank_content_updated;

/**
 * Content manager class
 *
 * @package    core_contentbank
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class content {
    /**
     * @var int Visibility value. Public content is visible to all users with access to the content bank of the
     * appropriate context.
     */
    public const VISIBILITY_PUBLIC = 1;

    /**
     * @var int Visibility value. Unlisted content is only visible to the author and to users with
     * moodle/contentbank:viewunlistedcontent capability.
     */
    public const VISIBILITY_UNLISTED = 2;

    /** @var stdClass $content The content of the current instance. **/
    protected $content  = null;

    /**
     * Content bank constructor
     *
     * @param stdClass $record A contentbank_content record.
     * @throws coding_exception If content type is not right.
     */
    public function __construct(stdClass $record) {
        // Content type should exist and be linked to plugin classname.
        $classname = $record->contenttype.'\\content';
        if (get_class($this) != $classname) {
            throw new coding_exception(get_string('contenttypenotfound', 'error', $record->contenttype));
        }
        $typeclass = $record->contenttype.'\\contenttype';
        if (!class_exists($typeclass)) {
            throw new coding_exception(get_string('contenttypenotfound', 'error', $record->contenttype));
        }
        // A record with the id must exist in 'contentbank_content' table.
        // To improve performance, we are only checking the id is set, but no querying the database.
        if (!isset($record->id)) {
            throw new coding_exception(get_string('invalidcontentid', 'error'));
        }
        $this->content = $record;
    }

    /**
     * Returns $this->content.
     *
     * @return stdClass  $this->content.
     */
    public function get_content(): stdClass {
        return $this->content;
    }

    /**
     * Returns $this->content->contenttype.
     *
     * @return string  $this->content->contenttype.
     */
    public function get_content_type(): string {
        return $this->content->contenttype;
    }

    /**
     * Return the contenttype instance of this content.
     *
     * @return contenttype The content type instance
     */
    public function get_content_type_instance(): contenttype {
        $context = context::instance_by_id($this->content->contextid);
        $contenttypeclass = "\\{$this->content->contenttype}\\contenttype";
        return new $contenttypeclass($context);
    }

    /**
     * Returns $this->content->timemodified.
     *
     * @return int  $this->content->timemodified.
     */
    public function get_timemodified(): int {
        return $this->content->timemodified;
    }

    /**
     * Updates content_bank table with information in $this->content.
     *
     * @return boolean  True if the content has been succesfully updated. False otherwise.
     * @throws \coding_exception if not loaded.
     */
    public function update_content(): bool {
        global $USER, $DB;

        // A record with the id must exist in 'contentbank_content' table.
        // To improve performance, we are only checking the id is set, but no querying the database.
        if (!isset($this->content->id)) {
            throw new coding_exception(get_string('invalidcontentid', 'error'));
        }
        $this->content->usermodified = $USER->id;
        $this->content->timemodified = time();
        $result = $DB->update_record('contentbank_content', $this->content);
        if ($result) {
            // Trigger an event for updating this content.
            $event = contentbank_content_updated::create_from_record($this->content);
            $event->trigger();
        }
        return $result;
    }

    /**
     * Set a new name to the content.
     *
     * @param string $name  The name of the content.
     * @return bool  True if the content has been succesfully updated. False otherwise.
     * @throws \coding_exception if not loaded.
     */
    public function set_name(string $name): bool {
        $name = trim($name);
        if ($name === '') {
            return false;
        }

        // Clean name.
        $name = clean_param($name, PARAM_TEXT);
        if (core_text::strlen($name) > 255) {
            $name = core_text::substr($name, 0, 255);
        }

        $oldname = $this->content->name;
        $this->content->name = $name;
        $updated = $this->update_content();
        if (!$updated) {
            $this->content->name = $oldname;
        }
        return $updated;
    }

    /**
     * Returns the name of the content.
     *
     * @return string   The name of the content.
     */
    public function get_name(): string {
        return $this->content->name;
    }

    /**
     * Set a new contextid to the content.
     *
     * @param int $contextid  The new contextid of the content.
     * @return bool  True if the content has been succesfully updated. False otherwise.
     */
    public function set_contextid(int $contextid): bool {
        if ($this->content->contextid == $contextid) {
            return true;
        }

        $oldcontextid = $this->content->contextid;
        $this->content->contextid = $contextid;
        $updated = $this->update_content();
        if ($updated) {
            // Move files to new context
            $fs = get_file_storage();
            $fs->move_area_files_to_new_context($oldcontextid, $contextid, 'contentbank', 'public', $this->content->id);
        } else {
            $this->content->contextid = $oldcontextid;
        }
        return $updated;
    }

    /**
     * Returns the contextid of the content.
     *
     * @return int   The id of the content context.
     */
    public function get_contextid(): string {
        return $this->content->contextid;
    }

    /**
     * Returns the content ID.
     *
     * @return int   The content ID.
     */
    public function get_id(): int {
        return $this->content->id;
    }

    /**
     * Change the content instanceid value.
     *
     * @param int $instanceid    New instanceid for this content
     * @return boolean           True if the instanceid has been succesfully updated. False otherwise.
     */
    public function set_instanceid(int $instanceid): bool {
        $this->content->instanceid = $instanceid;
        return $this->update_content();
    }

    /**
     * Returns the $instanceid of this content.
     *
     * @return int   contentbank instanceid
     */
    public function get_instanceid(): int {
        return $this->content->instanceid;
    }

    /**
     * Change the content config values.
     *
     * @param string $configdata    New config information for this content
     * @return boolean              True if the configdata has been succesfully updated. False otherwise.
     */
    public function set_configdata(string $configdata): bool {
        $this->content->configdata = $configdata;
        return $this->update_content();
    }

    /**
     * Return the content config values.
     *
     * @return mixed   Config information for this content (json decoded)
     */
    public function get_configdata() {
        return $this->content->configdata;
    }

    /**
     * Sets a new content visibility and saves it to database.
     *
     * @param int $visibility Must be self::PUBLIC or self::UNLISTED
     * @return bool
     * @throws coding_exception
     */
    public function set_visibility(int $visibility): bool {
        if (!in_array($visibility, [self::VISIBILITY_PUBLIC, self::VISIBILITY_UNLISTED])) {
            return false;
        }
        $this->content->visibility = $visibility;
        return $this->update_content();
    }

    /**
     * Return true if the content may be shown to other users in the content bank.
     *
     * @return boolean
     */
    public function get_visibility(): int {
        return $this->content->visibility;
    }

    /**
     * Import a file as a valid content.
     *
     * By default, all content has a public file area to interact with the content bank
     * repository. This method should be overridden by contentypes which does not simply
     * upload to the public file area.
     *
     * If any, the method will return the final stored_file. This way it can be invoked
     * as parent::import_file in case any plugin want to store the file in the public area
     * and also parse it.
     *
     * @param stored_file $file File to store in the content file area.
     * @return stored_file|null the stored content file or null if the file is discarted.
     */
    public function import_file(stored_file $file): ?stored_file {
        $originalfile = $this->get_file();
        if ($originalfile) {
            $originalfile->replace_file_with($file);
            return $originalfile;
        } else {
            $fs = get_file_storage();
            $filerecord = [
                'contextid' => $this->get_contextid(),
                'component' => 'contentbank',
                'filearea' => 'public',
                'itemid' => $this->get_id(),
                'filepath' => '/',
                'filename' => $file->get_filename(),
                'timecreated' => time(),
            ];
            return $fs->create_file_from_storedfile($filerecord, $file);
        }
    }

    /**
     * Returns the $file related to this content.
     *
     * @return stored_file  File stored in content bank area related to the given itemid.
     * @throws \coding_exception if not loaded.
     */
    public function get_file(): ?stored_file {
        $itemid = $this->get_id();
        $fs = get_file_storage();
        $files = $fs->get_area_files(
            $this->content->contextid,
            'contentbank',
            'public',
            $itemid,
            'itemid, filepath, filename',
            false
        );
        if (!empty($files)) {
            $file = reset($files);
            return $file;
        }
        return null;
    }

    /**
     * Returns the places where the file associated to this content is used or an empty array if the content has no file.
     *
     * @return array of stored_file where current file content is used or empty array if it hasn't any file.
     * @since 3.11
     */
    public function get_uses(): ?array {
        $references = [];

        $file = $this->get_file();
        if ($file != null) {
            $fs = get_file_storage();
            $references = $fs->get_references_by_storedfile($file);
        }

        return $references;
    }

    /**
     * Returns the file url related to this content.
     *
     * @return string       URL of the file stored in content bank area related to the given itemid.
     * @throws \coding_exception if not loaded.
     */
    public function get_file_url(): string {
        if (!$file = $this->get_file()) {
            return '';
        }
        $fileurl = moodle_url::make_pluginfile_url(
            $this->content->contextid,
            'contentbank',
            'public',
            $file->get_itemid(),
            $file->get_filepath(),
            $file->get_filename()
        );

        return $fileurl;
    }

    /**
     * Returns user has access permission for the content itself (based on what plugin needs).
     *
     * @return bool     True if content could be accessed. False otherwise.
     */
    public function is_view_allowed(): bool {
        // Plugins can overwrite this method in case they want to check something related to content properties.
        global $USER;
        $context = \context::instance_by_id($this->get_contextid());

        return $USER->id == $this->content->usercreated ||
            $this->get_visibility() == self::VISIBILITY_PUBLIC ||
            has_capability('moodle/contentbank:viewunlistedcontent', $context);
    }
}
