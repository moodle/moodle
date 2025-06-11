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

namespace core\output;

use core\context\system as context_system;
use core\exception\moodle_exception;
use core\exception\coding_exception;
use core\output\actions\component_action;
use moodle_page;
use moodle_url;
use stdClass;
use Mustache_Exception_UnknownTemplateException;

/**
 * Simple base class for Moodle renderers.
 *
 * Tracks the xhtml_container_stack to use, which is passed in in the constructor.
 *
 * Also has methods to facilitate generating HTML output.
 *
 * @copyright 2009 Tim Hunt
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class renderer_base {
    /**
     * @var xhtml_container_stack The xhtml_container_stack to use.
     */
    protected $opencontainers;

    /**
     * @var moodle_page The Moodle page the renderer has been created to assist with.
     */
    protected $page;

    /**
     * @var string The requested rendering target.
     */
    protected $target;

    /**
     * @var \Mustache_Engine The mustache template compiler
     */
    private $mustache;

    /**
     * @var array $templatecache The mustache template cache.
     */
    protected $templatecache = [];

    /**
     * Return an instance of the mustache class.
     *
     * @since 2.9
     * @return \Mustache_Engine
     */
    protected function get_mustache() {
        global $CFG;

        if ($this->mustache === null) {
            require_once("{$CFG->libdir}/filelib.php");

            $themename = $this->page->theme->name;
            $themerev = theme_get_revision();

            // Create new localcache directory.
            $cachedir = make_localcache_directory("mustache/$themerev/$themename");

            // Remove old localcache directories.
            $mustachecachedirs = glob("{$CFG->localcachedir}/mustache/*", GLOB_ONLYDIR);
            foreach ($mustachecachedirs as $localcachedir) {
                $cachedrev = [];
                preg_match("/\/mustache\/([0-9]+)$/", $localcachedir, $cachedrev);
                $cachedrev = isset($cachedrev[1]) ? intval($cachedrev[1]) : 0;
                if ($cachedrev > 0 && $cachedrev < $themerev) {
                    fulldelete($localcachedir);
                }
            }

            $loader = new mustache_filesystem_loader();
            $stringhelper = new mustache_string_helper();
            $cleanstringhelper = new mustache_clean_string_helper();
            $quotehelper = new mustache_quote_helper();
            $jshelper = new mustache_javascript_helper($this->page);
            $pixhelper = new mustache_pix_helper($this);
            $shortentexthelper = new mustache_shorten_text_helper();
            $userdatehelper = new mustache_user_date_helper();

            // We only expose the variables that are exposed to JS templates.
            $safeconfig = $this->page->requires->get_config_for_javascript($this->page, $this);

            $helpers = ['config' => $safeconfig,
                             'str' => [$stringhelper, 'str'],
                             'cleanstr' => [$cleanstringhelper, 'cleanstr'],
                             'quote' => [$quotehelper, 'quote'],
                             'js' => [$jshelper, 'help'],
                             'pix' => [$pixhelper, 'pix'],
                             'shortentext' => [$shortentexthelper, 'shorten'],
                             'userdate' => [$userdatehelper, 'transform'],
                         ];

            $this->mustache = new mustache_engine([
                'cache' => $cachedir,
                'escape' => 's',
                'loader' => $loader,
                'helpers' => $helpers,
                'pragmas' => [\Mustache_Engine::PRAGMA_BLOCKS],
                // Don't allow the JavaScript helper to be executed from within another
                // helper. If it's allowed it can be used by users to inject malicious
                // JS into the page.
                'disallowednestedhelpers' => ['js'],
                // Disable lambda rendering - content in helpers is already rendered, no need to render it again.
                'disable_lambda_rendering' => true,
            ]);
        }

        return $this->mustache;
    }


    /**
     * Constructor
     *
     * The constructor takes two arguments. The first is the page that the renderer
     * has been created to assist with, and the second is the target.
     * The target is an additional identifier that can be used to load different
     * renderers for different options.
     *
     * @param moodle_page $page the page we are doing output for.
     * @param string $target one of rendering target constants
     */
    public function __construct(moodle_page $page, $target) {
        $this->opencontainers = $page->opencontainers;
        $this->page = $page;
        $this->target = $target;
    }

    /**
     * Renders a template by name with the given context.
     *
     * The provided data needs to be array/stdClass made up of only simple types.
     * Simple types are array,stdClass,bool,int,float,string
     *
     * @since 2.9
     * @param string $templatename The template to render
     * @param array|stdClass $context Context containing data for the template.
     * @return string|boolean
     */
    public function render_from_template($templatename, $context) {
        $mustache = $this->get_mustache();

        if ($mustache->hasHelper('uniqid')) {
            // Grab a copy of the existing helper to be restored later.
            $uniqidhelper = $mustache->getHelper('uniqid');
        } else {
            // Helper doesn't exist.
            $uniqidhelper = null;
        }

        // Provide 1 random value that will not change within a template
        // but will be different from template to template. This is useful for
        // e.g. aria attributes that only work with id attributes and must be
        // unique in a page.
        $mustache->addHelper('uniqid', new mustache_uniqid_helper());
        if (isset($this->templatecache[$templatename])) {
            $template = $this->templatecache[$templatename];
        } else {
            try {
                $template = $mustache->loadTemplate($templatename);
                $this->templatecache[$templatename] = $template;
            } catch (Mustache_Exception_UnknownTemplateException $e) {
                throw new moodle_exception('Unknown template: ' . $templatename);
            }
        }

        $renderedtemplate = trim($template->render($context));

        // If we had an existing uniqid helper then we need to restore it to allow
        // handle nested calls of render_from_template.
        if ($uniqidhelper) {
            $mustache->addHelper('uniqid', $uniqidhelper);
        }

        return $renderedtemplate;
    }


    /**
     * Returns rendered widget.
     *
     * The provided widget needs to be an object that extends the renderable
     * interface.
     * If will then be rendered by a method based upon the classname for the widget.
     * For instance a widget of class `crazywidget` will be rendered by a protected
     * render_crazywidget method of this renderer.
     * If no render_crazywidget method exists and crazywidget implements templatable,
     * look for the 'crazywidget' template in the same component and render that.
     *
     * @param renderable $widget instance with renderable interface
     * @return string
     */
    public function render(renderable $widget) {
        $classparts = explode('\\', get_class($widget));
        // Strip namespaces.
        $classpartname = array_pop($classparts);
        // Remove _renderable suffixes.
        $classname = preg_replace('/_renderable$/', '', $classpartname);

        $rendermethods = [];

        // If the renderable is located within a namespace, and that namespace is within the `output` L2 API,
        // include the namespace as a possible renderer method name.
        if (array_search('output', $classparts) === 1 && count($classparts) > 2) {
            $concatenators = array_slice($classparts, 2);
            $concatenators[] = $classname;
            $rendermethods[] = "render_" . implode('__', $concatenators);
        }

        // Fall back to the last part of the class name.
        $rendermethods[] = "render_{$classname}";

        foreach ($rendermethods as $rendermethod) {
            if (method_exists($this, $rendermethod)) {
                // Call the render_[widget_name] function.
                // Note: This has a higher priority than the named_templatable to allow the theme to override the template.
                return $this->$rendermethod($widget);
            }
        }

        if ($widget instanceof named_templatable) {
            // This is a named templatable.
            // Fetch the template name from the get_template_name function instead.
            // Note: This has higher priority than the guessed template name.
            return $this->render_from_template(
                $widget->get_template_name($this),
                $widget->export_for_template($this)
            );
        }

        if ($widget instanceof templatable) {
            // Guess the templat ename based on the class name.
            // Note: There's no benefit to moving this aboved the named_templatable and this approach is more costly.
            $component = array_shift($classparts);
            if (!$component) {
                $component = 'core';
            }
            $template = $component . '/' . $classname;
            $context = $widget->export_for_template($this);
            return $this->render_from_template($template, $context);
        }

        $rendermethod = reset($rendermethods);
        throw new coding_exception("Can not render widget, renderer method ('{$rendermethod}') not found.");
    }

    /**
     * Adds a JS action for the element with the provided id.
     *
     * This method adds a JS event for the provided component action to the page
     * and then returns the id that the event has been attached to.
     * If no id has been provided then a new ID is generated by {@see html_writer::random_id()}
     *
     * @param component_action $action
     * @param string $id
     * @return string id of element, either original submitted or random new if not supplied
     */
    public function add_action_handler(component_action $action, $id = null) {
        if (!$id) {
            $id = html_writer::random_id($action->event);
        }
        $this->page->requires->event_handler("#$id", $action->event, $action->jsfunction, $action->jsfunctionargs);
        return $id;
    }

    /**
     * Returns true is output has already started, and false if not.
     *
     * @return boolean true if the header has been printed.
     */
    public function has_started() {
        return $this->page->state >= moodle_page::STATE_IN_BODY;
    }

    /**
     * Given an array or space-separated list of classes, prepares and returns the HTML class attribute value
     *
     * @param mixed $classes Space-separated string or array of classes
     * @return string HTML class attribute value
     */
    public static function prepare_classes($classes) {
        if (is_array($classes)) {
            return implode(' ', array_unique($classes));
        }
        return $classes;
    }

    /**
     * Return the direct URL for an image from the pix folder.
     *
     * Use this function sparingly and never for icons. For icons use pix_icon or the pix helper in a mustache template.
     *
     * @deprecated since Moodle 3.3
     * @param string $imagename the name of the icon.
     * @param string $component specification of one plugin like in get_string()
     * @return moodle_url
     */
    public function pix_url($imagename, $component = 'moodle') {
        debugging('pix_url is deprecated. Use image_url for images and pix_icon for icons.', DEBUG_DEVELOPER);
        return $this->page->theme->image_url($imagename, $component);
    }

    /**
     * Return the moodle_url for an image.
     *
     * The exact image location and extension is determined
     * automatically by searching for gif|png|jpg|jpeg, please
     * note there can not be diferent images with the different
     * extension. The imagename is for historical reasons
     * a relative path name, it may be changed later for core
     * images. It is recommended to not use subdirectories
     * in plugin and theme pix directories.
     *
     * There are three types of images:
     * 1/ theme images  - stored in theme/mytheme/pix/,
     *                    use component 'theme'
     * 2/ core images   - stored in /pix/,
     *                    overridden via theme/mytheme/pix_core/
     * 3/ plugin images - stored in mod/mymodule/pix,
     *                    overridden via theme/mytheme/pix_plugins/mod/mymodule/,
     *                    example: image_url('comment', 'mod_glossary')
     *
     * @param string $imagename the pathname of the image
     * @param string $component full plugin name (aka component) or 'theme'
     * @return moodle_url
     */
    public function image_url($imagename, $component = 'moodle') {
        return $this->page->theme->image_url($imagename, $component);
    }

    /**
     * Return the site's logo URL, if any.
     *
     * @param int $maxwidth The maximum width, or null when the maximum width does not matter.
     * @param int $maxheight The maximum height, or null when the maximum height does not matter.
     * @return moodle_url|false
     */
    public function get_logo_url($maxwidth = null, $maxheight = 200) {
        global $CFG;
        $logo = get_config('core_admin', 'logo');
        if (empty($logo)) {
            return false;
        }

        // 200px high is the default image size which should be displayed at 100px in the page to account for retina displays.
        // It's not worth the overhead of detecting and serving 2 different images based on the device.

        // Hide the requested size in the file path.
        $filepath = ((int) $maxwidth . 'x' . (int) $maxheight) . '/';

        // Use $CFG->themerev to prevent browser caching when the file changes.
        return moodle_url::make_pluginfile_url(
            context_system::instance()->id,
            'core_admin',
            'logo',
            $filepath,
            theme_get_revision(),
            $logo
        );
    }

    /**
     * Return the site's compact logo URL, if any.
     *
     * @param int $maxwidth The maximum width, or null when the maximum width does not matter.
     * @param int $maxheight The maximum height, or null when the maximum height does not matter.
     * @return moodle_url|false
     */
    public function get_compact_logo_url($maxwidth = 300, $maxheight = 300) {
        global $CFG;
        $logo = get_config('core_admin', 'logocompact');
        if (empty($logo)) {
            return false;
        }

        // Hide the requested size in the file path.
        $filepath = ((int) $maxwidth . 'x' . (int) $maxheight) . '/';

        // Use $CFG->themerev to prevent browser caching when the file changes.
        return moodle_url::make_pluginfile_url(
            context_system::instance()->id,
            'core_admin',
            'logocompact',
            $filepath,
            theme_get_revision(),
            $logo
        );
    }

    /**
     * Whether we should display the logo in the navbar.
     *
     * We will when there are no main logos, and we have compact logo.
     *
     * @return bool
     */
    public function should_display_navbar_logo() {
        $logo = $this->get_compact_logo_url();
        return !empty($logo);
    }

    /**
     * @deprecated since Moodle 4.0
     */
    #[\core\attribute\deprecated(null, reason: 'It is no longer used', since: '4.0', final: true)]
    public function should_display_main_logo() {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);
    }

    /**
     * Returns the moodle page object.
     *
     * @return moodle_page
     */
    public function get_page(): moodle_page {
        return $this->page;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(renderer_base::class, \renderer_base::class);
