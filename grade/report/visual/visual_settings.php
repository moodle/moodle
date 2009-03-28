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

/**
 * Generates XML output that describes a visualization and is
 * sent to the flash/flex front end. The output is based on the
 * visualizations classes found in ./visualizations
 */

// TODO: This needs to be replaced by web service user keys


/// Get a session id from the URI request and make a cookie
/// for it temparaly. This is needed as the flex application will
/// not have the users oringal cookie and only the session information
/// witch is passed to it.
$cookiewasset = false;
if(empty($_COOKIE) && isset($_GET['sessionid']) && isset($_GET['sessioncookie']) && isset($_GET['sessiontest'])) {
    $_COOKIE['MoodleSession' . $_GET['sessioncookie']] = $_GET['sessionid'];
    $_COOKIE['MoodleSessionTest' . $_GET['sessioncookie']] = $_GET['sessiontest'];
    $cookiewasset = true;
}

require_once '../../../config.php';
require_once $CFG->dirroot.'/grade/report/visual/lib.php';
require_once $CFG->libdir.'/phpxml/xml.php';

$visname  = required_param('visid', PARAM_ACTION);
$courseid = required_param('id', PARAM_INT);

/// basic access checks
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('nocourseid');
}
require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $course->id);
require_capability('gradereport/visual:view', $context);

$vis = grade_report_visual::get_visualization($visname, $context);

/// Make sure the user is allowed see this visualization
require_capability($vis->capability, $context);

/// Turn of error reporting as hummans will not be seeing 
/// this and it will be read by the front end. Notices and 
/// warnings will break the XML format and stop the
/// front end from working.
error_reporting(0);

/// Define array that repersents the XML output from the
/// visualization class selected.
$settings = array();
$settings['visualization'] = array();

$settings['visualization']['name'] = $vis->name;
$settings['visualization']['classname'] = get_class($vis);

$settings['visualization']['layout'] = array();
$settings['visualization']['layout']['type'] = $vis->layout;

$settings['visualization']['layout']['xaxis'] = array();
$settings['visualization']['layout']['yaxis'] = array();
$settings['visualization']['layout']['xaxis']['field'] = 'data.' . $vis->xaxis;
$settings['visualization']['layout']['yaxis']['field'] = 'data.' . $vis->yaxis;
$settings['visualization']['layout']['xaxis']['labelformat'] = $vis->xaxislabelformat;
$settings['visualization']['layout']['yaxis']['labelformat'] = $vis->yaxislabelformat;
$settings['visualization']['layout']['xaxis']['min'] = $vis->xaxismin;
$settings['visualization']['layout']['xaxis']['max'] = $vis->xaxismax;
$settings['visualization']['layout']['yaxis']['min'] = $vis->yaxismin;
$settings['visualization']['layout']['yaxis']['max'] = $vis->yaxismax;
$settings['visualization']['layout']['yaxis']['xoffset'] =$vis->yaxisxoffset;
$settings['visualization']['layout']['yaxis']['yoffset'] =$vis->yaxisyoffset;
$settings['visualization']['layout']['xaxis']['xoffset'] =$vis->xaxisxoffset;
$settings['visualization']['layout']['xaxis']['yoffset'] =$vis->xaxisyoffset;

if($vis->layoutsettings != null) {
    $settings['visualization']['layout']['setting'] = array();
    for($i = 0; $i < count($vis->layoutsettings); $i++) {
        $settings['visualization']['layout']['setting'][$i] = $vis->layoutsettings[$i];
    }
}

if($vis->edges != null) {
    $settings['visualization']['edge'] = array();
    for($i = 0; $i < count($vis->edges); $i++) {
        $settings['visualization']['edge'][$i] = array();
    
        if(is_array($vis->edges[$i]->sortby)) {
            $settings['visualization']['edge'][$i]['sortby'] = array();
        
            for($k = 0; $k < count($vis->edges[$i]->sortby); $k++) {
                $settings['visualization']['edge'][$i]['sortby'][$k] = 'data.' . $vis->edges[$i]->sortby[$k];
            }
        } else {
            $settings['visualization']['edge'][$i]['sortby'] =  'data.' . $vis->edges[$i]->sortby;
        }
    
        if(is_array($vis->edges[$i]->groupby)) {
            $settings['visualization']['edge'][$i]['groupby'] = array();
            
            for($k = 0; $k < count($vis->edges[$i]->groupby); $k++) {
                $settings['visualization']['edge'][$i]['groupby'][$k] = 'data.' . $vis->edges[$i]->groupby[$k];
            }
        } else {
            $settings['visualization']['edge'][$i]['groupby'] = 'data.' . $vis->edges[$i]->groupby;
        }
    }
}

if($vis->encoders != null) {
    $settings['visualization']['encoder'] = array();
    for($i = 0; $i < count($vis->encoders); $i++) {
        $settings['visualization']['encoder'][$i] = array();
        $settings['visualization']['encoder'][$i]['id'] = $vis->encoders[$i]->id;
        $settings['visualization']['encoder'][$i]['type'] = $vis->encoders[$i]->type;
        $settings['visualization']['encoder'][$i]['datafield'] = 'data.' . $vis->encoders[$i]->datafield;
        
        if($vis->encoders[$i]->settings != null) {
            $settings['visualization']['encoder'][$i]['setting'] = array();
            for($j = 0; $j < count($vis->encoders[$i]->settings); $j++) {
                $settings['visualization']['encoder'][$i]['setting'][$j] = $vis->encoders[$i]->settings[$j];
            }
        }
    }
}

