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
 * File witch defines the abstract class visualization witch all visualizations must extend.
 * Also defines serveral classes used by the visualization class and classes that extend it.
 * @package gradebook
 */

/**
 * Selector class that represents the selector UI widget in the flash/flex front end.
 * @package gradebook
 */
class selector {
    public $param;
    public $options;
    public $active;
    
    /**
     * Selector constructer.
     * @param string $param the URI value that the selector will add to the URL from witch the data for the visualization is loaded.
     * @param array $options an array of options (strings) that the selector will show where the key is the value of $param to be added to the URL.
     * @param string $active the key of the active element in $options that will show up as selected by defualt in the UI widget.
     */
    public function __construct($param, array $options, $active = null) {
        $this->param = $param;
        $this->options = $options;
        $this->active = $active;
    }
}

/**
 * Edge class that represents a set of edges that will be created based on the data
 * recvied by the front end.
 * @package gradebook
 */
class edge {
    public $sortby;
    public $groupby;
    
    /**
     * Edge constructer.
     * @param array $sortby array of data feilds the nodes should be sorted by whell creating the edges.
     * @param array $groupby array of data feilds the nodes will be grouped by when creating the edges.
     */
    public function __construct($sortby = null, $groupby = null) {
        $this->sortby = $sortby;
        $this->groupby = $groupby;
    }
}

/**
 * Encoder class that represents an encoder that will be applied to the visualization.
 * @package gradebook
 */
class encoder {
    const ENCODER_DEFUALT = 1;
    const ENCODER_COLOR = 1;
    const ENCODER_SHAPE = 2; 
    const ENCODER_SIZE = 3;
    
    /// Each encoder class needs to have a unique int id so it can be
    /// paird with a legend class. $counter holds the last id used in a
    /// encoder class + 1, so the next encoder class will have an $id of
    /// $counter.
    private static $counter = 0;
    
    public $id;
    public $type;
    public $settings;
    public $datafield;
    
    /**
     * Encoder constructer.
     * @param int $type the encoder type, one of ENCODER_COLOR, ENCODER_SHAPE or ENCODER_SIZE.
     * @param string $datafield the datafield of the data the encoder will effect.
     * @param array $settings the settings that will be passed to the encoder constructer in flare (in the front end).
     */
    public function __construct($type, $datafield, array $settings = null) {
        $this->type = $type;
        $this->settings = $settings;
        $this->datafield = $datafield;
        $this->id = self::$counter++;
    }
}

/**
 * Legend class representing a legend in the visualization.
 * Each legend is based on an encoder.
 * @package gradebook
 */
class legend {
    public $encoder;
    
    public $show;
    
    /**
     * Legend constructer.
     * @param object $encoder the encoder this legend is based on.
     * @param string $show the value to be selected by defualt in the legend, if null all are selected.
     */
    public function __construct($encoder, $show = null) {
        $this->encoder = $encoder;
        $this->show = $show;
    }
}

/**
 * Abstract visualization class that all visualization deftions (classes) must extend.
 * @package gradebook 
 */
abstract class visualization {
    /// Layout types.
    /// Currently only LAYOUT_AXIS has been fully implmented and tested,
    /// others may have unexpected results or errors in the front end.
    /// TODO: Add support for and test all layout types.
    const LAYOUT_DEFAULT = 1;
    const LAYOUT_AXIS = 1;
    const LAYOUT_CIRCLE = 2;
    const LAYOUT_DENDROGRAM = 3;
    const LAYOUT_FORCEDIRECTED = 4;
    const LAYOUT_INDENTEDTREE = 5;
    const LAYOUT_NODELINKTREE = 6;
    const LAYOUT_PIE = 7;
    const LAYOUT_RADIALTREE = 8;
    const LAYOUT_RANDOM = 9;
    const LAYOUT_STACKEDAREA = 10;
    const LAYOUT_TREEMAP = 11;
    
