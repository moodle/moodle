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
 * Strings for component 'theme_anomaly', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   theme_anomaly
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['choosereadme'] = '<div style="text-align:center;">
<h2 style="margin-bottom:2px;">Anomaly Theme Pack</h2>
<h3 style="margin:0">by <a href="http://newschoollearning.com" title="NewSchool Learning: Standards Based Moodle Designs">Patrick Malley</a></h3><h4 style="margin:3px">Version: 20090119</h4>
</div>
<div>&nbsp;
</div>
<h3 style="margin-bottom:5px; margin-top:5px;">Changing Your Color Preference</h3>
<p>To assist you in making color changes, I have separated all color attributes into separate CSS documents named styles_[color].css where [color] is actually the name of the different color variants.</p>
<p>The default color for this theme is green. To select a different color variant: </p>
<ol>
<li>Remane styles_select.css to styles_green.css.</li>
<li>Rename the styles_[color].css variant that you would like to use to styles_select.css.</li>
<li>That\'s it. Where you expecting something trickier?</li>
</ol>
<h3 style="margin-bottom:5px; margin-top:5px;">Editing your Site Tagline</h3>
<p>This theme uses PHP code in header.html that automatically pulls the site description that you have entered in Admin > Front page > Front page settings and places it directly below your site name at the top of the front page.</p>
<p>Here\'s the code in header.html that is responsible for this (highlighted in red; found on line 58):</p>
<code style="display:block; padding:10px; margin:10px; background:#f6f6f6; border:1px solid #eee;">
    &lt;h1 class=&quot;headermain&quot;&gt;&lt;?php echo $heading ?&gt;<span style="color:#ff0000">&lt;br /&gt;&lt;span&gt;&lt;?php echo $COURSE-&gt;summary //Retrieves Site Description from Front Page -&gt; Front Page Settings ?&gt;&lt;/span&gt;</span>&lt;/h1&gt;
</code>
<p>If you don\'t want to show a tagline, simply delete the code highlighted in red above from header.html and save.</p>
<p>If you want to show a tagline, BUT would prefer your tagline to be something other than your site description, you can manually enter your Tagline into header.html in the same sort of way.</p>
<p>For example, if I wanted to display my tagline - Standards Based Moodle Designs - I would edit the above code as follows:</p>
<code style="display:block; padding:10px; margin:10px; background:#f6f6f6; border:1px solid #eee;">
&lt;h1 class=&quot;headermain&quot;&gt;&lt;?php echo $heading ?&gt;&lt;br /&gt;&lt;span&gt;<span style="color:#ff0000;">Standards Based Moodle Designs</span>&lt;/span&gt;&lt;/h1&gt;
</code>
<h3 style="margin-bottom:5px; margin-top:5px;">Licensing</h3>
<p>This theme is licensed under <a href="http://docs.moodle.org/en/License">Moodle\'s GNU General Public License</a>. Feel free to use it, share it and edit it as you see fit. All that I ask is that you give me credit for the work, and do not ever take credit for making it yourself.</p>
<p>Please enjoy the theme.</p>';
