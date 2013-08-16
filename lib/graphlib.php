<?php

/**
 * Graph Class. PHP Class to draw line, point, bar, and area graphs, including numeric x-axis and double y-axis.
 * Version: 1.6.3
 * Copyright (C) 2000  Herman Veluwenkamp
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * Copy of GNU Lesser General Public License at: http://www.gnu.org/copyleft/lesser.txt
 * Contact author at: hermanV@mindless.com
 *
 * @package    core
 * @subpackage lib
 */

defined('MOODLE_INTERNAL') || die();

/* This file contains modifications by Martin Dougiamas
 * as part of Moodle (http://moodle.com).  Modified lines
 * are marked with "Moodle".
 */

/**
 * @package moodlecore
 */
class graph {
  var $image;
  var $debug             =   FALSE;        // be careful!!
  var $calculated        =   array();      // array of computed values for chart
  var $parameter         =   array(        // input parameters
    'width'              =>  320,          // default width of image
    'height'             =>  240,          // default height of image
    'file_name'          => 'none',        // name of file for file to be saved as.
                                           //  NOTE: no suffix required. this is determined from output_format below.
    'output_format'      => 'PNG',         // image output format. 'GIF', 'PNG', 'JPEG'. default 'PNG'.

    'seconds_to_live'    =>  0,            // expiry time in seconds (for HTTP header)
    'hours_to_live'      =>  0,            // expiry time in hours (for HTTP header)
    'path_to_fonts'      => 'fonts/',      // path to fonts folder. don't forget *trailing* slash!!
                                           //   for WINDOZE this may need to be the full path, not relative.

    'title'              => 'Graph Title', // text for graph title
    'title_font'         => 'default.ttf',   // title text font. don't forget to set 'path_to_fonts' above.
    'title_size'         =>  16,           // title text point size
    'title_colour'       => 'black',       // colour for title text

    'x_label'            => '',            // if this is set then this text is printed on bottom axis of graph.
    'y_label_left'       => '',            // if this is set then this text is printed on left axis of graph.
    'y_label_right'      => '',            // if this is set then this text is printed on right axis of graph.

    'label_size'         =>  8,           // label text point size
    'label_font'         => 'default.ttf', // label text font. don't forget to set 'path_to_fonts' above.
    'label_colour'       => 'gray33',      // label text colour
    'y_label_angle'      =>  90,           // rotation of y axis label

    'x_label_angle'      =>  90,            // rotation of y axis label

    'outer_padding'      =>  5,            // padding around outer text. i.e. title, y label, and x label.
    'inner_padding'      =>  0,            // padding beteen axis text and graph.
    'x_inner_padding'      =>  5,            // padding beteen axis text and graph.
    'y_inner_padding'      =>  6,            // padding beteen axis text and graph.
    'outer_border'       => 'none',        // colour of border aound image, or 'none'.
    'inner_border'       => 'black',       // colour of border around actual graph, or 'none'.
    'inner_border_type'  => 'box',         // 'box' for all four sides, 'axis' for x/y axis only,
                                           // 'y' or 'y-left' for y axis only, 'y-right' for right y axis only,
                                           // 'x' for x axis only, 'u' for both left and right y axis and x axis.
    'outer_background'   => 'none',        // background colour of entire image.
    'inner_background'   => 'none',        // background colour of plot area.

    'y_min_left'         =>  0,            // this will be reset to minimum value if there is a value lower than this.
    'y_max_left'         =>  0,            // this will be reset to maximum value if there is a value higher than this.
    'y_min_right'        =>  0,            // this will be reset to minimum value if there is a value lower than this.
    'y_max_right'        =>  0,            // this will be reset to maximum value if there is a value higher than this.
    'x_min'              =>  0,            // only used if x axis is numeric.
    'x_max'              =>  0,            // only used if x axis is numeric.

    'y_resolution_left'  =>  1,            // scaling for rounding of y axis max value.
                                           // if max y value is 8645 then
                                           // if y_resolution is 0, then y_max becomes 9000.
                                           // if y_resolution is 1, then y_max becomes 8700.
                                           // if y_resolution is 2, then y_max becomes 8650.
                                           // if y_resolution is 3, then y_max becomes 8645.
                                           // get it?
    'y_decimal_left'     =>  0,            // number of decimal places for y_axis text.
    'y_resolution_right' =>  2,            // ... same for right hand side
    'y_decimal_right'    =>  0,            // ... same for right hand side
    'x_resolution'       =>  2,            // only used if x axis is numeric.
    'x_decimal'          =>  0,            // only used if x axis is numeric.

    'point_size'         =>  4,            // default point size. use even number for diamond or triangle to get nice look.
    'brush_size'         =>  4,            // default brush size for brush line.
    'brush_type'         => 'circle',      // type of brush to use to draw line. choose from the following
                                           //   'circle', 'square', 'horizontal', 'vertical', 'slash', 'backslash'
    'bar_size'           =>  0.8,          // size of bar to draw. <1 bars won't touch
                                           //   1 is full width - i.e. bars will touch.
                                           //   >1 means bars will overlap.
    'bar_spacing'        =>  10,           // space in pixels between group of bars for each x value.
    'shadow_offset'      =>  3,            // draw shadow at this offset, unless overidden by data parameter.
    'shadow'             => 'grayCC',      // 'none' or colour of shadow.
    'shadow_below_axis'  => true,         // whether to draw shadows of bars and areas below the x/zero axis.


    'x_axis_gridlines'   => 'auto',        // if set to a number then x axis is treated as numeric.
    'y_axis_gridlines'   =>  6,            // number of gridlines on y axis.
    'zero_axis'          => 'none',        // colour to draw zero-axis, or 'none'.


    'axis_font'          => 'default.ttf', // axis text font. don't forget to set 'path_to_fonts' above.
    'axis_size'          =>  8,            // axis text font size in points
    'axis_colour'        => 'gray33',      // colour of axis text.
    'y_axis_angle'       =>  0,            // rotation of axis text.
    'x_axis_angle'       =>  0,            // rotation of axis text.

    'y_axis_text_left'   =>  1,            // whether to print left hand y axis text. if 0 no text, if 1 all ticks have text,
    'x_axis_text'        =>  1,            //   if 4 then print every 4th tick and text, etc...
    'y_axis_text_right'  =>  0,            // behaviour same as above for right hand y axis.

    'x_offset'           =>  0.5,          // x axis tick offset from y axis as fraction of tick spacing.
    'y_ticks_colour'     => 'black',       // colour to draw y ticks, or 'none'
    'x_ticks_colour'     => 'black',       // colour to draw x ticks, or 'none'
    'y_grid'             => 'line',        // grid lines. set to 'line' or 'dash'...
    'x_grid'             => 'line',        //   or if set to 'none' print nothing.
    'grid_colour'        => 'grayEE',      // default grid colour.
    'tick_length'        =>  4,            // length of ticks in pixels. can be negative. i.e. outside data drawing area.

    'legend'             => 'none',        // default. no legend.
                                          // otherwise: 'top-left', 'top-right', 'bottom-left', 'bottom-right',
                                          //   'outside-top', 'outside-bottom', 'outside-left', or 'outside-right'.
    'legend_offset'      =>  10,           // offset in pixels from graph or outside border.
    'legend_padding'     =>  5,            // padding around legend text.
    'legend_font'        => 'default.ttf',   // legend text font. don't forget to set 'path_to_fonts' above.
    'legend_size'        =>  8,            // legend text point size.
    'legend_colour'      => 'black',       // legend text colour.
    'legend_border'      => 'none',        // legend border colour, or 'none'.

    'decimal_point'      => '.',           // symbol for decimal separation  '.' or ',' *european support.
    'thousand_sep'       => ',',           // symbol for thousand separation ',' or ''

  );
  var $y_tick_labels     =   null;         // array of text values for y-axis tick labels
  var $offset_relation   =   null;         // array of offsets for different sets of data


    // init all text - title, labels, and axis text.
    function init() {

      /// Moodle mods:  overrides the font path and encodings

      global $CFG;

      /// A default.ttf is searched for in this order:
      ///      dataroot/lang/xx_local/fonts
      ///      dataroot/lang/xx/fonts
      ///      dirroot/lang/xx/fonts
      ///      dataroot/lang
      ///      lib/

      $currlang = current_language();
      if (file_exists("$CFG->dataroot/lang/".$currlang."_local/fonts/default.ttf")) {
          $fontpath = "$CFG->dataroot/lang/".$currlang."_local/fonts/";
      } else if (file_exists("$CFG->dataroot/lang/$currlang/fonts/default.ttf")) {
          $fontpath = "$CFG->dataroot/lang/$currlang/fonts/";
      } else if (file_exists("$CFG->dirroot/lang/$currlang/fonts/default.ttf")) {
          $fontpath = "$CFG->dirroot/lang/$currlang/fonts/";
      } else if (file_exists("$CFG->dataroot/lang/default.ttf")) {
          $fontpath = "$CFG->dataroot/lang/";
      } else {
          $fontpath = "$CFG->libdir/";
      }

      $this->parameter['path_to_fonts'] = $fontpath;

      /// End Moodle mods



      $this->calculated['outer_border'] = $this->calculated['boundary_box'];

      // outer padding
      $this->calculated['boundary_box']['left']   += $this->parameter['outer_padding'];
      $this->calculated['boundary_box']['top']    += $this->parameter['outer_padding'];
      $this->calculated['boundary_box']['right']  -= $this->parameter['outer_padding'];
      $this->calculated['boundary_box']['bottom'] -= $this->parameter['outer_padding'];

      $this->init_x_axis();
      $this->init_y_axis();
      $this->init_legend();
      $this->init_labels();

      //  take into account tick lengths
      $this->calculated['bottom_inner_padding'] = $this->parameter['x_inner_padding'];
      if (($this->parameter['x_ticks_colour'] != 'none') && ($this->parameter['tick_length'] < 0))
        $this->calculated['bottom_inner_padding'] -= $this->parameter['tick_length'];
      $this->calculated['boundary_box']['bottom'] -= $this->calculated['bottom_inner_padding'];

      $this->calculated['left_inner_padding'] = $this->parameter['y_inner_padding'];
      if ($this->parameter['y_axis_text_left']) {
        if (($this->parameter['y_ticks_colour'] != 'none') && ($this->parameter['tick_length'] < 0))
          $this->calculated['left_inner_padding'] -= $this->parameter['tick_length'];
      }
      $this->calculated['boundary_box']['left'] += $this->calculated['left_inner_padding'];

      $this->calculated['right_inner_padding'] = $this->parameter['y_inner_padding'];
      if ($this->parameter['y_axis_text_right']) {
        if (($this->parameter['y_ticks_colour'] != 'none') && ($this->parameter['tick_length'] < 0))
          $this->calculated['right_inner_padding'] -= $this->parameter['tick_length'];
      }
      $this->calculated['boundary_box']['right'] -= $this->calculated['right_inner_padding'];

      // boundaryBox now has coords for plotting area.
      $this->calculated['inner_border'] = $this->calculated['boundary_box'];

      $this->init_data();
      $this->init_x_ticks();
      $this->init_y_ticks();
    }

