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

class grade_distribution extends visualization {
    const RANGE = 10;
    
    public $percent = true;
    
    public $colorencodernode;
    
    public $colorencoderedge;
    
    public $shapeencodernode;
    
    public $shapeencoderedge;
    
    public $grouplegend;
    
    public $itemlegend;

    public function __construct() {
        parent::__construct(get_string('gradedistribution', 'gradereport_visual'));
    
        $this->layout = visualization::LAYOUT_AXIS;
        $this->layoutsettings = null;//array('false', 'true');
        //$this->nodeshape = visualization::SHAPE_VERTICAL_BAR;
        //$this->edgeshape = visualization::SHAPE_BEZIER;
        
        $this->edges = array(new edge('grade', array('group', 'item')));
        
        $this->xaxis = 'grade';
        $this->yaxis = 'students';
        $this->xaxislabelformat = '0\\%';
        
        if($this->percent) {
            $this->yaxislabelformat = '0\\%';
            $this->yaxislabel =  get_string('percentstudents', 'gradereport_visual');
        } else {
            $this->yaxislabel =  get_string('numberstudents', 'gradereport_visual');
        }
        
        //$this->xaxismax = 100 + grade_distribution::RANGE;
        //$this->xaxismin = grade_distribution::RANGE;
        $this->xaxisxoffset = -27;
        
        $this->xaxislabel =  get_string('grade', 'gradereport_visual');
        $this->title = get_string('gradedistribution:title', 'gradereport_visual');
        
        $this->capability = 'gradereport/visual:vis:grade_distribution';
        
        $this->colorencoderedge = new encoder(encoder::ENCODER_COLOR, 'item', array(2));
        $this->colorencodernode = new encoder(encoder::ENCODER_COLOR, 'item', array(1));
        //$this->shapeencoderedge = new encoder(encoder::ENCODER_SHAPE, 'group', array(2));
        $this->shapeencodernode = new encoder(encoder::ENCODER_SHAPE, 'group', array(1));
        $this->encoders = array($this->colorencodernode, $this->colorencoderedge, $this->shapeencodernode);
    
        $this->grouplegend = new legend($this->shapeencodernode, array(get_string('allgroups', 'gradereport_visual')));
        $this->itemlegend = new legend($this->colorencodernode, array(get_string('coursetotal', 'grades')));
        $this->legends = array($this->itemlegend, $this->grouplegend);
    }
    
    public function report_data($visualreport) {
        $data = array();
        $data['header'] = array();
        $data['header']['students'] = 'students';
       // $data['header']['gradelevel'] = 'gradelevel';
        $data['header']['grade'] = 'grade';
        $data['header']['item'] = 'item';
        $data['header']['group'] = 'group';
        //$data['header']['range'] = 'range';
        
        $rawdata = array();
        $counters = array();
        $rangesize = round(100 / grade_distribution::RANGE);

        foreach($visualreport->grades as $itemkey=>$itemgrades) {
            foreach($itemgrades as $studentkey=>$studentdata) {
                if($studentdata != null && $studentdata->finalgrade != null) {
                    foreach(groups_get_user_groups($visualreport->courseid, $studentkey) as $grouping) {
                        $gradelevel = floor(round($studentdata->standardise_score($studentdata->finalgrade, $visualreport->gtree->items[$itemkey]->grademin, $visualreport->gtree->items[$itemkey]->grademax, 0, 100)) / grade_distribution::RANGE);
                        //if($gradelevel == $rangesize) {
                        //    $gradelevel = $rangesize - 1;
                        //}

                        if(count($grouping) > 0) {
                            foreach($grouping as $group) {
                                if(isset($rawdata[$gradelevel][$itemkey][$group])) {
                                    $rawdata[$gradelevel][$itemkey][$group] += 1;
                                } else {
                                    $rawdata[$gradelevel][$itemkey][$group] = 1;
                                }
                            
                                if(isset($counters[$itemkey][$group])) {
                                    $counters[$itemkey][$group] += 1;
                                } else {
                                    $counters[$itemkey][$group] = 1;
                                }
                            }
                        } else {
                            if(isset($rawdata[$gradelevel][$itemkey]['ng'])) {
                                $rawdata[$gradelevel][$itemkey]['ng'] += 1;
                            } else {
                                $rawdata[$gradelevel][$itemkey]['ng'] = 1;
                            }
                            
                            if(isset($counters[$itemkey]['ng'])) {
                                $counters[$itemkey]['ng'] += 1;
                            } else {
                                $counters[$itemkey]['ng'] = 1;
                            }
                        }
                        
                        if(isset($rawdata[$gradelevel][$itemkey]['ag'])) {
                            $rawdata[$gradelevel][$itemkey]['ag'] += 1;
                        } else {
                            $rawdata[$gradelevel][$itemkey]['ag'] = 1;
                        }
                        
                        if(isset($counters[$itemkey]['ag'])) {
                            $counters[$itemkey]['ag'] += 1;
                        } else {
                            $counters[$itemkey]['ag'] = 1;
                        }
                    }
                }
            }
            
            for($i = 0; $i <= $rangesize; $i++) {
                foreach(groups_get_all_groups($visualreport->courseid) as $group) {
                    if(!isset($rawdata[$i][$itemkey][$group->id])) {
                        $rawdata[$i][$itemkey][$group->id] = 0;
                    }
                }
                
                if(!isset($rawdata[$i][$itemkey]['ng'])) {
                    $rawdata[$i][$itemkey]['ng'] = 0;
                }
                
                if(!isset($rawdata[$i][$itemkey]['ag'])) {
                    $rawdata[$i][$itemkey]['ag'] = 0;
                }
            }
        }
        
        
        $i = 0;
        
        foreach($rawdata as $gradelevel=>$items) {
            foreach($items as $itemkey=>$groups) {
                foreach($groups as $groupkey=>$students) {
                    $index = $gradelevel * 10000 + $i;
                    
                    //$data[$index]['gradelevel'] = $gradelevel; 
                    
                    if($this->percent) {
                        $data[$index]['students'] =  ($students / $counters[$itemkey][$groupkey]) * 100;
                    } else {
                        $data[$index]['students'] =  $students;
                    }
                    
                    $data[$index]['grade'] = $gradelevel * grade_distribution::RANGE; //($gradelevel * grade_distribution::RANGE) . '% - ' . (($gradelevel + 1) * grade_distribution::RANGE - 1) . '%';
                    
                    $data[$index]['item'] = $visualreport->gtree->items[$itemkey]->get_name();
                    if($groupkey == 'ng') {
                        $data[$index]['group'] = get_string('nogroup', 'gradereport_visual');
                    } else if($groupkey == 'ag'){
                        $data[$index]['group'] = get_string('allgroups', 'gradereport_visual');
                    } else {
                        $data[$index]['group'] = groups_get_group_name($groupkey);
                    }
                    //$data[$index]['range'] = grade_distribution::RANGE;
                    
                    $i++;
                }
            }
        }
        
       // ksort($data);
        
        return $data;
    }
}
?>