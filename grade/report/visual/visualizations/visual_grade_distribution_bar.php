<?php 
 ///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com       //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

require_once $CFG->dirroot . '/grade/report/visual/visualizations/visualization.php';

class grade_distribution_bar extends visualization {
    const RANGE = 10;
    
    public $percent = true;
    
    public $colorencoder;
    
    public $selector;
    
    public $itemlegend;

    public function __construct() {
        global $DB;
    
        parent::__construct(get_string('gradedistributionbar', 'gradereport_visual'));
    
        $this->layout = visualization::LAYOUT_AXIS;
        $this->layoutsettings = null; //array('false', 'true');
        $this->nodeshape = visualization::SHAPE_VERTICAL_BAR;
    
        $this->xaxis = 'grade';
        $this->yaxis = 'students';
        $this->xaxislabelformat = '0\\%';
        //$this->xaxismax = 100 + grade_distribution::RANGE;
        //$this->xaxismin = grade_distribution::RANGE;
        $this->xaxisxoffset = -27;
        
        $this->xaxislabel =  get_string('grade', 'gradereport_visual');
        
        if($this->percent) {
            $this->yaxislabelformat = '0\\%';
            $this->yaxislabel =  get_string('percentstudents', 'gradereport_visual');
        } else {
            $this->yaxislabel =  get_string('numberstudents', 'gradereport_visual');
        }
        
        $this->title = get_string('gradedistribution:title', 'gradereport_visual');
        
        $this->capability = 'gradereport/visual:vis:grade_distribution_bar';
        $this->usegroups = true;
    
        $options = array();
        foreach(groups_get_all_groups(required_param('id')) as $groupkey=>$group) {
            $options[$groupkey] = grade_report_visual::truncate($group->name);
        }
        $options[0] = 'All Groups';
        
        if(isset($DB) && !is_null($DB)) {
            $course = $DB->get_record('course', array('id' => required_param('id')));
        } else {
            $course = get_record('course', 'id', required_param('id'));
        }
        if (!$course) {
            print_error('nocourseid');
        }
        
        $active = groups_get_course_group($course, true);
        
        if(!$active) {
            $active = 0;
        }

        $this->selector = new selector('group', $options, $active);
        $this->selectors = array($this->selector);
        
        $this->colorencoder = new encoder(encoder::ENCODER_COLOR, 'item');
        $this->encoders = array($this->colorencoder);
    
        $this->itemlegend = new legend($this->colorencoder, array(get_string('coursetotal', 'grades')));
        $this->legends = array($this->itemlegend);
    }
    
    public function report_data($visualreport) {
        $data = array();
        $data['header'] = array();
        $data['header']['students'] = 'students';
        $data['header']['grade'] = 'grade';
        $data['header']['item'] = 'item';
        
        $rawdata = array();
        $counters = array();
        $rangesize = round(100 / grade_distribution::RANGE);

        foreach($visualreport->grades as $itemkey=>$itemgrades) {
            foreach($itemgrades as $studentkey=>$studentdata) {
                if($studentdata != null && $studentdata->finalgrade != null) {
                    $gradelevel = floor(round($studentdata->standardise_score($studentdata->finalgrade, $visualreport->gtree->items[$itemkey]->grademin, $visualreport->gtree->items[$itemkey]->grademax, 0, 100)) / grade_distribution::RANGE);
                    //if($gradelevel == $rangesize) {
                     //   $gradelevel = $rangesize - 1;
                    //}
                        
                    if(isset($rawdata[$gradelevel][$itemkey])) {
                        $rawdata[$gradelevel][$itemkey] += 1;
                    } else {
                        $rawdata[$gradelevel][$itemkey] = 1;
                    }
                    
                    if(isset($counters[$itemkey])) {
                        $counters[$itemkey] += 1;
                    } else {
                        $counters[$itemkey] = 1;
                    }
                    
                }
            }
            
            for($i = 0; $i <= $rangesize; $i++) {
                if(!isset($rawdata[$i][$itemkey])) {
                    $rawdata[$i][$itemkey] = 0;
                }
            }
        }
        
        
        $i = 0;
        
        foreach($rawdata as $gradelevel=>$items) {
            foreach($items as $itemkey=>$students) {
                $index = $gradelevel * 10000 + $i;
                
                if($this->percent) {
                    $data[$index]['students'] =  ($students / $counters[$itemkey]) * 100;
                } else {
                    $data[$index]['students'] =  $students;
                }
                //$data[$index]['students'] =  $students;
                
                $data[$index]['grade'] = $gradelevel * grade_distribution::RANGE; //($gradelevel * grade_distribution::RANGE) . '% - ' . (($gradelevel + 1) * grade_distribution::RANGE - 1) . '%';
                $data[$index]['item'] = $visualreport->gtree->items[$itemkey]->get_name();
                
                $i++;
            }
        }
        
        return $data;
    }
}
?>