    function draw_text() {
      $colour = $this->parameter['outer_background'];
      if ($colour != 'none') $this->draw_rectangle($this->calculated['outer_border'], $colour, 'fill'); // graph background

      // draw border around image
      $colour = $this->parameter['outer_border'];
      if ($colour != 'none') $this->draw_rectangle($this->calculated['outer_border'], $colour, 'box'); // graph border

      $this->draw_title();
      $this->draw_x_label();
      $this->draw_y_label_left();
      $this->draw_y_label_right();
      $this->draw_x_axis();
      $this->draw_y_axis();
      if      ($this->calculated['y_axis_left']['has_data'])  $this->draw_zero_axis_left();  // either draw zero axis on left
      else if ($this->calculated['y_axis_right']['has_data']) $this->draw_zero_axis_right(); // ... or right.
      $this->draw_legend();

      // draw border around plot area
      $colour = $this->parameter['inner_background'];
      if ($colour != 'none') $this->draw_rectangle($this->calculated['inner_border'], $colour, 'fill'); // graph background

      // draw border around image
      $colour = $this->parameter['inner_border'];
      if ($colour != 'none') $this->draw_rectangle($this->calculated['inner_border'], $colour, $this->parameter['inner_border_type']); // graph border
    }

    function draw_stack() {
      $this->init();
      $this->draw_text();

      $yOrder = $this->y_order; // save y_order data.
      // iterate over each data set. order is very important if you want to see data correctly. remember shadows!!
      foreach ($yOrder as $set) {
        $this->y_order = array($set);
        $this->init_data();
        $this->draw_data();
      }
      $this->y_order = $yOrder; // revert y_order data.

      $this->output();
    }

    function draw() {
      $this->init();
      $this->draw_text();
      $this->draw_data();
      $this->output();
    }

    // draw a data set
    function draw_set($order, $set, $offset) {
      if ($offset) @$this->init_variable($colour, $this->y_format[$set]['shadow'], $this->parameter['shadow']);
      else $colour  = $this->y_format[$set]['colour'];
      @$this->init_variable($point,      $this->y_format[$set]['point'],      'none');
      @$this->init_variable($pointSize,  $this->y_format[$set]['point_size'],  $this->parameter['point_size']);
      @$this->init_variable($line,       $this->y_format[$set]['line'],       'none');
      @$this->init_variable($brushType,  $this->y_format[$set]['brush_type'],  $this->parameter['brush_type']);
      @$this->init_variable($brushSize,  $this->y_format[$set]['brush_size'],  $this->parameter['brush_size']);
      @$this->init_variable($bar,        $this->y_format[$set]['bar'],        'none');
      @$this->init_variable($barSize,    $this->y_format[$set]['bar_size'],    $this->parameter['bar_size']);
      @$this->init_variable($area,       $this->y_format[$set]['area'],       'none');

      $lastX = 0;
      $lastY = 'none';
      $fromX = 0;
      $fromY = 'none';

      //print "set $set<br />";
      //expand_pre($this->calculated['y_plot']);

      foreach ($this->x_data as $index => $x) {
        //print "index $index<br />";
        $thisY = $this->calculated['y_plot'][$set][$index];
        $thisX = $this->calculated['x_plot'][$index];

        //print "$thisX, $thisY <br />";

        if (($bar!='none') && (string)$thisY != 'none') {
            if ($relatedset = $this->offset_relation[$set]) {                               // Moodle
                $yoffset = $this->calculated['y_plot'][$relatedset][$index];                // Moodle
            } else {                                                                        // Moodle
                $yoffset = 0;                                                               // Moodle
            }                                                                               // Moodle
            //$this->bar($thisX, $thisY, $bar, $barSize, $colour, $offset, $set);           // Moodle
            $this->bar($thisX, $thisY, $bar, $barSize, $colour, $offset, $set, $yoffset);   // Moodle
        }

        if (($area!='none') && (((string)$lastY != 'none') && ((string)$thisY != 'none')))
          $this->area($lastX, $lastY, $thisX, $thisY, $area, $colour, $offset);

        if (($point!='none') && (string)$thisY != 'none') $this->plot($thisX, $thisY, $point, $pointSize, $colour, $offset);

        if (($line!='none') && ((string)$thisY != 'none')) {
          if ((string)$fromY != 'none')
            $this->line($fromX, $fromY, $thisX, $thisY, $line, $brushType, $brushSize, $colour, $offset);

          $fromY = $thisY; // start next line from here
          $fromX = $thisX; // ...
        } else {
          $fromY = 'none';
          $fromX = 'none';
        }

        $lastX = $thisX;
        $lastY = $thisY;
      }
    }

    function draw_data() {
      // cycle thru y data to be plotted
      // first check for drop shadows...
      foreach ($this->y_order as $order => $set) {
        @$this->init_variable($offset, $this->y_format[$set]['shadow_offset'], $this->parameter['shadow_offset']);
        @$this->init_variable($colour, $this->y_format[$set]['shadow'], $this->parameter['shadow']);
        if ($colour != 'none') $this->draw_set($order, $set, $offset);

      }

      // then draw data
      foreach ($this->y_order as $order => $set) {
        $this->draw_set($order, $set, 0);
      }
    }

    function draw_legend() {
      $position      = $this->parameter['legend'];
      if ($position == 'none') return; // abort if no border

      $borderColour  = $this->parameter['legend_border'];
      $offset        = $this->parameter['legend_offset'];
      $padding       = $this->parameter['legend_padding'];
      $height        = $this->calculated['legend']['boundary_box_all']['height'];
      $width         = $this->calculated['legend']['boundary_box_all']['width'];
      $graphTop      = $this->calculated['boundary_box']['top'];
      $graphBottom   = $this->calculated['boundary_box']['bottom'];
      $graphLeft     = $this->calculated['boundary_box']['left'];
      $graphRight    = $this->calculated['boundary_box']['right'];
      $outsideRight  = $this->calculated['outer_border']['right'];
      $outsideBottom = $this->calculated['outer_border']['bottom'];
      switch ($position) {
        case 'top-left':
          $top    = $graphTop  + $offset;
          $bottom = $graphTop  + $height + $offset;
          $left   = $graphLeft + $offset;
          $right  = $graphLeft + $width + $offset;

          break;
        case 'top-right':
          $top    = $graphTop   + $offset;
          $bottom = $graphTop   + $height + $offset;
          $left   = $graphRight - $width - $offset;
          $right  = $graphRight - $offset;

          break;
        case 'bottom-left':
          $top    = $graphBottom - $height - $offset;
          $bottom = $graphBottom - $offset;
          $left   = $graphLeft   + $offset;
          $right  = $graphLeft   + $width + $offset;

          break;
        case 'bottom-right':
          $top    = $graphBottom - $height - $offset;
          $bottom = $graphBottom - $offset;
          $left   = $graphRight  - $width - $offset;
          $right  = $graphRight  - $offset;
          break;

        case 'outside-top' :
          $top    = $graphTop;
          $bottom = $graphTop     + $height;
          $left   = $outsideRight - $width - $offset;
          $right  = $outsideRight - $offset;
          break;

        case 'outside-bottom' :
          $top    = $graphBottom  - $height;
          $bottom = $graphBottom;
          $left   = $outsideRight - $width - $offset;
          $right  = $outsideRight - $offset;
         break;

        case 'outside-left' :
          $top    = $outsideBottom - $height - $offset;
          $bottom = $outsideBottom - $offset;
          $left   = $graphLeft;
          $right  = $graphLeft     + $width;
         break;

        case 'outside-right' :
          $top    = $outsideBottom - $height - $offset;
          $bottom = $outsideBottom - $offset;
          $left   = $graphRight    - $width;
          $right  = $graphRight;
          break;
        default: // default is top left. no particular reason.
          $top    = $this->calculated['boundary_box']['top'];
          $bottom = $this->calculated['boundary_box']['top'] + $this->calculated['legend']['boundary_box_all']['height'];
          $left   = $this->calculated['boundary_box']['left'];
          $right  = $this->calculated['boundary_box']['right'] + $this->calculated['legend']['boundary_box_all']['width'];

    }
      // legend border
      if($borderColour!='none') $this->draw_rectangle(array('top' => $top,
                                                            'left' => $left,
                                                            'bottom' => $bottom,
                                                            'right' => $right), $this->parameter['legend_border'], 'box');

      // legend text
      $legendText = array('points' => $this->parameter['legend_size'],
                          'angle'  => 0,
                          'font'   => $this->parameter['legend_font'],
                          'colour' => $this->parameter['legend_colour']);

      $box = $this->calculated['legend']['boundary_box_max']['height']; // use max height for legend square size.
      $x = $left + $padding;
      $x_text = $x + $box * 2;
      $y = $top + $padding;

      foreach ($this->y_order as $set) {
        $legendText['text'] = $this->calculated['legend']['text'][$set];
        if ($legendText['text'] != 'none') {
          // if text exists then draw box and text
          $boxColour = $this->colour[$this->y_format[$set]['colour']];

          // draw box
          ImageFilledRectangle($this->image, $x, $y, $x + $box, $y + $box, $boxColour);

          // draw text
          $coords = array('x' => $x + $box * 2, 'y' => $y, 'reference' => 'top-left');
          $legendText['boundary_box'] = $this->calculated['legend']['boundary_box'][$set];
          $this->update_boundaryBox($legendText['boundary_box'], $coords);
          $this->print_TTF($legendText);
          $y += $padding + $box;
        }
      }

    }

