<?PHP /*  $Id$ */

/// We use PHP so we can do value substitutions into the styles

    $nomoodlecookie = true;
    require_once("../../config.php");
    $themeurl = style_sheet_setup(filemtime("styles.php"), 300, $themename);


/// From here on it's nearly a normal stylesheet. 
/// First are some CSS definitions for normal tags, 
/// then custom tags follow.
///
/// New classes always get added to the end of the file.
/// 
/// Note that a group of standard colours are all
/// defined in config.php in this directory.  The
/// reason for this is because Moodle uses the same
/// colours to provide oldstyle formatting for
/// browsers without CSS.
/// 
/// You can hardcode colours in this file if you
/// don't care about this. 

?>

body {
	font-family: Arial, Helvetica, sans-serif;
	font-size : 13px;
	margin : 5px;
}

td, th {
 	font-family: Arial, Helvetica, sans-serif;
	font-size : 13px;}

a:link {
    text-decoration: none; 
    color: #0033CC;
}

a:visited {
    text-decoration: none; 
    color:#0033CC;
}

a:hover {
    text-decoration: underline; 
    color: #990000;
}

th {
    font-weight: bold; 
    background-color: <?PHP echo $THEME->cellheading?>;
}

ul {
   margin-bottom: 5px;
   margin-top: 0px;
}

form { 
    margin-bottom: 0;
}

input {
	background : transparent;
	
}

h2 {
	font-size : 16px;
    font-weight: bold;
	background-color: #FEF9F6;
	padding : 3;
	border : 1px solid  <?PHP echo $THEME->borders?>;
}

h4 {
	font-size : 13px;
    font-weight: bold;

}

.highlight {
    background-color: <?PHP echo $THEME->highlight?>;
}

.headingblock {
	font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
	font-size : 11px;
	font-weight: bold;
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
    background-color: #E3DFD4;
}

.navbar {
	font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
	font-size : 11px;
	font-weight: bold;
	background-color: #C6BDA8;
}

.generalbox {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
	background-color: #FEF9F6;
}

.generalboxcontent {

}

.generaltable {
}

.generaltableheader {

}

.generaltablecell {
}

.sideblock {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

.sideblockheading {
	font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
	font-size : 11px;
	font-weight: bold;
    background-color: #E3DFD4;
}

.sideblockmain {
    background-color: #FEF9F6;
}

.sideblocklinks {
    background-color: #FEF9F6;
}

.sideblocklatestnews {
     background-color: #FEF9F6;
}

.sideblockrecentactivity {
    background-color: #FEF9F6;
}

.siteinfo {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

.siteinfocontent {
    
}

.outlineheadingblock {
	font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
	font-size : 11px;
	font-weight: bold;
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

.weeklyoutline {
}

.weeklyoutlineside {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

.weeklyoutlinesidehighlight {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

.weeklyoutlinesidehidden {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: dashed;
}

.weeklyoutlinecontent {
    border-width: 0px;
    border-top: 1px;
    border-bottom: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

.weeklyoutlinecontenthighlight {
    background: #FFFFFF;
    border-width: 0px;
    border-top: 1px;
    border-bottom: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

.weeklyoutlinecontenthidden {
    background: #F7F6F1;
    border-width: 0px;
    border-top: 1px;
    border-bottom: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: dashed;
}

.weeklydatetext {
	font-weight: bold; 
}

.topicsoutline {
}

.topicsoutlineside {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

.topicsoutlinesidehighlight {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

.topicsoutlinesidehidden {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: dashed;
}

.topicsoutlinecontent {
    border-width: 0px;
    border-top: 1px;
    border-bottom: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

.topicsoutlinecontenthighlight {
    background: #FFFFFF;
    border-width: 0px;
    border-top: 1px;
    border-bottom: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

.topicsoutlinecontenthidden {
    background-color: #F7F6F1;
    border-width: 0px;
    border-top: 1px;
    border-bottom: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: dashed;
}

.forumpost {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

.forumpostpicture {
    background-color: #C6BDA8;
}

.forumpostside {
    background-color: #E3DFD4;
}

.forumpostmessage {
}


.siteinfo {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

.siteinfocontent {
		background-color: #E3DFD4;
}


.generalbox {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

.generalboxcontent {
    background-image: none;
    background-color: <?PHP echo $THEME->cellcontent?>;
}

.noticebox {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

.noticeboxcontent {
    text-align: center;
}


.feedbacktext {
    color: <?PHP echo $THEME->cellheading2?>;
}

a.dimmed:link {
    text-decoration: none; 
    color: #AAAAAA;
}

a.dimmed:visited {
    text-decoration: none;
    color: #AAAAAA;
}

a.dimmed:hover {
    text-decoration: underline;
    color: #990000;
}
.dimmed_text {
    color: #AAAAAA;
}

.forumpostheader {
  
}

.forumpostheadertopic {
   	font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
    background-color: #C6BDA8
}

.forumpostheaderpicture {
		background-color: #FEF9F6;
}

.forumpostheadername {
	background-color: #FEF9F6;
}

.forumpostheaderreplies {

}

.forumpostheaderdate {
 	background-color: #FEF9F6;
}

.logininfo {
   	font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
	font-size : 10px;
}

.homelink {
    font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
	font-size : 11px;
}

.teacheronly {
    color: #990000;
}

.header {
	background-color: #E3DFD4;
}

.headermain {
    font-weight: bold;
}

.headermenu {
}

.headerhome {
		background-color: #E3DFD4;
}

.headerhomemain {
    font-weight: bold;
}

.headerhomemenu {
}

.categorybox {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

.categoryboxcontent {
}

.categoryname {
    font-weight: bold;
}

.coursename {
}

.coursebox {
}

.courseboxcontent {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

.courseboxinfo {
}

.courseboxsummary {
}

a.autolink:link {
    text-decoration: none;
    color: #000000;
    background-color: <?PHP echo $THEME->autolink?>;
}

a.autolink:visited {
    text-decoration: none;
    color: #000000;
    background-color: <?PHP echo $THEME->autolink?>;
}

a.autolink:hover {
    text-decoration: underline;
    color: #990000;
}

.userinfobox {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
    margin-bottom: 5px;
}

.userinfoboxside {
}

.userinfoboxcontent {
}

.userinfoboxsummary {
}

.userinfoboxlinkcontent {
}

.generaltab {
}

.generaltabselected {
}


.forumheaderlist {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

.smallinfo {
}

.smallinfohead {
    color: #555555;
}

.tabledivider {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
    border-left: 0px;
    border-right: 0px;
    border-top: 0px;
}

