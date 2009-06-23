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
 * Functions for generating the HTML that Moodle should output.
 *
 * Please see http://docs.moodle.org/en/Developement:How_Moodle_outputs_HTML
 * for an overview.
 *
 * @package   moodlecore
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (5)
 */


/**
 * A renderer factory is just responsible for creating an appropriate renderer
 * for any given part of Moodle.
 *
 * Which renderer factory to use is chose by the current theme, and an instance
 * if created automatically when the theme is set up.
 *
 * A renderer factory must also have a constructor that takes a theme object and
 * a moodle_page object. (See {@link renderer_factory_base::__construct} for an example.)
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
interface renderer_factory {
    /**
     * Return the renderer for a particular part of Moodle.
     *
     * The renderer interfaces are defined by classes called moodle_..._renderer
     * where ... is the name of the module, which, will be defined in this file
     * for core parts of Moodle, and in a file called renderer.php for plugins.
     *
     * There is no separate interface definintion for renderers. Instead we
     * take advantage of PHP being a dynamic languages. The renderer returned
     * does not need to be a subclass of the moodle_..._renderer base class, it
     * just needs to impmenent the same interface. This is sometimes called
     * 'Duck typing'. For a tricky example, see {@link template_renderer} below.
     * renderer ob
     *
     * @param $module the name of part of moodle. E.g. 'core', 'quiz', 'qtype_multichoice'.
     * @return object an object implementing the requested renderer interface.
     */
    public function get_renderer($module);
}


/**
 * This is a base class to help you implement the renderer_factory interface.
 *
 * It keeps a cache of renderers that have been constructed, so you only need
 * to construct each one once in you subclass.
 *
 * It also has a method to get the name of, and include the renderer.php with
 * the definition of, the standard renderer class for a given module.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
abstract class renderer_factory_base implements renderer_factory {
    /** The theme we are rendering for. */
    protected $theme;

    /** The page we are doing output for. */
    protected $page;

    /** Used to cache renderers as they are created. */
    protected $renderers = array();

    /**
     * Constructor.
     * @param object $theme the theme we are rendering for.
     * @param moodle_page $page the page we are doing output for.
     */
    public function __construct($theme, $page) {
        $this->theme = $theme;
        $this->page = $page;
    }

    /* Implement the interface method. */
    public function get_renderer($module) {
        // Cache the renderers by module name, and delegate the actual
        // construction to the create_renderer method.
        if (!array_key_exists($module, $this->renderers)) {
            $this->renderers[$module] = $this->create_renderer($module);
        }

        return $this->renderers[$module];
    }

    /**
     * Subclasses should override this method to actually create an instance of
     * the appropriate renderer class, based on the module name. That is,
     * this method should implement the same contract as
     * {@link renderer_factory::get_renderer}.
     *
     * @param $module the name of part of moodle. E.g. 'core', 'quiz', 'qtype_multichoice'.
     * @return object an object implementing the requested renderer interface.
     */
    abstract public function create_renderer($module);

    /**
     * For a given module name, return the name of the standard renderer class
     * that defines the renderer interface for that module.
     *
     * Also, if it exists, include the renderer.php file for that module, so
     * the class definition of the default renderer has been loaded.
     *
     * @param string $module the name of part of moodle. E.g. 'core', 'quiz', 'qtype_multichoice'.
     * @return string the name of the standard renderer class for that module.
     */
    protected function standard_renderer_class_for_module($module) {
        $pluginrenderer = get_plugin_dir($module) . '/renderer.php';
        if (file_exists($pluginrenderer)) {
            include_once($pluginrenderer);
        }
        $class = 'moodle_' . $module . '_renderer';
        if (!class_exists($class)) {
            throw new coding_exception('Request for an unknown renderer class ' . $class);
        }
        return $class;
    }
}


