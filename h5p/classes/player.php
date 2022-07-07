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

use core_h5p\local\library\autoloader;
use core_xapi\local\statement\item_activity;

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
     * @var string optional component name to send xAPI statements.
     */
    private $component;

    /**
     * @var string Type of embed object, div or iframe.
     */
    private $embedtype;

    /**
     * @var context The context object where the .h5p belongs.
     */
    private $context;

    /**
     * @var factory The \core_h5p\factory object.
     */
    private $factory;

    /**
     * @var stdClass The error, exception and info messages, raised while preparing and running the player.
     */
    private $messages;

    /**
     * @var bool Set to true in scripts that can not redirect (CLI, RSS feeds, etc.), throws exceptions.
     */
    private $preventredirect;

    /**
     * Inits the H5P player for rendering the content.
     *
     * @param string $url Local URL of the H5P file to display.
     * @param stdClass $config Configuration for H5P buttons.
     * @param bool $preventredirect Set to true in scripts that can not redirect (CLI, RSS feeds, etc.), throws exceptions
     * @param string $component optional moodle component to sent xAPI tracking
     */
    public function __construct(string $url, \stdClass $config, bool $preventredirect = true, string $component = '') {
        if (empty($url)) {
            throw new \moodle_exception('h5pinvalidurl', 'core_h5p');
        }
        $this->url = new \moodle_url($url);
        $this->preventredirect = $preventredirect;

        $this->factory = new \core_h5p\factory();

        $this->messages = new \stdClass();

        $this->component = $component;

        // Create \core_h5p\core instance.
        $this->core = $this->factory->get_core();

        // Get the H5P identifier linked to this URL.
        list($file, $this->h5pid) = api::create_content_from_pluginfile_url(
            $url,
            $config,
            $this->factory,
            $this->messages,
            $this->preventredirect
        );
        if ($file) {
            $this->context = \context::instance_by_id($file->get_contextid());
            if ($this->h5pid) {
                // Load the content of the H5P content associated to this $url.
                $this->content = $this->core->loadContent($this->h5pid);

                // Get the embedtype to use for displaying the H5P content.
                $this->embedtype = core::determineEmbedType($this->content['embedType'], $this->content['library']['embedTypes']);
            }
        }
    }

    /**
     * Get the encoded URL for embeding this H5P content.
     *
     * @param string $url Local URL of the H5P file to display.
     * @param stdClass $config Configuration for H5P buttons.
     * @param bool $preventredirect Set to true in scripts that can not redirect (CLI, RSS feeds, etc.), throws exceptions
     * @param string $component optional moodle component to sent xAPI tracking
     *
     * @return string The embedable code to display a H5P file.
     */
    public static function display(string $url, \stdClass $config, bool $preventredirect = true,
            string $component = ''): string {
        global $OUTPUT;
        $params = [
                'url' => $url,
                'preventredirect' => $preventredirect,
                'component' => $component,
            ];

        $optparams = ['frame', 'export', 'embed', 'copyright'];
        foreach ($optparams as $optparam) {
            if (!empty($config->$optparam)) {
                $params[$optparam] = $config->$optparam;
            }
        }
        $fileurl = new \moodle_url('/h5p/embed.php', $params);

        $template = new \stdClass();
        $template->embedurl = $fileurl->out(false);

        $result = $OUTPUT->render_from_template('core_h5p/h5pembed', $template);
        $result .= self::get_resize_code();
        return $result;
    }

    /**
     * Get the error messages stored in our H5P framework.
     *
     * @return stdClass with framework error messages.
     */
    public function get_messages(): \stdClass {
        return helper::get_messages($this->messages, $this->factory);
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
        $exporturl = $this->get_export_settings($displayoptions[ core::DISPLAY_OPTION_DOWNLOAD ]);
        $xapiobject = item_activity::create_from_id($this->context->id);
        $contentsettings = [
            'library'         => core::libraryToString($this->content['library']),
            'fullScreen'      => $this->content['library']['fullscreen'],
            'exportUrl'       => ($exporturl instanceof \moodle_url) ? $exporturl->out(false) : '',
            'embedCode'       => $this->get_embed_code($this->url->out(),
                $displayoptions[ core::DISPLAY_OPTION_EMBED ]),
            'resizeCode'      => self::get_resize_code(),
            'title'           => $this->content['slug'],
            'displayOptions'  => $displayoptions,
            'url'             => $xapiobject->get_data()->id,
            'contentUrl'      => $contenturl->out(),
            'metadata'        => $this->content['metadata'],
            'contentUserData' => [0 => ['state' => '{}']]
        ];
        // Get the core H5P assets, needed by the H5P classes to render the H5P content.
        $settings = $this->get_assets();
        $settings['contents'][$cid] = array_merge($settings['contents'][$cid], $contentsettings);

        // Print JavaScript settings to page.
        $PAGE->requires->data_for_js('H5PIntegration', $settings, true);
    }

    /**
     * Outputs H5P wrapper HTML.
     *
     * @return string The HTML code to display this H5P content.
     */
    public function output(): string {
        global $OUTPUT, $USER;

        $template = new \stdClass();
        $template->h5pid = $this->h5pid;
        if ($this->embedtype === 'div') {
            $h5phtml = $OUTPUT->render_from_template('core_h5p/h5pdiv', $template);
        } else {
            $h5phtml = $OUTPUT->render_from_template('core_h5p/h5piframe', $template);
        }

        // Trigger capability_assigned event.
        \core_h5p\event\h5p_viewed::create([
            'objectid' => $this->h5pid,
            'userid' => $USER->id,
            'context' => $this->get_context(),
            'other' => [
                'url' => $this->url->out(),
                'time' => time()
            ]
        ])->trigger();

        return $h5phtml;
    }

    /**
     * Get the title of the H5P content to display.
     *
     * @return string the title
     */
    public function get_title(): string {
        return $this->content['title'];
    }

    /**
     * Get the context where the .h5p file belongs.
     *
     * @return context The context.
     */
    public function get_context(): \context {
        return $this->context;
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
     * @return \moodle_url|null The URL of the exported file.
     */
    private function get_export_settings(bool $downloadenabled): ?\moodle_url {

        if (!$downloadenabled) {
            return null;
        }

        $systemcontext = \context_system::instance();
        $slug = $this->content['slug'] ? $this->content['slug'] . '-' : '';
        // We have to build the right URL.
        // Depending the request was made through webservice/pluginfile.php or pluginfile.php.
        if (strpos($this->url, '/webservice/pluginfile.php')) {
            $url  = \moodle_url::make_webservice_pluginfile_url(
                $systemcontext->id,
                \core_h5p\file_storage::COMPONENT,
                \core_h5p\file_storage::EXPORT_FILEAREA,
                '',
                '',
                "{$slug}{$this->content['id']}.h5p"
            );
        } else {
            // If the request is made by tokenpluginfile.php we need to indicates to generate a token for current user.
            $includetoken = false;
            if (strpos($this->url, '/tokenpluginfile.php')) {
                $includetoken = true;
            }
            $url  = \moodle_url::make_pluginfile_url(
                $systemcontext->id,
                \core_h5p\file_storage::COMPONENT,
                \core_h5p\file_storage::EXPORT_FILEAREA,
                '',
                '',
                "{$slug}{$this->content['id']}.h5p",
                false,
                $includetoken
            );
        }

        return $url;
    }

    /**
     * Get the identifier for the H5P content, to be used in the arrays as index.
     *
     * @return string The identifier.
     */
    private function get_cid(): string {
        return 'cid-' . $this->h5pid;
    }

    /**
     * Get the core H5P assets, including all core H5P JavaScript and CSS.
     *
     * @return Array core H5P assets.
     */
    private function get_assets(): array {
        // Get core assets.
        $settings = helper::get_core_assets();
        // Added here because in the helper we don't have the h5p content id.
        $settings['moodleLibraryPaths'] = $this->core->get_dependency_roots($this->h5pid);
        // Add also the Moodle component where the results will be tracked.
        $settings['moodleComponent'] = $this->component;
        if (!empty($settings['moodleComponent'])) {
            $settings['reportingIsEnabled'] = true;
        }

        $cid = $this->get_cid();
        // The filterParameters function should be called before getting the dependencyfiles because it rebuild content
        // dependency cache and export file.
        $settings['contents'][$cid]['jsonContent'] = $this->get_filtered_parameters();

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
     * Get filtered parameters, modifying them by the renderer if the theme implements the h5p_alter_filtered_parameters function.
     *
     * @return string Filtered parameters.
     */
    private function get_filtered_parameters(): string {
        global $PAGE;

        $safeparams = $this->core->filterParameters($this->content);
        $decodedparams = json_decode($safeparams);
        $h5poutput = $PAGE->get_renderer('core_h5p');
        $h5poutput->h5p_alter_filtered_parameters(
            $decodedparams,
            $this->content['library']['name'],
            $this->content['library']['majorVersion'],
            $this->content['library']['minorVersion']
        );
        $safeparams = json_encode($decodedparams);

        return $safeparams;
    }

    /**
     * Finds library dependencies of view
     *
     * @return array Files that the view has dependencies to
     */
    private function get_dependency_files(): array {
        global $PAGE;

        $preloadeddeps = $this->core->loadContentDependencies($this->h5pid, 'preloaded');
        $files = $this->core->getDependenciesFiles($preloadeddeps);

        // Add additional asset files if required.
        $h5poutput = $PAGE->get_renderer('core_h5p');
        $h5poutput->h5p_alter_scripts($files['scripts'], $preloadeddeps, $this->embedtype);
        $h5poutput->h5p_alter_styles($files['styles'], $preloadeddeps, $this->embedtype);

        return $files;
    }

    /**
     * Resizing script for settings
     *
     * @return string The HTML code with the resize script.
     */
    private static function get_resize_code(): string {
        global $OUTPUT;

        $template = new \stdClass();
        $template->resizeurl = autoloader::get_h5p_core_library_url('js/h5p-resizer.js');

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
    private function get_embed_code(string $url, bool $embedenabled): string {
        global $OUTPUT;

        if ( ! $embedenabled) {
            return '';
        }

        $template = new \stdClass();
        $template->embedurl = self::get_embed_url($url, $this->component)->out(false);

        return $OUTPUT->render_from_template('core_h5p/h5pembed', $template);
    }

    /**
     * Get the encoded URL for embeding this H5P content.
     * @param  string $url The URL of the .h5p file.
     * @param string $component optional Moodle component to send xAPI tracking
     *
     * @return \moodle_url The embed URL.
     */
    public static function get_embed_url(string $url, string $component = ''): \moodle_url {
        $params = ['url' => $url];
        if (!empty($component)) {
            // If component is not empty, it will be passed too, in order to allow tracking too.
            $params['component'] = $component;
        }

        return new \moodle_url('/h5p/embed.php', $params);
    }

    /**
     * Return the info export file for Mobile App.
     *
     * @return array
     */
    public function get_export_file(): array {
        // Get the export url.
        $exporturl = $this->get_export_settings(true);
        // Get the filename of the export url.
        $path = $exporturl->out_as_local_url();
        $parts = explode('/', $path);
        $filename = array_pop($parts);
        // Get the required info from the export file to be able to get the export file by third apps.
        $file = helper::get_export_info($filename, $exporturl);

        return $file;
    }
}