    function draw_y_label_right() {
      if (!$this->parameter['y_label_right']) return;
      $x = $this->calculated['boundary_box']['right'] + $this->parameter['y_inner_padding'];
      if ($this->parameter['y_axis_text_right']) $x += $this->calculated['y_axis_right']['boundary_box_max']['width']
                                               + $this->calculated['right_inner_padding'];
      $y = ($this->calculated['boundary_box']['bottom'] + $this->calculated['boundary_box']['top']) / 2;

      $label = $this->calculated['y_label_right'];
      $coords = array('x' => $x, 'y' => $y, 'reference' => 'left-center');
      $this->update_boundaryBox($label['boundary_box'], $coords);
      $this->print_TTF($label);
    }


    function draw_y_label_left() {
      if (!$this->parameter['y_label_left']) return;
      $x = $this->calculated['boundary_box']['left'] - $this->parameter['y_inner_padding'];
      if ($this->parameter['y_axis_text_left']) $x -= $this->calculated['y_axis_left']['boundary_box_max']['width']
                                               + $this->calculated['left_inner_padding'];
      $y = ($this->calculated['boundary_box']['bottom'] + $this->calculated['boundary_box']['top']) / 2;

      $label = $this->calculated['y_label_left'];
      $coords = array('x' => $x, 'y' => $y, 'reference' => 'right-center');
      $this->update_boundaryBox($label['boundary_box'], $coords);
      $this->print_TTF($label);
    }

    function draw_title() {
      if (!$this->parameter['title']) return;
      //$y = $this->calculated['outside_border']['top'] + $this->parameter['outer_padding'];
      $y = $this->calculated['boundary_box']['top'] - $this->parameter['outer_padding'];
      $x = ($this->calculated['boundary_box']['right'] + $this->calculated['boundary_box']['left']) / 2;
      $label = $this->calculated['title'];
      $coords = array('x' => $x, 'y' => $y, 'reference' => 'bottom-center');
      $this->update_boundaryBox($label['boundary_box'], $coords);
      $this->print_TTF($label);
    }

    function draw_x_label() {
      if (!$this->parameter['x_label']) return;
      $y = $this->calculated['boundary_box']['bottom'] + $this->parameter['x_inner_padding'];
      if ($this->parameter['x_axis_text']) $y += $this->calculated['x_axis']['boundary_box_max']['height']
                                              + $this->calculated['bottom_inner_padding'];
      $x = ($this->calculated['boundary_box']['right'] + $this->calculated['boundary_box']['left']) / 2;
      $label = $this->calculated['x_label'];
      $coords = array('x' => $x, 'y' => $y, 'reference' => 'top-center');
      $this->update_boundaryBox($label['boundary_box'], $coords);
      $this->print_TTF($label);
    }

    function draw_zero_axis_left() {
      $colour = $this->parameter['zero_axis'];
      if ($colour == 'none') return;
      // draw zero axis on left hand side
      $this->calculated['zero_axis'] = round($this->calculated['boundary_box']['top']  + ($this->calculated['y_axis_left']['max'] * $this->calculated['y_axis_left']['factor']));
      ImageLine($this->image, $this->calculated['boundary_box']['left'], $this->calculated['zero_axis'], $this->calculated['boundary_box']['right'], $this->calculated['zero_axis'], $this->colour[$colour]);
    }

    function draw_zero_axis_right() {
      $colour = $this->parameter['zero_axis'];
      if ($colour == 'none') return;
      // draw zero axis on right hand side
      $this->calculated['zero_axis'] = round($this->calculated['boundary_box']['top']  + ($this->calculated['y_axis_right']['max'] * $this->calculated['y_axis_right']['factor']));
      ImageLine($this->image, $this->calculated['boundary_box']['left'], $this->calculated['zero_axis'], $this->calculated['boundary_box']['right'], $this->calculated['zero_axis'], $this->colour[$colour]);
    }

    function draw_x_axis() {
      $gridColour  = $this->colour[$this->parameter['grid_colour']];
      $tickColour  = $this->colour[$this->parameter['x_ticks_colour']];
      $axis_colour  = $this->parameter['axis_colour'];
      $xGrid       = $this->parameter['x_grid'];
      $gridTop     = $this->calculated['boundary_box']['top'];
      $gridBottom  = $this->calculated['boundary_box']['bottom'];

      if ($this->parameter['tick_length'] >= 0) {
        $tickTop     = $this->calculated['boundary_box']['bottom'] - $this->parameter['tick_length'];
        $tickBottom  = $this->calculated['boundary_box']['bottom'];
        $textBottom  = $tickBottom + $this->calculated['bottom_inner_padding'];
      } else {
        $tickTop     = $this->calculated['boundary_box']['bottom'];
        $tickBottom  = $this->calculated['boundary_box']['bottom'] - $this->parameter['tick_length'];
        $textBottom  = $tickBottom + $this->calculated['bottom_inner_padding'];
      }

      $axis_font    = $this->parameter['axis_font'];
      $axis_size    = $this->parameter['axis_size'];
      $axis_angle   = $this->parameter['x_axis_angle'];

      if ($axis_angle == 0)  $reference = 'top-center';
      if ($axis_angle > 0)   $reference = 'top-right';
      if ($axis_angle < 0)   $reference = 'top-left';
      if ($axis_angle == 90) $reference = 'top-center';

      //generic tag information. applies to all axis text.
      $axisTag = array('points' => $axis_size, 'angle' => $axis_angle, 'font' => $axis_font, 'colour' => $axis_colour);

      foreach ($this->calculated['x_axis']['tick_x'] as $set => $tickX) {
        // draw x grid if colour specified
        if ($xGrid != 'none') {
          switch ($xGrid) {
            case 'line':
              ImageLine($this->image, round($tickX), round($gridTop), round($tickX), round($gridBottom), $gridColour);
              break;
             case 'dash':
              ImageDashedLine($this->image, round($tickX), round($gridTop), round($tickX), round($gridBottom), $gridColour);
              break;
          }
        }

        if ($this->parameter['x_axis_text'] && !($set % $this->parameter['x_axis_text'])) { // test if tick should be displayed
          // draw tick
          if ($tickColour != 'none')
            ImageLine($this->image, round($tickX), round($tickTop), round($tickX), round($tickBottom), $tickColour);

          // draw axis text
          $coords = array('x' => $tickX, 'y' => $textBottom, 'reference' => $reference);
          $axisTag['text'] = $this->calculated['x_axis']['text'][$set];
          $axisTag['boundary_box'] = $this->calculated['x_axis']['boundary_box'][$set];
          $this->update_boundaryBox($axisTag['boundary_box'], $coords);
          $this->print_TTF($axisTag);
        }
      }
    }

    function draw_y_axis() {
      $gridColour  = $this->colour[$this->parameter['grid_colour']];
      $tickColour  = $this->colour[$this->parameter['y_ticks_colour']];
      $axis_colour  = $this->parameter['axis_colour'];
      $yGrid       = $this->parameter['y_grid'];
      $gridLeft    = $this->calculated['boundary_box']['left'];
      $gridRight   = $this->calculated['boundary_box']['right'];

      // axis font information
      $axis_font    = $this->parameter['axis_font'];
      $axis_size    = $this->parameter['axis_size'];
      $axis_angle   = $this->parameter['y_axis_angle'];
      $axisTag = array('points' => $axis_size, 'angle' => $axis_angle, 'font' => $axis_font, 'colour' => $axis_colour);


      if ($this->calculated['y_axis_left']['has_data']) {
        // LEFT HAND SIDE
        // left and right coords for ticks
        if ($this->parameter['tick_length'] >= 0) {
          $tickLeft     = $this->calculated['boundary_box']['left'];
          $tickRight    = $this->calculated['boundary_box']['left'] + $this->parameter['tick_length'];
        } else {
          $tickLeft     = $this->calculated['boundary_box']['left'] + $this->parameter['tick_length'];
          $tickRight    = $this->calculated['boundary_box']['left'];
        }
        $textRight      = $tickLeft - $this->calculated['left_inner_padding'];

        if ($axis_angle == 0)  $reference = 'right-center';
        if ($axis_angle > 0)   $reference = 'right-top';
        if ($axis_angle < 0)   $reference = 'right-bottom';
        if ($axis_angle == 90) $reference = 'right-center';

        foreach ($this->calculated['y_axis']['tick_y'] as $set => $tickY) {
          // draw y grid if colour specified
          if ($yGrid != 'none') {
            switch ($yGrid) {
              case 'line':
                ImageLine($this->image, round($gridLeft), round($tickY), round($gridRight), round($tickY), $gridColour);
                break;
               case 'dash':
                ImageDashedLine($this->image, round($gridLeft), round($tickY), round($gridRight), round($tickY), $gridColour);
                break;
            }
          }

          // y axis text
          if ($this->parameter['y_axis_text_left'] && !($set % $this->parameter['y_axis_text_left'])) { // test if tick should be displayed
            // draw tick
            if ($tickColour != 'none')
              ImageLine($this->image, round($tickLeft), round($tickY), round($tickRight), round($tickY), $tickColour);

            // draw axis text...
            $coords = array('x' => $textRight, 'y' => $tickY, 'reference' => $reference);
            $axisTag['text'] = $this->calculated['y_axis_left']['text'][$set];
            $axisTag['boundary_box'] = $this->calculated['y_axis_left']['boundary_box'][$set];
            $this->update_boundaryBox($axisTag['boundary_box'], $coords);
            $this->print_TTF($axisTag);
          }
        }
      }

      if ($this->calculated['y_axis_right']['has_data']) {
        // RIGHT HAND SIDE
        // left and right coords for ticks
        if ($this->parameter['tick_length'] >= 0) {
          $tickLeft     = $this->calculated['boundary_box']['right'] - $this->parameter['tick_length'];
          $tickRight    = $this->calculated['boundary_box']['right'];
        } else {
          $tickLeft     = $this->calculated['boundary_box']['right'];
          $tickRight    = $this->calculated['boundary_box']['right'] - $this->parameter['tick_length'];
        }
        $textLeft       = $tickRight+ $this->calculated['left_inner_padding'];

        if ($axis_angle == 0)  $reference = 'left-center';
        if ($axis_angle > 0)   $reference = 'left-bottom';
        if ($axis_angle < 0)   $reference = 'left-top';
        if ($axis_angle == 90) $reference = 'left-center';

        foreach ($this->calculated['y_axis']['tick_y'] as $set => $tickY) {
          if (!$this->calculated['y_axis_left']['has_data'] && $yGrid != 'none') { // draw grid if not drawn already (above)
            switch ($yGrid) {
              case 'line':
                ImageLine($this->image, round($gridLeft), round($tickY), round($gridRight), round($tickY), $gridColour);
                break;
               case 'dash':
                ImageDashedLine($this->image, round($gridLeft), round($tickY), round($gridRight), round($tickY), $gridColour);
                break;
            }
          }

          if ($this->parameter['y_axis_text_right'] && !($set % $this->parameter['y_axis_text_right'])) { // test if tick should be displayed
            // draw tick
            if ($tickColour != 'none')
              ImageLine($this->image, round($tickLeft), round($tickY), round($tickRight), round($tickY), $tickColour);

            // draw axis text...
            $coords = array('x' => $textLeft, 'y' => $tickY, 'reference' => $reference);
            $axisTag['text'] = $this->calculated['y_axis_right']['text'][$set];
            $axisTag['boundary_box'] = $this->calculated['y_axis_left']['boundary_box'][$set];
            $this->update_boundaryBox($axisTag['boundary_box'], $coords);
            $this->print_TTF($axisTag);
          }
        }
      }
    }

