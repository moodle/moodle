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
 * Course renderer.
 *
 * @package    theme_noanme
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace theme_fordson\output\core;
defined('MOODLE_INTERNAL') || die();
use moodle_url;
use lang_string;
use coursecat_helper;
use core_course_category;
use stdClass;
use core_course_list_element;
use context_course;
use context_system;
use pix_url;
use html_writer;
use heading;
use pix_icon;
use image_url;
use single_select;
require_once ($CFG->dirroot . '/course/renderer.php');
global $PAGE;
/**
 * Course renderer class.
 *
 * @package    theme_noanme
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
if ($PAGE->theme->settings->coursetilestyle < 10) {
    class course_renderer extends \core_course_renderer  {
        protected $countcategories = 0;

        public function view_available_courses($id = 0, $courses = null, $totalcount = null) {
            /* available courses */
            global $CFG, $OUTPUT, $PAGE;
            
            $rcourseids = array_keys($courses);
            $acourseids = array_chunk($rcourseids, 3);
            if ($PAGE->theme->settings->coursetilestyle == 8) {
                $acourseids = array_chunk($rcourseids, 2);
            }
            if ($id != 0) {
                $newcourse = get_string('availablecourses');
            }
            else {
                $newcourse = null;
            }
            $header = '
                <div id="category-course-list">
                    <div class="courses category-course-list-all">
                    
                    <div class="class-list">
                        <h4>' . $newcourse . '</h4>
                    </div>';
            $content = '';
            $footer = '<hr>
                   </div>
                </div>';
            if (count($rcourseids) > 0) {
                foreach ($acourseids as $courseids) {
                    $content .= '<div class="container-fluid"> <div class="row">';
                    $rowcontent = '';
                    foreach ($courseids as $courseid) {
                        $course = get_course($courseid);
                        $trimtitlevalue = $PAGE->theme->settings->trimtitle;
                        $trimsummaryvalue = $PAGE->theme->settings->trimsummary;
                        $summary = theme_fordson_strip_html_tags($course->summary);
                        $summary = format_text(theme_fordson_course_trim_char($summary, $trimsummaryvalue));
                        $trimtitle = format_string(theme_fordson_course_trim_char($course->fullname, $trimtitlevalue));
                        $noimgurl = $OUTPUT->image_url('noimg', 'theme');
                        $courseurl = new moodle_url('/course/view.php', array(
                            'id' => $courseid
                        ));

                        
                        $systemcontext = $PAGE->bodyid;
                        // Course completion Progress bar
                        if (\core_completion\progress::get_course_progress_percentage($course) && isloggedin() && $systemcontext == 'page-site-index') {
                            $comppc = \core_completion\progress::get_course_progress_percentage($course);
                            $comppercent = number_format($comppc, 0);
                            $hasprogress = true;
                        }else {
                            $comppercent = 0;
                            $hasprogress = false;
                        }

				        // Course completion Progress bar
				        if ($course->enablecompletion == 1 && isloggedin() && $systemcontext == 'page-site-index') {
				        	$completiontext = get_string('coursecompletion', 'completion');
				        	$compbar = "<div class='progress'>";
				            $compbar .= "<div class='progress-bar progress-bar-info barfill' role='progressbar' aria-valuenow='{$comppercent}' ";
				            $compbar .= " aria-valuemin='0' aria-valuemax='100' style='width: {$comppercent}%;'>";
				            $compbar .= "{$comppercent}%";
				            $compbar .= "</div>";
				            $compbar .= "</div>";
				            $progressbar = $compbar;
				        } else {	
				        	$progressbar = '';
				        	$completiontext = '';
				        }
	                    if ($course instanceof stdClass) {
	                        $course = new core_course_list_element($course);
	                    }
                        // print enrolmenticons
                        $pixcontent = '';
                        if ($icons = enrol_get_course_info_icons($course)) {
                            $pixcontent .= html_writer::start_tag('div', array('class' => 'enrolmenticons'));
                            foreach ($icons as $pix_icon) {
                                $pixcontent .= $this->render($pix_icon);
                            }
                            $pixcontent .= html_writer::end_tag('div'); 
                        }
                        // display course category if necessary (for example in search results)
                        if ($cat = core_course_category::get($course->category, IGNORE_MISSING)) {
                            $catcontent = html_writer::start_tag('div', array('class' => 'coursecat'));
                            $catcontent .= get_string('category').': '.
                                    html_writer::link(new moodle_url('/course/index.php', array('categoryid' => $cat->id)),
                                            $cat->get_formatted_name(), array('class' => $cat->visible ? '' : 'dimmed'));
                            $catcontent .= $pixcontent;
                            $catcontent .= html_writer::end_tag('div');
                            
                        }


                        
                        // Load from config if usea a img from course summary file if not exist a img then a default one ore use a fa-icon.
                        $imgurl = '';
                        $context = context_course::instance($course->id);
                        foreach ($course->get_course_overviewfiles() as $file) {
                            $isimage = $file->is_valid_image();
                            $imgurl = file_encode_url("$CFG->wwwroot/pluginfile.php", '/' . $file->get_contextid() . '/' . $file->get_component() . '/' . $file->get_filearea() . $file->get_filepath() . $file->get_filename() , !$isimage);
                            if (!$isimage) {
                                $imgurl = $noimgurl;
                            }
                        }
                        if (empty($imgurl)) {
                            $imgurl = $PAGE->theme->setting_file_url('headerdefaultimage', 'headerdefaultimage', true);
                            if (!$imgurl) {
                                $imgurl = $noimgurl;
                            }
                        }
                        
                        $customfieldcontent = '';

                        // Display custom fields.
				        if ($course->has_custom_fields()) {
				            $handler = \core_course\customfield\course_handler::create();
				            $customfields = $handler->display_custom_fields_data($course->get_custom_fields());
				            $customfieldcontent = \html_writer::tag('div', $customfields, ['class' => 'customfields-container']);
				        }
                        

                        if ($PAGE->theme->settings->coursetilestyle == 1) {
                            $rowcontent .= '
                        <div class="col-md-4">';
                            $rowcontent .= html_writer::start_tag('div', array(
                                'class' => $course->visible ? 'coursevisible' : 'coursedimmed1'
                            ));
                            $rowcontent .= '
                            <div class="class-box">
                                ';
                            if ($PAGE->theme->settings->titletooltip) {
                                $tooltiptext = 'data-tooltip="tooltip" data-placement= "top" title="' . format_string($course->fullname) . '"';
                            }
                            else {
                                $tooltiptext = '';
                            }
                            
                            $rowcontent .= '
                                    <a ' . $tooltiptext . ' href="' . $courseurl . '">
                                    <div class="courseimagecontainer">
                                    <div class="course-image-view" style="background-image: url(' . $imgurl . ');background-repeat: no-repeat;background-size:cover; background-position:center;">
                                    </div>
                                    <div class="course-overlay">
                                    <i class="fa fa-arrow-circle-right" aria-hidden="true"></i>
                                    </div>
                                    
                                    </div>
                                    <div class="course-title">
                                    <h4>' . $trimtitle . '</h4>
                                    </div>
                                    </a>
                                    <div class="course-summary">
                                    ' . $catcontent . '
                                    ' . $customfieldcontent . '
                                    ';
                            if ($course->has_course_contacts()) {
                                $rowcontent .= html_writer::start_tag('ul', array(
                                    'class' => 'teacherscourseview'
                                ));
                                foreach ($course->get_course_contacts() as $userid => $coursecontact) {
                                    $name = $coursecontact['rolename'] . ': ' . $coursecontact['username'];
                                    $rowcontent .= html_writer::tag('li', $name);
                                }
                                $rowcontent .= html_writer::end_tag('ul');
                            }
                            $rowcontent .= '
                            		
                                    </div>
                                </div>
                        </div>
                        </div>';
                        }
                        if ($PAGE->theme->settings->coursetilestyle == 2) {
                            // display course contacts. See core_course_list_element::get_course_contacts().
                            $enrollbutton = get_string('enrollcoursecard', 'theme_fordson');
                            $rowcontent .= '
                    <div class="col-md-4">
                        ';
                            $rowcontent .= '
                    <div class="tilecontainer">
                            <figure class="coursestyle2">
                                <div class="class-box-courseview" style="background-image: url(' . $imgurl . ');background-repeat: no-repeat;background-size:cover; background-position:center;">
                                
                                ';
                            if ($PAGE->theme->settings->titletooltip) {
                                $tooltiptext = 'data-toggle="tooltip" data-placement= "top" title="' . format_string($course->fullname) . '"';
                            }
                            else {
                                $tooltiptext = '';
                            }
                            $rowcontent .= html_writer::start_tag('div', array(
                                'class' => $course->visible ? 'coursevisible' : 'coursedimmed2'
                            ));
                            $rowcontent .= '
                                <figcaption>
                                    <h3>' . $trimtitle . '</h3>
                                    <div class="course-card">
                                    ' . $catcontent . '
                                    ' . $customfieldcontent . '
                                    <button type="button" class="btn btn-primary btn-sm coursestyle2btn">' . $enrollbutton . '   <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></button>
                                    ';
                            if ($course->has_course_contacts()) {
                                $rowcontent .= html_writer::start_tag('ul', array(
                                    'class' => 'teacherscourseview'
                                ));
                                foreach ($course->get_course_contacts() as $userid => $coursecontact) {
                                    $name = $coursecontact['rolename'] . ': ' . $coursecontact['username'];
                                    $rowcontent .= html_writer::tag('li', $name);
                                }
                                $rowcontent .= html_writer::end_tag('ul');
                            }
                            $rowcontent .= '
                                </div>

                                </figcaption>
                                    <a ' . $tooltiptext . ' href="' . $courseurl . '" class="coursestyle2url"></a>
                                </div>
                            </figure>
                    </div>
                    </div>
                        ';
                        }
                        if ($PAGE->theme->settings->coursetilestyle == 3) {
                            if ($PAGE->theme->settings->titletooltip) {
                                $tooltiptext = 'data-toggle="tooltip" data-placement= "top" title="' . format_string($course->fullname) . '"';
                            }
                            else {
                                $tooltiptext = '';
                            }
                            $rowcontent .= '
	                        <div class="col-md-4">
	                        <div class="tilecontainer">
	                            <div class="class-box-fp-style3" style="background-image: url(' . $imgurl . ');background-repeat: no-repeat;background-size:cover; background-position:center;">
	                                ';
                            $rowcontent .= html_writer::start_tag('div', array(
                                'class' => $course->visible ? 'coursevisible' : 'coursedimmed3'
                            ));
                            $rowcontent .= '
                                    <div class="course-title">
                                    <a ' . $tooltiptext . ' href="' . $courseurl . '"><h4>' . $trimtitle . '</h4></a>
                                    ' . $catcontent . '
                                    ' . $customfieldcontent . '
                                    <div class="completiontextposition">' . $completiontext . '</div>
                                    </div>
                                    '. $progressbar . '
                                    
                                    </div>
                                    
                                </div>
                               </div> 

                        </div>';
                        }
                        if ($PAGE->theme->settings->coursetilestyle == 4) {
                            $rowcontent .= '
                        <div class="col-md-4">';
                            $rowcontent .= html_writer::start_tag('div', array(
                                'class' => $course->visible ? 'coursevisible' : 'coursedimmed4'
                            ));
                            $rowcontent .= '
                            <div class="class-box4">
                                ';
                            if ($PAGE->theme->settings->titletooltip) {
                                $tooltiptext = 'data-toggle="tooltip" data-placement= "top" title="' . format_string($course->fullname) . '"';
                            }
                            else {
                                $tooltiptext = '';
                            }
                            $rowcontent .= '
                                    <a ' . $tooltiptext . ' href="' . $courseurl . '">
                                    <div class="courseimagecontainer">
                                    <div class="course-image-view" style="background-image: url(' . $imgurl . ');background-repeat: no-repeat;background-size:cover; background-position:center;">
                                    </div>
                                    <div class="course-overlay">
                                    <i class="fa fa-arrow-circle-right" aria-hidden="true"></i>
                                    </div>
                                    
                                    </div>
                                    <div class="course-title4">
                                    <h4>' . $trimtitle . '</h4>
                                    </div>
                                    </a>
                                    <div class="course-summary4">
                                    ' . $catcontent . '
                                    ' . $customfieldcontent . '
                                    ' . $summary . '
                                    ';
                            if ($course->has_course_contacts()) {
                                $rowcontent .= html_writer::start_tag('ul', array(
                                    'class' => 'teacherscourseview'
                                ));
                                foreach ($course->get_course_contacts() as $userid => $coursecontact) {
                                    $name = $coursecontact['rolename'] . ': ' . $coursecontact['username'];
                                    $rowcontent .= html_writer::tag('li', $name);
                                }
                                $rowcontent .= html_writer::end_tag('ul');
                            }
                            $rowcontent .= '
                                    </div>
                                </div>
                        </div>
                        </div>';
                        }
                        if ($PAGE->theme->settings->coursetilestyle == 5) {
                            $rowcontent .= html_writer::start_tag('div', array(
                                'class' => $course->visible ? 'col-12 d-flex flex-sm-row flex-column class-fullbox hoverhighlight coursevisible' : 'col-12 d-flex flex-sm-row flex-column class-fullbox hoverhighlight coursedimmed1'
                            ));
                            if ($PAGE->theme->settings->titletooltip) {
                                $tooltiptext = 'data-toggle="tooltip" data-placement= "top" title="' . format_string($course->fullname) . '"';
                            }
                            else {
                                $tooltiptext = '';
                            }
                            $rowcontent .= '
                            <div class="col-md-2">
                                <a ' . $tooltiptext . ' href="' . $courseurl . '">
                                   <img src="' . $imgurl . '" class="img-fluid" alt="Responsive image" width="200px">
                                </a>
                            </div>';
                            $rowcontent .= '
                            <div class="col-md-4">';
                            $rowcontent .= '
                                <a ' . $tooltiptext . ' href="' . $courseurl . '">
                                    <div class="course-title-fullbox">
                                        <h4>' . $trimtitle . '</h4>
                                </a>
                                
                                </div>';
                            if ($course->has_course_contacts()) {
                                $rowcontent .= html_writer::start_tag('ul', array(
                                    'class' => 'teacherscourseview'
                                ));
                                foreach ($course->get_course_contacts() as $userid => $coursecontact) {
                                    $name = $coursecontact['rolename'] . ': ' . $coursecontact['username'];
                                    $rowcontent .= html_writer::tag('li', $name);
                                }
                                $rowcontent .= html_writer::end_tag('ul');
                            }
                            $rowcontent .= '</div>';
                            $rowcontent .= '<div class="col-md-6">
                                    <div class="course-summary">
                                    ' . $catcontent . '
                                    ' . $customfieldcontent . '
                                    ' . $summary . '
                                    </div> 
                                    </div> ';
                            $rowcontent .= html_writer::end_tag('div');
                        }
                        if ($PAGE->theme->settings->coursetilestyle == 6) {
                            if ($PAGE->theme->settings->titletooltip) {
                                $tooltiptext = 'data-toggle="tooltip" data-placement= "top" title="' . format_string($course->fullname) . '"';
                            }
                            else {
                                $tooltiptext = '';
                            }
                            $rowcontent .= '
                        <div class="col-md-12">
                            <div class="class-fullbox" style="background-image: url(' . $imgurl . ');background-repeat: no-repeat;background-size:cover; background-position:center;">
                                <div class="fullbox">
                                ';
                            $rowcontent .= html_writer::start_tag('div', array(
                                'class' => $course->visible ? 'coursevisible' : 'coursedimmed3'
                            ));
                            $rowcontent .= '
                                <div class="course-info-inner"> 
                                    <div class="course-title-fullboxbkg">
                                        <h4><a href="' . $courseurl . '">' . $trimtitle . '</a></h4>
                                        ' . $catcontent . '
                                        ' . $customfieldcontent . '
                                    </div>
                                </div>
                                ';
                            $rowcontent .= '<div class="d-flex flex-sm-row flex-column coursedata">';
                            if ($course->has_course_contacts()) {
                                $rowcontent .= '<div class="col-md-6">';
                                $rowcontent .= html_writer::start_tag('ul', array(
                                    'class' => 'teacherscourseview'
                                ));
                                foreach ($course->get_course_contacts() as $userid => $coursecontact) {
                                    $name = $coursecontact['rolename'] . ': ' . $coursecontact['username'];
                                    $rowcontent .= html_writer::tag('li', $name);
                                }
                                $rowcontent .= html_writer::end_tag('ul');
                                $rowcontent .= '</div>';
                            }
                            $rowcontent .= '<div class="col-md-6">
                                    <div class="course-summary">
                                    ' . $summary . '
                                    </div> 
                                    </div> </div></div>';
                            $rowcontent .= '
                                        </div>
                                    
                                </div>
                        </div>';
                        }
                        if ($PAGE->theme->settings->coursetilestyle == 7) {
                            if ($PAGE->theme->settings->titletooltip) {
                                $tooltiptext = 'data-toggle="tooltip" data-placement= "top" title="' . format_string($course->fullname) . '"';
                            }
                            else {
                                $tooltiptext = '';
                            }
                            $rowcontent .= '
                        <div class="col-md-12">
                            <div class="class-fullbox7" style="background-image: url(' . $imgurl . ');background-repeat: no-repeat;background-size:cover; background-position:center; background-color: rgba(0,0,0,0.3);
    background-blend-mode: overlay;">
                            <div class="fullbox7">
                                ';
                            $rowcontent .= '<div class="course-info-inner">';
                            $rowcontent .= html_writer::start_tag('div', array(
                                'class' => $course->visible ? 'coursevisible course-title-fullboxbkg7 d-flex flex-sm-row flex-column' : 'course-title-fullboxbkg coursedimmed3 d-flex flex-sm-row flex-column'
                            ));
                            $rowcontent .= '<div class="col-md-6">
                                    <h4><a href="' . $courseurl . '">' . $trimtitle . '</a></h4>
                                    ' . $catcontent . '
                                    ' . $customfieldcontent . '
                                    </div>';
                            if ($course->has_course_contacts()) {
                                $rowcontent .= '<div class="col-md-6">';
                                $rowcontent .= html_writer::start_tag('ul', array(
                                    'class' => 'teacherscourseview'
                                ));
                                foreach ($course->get_course_contacts() as $userid => $coursecontact) {
                                    $name = $coursecontact['rolename'] . ': ' . $coursecontact['username'];
                                    $rowcontent .= html_writer::tag('li', $name);
                                }
                                $rowcontent .= html_writer::end_tag('ul');
                                $rowcontent .= '</div>';
                            }
                            $rowcontent .= '</div>
                                     </div>
                                    
                                    </div>
                                </div>
                        </div>';
                        }
                        if ($PAGE->theme->settings->coursetilestyle == 8) {

                            if ($PAGE->theme->settings->titletooltip) {
                                $tooltiptext = 'data-toggle="tooltip" data-placement= "top" title="' . format_string($course->fullname) . '"';
                            }
                            else {
                                $tooltiptext = '';
                            }
                            $rowcontent .= '
                                <div class="col-lg-6">
                                <div class="tilecontainer">';
                            $rowcontent .= html_writer::start_tag('div', array(
                                'class' => $course->visible ? 'coursevisible' : 'coursedimmed3'
                            ));
                            $rowcontent .= '<div class="class-box-fp-2col" style="background-image: url(' . $imgurl . ');background-repeat: no-repeat;background-size:cover; background-position:center;">
                                <a ' . $tooltiptext . ' href="' . $courseurl . '" class="coursestyle3url">';
                            $rowcontent .= '
                                    <div class="course-title-2col">
                                    
                                    <h4><a href="' . $courseurl . '">' . $trimtitle . '</a></h4>
                                    ' . $catcontent . '
                                    ' . $customfieldcontent . '
                                    </div>
                                    <div class="course-summary-2col">
                                    ' . $summary . '
                                    </div>
                                    </div>
                                    </a>
                                </div>
                               </div> 
                        </div>';
                        }
                        if ($PAGE->theme->settings->coursetilestyle == 9) {
                            if ($PAGE->theme->settings->titletooltip) {
                                $tooltiptext = 'data-toggle="tooltip" data-placement= "top" title="' . format_string($course->fullname) . '"';
                            }
                            else {
                                $tooltiptext = '';
                            }
                            
                            $rowcontent .= html_writer::start_tag('div', array(
                                'class' => $course->visible ? 'coursevisible col-md-12 d-flex flex-sm-row flex-column coursestyle9row' : 'coursedimmed9 col-md-12 d-flex flex-sm-row flex-column coursestyle9row'
                            ));
                            $rowcontent .= '
                                <div class="col-md-6">
                            	<h4><a href="' . $courseurl . '">' . $trimtitle . '</a></h4>';
                            if ($systemcontext !== 'page-site-index') {
                            	$rowcontent .= '<div class="course-summary">
	                                    ' . $summary . '
	                            </div>';
                            }
	                        $rowcontent .= '</div>';
		                        if ($systemcontext !== 'page-site-index') {
		                            $rowcontent .= ' 
		                            	<div class="col-md-6 row">
			                            	<div class="col-md-6">
			                                  ' . $catcontent . '
			                                  ' . $customfieldcontent . '
			                                </div>
			                                <div class="col-md-6">';
			                        if ($course->has_course_contacts()) {
		                                $rowcontent .= html_writer::start_tag('ul', array(
		                                    'class' => 'teacherscourseview'
		                                ));
		                                foreach ($course->get_course_contacts() as $userid => $coursecontact) {
		                                    $name = $coursecontact['rolename'] . ': ' . $coursecontact['username'];
		                                    $rowcontent .= html_writer::tag('li', $name);
		                                }
		                                $rowcontent .= html_writer::end_tag('ul');
		                            }
			                                  
			                        $rowcontent .= '
			                        	</div>
		                                </div>';
	                            }
	                        if ($systemcontext == 'page-site-index' && $course->enablecompletion == 1) {
	                        	
	                            $rowcontent .= '
	                            	<div class="col-md-6 row">
		                            	<div class="col-md-4 text-right">
		                                  ' . $completiontext  . '
		                                </div>
		                                <div class="col-md-8">
		                                  '. $progressbar . '
		                                </div>
	                                </div>';
	                        }
                            $rowcontent .= '
                            
	                        </div>';
                        }
                        
                    }
                    $content .= $rowcontent;
                    $content .= '</div> </div>';
                }
            }
            $coursehtml = $header . $content . $footer;
            return $coursehtml;
        }
        
        /**
         * Returns HTML to display the subcategories and courses in the given category
         *
         * This method is re-used by AJAX to expand content of not loaded category
         *
         * @param coursecat_helper $chelper various display options
         * @param coursecat $coursecat
         * @param int $depth depth of the category in the current tree
         * @return string
         */

        protected function coursecat_category(coursecat_helper $chelper, $coursecat, $depth) {
            if (!theme_fordson_get_setting('enablecategoryicon')) {
                return parent::coursecat_category($chelper, $coursecat, $depth);
            }
            global $CFG, $OUTPUT;
            $classes = array(
                'category'
            );
            if (empty($coursecat->visible)) {
                $classes[] = 'dimmed_category';
            }
            if ($chelper->get_subcat_depth() > 0 && $depth >= $chelper->get_subcat_depth()) {
                $categorycontent = '';
                $classes[] = 'notloaded';
                if ($coursecat->get_children_count() || ($chelper->get_show_courses() >= self::COURSECAT_SHOW_COURSES_COLLAPSED && $coursecat->get_courses_count())) {
                    $classes[] = 'with_children';
                    $classes[] = 'collapsed';
                }
            }
            else {
                $categorycontent = $this->coursecat_category_content($chelper, $coursecat, $depth);
                $classes[] = 'loaded';
                if (!empty($categorycontent)) {
                    $classes[] = 'with_children';
                }
            }
            $totalcount = core_course_category::get(0)->get_children_count();
            $content = '';
            if ($this->countcategories == 0 || ($this->countcategories % 3) == 0) {
                if (($this->countcategories % 3) == 0 && $totalcount != $this->countcategories) {
                    $content .= '</div> </div>';
                }
                if ($totalcount != $this->countcategories || $this->countcategories == 0) {
                    $categoryparam = optional_param('categoryid', 0, PARAM_INT);
                    if ($categoryparam) {
                        $content .= $OUTPUT->heading(get_string('categories'));
                    }
                    $content .= '<div class="container-fluid"><div class="row">';
                }
            }
            $classes[] = 'col-md-3 box-class';
            $content = '<div class="' . join(' ', $classes) . '" data-categoryid="' . $coursecat->id . '" data-depth="' . $depth . '" data-showcourses="' . $chelper->get_show_courses() . '" data-type="' . self::COURSECAT_TYPE_CATEGORY . '">';
            $content .= '<div class="cat-icon">';
            $val = theme_fordson_get_setting('catsicon');
            $url = new moodle_url('/course/index.php', array(
                'categoryid' => $coursecat->id
            ));
            $content .= '<a href="' . $url . '">';
            $content .= '<i class="fa fa-5x fa-' . $val . '"></i>';
            $categoryname = $coursecat->get_formatted_name();
            $content .= '<div>';
            $content .= '<div class="info-enhanced">';
            $content .= '<span class="class-category">' . $categoryname . '</span>';
            if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_COUNT) {
                $coursescount = $coursecat->get_courses_count();
                $content .= '  <span class="numberofcourses" title="' . get_string('numberofcourses') . '">(' . $coursescount . ')</span>';
            }
            $content .= '</div>';
            $content .= '</div>';
            $content .= '</a>';
            $content .= '</div>';
            $content .= '</div>';
            if ($totalcount == $this->countcategories) {
            }
            ++$this->countcategories;
            // category name

            return $content;
        }
        /**
         * Renders the list of subcategories in a category
         *
         * @param coursecat_helper $chelper various display options
         * @param coursecat $coursecat
         * @param int $depth depth of the category in the current tree
         * @return string
         */

        protected function coursecat_subcategories(coursecat_helper $chelper, $coursecat, $depth) {
            if (!theme_fordson_get_setting('enablecategoryicon')) {
                return parent::coursecat_subcategories($chelper, $coursecat, $depth);
            }
            global $CFG;
            $subcategories = array();
            if (!$chelper->get_categories_display_option('nodisplay')) {
                $subcategories = $coursecat->get_children($chelper->get_categories_display_options());
            }
            $totalcount = $coursecat->get_children_count();
            if (!$totalcount) {
                // Note that we call coursecat::get_children_count() AFTER coursecat::get_children() to avoid extra DB requests.
                // Categories count is cached during children categories retrieval.
                return '';
            }
            // prepare content of paging bar or more link if it is needed
            $paginationurl = $chelper->get_categories_display_option('paginationurl');
            $paginationallowall = $chelper->get_categories_display_option('paginationallowall');
            if ($totalcount > count($subcategories)) {
                if ($paginationurl) {
                    // the option 'paginationurl was specified, display pagingbar
                    $perpage = $chelper->get_categories_display_option('limit', $CFG->coursesperpage);
                    $page = $chelper->get_categories_display_option('offset') / $perpage;
                    $pagingbar = $this->paging_bar($totalcount, $page, $perpage, $paginationurl->out(false, array(
                        'perpage' => $perpage
                    )));
                    if ($paginationallowall) {
                        $pagingbar .= html_writer::tag('div', html_writer::link($paginationurl->out(false, array(
                            'perpage' => 'all'
                        )) , get_string('showall', '', $totalcount)) , array(
                            'class' => 'paging paging-showall'
                        ));
                    }
                }
                else if ($viewmoreurl = $chelper->get_categories_display_option('viewmoreurl')) {
                    // the option 'viewmoreurl' was specified, display more link (if it is link to category view page, add category id)
                    if ($viewmoreurl->compare(new moodle_url('/course/index.php') , URL_MATCH_BASE)) {
                        $viewmoreurl->param('categoryid', $coursecat->id);
                    }
                    $viewmoretext = $chelper->get_categories_display_option('viewmoretext', new lang_string('viewmore'));
                    $morelink = html_writer::tag('div', html_writer::link($viewmoreurl, $viewmoretext) , array(
                        'class' => 'paging paging-morelink'
                    ));
                }
            }
            else if (($totalcount > $CFG->coursesperpage) && $paginationurl && $paginationallowall) {
                // there are more than one page of results and we are in 'view all' mode, suggest to go back to paginated view mode
                $pagingbar = html_writer::tag('div', html_writer::link($paginationurl->out(false, array(
                    'perpage' => $CFG->coursesperpage
                )) , get_string('showperpage', '', $CFG->coursesperpage)) , array(
                    'class' => 'paging paging-showperpage'
                ));
            }
            // display list of subcategories
            $content = html_writer::start_tag('div', array(
                'class' => 'subcategories d-flex flex-wrap'
            ));
            if (!empty($pagingbar)) {
                $content .= $pagingbar;
            }
            foreach ($subcategories as $subcategory) {
                $content .= $this->coursecat_category($chelper, $subcategory, $depth + 1);
            }
            if (!empty($pagingbar)) {
                $content .= $pagingbar;
            }
            if (!empty($morelink)) {
                $content .= $morelink;
            }
            $content .= html_writer::end_tag('div');
            return $content;
        }

        protected function coursecat_courses(coursecat_helper $chelper, $courses, $totalcount = null) {
            global $CFG;
            if ($totalcount === null) {
                $totalcount = count($courses);
            }
            if (!$totalcount) {
                // Courses count is cached during courses retrieval.
                return '';
            }
            if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_AUTO) {
                if ($totalcount <= $CFG->courseswithsummarieslimit) {
                    $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED);
                }
                else {
                    $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_COLLAPSED);
                }
            }
            $paginationurl = $chelper->get_courses_display_option('paginationurl');
            $paginationallowall = $chelper->get_courses_display_option('paginationallowall');
            if ($totalcount > count($courses)) {
                if ($paginationurl) {
                    $perpage = $chelper->get_courses_display_option('limit', $CFG->coursesperpage);
                    $page = $chelper->get_courses_display_option('offset') / $perpage;
                    $pagingbar = $this->paging_bar($totalcount, $page, $perpage, $paginationurl->out(false, array(
                        'perpage' => $perpage
                    )));
                    if ($paginationallowall) {
                        $pagingbar .= html_writer::tag('div', html_writer::link($paginationurl->out(false, array(
                            'perpage' => 'all'
                        )) , get_string('showall', '', $totalcount)) , array(
                            'class' => 'paging paging-showall'
                        ));
                    }
                }
                else if ($viewmoreurl = $chelper->get_courses_display_option('viewmoreurl')) {
                    $viewmoretext = $chelper->get_courses_display_option('viewmoretext', new lang_string('viewmore'));
                    $morelink = html_writer::tag('div', html_writer::tag('a', html_writer::start_tag('i', array(
                        'class' => 'fa-graduation-cap' . ' fa fa-fw'
                    )) . html_writer::end_tag('i') . $viewmoretext, array(
                        'href' => $viewmoreurl,
                        'class' => 'btn btn-primary coursesmorelink'
                    )) , array(
                        'class' => 'paging paging-morelink'
                    ));
                }
            }
            else if (($totalcount > $CFG->coursesperpage) && $paginationurl && $paginationallowall) {
                $pagingbar = html_writer::tag('div', html_writer::link($paginationurl->out(false, array(
                    'perpage' => $CFG->coursesperpage
                )) , get_string('showperpage', '', $CFG->coursesperpage)) , array(
                    'class' => 'paging paging-showperpage'
                ));
            }
            $attributes = $chelper->get_and_erase_attributes('courses');
            $content = html_writer::start_tag('div', $attributes);
            if (!empty($pagingbar)) {
                $content .= $pagingbar;
            }
            $categoryid = optional_param('categoryid', 0, PARAM_INT);
            $coursecount = 0;
            $content .= $this->view_available_courses($categoryid, $courses, $totalcount);
            if (!empty($pagingbar)) {
                $content .= $pagingbar;
            }
            if (!empty($morelink)) {
                $content .= $morelink;
            }
            $content .= html_writer::end_tag('div');
            $content .= '<div class="clearfix"></div>';
            return $content;
        }

        protected static function timeaccesscompare($a, $b) {
            // Timeaccess is lastaccess entry and timestart an enrol entry.
            if ((!empty($a->timeaccess)) && (!empty($b->timeaccess))) {
                // Both last access.
                if ($a->timeaccess == $b->timeaccess) {
                    return 0;
                }
                return ($a->timeaccess > $b->timeaccess) ? -1 : 1;
            }
            else if ((!empty($a->timestart)) && (!empty($b->timestart))) {
                // Both enrol.
                if ($a->timestart == $b->timestart) {
                    return 0;
                }
                return ($a->timestart > $b->timestart) ? -1 : 1;
            }
            // Must be comparing an enrol with a last access.
            // -1 is to say that 'a' comes before 'b'.
            if (!empty($a->timestart)) {
                // 'a' is the enrol entry.
                return -1;
            }
            // 'b' must be the enrol entry.
            return 1;
        }

        public function frontpage_my_courses() {
            global $USER, $CFG, $DB;

            if (!isloggedin() or isguestuser()) {
                return '';
            }

            $nomycourses = '<div class="alert alert-info alert-block">' . get_string('nomycourses', 'theme_fordson') . '</div>';
            $lastaccess = '';
            $output = '';

            if (theme_fordson_get_setting('frontpagemycoursessorting')) {
                $courses = enrol_get_my_courses(null, 'sortorder ASC');
                if ($courses) {
                    // We have something to work with.  Get the last accessed information for the user and populate.
                    global $DB, $USER;
                    $lastaccess = $DB->get_records('user_lastaccess', array('userid' => $USER->id) , '', 'courseid, timeaccess');
                    if ($lastaccess) {
                        foreach ($courses as $course) {
                            if (!empty($lastaccess[$course->id])) {
                                $course->timeaccess = $lastaccess[$course->id]->timeaccess;
                            }
                        }
                    }
                    // Determine if we need to query the enrolment and user enrolment tables.
                    $enrolquery = false;
                    foreach ($courses as $course) {
                        if (empty($course->timeaccess)) {
                            $enrolquery = true;
                            break;
                        }
                    }
                    if ($enrolquery) {
                        // We do.
                        $params = array(
                            'userid' => $USER->id
                        );
                        $sql = "SELECT ue.id, e.courseid, ue.timestart
                            FROM {enrol} e
                            JOIN {user_enrolments} ue ON (ue.enrolid = e.id AND ue.userid = :userid)";
                        $enrolments = $DB->get_records_sql($sql, $params, 0, 0);
                        if ($enrolments) {
                            // Sort out any multiple enrolments on the same course.
                            $userenrolments = array();
                            foreach ($enrolments as $enrolment) {
                                if (!empty($userenrolments[$enrolment->courseid])) {
                                    if ($userenrolments[$enrolment->courseid] < $enrolment->timestart) {
                                        // Replace.
                                        $userenrolments[$enrolment->courseid] = $enrolment->timestart;
                                    }
                                }
                                else {
                                    $userenrolments[$enrolment->courseid] = $enrolment->timestart;
                                }
                            }
                            // We don't need to worry about timeend etc. as our course list will be valid for the user from above.
                            foreach ($courses as $course) {
                                if (empty($course->timeaccess)) {
                                    $course->timestart = $userenrolments[$course->id];
                                }
                            }
                        }
                    }
                    uasort($courses, array($this,'timeaccesscompare'));
                }
                else {
                    return $nomycourses;
                }
                $sortorder = $lastaccess;
            }
            else if (!empty($CFG->navsortmycoursessort)) {
                // sort courses the same as in navigation menu
                $sortorder = 'visible DESC,' . $CFG->navsortmycoursessort . ' ASC';
                $courses = enrol_get_my_courses('summary, summaryformat', $sortorder);
                if (!$courses) {
                    return $nomycourses;
                }
            }
            else {
                $sortorder = 'visible DESC,sortorder ASC';
                $courses = enrol_get_my_courses('summary, summaryformat', $sortorder);
                if (!$courses) {
                    return $nomycourses;
                }
            }
            $rhosts = array();
            $rcourses = array();
            if (!empty($CFG->mnet_dispatcher_mode) && $CFG->mnet_dispatcher_mode === 'strict') {
                $rcourses = get_my_remotecourses($USER->id);
                $rhosts = get_my_remotehosts();
            }
            if (!empty($courses) || !empty($rcourses) || !empty($rhosts)) {
                $chelper = new coursecat_helper();
                if (count($courses) > $CFG->frontpagecourselimit) {
                    // There are more enrolled courses than we can display, display link to 'My courses'.
                    $totalcount = count($courses);
                    $courses = array_slice($courses, 0, $CFG->frontpagecourselimit, true);
                    $chelper->set_courses_display_options(array(
                        'viewmoreurl' => new moodle_url('/my/') ,
                        'viewmoretext' => new lang_string('mycourses')
                    ));
                }
                else {
                    // All enrolled courses are displayed, display link to 'All courses' if there are more courses in system.
                    $chelper->set_courses_display_options(array(
                        'viewmoreurl' => new moodle_url('/course/index.php') ,
                        'viewmoretext' => new lang_string('fulllistofcourses')
                    ));
                    $totalcount = $DB->count_records('course') - 1;
                }
                $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED)->set_attributes(array(
                    'class' => 'frontpage-course-list-enrolled'
                ));
                $output .= $this->coursecat_courses($chelper, $courses, $totalcount);
                // MNET
                if (!empty($rcourses)) {
                    // at the IDP, we know of all the remote courses
                    $output .= html_writer::start_tag('div', array(
                        'class' => 'courses'
                    ));
                    foreach ($rcourses as $course) {
                        $output .= $this->frontpage_remote_course($course);
                    }
                    $output .= html_writer::end_tag('div'); // .courses
                    
                }
                elseif (!empty($rhosts)) {
                    // non-IDP, we know of all the remote servers, but not courses
                    $output .= html_writer::start_tag('div', array(
                        'class' => 'courses'
                    ));
                    foreach ($rhosts as $host) {
                        $output .= $this->frontpage_remote_host($host);
                    }
                    $output .= html_writer::end_tag('div'); // .courses
                    
                }
            }
            return $output;
        }

    }
}