/**
 * This is the default renderer factory for Moodle. It simply returns an instance
 * of the appropriate standard renderer class.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class standard_renderer_factory extends renderer_factory_base {
    /**
     * Constructor.
     * @param object $theme the theme we are rendering for.
     * @param moodle_page $page the page we are doing output for.
     */
    public function __construct($theme, $page) {
        parent::__construct($theme, $page);
    }

    /* Implement the subclass method. */
    public function create_renderer($module) {
        if ($module == 'core') {
            return new moodle_core_renderer($this->page->opencontainers);
        } else {
            $class = $this->standard_renderer_class_for_module($module);
            return new $class($this->page->opencontainers, $this->get_renderer('core'));
        }
    }
}


/**
 * This is a slight variatoin on the standard_renderer_factory that uses
 * custom_corners_core_renderer instead of moodle_core_renderer.
 *
 * This generates the slightly different HTML that the custom_corners theme expects.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class custom_corners_renderer_factory extends standard_renderer_factory {
    /**
     * Constructor.
     * @param object $theme the theme we are rendering for.
     * @param moodle_page $page the page we are doing output for.
     */
    public function __construct($theme, $page) {
        parent::__construct($theme, $page);
        $this->renderers = array('core' => new custom_corners_core_renderer($this->page->opencontainers));
    }
}


/**
 * This is renderer factory allows themes to override the standard renderers using
 * php code.
 * 
 * It will load any code from theme/mytheme/renderers.php and
 * theme/parenttheme/renderers.php, if then exist. Then whenever you ask for
 * a renderer for 'component', it will create a mytheme_component_renderer or a
 * parenttheme_component_renderer, instead of a moodle_component_renderer,
 * if either of those classes exist.
 *
 * This generates the slightly different HTML that the custom_corners theme expects.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class theme_overridden_renderer_factory extends standard_renderer_factory {
    protected $prefixes = array();

    /**
     * Constructor.
     * @param object $theme the theme we are rendering for.
     * @param moodle_page $page the page we are doing output for.
     */
    public function __construct($theme, $page) {
        global $CFG;
        parent::__construct($theme, $page);

        // Initialise $this->prefixes.
        $renderersfile = $theme->dir . '/renderers.php';
        if (is_readable($renderersfile)) {
            include_once($renderersfile);
            $this->prefixes[] = $theme->name . '_';
        }
        if (!empty($theme->parent)) {
            $renderersfile = $CFG->themedir .'/'. $theme->parent . '/renderers.php';
            if (is_readable($renderersfile)) {
                include_once($renderersfile);
                $this->prefixes[] = $theme->parent . '_';
            }
        }
    }

    /* Implement the subclass method. */
    public function create_renderer($module) {
        foreach ($this->prefixes as $prefix) {
            $classname = $prefix . $module . '_renderer';
            if (class_exists($classname)) {
                if ($module == 'core') {
                    return new $classname($this->page->opencontainers);
                } else {
                    return new $classname($this->page->opencontainers, $this->get_renderer('core'));
                }
            }
        }
        return parent::create_renderer($module);
    }
}