    function init_data() {
      $this->calculated['y_plot'] = array(); // array to hold pixel plotting coords for y axis
      $height = $this->calculated['boundary_box']['bottom'] - $this->calculated['boundary_box']['top'];
      $width  = $this->calculated['boundary_box']['right'] - $this->calculated['boundary_box']['left'];

      // calculate pixel steps between axis ticks.
      $this->calculated['y_axis']['step'] = $height / ($this->parameter['y_axis_gridlines'] - 1);

      // calculate x ticks spacing taking into account x offset for ticks.
      $extraTick  = 2 * $this->parameter['x_offset']; // extra tick to account for padding
      $numTicks = $this->calculated['x_axis']['num_ticks'] - 1;    // number of x ticks

      // Hack by rodger to avoid division by zero, see bug 1231
      if ($numTicks==0) $numTicks=1;

      $this->calculated['x_axis']['step'] = $width / ($numTicks + $extraTick);
      $widthPlot = $width - ($this->calculated['x_axis']['step'] * $extraTick);
      $this->calculated['x_axis']['step'] = $widthPlot / $numTicks;

      //calculate factor for transforming x,y physical coords to logical coords for right hand y_axis.
      $y_range = $this->calculated['y_axis_right']['max'] - $this->calculated['y_axis_right']['min'];
      $y_range = ($y_range ? $y_range : 1);
      $this->calculated['y_axis_right']['factor'] = $height / $y_range;

      //calculate factor for transforming x,y physical coords to logical coords for left hand axis.
      $yRange = $this->calculated['y_axis_left']['max'] - $this->calculated['y_axis_left']['min'];
      $yRange = ($yRange ? $yRange : 1);
      $this->calculated['y_axis_left']['factor'] = $height / $yRange;
      if ($this->parameter['x_axis_gridlines'] != 'auto') {
        $xRange = $this->calculated['x_axis']['max'] - $this->calculated['x_axis']['min'];
        $xRange = ($xRange ? $xRange : 1);
        $this->calculated['x_axis']['factor'] = $widthPlot / $xRange;
      }

      //expand_pre($this->calculated['boundary_box']);
      // cycle thru all data sets...
      $this->calculated['num_bars'] = 0;
      foreach ($this->y_order as $order => $set) {
        // determine how many bars there are
        if (isset($this->y_format[$set]['bar']) && ($this->y_format[$set]['bar'] != 'none')) {
          $this->calculated['bar_offset_index'][$set] = $this->calculated['num_bars']; // index to relate bar with data set.
          $this->calculated['num_bars']++;
        }

        // calculate y coords for plotting data
        foreach ($this->x_data as $index => $x) {
          $this->calculated['y_plot'][$set][$index] = $this->y_data[$set][$index];

          if ((string)$this->y_data[$set][$index] != 'none') {

            if (isset($this->y_format[$set]['y_axis']) && $this->y_format[$set]['y_axis'] == 'right') {
              $this->calculated['y_plot'][$set][$index] =
                round(($this->y_data[$set][$index] - $this->calculated['y_axis_right']['min'])
                  * $this->calculated['y_axis_right']['factor']);
            } else {
              //print "$set $index<br />";
              $this->calculated['y_plot'][$set][$index] =
                round(($this->y_data[$set][$index] - $this->calculated['y_axis_left']['min'])
                  * $this->calculated['y_axis_left']['factor']);
            }

          }
        }
      }
      //print "factor ".$this->calculated['x_axis']['factor']."<br />";
      //expand_pre($this->calculated['x_plot']);

      // calculate bar parameters if bars are to be drawn.
      if ($this->calculated['num_bars']) {
        $xStep       = $this->calculated['x_axis']['step'];
        $totalWidth  = $this->calculated['x_axis']['step'] - $this->parameter['bar_spacing'];
        $barWidth    = $totalWidth / $this->calculated['num_bars'];

        $barX = ($barWidth - $totalWidth) / 2; // starting x offset
        for ($i=0; $i < $this->calculated['num_bars']; $i++) {
          $this->calculated['bar_offset_x'][$i] = $barX;
          $barX += $barWidth; // add width of bar to x offset.
        }
        $this->calculated['bar_width'] = $barWidth;
      }


    }

    function init_x_ticks() {
      // get coords for x axis ticks and data plots
      //$xGrid       = $this->parameter['x_grid'];
      $xStep       = $this->calculated['x_axis']['step'];
      $ticksOffset = $this->parameter['x_offset']; // where to start drawing ticks relative to y axis.
      $gridLeft    = $this->calculated['boundary_box']['left'] + ($xStep * $ticksOffset); // grid x start
      $tickX       = $gridLeft; // tick x coord

      foreach ($this->calculated['x_axis']['text'] as $set => $value) {
        //print "index: $set<br />";
        // x tick value
        $this->calculated['x_axis']['tick_x'][$set] = $tickX;
        // if num ticks is auto then x plot value is same as x  tick
        if ($this->parameter['x_axis_gridlines'] == 'auto') $this->calculated['x_plot'][$set] = round($tickX);
        //print $this->calculated['x_plot'][$set].'<br />';
        $tickX += $xStep;
      }

      //print "xStep: $xStep <br />";
      // if numeric x axis then calculate x coords for each data point. this is seperate from x ticks.
      $gridX = $gridLeft;
      if (empty($this->calculated['x_axis']['factor'])) {
          $this->calculated['x_axis']['factor'] = 0;
      }
      if (empty($this->calculated['x_axis']['min'])) {
          $this->calculated['x_axis']['min'] = 0;
      }
      $factor = $this->calculated['x_axis']['factor'];
      $min = $this->calculated['x_axis']['min'];

      if ($this->parameter['x_axis_gridlines'] != 'auto') {
        foreach ($this->x_data as $index => $x) {
          //print "index: $index, x: $x<br />";
          $offset = $x - $this->calculated['x_axis']['min'];

          //$gridX = ($offset * $this->calculated['x_axis']['factor']);
          //print "offset: $offset <br />";
          //$this->calculated['x_plot'][$set] = $gridLeft + ($offset * $this->calculated['x_axis']['factor']);

          $this->calculated['x_plot'][$index] = $gridLeft + ($x - $min) * $factor;

          //print $this->calculated['x_plot'][$set].'<br />';
        }
      }
      //expand_pre($this->calculated['boundary_box']);
      //print "factor ".$this->calculated['x_axis']['factor']."<br />";
      //expand_pre($this->calculated['x_plot']);
    }

    function init_y_ticks() {
      // get coords for y axis ticks

      $yStep      = $this->calculated['y_axis']['step'];
      $gridBottom = $this->calculated['boundary_box']['bottom'];
      $tickY      = $gridBottom; // tick y coord

      for ($i = 0; $i < $this->parameter['y_axis_gridlines']; $i++) {
        $this->calculated['y_axis']['tick_y'][$i] = $tickY;
        $tickY   -= $yStep;
      }

    }