    /// Shape types for edges and nodes.
    /// Currently only SHAPE_HORIZONTAL_BAR, SHAPE_VERTICAL_BAR,
    /// and SHAPE_CARDINAL have been fully tested, others may have
    /// unexpected results or errors in the front end.
    /// SHAPE_HORIZONTAL_BAR will trun into SHAPE_VERTICAL_BAR when 
    /// inverted and vice versa.
    /// TODO: Test and add support for all shapes.
    const SHAPE_BEZIER = 1;
    const SHAPE_BLOCK = -1;
    const SHAPE_CARDINAL = 2;
    const SHAPE_HORIZONTAL_BAR = -5;
    const SHAPE_LINE = 0;
    const SHAPE_POLYBLOB = -3;
    const SHAPE_POLYGON = -2;
    const SHAPE_VERTICAL_BAR = -4;
    const SHAPE_WEDGE = -6;
    
    /**
     * Visualization name, name displayed in visualization selector and other places.
     * @var string $name
     */
    public $name;
    
    /**
     * The layout type to be used in the visualization. Must be one of the LAYOUT_* constants.
     * @var int $layout
     */
    public $layout = self::LAYOUT_DEFAULT;
   
    /**
     * Array of settings to be past to the layout constructor in the front end.
     * Thess will be diffrent for each layout type.
     * @var array $layoutsettings
     */
    public $layoutsettings = null;
   
    /**
     * If true the data past to the front end will be filiterd by the currently 
     * selected group in moodle.
     * @var bool $usegroups
     */
    public $usegroups = false;
   
    /**
     * Array of edge objects that will be used to create the edges in the 
     * visualization.
     * @var array $edges
     */
    public $edges = null;
   
    /**
     * The shape to be used for the nodes in the visualization. 
     * Must be one of the SHAPE_* constants.
     * @var int $nodeshape
     */
    public $nodeshape = null;
   
    /**
     * The edge shape to be used for the edges in the visualization.
     * Must be one of the SHAPE_* constants.
     * @var int $edgeshape
     */
    public $edgeshape = null;
    
    /**
     * Array of selector objects that repersent sellector UI widgets
     * in the flash front end.
     * @var array $selectors
     */
    public $selectors = null;
    
    /**
     * Font to be used threw out the visualization.
     * @var string $font
     */
    public $font = 'monospace';
    
    /**
     * Size of the font to be used in the visualization.
     * @var int $fontsize
     */
    public $fontsize = 20;
    
    /**
     * Array of legend objects that repersent the legends that will
     * be shown in the visualization.
     * @var array $legends
     */
    public $legends = null;

    /**
     * Data feild for the xaxis if using axis layout type.
     * @var string $xaxis
     */
    public $xaxis;
    
    /**
     * Data feild for the yaxis if using axis layout type.
     * @var string $yaxis
     */
    public $yaxis;
    
    /**
     * The format to use to encode the xaxis labels.
     * @var string $xaxislabelformat
     */
    public $xaxislabelformat;
    
    /**
     * The format to use to encode the yaxis labels.
     * @var string $yaxislabelformat
     */
    public $yaxislabelformat;
    
    /**
     * The minium value of the x axis.
     * @var float $xaxismin
     */
    public $xaxismin;
    
    /**
     * The maxium value of the x axis.
     * @var float $xaxismax
     */
    public $xaxismax;
    
    /**
     * The minium value of the y axis.
     * @var float $yaxismin
     */
    public $yaxismin;
    
    /**
     * The maxium value of the y axis.
     * @var float $yaxismax
     */
    public $yaxismax;
    
    /**
     * The x axis title.
     * @var string $xaxislabel
     */
    public $xaxislabel;
    
    /**
     * The y axis title.
     * @var string $yaxislabel 
     */
    public $yaxislabel;
    