/**
 * This is renderer factory that allows you to create templated themes.
 *
 * This should be considered an experimental proof of concept. In particular,
 * the performance is probably not very good. Do not try to use in on a busy site
 * without doing careful load testing first!
 *
 * This renderer factory returns instances of {@link template_renderer} class
 * which which implement the corresponding renderer interface in terms of
 * templates. To use this your theme must have a templates folder inside it.
 * Then suppose the method moodle_core_renderer::greeting($name = 'world');
 * exists. Then, a call to $OUTPUT->greeting() will cause the template
 * /theme/yourtheme/templates/core/greeting.php to be rendered, with the variable
 * $name available. The greeting.php template might contain
 * 
 * <pre>
 * <h1>Hello <?php echo $name ?>!</h1>
 * </pre>
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class template_renderer_factory extends renderer_factory_base {
    /**
     * An array of paths of where to search for templates. Normally this theme,
     * the parent theme then the standardtemplate theme. (If some of these do
     * not exist, or are the same as each other, then the list will be shorter.
     */
    protected $searchpaths = array();

    /**
     * Constructor.
     * @param object $theme the theme we are rendering for.
     * @param moodle_page $page the page we are doing output for.
     */
    public function __construct($theme, $page) {
        global $CFG;
        parent::__construct($theme, $page);

        // Initialise $this->searchpaths.
        if ($theme->name != 'standardtemplate') {
            $templatesdir = $theme->dir . '/templates';
            if (is_dir($templatesdir)) {
                $this->searchpaths[] = $templatesdir;
            }
        }
        if (!empty($theme->parent)) {
            $templatesdir = $CFG->themedir .'/'. $theme->parent . '/templates';
            if (is_dir($templatesdir)) {
                $this->searchpaths[] = $templatesdir;
            }
        }
        $this->searchpaths[] = $CFG->themedir .'/standardtemplate/templates';
    }

    /* Implement the subclass method. */
    public function create_renderer($module) {
        // Refine the list of search paths for this module.
        $searchpaths = array();
        foreach ($this->searchpaths as $rootpath) {
            $path = $rootpath . '/' . $module;
            if (is_dir($path)) {
                $searchpaths[] = $path;
            }
        }

        // Create a template_renderer that copies the API of the standard renderer.
        $copiedclass = $this->standard_renderer_class_for_module($module);
        return new template_renderer($copiedclass, $searchpaths, $this->page->opencontainers);
    }
}


/**
 * Simple base class for Moodle renderers.
 *
 * Tracks the xhtml_container_stack to use, which is passed in in the constructor.
 *
 * Also has methods to facilitate generating HTML output.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class moodle_renderer_base {
    /** @var xhtml_container_stack the xhtml_container_stack to use. */
    protected $containerstack;

    /**
     * Constructor
     * @param $containerstack the xhtml_container_stack to use. 
     */
    public function __construct($containerstack) {
        $this->containerstack = $containerstack;
    }

    protected function output_tag($tagname, $attributes, $contents) {
        return $this->output_start_tag($tagname, $attributes) . $contents .
                $this->output_end_tag($tagname);
    }
    protected function output_start_tag($tagname, $attributes) {
        return '<' . $tagname . $this->output_attributes($attributes) . '>';
    }
    protected function output_end_tag($tagname) {
        return '</' . $tagname . '>';
    }
    protected function output_empty_tag($tagname, $attributes) {
        return '<' . $tagname . $this->output_attributes($attributes) . ' />';
    }

    protected function output_attribute($name, $value) {
        if ($value || is_numeric($value)) { // We want 0 to be output.
            return ' ' . $name . '="' . $value . '"';
        }
    }
    protected function output_attributes($attributes) {
        $output = '';
        foreach ($attributes as $name => $value) {
            $output .= $this->output_attribute($name, $value);
        }
        return $output;
    }
    protected function output_class_attribute($classes) {
        return $this->output_attribute('class', implode(' ', $classes));
    }
}


