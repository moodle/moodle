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


body, td, th, li {
    font-family: "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;
}

th {
    font-weight: bold; 
    background-color: <?PHP echo $THEME->cellheading?>;
    background-image: url(<?PHP echo "$themeurl"?>/gradient.jpg);
}

a:link {
    text-decoration: none; 
    color: blue;
}

a:visited {
    text-decoration: none; 
    color: blue;
}

a:hover {
    text-decoration: underline; 
    color: red;
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
    -moz-border-radius: 3px;
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
    -moz-border-radius-bottomleft: 20px;
    -moz-border-radius-bottomright: 20px;
}

.sideblockheading {
    background-image: url(<?PHP echo "$themeurl"?>/gradient.jpg);
}

.sideblockmain {
    -moz-border-radius-bottomleft: 20px;
    -moz-border-radius-bottomright: 20px;
}

.sideblocklinks {
}

.sideblocklatestnews {
    -moz-border-radius-bottomleft: 20px;
    -moz-border-radius-bottomright: 20px;
}

.sideblockrecentactivity {
    -moz-border-radius-bottomleft: 20px;
    -moz-border-radius-bottomright: 20px;
}

.outlineheadingblock {
    background-image: url(<?PHP echo "$themeurl"?>/gradient.jpg);
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
}

.forumpostside {
    -moz-border-radius-bottomleft: 20px;
}

.forumpostmessage {
    -moz-border-radius-bottomright: 20px;
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
    font-weight: bold; 
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
    -moz-border-radius: 20px;
    padding: 10px;
}


.generalbox {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
    -moz-border-radius-topleft: 3px;
    -moz-border-radius-topright: 3px;
    -moz-border-radius-bottomleft: 15px;
    -moz-border-radius-bottomright: 15px;
}

.generalboxcontent {
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
    color: #AAAAAA;
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
    font-weight: bold;
}

.headermenu {
}

.headerhome {
}

.headerhomemain {
    font-size: x-large;
    font-weight: bold;
}

.headerhomemenu {
}

.categorybox {
}

.categoryboxcontent {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
    -moz-border-radius: 20px;
}

.categoryname {
    font-size: larger;
    font-weight: bold;
}

.categorynumber {
    font-size: larger;
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
    color: red;
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

