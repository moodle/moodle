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

class grades_vs_students extends visualization {

    public $colorencoder;
    
    public $shapeencoder;
    
    public $grouplegend;
    
    public $itemlegend;
    

    public function __construct() {
        parent::__construct(get_string('gradesvsstudents', 'gradereport_visual'));
        
        $this->layout = visualization::LAYOUT_AXIS;
        $this->layoutsettings = null;
        
        $this->xaxis = 'student';
        $this->yaxis = 'grade';
        
        $this->xaxislabel = get_string('student', 'gradereport_visual');
        $this->yaxislabel = get_string('grade', 'gradereport_visual');
        $this->title = get_string('gradesvsstudents:title', 'gradereport_visual');
        
        $this->capability = 'gradereport/visual:vis:grades_vs_students';
        
        $this->colorencoder = new encoder(encoder::ENCODER_COLOR, 'item');
        $this->shapeencoder = new encoder(encoder::ENCODER_SHAPE, 'group');
        $this->encoders = array($this->colorencoder, $this->shapeencoder);
    
        $this->grouplegend = new legend($this->shapeencoder);
        $this->itemlegend = new legend($this->colorencoder);
        $this->legends = array($this->itemlegend, $this->grouplegend);
    }
    
    
    public function report_data($visualreport) {
        $data = array();
        $data['header'] = array();
        $data['header']['student'] = 'student';
        $data['header']['grade'] = 'grade';
        $data['header']['item'] = 'item';
        $data['header']['group'] = 'group';
        
        foreach($visualreport->grades as $itemkey=>$itemgrades) {
            foreach($itemgrades as $studentkey=>$studentdata) {
                if($studentdata != null && $studentdata->finalgrade != null) {
                    foreach(groups_get_user_groups($visualreport->courseid, $studentkey) as $grouping) {
                        if(count($grouping) > 0) {
                            foreach($grouping as $group) {
                                $data[$studentkey . '-' . $itemkey . '-' . $group]['student'] = $visualreport->users[$studentkey]->firstname . ' ' . $visualreport->users[$studentkey]->lastname;
                                $data[$studentkey . '-' . $itemkey . '-' . $group]['grade'] = $studentdata->standardise_score($studentdata->finalgrade, $visualreport->gtree->items[$itemkey]->grademin, $visualreport->gtree->items[$itemkey]->grademax, 0, 100); 
                                $data[$studentkey . '-' . $itemkey . '-' . $group]['item'] = $visualreport->gtree->items[$itemkey]->get_name();
                                $data[$studentkey . '-' . $itemkey . '-' . $group]['group'] = groups_get_group_name($group);
                            }
                        } else {
                            $data[$studentkey . '-' . $itemkey . '-ng' ]['student'] = $visualreport->users[$studentkey]->firstname . ' ' . $visualreport->users[$studentkey]->lastname;
                            $data[$studentkey . '-' . $itemkey . '-ng' ]['grade'] = $studentdata->standardise_score($studentdata->finalgrade, $visualreport->gtree->items[$itemkey]->grademin, $visualreport->gtree->items[$itemkey]->grademax, 0, 100); 
                            $data[$studentkey . '-' . $itemkey . '-ng' ]['item'] = $visualreport->gtree->items[$itemkey]->get_name();
                            $data[$studentkey . '-' . $itemkey . '-ng' ]['group'] = get_string('nogroup', 'gradereport_visual');
                        }
                    }
                }
            }
        }
        
        return $data;
    }
}

?>