/**
 * This is the templated renderer which copies the API of another class, replacing
 * all methods calls with instantiation of a template.
 *
 * When the method method_name is called, this class will search for a template
 * called method_name.php in the folders in $searchpaths, taking the first one
 * that it finds. Then it will set up variables for each of the arguments of that
 * method, and render the template. This is implemented in the {@link __call()}
 * PHP magic method.
 *
 * Methods like print_box_start and print_box_end are handles specially, and
 * implemented in terms of the print_box.php method.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class template_renderer extends moodle_renderer_base {
    /** @var ReflectionClass information about the class whose API we are copying. */
    protected $copiedclass;
    /** @var array of places to search for templates. */
    protected $searchpaths;

    /**
     * Magic word used when breaking apart container templates to implement
     * _start and _end methods.
     */
    const contentstoken = '-@#-Contents-go-here-#@-';

    /**
     * Constructor
     * @param string $copiedclass the name of a class whose API we should be copying.
     * @param $searchpaths a list of folders to search for templates in.
     * @param $containerstack the xhtml_container_stack to use.
     */
    public function __construct($copiedclass, $searchpaths, $containerstack) {
        parent::__construct($containerstack);
        $this->copiedclass = new ReflectionClass($copiedclass);
        $this->searchpaths = $searchpaths;
    }

    /* PHP magic method implementation. */
    public function __call($method, $arguments) {
        if (substr($method, -6) == '_start') {
            return $this->process_start(substr($method, 0, -6), $arguments);
        } else if (substr($method, -4) == '_end') {
            return $this->process_end(substr($method, 0, -4), $arguments);
        } else {
            return $this->process_template($method, $arguments);
        }
    }

    /**
     * Render the template for a given method of the renderer class we are copying,
     * using the arguments passed.
     * @param string $method the method that was called.
     * @param array $arguments the arguments that were passed to it.
     * @return string the HTML to be output.
     */
    protected function process_template($method, $arguments) {
        if (!$this->copiedclass->hasMethod($method) ||
                !$this->copiedclass->getMethod($method)->isPublic()) {
            throw new coding_exception('Unknown method ' . $method);
        }

        // Find the template file for this method.
        $template = $this->find_template($method);

        // Use the reflection API to find out what variable names the arguments
        // should be stored in, and fill in any missing ones with the defaults.
        $namedarguments = array();
        $expectedparams = $this->copiedclass->getMethod($method)->getParameters();
        foreach ($expectedparams as $param) {
            $paramname = $param->getName();
            if (!empty($arguments)) {
                $namedarguments[$paramname] = array_shift($arguments);
            } else if ($param->isDefaultValueAvailable()) {
                $namedarguments[$paramname] = $param->getDefaultValue();
            } else {
                throw new coding_exception('Missing required argument ' . $paramname);
            }
        }

        // Actually render the template.
        return $this->render_template($template, $namedarguments);
    }

    /**
     * Actually do the work of rendering the template.
     * @param $_template the full path to the template file.
     * @param $_namedarguments an array variable name => value, the variables
     *      that should be available to the template.
     * @return string the HTML to be output.
     */
    protected function render_template($_template, $_namedarguments) {
        // Note, we intentionally break the coding guidelines with regards to
        // local variable names used in this function, so that they do not clash
        // with the names of any variables being passed to the template.

        // Set up the global variables that the template may wish to access.
        global $CFG, $PAGE, $THEME;

        // And the parameters from the function call.
        extract($_namedarguments);

        // Include the template, capturing the output.
        ob_start();
        include($_template);
        $_result = ob_get_contents();
        ob_end_clean();

        return $_result;
    }

    /**
     * Searches the folders in {@link $searchpaths} to try to find a template for
     * this method name. Throws an exception if one cannot be found.
     * @param string $method the method name.
     * @return string the full path of the template to use.
     */
    protected function find_template($method) {
        foreach ($this->searchpaths as $path) {
            $filename = $path . '/' . $method . '.php';
            if (file_exists($filename)) {
                return $filename;
            }
        }
        throw new coding_exception('Cannot find template for ' . $this->copiedclass->getName() . '::' . $method);
    }

    /**
     * Handle methods like print_box_start by using the print_box template,
     * splitting the result, pusing the end onto the stack, then returning the start.
     * @param string $method the method that was called, with _start stripped off.
     * @param array $arguments the arguments that were passed to it.
     * @return string the HTML to be output.
     */
    protected function process_start($template, $arguments) {
        array_unshift($arguments, self::contentstoken);
        $html = $this->process_template($template, $arguments);
        list($start, $end) = explode(self::contentstoken, $html, 2);
        $this->containerstack->push($template, $end);
        return $start;
    }

    /**
     * Handle methods like print_box_end, we just need to pop the end HTML from
     * the stack.
     * @param string $method the method that was called, with _end stripped off.
     * @param array $arguments not used. Assumed to be irrelevant.
     * @return string the HTML to be output.
     */
    protected function process_end($template, $arguments) {
        return $this->containerstack->pop($template);
    }

    /**
     * @return array the list of paths where this class searches for templates.
     */
    public function get_search_paths() {
        return $this->searchpaths;
    }

    /**
     * @return string the name of the class whose API we are copying.
     */
    public function get_copied_class() {
        return $this->copiedclass->getName();
    }
}