    function init_labels() {
      if ($this->parameter['title']) {
        $size = $this->get_boundaryBox(
          array('points' => $this->parameter['title_size'],
                'angle'  => 0,
                'font'   => $this->parameter['title_font'],
                'text'   => $this->parameter['title']));
        $this->calculated['title']['boundary_box']  = $size;
        $this->calculated['title']['text']         = $this->parameter['title'];
        $this->calculated['title']['font']         = $this->parameter['title_font'];
        $this->calculated['title']['points']       = $this->parameter['title_size'];
        $this->calculated['title']['colour']       = $this->parameter['title_colour'];
        $this->calculated['title']['angle']        = 0;

        $this->calculated['boundary_box']['top'] += $size['height'] + $this->parameter['outer_padding'];
        //$this->calculated['boundary_box']['top'] += $size['height'];

      } else $this->calculated['title']['boundary_box'] = $this->get_null_size();

      if ($this->parameter['y_label_left']) {
        $this->calculated['y_label_left']['text']    = $this->parameter['y_label_left'];
        $this->calculated['y_label_left']['angle']   = $this->parameter['y_label_angle'];
        $this->calculated['y_label_left']['font']    = $this->parameter['label_font'];
        $this->calculated['y_label_left']['points']  = $this->parameter['label_size'];
        $this->calculated['y_label_left']['colour']  = $this->parameter['label_colour'];

        $size = $this->get_boundaryBox($this->calculated['y_label_left']);
        $this->calculated['y_label_left']['boundary_box']  = $size;
        //$this->calculated['boundary_box']['left'] += $size['width'] + $this->parameter['inner_padding'];
        $this->calculated['boundary_box']['left'] += $size['width'];

      } else $this->calculated['y_label_left']['boundary_box'] = $this->get_null_size();

      if ($this->parameter['y_label_right']) {
        $this->calculated['y_label_right']['text']    = $this->parameter['y_label_right'];
        $this->calculated['y_label_right']['angle']   = $this->parameter['y_label_angle'];
        $this->calculated['y_label_right']['font']    = $this->parameter['label_font'];
        $this->calculated['y_label_right']['points']  = $this->parameter['label_size'];
        $this->calculated['y_label_right']['colour']  = $this->parameter['label_colour'];

        $size = $this->get_boundaryBox($this->calculated['y_label_right']);
        $this->calculated['y_label_right']['boundary_box']  = $size;
        //$this->calculated['boundary_box']['right'] -= $size['width'] + $this->parameter['inner_padding'];
        $this->calculated['boundary_box']['right'] -= $size['width'];

      } else $this->calculated['y_label_right']['boundary_box'] = $this->get_null_size();

      if ($this->parameter['x_label']) {
        $this->calculated['x_label']['text']         = $this->parameter['x_label'];
        $this->calculated['x_label']['angle']        = $this->parameter['x_label_angle'];
        $this->calculated['x_label']['font']         = $this->parameter['label_font'];
        $this->calculated['x_label']['points']       = $this->parameter['label_size'];
        $this->calculated['x_label']['colour']       = $this->parameter['label_colour'];

        $size = $this->get_boundaryBox($this->calculated['x_label']);
        $this->calculated['x_label']['boundary_box']  = $size;
        //$this->calculated['boundary_box']['bottom'] -= $size['height'] + $this->parameter['inner_padding'];
        $this->calculated['boundary_box']['bottom'] -= $size['height'];

      } else $this->calculated['x_label']['boundary_box'] = $this->get_null_size();

    }


    function init_legend() {
      $this->calculated['legend'] = array(); // array to hold calculated values for legend.
      //$this->calculated['legend']['boundary_box_max'] = array('height' => 0, 'width' => 0);
      $this->calculated['legend']['boundary_box_max'] = $this->get_null_size();
      if ($this->parameter['legend'] == 'none') return;

      $position = $this->parameter['legend'];
      $numSets = 0; // number of data sets with legends.
      $sumTextHeight = 0; // total of height of all legend text items.
      $width = 0;
      $height = 0;

      foreach ($this->y_order as $set) {
       $text = isset($this->y_format[$set]['legend']) ? $this->y_format[$set]['legend'] : 'none';
       $size = $this->get_boundaryBox(
         array('points' => $this->parameter['legend_size'],
               'angle'  => 0,
               'font'   => $this->parameter['legend_font'],
               'text'   => $text));

       $this->calculated['legend']['boundary_box'][$set] = $size;
       $this->calculated['legend']['text'][$set]        = $text;
       //$this->calculated['legend']['font'][$set]        = $this->parameter['legend_font'];
       //$this->calculated['legend']['points'][$set]      = $this->parameter['legend_size'];
       //$this->calculated['legend']['angle'][$set]       = 0;

       if ($text && $text!='none') {
         $numSets++;
         $sumTextHeight += $size['height'];
       }

       if ($size['width'] > $this->calculated['legend']['boundary_box_max']['width'])
         $this->calculated['legend']['boundary_box_max'] = $size;
      }

      $offset  = $this->parameter['legend_offset'];  // offset in pixels of legend box from graph border.
      $padding = $this->parameter['legend_padding']; // padding in pixels around legend text.
      $textWidth = $this->calculated['legend']['boundary_box_max']['width']; // width of largest legend item.
      $textHeight = $this->calculated['legend']['boundary_box_max']['height']; // use height as size to use for colour square in legend.
      $width = $padding * 2 + $textWidth + $textHeight * 2;  // left and right padding + maximum text width + space for square
      $height = ($padding + $textHeight) * $numSets + $padding; // top and bottom padding + padding between text + text.

      $this->calculated['legend']['boundary_box_all'] = array('width'     => $width,
                                                            'height'    => $height,
                                                            'offset'    => $offset,
                                                            'reference' => $position);

      switch ($position) { // move in right or bottom if legend is outside data plotting area.
        case 'outside-top' :
          $this->calculated['boundary_box']['right']      -= $offset + $width; // move in right hand side
          break;

        case 'outside-bottom' :
          $this->calculated['boundary_box']['right']      -= $offset + $width; // move in right hand side
          break;

        case 'outside-left' :
          $this->calculated['boundary_box']['bottom']      -= $offset + $height; // move in right hand side
          break;

        case 'outside-right' :
          $this->calculated['boundary_box']['bottom']      -= $offset + $height; // move in right hand side
          break;
      }
    }

    function init_y_axis() {
      $this->calculated['y_axis_left'] = array(); // array to hold calculated values for y_axis on left.
      $this->calculated['y_axis_left']['boundary_box_max'] = $this->get_null_size();
      $this->calculated['y_axis_right'] = array(); // array to hold calculated values for y_axis on right.
      $this->calculated['y_axis_right']['boundary_box_max'] = $this->get_null_size();

      $axis_font       = $this->parameter['axis_font'];
      $axis_size       = $this->parameter['axis_size'];
      $axis_colour     = $this->parameter['axis_colour'];
      $axis_angle      = $this->parameter['y_axis_angle'];
      $y_tick_labels   = $this->y_tick_labels;

      $this->calculated['y_axis_left']['has_data'] = FALSE;
      $this->calculated['y_axis_right']['has_data'] = FALSE;

      // find min and max y values.
      $minLeft = $this->parameter['y_min_left'];
      $maxLeft = $this->parameter['y_max_left'];
      $minRight = $this->parameter['y_min_right'];
      $maxRight = $this->parameter['y_max_right'];
      $dataLeft = array();
      $dataRight = array();
      foreach ($this->y_order as $order => $set) {
        if (isset($this->y_format[$set]['y_axis']) && $this->y_format[$set]['y_axis'] == 'right') {
          $this->calculated['y_axis_right']['has_data'] = TRUE;
          $dataRight = array_merge($dataRight, $this->y_data[$set]);
        } else {
          $this->calculated['y_axis_left']['has_data'] = TRUE;
          $dataLeft = array_merge($dataLeft, $this->y_data[$set]);
        }
      }
      $dataLeftRange = $this->find_range($dataLeft, $minLeft, $maxLeft, $this->parameter['y_resolution_left']);
      $dataRightRange = $this->find_range($dataRight, $minRight, $maxRight, $this->parameter['y_resolution_right']);
      $minLeft = $dataLeftRange['min'];
      $maxLeft = $dataLeftRange['max'];
      $minRight = $dataRightRange['min'];
      $maxRight = $dataRightRange['max'];

      $this->calculated['y_axis_left']['min']  = $minLeft;
      $this->calculated['y_axis_left']['max']  = $maxLeft;
      $this->calculated['y_axis_right']['min'] = $minRight;
      $this->calculated['y_axis_right']['max'] = $maxRight;

      $stepLeft = ($maxLeft - $minLeft) / ($this->parameter['y_axis_gridlines'] - 1);
      $startLeft = $minLeft;
      $step_right = ($maxRight - $minRight) / ($this->parameter['y_axis_gridlines'] - 1);
      $start_right = $minRight;

      if ($this->parameter['y_axis_text_left']) {
        for ($i = 0; $i < $this->parameter['y_axis_gridlines']; $i++) { // calculate y axis text sizes
          // left y axis
          if ($y_tick_labels) {
            $value = $y_tick_labels[$i];
          } else {
            $value = number_format($startLeft, $this->parameter['y_decimal_left'], $this->parameter['decimal_point'], $this->parameter['thousand_sep']);
          }
          $this->calculated['y_axis_left']['data'][$i]  = $startLeft;
          $this->calculated['y_axis_left']['text'][$i]  = $value; // text is formatted raw data

          $size = $this->get_boundaryBox(
            array('points' => $axis_size,
                  'font'   => $axis_font,
                  'angle'  => $axis_angle,
                  'colour' => $axis_colour,
                  'text'   => $value));
          $this->calculated['y_axis_left']['boundary_box'][$i] = $size;

          if ($size['height'] > $this->calculated['y_axis_left']['boundary_box_max']['height'])
            $this->calculated['y_axis_left']['boundary_box_max']['height'] = $size['height'];
          if ($size['width'] > $this->calculated['y_axis_left']['boundary_box_max']['width'])
            $this->calculated['y_axis_left']['boundary_box_max']['width'] = $size['width'];

          $startLeft += $stepLeft;
        }
        $this->calculated['boundary_box']['left'] += $this->calculated['y_axis_left']['boundary_box_max']['width']
                                                    + $this->parameter['y_inner_padding'];
      }

      if ($this->parameter['y_axis_text_right']) {
        for ($i = 0; $i < $this->parameter['y_axis_gridlines']; $i++) { // calculate y axis text sizes
          // right y axis
          $value = number_format($start_right, $this->parameter['y_decimal_right'], $this->parameter['decimal_point'], $this->parameter['thousand_sep']);
          $this->calculated['y_axis_right']['data'][$i]  = $start_right;
          $this->calculated['y_axis_right']['text'][$i]  = $value; // text is formatted raw data
          $size = $this->get_boundaryBox(
            array('points' => $axis_size,
                  'font'   => $axis_font,
                  'angle'  => $axis_angle,
                  'colour' => $axis_colour,
                  'text'   => $value));
          $this->calculated['y_axis_right']['boundary_box'][$i] = $size;

          if ($size['height'] > $this->calculated['y_axis_right']['boundary_box_max']['height'])
            $this->calculated['y_axis_right']['boundary_box_max'] = $size;
          if ($size['width'] > $this->calculated['y_axis_right']['boundary_box_max']['width'])
            $this->calculated['y_axis_right']['boundary_box_max']['width'] = $size['width'];

          $start_right += $step_right;
        }
        $this->calculated['boundary_box']['right'] -= $this->calculated['y_axis_right']['boundary_box_max']['width']
                                                    + $this->parameter['y_inner_padding'];
      }
    }