    /**
     * The offset in pixels the y axis labels will be moved in the y direction
     * @var float $yaxisyoffset 
     */
    public $yaxisyoffset;
    
    /**
     * The offset in pixels the y axis labels will be moved in the x direction
     * @var float $yaxisxoffset 
     */
    public $yaxisxoffset;
    
    /**
     * The offset in pixels the x axis labels will be moved in the y direction
     * @var float $xaxisyoffset 
     */
    public $xaxisyoffset;
    
    /**
     * The offset in pixels the x axis labels will be moved in the x direction
     * @var float $xaxisxoffset 
     */
    public $xaxisxoffset;
    
    /**
    * The title that will be show in the visualization.
    * @var string $title
    */
    public $title;
    
    /**
     * The capability required of the user to view this visualization.
     * If null, no capability is needed.
     * @var string $capability
     */
    public $capability = null;
    
    /**
     * Array of encoders to apply to the visualization.
     * @var array $encoders
     */
    public $encoders = null;
    
    /**
     * Background color of the visualization.
     * @var string $backgroundcolor
     */
    public $backgroundcolor = 'ffffff';
    
    /**
     * Width of the embedded flash applet.
     * @var int $width
     */
    public $width = 800;
    
    /**
     * Width of the embedded flash applet.
     * @var int $height
     */
    public $height = 600;
    
    /**
     * Frame rate of the flash applet.
     * @var int $framerate
     */
    public $framerate = 30;
    
    /**
     * Default quality setting for the flash applet.
     * @var string $quality
     */
    public $quality = "high";

    /**
     * Background color of the pop up widget.
     * @var string $popupbgcolor 
     */
    public $popupbgcolor = '7777ff';

    /**
     * Background alpha value of the pop up widget.
     * @var float $popupbgalpha
     */
    public $popupbgalpha = 0.60;
    
    /**
     * Color of the border of the pop up widget.
     * @var string $popuplinecolor
     */
    public $popuplinecolor = '0000ff';
    
    /**
     * Alpha value of the border of the pop up widget
     * @var float $popuplinealpha
     */
    public $popuplinealpha = 0.3;
    
    /**
     * Line width of the border of the pop up widget
     * @var int $popuplinesize
     */
    public $popuplinesize = 3;
    
    /**
     * Font to be used in the pop up widget
     * @var string $popupfont
     */
    public $popupfont = 'monospace';
    
    /**
     * Font size used in the pop up widget
     * @var int $popupfontsize
     */
    public $popupfontsize = 12;

    /**
     * Background color of the button widget
     * @var string buttonbgcolor
     */
    public $buttonbgcolor = '9999FF';
    
    /**
     * Background alpha value of the button widget.
     * @var float $buttonbgalpha 
     */
    public $buttonbgalpha = 0.6;
    
    /**
     * Font used on button widgets.
     * @var string $buttonfont
     */
    public $buttonfont = 'monospace';
    
    /**
     * Font size used on button widgets.
     * @var int $buttonfontsize
     */
    public $buttonfontsize = 12;
    
    /**
     * Line width of the border of the button widget.
     * @var int $buttonlinesize
     */
    public $buttonlinesize = 1;
    
    /**
     * Color of the border of the button widget.
     *@var string $buttonlinecolor
     */
    public $buttonlinecolor = '4444FF';
    
    /**
     * Alpha value of the border for the button widget.
     * @var flaot $buttonlinealpha
     */
    public $buttonlinealpha = 0.3;

    /**
     * Visualization constructer.
     * Sets name of visualization.
     * @param string $name The name of the visualization.
     */
    public function __construct($name) {
        $this->name = $name;
    }
    
    /**
     * Report_data function that will be called to generate the
     * data sent to the front end. All visualization deftions must
     * implment this function.
     * @param object $visualreport The visual report object.
     * @returns array Returns an 2d array repersenting a table of data to be sent to the front end.
     */
    abstract public function report_data($visualreport);
}
?>