/**
 * This class keeps track of which HTML tags are currently open.
 *
 * This makes it much easier to always generate well formed XHTML output, even
 * if execution terminates abruptly. Any time you output some opening HTML
 * without the matching closing HTML, you should push the neccessary close tags
 * onto the stack.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class xhtml_container_stack {
    /** @var array stores the list of open containers. */
    protected $opencontainsers = array();

    /**
     * Push the close HTML for a recently opened container onto the stack.
     * @param string $type The type of container. This is checked when {@link pop()}
     *      is called and must match, otherwise a developer debug warning is output.
     * @param string $closehtml The HTML required to close the container.
     */
    public function push($type, $closehtml) {
        $container = new stdClass;
        $container->type = $type;
        $container->closehtml = $closehtml;
        array_push($this->opencontainsers, $container);
    }

    /**
     * Pop the HTML for the next closing container from the stack. The $type
     * must match the type passed when the container was opened, otherwise a
     * warning will be output.
     * @param string $type The type of container.
     * @return string the HTML requried to close the container.
     */
    public function pop($type) {
        if (empty($this->opencontainsers)) {
            debugging('There are no more open containers. This suggests there is a nesting problem.', DEBUG_DEVELOPER);
            return;
        }

        $container = array_pop($this->opencontainsers);
        if ($container->type != $type) {
            debugging('The type of container to be closed (' . $container->type .
                    ') does not match the type of the next open container (' . $type .
                    '). This suggests there is a nesting problem.', DEBUG_DEVELOPER);
        }
        return $container->closehtml;
    }

    /**
     * Close all but the last open container. This is useful in places like error
     * handling, where you want to close all the open containers (apart from <body>)
     * before outputting the error message.
     * @return string the HTML requried to close any open containers inside <body>.
     */
    public function pop_all_but_last() {
        $output = '';
        while (count($this->opencontainsers) > 1) {
            $container = array_pop($this->opencontainsers);
            $output .= $container->closehtml;
        }
        return $output;
    }

    /**
     * You can call this function if you want to throw away an instance of this
     * class without properly emptying the stack (for example, in a unit test).
     * Calling this method stops the destruct method from outputting a developer
     * debug warning. After calling this method, the instance can no longer be used.
     */
    public function discard() {
        $this->opencontainsers = null;
    }

    /**
     * Emergency fallback. If we get to the end of processing and not all
     * containers have been closed, output the rest with a developer debug warning.
     */
    public function __destruct() {
        if (empty($this->opencontainsers)) {
            return;
        }

        debugging('Some containers were left open. This suggests there is a nesting problem.', DEBUG_DEVELOPER);
        echo $this->pop_all_but_last();
        $container = array_pop($this->opencontainsers);
        echo $container->closehtml;
    }
}