    function init_x_axis() {
      $this->calculated['x_axis'] = array(); // array to hold calculated values for x_axis.
      $this->calculated['x_axis']['boundary_box_max'] = array('height' => 0, 'width' => 0);

      $axis_font       = $this->parameter['axis_font'];
      $axis_size       = $this->parameter['axis_size'];
      $axis_colour     = $this->parameter['axis_colour'];
      $axis_angle      = $this->parameter['x_axis_angle'];

      // check whether to treat x axis as numeric
      if ($this->parameter['x_axis_gridlines'] == 'auto') { // auto means text based x_axis, not numeric...
        $this->calculated['x_axis']['num_ticks'] = sizeof($this->x_data);
          $data = $this->x_data;
          for ($i=0; $i < $this->calculated['x_axis']['num_ticks']; $i++) {
            $value = array_shift($data); // grab value from begin of array
            $this->calculated['x_axis']['data'][$i]  = $value;
            $this->calculated['x_axis']['text'][$i]  = $value; // raw data and text are both the same in this case
            $size = $this->get_boundaryBox(
              array('points' => $axis_size,
                    'font'   => $axis_font,
                    'angle'  => $axis_angle,
                    'colour' => $axis_colour,
                    'text'   => $value));
            $this->calculated['x_axis']['boundary_box'][$i] = $size;
            if ($size['height'] > $this->calculated['x_axis']['boundary_box_max']['height'])
              $this->calculated['x_axis']['boundary_box_max'] = $size;
          }

      } else { // x axis is numeric so find max min values...
        $this->calculated['x_axis']['num_ticks'] = $this->parameter['x_axis_gridlines'];

        $min = $this->parameter['x_min'];
        $max = $this->parameter['x_max'];
        $data = array();
        $data = $this->find_range($this->x_data, $min, $max, $this->parameter['x_resolution']);
        $min = $data['min'];
        $max = $data['max'];
        $this->calculated['x_axis']['min'] = $min;
        $this->calculated['x_axis']['max'] = $max;

        $step = ($max - $min) / ($this->calculated['x_axis']['num_ticks'] - 1);
        $start = $min;

        for ($i = 0; $i < $this->calculated['x_axis']['num_ticks']; $i++) { // calculate x axis text sizes
          $value = number_format($start, $this->parameter['xDecimal'], $this->parameter['decimal_point'], $this->parameter['thousand_sep']);
          $this->calculated['x_axis']['data'][$i]  = $start;
          $this->calculated['x_axis']['text'][$i]  = $value; // text is formatted raw data

          $size = $this->get_boundaryBox(
            array('points' => $axis_size,
                  'font'   => $axis_font,
                  'angle'  => $axis_angle,
                  'colour' => $axis_colour,
                  'text'   => $value));
          $this->calculated['x_axis']['boundary_box'][$i] = $size;

          if ($size['height'] > $this->calculated['x_axis']['boundary_box_max']['height'])
            $this->calculated['x_axis']['boundary_box_max'] = $size;

          $start += $step;
        }
      }
      if ($this->parameter['x_axis_text'])
        $this->calculated['boundary_box']['bottom'] -= $this->calculated['x_axis']['boundary_box_max']['height']
                                                      + $this->parameter['x_inner_padding'];
    }

    // find max and min values for a data array given the resolution.
    function find_range($data, $min, $max, $resolution) {
      if (sizeof($data) == 0 ) return array('min' => 0, 'max' => 0);
      foreach ($data as $key => $value) {
        if ($value=='none') continue;
        if ($value > $max) $max = $value;
        if ($value < $min) $min = $value;
      }

      if ($max == 0) {
        $factor = 1;
      } else {
        if ($max < 0) $factor = - pow(10, (floor(log10(abs($max))) + $resolution) );
        else $factor = pow(10, (floor(log10(abs($max))) - $resolution) );
      }
      if ($factor > 0.1) { // To avoid some wierd rounding errors (Moodle)
        $factor = round($factor * 1000.0) / 1000.0; // To avoid some wierd rounding errors (Moodle)
      } // To avoid some wierd rounding errors (Moodle)

      $max = $factor * @ceil($max / $factor);
      $min = $factor * @floor($min / $factor);

      //print "max=$max, min=$min<br />";

      return array('min' => $min, 'max' => $max);
    }

    function graph() {
      if (func_num_args() == 2) {
        $this->parameter['width']  = func_get_arg(0);
        $this->parameter['height'] = func_get_arg(1);
      }
      //$this->boundaryBox  = array(
      $this->calculated['boundary_box'] = array(
        'left'      =>  0,
        'top'       =>  0,
        'right'     =>  $this->parameter['width'] - 1,
        'bottom'    =>  $this->parameter['height'] - 1);

      $this->init_colours();

      //ImageColorTransparent($this->image, $this->colour['white']); // colour for transparency
    }

    function print_TTF($message) {
      $points    = $message['points'];
      $angle     = $message['angle'];
      $text      = $message['text'];
      $colour    = $this->colour[$message['colour']];
      $font      = $this->parameter['path_to_fonts'].$message['font'];

      $x         = $message['boundary_box']['x'];
      $y         = $message['boundary_box']['y'];
      $offsetX   = $message['boundary_box']['offsetX'];
      $offsetY   = $message['boundary_box']['offsetY'];
      $height    = $message['boundary_box']['height'];
      $width     = $message['boundary_box']['width'];
      $reference = $message['boundary_box']['reference'];

      switch ($reference) {
        case 'top-left':
        case 'left-top':
          $y += $height - $offsetY;
          //$y += $offsetY;
          $x += $offsetX;
          break;
        case 'left-center':
          $y += ($height / 2) - $offsetY;
          $x += $offsetX;
          break;
        case 'left-bottom':
          $y -= $offsetY;
          $x += $offsetX;
         break;
        case 'top-center':
          $y += $height - $offsetY;
          $x -= ($width / 2) - $offsetX;
         break;
        case 'top-right':
        case 'right-top':
          $y += $height - $offsetY;
          $x -= $width  - $offsetX;
          break;
        case 'right-center':
          $y += ($height / 2) - $offsetY;
          $x -= $width  - $offsetX;
          break;
        case 'right-bottom':
          $y -= $offsetY;
          $x -= $width  - $offsetX;
          break;
        case 'bottom-center':
          $y -= $offsetY;
          $x -= ($width / 2) - $offsetX;
         break;
        default:
          $y = 0;
          $x = 0;
          break;
      }
      // start of Moodle addition
      $text = core_text::utf8_to_entities($text, true, true); //does not work with hex entities!
      // end of Moodle addition
      ImageTTFText($this->image, $points, $angle, $x, $y, $colour, $font, $text);
    }

    // move boundaryBox to coordinates specified
    function update_boundaryBox(&$boundaryBox, $coords) {
      $width      = $boundaryBox['width'];
      $height     = $boundaryBox['height'];
      $x          = $coords['x'];
      $y          = $coords['y'];
      $reference  = $coords['reference'];
      switch ($reference) {
        case 'top-left':
        case 'left-top':
          $top    = $y;
          $bottom = $y + $height;
          $left   = $x;
          $right  = $x + $width;
          break;
        case 'left-center':
          $top    = $y - ($height / 2);
          $bottom = $y + ($height / 2);
          $left   = $x;
          $right  = $x + $width;
          break;
        case 'left-bottom':
          $top    = $y - $height;
          $bottom = $y;
          $left   = $x;
          $right  = $x + $width;
          break;
        case 'top-center':
          $top    = $y;
          $bottom = $y + $height;
          $left   = $x - ($width / 2);
          $right  = $x + ($width / 2);
          break;
        case 'right-top':
        case 'top-right':
          $top    = $y;
          $bottom = $y + $height;
          $left   = $x - $width;
          $right  = $x;
          break;
        case 'right-center':
          $top    = $y - ($height / 2);
          $bottom = $y + ($height / 2);
          $left   = $x - $width;
          $right  = $x;
          break;
        case 'bottom=right':
        case 'right-bottom':
          $top    = $y - $height;
          $bottom = $y;
          $left   = $x - $width;
          $right  = $x;
          break;
        default:
          $top    = 0;
          $bottom = $height;
          $left   = 0;
          $right  = $width;
          break;
      }

      $boundaryBox = array_merge($boundaryBox, array('top'       => $top,
                                                     'bottom'    => $bottom,
                                                     'left'      => $left,
                                                     'right'     => $right,
                                                     'x'         => $x,
                                                     'y'         => $y,
                                                     'reference' => $reference));
    }

    function get_null_size() {
      return array('width'      => 0,
                   'height'     => 0,
                   'offsetX'    => 0,
                   'offsetY'    => 0,
                   //'fontHeight' => 0
                   );
    }

