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

.sideblockmain {
}

.sideblocklinks {
}

.sideblocklatestnews {
}

.sideblockrecentactivity {
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

.forumpostside {
}

.forumpostmessage {
}


.weeklyoutline {
}

.weeklyoutlineside {
}

.weeklyoutlinesidehighlight {
}

.weeklyoutlinecontent {
}

.weeklyoutlinecontenthighlight {
}


.topicsoutline {
}

.topicsoutlineside {
}

.topicsoutlinesidehighlight {
}

.topicsoutlinecontent {
}

.topicsoutlinecontenthighlight {
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

.forumpostheader {
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