/**
 * The standard implementation of the moodle_core_renderer interface.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class moodle_core_renderer extends moodle_renderer_base {
    public function link_to_popup_window() {
        
    }

    public function button_to_popup_window() {
        
    }

    public function close_window_button($buttontext = null, $reloadopener = false) {
        if (empty($buttontext)) {
            $buttontext = get_string('closewindow');
        }
        // TODO
    }

    public function close_window($delay = 0, $reloadopener = false) {
        // TODO
    }

    /**
     * Output a <select> menu.
     *
     * You can either call this function with a single moodle_select_menu argument
     * or, with a list of parameters, in which case those parameters are sent to
     * the moodle_select_menu constructor.
     *
     * @param moodle_select_menu $selectmenu a moodle_select_menu that describes
     *      the select menu you want output.
     * @return string the HTML for the <select>
     */
    public function select_menu($selectmenu) {
        $selectmenu = clone($selectmenu);
        $selectmenu->prepare();

        if ($selectmenu->nothinglabel) {
            $selectmenu->options = array($selectmenu->nothingvalue => $selectmenu->nothinglabel) +
                    $selectmenu->options;
        }

        if (empty($selectmenu->id)) {
            $selectmenu->id = 'menu' . str_replace(array('[', ']'), '', $selectmenu->name);
        }

        $attributes = array(
            'name' => $selectmenu->name,
            'id' => $selectmenu->id,
            'class' => $selectmenu->get_classes_string(),
            'onchange' => $selectmenu->script,
        );
        if ($selectmenu->disabled) {
            $attributes['disabled'] = 'disabled';
        }
        if ($selectmenu->tabindex) {
            $attributes['tabindex'] = $tabindex;
        }

        if ($selectmenu->listbox) {
            if (is_integer($selectmenu->listbox)) {
                $size = $selectmenu->listbox;
            } else {
                $size = min($selectmenu->maxautosize, count($selectmenu->options));
            }
            $attributes['size'] = $size;
            if ($selectmenu->multiple) {
                $attributes['multiple'] = 'multiple';
            }
        }

        $html = $this->output_start_tag('select', $attributes) . "\n";
        foreach ($selectmenu->options as $value => $label) {
            $attributes = array('value' => $value);
            if ((string)$value == (string)$selectmenu->selectedvalue ||
                    (is_array($selectmenu->selectedvalue) && in_array($value, $selectmenu->selectedvalue))) {
                $attributes['selected'] = 'selected';
            }
            $html .= '    ' . $this->output_tag('option', $attributes, s($label)) . "\n";
        }
        $html .= $this->output_end_tag('select') . "\n";

        return $html;
    }

    // TODO choose_from_menu_nested

    // TODO choose_from_radio

    /**
     * Output an error message. By default wraps the error message in <span class="error">.
     * If the error message is blank, nothing is output.
     * @param $message the error message.
     * @return string the HTML to output.
     */
    public function error_text($message) {
        if (empty($message)) {
            return '';
        }
        return $this->output_tag('span', array('class' => 'error'), $message);
    }
}


/**
 * Base class for classes representing HTML elements, like moodle_select_menu.
 *
 * Handles the id and class attribues.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class moodle_html_component {
    /**
     * @var string value to use for the id attribute of this HTML tag.
     */
    public $id = '';
    /**
     * @var array class names to add to this HTML element.
     */
    public $classes = array();

    /**
     * Ensure some class names are an array.
     * @param mixed $classes either an array of class names or a space-separated
     *      string containing class names.
     * @return array the class names as an array.
     */
    public static function clean_clases($classes) {
        if (is_array($classes)) {
            return $classes;
        } else {
            return explode(' '. trim($classes));
        }
    }

    /**
     * Set the class name array.
     * @param mixed $classes either an array of class names or a space-separated
     *      string containing class names.
     */
    public function set_classes($classes) {
        $this->classes = self::clean_clases($classes);
    }

    /**
     * Add a class name to the class names array.
     * @param string $class the new class name to add.
     */
    public function add_class($class) {
        $this->classes[] = $class;
    }

    /**
     * Add a whole lot of class names to the class names array.
     * @param mixed $classes either an array of class names or a space-separated
     *      string containing class names.
     */
    public function add_classes($classes) {
        $this->classes += self::clean_clases($classes);
    }

    /**
     * Get the class names as a string.
     * @return string the class names as a space-separated string. Ready to be put in the class="" attribute.
     */
    public function get_classes_string() {
        return implode(' ', $this->classes);
    }

    /**
     * Perform any cleanup or final processing that should be done before an
     * instance of this class is output.
     */
    public function prepare() {
        $this->classes = array_unique(self::clean_clases($this->classes));
    }
}


