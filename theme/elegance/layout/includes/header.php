<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package   theme_enlight
 * @copyright 2015 Nephzat Dev Team,nephzat.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$surl = new moodle_url('/course/search.php');
$courserenderer = $PAGE->get_renderer('core', 'course');
$tcmenu = $courserenderer->top_course_menu();
$cmenuhide = theme_enlight_get_setting('cmenuhide');
?>
<header id="site-header">

	<div class="header-top">
    <div class="navbar">
      <div class="navbar-inner">
        <div class="container-fluid">
          <a data-target="#site-custom-menu" data-toggle="collapse" class="btn btn-navbar">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a href="javascript:void(0);" class="brand" style="display: none;">Enlight</a>
          <div id="site-custom-menu" class="nav-collapse collapse">
            <?php echo $OUTPUT->custom_menu(); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="header-main">
    <div class="navbar">
      <div class="navbar-inner">
        <div id="sgkk" class="container-fluid">
          <a class="btn btn-navbar" data-toggle="collapse" data-target="#site-user-menu">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="<?php echo $CFG->wwwroot;?>">
          <img src="<?php echo theme_enlight_get_logo_url(); ?>" alt="Enlight"></a>
          <div class="clearfix hidden-desktop"></div>
          <div id="site-user-menu" class="nav-collapse collapse">

            <ul class="nav pull-right">
              <li><a href="<?php echo $CFG->wwwroot;?>"><?php echo get_string('home'); ?><span class="caretup"></span></a></li>
<?php
if (!$cmenuhide) {
?>
              <li class="dropdown hidden-desktop">
                <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
				<?php echo get_string('courses'); ?><i class="fa fa-chevron-down"></i><span class="caretup"></span></a>
                 <?php echo $tcmenu['topmmenu']; ?>
              </li>
              <li class="visible-desktop" id="cr_link">
              <a href="<?php echo new moodle_url('/course/index.php'); ?>" ><?php echo get_string('courses'); ?>
              <i class="fa fa-chevron-down"></i><span class="caretup"></span></a>
                <?php echo $tcmenu['topcmenu']; ?>
              </li>
<?php
} else {
    echo '<li><a href="'.new moodle_url('/course/index.php').'">'.get_string('courses').'</a></li>'."\n";
}
?>
			 <?php  echo $OUTPUT->user_menu(); ?>
            </ul>
          </div>
        </div>
      </div>
    </div>
    
   
    
  </div>

</header>
<!--E.O.Header-->

<script>
 $(function(){
<?php
if (right_to_left()) {
?>
 
  var w =  $(".header-main #sgkk").width();
   var win = $(window).width();
   if(win>=980)
   {
	   var ul_w =  $(".header-main #site-user-menu ul").width();
	   var le = ( w-ul_w );
	   $('#cr_menu').css({"width":w+'px' , "right": '-'+le+'px' });
   }
   
   	$(window).resize(function(){
	   var w =  $(".header-main #sgkk").width();
	   var win = $(window).width();
	   if(win>=980)
	   {
		   var ul_w =  $(".header-main #site-user-menu ul").width();
		   var le = ( w-ul_w );
		   $('#cr_menu').css({"width":w+'px' , "right": '-'+le+'px' });
	   }
	});
 
<?php
} else {
?>
   var w =  $(".header-main #sgkk").width();
   var win = $(window).width();
   if(win>=980)
   {
	   var ul_w =  $(".header-main #site-user-menu ul").width();
	   var le = ( w-ul_w );
	   $('#cr_menu').css({"width":w+'px' , "left": '-'+le+'px' });
   }
   
   	$(window).resize(function(){
	   var w =  $(".header-main #sgkk").width();
	   var win = $(window).width();
	   if(win>=980)
	   {
		   var ul_w =  $(".header-main #site-user-menu ul").width();
		   var le = ( w-ul_w );
		   $('#cr_menu').css({"width":w+'px' , "left": '-'+le+'px' });
	   }
	});
	
<?php
}
?>	
 
	$("#cr_link").mouseenter(function() {
	   $("#cr_link").addClass("active");	 
   	  // $('#cr_menu').stop(true, true).show(400);
	   $('#cr_menu').show();
	});
	
	$("#cr_link").mouseleave(function() {
		$("#cr_link").removeClass("active");
		// $('#cr_menu').stop(true, true).hide(400);
		$('#cr_menu').hide();
	});
	
 });
</script>
