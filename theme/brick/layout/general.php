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
 * The default layout for the Brick theme.
 *
 * @package   theme_brick
 * @copyright 2010 John Stabinger (http://newschoollearning.com/)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$hasheading = ($PAGE->heading);
$hasnavbutton = ($PAGE->button);
$hasnavbar = (empty($PAGE->layout_options['nonavbar']) && $PAGE->has_navbar());
$hasfooter = (empty($PAGE->layout_options['nofooter']));
$hassidepost = $PAGE->blocks->region_has_content('side-post', $OUTPUT);
$showsidepost = ($hassidepost && !$PAGE->blocks->region_completely_docked('side-post', $OUTPUT));

$custommenu = $OUTPUT->custom_menu();
$hascustommenu = (empty($PAGE->layout_options['nocustommenu']) && !empty($custommenu));


$bodyclasses = array();
if ($showsidepost) {
    $bodyclasses[] = 'side-post-only';
} else if (!$showsidepost) {
    $bodyclasses[] = 'content-only';
}

$courseheader = $coursecontentheader = $coursecontentfooter = $coursefooter = '';
if (empty($PAGE->layout_options['nocourseheaderfooter'])) {
    $courseheader = $OUTPUT->course_header();
    $coursecontentheader = $OUTPUT->course_content_header();
    if (empty($PAGE->layout_options['nocoursefooter'])) {
        $coursecontentfooter = $OUTPUT->course_content_footer();
        $coursefooter = $OUTPUT->course_footer();
    }
}

echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes() ?>>
<head>
    <title><?php echo $PAGE->title ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->pix_url('favicon', 'theme')?>" />
    <?php echo $OUTPUT->standard_head_html() ?>
</head>

<body id="<?php p($PAGE->bodyid) ?>" class="<?php p($PAGE->bodyclasses.' '.join(' ', $bodyclasses)) ?>">
<?php echo $OUTPUT->standard_top_of_body_html() ?>

<!-- START OF HEADER -->

<div id="page-header">
	<div id="header">

		<?php if (!empty($PAGE->theme->settings->logo)) { ?>

			<div id="logo">
			</div>

		<?php } else { ?>

			<div id="nologo">
				<?php echo $PAGE->heading ?>
			</div>

		<?php } ?>

			<div id="loggedinas">
			<?php if ($hasheading) {
            	echo $OUTPUT->lang_menu();
            	echo $OUTPUT->login_info();
            	echo $PAGE->headingmenu;
            } ?>
			</div>

		<div id="headerbottom">

			<div id="menu">
				<?php if ($hascustommenu) { ?>
 					<div id="custommenu"><?php echo $custommenu; ?></div>
				<?php } ?>
			</div>
			<?php if ($hasheading && !empty($PAGE->theme->settings->logo)) { ?>
			   	<div id="headingtitle">
	    			<h1><?php echo $PAGE->heading ?></h1>
	    		</div>
	    	<?php } ?>

		</div>

	</div>
</div>
<!-- END OF HEADER -->




<div id="mypagewrapper">
	<div id="page">
		<div id="wrapper" class="clearfix">

          <?php if (!empty($courseheader)) { ?>
            <div id="course-header"><?php echo $courseheader; ?></div>
          <?php } ?>
<!-- START OF CONTENT -->

			<div id="page-content-wrapper" class="wrapper clearfix">
		    	<div id="page-content">
    		    	<div id="region-main-box">
        		    	<div id="region-post-box">

	            	    	<div id="region-main-wrap">
    	            	    	<div id="region-main">
        	            	    	<div class="region-content">

        	            	             <?php if ($hasnavbar) { ?>
        	    							<div class="navbar">
            									<div class="wrapper">
	            	    							<div class="breadcrumb"><?php echo $OUTPUT->navbar(); ?></div>
	    	        							</div>
    	        							</div>
        								<?php } ?>

                                        <?php echo $coursecontentheader; ?>
            	            	    	<?php echo $OUTPUT->main_content() ?>
                                        <?php echo $coursecontentfooter; ?>

	                	        	</div>
    	                		</div>
	    	            	</div>

		                	<?php if ($hassidepost) { ?>

    		            	<div id="region-post" class="block-region">
        		            	<div class="region-content">

        		             		<?php if ($hasnavbutton) { ?>
		        		           		<div class="navbutton"><?php echo $PAGE->button; ?></div>
	        		            	<?php } ?>

            		            	<?php echo $OUTPUT->blocks_for_region('side-post') ?>

                		    	</div>
	                		</div>

	    	            	<?php } ?>

    	    	    	</div>
	    	    	</div>
	    		</div>
    		</div>

<!-- END OF CONTENT -->
            <?php if (!empty($coursefooter)) { ?>
                <div id="course-footer"><?php echo $coursefooter; ?></div>
            <?php } ?>


		</div>
	</div>
</div>

<!-- START OF FOOTER -->

<?php if ($hasfooter) { ?>
	<div id="page-footer" class="wrapper">
		<p class="helplink"><?php echo page_doc_link(get_string('moodledocslink')) ?></p>
		<?php
        echo $OUTPUT->login_info();
		echo $OUTPUT->home_link();
        echo $OUTPUT->standard_footer_html();
		?>
	</div>
<?php } ?>

<?php echo $OUTPUT->standard_end_of_body_html() ?>
</body>
</html>