/**
 * This class hold all the information required to describe a <select> menu that
 * will be printed by {@link moodle_core_renderer::select_menu()}. (Or by an overrides
 * version of that method in a subclass.)
 *
 * All the fields that are not set by the constructor have sensible defaults, so
 * you only need to set the properties where you want non-default behaviour.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class moodle_select_menu extends moodle_html_component {
    /**
     * @var array the choices to show in the menu. An array $value => $label.
     */
    public $options;
    /**
     * @var string the name of this form control. That is, the name of the GET/POST
     * variable that will be set if this select is submmitted as part of a form.
     */
    public $name;
    /**
     * @var string the option to select initially. Should match one
     * of the $options array keys. Default none.
     */
    public $selectedvalue;
    /**
     * @var string The label for the 'nothing is selected' option.
     * Defaults to get_string('choosedots').
     * Set this to '' if you do not want a 'nothing is selected' option.
     */
    public $nothinglabel = null;
    /**
     * @var string The value returned by the 'nothing is selected' option. Defaults to 0.
     */
    public $nothingvalue = 0;
    /**
     * @var boolean set this to true if you want the control to appear disabled.
     */
    public $disabled = false;
    /**
     * @var integer if non-zero, sets the tabindex attribute on the <select> element. Default 0.
     */
    public $tabindex = 0;
    /**
     * @var mixed Defaults to false, which means display the select as a dropdown menu.
     * If true, display this select as a list box whose size is chosen automatically.
     * If an integer, display as list box of that size.
     */
    public $listbox = false;
    /**
     * @var integer if you are using $listbox === true to get an automatically
     * sized list box, the size of the list box will be the number of options,
     * or this number, whichever is smaller.
     */
    public $maxautosize = 10;
    /**
     * @var boolean if true, allow multiple selection. Only used if $listbox is true.
     */
    public $multiple = false;
    /**
     * @deprecated
     * @var string JavaScript to add as an onchange attribute. Do not use this.
     * Use the YUI even library instead.
     */
    public $script = '';

    /* @see lib/moodle_html_component#prepare() */
    public function prepare() {
        if (empty($this->id)) {
            $this->id = 'menu' . str_replace(array('[', ']'), '', $this->name);
        }
        if (empty($this->classes)) {
            $this->set_classes(array('menu' . str_replace(array('[', ']'), '', $this->name)));
        }
        $this->add_class('select');
        parent::prepare();
    }

    /**
     * This is a shortcut for making a simple select menu. It lets you specify
     * the options, name and selected option in one line of code.
     * @param array $options used to initialise {@link $options}.
     * @param string $name used to initialise {@link $name}.
     * @param string $selected  used to initialise {@link $selected}.
     * @return moodle_select_menu A moodle_select_menu object with the three common fields initialised.
     */
    public static function make($options, $name, $selected = '') {
        $menu = new moodle_select_menu();
        $menu->options = $options;
        $menu->name = $name;
        $menu->selectedvalue = $selected;
        return $menu;
    }

    /**
     * This is a shortcut for making a yes/no select menu.
     * @param string $name used to initialise {@link $name}.
     * @param string $selected  used to initialise {@link $selected}.
     * @return moodle_select_menu A menu initialised with yes/no options.
     */
    public static function make_yes_no($name, $selected) {
        return self::make(array(0 => get_string('no'), 1 => get_string('yes')), $name, $selected);
    }
}


/**
 * A renderer for the custom corner theme, and other themes based on it.
 *
 * Generates the slightly different HTML that the custom corners theme wants.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class custom_corners_core_renderer extends moodle_core_renderer {

    // TODO
}

