<?PHP // $Id$

/// We use PHP so we can do value substitutions into the styles

    require_once("../../config.php"); 

    if (isset($themename)) {
        $CFG->theme = $themename;
    }

    $themeurl = "$CFG->wwwroot/theme/$CFG->theme";

/// From here on it's nearly a normal stylesheet.
/// First are some CSS definitions for normal tags, 
/// then custom tags follow.
///
/// Note that colours are all defined in config.php
/// in this directory

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
}

.weeklyoutlinesidehighlight {
    background-image: url(<?PHP echo "$themeurl"?>/texture3.jpg);
}

.weeklyoutlinecontent {
}

.topicsoutline {
}

.topicsoutlineside {
    background-image: url(<?PHP echo "$themeurl"?>/texture2.jpg);
}

.topicsoutlinesidehighlight {
    background-image: url(<?PHP echo "$themeurl"?>/texture3.jpg);
}

.topicsoutlinecontent {
}

.forumpost {
    border-width: 1px;
    border-color: <?PHP echo $THEME->borders?>;
    border-style: solid;
}

.forumpostheader {
    background-image: url(<?PHP echo "$themeurl"?>/texture2.jpg);
}

.forumpostheadertopic {
    background-image: url(<?PHP echo "$themeurl"?>/texture3.jpg);
}

.forumpostpicture {
    background-image: url(<?PHP echo "$themeurl"?>/texture2.jpg);
}

.forumpostside {
    background-image: url(<?PHP echo "$themeurl"?>/texture2.jpg);
}

.forumpostmessage {
}