if($vis->legends != null) {
    $settings['visualization']['legend'] = array();
    for($i = 0; $i < count($vis->legends); $i++) {
        $settings['visualization']['legend'][$i] = array();
        $settings['visualization']['legend'][$i]['encoderid'] = $vis->legends[$i]->encoder->id;
        $settings['visualization']['legend'][$i]['datafield'] = 'data.' . $vis->legends[$i]->encoder->datafield;
        
        if($vis->legends[$i]->show != null) {
            $settings['visualization']['legend'][$i]['show'] = array();
            for($j = 0; $j < count($vis->legends[$i]->show); $j++) {
                $settings['visualization']['legend'][$i]['show'][$j] = $vis->legends[$i]->show[$j];
            }
        }
    }
}

if($vis->selectors != null) {
    $settings['visualization']['selector'] = array();
    for($i = 0; $i < count($vis->selectors); $i++) {
        $settings['visualization']['selector'][$i] = array();
        $settings['visualization']['selector'][$i]['param'] = $vis->selectors[$i]->param;
        $settings['visualization']['selector'][$i]['active'] = $vis->selectors[$i]->active;
        
        
        if($vis->selectors[$i]->options != null) {
            $settings['visualization']['selector'][$i]['option'] = array();
            $k = 0;
            foreach($vis->selectors[$i]->options as $value=>$title) {
                $settings['visualization']['selector'][$i]['option'][$k] = array();
                $settings['visualization']['selector'][$i]['option'][$k]['title'] = $title;
                $settings['visualization']['selector'][$i]['option'][$k]['value'] = $value;
                $k++;
            }
        }
    }
}

/// TODO: add in capabilities data
$settings['visualization']['capabilities'] = array();

$settings['visualization']['flash'] = array();
$settings['visualization']['flash']['width'] = $vis->width;
$settings['visualization']['flash']['height'] = $vis->height;
$settings['visualization']['flash']['framerate'] = $vis->framerate;
$settings['visualization']['flash']['quality'] = $vis->quality;

$settings['visualization']['labels'] = array();
$settings['visualization']['labels']['xaxis'] = $vis->xaxislabel;
$settings['visualization']['labels']['yaxis'] = $vis->yaxislabel;
$settings['visualization']['labels']['title'] = $vis->title;

$settings['visualization']['style'] = array();
$settings['visualization']['style']['nodeshape'] = $vis->nodeshape;
$settings['visualization']['style']['edgeshape'] = $vis->edgeshape;
$settings['visualization']['style']['bgcolor'] = $vis->backgroundcolor;

$settings['visualization']['style']['text'] = array();
$settings['visualization']['style']['text']['font'] = $vis->font;
$settings['visualization']['style']['text']['size'] = $vis->fontsize;

$settings['visualization']['style']['button'] = array();
$settings['visualization']['style']['button']['bgcolor'] = $vis->buttonbgcolor;
$settings['visualization']['style']['button']['alpha'] = $vis->buttonbgalpha;
$settings['visualization']['style']['button']['text'] = array();
$settings['visualization']['style']['button']['text']['font'] = $vis->buttonfont;
$settings['visualization']['style']['button']['text']['size'] = $vis->buttonfontsize;
$settings['visualization']['style']['button']['line'] = array();
$settings['visualization']['style']['button']['line']['size'] = $vis->buttonlinesize;
$settings['visualization']['style']['button']['line']['color'] = $vis->buttonlinecolor;
$settings['visualization']['style']['button']['line']['alpha'] = $vis->buttonlinealpha;

$settings['visualization']['style']['popup'] = array();
$settings['visualization']['style']['popup']['bgcolor'] = $vis->popupbgcolor;
$settings['visualization']['style']['popup']['alpha'] = $vis->popupbgalpha;
$settings['visualization']['style']['popup']['text']  = array();
$settings['visualization']['style']['popup']['text']['font'] = $vis->popupfont;
$settings['visualization']['style']['popup']['text']['size'] = $vis->popupfontsize;
$settings['visualization']['style']['popup']['line'] = array();
$settings['visualization']['style']['popup']['line']['size'] = $vis->popuplinesize;
$settings['visualization']['style']['popup']['line']['color'] = $vis->popuplinecolor;
$settings['visualization']['style']['popup']['line']['alpha'] = $vis->popuplinealpha;

$settings['visualization']['lang'] = array();
$settings['visualization']['lang']['hide'] = get_string('hide', 'gradereport_visual');
$settings['visualization']['lang']['show'] = get_string('show', 'gradereport_visual');
$settings['visualization']['lang']['xlabels'] = get_string('xlabels', 'gradereport_visual');
$settings['visualization']['lang']['ylabels'] = get_string('ylabels', 'gradereport_visual');
$settings['visualization']['lang']['axes'] = get_string('axes', 'gradereport_visual');
$settings['visualization']['lang']['invertaxes'] = get_string('invertaxes', 'gradereport_visual');

/// Turn array into XML string and output.
$xml = XML_serialize($settings);
echo $xml;

/// Clean up cookie if it was created.
if($cookiewasset) {
    $_COOKIE['MoodleSession' . $_GET['sessioncookie']] = null;
    $_COOKIE['MoodleSessionTest' . $_GET['sessioncookie']] = null;
}
?>
