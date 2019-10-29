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
 * H5P player class.
 *
 * @package    core_h5p
 * @copyright  2019 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_h5p;

defined('MOODLE_INTERNAL') || die();

/**
 * H5P player class, for displaying any local H5P content.
 *
 * @package    core_h5p
 * @copyright  2019 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class player {

    /**
     * @var string The local H5P URL containing the .h5p file to display.
     */
    private $url;

    /**
     * @var core The H5PCore object.
     */
    private $core;

    /**
     * @var int H5P DB id.
     */
    private $h5pid;

    /**
     * @var array JavaScript requirements for this H5P.
     */
    private $jsrequires = [];

    /**
     * @var array CSS requirements for this H5P.
     */
    private $cssrequires = [];

    /**
     * @var array H5P content to display.
     */
    private $content;

    /**
     * @var string Type of embed object, div or iframe.
     */
    private $embedtype;

    /**
     * @var context The context object where the .h5p belongs.
     */
    private $context;

    /**
     * @var context The \core_h5p\factory object.
     */
    private $factory;

    /**
     * Inits the H5P player for rendering the content.
     *
     * @param string $url Local URL of the H5P file to display.
     * @param stdClass $config Configuration for H5P buttons.
     */
    public function __construct(string $url, \stdClass $config) {
        if (empty($url)) {
            throw new \moodle_exception('h5pinvalidurl', 'core_h5p');
        }
        $this->url = new \moodle_url($url);

        $this->factory = new \core_h5p\factory();

        // Create \core_h5p\core instance.
        $this->core = $this->factory->get_core();

        // Get the H5P identifier linked to this URL.
        if ($this->h5pid = $this->get_h5p_id($url, $config)) {
            // Load the content of the H5P content associated to this $url.
            $this->content = $this->core->loadContent($this->h5pid);

            // Get the embedtype to use for displaying the H5P content.
            $this->embedtype = core::determineEmbedType($this->content['embedType'], $this->content['library']['embedTypes']);
        }
    }

    /**
     * Get the error messages stored in our H5P framework.
     *
     * @return stdClass with framework error messages.
     */
    public function get_messages() : \stdClass {
        $messages = new \stdClass();
        $messages->error = $this->core->h5pF->getMessages('error');

        if (empty($messages->error)) {
            $messages->error = false;
        }
        return $messages;
    }

    /**
     * Create the H5PIntegration variable that will be included in the page. This variable is used as the
     * main H5P config variable.
     */
    public function add_assets_to_page() {
        global $PAGE;

        $cid = $this->get_cid();
        $systemcontext = \context_system::instance();

        $disable = array_key_exists('disable', $this->content) ? $this->content['disable'] : core::DISABLE_NONE;
        $displayoptions = $this->core->getDisplayOptionsForView($disable, $this->h5pid);

        $contenturl = \moodle_url::make_pluginfile_url($systemcontext->id, \core_h5p\file_storage::COMPONENT,
            \core_h5p\file_storage::CONTENT_FILEAREA, $this->h5pid, null, null);

        $contentsettings = [
            'library'         => core::libraryToString($this->content['library']),
            'fullScreen'      => $this->content['library']['fullscreen'],
            'exportUrl'       => $this->get_export_settings($displayoptions[ core::DISPLAY_OPTION_DOWNLOAD ]),
            'embedCode'       => $this->get_embed_code($this->url->out(),
                $displayoptions[ core::DISPLAY_OPTION_EMBED ]),
            'resizeCode'      => $this->get_resize_code(),
            'title'           => $this->content['slug'],
            'displayOptions'  => $displayoptions,
            'url'             => self::get_embed_url($this->url->out())->out(),
            'contentUrl'      => $contenturl->out(),
            'metadata'        => $this->content['metadata'],
            'contentUserData' => [0 => ['state' => '{}']]
        ];
        // Get the core H5P assets, needed by the H5P classes to render the H5P content.
        $settings = $this->get_assets();
        $settings['contents'][$cid] = array_merge($settings['contents'][$cid], $contentsettings);

        foreach ($this->jsrequires as $script) {
            $PAGE->requires->js($script, true);
        }

        foreach ($this->cssrequires as $css) {
            $PAGE->requires->css($css);
        }

        // Print JavaScript settings to page.
        $PAGE->requires->data_for_js('H5PIntegration', $settings, true);
    }

    /**
     * Outputs H5P wrapper HTML.
     *
     * @return string The HTML code to display this H5P content.
     */
    public function output() : string {
        global $OUTPUT;

        $template = new \stdClass();
        $template->h5pid = $this->h5pid;
        if ($this->embedtype === 'div') {
            return $OUTPUT->render_from_template('core_h5p/h5pdiv', $template);
        } else {
            return $OUTPUT->render_from_template('core_h5p/h5piframe', $template);
        }
    }

    /**
     * Get the title of the H5P content to display.
     *
     * @return string the title
     */
    public function get_title() : string {
        return $this->content['title'];
    }

    /**
     * Get the context where the .h5p file belongs.
     *
     * @return context The context.
     */
    public function get_context() : \context {
        return $this->context;
    }

    /**
     * Get the H5P DB instance id for a H5P pluginfile URL. The H5P file will be saved if it doesn't exist previously or
     * if its content has changed. Besides, the displayoptions in the $config will be also updated when they have changed and
     * the user has the right permissions.
     *
     * @param string $url H5P pluginfile URL.
     * @param stdClass $config Configuration for H5P buttons.
     *
     * @return int|false H5P DB identifier.
     */
    private function get_h5p_id(string $url, \stdClass $config) {
        global $DB;

        $fs = get_file_storage();

        // Deconstruct the URL and get the pathname associated.
        $pathnamehash = $this->get_pluginfile_hash($url);
        if (!$pathnamehash) {
            $this->core->h5pF->setErrorMessage(get_string('h5pfilenotfound', 'core_h5p'));
            return false;
        }

        // Get the file.
        $file = $fs->get_file_by_hash($pathnamehash);
        if (!$file) {
            $this->core->h5pF->setErrorMessage(get_string('h5pfilenotfound', 'core_h5p'));
            return false;
        }

        $h5p = $DB->get_record('h5p', ['pathnamehash' => $pathnamehash]);
        $contenthash = $file->get_contenthash();
        if ($h5p && $h5p->contenthash != $contenthash) {
            // The content exists and it is different from the one deployed previously. The existing one should be removed before
            // deploying the new version.
            $this->delete_h5p($h5p);
            $h5p = false;
        }

        if ($h5p) {
            // The H5P content has been deployed previously.
            $displayoptions = $this->get_display_options($config);
            // Check if the user can set the displayoptions.
            if ($displayoptions != $h5p->displayoptions && has_capability('moodle/h5p:setdisplayoptions', $this->context)) {
                // If the displayoptions has changed and the user has permission to modify it, update this information in the DB.
                $this->core->h5pF->updateContentFields($h5p->id, ['displayoptions' => $displayoptions]);
            }
            return $h5p->id;
        } else {
            // The H5P content hasn't been deployed previously.

            // Check if the user uploading the H5P content is "trustable". If the file hasn't been uploaded by a user with this
            // capability, the content won't be deployed and an error message will be displayed.
            if (!has_capability('moodle/h5p:deploy', $this->context, $file->get_userid())) {
                $this->core->h5pF->setErrorMessage(get_string('nopermissiontodeploy', 'core_h5p'));
                return false;
            }

            // Validate and store the H5P content before displaying it.
            return $this->save_h5p($file, $config);
        }
    }

    /**
     * Get the pathnamehash from an H5P internal URL.
     *
     * @param  string $url H5P pluginfile URL poiting to an H5P file.
     *
     * @return string|false pathnamehash for the file in the internal URL.
     */
    private function get_pluginfile_hash(string $url) {
        global $USER;

        // Decode the URL before start processing it.
        $url = new \moodle_url(urldecode($url));

        // Remove params from the URL (such as the 'forcedownload=1'), to avoid errors.
        $url->remove_params(array_keys($url->params()));
        $path = $url->out_as_local_url();

        $parts = explode('/', $path);
        $filename = array_pop($parts);
        // First is an empty row and then the pluginfile.php part. Both can be ignored.
        array_shift($parts);
        array_shift($parts);

        // Get the contextid, component and filearea.
        $contextid = array_shift($parts);
        $component = array_shift($parts);
        $filearea = array_shift($parts);

        // Ignore draft files, because they are considered temporary files, so shouldn't be displayed.
        if ($filearea == 'draft') {
            return false;
        }

        // Get the context.
        try {
            list($this->context, $course, $cm) = get_context_info_array($contextid);
        } catch (\moodle_exception $e) {
            throw new \moodle_exception('invalidcontextid', 'core_h5p');
        }

        // For CONTEXT_USER, such as the private files, raise an exception if the owner of the file is not the current user.
        if ($this->context->contextlevel == CONTEXT_USER && $USER->id !== $this->context->instanceid) {
            throw new \moodle_exception('h5pprivatefile', 'core_h5p');
        }

        // For CONTEXT_MODULE, check if the user is enrolled in the course and has permissions view this .h5p file.
        if ($this->context->contextlevel == CONTEXT_MODULE) {
            // Require login to the course first (without login to the module).
            require_course_login($course, true, null, false, true);

            // Now check if module is available OR it is restricted but the intro is shown on the course page.
            $cminfo = \cm_info::create($cm);
            if (!$cminfo->uservisible) {
                if (!$cm->showdescription || !$cminfo->is_visible_on_course_page()) {
                    // Module intro is not visible on the course page and module is not available, show access error.
                    require_course_login($course, true, $cminfo, false, true);
                }
            }
        }

        // Some components, such as mod_page or mod_resource, add the revision to the URL to prevent caching problems.
        // So the URL contains this revision number as itemid but a 0 is always stored in the files table.
        // In order to get the proper hash, a callback should be done (looking for those exceptions).
        $pathdata = component_callback($component, 'get_path_from_pluginfile', [$filearea, $parts], null);
        if (null === $pathdata) {
            // Look for the components and fileareas which have empty itemid defined in xxx_pluginfile.
            $hasnullitemid = false;
            $hasnullitemid = $hasnullitemid || ($component === 'user' && ($filearea === 'private' || $filearea === 'profile'));
            $hasnullitemid = $hasnullitemid || ($component === 'mod' && $filearea === 'intro');
            $hasnullitemid = $hasnullitemid || ($component === 'course' &&
                    ($filearea === 'summary' || $filearea === 'overviewfiles'));
            $hasnullitemid = $hasnullitemid || ($component === 'coursecat' && $filearea === 'description');
            $hasnullitemid = $hasnullitemid || ($component === 'backup' &&
                    ($filearea === 'course' || $filearea === 'activity' || $filearea === 'automated'));
            if ($hasnullitemid) {
                $itemid = 0;
            } else {
                $itemid = array_shift($parts);
            }

            if (empty($parts)) {
                $filepath = '/';
            } else {
                $filepath = '/' . implode('/', $parts) . '/';
            }
        } else {
            // The itemid and filepath have been returned by the component callback.
            [
                'itemid' => $itemid,
                'filepath' => $filepath,
            ] = $pathdata;
        }

        $fs = get_file_storage();
        return $fs->get_pathname_hash($contextid, $component, $filearea, $itemid, $filepath, $filename);
    }

    /**
     * Store an H5P file
     *
     * @param stored_file $file Moodle file instance
     * @param stdClass $config Button options config.
     *
     * @return int|false The H5P identifier or false if it's not a valid H5P package.
     */
    private function save_h5p($file, \stdClass $config) : int {
        // This may take a long time.
        \core_php_time_limit::raise();

        $path = $this->core->fs->getTmpPath();
        $this->core->h5pF->getUploadedH5pFolderPath($path);
        // Add manually the extension to the file to avoid the validation fails.
        $path .= '.h5p';
        $this->core->h5pF->getUploadedH5pPath($path);

        // Copy the .h5p file to the temporary folder.
        $file->copy_content_to($path);

        // Check if the h5p file is valid before saving it.
        $h5pvalidator = $this->factory->get_validator();
        if ($h5pvalidator->isValidPackage(false, false)) {
            $h5pstorage = $this->factory->get_storage();

            $options = ['disable' => $this->get_display_options($config)];
            $content = [
                'pathnamehash' => $file->get_pathnamehash(),
                'contenthash' => $file->get_contenthash(),
            ];

            $h5pstorage->savePackage($content, null, false, $options);
            return $h5pstorage->contentId;
        }

        return false;
    }

    /**
     * Get the representation of display options as int.
     * @param stdClass $config Button options config.
     *
     * @return int The representation of display options as int.
     */
    private function get_display_options(\stdClass $config) : int {
        $export = isset($config->export) ? $config->export : 0;
        $embed = isset($config->embed) ? $config->embed : 0;
        $copyright = isset($config->copyright) ? $config->copyright : 0;
        $frame = ($export || $embed || $copyright);
        if (!$frame) {
            $frame = isset($config->frame) ? $config->frame : 0;
        }

        $disableoptions = [
            core::DISPLAY_OPTION_FRAME     => $frame,
            core::DISPLAY_OPTION_DOWNLOAD  => $export,
            core::DISPLAY_OPTION_EMBED     => $embed,
            core::DISPLAY_OPTION_COPYRIGHT => $copyright,
        ];

        return $this->core->getStorableDisplayOptions($disableoptions, 0);
    }

    /**
     * Delete an H5P package.
     *
     * @param stdClass $content The H5P package to delete.
     */
    private function delete_h5p(\stdClass $content) {
        $h5pstorage = $this->factory->get_storage();
        // Add an empty slug to the content if it's not defined, because the H5P library requires this field exists.
        // It's not used when deleting a package, so the real slug value is not required at this point.
        $content->slug = $content->slug ?? '';
        $h5pstorage->deletePackage( (array) $content);
    }

    /**
     * Export path for settings
     *
     * @param bool $downloadenabled Whether the option to export the H5P content is enabled.
     *
     * @return string The URL of the exported file.
     */
    private function get_export_settings(bool $downloadenabled) : string {

        if ( ! $downloadenabled) {
            return '';
        }

        $systemcontext = \context_system::instance();
        $slug = $this->content['slug'] ? $this->content['slug'] . '-' : '';
        $url  = \moodle_url::make_pluginfile_url(
            $systemcontext->id,
            \core_h5p\file_storage::COMPONENT,
            \core_h5p\file_storage::EXPORT_FILEAREA,
            '',
            '',
            "{$slug}{$this->content['id']}.h5p"
        );

        return $url->out();
    }

    /**
     * Get a query string with the theme revision number to include at the end
     * of URLs. This is used to force the browser to reload the asset when the
     * theme caches are cleared.
     *
     * @return string
     */
    private function get_cache_buster() : string {
        global $CFG;
        return '?ver=' . $CFG->themerev;
    }

    /**
     * Get the identifier for the H5P content, to be used in the arrays as index.
     *
     * @return string The identifier.
     */
    private function get_cid() : string {
        return 'cid-' . $this->h5pid;
    }

    /**
     * Get the core H5P assets, including all core H5P JavaScript and CSS.
     *
     * @return Array core H5P assets.
     */
    private function get_assets() : array {
        global $CFG;

        // Get core settings.
        $settings = $this->get_core_settings();
        $settings['core'] = [
          'styles' => [],
          'scripts' => []
        ];
        $settings['loadedJs'] = [];
        $settings['loadedCss'] = [];

        // Make sure files are reloaded for each plugin update.
        $cachebuster = $this->get_cache_buster();

        // Use relative URL to support both http and https.
        $liburl = $CFG->wwwroot . '/lib/h5p/';
        $relpath = '/' . preg_replace('/^[^:]+:\/\/[^\/]+\//', '', $liburl);

        // Add core stylesheets.
        foreach (core::$styles as $style) {
            $settings['core']['styles'][] = $relpath . $style . $cachebuster;
            $this->cssrequires[] = new \moodle_url($liburl . $style . $cachebuster);
        }
        // Add core JavaScript.
        foreach (core::get_scripts() as $script) {
            $settings['core']['scripts'][] = $script->out(false);
            $this->jsrequires[] = $script;
        }

        $cid = $this->get_cid();
        // The filterParameters function should be called before getting the dependencyfiles because it rebuild content
        // dependency cache and export file.
        $settings['contents'][$cid]['jsonContent'] = $this->core->filterParameters($this->content);

        $files = $this->get_dependency_files();
        if ($this->embedtype === 'div') {
            $systemcontext = \context_system::instance();
            $h5ppath = "/pluginfile.php/{$systemcontext->id}/core_h5p";

            // Schedule JavaScripts for loading through Moodle.
            foreach ($files['scripts'] as $script) {
                $url = $script->path . $script->version;

                // Add URL prefix if not external.
                $isexternal = strpos($script->path, '://');
                if ($isexternal === false) {
                    $url = $h5ppath . $url;
                }
                $settings['loadedJs'][] = $url;
                $this->jsrequires[] = new \moodle_url($isexternal ? $url : $CFG->wwwroot . $url);
            }

            // Schedule stylesheets for loading through Moodle.
            foreach ($files['styles'] as $style) {
                $url = $style->path . $style->version;

                // Add URL prefix if not external.
                $isexternal = strpos($style->path, '://');
                if ($isexternal === false) {
                    $url = $h5ppath . $url;
                }
                $settings['loadedCss'][] = $url;
                $this->cssrequires[] = new \moodle_url($isexternal ? $url : $CFG->wwwroot . $url);
            }

        } else {
            // JavaScripts and stylesheets will be loaded through h5p.js.
            $settings['contents'][$cid]['scripts'] = $this->core->getAssetsUrls($files['scripts']);
            $settings['contents'][$cid]['styles']  = $this->core->getAssetsUrls($files['styles']);
        }
        return $settings;
    }

    /**
     * Get the settings needed by the H5P library.
     *
     * @return array The settings.
     */
    private function get_core_settings() : array {
        global $CFG;

        $basepath = $CFG->wwwroot . '/';
        $systemcontext = \context_system::instance();

        // Generate AJAX paths.
        $ajaxpaths = [];
        $ajaxpaths['xAPIResult'] = '';
        $ajaxpaths['contentUserData'] = '';

        $settings = array(
            'baseUrl' => $basepath,
            'url' => "{$basepath}pluginfile.php/{$systemcontext->instanceid}/core_h5p",
            'urlLibraries' => "{$basepath}pluginfile.php/{$systemcontext->id}/core_h5p/libraries",
            'postUserStatistics' => false,
            'ajax' => $ajaxpaths,
            'saveFreq' => false,
            'siteUrl' => $CFG->wwwroot,
            'l10n' => array('H5P' => $this->core->getLocalization()),
            'user' => [],
            'hubIsEnabled' => false,
            'reportingIsEnabled' => false,
            'crossorigin' => null,
            'libraryConfig' => $this->core->h5pF->getLibraryConfig(),
            'pluginCacheBuster' => $this->get_cache_buster(),
            'libraryUrl' => $basepath . 'lib/h5p/js',
            'moodleLibraryPaths' => $this->core->get_dependency_roots($this->h5pid),
        );

        return $settings;
    }

    /**
     * Finds library dependencies of view
     *
     * @return array Files that the view has dependencies to
     */
    private function get_dependency_files() : array {
        $preloadeddeps = $this->core->loadContentDependencies($this->h5pid, 'preloaded');
        $files = $this->core->getDependenciesFiles($preloadeddeps);

        return $files;
    }

    /**
     * Resizing script for settings
     *
     * @return string The HTML code with the resize script.
     */
    private function get_resize_code() : string {
        global $OUTPUT;

        $template = new \stdClass();
        $template->resizeurl = new \moodle_url('/lib/h5p/js/h5p-resizer.js');

        return $OUTPUT->render_from_template('core_h5p/h5presize', $template);
    }

    /**
     * Embed code for settings
     *
     * @param string $url The URL of the .h5p file.
     * @param bool $embedenabled Whether the option to embed the H5P content is enabled.
     *
     * @return string The HTML code to reuse this H5P content in a different place.
     */
    private function get_embed_code(string $url, bool $embedenabled) : string {
        global $OUTPUT;

        if ( ! $embedenabled) {
            return '';
        }

        $template = new \stdClass();
        $template->embedurl = self::get_embed_url($url)->out();

        return $OUTPUT->render_from_template('core_h5p/h5pembed', $template);
    }

    /**
     * Get the encoded URL for embeding this H5P content.
     * @param  string $url The URL of the .h5p file.
     *
     * @return \moodle_url The embed URL.
     */
    public static function get_embed_url(string $url) : \moodle_url {
        return new \moodle_url('/h5p/embed.php', ['url' => $url]);
    }
}
