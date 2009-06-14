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
 * Load up any required Javascript libraries
 *
 * @package   moodlecore
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}
?>

<!--<style type="text/css">/*<![CDATA[*/ body{behavior:url(<?php echo $CFG->httpswwwroot ?>/lib/csshover.htc);} /*]]>*/</style>-->

<script type="text/javascript" src="<?php echo $CFG->httpswwwroot ?>/lib/javascript-static.js"></script>
<script type="text/javascript" src="<?php echo $CFG->httpswwwroot ?>/lib/javascript-mod.php"></script>
<script type="text/javascript" src="<?php echo $CFG->httpswwwroot ?>/lib/overlib/overlib.js"></script>
<script type="text/javascript" src="<?php echo $CFG->httpswwwroot ?>/lib/overlib/overlib_cssstyle.js"></script>
<script type="text/javascript" src="<?php echo $CFG->httpswwwroot ?>/lib/cookies.js"></script>
<script type="text/javascript" src="<?php echo $CFG->httpswwwroot ?>/lib/ufo.js"></script>
<script type="text/javascript" src="<?php echo $CFG->httpswwwroot ?>/lib/dropdown.js"></script>

<script type="text/javascript" defer="defer">
//<![CDATA[
setTimeout('fix_column_widths()', 20);
//]]>
</script>
<script type="text/javascript">
//<![CDATA[
var id2clientid = {};
var id2itemid   = {};
<?php
if (!empty($focus)) {
    if(($pos = strpos($focus, '.')) !== false) {
        //old style focus using form name - no allowed inXHTML Strict
        $topelement = substr($focus, 0, $pos);
        echo "addonload(function() { if(document.$topelement) document.$focus.focus(); });\n";
    } else {
        //focus element with given id
        echo "addonload(function() { if(el = document.getElementById('$focus')) el.focus(); });\n";
    }
    $focus = false; // Prevent themes from adding it to body tag which breaks addonload(), MDL-10249
}
?>
//]]>
</script>
