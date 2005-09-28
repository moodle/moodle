/***
 *** Hide Label Class
 ***/

.mod-lesson .hidden-label {
  display: none;
}

/***
 *** Left Menu Styles
 ***/

.mod-lesson .leftmenutable {
  width:170px;
  height:400px;
  overflow:auto;    
  vertical-align:top;
}

.mod-lesson .leftmenu a:link, 
.mod-lesson .leftmenu a:active, 
.mod-lesson .leftmenu a:visited, 
.mod-lesson .leftmenu1 a:link, 
.mod-lesson .leftmenu1 a:active, 
.mod-lesson .leftmenu1 a:visited{
  font-size:.7em;
  vertical-align:top;
}

.mod-lesson .main a:link, 
.mod-lesson .main a:active,
.mod-lesson .main a:visited{
  font-size:.9em; 
  vertical-align:top;
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