<?PHP /*  $Id$ */

/// We use PHP so we can do value substitutions into the styles

    if (!isset($themename)) {
        $themename = NULL;
    }

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

th {
    font-weight: bold;
    background-color: <?PHP echo $THEME->cellheading?>;
}

ul {
   margin-bottom: 5px;
   margin-top: 0px;
}

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
    -moz-border-radius-bottomleft: 20px;
    -moz-border-radius-bottomright: 20px;
}

.sideblockheading {
	font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
	font-size : 11px;
	font-weight: bold;
    background-color: #E3DFD4;
}

.sideblockmain {
    background-color: #FEF9F6;
    -moz-border-radius-bottomleft: 20px;
    -moz-border-radius-bottomright: 20px;
}

.sideblocklinks {
    background-color: #FEF9F6;
}

.sideblocklatestnews {
     background-color: #FEF9F6;
    -moz-border-radius-bottomleft: 20px;
    -moz-border-radius-bottomright: 20px;
}

.sideblockrecentactivity {
    background-color: #FEF9F6;
    -moz-border-radius-bottomleft: 20px;
    -moz-border-radius-bottomright: 20px;
}

.outlineheadingblock {
	font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
	font-size : 11px;
	font-weight: bold;
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
    -moz-border-radius: 3px;
}

.forumpost {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
    -moz-border-radius-bottomleft: 20px;
    -moz-border-radius-bottomright: 20px;
}

.forumpostpicture {
	    background-color: #C6BDA8;
}

.forumpostside {
    background-color: #E3DFD4;
    -moz-border-radius-bottomleft: 20px;
}

