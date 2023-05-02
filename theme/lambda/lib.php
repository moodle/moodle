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
function theme_lambda_get_setting($setting, $format = false) {
    global $CFG;
    require_once($CFG->dirroot . '/lib/weblib.php');
    static $theme;
    if (empty($theme)) {
        $theme = theme_config::load('lambda');
    }
    if (empty($theme->settings->$setting)) {
        return false;
    } else if (!$format) {
        return $theme->settings->$setting;
    } else if ($format === 'format_text') {
        return format_text($theme->settings->$setting, FORMAT_PLAIN);
    } else if ($format === 'format_html') {
        return format_text($theme->settings->$setting, FORMAT_HTML, array('trusted' => true, 'noclean' => true));
    } else {
        return format_string($theme->settings->$setting);
    }
}

function theme_lambda_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    if ($context->contextlevel == CONTEXT_SYSTEM) {
        $theme = theme_config::load('lambda');		
		if ($filearea === 'logo') {
            return $theme->setting_file_serve('logo', $args, $forcedownload, $options);
        } else if ($filearea === 'favicon') {
            return $theme->setting_file_serve('favicon', $args, $forcedownload, $options);
        } else if ($filearea === 'pagebackground') {
            return $theme->setting_file_serve('pagebackground', $args, $forcedownload, $options);
        } else if ($filearea === 'header_background') {
            return $theme->setting_file_serve('header_background', $args, $forcedownload, $options);
        } else if ($filearea === 'category_background') {
            return $theme->setting_file_serve('category_background', $args, $forcedownload, $options);
		} else if ($filearea === 'slide1image') {
            return $theme->setting_file_serve('slide1image', $args, $forcedownload, $options);
        } else if ($filearea === 'slide2image') {
            return $theme->setting_file_serve('slide2image', $args, $forcedownload, $options);
        } else if ($filearea === 'slide3image') {
            return $theme->setting_file_serve('slide3image', $args, $forcedownload, $options);
        } else if ($filearea === 'slide4image') {
            return $theme->setting_file_serve('slide4image', $args, $forcedownload, $options);
        } else if ($filearea === 'slide5image') {
            return $theme->setting_file_serve('slide5image', $args, $forcedownload, $options);
        } else if ((substr($filearea, 0, 15) === 'carousel_image_')) {
            return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
        } else if ($filearea === 'fonts_file_body') {
            return $theme->setting_file_serve('fonts_file_body', $args, $forcedownload, $options);
        } else if ($filearea === 'fonts_file_headings') {
            return $theme->setting_file_serve('fonts_file_headings', $args, $forcedownload, $options);
		} else {
            send_file_not_found();
        }
    } else {
        send_file_not_found();
    }
}

function theme_lambda_page_init(moodle_page $page)
{
    $page->requires->jquery();
	if ($page->pagelayout === 'frontpage') {
        $page->requires->jquery_plugin('jquery.easing.min.1.4', 'theme_lambda');
        $page->requires->jquery_plugin('slideshow', 'theme_lambda');
        $page->requires->jquery_plugin('carousel', 'theme_lambda');
    }
}

function lambda_set_pagewidth1($css, $pagewidth) {
    $tag = '[[setting:pagewidth]]';
    $replacement = $pagewidth;
    if (is_null($replacement)) {
        $replacement = '1600';
    }
    if ( ($replacement == "90") || ($replacement == "100") ) {
		$css = str_replace($tag, $replacement.'%', $css);
	} else {
		$css = str_replace($tag, $replacement.'px', $css);
	}
    return $css;
}

function lambda_set_pagewidth2($css, $pagewidth) {
    $tag = '[[setting:pagewidth_wide]]';
    if ($pagewidth == "100") {
        $replacement = 'body {background:none repeat scroll 0 0 #fff;padding-top:0;} @media(max-width:767px){body {padding-left: 0; padding-right: 0;} #page {padding: 10px 0;}} #wrapper {max-width:100%;width:100%;} #page-header {margin:0 auto;max-width:100%;} @media(min-width: 768px) {#page-header > .container-fluid {max-width: 90%;}} .container-fluid {padding: 0; max-width:100%} .navbar {background: none repeat scroll 0 0 [[setting:menufirstlevelcolor]];padding: 0;} .navbar-inner {margin: 0 auto; max-width: 90%;} .navbar .brand {margin-left:0;} .navbar #search {margin-right:0;} .pagelayout-frontpage header.navbar + .container-fluid > img.lambda-shadow {display: none;} #page {margin: 0 auto; max-width: 90%;} #page-footer .row-fluid {margin: 0 auto; max-width: 90%;} .spotlight-full {margin-left: -5.75% !important; margin-right: -5.75% !important;} .socials-header .social_icons.pull-right {padding-right:10%;} .socials-header .social_contact {padding-left:10%;} .path-login.login_lambda .logo-header {padding-top: 25px;}';
		$css = str_replace($tag, $replacement, $css);
	}
	else { 
		$css = str_replace($tag, "", $css);
	}
    return $css;
}

