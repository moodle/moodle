/***
 *** Style for page contents (display to student)
 ***/
 
.mod-lesson .contents {
    text-align: left;
}

/***
 *** Slide show Style
 ***/

/* NOTE: background color, height and width are set in the lesson settings */
.mod-lesson .slideshow {  
    overflow: auto;
    padding-right: 16px; /* for the benefit of macIE5 only */ 
    /* \ commented backslash hack - recover from macIE5 workarounds, it will ignore the following rule */
    padding-right: 0;
    padding: 15px;
}

/***
 *** Hide Label Class
 ***/

.mod-lesson .hidden-label {
  display: none;
}

/***
 *** Left Menu Styles
 ***/

.mod-lesson .leftmenu_container {
  width:170px;
  height:400px;
  overflow:auto;
  vertical-align:top;
  padding-bottom: 15px;  /* for the sake of MacIE5 only */
}
.mod-lesson .leftmenu_title {
}
.mod-lesson .leftmenu_courselink {
    font-size:.9em;
}
.mod-lesson .leftmenu_links {
    font-size:.85em;
    font-style: none;
}
.mod-lesson .leftmenu_links ul {
    list-style-type: none;
    margin: 0px;
    padding: 0px;
    padding-top: 5px;
    padding-left: 10px;
}

.mod-lesson .leftmenu_selected_link {
    
}

.mod-lesson .leftmenu_not_selected_link {
    
}


/***
 *** Lesson Buttons
 ***/

.mod-lesson .lessonbutton a {
  padding-left:1em;
  padding-right:1em;
}

.mod-lesson .lessonbutton a:link,
.mod-lesson .lessonbutton a:visited, 
.mod-lesson .lessonbutton a:hover {
    color: #000;
    text-decoration: none;
}

.mod-lesson .lessonbutton a:link,
.mod-lesson .lessonbutton a:visited {
  border-top: 1px solid #cecece;
  border-bottom: 2px solid #4a4a4a;
  border-left: 1px solid #cecece;
  border-right: 2px solid #4a4a4a;
}

.mod-lesson .lessonbutton a:hover {
  border-bottom: 1px solid #cecece;
  border-top: 2px solid #4a4a4a;
  border-right: 1px solid #cecece;
  border-left: 2px solid #4a4a4a;
}


/***
 ***  Use these to override lessonbutton class
 ***  Or just comment out all of lessonbutton class definitions and start from scratch below
 ***/

/* for branch tables only */
.mod-lesson .previousbutton {
}

/* for branch tables only */
.mod-lesson .nextbutton {
}

/* All other buttons */
.mod-lesson .standardbutton {
}

/*  branchbuttoncontainer wraps around branch table buttons */
.mod-lesson .branchbuttoncontainer {
    text-align: center;
}

/*  branchslidetop and branchslidebottom classes are wrapped around the branch buttons (branchslidetop around 
    top ones and branchslidebottom around the bottom ones)
    that are printed out with the branch table in slide show mode. */
.mod-lesson .branchslidetop {
    /*  You could float them out of the slide
    position: absolute;
    top: 60px;*/
    /*  You may choose to not show them at all
    display: none;*/
}

.mod-lesson .branchslidebottom {
    /*  You may choose to not show them at all
    display: none;*/
}

/***
 *** Lesson Progress Bar
 ***    Default styles for this are very basic right now.
 ***    User is supposed to configure this to their liking (like using pictures)
 ***/

.mod-lesson .progress_bar {
    padding: 20px;
}

.mod-lesson .progress_bar_table {
    width: 80%;
    padding: 0px;
    margin: 0px;
}

.mod-lesson .progress_bar_completed {
    /*  Example Use of Image
    background-image: url(<?php echo $CFG->wwwroot ?>/mod/lesson/completed.gif);
    background-position: center;
    background-repeat: repeat-x;
    */
    background-color: green;
    padding: 0px;
    margin: 0px;    
}

.mod-lesson .progress_bar_todo {
    /*  Example Use of Image
    background-image: url(<?php echo $CFG->wwwroot ?>/mod/lesson/todo.gif);
    background-repeat: repeat-x;
    background-position: center;
    */
    background-color: red;
    text-align: left;
    padding: 0px;
    margin: 0px;
}

.mod-lesson .progress_bar_token {
    /*  Example Use of Image
    background-image: url(<?php echo $CFG->wwwroot ?>/mod/lesson/token.gif);
    background-repeat: repeat-none;
    */
    background-color: #000000;
    height: 20px;
    width: 5px;
    padding: 0px;
    margin: 0px;
}