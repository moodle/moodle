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
    font-family: "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;
    background-image: url(<?PHP echo "$themeurl"?>/texture1.jpg);
}

td, th {
    font-family: "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;
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



.highlight {
    background-color: <?PHP echo $THEME->highlight?>;
}

.headingblock {
    background-image: url(<?PHP echo "$themeurl"?>/texture3.jpg);
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

.navbar {
    background-image: url(<?PHP echo "$themeurl"?>/texture3.jpg);
}

.generalbox {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
    background-image: url(<?PHP echo "$themeurl"?>/texture2.jpg);
}

.generalboxcontent {
    background-image: url(<?PHP echo "$themeurl"?>/texture2.jpg);
}

.generaltable {
}

.generaltableheader {
    background-image: url(<?PHP echo "$themeurl"?>/texture3.jpg);
}

.generaltablecell {
}

.sideblock {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

.sideblockheading {
    background-image: url(<?PHP echo "$themeurl"?>/texture3.jpg);
}

.sideblockmain {
    background-image: url(<?PHP echo "$themeurl"?>/texture2.jpg);
}

.sideblocklinks {
    background-image: url(<?PHP echo "$themeurl"?>/texture2.jpg);
}

.sideblocklatestnews {
    background-image: url(<?PHP echo "$themeurl"?>/texture2.jpg);
}

.sideblockrecentactivity {
    background-image: url(<?PHP echo "$themeurl"?>/texture2.jpg);
}

.siteinfo {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

.siteinfocontent {
    background-image: url(<?PHP echo "$themeurl"?>/texture2.jpg);
}

.outlineheadingblock {
    background-image: url(<?PHP echo "$themeurl"?>/texture3.jpg);
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

.weeklyoutline {
}

.weeklyoutlineside {
    background-image: url(<?PHP echo "$themeurl"?>/texture2.jpg);
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

.weeklyoutlinesidehighlight {
    background-image: url(<?PHP echo "$themeurl"?>/texture3.jpg);
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
    background: #EEFAFF;
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
    background: #EEFAFF;
    border-width: 0px;
    border-top: 1px;
    border-bottom: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: dashed;
}

.weeklydatetext {
    font-size: medium;
    font-weight: bold; 
    color: <?PHP echo $THEME->cellheading2?>;
}

.topicsoutline {
}

.topicsoutlineside {
    background-image: url(<?PHP echo "$themeurl"?>/texture2.jpg);
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

.topicsoutlinesidehighlight {
    background-image: url(<?PHP echo "$themeurl"?>/texture3.jpg);
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
    background: #EEFAFF;
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
    background: #EEFAFF;
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
    background-image: url(<?PHP echo "$themeurl"?>/texture2.jpg);
}

.forumpostside {
    background-image: url(<?PHP echo "$themeurl"?>/texture2.jpg);
}

.forumpostmessage {
}


.siteinfo {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

.siteinfocontent {
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
    color: red;
}
.dimmed_text {
    color: #AAAAAA;
}

.forumpostheader {
    background-image: url(<?PHP echo "$themeurl"?>/texture2.jpg);
}

.forumpostheadertopic {
    background-image: url(<?PHP echo "$themeurl"?>/texture3.jpg);
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
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

.categoryboxcontent {
}

.categoryname {
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