function lambda_set_category_layout($css,$category_layout) {
	global $CFG;
    $tag = '[[setting:category_layout]]';
    if ($category_layout == "0") {
        $replacement = '.coursebox{border:none;border-bottom:1px solid #e5e5e5;margin-bottom:25px;padding-bottom:35px}.coursebox .content .courseimage{float:left;margin-right:2em}@media (min-width:1187px){.coursebox .content .courseimage{height:225px;width:325px}}@media (max-width:1186px){.coursebox .content .courseimage{height:200px;width:290px}}@media (max-width:980px){.coursebox .content .courseimage{height:150px;width:220px}.coursebox .content .coursecat,.coursebox .content .summary{padding-top:0}}@media (max-width:580px){.coursebox .content .courseimage{height:250px;width:100%;float:unset}}';
		$replacement .= '#myoverview_courses_view .row-fluid .span6{border-bottom:1px solid #e5e5e5;margin-bottom:1em;position:relative}@media (min-width:440px){#myoverview_courses_view .row-fluid .span6{width:100%;padding-bottom:.5em}#myoverview_courses_view .myoverviewimg{float:left}}@media (min-width:647px) and (max-width:767px){#myoverview_courses_view .row-fluid .span6{padding-bottom:2.5em}}#myoverview_courses_view .course-info-container{margin-top:1em}#myoverview_courses_view .myoverviewimg{width:240px;height:166px;margin-right:2em}#myoverview_courses_view .course-info-container .media-heading a,#myoverview_timeline_courses .course-info-container h4 a{color:#555;margin-top:2em}#myoverview_courses_view .course-info-container p.text-muted,#myoverview_timeline_courses .course-info-container p.muted{color:#555}#myoverview_courses_view .course-info-container .progress-chart-container{position:absolute;left:175px;top:1.5em}.progress-chart-container .no-progress{display:none}.progress-chart-container .progress-doughnut .progress-indicator svg .circle{stroke:[[setting:maincolor]]}.progress-chart-container .progress-doughnut{background:rgba(255,255,255,.5)}.progress-chart-container .progress-doughnut .progress-text.has-percent{color:#333;font-weight:700}#myoverview_timeline_courses .progress-chart-container .no-progress{height:0;width:0;margin-bottom:-70px;display:block}#myoverview_timeline_courses .progress-chart-container .no-progress .icon{display:none}';
		$css = str_replace($tag, $replacement, $css);
	}
	else if ($category_layout == "1") {
        $replacement = '.frontpage-course-list-enrolled,.courses.frontpage-course-list-all,.course_category_tree .category-browse{display:flex;flex-wrap:wrap;justify-content:space-evenly;position:relative;margin-bottom:20px}#frontpage-category-combo .coursebox{float:left} #frontpage-category-combo .category.loaded.with_children .content .courses .coursebox{margin-top:10px} .coursebox{width: 325px; position: relative; border: 1px solid #fff; box-shadow: 0 0 10px rgba(0,0,0,.15); transition: all .3s ease-out 0s; padding: 0; margin: 0 10px 30px 10px;}.coursebox:hover{box-shadow:1px 4px 20px -2px rgba(0,0,0,.2);transform:translateY(-1px)} .coursebox .summary > div p {margin: 0;} .coursebox h3.coursename {line-height: 25px; margin-bottom: 5px;} .coursebox .course-btn,.coursebox h3.coursename{text-align:center;margin-top:0}.coursebox .content .summary{width:auto;padding:0 .5em;margin:0;height:140px;overflow:hidden}.coursebox .summary>div{overflow:hidden;text-align:justify;line-height:25px;max-height:100px}.coursebox .content .courseimage{width:100%!important;height:166px;margin:0 0 10px!important;float:unset}.coursebox .content .teachers,.coursebox .content .custom_fields{display:none}.path-enrol .coursebox .content .teachers,.path-enrol .coursebox .content .custom_fields{display:block;padding: 0 .5em;margin: 0;}.coursebox .enrolmenticons,.coursebox .moreinfo{padding:0}.coursebox .enrolmenticons img{padding:5px!important;background:rgba(255,255,255,.75);border-radius:50%;height:16px;width:16px;float:left;position:absolute;top:10px;left:10px}.coursebox .enrolmenticons img+img{left:40px}.coursebox .enrolmenticons img+img+img{left:70px}.coursebox .enrolmenticons img+img+img+img{left:100px}.coursebox .enrolmenticons img+img+img+img+img{left:130px}@media (max-width:360px){.coursebox{width:85%}} #page-enrol-index #fitem_id_submitbutton{background: transparent; border: none;} @media (min-width:850px){#page-enrol-index .coursebox.clearfix{width:calc(100% - 20px)}#page-enrol-index #region-main .box.generalbox{width:calc(50% - 20px);float:left;padding:10px}} @media (max-width:849px){#page-enrol-index .coursebox.clearfix{width:99%}}#page-enrol-index .coursebox .content .summary,#page-enrol-index .coursebox .summary>div{height:auto;max-height:none}#page-enrol-index .fcontainer .col-md-9.form-inline.felement{width:100%;margin-left:0}#page-enrol-index #fitem_id_submitbutton .col-md-9.form-inline.felement{margin-left:0;float:left;display:block;width:auto} .courses > .paging.paging-morelink, .course_category_tree .subcategories > .paging.paging-morelink {text-align: left; position: absolute; bottom: -25px; left: 0;} .courses.category-browse .pagination.pagination-centered {width: 100% !important; display: block !important;} .courses .coursebox.collapsed {margin-bottom: 12px;} .category-browse .subcategories .courses {display: flex; flex-wrap: wrap;} .category-browse .subcategories .courses .coursebox {margin-bottom: 12px;} .category-browse .subcategories .courses .coursebox.collapsed .enrolmenticons {display: none;} .category-browse .subcategories .courses .coursebox .enrolmenticons img {top: 35px;}';
		$replacement .= '#myoverview_courses_view .row-fluid .span6{width:325px!important}#myoverview_courses_view .row-fluid .span6 .well{border:1px solid #fff;box-shadow:0 0 10px rgba(0,0,0,.15);transition:all .3s ease-out 0s;padding:0 0 10px;position:relative;margin-bottom:15px}#myoverview_courses_view .row-fluid .span6 .well .course-info-container{padding:0 5px;height:110px;overflow:hidden}.well .course-info-container p{overflow:hidden;text-align:justify;line-height:25px}.well .course-info-container h4.media-heading{line-height:25px;text-align:center}#myoverview_courses_view .row-fluid .span6 .well:hover{box-shadow:1px 4px 20px -2px rgba(0,0,0,.2);transform:translateY(-1px)}#myoverview_courses_view .course-info-container .media-heading a,#myoverview_timeline_courses .course-info-container h4 a{color:#555;margin-top:2em}#myoverview_courses_view .course-info-container p.text-muted,#myoverview_timeline_courses .course-info-container p.muted{color:#555}#myoverview_courses_view .myoverviewimg{height:166px}#myoverview_courses_view .course-info-container .progress-chart-container{position:absolute;left:225px;top:1.5em}.progress-chart-container .no-progress{display:none}.progress-chart-container .progress-doughnut .progress-indicator svg .circle{stroke:[[setting:maincolor]]}.progress-chart-container .progress-doughnut{background:rgba(255,255,255,.5)}.progress-chart-container .progress-doughnut .progress-text.has-percent{color:#333;font-weight:700}#myoverview_timeline_courses .progress-chart-container .no-progress{height:0;width:0;margin-bottom:-70px;display:block}#myoverview_timeline_courses .progress-chart-container .no-progress .icon{display:none}';
		$css = str_replace($tag, $replacement, $css);
	}
	else { 
		$css = str_replace($tag, "", $css);
	}
    return $css;
}

