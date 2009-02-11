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

class grades_vs_items extends visualization {
    public $colorencodernode;
    
    public $colorencoderedge;
    
    public $shapeencodernode;
    
    public $shapeencoderedge;
    
    public $grouplegend;
    
    public $itemlegend;

    public function __construct() {
        parent::__construct(get_string('gradesvsitems', 'gradereport_visual'));
        
        $this->layout = visualization::LAYOUT_AXIS;
        $this->layoutsettings = null;
        
        //$this->edges = array(new edge('item', array('group')));
        
        $this->xaxis = 'item';
        $this->yaxis = 'grade';
        $this->yaxislabelformat = '0\\%';
        
        $this->xaxislabel = get_string('item', 'gradereport_visual');
        $this->yaxislabel = get_string('grade', 'gradereport_visual');
        $this->title = get_string('gradesvsitems:title', 'gradereport_visual');
    
        $this->capability = 'gradereport/visual:vis:grades_vs_items';
        
        $this->colorencodernode = new encoder(encoder::ENCODER_COLOR, 'group');
        $this->shapeencodernode = new encoder(encoder::ENCODER_SHAPE, 'item');
        $this->colorencoderedge = new encoder(encoder::ENCODER_COLOR, 'group', array(2));
        $this->encoders = array($this->colorencodernode, $this->colorencoderedge, $this->shapeencodernode);
        
        $this->grouplegend = new legend($this->colorencodernode);
        $this->itemlegend = new legend($this->shapeencodernode);
        $this->legends = array($this->grouplegend, $this->itemlegend);
    }
    
    public function report_data($visualreport) {
    $data = array();
        $data['header'] = array();
        $data['header']['grade'] = 'grade';
        $data['header']['item'] = 'item';
        $data['header']['group'] = 'group';
        
        $count = array();
        
        foreach($visualreport->grades as $itemkey=>$itemgrades) {
            foreach($itemgrades as $studentkey=>$studentdata) {
                if($studentdata != null && $studentdata->finalgrade != null) {
                    foreach(groups_get_user_groups($visualreport->courseid, $studentkey) as $grouping) {
                        
                        if(count($grouping) > 0) {
                            foreach($grouping as $group) {
                                if(!isset($data[$itemkey . '-' . $group])) {
                                    $data[$itemkey . '-' . $group] = array();
                                    $data[$itemkey . '-' . $group]['grade'] = $studentdata->standardise_score($studentdata->finalgrade, $visualreport->gtree->items[$itemkey]->grademin, $visualreport->gtree->items[$itemkey]->grademax, 0, 100);
                                    $data[$itemkey . '-' . $group]['item'] = $visualreport->gtree->items[$itemkey]->get_name();
                                    $data[$itemkey . '-' . $group]['group'] = groups_get_group_name($group);
                                    $count[$itemkey . '-' . $group] = 1;
                                } else {
                                    $data[$itemkey . '-' . $group]['grade'] += $studentdata->standardise_score($studentdata->finalgrade, $visualreport->gtree->items[$itemkey]->grademin, $visualreport->gtree->items[$itemkey]->grademax, 0, 100);
                                    $count[$itemkey . '-' . $group]++;
                                }
                            }
                        } else {
                            if(!isset($data[$itemkey . '-ng'])) {
                                $data[$itemkey . '-ng'] = array();
                                $data[$itemkey . '-ng']['grade'] = $studentdata->standardise_score($studentdata->finalgrade, $visualreport->gtree->items[$itemkey]->grademin, $visualreport->gtree->items[$itemkey]->grademax, 0, 100);
                                $data[$itemkey . '-ng']['item'] = $visualreport->gtree->items[$itemkey]->get_name();
                                $data[$itemkey . '-ng']['group'] = get_string('nogroup', 'gradereport_visual');
                                $count[$itemkey . '-ng'] = 1;
                            } else {
                                $data[$itemkey . '-ng']['grade'] += $studentdata->standardise_score($studentdata->finalgrade, $visualreport->gtree->items[$itemkey]->grademin, $visualreport->gtree->items[$itemkey]->grademax, 0, 100);
                                $count[$itemkey . '-ng']++;
                            }
                        }
                        
                         if(!isset($data[$itemkey . '-ag'])) {
                            $data[$itemkey . '-ag'] = array();
                            $data[$itemkey . '-ag']['grade'] = $studentdata->standardise_score($studentdata->finalgrade, $visualreport->gtree->items[$itemkey]->grademin, $visualreport->gtree->items[$itemkey]->grademax, 0, 100);
                            $data[$itemkey . '-ag']['item'] = $visualreport->gtree->items[$itemkey]->get_name();
                            $data[$itemkey . '-ag']['group'] = get_string('allgroups', 'gradereport_visual');
                            $count[$itemkey . '-ag'] = 1;
                        } else {
                            $data[$itemkey . '-ag']['grade'] += $studentdata->standardise_score($studentdata->finalgrade, $visualreport->gtree->items[$itemkey]->grademin, $visualreport->gtree->items[$itemkey]->grademax, 0, 100);
                            $count[$itemkey . '-ag']++;
                        }
                        
                    }
                }
            }
        }
        
        foreach($data as $key=>$row) {
            if($key != 'header'){
                $data[$key]['grade'] /= $count[$key];
            }
        }
        
        return $data;
    }
}
?>