.forumpostmessage {
    -moz-border-radius-bottomright: 20px;
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

.siteinfo {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

.siteinfocontent {
	 background-color: #FEF9F6;
    -moz-border-radius: 20px;

}


.generalbox {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
	background-color: #FEF9F6;
    -moz-border-radius-topleft: 3px;
    -moz-border-radius-topright: 3px;
    -moz-border-radius-bottomleft: 15px;
    -moz-border-radius-bottomright: 15px;
}

.generalboxcontent {
    background-image: none;
    background-color: <?PHP echo $THEME->cellcontent?>;
    -moz-border-radius-topleft: 3px;
    -moz-border-radius-topright: 3px;
    -moz-border-radius-bottomleft: 15px;
    -moz-border-radius-bottomright: 15px;
}

.noticebox {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
    -moz-border-radius: 3px;
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

.categorynumber {
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
    -moz-border-radius: 20px;
}

.courseboxinfo {
	font-size : 11px;
	font-weight: bold;
}

.courseboxsummary {
}

a.autolink:link {
    text-decoration: none;
    color: #000000;
    background-color: <?PHP echo $THEME->autolink?>;
	border-bottom: dashed 1px #000000;
	cursor: help;
}

a.autolink:visited {
    text-decoration: none;
    color: #000000;
    background-color: <?PHP echo $THEME->autolink?>;
	border-bottom: dashed 1px #000000;
	cursor: help;
}

a.autolink:hover {
    text-decoration: none;
	border-bottom: solid 1px #000000;
    color: #000000;
	cursor: help;
}

.userinfobox {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
    -moz-border-radius-bottomleft: 20px;
    -moz-border-radius-bottomright: 20px;
    margin-bottom: 5px;
}

.userinfoboxside {
    -moz-border-radius-bottomleft: 20px;
}

.userinfoboxcontent {
    -moz-border-radius-bottomright: 20px;
}

.userinfoboxsummary {
}

.userinfoboxlinkcontent {
    -moz-border-radius-bottomright: 20px;
}

.generaltab {
    -moz-border-radius-topleft: 15px;
    -moz-border-radius-topright: 15px;
}

.generaltabselected {
    -moz-border-radius-topleft: 15px;
    -moz-border-radius-topright: 15px;
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

.headingblockcontent {
}




TABLE.calendarmini {
	width: 100%;
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
	font-size : 10px;
    margin: 0px;
    padding: 2px;
    -moz-border-radius: 10px;
}

TABLE.calendarmonth {
	width: 100%;
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
    margin: 0px;
    padding: 0px;
    -moz-border-radius: 10px;
}

TABLE.calendarmini TBODY TD {
    text-align: center;
    vertical-align: center;
    width: 14%;
   border-width: 1px;
    border-color: <?PHP echo $THEME->cellcontent2?>;
    border-style: solid;
    -moz-border-radius: 4px;
}

TABLE.calendarmonth TBODY TD {
    width: 14%;
    border-width: 1px;
    border-color: <?PHP echo $THEME->cellcontent2?>;
    border-style: solid;
    vertical-align: top;
    background-color: <?PHP echo $THEME->body?>;
}

TABLE.calendarmonth TBODY TD TABLE {
	margin-top: 0px;
	margin-left: 0px;
	font-size : 10px;
	line-height: 1.2em;
}

TABLE.calendarmonth TBODY TD TABLE TD {
    border: none;
    background: none;
}

TABLE.calendarmonth TBODY TD DIV {
	margin-top: 0px;
	margin-left: 0px;
	font-size: 10px;
	line-height: 1.2em;
}

TABLE.calendarmini THEAD TD {
    font-size: 10px;
	font-weight: bold;
    text-align: center;
    vertical-align: center;
}

TABLE.calendarmonth THEAD TD {
	text-align: center;
    vertical-align: center;
	font-weight: bold;
    padding-bottom: 3px;
    border-bottom: 2px <?PHP echo $THEME->borders?> solid;
    background-color: <?PHP echo $THEME->body?>;
}

.sideblockmain .cal_event {

}
.sideblockmain .cal_event_date {

}

.cal_event_global {
	background-color: #009999 !important;
	border: 2px #009999 solid !important;
}

.cal_event_course {
	background-color: #ff3333 !important;
	border: 2px #ff3333 solid !important;
}

.cal_event_group {
	background-color: #ffcc33 !important;
	border: 2px #ffcc33 solid !important;
}

.cal_event_user {
	background-color: #99ccff !important;
	border: 2px #99ccff solid !important;
}

.cal_duration_global {
	border-top: 2px #009999 solid !important;
	border-bottom: 2px #009999 solid !important;
}

.cal_duration_course {
	border-top: 2px #ff3333 solid !important;
	border-bottom: 2px #ff3333 solid !important;
}

.cal_duration_user {
	border-top: 2px #99ccff solid !important;
	border-bottom: 2px #99ccff solid !important;
}

.cal_duration_group {
	border-top: 2px #ffcc33 solid !important;
	border-bottom: 2px #ffcc33 solid !important;
}

.cal_weekend {
    color: red;
}

.cal_today {
	border: 2px black solid !important;
}

.mycalendar {
	background-color: <?PHP echo $THEME->cellcontent?>;
	-moz-border-radius-bottomleft: 20px;
    -moz-border-radius-bottomright: 20px;
}

.mycalendar .cal_event {
	font-weight: bold;
}
.mycalendar .cal_event_date {
	font-size: 10px;
}

.mycalendar TABLE.cal_filters {
	width: 100%;
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
    margin: 0px;
    padding: 2px;
    -moz-border-radius: 10px;
}

.mycalendar .cal_filters THEAD TD {
	border-bottom: 2px <?PHP echo $THEME->borders?> solid;
    margin: 0px;
    padding: 2px;
}


.mycalendar .cal_event_table {
	width: 100%;
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
    margin: 0px;
    padding: 2px;
    -moz-border-radius: 10px;
}

.mycalendar .cal_event_table THEAD {
	background-color: <?PHP echo $THEME->cellcontent?>;
	text-align: center;
	font-weight: bold;
}

.sideblockmain HR {
	height: 1px;
	border: none;
	border-top: 1px #999 solid;
	margin-top: 4px;
	margin-bottom: 4px;
}

.mycalendar HR {
	height: 1px;
	border: none;
	border-top: 1px #999 solid;
	margin-top: 4px;
	margin-bottom: 4px;
}

.calendarexpired {
	color: red;
	font-weight: bold;
}

.calendarreferer {
	font-weight: bold;
}

TD.cal_event_description {
	width: 80%;
	border-left: 2px <?php echo $THEME->borders?> solid;
	vertical-align: top;
	padding: 5px;
}

.cal_popup_fg {
	background-color: <?php echo $THEME->cellcontent?>;
}

.cal_popup_bg {
	border-top: 2px #C6BDA8 solid;
	border-left: 2px #C6BDA8 solid;
	border-right: 2px #663300 solid;
	border-bottom: 2px #663300 solid;
	background-color: #E3DFD4;
	padding: 0px;
	margin: 0px;
}

.cal_popup_caption {
	background-color: #E3DFD4;
	font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
	font-size: 10px;
	font-weight: bold;
}


.cal_popup_close {
	font-size: 75%;
	font-weight: bold;
	font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
	margin-right: 5px;
}

.sideblock .cal_controls {
	font-size: 8px;
}

A IMG {
	border: none;
}

TABLE.formtable TD {
	padding: 9px;
}



.eventfull {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
    -moz-border-radius-bottomleft: 15px;
    -moz-border-radius-bottomright: 15px;
}

.eventfullheader {
}

.eventfullpicture {
    padding:8px;
}

.eventfullside {
    -moz-border-radius-bottomleft: 15px;
}

.eventfullmessage {
    -moz-border-radius-bottomright: 15px;
}


