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

namespace tool_brickfield\local\htmlchecker;

use DOMDocument;
use tool_brickfield\local\htmlchecker\common\brickfield_accessibility_css;

/**
 * Brickfield accessibility HTML checker library.
 *
 * The main interface class for brickfield_accessibility.
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class brickfield_accessibility {

    /** @var int Failure level severe. */
    const BA_TEST_SEVERE = 1;

    /** @var int Failure level moderate. */
    const BA_TEST_MODERATE = 2;

    /** @var int Failure level seggestion. */
    const BA_TEST_SUGGESTION = 3;

    /** @var string Tag identifier to enclose all error HTML fragments in. */
    const BA_ERROR_TAG = 'bferror';

    /** @var object The central DOMDocument object */
    public $dom;

    /** @var string The type of request this is (either 'string', 'file', or 'uri' */
    public $type;

    /** @var string The value of the request. Either HTML, a URI, or the path to a file */
    public $value;

    /** @var string The base URI of the current request (used to rebuild page if necessary) */
    public $uri = '';

    /** @var string The translation domain of the current library */
    public $domain;

    /** @var string The name of the guideline */
    public $guidelinename = 'wcag';

    /** @var string The name of the reporter to use */
    public $reportername = 'static';

    /** @var object A reporting object */
    public $reporter;

    /** @var object The central guideline object */
    public $guideline;

    /** @var string The base URL for any request of type URI */
    public $baseurl;

    /** @var array An array of the current file or URI path */
    public $path = [];

    /** @var array An array of additional CSS files to load (useful for CMS content) */
    public $cssfiles = [];

    /** @var object The brickfieldCSS object */
    public $css;

    /** @var array An array of additional options */
    public $options = [
            'cms_mode'      => false,
            'start_element' => 0,
            'end_element'   => 0,
            'cms_template'  => []
        ];

    /** @var bool An indicator if the DOMDocument loaded. If not, this means that the
     * HTML given to it was so munged it wouldn't even load.
     */
    public $isvalid = true;

    /**
     * The class constructor
     * @param string $value Either the HTML string to check or the file/uri of the request
     * @param string $guideline The name of the guideline
     * @param string $type The type of the request (either file, uri, or string)
     * @param string $reporter The name of the reporter to use
     * @param string $domain The domain of the translation language to use
     */
    public function __construct(string $value = '', string $guideline = 'wcag2aaa', string $type = 'string',
                                string $reporter = 'static', string $domain = 'en') {
        $this->dom = new DOMDocument();
        $this->type = $type;
        if ($type == 'uri' || $type == 'file') {
            $this->uri = $value;
        }
        $this->domain = $domain;
        $this->guidelinename = $guideline;
        $this->reportername = $reporter;
        $this->value = $value;
    }

    /**
     * Prepares the DOMDocument object for brickfield_accessibility. It loads based on the file type
     * declaration and first scrubs the value using prepareValue().
     */
    public function prepare_dom() {
        $this->prepare_value();
        $this->isvalid = @$this->dom->loadHTML('<?xml encoding="utf-8" ?>' . $this->value);
        $this->prepare_base_url($this->value, $this->type);
    }

    /**
     * If the CMS mode options are set, then we remove some items front the
     * HTML value before sending it back.
     */
    public function prepare_value() {
        // We ignore the 'string' type because it would mean the value already contains HTML.
        if ($this->type == 'file' || $this->type == 'uri') {
            $this->value = @file_get_contents($this->value);
        }

        // If there are no surrounding tags, add self::BA_ERROR_TAG to prevent the DOM from adding a <p> tag.
        if (strpos(trim($this->value), '<') !== 0) {
            $this->value = '<' . self::BA_ERROR_TAG . '>' . $this->value . '</' . self::BA_ERROR_TAG . '>';
        }
    }

    /**
     * Set global predefined options for brickfield_accessibility. First we check that the
     * array key has been defined.
     * @param mixed $variable Either an array of values, or a variable name of the option
     * @param mixed $value If this is a single option, the value of the option
     */
    public function set_option($variable, $value = null) {
        if (!is_array($variable)) {
            $variable = [$variable => $value];
        }
        foreach ($variable as $k => $value) {
            if (isset($this->options[$k])) {
                $this->options[$k] = $value;
            }
        }
    }

    /**
     * Returns an absolute path from a relative one.
     * @param string $absolute The absolute URL
     * @param string $relative The relative path
     * @return string A new path
     */
    public function get_absolute_path(string $absolute, string $relative): string {
        if (substr($relative, 0, 2) == '//') {
            if ($this->uri) {
                $current = parse_url($this->uri);
            } else {
                $current = ['scheme' => 'http'];
            }
            return $current['scheme'] .':'. $relative;
        }

        $relativeurl = parse_url($relative);

        if (isset($relativeurl['scheme'])) {
            return $relative;
        }

        $absoluteurl = parse_url($absolute);

        if (isset($absoluteurl['path'])) {
            $path = dirname($absoluteurl['path']);
        }

        if ($relative[0] == '/') {
            $cparts = array_filter(explode('/', $relative));
        } else {
            $aparts = array_filter(explode('/', $path));
            $rparts = array_filter(explode('/', $relative));
            $cparts = array_merge($aparts, $rparts);

            foreach ($cparts as $i => $part) {
                if ($part == '.') {
                    $cparts[$i] = null;
                }

                if ($part == '..') {
                    $cparts[$i - 1] = null;
                    $cparts[$i] = null;
                }
            }

            $cparts = array_filter($cparts);
        }

        $path = implode('/', $cparts);
        $url  = "";

        if (isset($absoluteurl['scheme'])) {
            $url = $absoluteurl['scheme'] .'://';
        }

        if (isset($absoluteurl['user'])) {
            $url .= $absoluteurl['user'];

            if ($absoluteurl['pass']) {
                $url .= ':'. $absoluteurl['user'];
            }

            $url .= '@';
        }

        if (isset($absoluteurl['host'])) {
            $url .= $absoluteurl['host'];

            if (isset($absoluteurl['port'])) {
                $url .= ':'. $absoluteurl['port'];
            }

            $url .= '/';
        }

        $url .= $path;

        return $url;
    }

    /**
     * Sets the URI if this is for a string or to change where
     * Will look for resources like CSS files
     * @param string $uri The URI to set
     */
    public function set_uri(string $uri) {
        if (parse_url($uri)) {
            $this->uri = $uri;
        }
    }

    /**
     * Formats the base URL for either a file or uri request. We are essentially
     * formatting a base url for future reporters to use to find CSS files or
     * for tests that use external resources (images, objects, etc) to run tests on them.
     * @param string $value The path value
     * @param string $type The type of request
     */
    public function prepare_base_url(string $value, string $type) {
        if ($type == 'file') {
            $path = explode('/', $this->uri);
            array_pop($path);
            $this->path = $path;
        } else if ($type == 'uri' || $this->uri) {
            $parts = explode('://', $this->uri);
            $this->path[] = $parts[0] .':/';

            if (is_array($parts[1])) {
                foreach (explode('/', $this->get_base_from_file($parts[1])) as $part) {
                    $this->path[] = $part;
                }
            } else {
                $this->path[] = $parts[1] .'/';
            }
        }
    }

    /**
     * Retrieves the absolute path to a file
     * @param string $file The path to a file
     * @return string The absolute path to a file
     */
    public function get_base_from_file(string $file): string {
         $find = '/';
         $afterfind = substr(strrchr($file, $find), 1);
         $strlenstr = strlen($afterfind);
         $result = substr($file, 0, -$strlenstr);

         return $result;
    }

    /**
     * Helper method to add an additional CSS file
     * @param string $css The URI or file path to a CSS file
     */
    public function add_css(string $css) {
        if (is_array($css)) {
            $this->cssfiles = $css;
        } else {
            $this->cssfiles[] = $css;
        }
    }

    /**
     * Retrives a single error from the current reporter
     * @param string $error The error key
     * @return object A ReportItem object
     */
    public function get_error(string $error) {
        return $this->reporter->get_error($error);
    }

    /**
     * A local method to load the required file for a reporter and set it for the current object
     * @param array $options An array of options for the reporter
     */
    public function load_reporter(array $options = []) {
        $classname = '\\tool_brickfield\\local\\htmlchecker\\reporters\\'.'report_'.$this->reportername;

        $this->reporter = new $classname($this->dom, $this->css, $this->guideline, $this->path);

        if (count($options)) {
            $this->reporter->set_options($options);
        }
    }

    /**
     * Checks that the DOM object is valid or not
     * @return bool Whether the DOMDocument is valid
     */
    public function is_valid(): bool {
        return $this->isvalid;
    }

    /**
     * Starts running automated checks. Loads the CSS file parser
     * and the guideline object.
     * @param null $options
     * @return bool
     */
    public function run_check($options = null) {
        $this->prepare_dom();

        if (!$this->is_valid()) {
            return false;
        }

        $this->get_css_object();
        $classname = 'tool_brickfield\\local\\htmlchecker\\guidelines\\'.strtolower($this->guidelinename).'_guideline';

        $this->guideline = new $classname($this->dom, $this->css, $this->path, $options, $this->domain, $this->options['cms_mode']);
    }

    /**
     * Loads the brickfield_accessibility_css object
     */
    public function get_css_object() {
        $this->css = new brickfield_accessibility_css($this->dom, $this->uri, $this->type, $this->path, false, $this->cssfiles);
    }

    /**
     * Returns a formatted report from the current reporter.
     * @param array $options An array of all the options
     * @return mixed See the documentation on your reporter's getReport method.
     */
    public function get_report(array $options = []) {
        if (!$this->reporter) {
            $this->load_reporter($options);
        }
        if ($options) {
            $this->reporter->set_options($options);
        }
        $report = $this->reporter->get_report();
        $path = $this->path;
        return ['report' => $report, 'path' => $path];
    }

    /**
     * Runs one test on the current DOMDocument
     * @param string $test The name of the test to run
     * @return bool|array The ReportItem returned from the test
     */
    public function get_test(string $test) {
        $test = 'tool_brickfield\local\htmlchecker\common\checks\\' . $test;

        if (!class_exists($test)) {
            return false;
        }

        $testclass = new $test($this->dom, $this->css, $this->path);

        return $testclass->report;
    }

    /**
     * Retrieves the default severity of a test
     * @param string $test The name of the test to run
     * @return object The severity level of the test
     */
    public function get_test_severity(string $test) {
        $testclass = new $test($this->dom, $this->css, $this->path);

        return $testclass->get_severity();
    }

    /**
     * A general cleanup function which just does some memory
     * cleanup by unsetting the particularly large local vars.
     */
    public function cleanup() {
        unset($this->dom);
        unset($this->css);
        unset($this->guideline);
        unset($this->reporter);
    }

    /**
     * Determines if the link text is the same as the link URL, without necessarily being an exact match.
     * For example, 'www.google.com' matches 'https://www.google.com'.
     * @param string $text
     * @param string $href
     * @return bool
     */
    public static function match_urls(string $text, string $href): bool {
        $parsetext = parse_url($text);
        $parsehref = parse_url($href);
        $parsetextfull = (isset($parsetext['host'])) ? $parsetext['host'] : '';
        $parsetextfull .= (isset($parsetext['path'])) ? $parsetext['path'] : '';
        $parsehreffull = (isset($parsehref['host'])) ? $parsehref['host'] : '';
        $parsehreffull .= (isset($parsehref['path'])) ? $parsehref['path'] : '';

        // Remove any last '/' character before comparing.
        return (rtrim($parsetextfull, '/') === rtrim($parsehreffull, '/'));
    }
}
