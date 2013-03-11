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
 * Nasty strings to use in tests.
 *
 * @package   core
 * @category  test
 * @copyright 2013 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Nasty strings manager.
 *
 * Responds to nasty strings requests with a random string of the list
 * to try with different combinations in different places.
 *
 * @package   core
 * @category  test
 * @copyright 2013 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class nasty_strings {

    /**
     * List of different strings to fill fields and assert against them
     *
     * Non of these strings can be a part of another one, this would not be good
     * when using more one string at the same time and asserting results.
     *
     * @static
     * @var array
     */
    protected static $strings = array(
        '< > & &lt; &gt; &amp; \' \\" \ \'$@NULL@$ @@TEST@@ \\\" \\ , ; : . 日本語­% %%',
        '&amp; \' \\" \ \'$@NULL@$ < > & &lt; &gt; @@TEST@@ \\\" \\ , ; : . 日本語­% %%',
        '< > & &lt; &gt; &amp; \' \\" \ \\\" \\ , ; : . \'$@NULL@$ @@TEST@@ 日本語­% %%',
        '< > & &lt; &gt; &amp; \' \\" \ \'$@NULL@$ 日本語­% %%@@TEST@@ \. \\" \\ , ; :',
        '< > & &lt; &gt; \\\" \\ , ; : . 日本語&amp; \' \\" \ \'$@NULL@$ @@TEST@@­% %%',
        '\' \\" \ \'$@NULL@$ @@TEST@@ < > & &lt; &gt; &amp; \\\" \\ , ; : . 日本語­% %%',
        '\\\" \\ , ; : . 日本語­% < > & &lt; &gt; &amp; \' \\" \ \'$@NULL@$ @@TEST@@ %%',
        '< > & &lt; &gt; &amp; \' \\" \ \'$@NULL@$ 日本語­% %% @@TEST@@ \\\" \\ . , ; :',
        '. 日本語&amp; \' \\" < > & &lt; &gt; \\ , ; : \ \'$@NULL@$ \\\" @@TEST@@­% %%',
        '&amp; \' \\" \ < > & &lt; &gt; \\\" \\ , ; : . 日本語\'$@NULL@$ @@TEST@@­% %%',
    );

    /**
     * Already used nasty strings.
     *
     * This array will be cleaned before each scenario.
     *
     * @static
     * @var array
     */
    protected static $usedstrings = array();

    /**
     * Returns a nasty string and stores the key mapping.
     *
     * @static
     * @param string $key The key
     * @return string
     */
    public static function get($key) {

        // If have been used during the this tests return it.
        if (isset(self::$usedstrings[$key])) {
            return self::$strings[self::$usedstrings[$key]];
        }

        // Getting non-used random string.
        do {
            $index = self::random_index();
        } while (in_array($index, self::$usedstrings));

        // Mark the string as already used.
        self::$usedstrings[$key] = $index;

        return self::$strings[$index];
    }

    /**
     * Resets the used strings var.
     * @static
     * @return void
     */
    public static function reset_used_strings() {
        self::$usedstrings = array();
    }

    /**
     * Returns a random index.
     * @static
     * @return int
     */
    protected static function random_index() {
        return mt_rand(0, count(self::$strings) - 1);
    }

}
