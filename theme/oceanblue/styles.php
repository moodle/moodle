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





a:link    {font-family: "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif; 

           text-decoration: none; 

           color: 0000CC;}

a:visited {font-family: "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif; 

           text-decoration: none; 

           color: 0000CC;}

a:hover   {font-family: "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif; 

           text-decoration: none; 

           color: FF0000;}



body {font-family: "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;}



p    {font-family: "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;}



h1   {font-family: "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;}



h2   {font-family: "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;}



h3   {font-family: "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;}



h4   {font-family: "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;}



th   {font-family: "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif; 

      font-weight: bold; 

      background-color: <?PHP echo $THEME->cellheading?>;}



td   {font-family: "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;}



li   {font-family: "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;}



form { margin-bottom: 0; }



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



.generalbox {

    border-width: 1px;

    border-color: <?PHP echo $THEME->borders?>;

    border-style: solid;

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

.logininfo {
    font-size: x-small;
}

.homelink {
    font-size: x-small;
}

.teacheronly {
    color: "#990000";
}
