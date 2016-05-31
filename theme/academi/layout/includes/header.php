<?php global $CFG; ?>
<header id="header">
	<div class="header-top">
    <div class="navbar">
      <div class="navbar-inner">
        <div class="container-fluid">
          <a data-target=".navbar-responsive-collapse" data-toggle="collapse" class="btn btn-navbar">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="<?php echo $CFG->wwwroot;?>"><?php echo format_string($SITE->shortname, true, array('context' => context_course::instance(SITEID))); ?></a>
            <?php if($CFG->branch > "27"): ?>
										       <?php echo $OUTPUT->user_menu(); ?>
             <?php endif; ?>   
          <div class="nav-collapse collapse navbar-responsive-collapse">
													<?php echo $OUTPUT->custom_menu(); ?>
            <ul class="nav pull-right">
                <li><?php echo $OUTPUT->page_heading_menu(); ?></li>
                 <?php if($CFG->branch < "28"): ?>
                   <li class="navbar-text"><?php echo $OUTPUT->login_info() ?></li>
                 <?php endif; ?>   
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="header-main">
  	<div class="container-fluid">
    	<div class="row-fluid">
      	<div class="span5">
        	<div class="logo"><a href="<?php echo $CFG->wwwroot;?>"><img src="<?php echo get_logo_url(); ?>" width="243" height="77" alt="Academi"></a></div>
        </div>
      	<div class="span7">
        	<div class="infoarea">          	
            <span><i class="fa fa-phone"></i>Call Us: <?php echo theme_academi_get_setting('phoneno'); ?></span>
            <span><i class="fa fa-envelope-o"></i>Email: <?php echo theme_academi_get_setting('emailid'); ?></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>
<!--E.O.Header-->