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

/**
 * The base class for a guideline
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class brickfield_accessibility_guideline {
    /** @var object The current document's DOMDocument */
    public $dom;

    /** @var object The current brickfield CSS object */
    public $css;

    /** @var array The path to the current document */
    public $path;

    /** @var array An array of report objects */
    public $report;

    /** @var array An array of translations for all this guideline's tests */
    public $translations;

    /** @var bool Whether we are running in CMS mode */
    public $cmsmode = false;

    /** @var array An array of all the severity levels for every test */
    public $severity = [];

    /**
     * The class constructor.
     * @param object $dom The current DOMDocument object
     * @param object $css The current brickfieldCSS object
     * @param array $path The current path
     * @param null $arg
     * @param string $domain
     * @param bool $cmsmode
     */
    public function __construct(&$dom, &$css, array &$path,
                                $arg = null, string $domain = 'en', bool $cmsmode = false) {
        $this->dom = &$dom;
        $this->css = &$css;
        $this->path = &$path;
        $this->cmsmode = $cmsmode;
        $this->load_translations($domain);
        $this->run($arg, $domain);
    }

    /**
     * Returns an array of all the tests associated with the current guideline
     * @return array
     */
    public function get_tests(): array {
        return $this->tests;
    }

    /**
     * Loads translations from a file. This can be overriden, just as long as the
     * local variable 'translations' is an associative array with test function names
     * as the key
     * @param string $domain
     */
    public function load_translations(string $domain) {
        $csv = fopen(dirname(__FILE__) .'/guidelines/translations/'. $domain .'.txt', 'r');

        if ($csv) {
            while ($translation = fgetcsv($csv)) {
                if (count($translation) == 4) {
                    $this->translations[$translation[0]] = [
                        'title'       => $translation[1],
                        'description' => $translation[2],
                    ];
                }
            }
        }
    }

    /**
     * Returns the translation for a test name.
     * @param string $testname The function name of the test
     * @return mixed
     */
    public function get_translation(string $testname) {
        return (isset($this->translations[$testname]))
            ? $this->translations[$testname]
            : $testname;
    }

    /**
     * Iterates through each test string, makes a new test object, and runs it against the current DOM
     * @param null $arg
     * @param string $language
     */
    public function run($arg = null, string $language = 'en') {
        foreach ($this->tests as $testname => $options) {
            if (is_numeric($testname) && !is_array($options)) {
                $testname = $options;
            }
            $name = $testname;
            $testname = 'tool_brickfield\local\htmlchecker\common\\checks\\'.$testname;
            if (class_exists($testname) && $this->dom) {
                $testname = new $testname($this->dom, $this->css, $this->path, $language, $arg);
                if (!$this->cmsmode || ($testname->cms && $this->cmsmode)) {
                    $this->report[$name] = $testname->get_report();
                }
                $this->severity[$name] = $testname->defaultseverity;
                unset($testname);
            } else {
                $this->report[$name] = false;
            }
        }
    }

    /**
     * Returns all the Report variable
     * @return mixed Look to your report to see what it returns
     */
    public function get_report() {
        return $this->report;
    }

    /**
     * Returns the severity level of a given test
     * @param string $testname The name of the test
     * @return int The severity level
     */
    public function get_severity(string $testname): int {
        if (isset($this->tests[$testname]['severity'])) {
            return $this->tests[$testname]['severity'];
        }

        if (isset($this->severity[$testname])) {
            return $this->severity[$testname];
        }

        return brickfield_accessibility::BA_TEST_MODERATE;
    }
}
