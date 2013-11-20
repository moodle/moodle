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
 * Strings for component 'filter_mathjax', language 'en'.
 *
 * Note: use filter/mathjax/cli/update_lang_files.php script to import strings from upstream JS lang files.
 *
 * @package    filter_mathjax
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


//== Custom Moodle strings that are not part of upstream MathJax ==
$string['filtername'] = 'MathJax';
$string['httpurl'] = 'HTTP MathJax URL';
$string['httpurl_help'] = 'Full URL to MathJax library. Used when the page is loaded via http.';
$string['httpsurl'] = 'HTTP MathJax URL';
$string['httpsurl_help'] = 'Full URL to MathJax library. Used when the page is loaded via https (secure). ';
$string['localinstall'] = 'Local MathJax installation';
$string['localinstall_help'] = 'The default MathJAX configuration uses the CDN version of MathJAX, but MathJAX can be installed locally if required. Some reasons this might be useful are to save on bandwidth - or because of local proxy restrictions. To use a local installation of MathJAX, first download the full MathJax library from http://www.mathjax.org/. Then install it on a web server. Finally update the MathJax filter settings httpurl and/or httpsurl to point to the local MathJax.js url.';
$string['mathjaxsettings'] = 'MathJax configuration';
$string['mathjaxsettings_desc'] = 'The default MathJAX configuration should be appropriate for most users, but MathJax is highly configurable and advanced users may want to provide a different configuration. More information on MathJax configuration options is available here: http://docs.mathjax.org/en/latest/options/index.html';

