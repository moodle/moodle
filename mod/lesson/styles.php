<style type="text/css">

img {
	border:0;
}

.hidden-label {
	display: none;
}

.footer {
  background-image: url(<?PHP echo "$themeurl"?>/footer.jpg);
  color: <?PHP echo $THEME->body?>;
  padding-top:40px; margin-top:40px;
}
 
.footer a:link, .footer v:link, .footer a:visited { 
	color: <?PHP echo $THEME->body?>; 
}

.leftmenu { <?php //in view.php in lesson mod ?>
	width:140px;
	height:530px;
	margin-top:-18px;
	white-space:nowrap;
}

.leftmenu1 { <?php //in lesson.php in lesson mod ?>
	background: no-repeat url(<?PHP echo "$themeurl"?>/leftmenu.jpg); 
	width:140px;
	height:530px;
	margin-top:-6px;

	white-space:nowrap;
}

.lmlinks {
	width:140px;
	height:400px;
	overflow:auto;
	<?php //white-space:nowrap; ?>
	padding-left:7px;
	padding-top:4px;
	
}


.leftmenu a:link, .leftmenu a:active, .leftmenu a:visited, .leftmenu1 a:link, .leftmenu1 a:active, .leftmenu1 a:visited{
font-size:.7em; 
}

.lmMainlinks a:link, .lmMainlinks a:active, .lmMainlinks a:visited{
font-size:.9em; 
}

.slidepos {
padding-left:153px; margin-top:-480px;
 font-family:Arial;
}

.viewpos {
}

</style>
