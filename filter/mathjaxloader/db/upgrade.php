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
 * MathJAX filter upgrade code.
 *
 * @package    filter_mathjaxloader
 * @copyright  2014 Damyon Wiese (damyon@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_filter_mathjaxloader_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Moodle v2.7.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2014081100) {

        $sslcdnurl = get_config('filter_mathjaxloader', 'httpsurl');
        if ($sslcdnurl === "https://c328740.ssl.cf1.rackcdn.com/mathjax/2.3-latest/MathJax.js") {
            set_config('httpsurl', 'https://cdn.mathjax.org/mathjax/2.3-latest/MathJax.js', 'filter_mathjaxloader');
        }

        upgrade_plugin_savepoint(true, 2014081100, 'filter', 'mathjaxloader');
    }

    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2015021200) {

        $httpurl = get_config('filter_mathjaxloader', 'httpurl');
        // Don't change the config if it has been manually changed to something besides the default setting value.
        if ($httpurl === "http://cdn.mathjax.org/mathjax/2.3-latest/MathJax.js") {
            set_config('httpurl', 'http://cdn.mathjax.org/mathjax/2.5-latest/MathJax.js', 'filter_mathjaxloader');
        }

        $httpsurl = get_config('filter_mathjaxloader', 'httpsurl');
        // Don't change the config if it has been manually changed to something besides the default setting value.
        if ($httpsurl === "https://cdn.mathjax.org/mathjax/2.3-latest/MathJax.js") {
            set_config('httpsurl', 'https://cdn.mathjax.org/mathjax/2.5-latest/MathJax.js', 'filter_mathjaxloader');
        }

        upgrade_plugin_savepoint(true, 2015021200, 'filter', 'mathjaxloader');
    }

    if ($oldversion < 2015021700) {

        $oldconfig = get_config('filter_mathjaxloader', 'mathjaxconfig');
        $olddefault = 'MathJax.Hub.Config({
    config: ["MMLorHTML.js", "Safe.js"],
    jax: ["input/TeX","input/MathML","output/HTML-CSS","output/NativeMML"],
    extensions: ["tex2jax.js","mml2jax.js","MathMenu.js","MathZoom.js"],
    TeX: {
        extensions: ["AMSmath.js","AMSsymbols.js","noErrors.js","noUndefined.js"]
    },
    menuSettings: {
        zoom: "Double-Click",
        mpContext: true,
        mpMouse: true
    },
    errorSettings: { message: ["!"] },
    skipStartupTypeset: true,
    messageStyle: "none"
});
';
        $newdefault = '
MathJax.Hub.Config({
    config: ["Accessible.js", "Safe.js"],
    errorSettings: { message: ["!"] },
    skipStartupTypeset: true,
    messageStyle: "none"
});
';

        // Ignore white space changes.
        $oldconfig = trim(preg_replace('/\s+/', ' ', $oldconfig));
        $olddefault = trim(preg_replace('/\s+/', ' ', $olddefault));

        // Update the default config for mathjax only if it has not been customised.

        if ($oldconfig == $olddefault) {
            set_config('mathjaxconfig', $newdefault, 'filter_mathjaxloader');
        }

        upgrade_plugin_savepoint(true, 2015021700, 'filter', 'mathjaxloader');
    }

    return true;
}