function lambda_set_logo_res($css, $logo_res) {
    $tag = '[[setting:logo_res]]';
    if ($logo_res) {
        $replacement = 'a.logo img {max-height:90px;} @media(max-width:767px){a.logo img {max-height:75px;}} @media(max-width:480px){a.logo img {max-height:60px;}}';
		$css = str_replace($tag, $replacement, $css);
	}
	else { 
		$css = str_replace($tag, "", $css);
	}
    return $css;
}

function lambda_set_block_icons($css, $block_icons) {
    $tag = '[[setting:block_icons]]';
    if ($block_icons == "1") {
		$replacement = '.block .card-title::before {background: transparent !important; color: #999;}';
		$css = str_replace($tag, $replacement, $css);
	}
	else if ($block_icons == "2") { 
		$replacement = '.block .card-title::before {display: none;}';
		$css = str_replace($tag, $replacement, $css);
	}
	else { 
		$css = str_replace($tag, "", $css);
	}
    return $css;
}

function lambda_hide_category_background($css, $hide_category_background) {
    $tag = '[[setting:hide_category_background]]';
    if ($hide_category_background == "1") {
		$replacement = '.course_category_tree .collapsible-actions {margin-bottom: 25px; background: #fff; height: 0; padding: 0; border-bottom: none;} .course_category_tree .collapsible-actions:before {display: none;} .course_category_tree .collapsible-actions .collapseexpand {color: #555 !important; font-size: 1em; font-weight: 300;}';
		$css = str_replace($tag, $replacement, $css);
	}
	else { 
		$css = str_replace($tag, "", $css);
	}
    return $css;
}

function lambda_set_customcss($css, $customcss) {
    $tag = '[[setting:customcss]]';
    $replacement = $customcss;
    if (is_null($replacement)) {
        $replacement = '';
    }

    $css = str_replace($tag, $replacement, $css);

    return $css;
}

