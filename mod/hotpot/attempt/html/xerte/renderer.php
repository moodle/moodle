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
 * Render an attempt at a HotPot quiz
 * Output format: html_xerte
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// get parent class
require_once($CFG->dirroot.'/mod/hotpot/attempt/html/renderer.php');

/**
 * mod_hotpot_attempt_html_xerte_renderer
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class mod_hotpot_attempt_html_xerte_renderer extends mod_hotpot_attempt_html_renderer {

    // source file types with which this output format can be used
    var $filetypes = array('html_xerte');

    /**
     * init
     *
     * @param xxx $quiz (passed by reference)
     */
    function init($hotpot) {
        $hotpot->usemediafilter = 0;
        parent::init($hotpot);
    }

    /**
     * preprocessing
     *
     * @return xxx
     */
    function preprocessing() {
        if ($this->cache_uptodate) {
            return true;
        }

        if (! $this->hotpot->source->get_filecontents()) {
            // empty source file - shouldn't happen !!
            return false;
        }

        if ($pos = strpos($this->hotpot->source->filecontents, '<title>')) {
            $insert = '<base href="'.$this->hotpot->source->baseurl.'/'.$this->hotpot->source->filepath.'">'."\n";
            $this->hotpot->source->filecontents = substr_replace($this->hotpot->source->filecontents, $insert, $pos, 0);
        }

        // replace external javascript with modified inline javascript
        $search = '/<script[^>]*src\s*=\s*"([^"]*)"[^>]*>\s*<\/script>/';
        $callback = array($this, 'preprocessing_xerte_js');
        $this->hotpot->source->filecontents = preg_replace_callback($search, $callback, $this->hotpot->source->filecontents);

        parent::preprocessing();
    }

    /**
     * preprocessing_xerte_js
     *
     * @param xxx $match
     * @return xxx
     */
    function preprocessing_xerte_js($match) {
        $js = $this->hotpot->source->get_sibling_filecontents($match[1]);

        // set baseurl
        $baseurl = $this->hotpot->source->baseurl.'/';
        if ($pos = strrpos($this->hotpot->source->filepath, '/')) {
            $baseurl .= substr($this->hotpot->source->filepath, 0, $pos + 1);
        }

        // several search-and-replace fixes
        //  - add style to center the Flash Object
        //  - convert MainPreloader.swf to absolute URL
        //  - break up "script" strings to prevent unwanted HotPot postprocessing
        $search = array(
            'style="'."padding:0px; width:' + rloWidth + 'px; height:' + rloHeight + 'px;".'"', // NEW
            ' style="'."width:' + rloWidth + 'px; height:' + rloHeight + 'px; ".'"',            // OLD
            'var FileLocation = xmlPath;',       // NEW
            'var FileLocation = getLocation();', // OLD
            'MainPreloader.swf',
            'script', 'Script', 'SCRIPT',
        );
        $replace = array(
            'style="'."padding:0px; width:' + rloWidth + 'px; height:' + rloHeight + 'px; margin:auto;".'"', // NEW
            ' style="'."width:' + rloWidth + 'px; height:' + rloHeight + 'px; margin:auto;".'"', // OLD
            "var FileLocation = '$baseurl';", // NEW
            "var FileLocation = '$baseurl';", // OLD
            $baseurl.'MainPreloader.swf',
            "scr' + 'ipt", "Scr' + 'ipt", "SCR' + 'IPT",
        );

        if ($this->hotpot->source->get_displayMode()=='fill window') {
            // remove "id" to prevent resizing of Flash object
            // there might be another way to do this
            // e.g. using js to stretch canvas area
            $search[] = 'id="'."rlo' + rloID + '".'"';
            $replace[] = '';
        }

        $js = str_replace($search, $replace, $js);
        return '<script type="text/javascript">'."\n".trim($js)."\n".'</script>'."\n";
    }
}
