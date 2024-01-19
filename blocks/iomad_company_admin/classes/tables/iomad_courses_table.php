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
 * Base class for the table used by a {@link quiz_attempts_report}.
 *
 * @package   local_report_user_license_allocations
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_iomad_company_admin\tables;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');

use \table_sql;
use \iomad;
use \moodle_url;
use \single_select;
use \html_writer;

class iomad_courses_table extends table_sql {

    /**
     * Generate the display of the user's firstname
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_company($row) {
        global $output, $DB;

        $companies = $DB->get_records_sql("SELECT c.id,c.shortname FROM {company} c
                                           JOIN {company_course} cc ON (c.id = cc.companyid)
                                           WHERE cc.courseid = :courseid",
                                           array('courseid' => $row->courseid));
        $linkurl = "/blocks/iomad_company_admin/iomad_courses_form.php";

        if($row->visible == 0) {        
            $return = "<span class=\"dimmed_text\">";
        } elseif($row->visible == 1) {
        	   $return = "";
        }
 
        $first = true;
        foreach ($companies as $company) {
            if ($first) {
                $return .= "<a href='" . new moodle_url($linkurl, array('companyid' => $company->id)) .
                           "'>$company->shortname</a>";
                $first = false;
            } else {
                $return .= ",<a href='" . new moodle_url($linkurl, array('companyid' => $company->id)) .
                           "'>$company->shortname</a>";
            }
        }
        
        if($row->visible == 0) {        
            $return .= "</span>";
        }
        
        return $return;
    }

    /**
     * Generate the display of the user's lastname
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_coursename($row) {
        global $output;

        $courseurl = "/course/view.php";
        
        if($row->visible == 0) {        
            $coursereturn = "<span class=\"dimmed_text\">";
        } elseif($row->visible == 1) {
        	   $coursereturn = "";
        }
        
        $coursereturn .= "<a href='" . new moodle_url($courseurl, array('id' => $row->courseid)) .
               "'>" . format_string($row->coursename, true, 1) . "</a>";
               
        if($row->visible == 0) {        
            $coursereturn .= "</span>";
        }
        
        return $coursereturn;

    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_licensed($row) {
        global $USER, $systemcontext, $company, $OUTPUT, $DB;
        
        // Apply styling if the course is hidden
        if($row->visible == 0) {        
            $licenseselectoutput = "<span class=\"dimmed_text\">";
        } elseif($row->visible == 1) {
            $licenseselectoutput = "";        
        }

        // Deal with self enrol.
        if ($DB->get_record('enrol', array('courseid' => $row->courseid, 'enrol' => 'self', 'status' => 0))) {
            $row->licensed = 3;
            $licenseselectoutput = get_string('pluginname', 'enrol_self');
        }

        if (!empty($USER->editing) &&
        iomad::has_capability('block/iomad_company_admin:managecourses', $systemcontext)) {

            $editable = new \block_iomad_company_admin\output\courses_license_editable($company,
                                                          $systemcontext,
                                                          $row,
                                                          $row->licensed);

            return $OUTPUT->render_from_template('core/inplace_editable', $editable->export_for_template($OUTPUT));

        } else {
            if ($row->licensed == 0) {
                $licenseselectoutput .= get_string('no');
            } else if ($row->licensed == 1) {
                $licenseselectoutput .= get_string('yes');
            }
        }
        
        if($row->visible == 0) {        
            $licenseselectoutput .= "</span>";
        }

        return $licenseselectoutput;
    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_autoenrol($row) {
        global $USER, $systemcontext, $company, $OUTPUT, $DB;

        $options = [get_string('no'), get_string('yes')];

        if (empty($row->autoenrol)) {
            $value = 0;
        } else {
            $value = $row->autoenrol;
        }

        if (!empty($USER->editing) &&
        iomad::has_capability('block/iomad_company_admin:managecourses', $systemcontext)) {

            $editable = new \block_iomad_company_admin\output\courses_autoenrol_editable($company,
                                                          $systemcontext,
                                                          $row,
                                                          $value);

            return $OUTPUT->render_from_template('core/inplace_editable', $editable->export_for_template($OUTPUT));

        } elseif($row->visible == 0) {
        	
            return "<span class=\"dimmed_text\">" . $options[$value] . "</span>";
            
        } elseif($row->visible == 1){
        	
            return $options[$value];
        }
    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_shared($row) {
        global $USER, $systemcontext, $company, $OUTPUT, $DB;

        $sharedselectoptions = array('0' => get_string('no'),
                                    '1' => get_string('open', 'block_iomad_company_admin'),
                                    '2' => get_string('closed', 'block_iomad_company_admin'));

        if (!empty($USER->editing) &&
        iomad::has_capability('block/iomad_company_admin:managecourses', $systemcontext)) {

            $editable = new \block_iomad_company_admin\output\courses_shared_editable($company,
                                                          $systemcontext,
                                                          $row,
                                                          $row->shared);

            return $OUTPUT->render_from_template('core/inplace_editable', $editable->export_for_template($OUTPUT));
        
        } else if($row->visible == 0) {

            return "<span class=\"dimmed_text\">" . $sharedselectoptions[$row->shared] . "</span>";
        
        } else if($row->visible == 1) {

            return $sharedselectoptions[$row->shared];
        }

    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_validlength($row) {
        global $USER, $systemcontext, $company, $OUTPUT;

        if (!empty($USER->editing) &&
            iomad::has_capability('block/iomad_company_admin:managecourses', $systemcontext)) {
            $editable = new \block_iomad_company_admin\output\courses_validlength_editable($company,
                                                          $systemcontext,
                                                          $row,
                                                          $row->validlength);

            return $OUTPUT->render_from_template('core/inplace_editable', $editable->export_for_template($OUTPUT));

        } else if($row->visible == 0) {

            return "<span class=\"dimmed_text\">" . $row->validlength . "</span>";
        
        } else if($row->visible == 1) {

            return $row->validlength;
        }
    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_expireafter($row) {
        global $USER, $systemcontext, $company, $OUTPUT;

        if (!empty($USER->editing) &&
            iomad::has_capability('block/iomad_company_admin:managecourses', $systemcontext)) {

            $editable = new \block_iomad_company_admin\output\enrolment_expireafter_editable($company,
                                                          $systemcontext,
                                                          $row,
                                                          $row->expireafter);

            return $OUTPUT->render_from_template('core/inplace_editable', $editable->export_for_template($OUTPUT));

        } else if($row->visible == 0) {

            return "<span class=\"dimmed_text\">" . $row->expireafter . "</span>";
        
        } else if($row->visible == 1) {

            return $row->expireafter;
        }
    }

    /**
     * Generate the display of the warn expiry time.
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_warnexpire($row) {
        global $USER, $systemcontext, $company, $OUTPUT;

        if (!empty($USER->editing) &&
            iomad::has_capability('block/iomad_company_admin:managecourses', $systemcontext)) {

            $editable = new \block_iomad_company_admin\output\courses_warnexpire_editable($company,
                                                          $systemcontext,
                                                          $row,
                                                          $row->warnexpire);

            return $OUTPUT->render_from_template('core/inplace_editable', $editable->export_for_template($OUTPUT));

        } else if($row->visible == 0) {

            return "<span class=\"dimmed_text\">" . $row->warnexpire . "</span>";
        
        } else if($row->visible == 1) {
        	
            return $row->warnexpire;
        }

    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_warnnotstarted($row) {
        global $USER, $systemcontext, $company, $OUTPUT;

        if (!empty($USER->editing) &&
            iomad::has_capability('block/iomad_company_admin:managecourses', $systemcontext)) {

            $editable = new \block_iomad_company_admin\output\courses_warnnotstarted_editable($company,
                                                          $systemcontext,
                                                          $row,
                                                          $row->warnnotstarted);

            return $OUTPUT->render_from_template('core/inplace_editable', $editable->export_for_template($OUTPUT));

        } else if($row->visible == 0) {

            return "<span class=\"dimmed_text\">" . $row->warnnotstarted . "</span>";
        
        } else if($row->visible == 1) {
        	
            return $row->warnnotstarted;
        }
    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_warncompletion($row) {
        global $USER, $systemcontext, $company, $OUTPUT;

        if (!empty($USER->editing) &&
            iomad::has_capability('block/iomad_company_admin:managecourses', $systemcontext)) {

            $editable = new \block_iomad_company_admin\output\courses_warncompletion_editable($company,
                                                          $systemcontext,
                                                          $row,
                                                          $row->warncompletion);

            return $OUTPUT->render_from_template('core/inplace_editable', $editable->export_for_template($OUTPUT));

        } else if($row->visible == 0) {

            return "<span class=\"dimmed_text\">" . $row->warncompletion . "</span>";
        
        } else if($row->visible == 1) {
        	
            return $row->warncompletion;
        }
    }

    /**
     * Generate the display of the user's license allocated timestamp
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_notifyperiod($row) {
        global $USER, $systemcontext, $company, $OUTPUT;

        if (!empty($USER->editing) &&
            iomad::has_capability('block/iomad_company_admin:managecourses', $systemcontext)) {

            $editable = new \block_iomad_company_admin\output\courses_notifyperiod_editable($company,
                                                          $systemcontext,
                                                          $row,
                                                          $row->notifyperiod);

            return $OUTPUT->render_from_template('core/inplace_editable', $editable->export_for_template($OUTPUT));

        } else if($row->visible == 0) {

            return "<span class=\"dimmed_text\">" . $row->notifyperiod . "</span>";
        
        } else if($row->visible == 1) {
        	
            return $row->notifyperiod;
        }
    }

    /**
     * Generate the display of the ucourses has grade column.
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_hasgrade($row) {
        global $USER, $systemcontext, $company, $OUTPUT;

        if (!empty($USER->editing) &&
            iomad::has_capability('block/iomad_company_admin:managecourses', $systemcontext)) {
            $editable = new \block_iomad_company_admin\output\courses_hasgrade_editable($company,
                                                          $systemcontext,
                                                          $row,
                                                          $row->hasgrade);

            return $OUTPUT->render_from_template('core/inplace_editable', $editable->export_for_template($OUTPUT));


        } else {
        	
            $gradereturn = "";
            if ($row->hasgrade) {
                $gradereturn = get_string('yes');
            } else {
                $gradereturn = get_string('no');
                }

            if ($row->visible == 0) {
                return "<span class=\"dimmed_text\">" . $gradereturn . "</span>";
            } elseif ($row->visible == 1) {
                return $gradereturn;        
            }     

        }

    }
    
    /**
     * Generate the display of the course visibility column.
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_coursevisibility($row) {
        global $output;
        
        if(empty($USER->editing)) {
        
            if($row->visible == 0) {        
                $visiblereturn = "<span class=\"dimmed_text\"><i class='icon fa fa-eye-slash fa-fw ' title='" . get_string('hidden', 'badges') . "' role='img' aria-label='" . get_string('hidden', 'badges') . "'></i></span>";
            } elseif($row->visible == 1) {
        	       $visiblereturn = "<i class='icon fa fa-eye fa-fw ' title='" . get_string('visible', 'badges') . "' role='img' aria-label='" . get_string('visible', 'badges') . "'></i>";
            }   
            
        }   
        
        return $visiblereturn;

    }

    /**
     * Generate the display of the actions column.
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_actions($row) {
        global $OUTPUT, $DB, $params, $systemcontext, $USER, $company;

        $actionsoutput = "";

        if (!empty($USER->editing)) {
            if ($row->shared == 0 && 
                (iomad::has_capability('block/iomad_company_admin:deletecourses', $systemcontext) ||
                iomad::has_capability('block/iomad_company_admin:deletecourses', $systemcontext))) {
                $linkurl = "/blocks/iomad_company_admin/iomad_courses_form.php";
                $linkparams = $params;
                if (!empty($params['coursesearchtext'])) {
                    $linkparams['coursesearch'] = $params['coursesearchtext'];
                }
                $linkparams['deleteid'] = $row->courseid;
                $linkparams['sesskey'] = sesskey();
                $deleteurl = new moodle_url($linkurl, $linkparams);
                $actionsoutput = html_writer::start_tag('div');
                $actionsoutput .= "<a href='$deleteurl'><i class='icon fa fa-trash fa-fw ' title='" . get_string('delete') . "' role='img' aria-label='" . get_string('delete') . "'></i></a>";
    
            } else if (iomad::has_capability('block/iomad_company_admin:deleteallcourses', $systemcontext)) {
                $linkurl = "/blocks/iomad_company_admin/iomad_courses_form.php";
                $linkparams = $params;
                if (!empty($params['coursesearchtext'])) {
                    $linkparams['coursesearch'] = $params['coursesearchtext'];
                }
                $linkparams['deleteid'] = $row->courseid;
                $linkparams['sesskey'] = sesskey();
                $deleteurl = new moodle_url($linkurl, $linkparams);
                $actionsoutput = html_writer::start_tag('div');
                $actionsoutput .= "<a href='$deleteurl'><i class='icon fa fa-trash fa-fw ' title='" . get_string('delete') . "' role='img' aria-label='" . get_string('delete') . "'></i></a>";
            }

            // Handle course visibility action
            if (iomad::has_capability('block/iomad_company_admin:hideshowallcourses', $systemcontext) || 
            (iomad::has_capability('block/iomad_company_admin:hideshowcourses', $systemcontext) && 
            $DB->get_record('company_created_courses', ['companyid' => $company->id, 'courseid' => $row->courseid]))) {
                $linkurl = "/blocks/iomad_company_admin/iomad_courses_form.php";            
                $linkparams = $params;
                if (!empty($params['coursesearchtext'])) {
                    $linkparams['coursesearch'] = $params['coursesearchtext'];
                }             
                $linkparams['sesskey'] = sesskey();
                
                if($row->visible == 1) {
                	  $linkparams['hideid'] = $row->courseid;
                	  $hideurl = new moodle_url($linkurl, $linkparams);
                    $actionsoutput .= "<a href='$hideurl'><i class='icon fa fa-eye fa-fw ' title='" . get_string('hide') . "' role='img' aria-label='" . get_string('hide') . "'></i></a>";
                    $actionsoutput .= html_writer::end_tag('div');
                    
                } else if($row->visible == 0) {
                	  $linkparams['showid'] = $row->courseid;
                	  $showurl = new moodle_url($linkurl, $linkparams);
                 	  $actionsoutput .= "<a href='$showurl'><i class='icon fa fa-eye-slash fa-fw ' title='" . get_string('show') . "' role='img' aria-label='" . get_string('hide') . "'></i></a>";
                    $actionsoutput .= html_writer::end_tag('div');
            }
            
	     }

        $actionsoutput .= html_writer::end_tag('div');

	}

        return $actionsoutput;

    }
}