    function get_boundaryBox($message) {
      $points  = $message['points'];
      $angle   = $message['angle'];
      $font    = $this->parameter['path_to_fonts'].$message['font'];
      $text    = $message['text'];

      //print ('get_boundaryBox');
      //expandPre($message);

      // get font size
      $bounds = ImageTTFBBox($points, $angle, $font, "W");
      if ($angle < 0) {
        $fontHeight = abs($bounds[7]-$bounds[1]);
      } else if ($angle > 0) {
        $fontHeight = abs($bounds[1]-$bounds[7]);
      } else {
        $fontHeight = abs($bounds[7]-$bounds[1]);
      }

      // get boundary box and offsets for printing at an angle
      // start of Moodle addition
      $text = core_text::utf8_to_entities($text, true, true); //gd does not work with hex entities!
      // end of Moodle addition
      $bounds = ImageTTFBBox($points, $angle, $font, $text);

      if ($angle < 0) {
        $width = abs($bounds[4]-$bounds[0]);
        $height = abs($bounds[3]-$bounds[7]);
        $offsetY = abs($bounds[3]-$bounds[1]);
        $offsetX = 0;

      } else if ($angle > 0) {
        $width = abs($bounds[2]-$bounds[6]);
        $height = abs($bounds[1]-$bounds[5]);
        $offsetY = 0;
        $offsetX = abs($bounds[0]-$bounds[6]);

      } else {
        $width = abs($bounds[4]-$bounds[6]);
        $height = abs($bounds[7]-$bounds[1]);
        $offsetY = $bounds[1];
        $offsetX = 0;
      }

      //return values
      return array('width'      => $width,
                   'height'     => $height,
                   'offsetX'    => $offsetX,
                   'offsetY'    => $offsetY,
                   //'fontHeight' => $fontHeight
                   );
    }

    function draw_rectangle($border, $colour, $type) {
      $colour = $this->colour[$colour];
      switch ($type) {
        case 'fill':    // fill the rectangle
          ImageFilledRectangle($this->image, $border['left'], $border['top'], $border['right'], $border['bottom'], $colour);
          break;
        case 'box':     // all sides
          ImageRectangle($this->image, $border['left'], $border['top'], $border['right'], $border['bottom'], $colour);
          break;
        case 'axis':    // bottom x axis and left y axis
          ImageLine($this->image, $border['left'], $border['top'], $border['left'], $border['bottom'], $colour);
          ImageLine($this->image, $border['left'], $border['bottom'], $border['right'], $border['bottom'], $colour);
          break;
        case 'y':       // left y axis only
        case 'y-left':
          ImageLine($this->image, $border['left'], $border['top'], $border['left'], $border['bottom'], $colour);
          break;
        case 'y-right': // right y axis only
          ImageLine($this->image, $border['right'], $border['top'], $border['right'], $border['bottom'], $colour);
          break;
        case 'x':       // bottom x axis only
          ImageLine($this->image, $border['left'], $border['bottom'], $border['right'], $border['bottom'], $colour);
          break;
        case 'u':       // u shaped. bottom x axis and both left and right y axis.
          ImageLine($this->image, $border['left'], $border['top'], $border['left'], $border['bottom'], $colour);
          ImageLine($this->image, $border['right'], $border['top'], $border['right'], $border['bottom'], $colour);
          ImageLine($this->image, $border['left'], $border['bottom'], $border['right'], $border['bottom'], $colour);
          break;

      }
    }

    function init_colours() {
      $this->image              = ImageCreate($this->parameter['width'], $this->parameter['height']);
      // standard colours
      $this->colour['white']    = ImageColorAllocate ($this->image, 0xFF, 0xFF, 0xFF); // first colour is background colour.
      $this->colour['black']    = ImageColorAllocate ($this->image, 0x00, 0x00, 0x00);
      $this->colour['maroon']   = ImageColorAllocate ($this->image, 0x80, 0x00, 0x00);
      $this->colour['green']    = ImageColorAllocate ($this->image, 0x00, 0x80, 0x00);
      $this->colour['ltgreen']  = ImageColorAllocate ($this->image, 0x52, 0xF1, 0x7F);
      $this->colour['ltltgreen']= ImageColorAllocate ($this->image, 0x99, 0xFF, 0x99);
      $this->colour['olive']    = ImageColorAllocate ($this->image, 0x80, 0x80, 0x00);
      $this->colour['navy']     = ImageColorAllocate ($this->image, 0x00, 0x00, 0x80);
      $this->colour['purple']   = ImageColorAllocate ($this->image, 0x80, 0x00, 0x80);
      $this->colour['gray']     = ImageColorAllocate ($this->image, 0x80, 0x80, 0x80);
      $this->colour['red']      = ImageColorAllocate ($this->image, 0xFF, 0x00, 0x00);
      $this->colour['ltred']    = ImageColorAllocate ($this->image, 0xFF, 0x99, 0x99);
      $this->colour['ltltred']  = ImageColorAllocate ($this->image, 0xFF, 0xCC, 0xCC);
      $this->colour['orange']   = ImageColorAllocate ($this->image, 0xFF, 0x66, 0x00);
      $this->colour['ltorange']   = ImageColorAllocate ($this->image, 0xFF, 0x99, 0x66);
      $this->colour['ltltorange'] = ImageColorAllocate ($this->image, 0xFF, 0xcc, 0x99);
      $this->colour['lime']     = ImageColorAllocate ($this->image, 0x00, 0xFF, 0x00);
      $this->colour['yellow']   = ImageColorAllocate ($this->image, 0xFF, 0xFF, 0x00);
      $this->colour['blue']     = ImageColorAllocate ($this->image, 0x00, 0x00, 0xFF);
      $this->colour['ltblue']   = ImageColorAllocate ($this->image, 0x00, 0xCC, 0xFF);
      $this->colour['ltltblue'] = ImageColorAllocate ($this->image, 0x99, 0xFF, 0xFF);
      $this->colour['fuchsia']  = ImageColorAllocate ($this->image, 0xFF, 0x00, 0xFF);
      $this->colour['aqua']     = ImageColorAllocate ($this->image, 0x00, 0xFF, 0xFF);
      //$this->colour['white']    = ImageColorAllocate ($this->image, 0xFF, 0xFF, 0xFF);
      // shades of gray
      $this->colour['grayF0']   = ImageColorAllocate ($this->image, 0xF0, 0xF0, 0xF0);
      $this->colour['grayEE']   = ImageColorAllocate ($this->image, 0xEE, 0xEE, 0xEE);
      $this->colour['grayDD']   = ImageColorAllocate ($this->image, 0xDD, 0xDD, 0xDD);
      $this->colour['grayCC']   = ImageColorAllocate ($this->image, 0xCC, 0xCC, 0xCC);
      $this->colour['gray33']   = ImageColorAllocate ($this->image, 0x33, 0x33, 0x33);
      $this->colour['gray66']   = ImageColorAllocate ($this->image, 0x66, 0x66, 0x66);
      $this->colour['gray99']   = ImageColorAllocate ($this->image, 0x99, 0x99, 0x99);

      $this->colour['none']   = 'none';
      return true;
    }

    function output() {
      if ($this->debug) { // for debugging purposes.
        //expandPre($this->graph);
        //expandPre($this->y_data);
        //expandPre($this->x_data);
        //expandPre($this->parameter);
      } else {

        $expiresSeconds = $this->parameter['seconds_to_live'];
        $expiresHours = $this->parameter['hours_to_live'];

        if ($expiresHours || $expiresSeconds) {
          $now = mktime (date("H"),date("i"),date("s"),date("m"),date("d"),date("Y"));
          $expires = mktime (date("H")+$expiresHours,date("i"),date("s")+$expiresSeconds,date("m"),date("d"),date("Y"));
          $expiresGMT = gmdate('D, d M Y H:i:s', $expires).' GMT';
          $lastModifiedGMT  = gmdate('D, d M Y H:i:s', $now).' GMT';

          Header('Last-modified: '.$lastModifiedGMT);
          Header('Expires: '.$expiresGMT);
        }

        if ($this->parameter['file_name'] == 'none') {
          switch ($this->parameter['output_format']) {
            case 'GIF':
              Header("Content-type: image/gif");  // GIF??. switch to PNG guys!!
              ImageGIF($this->image);
              break;
            case 'JPEG':
              Header("Content-type: image/jpeg"); // JPEG for line art??. included for completeness.
              ImageJPEG($this->image);
              break;
           default:
              Header("Content-type: image/png");  // preferred output format
              ImagePNG($this->image);
              break;
          }
        } else {
           switch ($this->parameter['output_format']) {
            case 'GIF':
              ImageGIF($this->image, $this->parameter['file_name'].'.gif');
              break;
            case 'JPEG':
              ImageJPEG($this->image, $this->parameter['file_name'].'.jpg');
              break;
           default:
              ImagePNG($this->image, $this->parameter['file_name'].'.png');
              break;
          }
        }

        ImageDestroy($this->image);
      }
    } // function output

    function init_variable(&$variable, $value, $default) {
      if (!empty($value)) $variable = $value;
      else if (isset($default)) $variable = $default;
      else unset($variable);
    }

    // plot a point. options include square, circle, diamond, triangle, and dot. offset is used for drawing shadows.
    // for diamonds and triangles the size should be an even number to get nice look. if odd the points are crooked.
    function plot($x, $y, $type, $size, $colour, $offset) {
      //print("drawing point of type: $type, at offset: $offset");
      $u = $x + $offset;
      $v = $this->calculated['inner_border']['bottom'] - $y + $offset;
      $half = $size / 2;

      switch ($type) {
        case 'square':
          ImageFilledRectangle($this->image, $u-$half, $v-$half, $u+$half, $v+$half, $this->colour[$colour]);
          break;
        case 'square-open':
          ImageRectangle($this->image, $u-$half, $v-$half, $u+$half, $v+$half, $this->colour[$colour]);
          break;
        case 'circle':
          ImageArc($this->image, $u, $v, $size, $size, 0, 360, $this->colour[$colour]);
          ImageFillToBorder($this->image, $u, $v, $this->colour[$colour], $this->colour[$colour]);
          break;
        case 'circle-open':
          ImageArc($this->image, $u, $v, $size, $size, 0, 360, $this->colour[$colour]);
          break;
        case 'diamond':
          ImageFilledPolygon($this->image, array($u, $v-$half, $u+$half, $v, $u, $v+$half, $u-$half, $v), 4, $this->colour[$colour]);
          break;
        case 'diamond-open':
          ImagePolygon($this->image, array($u, $v-$half, $u+$half, $v, $u, $v+$half, $u-$half, $v), 4, $this->colour[$colour]);
          break;
        case 'triangle':
          ImageFilledPolygon($this->image, array($u, $v-$half, $u+$half, $v+$half, $u-$half, $v+$half), 3, $this->colour[$colour]);
          break;
        case 'triangle-open':
          ImagePolygon($this->image, array($u, $v-$half, $u+$half, $v+$half, $u-$half, $v+$half), 3, $this->colour[$colour]);
          break;
        case 'dot':
          ImageSetPixel($this->image, $u, $v, $this->colour[$colour]);
          break;
      }
    }

