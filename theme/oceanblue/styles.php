<?PHP /*  $Id$ */

/// We use PHP so we can do value substitutions into the styles

    $nomoodlecookie = true;
    require_once("../../config.php");
    if (!isset($themename)) {
        $themename = NULL;
    }

    if (isset($localconfig)) {
        unset($THEME);
        include('config.php');
    }
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


body, td, th, li {
    font-family: "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;
}

th {
    font-weight: normal;
    background-color: <?PHP echo $THEME->cellheading?>;
    background-image: url(<?PHP echo "$themeurl"?>/gradient.jpg);
}

a:link {
    text-decoration: none;
    color: #0000CC;
}

a:visited {
    text-decoration: none;
    color: #0000CC;
}

a:hover {
    text-decoration: underline;
    color: #FF0000;
}

form {
    margin-bottom: 0;
}





.highlight {
    background-color: <?PHP echo $THEME->highlight?>;
}

.headingblock {
    background-image: url(<?PHP echo "$themeurl"?>/gradient.jpg);
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

.navbar {
    background-image: url(<?PHP echo "$themeurl"?>/gradient.jpg);
}

.generaltable {
}

.generaltableheader {
    background-image: url(<?PHP echo "$themeurl"?>/gradient.jpg);
}

.generaltablecell {
}

.sideblock {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

.sideblockheading {
    background-image: url(<?PHP echo "$themeurl"?>/gradient.jpg);
}

.sideblocklinks {
}

.outlineheadingblock {
    background-image: url(<?PHP echo "$themeurl"?>/gradient.jpg);
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

.forumpost {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

.forumpostpicture {
}

.weeklyoutline {
}

.weeklyoutlineside {
}

.weeklyoutlinesidehighlight {
}

.weeklyoutlinesidehidden {
    background-color: <?PHP echo $THEME->hidden?>;
}

.weeklyoutlinecontent {
    border-color: <?PHP echo $THEME->cellheading ?>;
    border-style: solid;
    border-width: 1px;
    border-left: 0px;
    border-right: 0px;
}

.weeklyoutlinecontenthighlight {
    border-color: <?PHP echo $THEME->cellheading2 ?>;
    border-style: solid;
    border-width: 1px;
    border-left: 0px;
    border-right: 0px;
}

.weeklyoutlinecontenthidden {
    border-color: <?PHP echo $THEME->hidden ?>;
    border-style: solid;
    border-width: 1px;
    border-left: 0px;
    border-right: 0px;
}

.weeklydatetext {
    font-size: medium;
    font-weight: normal;
    color: <?PHP echo $THEME->cellheading2?>;
}

.topicsoutline {
}

.topicsoutlineside {
}

.topicsoutlinesidehighlight {
}

.topicsoutlinesidehidden {
    background-color: <?PHP echo $THEME->hidden?>;
}

.topicsoutlinecontent {
    border-color: <?PHP echo $THEME->cellheading ?>;
    border-style: solid;
    border-width: 1px;
    border-left: 0px;
    border-right: 0px;
}

.topicsoutlinecontenthighlight {
    border-color: <?PHP echo $THEME->cellheading2 ?>;
    border-style: solid;
    border-width: 1px;
    border-left: 0px;
    border-right: 0px;
}

.topicsoutlinecontenthidden {
    border-color: <?PHP echo $THEME->hidden ?>;
    border-style: solid;
    border-width: 1px;
    border-left: 0px;
    border-right: 0px;
}

.siteinfo {
}

.siteinfocontent {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
    padding: 10px;
}


.generalbox {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
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
    color: <?PHP echo $THEME->hidden?>;
}

a.dimmed:visited {
    text-decoration: none;
    color: <?PHP echo $THEME->hidden?>;
}

a.dimmed:hover {
    text-decoration: underline;
    color: red;
}

.dimmed_text {
    color: #666666;
}

.forumpostheader {
}

.forumpostheadertopic {
}

.forumpostheaderpicture {
}

.forumpostheadername {
    font-size: small;
}

.forumpostheaderreplies {
    font-size: small;
}

.forumpostheaderdate {
    font-size: small;
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

.header {
}

.headermain {
    font-size: large;
    font-weight: normal;
}

.headermenu {
}

.headerhome {
}

.headerhomemain {
    font-size: x-large;
    font-weight: normal;
}

.headerhomemenu {
}

.categorybox {
}

.categoryboxcontent {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

.categoryname {
    font-size: larger;
    font-weight: normal;
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
    font-size: small;
}

a.autolink:link {
    text-decoration: none;
    color: #555555;
    background-color: <?PHP echo $THEME->autolink?>;
}

a.autolink:visited {
    text-decoration: none;
    color: #555555;
    background-color: <?PHP echo $THEME->autolink?>;
}

a.autolink:hover {
    text-decoration: underline;
    color: red;
}

.userinfobox {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
    margin-bottom: 5px;
}

.userinfoboxsummary {
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
    font-size: .7em;
    margin: 0px;
    padding: 2px;
}

TABLE.calendarmonth {
	width: 100%;
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
    margin: 0px;
    padding: 2px;
}

TABLE.calendarmini TBODY TD {
    text-align: center;
    vertical-align: center;
    width: 14%;
    border-width: 2px;
    border-color: <?PHP echo $THEME->cellcontent2?>;
    border-style: solid;
}

TABLE.calendarmonth TBODY TD {
    width: 14%;
    border-width: 2px;
    border-color: <?PHP echo $THEME->cellcontent2?>;
    border-style: solid;
    vertical-align: top;
}

.cal_event_global {
	background-color: #99cc99;
	border: 2px #99cc99 solid !important;
}

.cal_event_course {
	background-color: #ff9966;
	border: 2px #ff9966 solid !important;
}

.cal_event_group {
	background-color: orange;
	border: 2px orange solid !important;
}

.cal_event_user {
	background-color: #ffcc99;
	border: 2px #ffcc99 solid !important;
}

.cal_duration_global {
	border-top: 2px #99cc99 solid !important;
	border-bottom: 2px #99cc99 solid !important;
}

.cal_duration_course {
	border-top: 2px #ff9966 solid !important;
	border-bottom: 2px #ff9966 solid !important;
}

.cal_duration_user {
	border-top: 2px #ffcc99 solid !important;
	border-bottom: 2px #ffcc99 solid !important;
}

.cal_duration_group {
	border-top: 2px orange solid !important;
	border-bottom: 2px orange solid !important;
}

.cal_weekend {
    color: red;
}

.cal_today {
	border: 2px black solid !important;
}

TABLE.calendarmonth TBODY TD TABLE {
	margin-top: 0px;
	margin-left: 0px;
	font-size: 0.75em;
	line-height: 1.2em;
}

TABLE.calendarmonth TBODY TD TABLE TD {
    border: none;
}

TABLE.calendarmonth TBODY TD DIV {
	margin-top: 0px;
	margin-left: 0px;
	font-size: 0.75em;
	line-height: 1.2em;
}

TABLE.calendarmini THEAD TD {
    font-size: .95em;
    text-align: center;
    vertical-align: center;
}

TABLE.calendarmonth THEAD TD {
	text-align: center;
    vertical-align: center;
    padding-bottom: 3px;
    border-bottom: 2px <?PHP echo $THEME->borders?> solid;
}

.sideblockmain .cal_event {
	font-size: 0.8em;
	font-weight: normal;
}
.sideblockmain .cal_event_date {
	font-size: 0.6em;
}

.mycalendar .cal_event {
	font-weight: normal;
}
.mycalendar .cal_event_date {
	font-size: 0.8em;
}

.mycalendar TABLE.cal_filters {
	width: 100%;
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
    margin: 0px;
    padding: 2px;
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
}

.mycalendar .cal_event_table THEAD {
	background-color: <?PHP echo $THEME->cellcontent?>;
	text-align: center;
	font-weight: normal;
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
	font-weight: normal;
}

.calendarreferer {
	font-weight: normal;
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
    border-top: 2px <?php echo $THEME->cellcontent2 ?> solid;
    border-left: 2px <?php echo $THEME->cellcontent2 ?> solid;
    border-right: 2px <?php echo $THEME->cellheading2 ?> solid;
    border-bottom: 2px <?php echo $THEME->cellheading2 ?> solid;
    background-image: url(<?PHP echo "$themeurl"?>/gradient.jpg);
    padding: 0px;
    margin: 0px;
}

.cal_popup_caption {
    font-size: 75%;
    font-weight: normal;
    font-family: sans-serif;
}

.cal_popup_close {
	font-size: 75%;
	font-weight: normal;
    font-family: sans-serif;
	margin-right: 5px;
}

.sideblock .cal_controls {
	text-align: center;
	font-size: 9px;
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
}

.eventfullheader {
}

.eventfullpicture {
    padding:8px;
}

#block_course_summary .sideblockmain {
    padding: 10px;
}

.sitetopic {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

