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
 * H5P editor class.
 *
 * @package    core_h5p
 * @copyright  2020 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_h5p;

use core_h5p\local\library\autoloader;
use core_h5p\output\h5peditor as editor_renderer;
use H5PCore;
use H5peditor;
use stdClass;
use coding_exception;
use MoodleQuickForm;

defined('MOODLE_INTERNAL') || die();

/**
 * H5P editor class, for editing local H5P content.
 *
 * @package    core_h5p
 * @copyright  2020 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class editor {

    /**
     * @var core The H5PCore object.
     */
    private $core;

    /**
     * @var H5peditor $h5peditor The H5P Editor object.
     */
    private $h5peditor;

    /**
     * @var int Id of the H5P content from the h5p table.
     */
    private $id = null;

    /**
     * @var array Existing H5P content instance before edition.
     */
    private $oldcontent = null;

    /**
     * @var stored_file File of ane existing H5P content before edition.
     */
    private $oldfile = null;

    /**
     * @var array File area to save the file of a new H5P content.
     */
    private $filearea = null;

    /**
     * @var string H5P Library name
     */
    private $library = null;

    /**
     * Inits the H5P editor.
     */
    public function __construct() {
        autoloader::register();

        $factory = new factory();
        $this->h5peditor = $factory->get_editor();
        $this->core = $factory->get_core();
    }

    /**
     * Loads an existing content for edition.
     *
     * If the H5P content or its file can't be retrieved, it is not possible to edit the content.
     *
     * @param int $id Id of the H5P content from the h5p table.
     *
     * @return void
     */
    public function set_content(int $id): void {
        $this->id = $id;

        // Load the present content.
        $this->oldcontent = $this->core->loadContent($id);
        if ($this->oldcontent === null) {
            print_error('invalidelementid');
        }

        // Identify the content type library.
        $this->library = H5PCore::libraryToString($this->oldcontent['library']);

        // Get current file and its file area.
        $pathnamehash = $this->oldcontent['pathnamehash'];
        $fs = get_file_storage();
        $oldfile = $fs->get_file_by_hash($pathnamehash);
        if (!$oldfile) {
            print_error('invalidelementid');
        }
        $this->set_filearea(
            $oldfile->get_contextid(),
            $oldfile->get_component(),
            $oldfile->get_filearea(),
            $oldfile->get_itemid(),
            $oldfile->get_filepath(),
            $oldfile->get_filename(),
            $oldfile->get_userid()
        );
        $this->oldfile = $oldfile;
    }

    /**
     * Sets the content type library and the file area to create a new H5P content.
     *
     * Note: this method must be used to create new content, to edit an existing
     * H5P content use only set_content with the ID from the H5P table.
     *
     * @param string $library Library of the H5P content type to create.
     * @param int $contextid Context where the file of the H5P content will be stored.
     * @param string $component Component where the file of the H5P content will be stored.
     * @param string $filearea File area where the file of the H5P content will be stored.
     * @param int $itemid Item id file of the H5P content.
     * @param string $filepath File path where the file of the H5P content will be stored.
     * @param null|string $filename H5P content file name.
     * @param null|int $userid H5P content file owner userid (default will use $USER->id).
     *
     * @return void
     */
    public function set_library(string $library, int $contextid, string $component, string $filearea,
            ?int $itemid = 0, string $filepath = '/', ?string $filename = null, ?int $userid = null): void {

        $this->library = $library;
        $this->set_filearea($contextid, $component, $filearea, $itemid, $filepath, $filename, $userid);
    }

    /**
     * Sets the Moodle file area where the file of a new H5P content will be stored.
     *
     * @param int $contextid Context where the file of the H5P content will be stored.
     * @param string $component Component where the file of the H5P content will be stored.
     * @param string $filearea File area where the file of the H5P content will be stored.
     * @param int $itemid Item id file of the H5P content.
     * @param string $filepath File path where the file of the H5P content will be stored.
     * @param null|string $filename H5P content file name.
     * @param null|int $userid H5P content file owner userid (default will use $USER->id).
     *
     * @return void
     */
    private function set_filearea(int $contextid, string $component, string $filearea,
            int $itemid, string $filepath = '/', ?string $filename = null, ?int $userid = null): void {
        global $USER;

        $this->filearea = [
            'contextid' => $contextid,
            'component' => $component,
            'filearea' => $filearea,
            'itemid' => $itemid,
            'filepath' => $filepath,
            'filename' => $filename,
            'userid' => $userid ?? $USER->id,
        ];
    }

    /**
     * Adds an H5P editor to a form.
     *
     * @param MoodleQuickForm $mform Moodle Quick Form
     *
     * @return void
     */
    public function add_editor_to_form(MoodleQuickForm $mform): void {
        global $PAGE;

        $this->add_assets_to_page();

        $data = $this->data_preprocessing();

        // Hidden fields used bu H5P editor.
        $mform->addElement('hidden', 'h5plibrary', $data->h5plibrary);
        $mform->setType('h5plibrary', PARAM_RAW);

        $mform->addElement('hidden', 'h5pparams', $data->h5pparams);
        $mform->setType('h5pparams', PARAM_RAW);

        $mform->addElement('hidden', 'h5paction');
        $mform->setType('h5paction', PARAM_ALPHANUMEXT);

        // Render H5P editor.
        $ui = new editor_renderer($data);
        $editorhtml = $PAGE->get_renderer('core_h5p')->render($ui);
        $mform->addElement('html', $editorhtml);
    }

    /**
     * Creates or updates an H5P content.
     *
     * @param stdClass $content Object containing all the necessary data.
     *
     * @return int Content id
     */
    public function save_content(stdClass $content): int {

        if (empty($content->h5pparams)) {
            throw new coding_exception('Missing H5P params.');
        }

        if (!isset($content->h5plibrary)) {
            throw new coding_exception('Missing H5P library.');
        }

        if ($content->h5plibrary != $this->library) {
            throw new coding_exception("Wrong H5P library.");
        }

        $content->params = $content->h5pparams;

        if (!empty($this->oldcontent)) {
            $content->id = $this->oldcontent['id'];
            // Get old parameters for comparison.
            $oldparams = json_decode($this->oldcontent['params']) ?? null;
            // Keep the existing display options.
            $content->disable = $this->oldcontent['disable'];
            $oldlib = $this->oldcontent['library'];
        } else {
            $oldparams = null;
            $oldlib = null;
        }

        // Prepare library data to be save.
        $content->library = H5PCore::libraryFromString($content->h5plibrary);
        $content->library['libraryId'] = $this->core->h5pF->getLibraryId($content->library['machineName'],
            $content->library['majorVersion'],
            $content->library['minorVersion']);

        // Prepare current parameters.
        $params = json_decode($content->params);

        $modified = false;
        if (empty($params->metadata)) {
            $params->metadata = new stdClass();
            $modified = true;
        }
        if (empty($params->metadata->title)) {
            // Use a default string if not available.
            $params->metadata->title = 'Untitled';
            $modified = true;
        }
        if (!isset($content->title)) {
            $content->title = $params->metadata->title;
        }
        if ($modified) {
            $content->params = json_encode($params);
        }

        // Save content.
        $content->id = $this->core->saveContent((array)$content);

        // Move any uploaded images or files. Determine content dependencies.
        $this->h5peditor->processParameters($content, $content->library, $params->params, $oldlib, $oldparams);

        $this->update_h5p_file($content);

        return $content->id;
    }

    /**
     * Creates or updates the H5P file and the related database data.
     *
     * @param stdClass $content Object containing all the necessary data.
     *
     * @return void
     */
    private function update_h5p_file(stdClass $content): void {
        global $USER;

        // Keep title before filtering params.
        $title = $content->title;
        $contentarray = $this->core->loadContent($content->id);
        $contentarray['title'] = $title;

        // Generates filtered params and export file.
        $this->core->filterParameters($contentarray);

        $slug = isset($contentarray['slug']) ? $contentarray['slug'] . '-' : '';
        $filename = $contentarray['id'] ?? $contentarray['title'];
        $filename = $slug . $filename . '.h5p';
        $file = $this->core->fs->get_export_file($filename);
        $fs = get_file_storage();

        if ($file) {
            $fields['contenthash'] = $file->get_contenthash();

            // Delete old file if any.
            if (!empty($this->oldfile)) {
                $this->oldfile->delete();
            }
            // Create new file.
            if (empty($this->filearea['filename'])) {
                $this->filearea['filename'] = $contentarray['slug'] . '.h5p';
            }
            $newfile = $fs->create_file_from_storedfile($this->filearea, $file);
            if (empty($this->oldcontent)) {
                $pathnamehash = $newfile->get_pathnamehash();
            } else {
                $pathnamehash = $this->oldcontent['pathnamehash'];
            }

            // Update hash fields in the h5p table.
            $fields['pathnamehash'] = $pathnamehash;
            $this->core->h5pF->updateContentFields($contentarray['id'], $fields);
        }
    }

    /**
     * Add required assets for displaying the editor.
     *
     * @return void
     * @throws coding_exception If page header is already printed.
     */
    private function add_assets_to_page(): void {
        global $PAGE, $CFG;

        if ($PAGE->headerprinted) {
            throw new coding_exception('H5P assets cannot be added when header is already printed.');
        }

        $context = \context_system::instance();

        $settings = helper::get_core_assets();

        // Use jQuery and styles from core.
        $assets = [
            'css' => $settings['core']['styles'],
            'js' => $settings['core']['scripts']
        ];

        // Use relative URL to support both http and https.
        $url = autoloader::get_h5p_editor_library_url()->out();
        $url = '/' . preg_replace('/^[^:]+:\/\/[^\/]+\//', '', $url);

        // Make sure files are reloaded for each plugin update.
        $cachebuster = helper::get_cache_buster();

        // Add editor styles.
        foreach (H5peditor::$styles as $style) {
            $assets['css'][] = $url . $style . $cachebuster;
        }

        // Add editor JavaScript.
        foreach (H5peditor::$scripts as $script) {
            // We do not want the creator of the iframe inside the iframe.
            if ($script !== 'scripts/h5peditor-editor.js') {
                $assets['js'][] = $url . $script . $cachebuster;
            }
        }

        // Add JavaScript with library framework integration (editor part).
        $PAGE->requires->js(autoloader::get_h5p_editor_library_url('scripts/h5peditor-editor.js' . $cachebuster), true);
        $PAGE->requires->js(autoloader::get_h5p_editor_library_url('scripts/h5peditor-init.js' . $cachebuster), true);

        // Load editor translations.
        $language = framework::get_language();
        $editorstrings = $this->get_editor_translations($language);
        $PAGE->requires->data_for_js('H5PEditor.language.core', $editorstrings, false);

        // Add JavaScript settings.
        $root = $CFG->wwwroot;
        $filespathbase = "{$root}/pluginfile.php/{$context->id}/core_h5p/";

        $factory = new factory();
        $contentvalidator = $factory->get_content_validator();

        $editorajaxtoken = core::createToken(editor_ajax::EDITOR_AJAX_TOKEN);
        $sesskey = sesskey();
        $settings['editor'] = [
            'filesPath' => $filespathbase . 'editor',
            'fileIcon' => [
                'path' => $url . 'images/binary-file.png',
                'width' => 50,
                'height' => 50,
            ],
            'ajaxPath' => $CFG->wwwroot . "/h5p/ajax.php?sesskey={$sesskey}&token={$editorajaxtoken}&action=",
            'libraryUrl' => $url,
            'copyrightSemantics' => $contentvalidator->getCopyrightSemantics(),
            'metadataSemantics' => $contentvalidator->getMetadataSemantics(),
            'assets' => $assets,
            'apiVersion' => H5PCore::$coreApi,
            'language' => $language,
        ];

        if (!empty($this->id)) {
            $settings['editor']['nodeVersionId'] = $this->id;

            // Override content URL.
            $contenturl = "{$root}/pluginfile.php/{$context->id}/core_h5p/content/{$this->id}";
            $settings['contents']['cid-' . $this->id]['contentUrl'] = $contenturl;
        }

        $PAGE->requires->data_for_js('H5PIntegration', $settings, true);
    }

    /**
     * Get editor translations for the defined language.
     * Check if the editor strings have been translated in Moodle.
     * If the strings exist, they will override the existing ones in the JS file.
     *
     * @param string $language The language for the translations to be returned.
     * @return array The editor string translations.
     */
    private function get_editor_translations(string $language): array {
        global $CFG;

        // Add translations.
        $languagescript = "language/{$language}.js";

        if (!file_exists("{$CFG->dirroot}" . autoloader::get_h5p_editor_library_base($languagescript))) {
            $languagescript = 'language/en.js';
        }

        // Check if the editor strings have been translated in Moodle.
        // If the strings exist, they will override the existing ones in the JS file.

        // Get existing strings from current JS language file.
        $langcontent = file_get_contents("{$CFG->dirroot}" . autoloader::get_h5p_editor_library_base($languagescript));

        // Get only the content between { } (for instance, ; at the end of the file has to be removed).
        $langcontent = substr($langcontent, 0, strpos($langcontent, '}', -0) + 1);
        $langcontent = substr($langcontent, strpos($langcontent, '{'));

        // Parse the JS language content and get a PHP array.
        $editorstrings = helper::parse_js_array($langcontent);
        foreach ($editorstrings as $key => $value) {
            $stringkey = 'editor:'.strtolower(trim($key));
            $value = autoloader::get_h5p_string($stringkey, $language);
            if (!empty($value)) {
                $editorstrings[$key] = $value;
            }
        }

        return $editorstrings;
    }

    /**
     * Preprocess the data sent through the form to the H5P JS Editor Library.
     *
     * @return stdClass
     */
    private function data_preprocessing(): stdClass {

        $defaultvalues = [
            'id' => $this->id,
            'h5plibrary' => $this->library,
        ];

        // In case both contentid and library have values, content(edition) takes precedence over library(creation).
        if (empty($this->oldcontent)) {
            $maincontentdata = ['params' => (object)[]];
        } else {
            $params = $this->core->filterParameters($this->oldcontent);
            $maincontentdata = ['params' => json_decode($params)];
            if (isset($this->oldcontent['metadata'])) {
                $maincontentdata['metadata'] = $this->oldcontent['metadata'];
            }
        }

        $defaultvalues['h5pparams'] = json_encode($maincontentdata, true);

        return (object) $defaultvalues;
    }
}