    function bar($x, $y, $type, $size, $colour, $offset, $index, $yoffset) {
      $index_offset = $this->calculated['bar_offset_index'][$index];
      if ( $yoffset ) {
        $bar_offsetx = 0;
      } else {
        $bar_offsetx = $this->calculated['bar_offset_x'][$index_offset];
      }
      //$this->dbug("drawing bar at offset = $offset : index = $index: bar_offsetx = $bar_offsetx");

      $span = ($this->calculated['bar_width'] * $size) / 2;
      $x_left  = $x + $bar_offsetx - $span;
      $x_right = $x + $bar_offsetx + $span;

      if ($this->parameter['zero_axis'] != 'none') {
        $zero = $this->calculated['zero_axis'];
        if ($this->parameter['shadow_below_axis'] ) $zero  += $offset;
        $u_left  = $x_left + $offset;
        $u_right = $x_right + $offset - 1;
        $v       = $this->calculated['boundary_box']['bottom'] - $y + $offset;

        if ($v > $zero) {
          $top = $zero +1;
          $bottom = $v;
        } else {
          $top = $v;
          $bottom = $zero - 1;
        }

        switch ($type) {
          case 'open':
            //ImageRectangle($this->image, round($u_left), $top, round($u_right), $bottom, $this->colour[$colour]);
            if ($v > $zero)
              ImageRectangle($this->image, round($u_left), $bottom, round($u_right), $bottom, $this->colour[$colour]);
            else
              ImageRectangle($this->image, round($u_left), $top, round($u_right), $top, $this->colour[$colour]);
            ImageRectangle($this->image, round($u_left), $top, round($u_left), $bottom, $this->colour[$colour]);
            ImageRectangle($this->image, round($u_right), $top, round($u_right), $bottom, $this->colour[$colour]);
            break;
          case 'fill':
            ImageFilledRectangle($this->image, round($u_left), $top, round($u_right), $bottom, $this->colour[$colour]);
            break;
        }

      } else {

        $bottom = $this->calculated['boundary_box']['bottom'];
        if ($this->parameter['shadow_below_axis'] ) $bottom  += $offset;
        if ($this->parameter['inner_border'] != 'none') $bottom -= 1; // 1 pixel above bottom if border is to be drawn.
        $u_left  = $x_left + $offset;
        $u_right = $x_right + $offset - 1;
        $v       = $this->calculated['boundary_box']['bottom'] - $y + $offset;

        // Moodle addition, plus the function parameter yoffset
        if ($yoffset) {                                           // Moodle
            $yoffset = $yoffset - round(($bottom - $v) / 2.0);    // Moodle
            $bottom -= $yoffset;                                  // Moodle
            $v      -= $yoffset;                                  // Moodle
        }                                                         // Moodle

        switch ($type) {
          case 'open':
            ImageRectangle($this->image, round($u_left), $v, round($u_right), $bottom, $this->colour[$colour]);
            break;
          case 'fill':
            ImageFilledRectangle($this->image, round($u_left), $v, round($u_right), $bottom, $this->colour[$colour]);
            break;
        }
      }
    }

    function area($x_start, $y_start, $x_end, $y_end, $type, $colour, $offset) {
      //dbug("drawing area type: $type, at offset: $offset");
      if ($this->parameter['zero_axis'] != 'none') {
        $bottom = $this->calculated['boundary_box']['bottom'];
        $zero   = $this->calculated['zero_axis'];
        if ($this->parameter['shadow_below_axis'] ) $zero  += $offset;
        $u_start = $x_start + $offset;
        $u_end   = $x_end + $offset;
        $v_start = $bottom - $y_start + $offset;
        $v_end   = $bottom - $y_end + $offset;
        switch ($type) {
          case 'fill':
            // draw it this way 'cos the FilledPolygon routine seems a bit buggy.
            ImageFilledPolygon($this->image, array($u_start, $v_start, $u_end, $v_end, $u_end, $zero, $u_start, $zero), 4, $this->colour[$colour]);
            ImagePolygon($this->image, array($u_start, $v_start, $u_end, $v_end, $u_end, $zero, $u_start, $zero), 4, $this->colour[$colour]);
           break;
          case 'open':
            //ImagePolygon($this->image, array($u_start, $v_start, $u_end, $v_end, $u_end, $zero, $u_start, $zero), 4, $this->colour[$colour]);
            ImageLine($this->image, $u_start, $v_start, $u_end, $v_end, $this->colour[$colour]);
            ImageLine($this->image, $u_start, $v_start, $u_start, $zero, $this->colour[$colour]);
            ImageLine($this->image, $u_end, $v_end, $u_end, $zero, $this->colour[$colour]);
           break;
        }
      } else {
        $bottom = $this->calculated['boundary_box']['bottom'];
        $u_start = $x_start + $offset;
        $u_end   = $x_end + $offset;
        $v_start = $bottom - $y_start + $offset;
        $v_end   = $bottom - $y_end + $offset;

        if ($this->parameter['shadow_below_axis'] ) $bottom  += $offset;
        if ($this->parameter['inner_border'] != 'none') $bottom -= 1; // 1 pixel above bottom if border is to be drawn.
        switch ($type) {
          case 'fill':
            ImageFilledPolygon($this->image, array($u_start, $v_start, $u_end, $v_end, $u_end, $bottom, $u_start, $bottom), 4, $this->colour[$colour]);
           break;
          case 'open':
            ImagePolygon($this->image, array($u_start, $v_start, $u_end, $v_end, $u_end, $bottom, $u_start, $bottom), 4, $this->colour[$colour]);
           break;
        }
      }
    }

    function line($x_start, $y_start, $x_end, $y_end, $type, $brush_type, $brush_size, $colour, $offset) {
      //dbug("drawing line of type: $type, at offset: $offset");
      $u_start = $x_start + $offset;
      $v_start = $this->calculated['boundary_box']['bottom'] - $y_start + $offset;
      $u_end   = $x_end + $offset;
      $v_end   = $this->calculated['boundary_box']['bottom'] - $y_end + $offset;

      switch ($type) {
        case 'brush':
          $this->draw_brush_line($u_start, $v_start, $u_end, $v_end, $brush_size, $brush_type, $colour);
         break;
        case 'line' :
          ImageLine($this->image, $u_start, $v_start, $u_end, $v_end, $this->colour[$colour]);
          break;
        case 'dash':
          ImageDashedLine($this->image, $u_start, $v_start, $u_end, $v_end, $this->colour[$colour]);
          break;
      }
    }

    // function to draw line. would prefer to use gdBrush but this is not supported yet.
    function draw_brush_line($x0, $y0, $x1, $y1, $size, $type, $colour) {
      //$this->dbug("line: $x0, $y0, $x1, $y1");
      $dy = $y1 - $y0;
      $dx = $x1 - $x0;
      $t = 0;
      $watchdog = 1024; // precaution to prevent infinite loops.

      $this->draw_brush($x0, $y0, $size, $type, $colour);
      if (abs($dx) > abs($dy)) { // slope < 1
        //$this->dbug("slope < 1");
        $m = $dy / $dx; // compute slope
        $t += $y0;
        $dx = ($dx < 0) ? -1 : 1;
        $m *= $dx;
        while (round($x0) != round($x1)) {
          if (!$watchdog--) break;
          $x0 += $dx; // step to next x value
          $t += $m;   // add slope to y value
          $y = round($t);
          //$this->dbug("x0=$x0, x1=$x1, y=$y watchdog=$watchdog");
          $this->draw_brush($x0, $y, $size, $type, $colour);

        }
      } else { // slope >= 1
        //$this->dbug("slope >= 1");
        $m = $dx / $dy; // compute slope
        $t += $x0;
        $dy = ($dy < 0) ? -1 : 1;
        $m *= $dy;
        while (round($y0) != round($y1)) {
          if (!$watchdog--) break;
          $y0 += $dy; // step to next y value
          $t += $m;   // add slope to x value
          $x = round($t);
          //$this->dbug("x=$x, y0=$y0, y1=$y1 watchdog=$watchdog");
          $this->draw_brush($x, $y0, $size, $type, $colour);

        }
      }
    }

    function draw_brush($x, $y, $size, $type, $colour) {
      $x = round($x);
      $y = round($y);
      $half = round($size / 2);
      switch ($type) {
        case 'circle':
          ImageArc($this->image, $x, $y, $size, $size, 0, 360, $this->colour[$colour]);
          ImageFillToBorder($this->image, $x, $y, $this->colour[$colour], $this->colour[$colour]);
          break;
        case 'square':
          ImageFilledRectangle($this->image, $x-$half, $y-$half, $x+$half, $y+$half, $this->colour[$colour]);
          break;
        case 'vertical':
          ImageFilledRectangle($this->image, $x, $y-$half, $x+1, $y+$half, $this->colour[$colour]);
          break;
        case 'horizontal':
          ImageFilledRectangle($this->image, $x-$half, $y, $x+$half, $y+1, $this->colour[$colour]);
          break;
        case 'slash':
          ImageFilledPolygon($this->image, array($x+$half, $y-$half,
                                                 $x+$half+1, $y-$half,
                                                 $x-$half+1, $y+$half,
                                                 $x-$half, $y+$half
                                                 ), 4, $this->colour[$colour]);
          break;
        case 'backslash':
          ImageFilledPolygon($this->image, array($x-$half, $y-$half,
                                                 $x-$half+1, $y-$half,
                                                 $x+$half+1, $y+$half,
                                                 $x+$half, $y+$half
                                                 ), 4, $this->colour[$colour]);
          break;
        default:
          @eval($type); // user can create own brush script.
      }
    }

} // class graph
