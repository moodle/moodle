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

class grade_distribution_group_bar extends visualization {
    const RANGE = 10;

    public $percent = true;

    public $colorencoder;
    
    public $selector;
    
    public $grouplegend;

    public function __construct() {
        global $DB;
    
        parent::__construct(get_string('gradedistributiongroupbar', 'gradereport_visual'));
    
        $this->layout = visualization::LAYOUT_AXIS;
        $this->layoutsettings = array('false', 'true');
        $this->nodeshape = visualization::SHAPE_VERTICAL_BAR;
    
        $this->xaxis = 'grade';
        $this->yaxis = 'students';
        $this->xaxislabelformat = '0\\%';
        $this->xaxisxoffset = -27;
        
        $this->xaxislabel =  get_string('grade', 'gradereport_visual');
        
        if($this->percent) {
            $this->yaxislabelformat = '0\\%';
            $this->yaxislabel =  get_string('percentstudents', 'gradereport_visual');
        } else {
            $this->yaxislabel =  get_string('numberstudents', 'gradereport_visual');
        }
        
        $this->title = get_string('gradedistribution:title', 'gradereport_visual');
        
        $this->capability = 'gradereport/visual:vis:grade_distribution_group_bar';
        
        $courseid = required_param('id');
    
        $options = array();
        $items = grade_item::fetch_all(array('courseid' => $courseid));
        
        foreach($items as $item) {
            if(count($item->get_final()) > 0) {
                $options[$item->id] = grade_report_visual::truncate($item->get_name());
            }
        }
        $options['ai'] = 'All Items';
        
        $this->selector = new selector('item', $options, 'ai');
        $this->selectors = array($this->selector);
        
        $this->colorencoder = new encoder(encoder::ENCODER_COLOR, 'group');
        $this->encoders = array($this->colorencoder);
    
        $this->grouplegend = new legend($this->colorencoder);
        $this->legends = array($this->grouplegend);
    }
    
    private function report_rawdata($visualreport, $itemkey, &$rawdata, &$counters) {
        $rangesize = round(100 / grade_distribution::RANGE);
        
        foreach($visualreport->grades[$itemkey] as $studentkey=>$studentdata) {
                if($studentdata != null && $studentdata->finalgrade != null) {
                    $gradelevel = floor($studentdata->standardise_score($studentdata->finalgrade, $visualreport->gtree->items[$itemkey]->grademin, $visualreport->gtree->items[$itemkey]->grademax, 0, 100) / grade_distribution::RANGE);
                    //if($gradelevel == $rangesize) {
                    //    $gradelevel = $rangesize - 1;
                    //}
                    
                    $groups = groups_get_all_groups($visualreport->courseid, $studentkey);
                    
                    if(count($groups) > 0) {
                        foreach($groups as $group) {
                            if(isset($rawdata[$gradelevel][$group->id])) {
                                $rawdata[$gradelevel][$group->id] += 1;
                            } else {
                                $rawdata[$gradelevel][$group->id] = 1;
                            }
                            
                                if(isset($counters[$group->id])) {
                                    $counters[$group->id] += 1;
                                } else {
                                    $counters[$group->id] = 1;
                                }
                            }
                    } else {
                            if(isset($rawdata[$gradelevel]['ng'])) {
                                $rawdata[$gradelevel]['ng'] += 1;
                            } else {
                                $rawdata[$gradelevel]['ng'] = 1;
                            }
                            
                            if(isset($counters['ng'])) {
                                $counters['ng'] += 1;
                            } else {
                                $counters['ng'] = 1;
                            }
                    }
                }
            }
    }
    
    public function report_data($visualreport) {
        $data = array();
        $data['header'] = array();
        $data['header']['students'] = 'students';
        $data['header']['grade'] = 'grade';
        $data['header']['group'] = 'group';
        
        $rawdata = array();
        $counters = array();
        $rangesize = round(100 / grade_distribution::RANGE);

        $item = optional_param('item', 'ai');

        if($item == 'ai') {
            foreach($visualreport->grades as $itemkey=>$itemgrades) {
                $this->report_rawdata($visualreport, $itemkey, $rawdata, $counters);
            }
        } else {
            $this->report_rawdata($visualreport, $item, $rawdata, $counters);
        }
        
        for($i = 0; $i <= $rangesize; $i++) {
            foreach(groups_get_all_groups($visualreport->courseid) as $group) {
                if(!isset($rawdata[$i][$group->id])) {
                    $rawdata[$i][$group->id] = 0;
                }
            }
        }
        
        $i = 0;
        
        foreach($rawdata as $gradelevel=>$groups) {
            foreach($groups as $groupkey=>$students) {
                $index = $gradelevel * 10000 + $i;
                //$data[$index]['students'] =  $students; 
                
                if($this->percent) {
                    $data[$index]['students'] =  ($students / $counters[$groupkey]) * 100;
                } else {
                    $data[$index]['students'] =  $students;
                }
                
                $data[$index]['grade'] = $gradelevel * grade_distribution::RANGE;
                if($groupkey == 'ng') {
                    $data[$index]['group'] = get_string('nogroup', 'gradereport_visual');
                } else {
                    $data[$index]['group'] = groups_get_group_name($groupkey);
                }
                    
                $i++;
            }
        }
        
        return $data;
    }
}
?>