function theme_lambda_process_css($css, $theme) {
	global $CFG;
	
    $pagewidth = $theme->settings->pagewidth;
    $css = lambda_set_pagewidth1($css,$pagewidth);
	$css = lambda_set_pagewidth2($css,$pagewidth);
	$logo_res = $theme->settings->logo_res;
	$css = lambda_set_logo_res($css,$logo_res);
	$category_layout = $theme->settings->category_layout;
	$css = lambda_set_category_layout($css,$category_layout);
	$block_icons = $theme->settings->block_icons;
	$css = lambda_set_block_icons($css,$block_icons);
	$hide_category_background = $theme->settings->hide_category_background;
	$css = lambda_hide_category_background($css,$hide_category_background);
	$socials_header_bg = $theme->settings->socials_header_bg;
	$css = theme_lambda_set_border_top($css,$socials_header_bg);
    
	// Set the Fonts.
	$font_heading_src = '';
	$font_body_src = '';
	
	if ($theme->settings->font_body_size ==1) {
        $bodysize = '12px';
    } else if ($theme->settings->font_body_size ==2) {
        $bodysize = '13px';
	} else if ($theme->settings->font_body_size ==3) {
        $bodysize = '14px';
	} else if ($theme->settings->font_body_size ==4) {
        $bodysize = '15px';
	} else if ($theme->settings->font_body_size ==5) {
        $bodysize = '16px';}
	
	if ($theme->settings->fonts_source ==1) {
    if ($theme->settings->font_body ==1) {
        $bodyfont = 'Open Sans';
    } else if ($theme->settings->font_body ==2) {
        $bodyfont = 'Arimo';
    } else if ($theme->settings->font_body ==3) {
        $bodyfont = 'Arvo';
	} else if ($theme->settings->font_body ==4) {
        $bodyfont = 'Bree Serif';
	} else if ($theme->settings->font_body ==5) {
        $bodyfont = 'Cabin';
	} else if ($theme->settings->font_body ==6) {
        $bodyfont = 'Cantata One';
	} else if ($theme->settings->font_body ==7) {
        $bodyfont = 'Crimson Text';
	} else if ($theme->settings->font_body ==8) {
        $bodyfont = 'Droid Sans';
	} else if ($theme->settings->font_body ==9) {
        $bodyfont = 'Droid Serif';
	} else if ($theme->settings->font_body ==10) {
        $bodyfont = 'Gudea';
	} else if ($theme->settings->font_body ==11) {
        $bodyfont = 'Imprima';
	} else if ($theme->settings->font_body ==12) {
        $bodyfont = 'Lekton';
	} else if ($theme->settings->font_body ==13) {
        $bodyfont = 'Nunito';
	} else if ($theme->settings->font_body ==14) {
        $bodyfont = 'Montserrat';
	} else if ($theme->settings->font_body ==15) {
        $bodyfont = 'Playfair Display';
	} else if ($theme->settings->font_body ==16) {
        $bodyfont = 'Pontano Sans';
	} else if ($theme->settings->font_body ==17) {
        $bodyfont = 'PT Sans';
	} else if ($theme->settings->font_body ==18) {
        $bodyfont = 'Raleway';
	} else if ($theme->settings->font_body ==22) {
        $bodyfont = 'Roboto';
	} else if ($theme->settings->font_body ==19) {
        $bodyfont = 'Ubuntu';
	} else if ($theme->settings->font_body ==20) {
        $bodyfont = 'Vollkorn';
	} else if ($theme->settings->font_body ==21) {
        $bodyfont = 'Work Sans';}
		
	if ($theme->settings->font_heading ==1) {
        $headingfont = 'Open Sans';
		$headingweight = '700';
    } else if ($theme->settings->font_heading ==2) {
        $headingfont = 'Abril Fatface';
		$headingweight = '400';
    } else if ($theme->settings->font_heading ==3) {
        $headingfont = 'Arimo';
		$headingweight = '700';
    } else if ($theme->settings->font_heading ==4) {
        $headingfont = 'Arvo';
		$headingweight = '700';
    } else if ($theme->settings->font_heading ==5) {
        $headingfont = 'Bevan';
		$headingweight = '400';
    } else if ($theme->settings->font_heading ==6) {
        $headingfont = 'Bree Serif';
		$headingweight = '400';
    } else if ($theme->settings->font_heading ==7) {
        $headingfont = 'Cabin';
		$headingweight = '700';
    } else if ($theme->settings->font_heading ==8) {
        $headingfont = 'Cantata One';
		$headingweight = '700';
    } else if ($theme->settings->font_heading ==9) {
        $headingfont = 'Crimson Text';
		$headingweight = '700';
    } else if ($theme->settings->font_heading ==10) {
        $headingfont = 'Encode Sans';
		$headingweight = '700';
    } else if ($theme->settings->font_heading ==11) {
        $headingfont = 'Enriqueta';
		$headingweight = '700';
    } else if ($theme->settings->font_heading ==12) {
        $headingfont = 'Gudea';
		$headingweight = '700';
    } else if ($theme->settings->font_heading ==13) {
        $headingfont = 'Imprima';
		$headingweight = '700';
    } else if ($theme->settings->font_heading ==14) {
        $headingfont = 'Josefin Sans';
		$headingweight = '700';
    } else if ($theme->settings->font_heading ==15) {
        $headingfont = 'Lekton';
		$headingweight = '700';
    } else if ($theme->settings->font_heading ==16) {
        $headingfont = 'Lobster';
		$headingweight = '400';
    } else if ($theme->settings->font_heading ==17) {
        $headingfont = 'Nunito';
		$headingweight = '700';
    } else if ($theme->settings->font_heading ==18) {
        $headingfont = 'Montserrat';
		$headingweight = '700';
    } else if ($theme->settings->font_heading ==19) {
        $headingfont = 'Pacifico';
		$headingweight = '400';
    } else if ($theme->settings->font_heading ==20) {
        $headingfont = 'Playfair Display';
		$headingweight = '700';
    } else if ($theme->settings->font_heading ==21) {
        $headingfont = 'Pontano Sans';
		$headingweight = '700';
    } else if ($theme->settings->font_heading ==22) {
        $headingfont = 'PT Sans';
		$headingweight = '700';
    } else if ($theme->settings->font_heading ==23) {
        $headingfont = 'Raleway';
		$headingweight = '500';
    } else if ($theme->settings->font_heading ==28) {
        $headingfont = 'Roboto';
		$headingweight = '500';
    } else if ($theme->settings->font_heading ==24) {
        $headingfont = 'Sansita One';
		$headingweight = '400';
    } else if ($theme->settings->font_heading ==25) {
        $headingfont = 'Ubuntu';
		$headingweight = '700';
    } else if ($theme->settings->font_heading ==26) {
        $headingfont = 'Vollkorn';
		$headingweight = '700';
	} else if ($theme->settings->font_heading ==27) {
        $headingfont = 'Work Sans';
        $headingweight = '700';}
	} else {
			$headingfont = 'custom_heading_font';
			if ($theme->settings->font_headings_weight == 3) {$headingweight = 300;}
			else if ($theme->settings->font_headings_weight == 2) {$headingweight = 400;}
			else {$headingweight = 700;}
			if (!is_null($theme->setting_file_url('fonts_file_headings', 'fonts_file_headings'))) {
                $font_heading_src = "url(".$theme->setting_file_url('fonts_file_headings', 'fonts_file_headings').")";
            }
			$bodyfont = 'custom_body_font';
			if (!is_null($theme->setting_file_url('fonts_file_body', 'fonts_file_body'))) {
                $font_body_src = "url(".$theme->setting_file_url('fonts_file_body', 'fonts_file_body').")";
            }
    }
    $css = theme_lambda_set_customfontface($css, $font_heading_src, $font_body_src);
    $css = theme_lambda_set_headingfont($css, $headingfont, $headingweight);
    $css = theme_lambda_set_bodyfont($css, $bodyfont, $bodysize);
  
    if (!empty($theme->settings->customcss)) {
        $customcss = $theme->settings->customcss;
    } else {
        $customcss = null;
    }
    $css = lambda_set_customcss($css, $customcss);
	
	if ($theme->settings->banner_font_color==0) {
        $banner_font_color = '#fff';
    } else if ($theme->settings->banner_font_color==1) {
        $banner_font_color = '#000';
    } else if ($theme->settings->banner_font_color==2) {
        $banner_font_color = '[[setting:maincolor]]';
    } else {
        $banner_font_color = null;
    }
    $css = theme_lambda_set_category_banner_font_color($css, $banner_font_color);
	
	if (!empty($theme->settings->socials_header_bg)) {
        $socials_header_bg = $theme->settings->socials_header_bg;
    } else {
        $socials_header_bg = null;
    }
    $css = theme_lambda_set_socials_header_bg($css, $socials_header_bg);
	
    if (!empty($theme->settings->maincolor)) {
        $maincolor = $theme->settings->maincolor;
    } else {
        $maincolor = null;
    }
    $css = theme_lambda_set_maincolor($css, $maincolor);

    if (!empty($theme->settings->mainhovercolor)) {
        $mainhovercolor = $theme->settings->mainhovercolor;
    } else {
        $mainhovercolor = null;
    }
    $css = theme_lambda_set_mainhovercolor($css, $mainhovercolor);
	
	if (!empty($theme->settings->linkcolor)) {
        $linkcolor = $theme->settings->linkcolor;
    } else {
        $linkcolor = null;
    }
    $css = theme_lambda_set_linkcolor($css, $linkcolor);

    if (!empty($theme->settings->def_buttoncolor)) {
        $def_buttoncolor = $theme->settings->def_buttoncolor;
    } else {
        $def_buttoncolor = null;
    }
    $css = theme_lambda_set_def_buttoncolor($css, $def_buttoncolor);

    if (!empty($theme->settings->def_buttonhovercolor)) {
        $def_buttonhovercolor = $theme->settings->def_buttonhovercolor;
    } else {
        $def_buttonhovercolor = null;
    }
    $css = theme_lambda_set_def_buttonhovercolor($css, $def_buttonhovercolor);
	
    if (!empty($theme->settings->headercolor)) {
        $headercolor = $theme->settings->headercolor;
    } else {
        $headercolor = null;
    }
    $css = theme_lambda_set_headercolor($css, $headercolor);

    if (!empty($theme->settings->menufirstlevelcolor)) {
        $menufirstlevelcolor = $theme->settings->menufirstlevelcolor;
    } else {
        $menufirstlevelcolor = null;
    }
    $css = theme_lambda_set_menufirstlevelcolor($css, $menufirstlevelcolor);

    if (!empty($theme->settings->menufirstlevel_linkcolor)) {
        $menufirstlevel_linkcolor = $theme->settings->menufirstlevel_linkcolor;
    } else {
        $menufirstlevel_linkcolor = null;
    }
    $css = theme_lambda_set_menufirstlevel_linkcolor($css, $menufirstlevel_linkcolor);

    if (!empty($theme->settings->menusecondlevelcolor)) {
        $menusecondlevelcolor = $theme->settings->menusecondlevelcolor;
    } else {
        $menusecondlevelcolor = null;
    }
    $css = theme_lambda_set_menusecondlevelcolor($css, $menusecondlevelcolor);

    if (!empty($theme->settings->menusecondlevel_linkcolor)) {
        $menusecondlevel_linkcolor = $theme->settings->menusecondlevel_linkcolor;
    } else {
        $menusecondlevel_linkcolor = null;
    }
    $css = theme_lambda_set_menusecondlevel_linkcolor($css, $menusecondlevel_linkcolor);

    if (!empty($theme->settings->footercolor)) {
        $footercolor = $theme->settings->footercolor;
    } else {
        $footercolor = null;
    }
    $css = theme_lambda_set_footercolor($css, $footercolor);

    if (!empty($theme->settings->footerheadingcolor)) {
        $footerheadingcolor = $theme->settings->footerheadingcolor;
    } else {
        $footerheadingcolor = null;
    }
    $css = theme_lambda_set_footerheadingcolor($css, $footerheadingcolor);

    if (!empty($theme->settings->footertextcolor)) {
        $footertextcolor = $theme->settings->footertextcolor;
    } else {
        $footertextcolor = null;
    }
    $css = theme_lambda_set_footertextcolor($css, $footertextcolor);
	
    if (!empty($theme->settings->copyrightcolor)) {
        $copyrightcolor = $theme->settings->copyrightcolor;
    } else {
        $copyrightcolor = null;
    }
    $css = theme_lambda_set_copyrightcolor($css, $copyrightcolor);

    if (!empty($theme->settings->copyright_textcolor)) {
        $copyright_textcolor = $theme->settings->copyright_textcolor;
    } else {
        $copyright_textcolor = null;
    }
	$css = theme_lambda_set_copyright_textcolor($css, $copyright_textcolor);
	
	if (!empty($theme->settings->socials_color)) {
        $socials_color = $theme->settings->socials_color;
    } else {
        $socials_color = null;
    }
    $css = theme_lambda_set_socials_color($css, $socials_color);
	
	if (!empty($theme->settings->carousel_img_dim)) {
        $carousel_img_dim = $theme->settings->carousel_img_dim;
    } else {
        $carousel_img_dim = null;
    }
    $css = theme_lambda_set_carousel_img_dim($css, $carousel_img_dim);
	
	if (!empty($theme->settings->slideshow_height)) {
        $slideshow_height = $theme->settings->slideshow_height;
    } else {
        $slideshow_height = null;
    }
	$hide_captions = $theme->settings->slideshow_hide_captions;
    $css = theme_lambda_set_slideshow_height($css, $slideshow_height, $hide_captions);
	
	if (!is_null($theme->setting_file_url('category_background', 'category_background'))) {
		$background = $theme->setting_file_url('category_background', 'category_background');
	} else {
		$background = null;
	}
	$css = theme_lambda_set_category_banner_bg($css, $background);
	
	if (!is_null($theme->setting_file_url('header_background', 'header_background'))) {
		$background = $theme->setting_file_url('header_background', 'header_background');
	} else {
		$background = null;
	}
	if ($theme->settings->header_bg_repeat==1)  {
        $repeat = ' repeat 0 0';
    }
	else {
		$repeat = ' 50% 50% / cover';
	}
	$css = theme_lambda_set_header_bg($css, $background, $repeat);

    $setting = 'list_bg';
	if (is_null($theme->setting_file_url('pagebackground', 'pagebackground'))) {
    	global $OUTPUT;
		if ($theme->settings->list_bg==0)  {
        	$pagebackground = $OUTPUT->image_url('page_bg/page_bg_01', 'theme');
			$repeat = 'no-repeat fixed 0 0';
			$size = 'cover';}
		else if ($theme->settings->list_bg==1)  {
        	$pagebackground = $OUTPUT->image_url('page_bg/page_bg_02', 'theme');
			$repeat = 'no-repeat fixed 0 0';
			$size = 'cover';}
		else if ($theme->settings->list_bg==2)  {
			$pagebackground = $OUTPUT->image_url('page_bg/page_bg_blur01', 'theme');
			$repeat = 'no-repeat fixed 0 0';
			$size = 'cover';}
		else if ($theme->settings->list_bg==3)  {
			$pagebackground = $OUTPUT->image_url('page_bg/page_bg_blur02', 'theme');
			$repeat = 'no-repeat fixed 0 0';
			$size = 'cover';}
		else if ($theme->settings->list_bg==4)  {
			$pagebackground = $OUTPUT->image_url('page_bg/page_bg_blur03', 'theme');
			$repeat = 'no-repeat fixed 0 0';
			$size = 'cover';}
		else if ($theme->settings->list_bg==5)  {
			$pagebackground = $OUTPUT->image_url('page_bg/cream_pixels', 'theme');
			$repeat = 'repeat fixed 0 0';
			$size = 'auto';}
		else if ($theme->settings->list_bg==6)  {
			$pagebackground = $OUTPUT->image_url('page_bg/mochaGrunge', 'theme');
			$repeat = 'repeat fixed 0 0';
			$size = 'auto';}
		else if ($theme->settings->list_bg==7)  {
			$pagebackground = $OUTPUT->image_url('page_bg/skulls', 'theme');
			$repeat = 'repeat fixed 0 0';
			$size = 'auto';}
		else if ($theme->settings->list_bg==8)  {
			$pagebackground = $OUTPUT->image_url('page_bg/sos', 'theme');
			$repeat = 'repeat fixed 0 0';
			$size = 'auto';}
		else if ($theme->settings->list_bg==9)  {
			$pagebackground = $OUTPUT->image_url('page_bg/squairy_light', 'theme');
			$repeat = 'repeat fixed 0 0';
			$size = 'auto';}
		else if ($theme->settings->list_bg==10)  {
			$pagebackground = $OUTPUT->image_url('page_bg/subtle_white_feathers', 'theme');
			$repeat = 'repeat fixed 0 0';
			$size = 'auto';}
		else if ($theme->settings->list_bg==11)  {
			$pagebackground = $OUTPUT->image_url('page_bg/tweed', 'theme');
			$repeat = 'repeat fixed 0 0';
			$size = 'auto';}
		else if ($theme->settings->list_bg==12)  {
			$pagebackground = $OUTPUT->image_url('page_bg/wet_snow', 'theme');
			$repeat = 'repeat fixed 0 0';
			$size = 'auto';}
		else {
			$pagebackground = $OUTPUT->image_url('page_bg/page_bg_00', 'theme');
			$repeat = 'no-repeat fixed 0 0';
			$size = 'cover';}
		$css = theme_lambda_set_pagebackground($css, $pagebackground, $setting);
		$css = theme_lambda_set_background_repeat($css, $repeat, $size);
    } else {
    	$setting = 'pagebackground';
    	$pagebackground = $theme->setting_file_url($setting, $setting);
    	$css = theme_lambda_set_pagebackground($css, $pagebackground, $setting);
	
		$repeat = 'no-repeat fixed 0 0';
		$size = 'cover';
    	if ($theme->settings->page_bg_repeat==1)  {
     	   $repeat = 'repeat fixed 0 0';
			$size = 'auto';
    	}
    	$css = theme_lambda_set_background_repeat($css, $repeat, $size);
	}
	
	if (!empty($CFG->themedir)) {
		$csslambda = file_get_contents($CFG->themedir . '/lambda/bootstrapbase/moodle.css');
		}
	else {
		$csslambda = file_get_contents($CFG->dirroot . '/theme/lambda/bootstrapbase/moodle.css');
	}
	$csslambda .= "\n" . $css;
	
    return $csslambda;
}

