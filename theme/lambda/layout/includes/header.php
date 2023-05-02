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
 *
 * @package   theme_lambda
 * @copyright 2020 redPIthemes
 *
 */

$searchbox = theme_lambda_get_setting('navbar_search_form');
$login_link = theme_lambda_get_setting('login_link');
$login_custom_url = theme_lambda_get_setting('custom_login_link_url');
$login_custom_txt = theme_lambda_get_setting('custom_login_link_txt');
$home_button = theme_lambda_get_setting('home_button');
$shadow_effect = theme_lambda_get_setting('shadow_effect');
$auth_googleoauth2 = theme_lambda_get_setting('auth_googleoauth2');
$haslogo = (!empty($PAGE->theme->settings->logo));
$hasheaderprofilepic = (empty($PAGE->theme->settings->headerprofilepic)) ? false : $PAGE->theme->settings->headerprofilepic;
$moodle_global_search = 0;

$checkuseragent = '';
if (!empty($_SERVER['HTTP_USER_AGENT'])) {
    $checkuseragent = $_SERVER['HTTP_USER_AGENT'];
}
$username = get_string('username');
if (strpos($checkuseragent, 'MSIE 8')) {$username = str_replace("'", "&prime;", $username);}
?>

<?php if($PAGE->theme->settings->socials_position==1) { ?>
    	<div class="container-fluid socials-header"> 
    	<?php require_once(dirname(__FILE__).'/socials.php');?>
        </div>
<?php
} ?>

<header id="page-header" class="clearfix">              	
		<?php 
			if ($PAGE->theme->settings->page_centered_logo==0) {require_once(dirname(__FILE__).'/header_var1.php');}
			else {require_once(dirname(__FILE__).'/header_var2.php');}
		?>               
</header>
<header class="navbar">
    <nav class="navbar-inner">
        <div class="lambda-custom-menu container-fluid">
            <?php
                if ($home_button == 'shortname') {
					$shortname = format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]);
                    $home_button_string = '<a class="brand" href="'.$CFG->wwwroot.'">'.$shortname.'</a>'; 
                }
                else if ($home_button == 'home') { 
                    $home_button_string = '<a class="brand" href="'.$CFG->wwwroot.'">'.get_string('home').'</a>'; 
                }
                else if ($home_button == 'frontpagedashboard') { 
                    if (isloggedin()) {
                        $home_button_string = '<a class="brand" href="'.$CFG->wwwroot.'">'.get_string('mymoodle', 'admin').'</a>'; 
                    }
                    else {
                        $home_button_string = '<a class="brand" href="'.$CFG->wwwroot.'">'.get_string('frontpage', 'admin').'</a>'; 
                    }
                }
                else { // Fallback, should not happen
                    $home_button_string = '<a class="brand" href="'.$CFG->wwwroot.'">'.$SITE->shortname.'</a>'; 
                }
                echo $home_button_string;
            ?>
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <div class="nav-collapse collapse">
            	<ul class="nav">
                <?php echo $OUTPUT->custom_menu(); ?>
                </ul>
                <ul class="nav pull-right">
                    <li><?php echo $OUTPUT->page_heading_menu(); ?></li>
                </ul>
                
                <?php
					if (!empty($CFG->enableglobalsearch) && has_capability('moodle/search:query', context_system::instance())) {
						$moodle_global_search = 1;
					}
				?>
                
                <?php if (($searchbox==0) OR ($searchbox==1 AND (isloggedin() AND !isguestuser()))) { ?>
                <form id="search" action="<?php if ($moodle_global_search) {echo $CFG->wwwroot.'/search/index.php';} else {echo $CFG->wwwroot.'/course/search.php';} ?>" >
                	<label for="coursesearchbox" class="lambda-sr-only"><?php if ($moodle_global_search) {echo get_string('search', 'search');} else {echo get_string('searchcourses');} ?></label>						
					<input id="coursesearchbox" type="text" onFocus="if(this.value =='<?php if ($moodle_global_search) {echo get_string('search', 'search');} else {echo get_string('searchcourses');} ?>' ) this.value=''" onBlur="if(this.value=='') this.value='<?php if ($moodle_global_search) {echo get_string('search', 'search');} else {echo get_string('searchcourses');} ?>'" value="<?php if ($moodle_global_search) {echo get_string('search', 'search');} else {echo get_string('searchcourses');} ?>" <?php if ($moodle_global_search) {echo 'name="q"';} else {echo 'name="search"';} ?> >
					<button type="submit"><span class="lambda-sr-only"><?php echo get_string('submit'); ?></span></button>						
				</form>
                <?php } ?>
                
            </div>
        </div>
    </nav>
</header>

<?php if ($shadow_effect) { ?>
	<div class="container-fluid" style="background: #fff;"><img src="<?php echo $OUTPUT->image_url('bg/lambda-shadow', 'theme'); ?>" class="lambda-shadow" alt=""></div>
<?php } ?>