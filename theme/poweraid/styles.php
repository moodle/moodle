<?PHP // $Id$
/*
  Filename: style.php
  Purpose of file: Setting the CSS defines for the theme

  Author of file: Bjarne Varoystrand aka Black Skorpio
  E-mail: webmaster@postnuke-sweden.com
  Web: www.postnuke-sweden.com
  ICQ: 1194177

 ---------------------------------------------------------------------
 LICENSE

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License (GPL)
 as published by the Free Software Foundation; either version 2
 of the License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 To read the license please visit http://www.gnu.org/copyleft/gpl.html
*/

/* We use PHP so we can do value substitutions into the styles */

	require_once("../../config.php"); 

	if (isset($themename)) {
		$CFG->theme = $themename;
	}

	$themeurl = "$CFG->wwwroot/theme/$CFG->theme";

/*
   From here on it's nearly a normal stylesheet.
   First are some CSS definitions for normal tags, 
   then custom tags follow.

   Note that colours are all defined in config.php
   in this directory
*/

?>
a:link {
	color: #333333;
	text-decoration: none;
	font-weight: bold
}
a:visited {
	color: #333333;
	text-decoration: none;
	font-weight: bolder;
}
a:hover {
	color: #000000;
	text-decoration: underline;
	background-color: #CCCCCC;
}
a:active {
	color: #000000;
	text-decoration: underline;
}

body {
	font-family: "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;
	color: #000000;
}
p {
}
h1 {
}
h2 {
}
h3 {
}
h4 {
}
th {
	font-weight: bold;
	background-color: #FFD991;
}
td {
}

li {
}

form {
	margin-bottom: 0;
}

.highlight {
	background-color: #AAFFAA;
}

.headingblock {
	background-image: url(<?PHP echo "$themeurl"?>/images/gradient.jpg);
	border-width: 1px;
	border-color: <?PHP echo $THEME->borders?>;
	border-style: solid;
	font-weight: bold;
}

.navbar {
	background-image: url(<?PHP echo "$themeurl"?>/images/gradient.jpg);
}

.generalbox {
	border-width: 1px;
	border-color: <?PHP echo $THEME->borders?>;
	border-style: solid;
}

.generaltable {
}

.generaltableheader {
	background-image: url(<?PHP echo "$themeurl"?>/images/gradient.jpg);
}

.generaltablecell {
}

.sideblock {
	border-width: 1px;
	border-color: <?PHP echo $THEME->borders?>;
	border-style: solid;
}

.sideblockheading {
	background-image: url(<?PHP echo "$themeurl"?>/images/gradient.jpg);
	font-weight: bold;
}

.sideblockmain {
}

.sideblocklinks {
}

.sideblocklatestnews {
}

.sideblockrecentactivity {
}

.outlineheadingblock {
	background-image: url(<?PHP echo "$themeurl"?>/images/gradient.jpg);
	border-width: 1px;
	border-color: <?PHP echo $THEME->borders?>;
	border-style: solid;
	font-weight: bold;
}

.weeklyoutline {
}

.topicsoutline {
}

.forumpost {
	border-width: 1px;
	border-color: <?PHP echo $THEME->borders?>;
	border-style: solid;
}

.forumpostheader {
	background-image: url(<?PHP echo "$themeurl"?>/images/gradient.jpg);
}

.forumpostheadertopic {
}

.forumpostpicture {
}

.forumpostside {
}

.forumpostmessage {
}

input {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 10px;
	font-weight: bold;
	color: #000000;
	background-color: #999999;
}
select {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 10px;
	font-weight: bold;
	color: #000000;
	background-color: #999999;
}

.logininfo {
    font-size: x-small;
}

.homelink {
    font-size: x-small;
}

.teacheronly {
    color: #990000;
}