function theme_lambda_set_customfontface($css, $font_heading_src, $font_body_src) {
    $tag = '[[setting:fontface]]';
	$replacement = '';
    if ($font_heading_src != '') {$replacement .= '@font-face {font-family: "custom_heading_font"; src: '.$font_heading_src.';}';}
	if ($font_body_src != '') {$replacement .= ' @font-face {font-family: "custom_body_font"; src: '.$font_body_src.';}';}
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function theme_lambda_set_headingfont($css, $headingfont, $headingweight) {
    $tag = '[[setting:headingfont]]';
    $replacement = $headingfont;
    if (is_null($replacement)) {
        $replacement = 'Open Sans';
    }
    $css = str_replace($tag, $replacement, $css);
    $tag = '[[setting:headingweight]]';
    $replacement = $headingweight;
    if (is_null($replacement)) {
        $replacement = '700';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function theme_lambda_set_bodyfont($css, $bodyfont, $bodysize) {
    $tag = '[[setting:bodyfont]]';
    $replacement = $bodyfont;
    if (is_null($replacement)) {
        $replacement = 'Open Sans';
    }
    $css = str_replace($tag, $replacement, $css);
	$tag = '[[setting:bodysize]]';
    $replacement = $bodysize;
    if (is_null($replacement)) {
        $replacement = '13px';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function theme_lambda_set_maincolor($css, $themecolor) {
    $tag = '[[setting:maincolor]]';
    $replacement = $themecolor;
    if (is_null($replacement)) {
        $replacement = '#ffae00';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function theme_lambda_set_mainhovercolor($css, $themecolor) {
    $tag = '[[setting:mainhovercolor]]';
    $replacement = $themecolor;
    if (is_null($replacement)) {
        $replacement = '#efa300';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function theme_lambda_set_linkcolor($css, $themecolor) {
    $tag = '[[setting:linkcolor]]';
    $replacement = $themecolor;
    if (is_null($replacement)) {
        $replacement = '#efa300';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function theme_lambda_set_def_buttoncolor($css, $themecolor) {
    $tag = '[[setting:def_buttoncolor]]';
    $replacement = $themecolor;
    if (is_null($replacement)) {
        $replacement = '#8ec63f';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function theme_lambda_set_def_buttonhovercolor($css, $themecolor) {
    $tag = '[[setting:def_buttonhovercolor]]';
    $replacement = $themecolor;
    if (is_null($replacement)) {
        $replacement = '#77ae29';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function theme_lambda_set_headercolor($css, $themecolor) {
    $tag = '[[setting:headercolor]]';
    $replacement = $themecolor;
    if (is_null($replacement)) {
        $replacement = '#ffffff';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function theme_lambda_set_menufirstlevelcolor($css, $themecolor) {
    $tag = '[[setting:menufirstlevelcolor]]';
    $replacement = $themecolor;
    if (is_null($replacement)) {
        $replacement = '#323A45';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function theme_lambda_set_menufirstlevel_linkcolor($css, $themecolor) {
    $tag = '[[setting:menufirstlevel_linkcolor]]';
    $replacement = $themecolor;
    if (is_null($replacement)) {
        $replacement = '#ffffff';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function theme_lambda_set_menusecondlevelcolor($css, $themecolor) {
    $tag = '[[setting:menusecondlevelcolor]]';
    $replacement = $themecolor;
    if (is_null($replacement)) {
        $replacement = '#f4f4f4';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function theme_lambda_set_menusecondlevel_linkcolor($css, $themecolor) {
    $tag = '[[setting:menusecondlevel_linkcolor]]';
    $replacement = $themecolor;
    if (is_null($replacement)) {
        $replacement = '#444444';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function theme_lambda_set_footercolor($css, $themecolor) {
    $tag = '[[setting:footercolor]]';
    $replacement = $themecolor;
    if (is_null($replacement)) {
        $replacement = '#323A45';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function theme_lambda_set_footerheadingcolor($css, $themecolor) {
    $tag = '[[setting:footerheadingcolor]]';
    $replacement = $themecolor;
    if (is_null($replacement)) {
        $replacement = '#f9f9f9';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function theme_lambda_set_footertextcolor($css, $themecolor) {
    $tag = '[[setting:footertextcolor]]';
    $replacement = $themecolor;
    if (is_null($replacement)) {
        $replacement = '#bdc3c7';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function theme_lambda_set_copyrightcolor($css, $themecolor) {
    $tag = '[[setting:copyrightcolor]]';
    $replacement = $themecolor;
    if (is_null($replacement)) {
        $replacement = '#292F38';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function theme_lambda_set_copyright_textcolor($css, $themecolor) {
    $tag = '[[setting:copyright_textcolor]]';
    $replacement = $themecolor;
    if (is_null($replacement)) {
        $replacement = '#bdc3c7';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function theme_lambda_set_socials_color($css, $themecolor) {
    $tag = '[[setting:socials_color]]';
    $replacement = $themecolor;
    if (is_null($replacement)) {
        $replacement = '#a9a9a9';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function theme_lambda_set_socials_header_bg($css, $themecolor) {
    $tag = '[[setting:socials_header_bg]]';
	$replacement = '#ff0000';
	switch ($themecolor) {
    case 0:
        $replacement = 'transparent';
        break;
    case 1:
        $replacement = 'rgba(0,0,0,0.025)';
        break;
    case 2:
        $replacement = 'rgba(0,0,0,0.25)';
        break;
    case 3:
        $replacement = '[[setting:maincolor]]';
        break;
    case 4:
        $replacement = '[[setting:copyrightcolor]]';
        break;
	}
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function theme_lambda_set_carousel_img_dim($css, $carousel_img_dim) {
    $tag = '[[setting:carousel_img_dim]]';
    $replacement = $carousel_img_dim;
    if (is_null($replacement)) {
        $replacement = '260px';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function theme_lambda_set_slideshow_height($css, $slideshow_height, $hide_captions) {
    $tag = '[[setting:slideshow_height]]';
    $replacement = $slideshow_height;
    if (is_null($replacement) || ($replacement == 'responsive')) {
        $replacement = '475px';
    }
    $css = str_replace($tag, $replacement, $css);
	if ($replacement == '475px') {
		$tag = '[[setting:slideshow_height_desktop_s]]';
		$replacement_tablet_l = '425px';
		$css = str_replace($tag, $replacement_tablet_l, $css);
		$tag = '[[setting:slideshow_height_tablet]]';
		$replacement_tablet_l = '375px';
		$css = str_replace($tag, $replacement_tablet_l, $css);
		$tag = '[[setting:slideshow_height_mobile]]';
		$replacement_tablet_l = '300px';
		$css = str_replace($tag, $replacement_tablet_l, $css);
	}
	else if ($replacement == '525px') {
		$tag = '[[setting:slideshow_height_desktop_s]]';
		$replacement_tablet_l = '475px';
		$css = str_replace($tag, $replacement_tablet_l, $css);
		$tag = '[[setting:slideshow_height_tablet]]';
		$replacement_tablet_l = '425px';
		$css = str_replace($tag, $replacement_tablet_l, $css);
		$tag = '[[setting:slideshow_height_mobile]]';
		$replacement_tablet_l = '350px';
		$css = str_replace($tag, $replacement_tablet_l, $css);
	}
	else if ($replacement == '575px') {
		$tag = '[[setting:slideshow_height_desktop_s]]';
		$replacement_tablet_l = '525px';
		$css = str_replace($tag, $replacement_tablet_l, $css);
		$tag = '[[setting:slideshow_height_tablet]]';
		$replacement_tablet_l = '475px';
		$css = str_replace($tag, $replacement_tablet_l, $css);
		$tag = '[[setting:slideshow_height_mobile]]';
		$replacement_tablet_l = '400px';
		$css = str_replace($tag, $replacement_tablet_l, $css);
	}
	else if ($replacement == '425px') {
		$tag = '[[setting:slideshow_height_desktop_s]]';
		$replacement_tablet_l = '375px';
		$css = str_replace($tag, $replacement_tablet_l, $css);
		$tag = '[[setting:slideshow_height_tablet]]';
		$replacement_tablet_l = '325px';
		$css = str_replace($tag, $replacement_tablet_l, $css);
		$tag = '[[setting:slideshow_height_mobile]]';
		$replacement_tablet_l = '275px';
		$css = str_replace($tag, $replacement_tablet_l, $css);
	}
	else if ($replacement == '375px') {
		$tag = '[[setting:slideshow_height_desktop_s]]';
		$replacement_tablet_l = '325px';
		$css = str_replace($tag, $replacement_tablet_l, $css);
		$tag = '[[setting:slideshow_height_tablet]]';
		$replacement_tablet_l = '275px';
		$css = str_replace($tag, $replacement_tablet_l, $css);
		$tag = '[[setting:slideshow_height_mobile]]';
		$replacement_tablet_l = '200px';
		$css = str_replace($tag, $replacement_tablet_l, $css);
	}
	
	$tag = '[[setting:slideshow_height_responsive]]';
    $replacement = '';
    if ($slideshow_height=='responsive') {
        $replacement = '.camera_wrap {position: relative;} .camera_fakehover {min-height: 60px !important;}';
    }
    $css = str_replace($tag, $replacement, $css);
	
	$tag = '[[setting:slideshow_hide_captions]]';
    $replacement = '';
    if ($hide_captions == 1) {
        $replacement = '@media(max-width:767px){#camera_wrap .camera_caption {display: none !important;}}';
    }
    $css = str_replace($tag, $replacement, $css);
	
    return $css;
}

function theme_lambda_set_category_banner_font_color($css, $themecolor) {
    $tag = '[[setting:category_banner_font_color]]';
    $replacement = $themecolor;
    if (is_null($replacement)) {
        $replacement = '#fff';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function theme_lambda_set_category_banner_bg($css, $background) {
    global $OUTPUT;
    $tag = '[[setting:category_banner_bg]]';
    $replacement = $background;
    if (is_null($replacement)) {
		$replacement = $OUTPUT->image_url('bg/category-bg', 'theme');
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function theme_lambda_set_header_bg($css, $background, $repeat) {
    global $OUTPUT;
    $tag = '[[setting:header_bg]]';
    $replacement = 'url('.$background.')';
	$replacement .= $repeat;
    if (is_null($background)) {
        $replacement = '';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function theme_lambda_set_pagebackground($css, $pagebackground, $setting) {
    global $OUTPUT;
    $tag = '[[setting:pagebackground]]';
    $replacement = $pagebackground;
    if (is_null($replacement)) {
		$replacement = $OUTPUT->image_url('page_bg/page_bg_00', 'theme');
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function theme_lambda_set_background_repeat($css, $repeat, $size) {
    $tag = '[[setting:background-repeat]]';
    $css = str_replace($tag, $repeat, $css);
	$tag = '[[setting:background-size]]';
    $css = str_replace($tag, $size, $css);
    return $css;
}

function theme_lambda_set_border_top($css,$socials_header_bg) {
    $tag = '[[setting:border_top]]';
	$replacement = '';
	if (($socials_header_bg==3) || ($socials_header_bg==4)) {$replacement = '#wrapper {border-top: none;
}';}
	$css = str_replace($tag, $replacement, $css);
    return $css;
}

function theme_lambda_init_sidebar(moodle_page $page) {
    user_preference_allow_ajax_update('theme_lambda_sidebar', PARAM_TEXT);
    $page->requires->yui_module('moodle-theme_lambda-sidebar', 'M.theme_lambda.sidebar.init', array());
}

function theme_lambda_get_sidebar_stat() {
    return get_user_preferences('theme_lambda_sidebar', '');
}

function theme_lambda_is_jpeg(&$pict) {
    return (bin2hex($pict[0]) == 'ff' && bin2hex($pict[1]) == 'd8');
}

function theme_lambda_is_png(&$pict) {
    return (bin2hex($pict[0]) == '89' && $pict[1] == 'P' && $pict[2] == 'N' && $pict[3] == 'G');
}

function theme_lambda_getslidesize($img_loc) {
	if (theme_lambda_is_jpeg($img_loc)) {
    $handle = fopen($img_loc, "rb");
    $new_block = NULL;
    if(!feof($handle)) {
        $new_block = fread($handle, 32);
        $i = 0;
        if($new_block[$i]=="\xFF" && $new_block[$i+1]=="\xD8" && $new_block[$i+2]=="\xFF" && $new_block[$i+3]=="\xE0") {
            $i += 4;
            if($new_block[$i+2]=="\x4A" && $new_block[$i+3]=="\x46" && $new_block[$i+4]=="\x49" && $new_block[$i+5]=="\x46" && $new_block[$i+6]=="\x00") {
                $block_size = unpack("H*", $new_block[$i] . $new_block[$i+1]);
                $block_size = hexdec($block_size[1]);
                while(!feof($handle)) {
                    $i += $block_size;
                    $new_block .= fread($handle, $block_size);
                    if($new_block[$i]=="\xFF") {
                        $sof_marker = array("\xC0", "\xC1", "\xC2", "\xC3", "\xC5", "\xC6", "\xC7", "\xC8", "\xC9", "\xCA", "\xCB", "\xCD", "\xCE", "\xCF");
                        if(in_array($new_block[$i+1], $sof_marker)) {
                            $size_data = $new_block[$i+2] . $new_block[$i+3] . $new_block[$i+4] . $new_block[$i+5] . $new_block[$i+6] . $new_block[$i+7] . $new_block[$i+8];
                            $unpacked = unpack("H*", $size_data);
                            $unpacked = $unpacked[1];
                            $height = hexdec($unpacked[6] . $unpacked[7] . $unpacked[8] . $unpacked[9]);
                            $width = hexdec($unpacked[10] . $unpacked[11] . $unpacked[12] . $unpacked[13]);
                            return array($width, $height);
                        } else {
                            $i += 2;
                            $block_size = unpack("H*", $new_block[$i] . $new_block[$i+1]);
                            $block_size = hexdec($block_size[1]);
                        }
                    } else {
                        return FALSE;
                    }
                }
            }
        }
    }
    return FALSE;
	} else if (theme_lambda_is_png($img_loc)) {
		$handle = fopen( $img_loc, "rb" );
    	if ( ! feof( $handle ) ) {
        $new_block = fread( $handle, 24 );
        if ( $new_block[0] == "\x89" &&
            $new_block[1] == "\x50" &&
            $new_block[2] == "\x4E" &&
            $new_block[3] == "\x47" &&
            $new_block[4] == "\x0D" &&
            $new_block[5] == "\x0A" &&
            $new_block[6] == "\x1A" &&
            $new_block[7] == "\x0A" ) {
                if ( $new_block[12] . $new_block[13] . $new_block[14] . $new_block[15] === "\x49\x48\x44\x52" ) {
                    $width  = unpack( 'H*', $new_block[16] . $new_block[17] . $new_block[18] . $new_block[19] );
                    $width  = hexdec( $width[1] );
                    $height = unpack( 'H*', $new_block[20] . $new_block[21] . $new_block[22] . $new_block[23] );
                    $height  = hexdec( $height[1] );
                    return array( $width, $height );
                }
            }
        }
    return FALSE;
	} else {
		return getimagesize($img_loc);
	}
}