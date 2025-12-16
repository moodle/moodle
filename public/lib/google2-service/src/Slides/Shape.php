<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Slides;

class Shape extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const SHAPE_TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Text box shape.
   */
  public const SHAPE_TYPE_TEXT_BOX = 'TEXT_BOX';
  /**
   * Rectangle shape. Corresponds to ECMA-376 ST_ShapeType 'rect'
   */
  public const SHAPE_TYPE_RECTANGLE = 'RECTANGLE';
  /**
   * Round corner rectangle shape. Corresponds to ECMA-376 ST_ShapeType
   * 'roundRect'
   */
  public const SHAPE_TYPE_ROUND_RECTANGLE = 'ROUND_RECTANGLE';
  /**
   * Ellipse shape. Corresponds to ECMA-376 ST_ShapeType 'ellipse'
   */
  public const SHAPE_TYPE_ELLIPSE = 'ELLIPSE';
  /**
   * Curved arc shape. Corresponds to ECMA-376 ST_ShapeType 'arc'
   */
  public const SHAPE_TYPE_ARC = 'ARC';
  /**
   * Bent arrow shape. Corresponds to ECMA-376 ST_ShapeType 'bentArrow'
   */
  public const SHAPE_TYPE_BENT_ARROW = 'BENT_ARROW';
  /**
   * Bent up arrow shape. Corresponds to ECMA-376 ST_ShapeType 'bentUpArrow'
   */
  public const SHAPE_TYPE_BENT_UP_ARROW = 'BENT_UP_ARROW';
  /**
   * Bevel shape. Corresponds to ECMA-376 ST_ShapeType 'bevel'
   */
  public const SHAPE_TYPE_BEVEL = 'BEVEL';
  /**
   * Block arc shape. Corresponds to ECMA-376 ST_ShapeType 'blockArc'
   */
  public const SHAPE_TYPE_BLOCK_ARC = 'BLOCK_ARC';
  /**
   * Brace pair shape. Corresponds to ECMA-376 ST_ShapeType 'bracePair'
   */
  public const SHAPE_TYPE_BRACE_PAIR = 'BRACE_PAIR';
  /**
   * Bracket pair shape. Corresponds to ECMA-376 ST_ShapeType 'bracketPair'
   */
  public const SHAPE_TYPE_BRACKET_PAIR = 'BRACKET_PAIR';
  /**
   * Can shape. Corresponds to ECMA-376 ST_ShapeType 'can'
   */
  public const SHAPE_TYPE_CAN = 'CAN';
  /**
   * Chevron shape. Corresponds to ECMA-376 ST_ShapeType 'chevron'
   */
  public const SHAPE_TYPE_CHEVRON = 'CHEVRON';
  /**
   * Chord shape. Corresponds to ECMA-376 ST_ShapeType 'chord'
   */
  public const SHAPE_TYPE_CHORD = 'CHORD';
  /**
   * Cloud shape. Corresponds to ECMA-376 ST_ShapeType 'cloud'
   */
  public const SHAPE_TYPE_CLOUD = 'CLOUD';
  /**
   * Corner shape. Corresponds to ECMA-376 ST_ShapeType 'corner'
   */
  public const SHAPE_TYPE_CORNER = 'CORNER';
  /**
   * Cube shape. Corresponds to ECMA-376 ST_ShapeType 'cube'
   */
  public const SHAPE_TYPE_CUBE = 'CUBE';
  /**
   * Curved down arrow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'curvedDownArrow'
   */
  public const SHAPE_TYPE_CURVED_DOWN_ARROW = 'CURVED_DOWN_ARROW';
  /**
   * Curved left arrow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'curvedLeftArrow'
   */
  public const SHAPE_TYPE_CURVED_LEFT_ARROW = 'CURVED_LEFT_ARROW';
  /**
   * Curved right arrow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'curvedRightArrow'
   */
  public const SHAPE_TYPE_CURVED_RIGHT_ARROW = 'CURVED_RIGHT_ARROW';
  /**
   * Curved up arrow shape. Corresponds to ECMA-376 ST_ShapeType 'curvedUpArrow'
   */
  public const SHAPE_TYPE_CURVED_UP_ARROW = 'CURVED_UP_ARROW';
  /**
   * Decagon shape. Corresponds to ECMA-376 ST_ShapeType 'decagon'
   */
  public const SHAPE_TYPE_DECAGON = 'DECAGON';
  /**
   * Diagonal stripe shape. Corresponds to ECMA-376 ST_ShapeType 'diagStripe'
   */
  public const SHAPE_TYPE_DIAGONAL_STRIPE = 'DIAGONAL_STRIPE';
  /**
   * Diamond shape. Corresponds to ECMA-376 ST_ShapeType 'diamond'
   */
  public const SHAPE_TYPE_DIAMOND = 'DIAMOND';
  /**
   * Dodecagon shape. Corresponds to ECMA-376 ST_ShapeType 'dodecagon'
   */
  public const SHAPE_TYPE_DODECAGON = 'DODECAGON';
  /**
   * Donut shape. Corresponds to ECMA-376 ST_ShapeType 'donut'
   */
  public const SHAPE_TYPE_DONUT = 'DONUT';
  /**
   * Double wave shape. Corresponds to ECMA-376 ST_ShapeType 'doubleWave'
   */
  public const SHAPE_TYPE_DOUBLE_WAVE = 'DOUBLE_WAVE';
  /**
   * Down arrow shape. Corresponds to ECMA-376 ST_ShapeType 'downArrow'
   */
  public const SHAPE_TYPE_DOWN_ARROW = 'DOWN_ARROW';
  /**
   * Callout down arrow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'downArrowCallout'
   */
  public const SHAPE_TYPE_DOWN_ARROW_CALLOUT = 'DOWN_ARROW_CALLOUT';
  /**
   * Folded corner shape. Corresponds to ECMA-376 ST_ShapeType 'foldedCorner'
   */
  public const SHAPE_TYPE_FOLDED_CORNER = 'FOLDED_CORNER';
  /**
   * Frame shape. Corresponds to ECMA-376 ST_ShapeType 'frame'
   */
  public const SHAPE_TYPE_FRAME = 'FRAME';
  /**
   * Half frame shape. Corresponds to ECMA-376 ST_ShapeType 'halfFrame'
   */
  public const SHAPE_TYPE_HALF_FRAME = 'HALF_FRAME';
  /**
   * Heart shape. Corresponds to ECMA-376 ST_ShapeType 'heart'
   */
  public const SHAPE_TYPE_HEART = 'HEART';
  /**
   * Heptagon shape. Corresponds to ECMA-376 ST_ShapeType 'heptagon'
   */
  public const SHAPE_TYPE_HEPTAGON = 'HEPTAGON';
  /**
   * Hexagon shape. Corresponds to ECMA-376 ST_ShapeType 'hexagon'
   */
  public const SHAPE_TYPE_HEXAGON = 'HEXAGON';
  /**
   * Home plate shape. Corresponds to ECMA-376 ST_ShapeType 'homePlate'
   */
  public const SHAPE_TYPE_HOME_PLATE = 'HOME_PLATE';
  /**
   * Horizontal scroll shape. Corresponds to ECMA-376 ST_ShapeType
   * 'horizontalScroll'
   */
  public const SHAPE_TYPE_HORIZONTAL_SCROLL = 'HORIZONTAL_SCROLL';
  /**
   * Irregular seal 1 shape. Corresponds to ECMA-376 ST_ShapeType
   * 'irregularSeal1'
   */
  public const SHAPE_TYPE_IRREGULAR_SEAL_1 = 'IRREGULAR_SEAL_1';
  /**
   * Irregular seal 2 shape. Corresponds to ECMA-376 ST_ShapeType
   * 'irregularSeal2'
   */
  public const SHAPE_TYPE_IRREGULAR_SEAL_2 = 'IRREGULAR_SEAL_2';
  /**
   * Left arrow shape. Corresponds to ECMA-376 ST_ShapeType 'leftArrow'
   */
  public const SHAPE_TYPE_LEFT_ARROW = 'LEFT_ARROW';
  /**
   * Callout left arrow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'leftArrowCallout'
   */
  public const SHAPE_TYPE_LEFT_ARROW_CALLOUT = 'LEFT_ARROW_CALLOUT';
  /**
   * Left brace shape. Corresponds to ECMA-376 ST_ShapeType 'leftBrace'
   */
  public const SHAPE_TYPE_LEFT_BRACE = 'LEFT_BRACE';
  /**
   * Left bracket shape. Corresponds to ECMA-376 ST_ShapeType 'leftBracket'
   */
  public const SHAPE_TYPE_LEFT_BRACKET = 'LEFT_BRACKET';
  /**
   * Left right arrow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'leftRightArrow'
   */
  public const SHAPE_TYPE_LEFT_RIGHT_ARROW = 'LEFT_RIGHT_ARROW';
  /**
   * Callout left right arrow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'leftRightArrowCallout'
   */
  public const SHAPE_TYPE_LEFT_RIGHT_ARROW_CALLOUT = 'LEFT_RIGHT_ARROW_CALLOUT';
  /**
   * Left right up arrow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'leftRightUpArrow'
   */
  public const SHAPE_TYPE_LEFT_RIGHT_UP_ARROW = 'LEFT_RIGHT_UP_ARROW';
  /**
   * Left up arrow shape. Corresponds to ECMA-376 ST_ShapeType 'leftUpArrow'
   */
  public const SHAPE_TYPE_LEFT_UP_ARROW = 'LEFT_UP_ARROW';
  /**
   * Lightning bolt shape. Corresponds to ECMA-376 ST_ShapeType 'lightningBolt'
   */
  public const SHAPE_TYPE_LIGHTNING_BOLT = 'LIGHTNING_BOLT';
  /**
   * Divide math shape. Corresponds to ECMA-376 ST_ShapeType 'mathDivide'
   */
  public const SHAPE_TYPE_MATH_DIVIDE = 'MATH_DIVIDE';
  /**
   * Equal math shape. Corresponds to ECMA-376 ST_ShapeType 'mathEqual'
   */
  public const SHAPE_TYPE_MATH_EQUAL = 'MATH_EQUAL';
  /**
   * Minus math shape. Corresponds to ECMA-376 ST_ShapeType 'mathMinus'
   */
  public const SHAPE_TYPE_MATH_MINUS = 'MATH_MINUS';
  /**
   * Multiply math shape. Corresponds to ECMA-376 ST_ShapeType 'mathMultiply'
   */
  public const SHAPE_TYPE_MATH_MULTIPLY = 'MATH_MULTIPLY';
  /**
   * Not equal math shape. Corresponds to ECMA-376 ST_ShapeType 'mathNotEqual'
   */
  public const SHAPE_TYPE_MATH_NOT_EQUAL = 'MATH_NOT_EQUAL';
  /**
   * Plus math shape. Corresponds to ECMA-376 ST_ShapeType 'mathPlus'
   */
  public const SHAPE_TYPE_MATH_PLUS = 'MATH_PLUS';
  /**
   * Moon shape. Corresponds to ECMA-376 ST_ShapeType 'moon'
   */
  public const SHAPE_TYPE_MOON = 'MOON';
  /**
   * No smoking shape. Corresponds to ECMA-376 ST_ShapeType 'noSmoking'
   */
  public const SHAPE_TYPE_NO_SMOKING = 'NO_SMOKING';
  /**
   * Notched right arrow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'notchedRightArrow'
   */
  public const SHAPE_TYPE_NOTCHED_RIGHT_ARROW = 'NOTCHED_RIGHT_ARROW';
  /**
   * Octagon shape. Corresponds to ECMA-376 ST_ShapeType 'octagon'
   */
  public const SHAPE_TYPE_OCTAGON = 'OCTAGON';
  /**
   * Parallelogram shape. Corresponds to ECMA-376 ST_ShapeType 'parallelogram'
   */
  public const SHAPE_TYPE_PARALLELOGRAM = 'PARALLELOGRAM';
  /**
   * Pentagon shape. Corresponds to ECMA-376 ST_ShapeType 'pentagon'
   */
  public const SHAPE_TYPE_PENTAGON = 'PENTAGON';
  /**
   * Pie shape. Corresponds to ECMA-376 ST_ShapeType 'pie'
   */
  public const SHAPE_TYPE_PIE = 'PIE';
  /**
   * Plaque shape. Corresponds to ECMA-376 ST_ShapeType 'plaque'
   */
  public const SHAPE_TYPE_PLAQUE = 'PLAQUE';
  /**
   * Plus shape. Corresponds to ECMA-376 ST_ShapeType 'plus'
   */
  public const SHAPE_TYPE_PLUS = 'PLUS';
  /**
   * Quad-arrow shape. Corresponds to ECMA-376 ST_ShapeType 'quadArrow'
   */
  public const SHAPE_TYPE_QUAD_ARROW = 'QUAD_ARROW';
  /**
   * Callout quad-arrow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'quadArrowCallout'
   */
  public const SHAPE_TYPE_QUAD_ARROW_CALLOUT = 'QUAD_ARROW_CALLOUT';
  /**
   * Ribbon shape. Corresponds to ECMA-376 ST_ShapeType 'ribbon'
   */
  public const SHAPE_TYPE_RIBBON = 'RIBBON';
  /**
   * Ribbon 2 shape. Corresponds to ECMA-376 ST_ShapeType 'ribbon2'
   */
  public const SHAPE_TYPE_RIBBON_2 = 'RIBBON_2';
  /**
   * Right arrow shape. Corresponds to ECMA-376 ST_ShapeType 'rightArrow'
   */
  public const SHAPE_TYPE_RIGHT_ARROW = 'RIGHT_ARROW';
  /**
   * Callout right arrow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'rightArrowCallout'
   */
  public const SHAPE_TYPE_RIGHT_ARROW_CALLOUT = 'RIGHT_ARROW_CALLOUT';
  /**
   * Right brace shape. Corresponds to ECMA-376 ST_ShapeType 'rightBrace'
   */
  public const SHAPE_TYPE_RIGHT_BRACE = 'RIGHT_BRACE';
  /**
   * Right bracket shape. Corresponds to ECMA-376 ST_ShapeType 'rightBracket'
   */
  public const SHAPE_TYPE_RIGHT_BRACKET = 'RIGHT_BRACKET';
  /**
   * One round corner rectangle shape. Corresponds to ECMA-376 ST_ShapeType
   * 'round1Rect'
   */
  public const SHAPE_TYPE_ROUND_1_RECTANGLE = 'ROUND_1_RECTANGLE';
  /**
   * Two diagonal round corner rectangle shape. Corresponds to ECMA-376
   * ST_ShapeType 'round2DiagRect'
   */
  public const SHAPE_TYPE_ROUND_2_DIAGONAL_RECTANGLE = 'ROUND_2_DIAGONAL_RECTANGLE';
  /**
   * Two same-side round corner rectangle shape. Corresponds to ECMA-376
   * ST_ShapeType 'round2SameRect'
   */
  public const SHAPE_TYPE_ROUND_2_SAME_RECTANGLE = 'ROUND_2_SAME_RECTANGLE';
  /**
   * Right triangle shape. Corresponds to ECMA-376 ST_ShapeType 'rtTriangle'
   */
  public const SHAPE_TYPE_RIGHT_TRIANGLE = 'RIGHT_TRIANGLE';
  /**
   * Smiley face shape. Corresponds to ECMA-376 ST_ShapeType 'smileyFace'
   */
  public const SHAPE_TYPE_SMILEY_FACE = 'SMILEY_FACE';
  /**
   * One snip corner rectangle shape. Corresponds to ECMA-376 ST_ShapeType
   * 'snip1Rect'
   */
  public const SHAPE_TYPE_SNIP_1_RECTANGLE = 'SNIP_1_RECTANGLE';
  /**
   * Two diagonal snip corner rectangle shape. Corresponds to ECMA-376
   * ST_ShapeType 'snip2DiagRect'
   */
  public const SHAPE_TYPE_SNIP_2_DIAGONAL_RECTANGLE = 'SNIP_2_DIAGONAL_RECTANGLE';
  /**
   * Two same-side snip corner rectangle shape. Corresponds to ECMA-376
   * ST_ShapeType 'snip2SameRect'
   */
  public const SHAPE_TYPE_SNIP_2_SAME_RECTANGLE = 'SNIP_2_SAME_RECTANGLE';
  /**
   * One snip one round corner rectangle shape. Corresponds to ECMA-376
   * ST_ShapeType 'snipRoundRect'
   */
  public const SHAPE_TYPE_SNIP_ROUND_RECTANGLE = 'SNIP_ROUND_RECTANGLE';
  /**
   * Ten pointed star shape. Corresponds to ECMA-376 ST_ShapeType 'star10'
   */
  public const SHAPE_TYPE_STAR_10 = 'STAR_10';
  /**
   * Twelve pointed star shape. Corresponds to ECMA-376 ST_ShapeType 'star12'
   */
  public const SHAPE_TYPE_STAR_12 = 'STAR_12';
  /**
   * Sixteen pointed star shape. Corresponds to ECMA-376 ST_ShapeType 'star16'
   */
  public const SHAPE_TYPE_STAR_16 = 'STAR_16';
  /**
   * Twenty four pointed star shape. Corresponds to ECMA-376 ST_ShapeType
   * 'star24'
   */
  public const SHAPE_TYPE_STAR_24 = 'STAR_24';
  /**
   * Thirty two pointed star shape. Corresponds to ECMA-376 ST_ShapeType
   * 'star32'
   */
  public const SHAPE_TYPE_STAR_32 = 'STAR_32';
  /**
   * Four pointed star shape. Corresponds to ECMA-376 ST_ShapeType 'star4'
   */
  public const SHAPE_TYPE_STAR_4 = 'STAR_4';
  /**
   * Five pointed star shape. Corresponds to ECMA-376 ST_ShapeType 'star5'
   */
  public const SHAPE_TYPE_STAR_5 = 'STAR_5';
  /**
   * Six pointed star shape. Corresponds to ECMA-376 ST_ShapeType 'star6'
   */
  public const SHAPE_TYPE_STAR_6 = 'STAR_6';
  /**
   * Seven pointed star shape. Corresponds to ECMA-376 ST_ShapeType 'star7'
   */
  public const SHAPE_TYPE_STAR_7 = 'STAR_7';
  /**
   * Eight pointed star shape. Corresponds to ECMA-376 ST_ShapeType 'star8'
   */
  public const SHAPE_TYPE_STAR_8 = 'STAR_8';
  /**
   * Striped right arrow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'stripedRightArrow'
   */
  public const SHAPE_TYPE_STRIPED_RIGHT_ARROW = 'STRIPED_RIGHT_ARROW';
  /**
   * Sun shape. Corresponds to ECMA-376 ST_ShapeType 'sun'
   */
  public const SHAPE_TYPE_SUN = 'SUN';
  /**
   * Trapezoid shape. Corresponds to ECMA-376 ST_ShapeType 'trapezoid'
   */
  public const SHAPE_TYPE_TRAPEZOID = 'TRAPEZOID';
  /**
   * Triangle shape. Corresponds to ECMA-376 ST_ShapeType 'triangle'
   */
  public const SHAPE_TYPE_TRIANGLE = 'TRIANGLE';
  /**
   * Up arrow shape. Corresponds to ECMA-376 ST_ShapeType 'upArrow'
   */
  public const SHAPE_TYPE_UP_ARROW = 'UP_ARROW';
  /**
   * Callout up arrow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'upArrowCallout'
   */
  public const SHAPE_TYPE_UP_ARROW_CALLOUT = 'UP_ARROW_CALLOUT';
  /**
   * Up down arrow shape. Corresponds to ECMA-376 ST_ShapeType 'upDownArrow'
   */
  public const SHAPE_TYPE_UP_DOWN_ARROW = 'UP_DOWN_ARROW';
  /**
   * U-turn arrow shape. Corresponds to ECMA-376 ST_ShapeType 'uturnArrow'
   */
  public const SHAPE_TYPE_UTURN_ARROW = 'UTURN_ARROW';
  /**
   * Vertical scroll shape. Corresponds to ECMA-376 ST_ShapeType
   * 'verticalScroll'
   */
  public const SHAPE_TYPE_VERTICAL_SCROLL = 'VERTICAL_SCROLL';
  /**
   * Wave shape. Corresponds to ECMA-376 ST_ShapeType 'wave'
   */
  public const SHAPE_TYPE_WAVE = 'WAVE';
  /**
   * Callout wedge ellipse shape. Corresponds to ECMA-376 ST_ShapeType
   * 'wedgeEllipseCallout'
   */
  public const SHAPE_TYPE_WEDGE_ELLIPSE_CALLOUT = 'WEDGE_ELLIPSE_CALLOUT';
  /**
   * Callout wedge rectangle shape. Corresponds to ECMA-376 ST_ShapeType
   * 'wedgeRectCallout'
   */
  public const SHAPE_TYPE_WEDGE_RECTANGLE_CALLOUT = 'WEDGE_RECTANGLE_CALLOUT';
  /**
   * Callout wedge round rectangle shape. Corresponds to ECMA-376 ST_ShapeType
   * 'wedgeRoundRectCallout'
   */
  public const SHAPE_TYPE_WEDGE_ROUND_RECTANGLE_CALLOUT = 'WEDGE_ROUND_RECTANGLE_CALLOUT';
  /**
   * Alternate process flow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'flowChartAlternateProcess'
   */
  public const SHAPE_TYPE_FLOW_CHART_ALTERNATE_PROCESS = 'FLOW_CHART_ALTERNATE_PROCESS';
  /**
   * Collate flow shape. Corresponds to ECMA-376 ST_ShapeType 'flowChartCollate'
   */
  public const SHAPE_TYPE_FLOW_CHART_COLLATE = 'FLOW_CHART_COLLATE';
  /**
   * Connector flow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'flowChartConnector'
   */
  public const SHAPE_TYPE_FLOW_CHART_CONNECTOR = 'FLOW_CHART_CONNECTOR';
  /**
   * Decision flow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'flowChartDecision'
   */
  public const SHAPE_TYPE_FLOW_CHART_DECISION = 'FLOW_CHART_DECISION';
  /**
   * Delay flow shape. Corresponds to ECMA-376 ST_ShapeType 'flowChartDelay'
   */
  public const SHAPE_TYPE_FLOW_CHART_DELAY = 'FLOW_CHART_DELAY';
  /**
   * Display flow shape. Corresponds to ECMA-376 ST_ShapeType 'flowChartDisplay'
   */
  public const SHAPE_TYPE_FLOW_CHART_DISPLAY = 'FLOW_CHART_DISPLAY';
  /**
   * Document flow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'flowChartDocument'
   */
  public const SHAPE_TYPE_FLOW_CHART_DOCUMENT = 'FLOW_CHART_DOCUMENT';
  /**
   * Extract flow shape. Corresponds to ECMA-376 ST_ShapeType 'flowChartExtract'
   */
  public const SHAPE_TYPE_FLOW_CHART_EXTRACT = 'FLOW_CHART_EXTRACT';
  /**
   * Input output flow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'flowChartInputOutput'
   */
  public const SHAPE_TYPE_FLOW_CHART_INPUT_OUTPUT = 'FLOW_CHART_INPUT_OUTPUT';
  /**
   * Internal storage flow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'flowChartInternalStorage'
   */
  public const SHAPE_TYPE_FLOW_CHART_INTERNAL_STORAGE = 'FLOW_CHART_INTERNAL_STORAGE';
  /**
   * Magnetic disk flow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'flowChartMagneticDisk'
   */
  public const SHAPE_TYPE_FLOW_CHART_MAGNETIC_DISK = 'FLOW_CHART_MAGNETIC_DISK';
  /**
   * Magnetic drum flow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'flowChartMagneticDrum'
   */
  public const SHAPE_TYPE_FLOW_CHART_MAGNETIC_DRUM = 'FLOW_CHART_MAGNETIC_DRUM';
  /**
   * Magnetic tape flow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'flowChartMagneticTape'
   */
  public const SHAPE_TYPE_FLOW_CHART_MAGNETIC_TAPE = 'FLOW_CHART_MAGNETIC_TAPE';
  /**
   * Manual input flow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'flowChartManualInput'
   */
  public const SHAPE_TYPE_FLOW_CHART_MANUAL_INPUT = 'FLOW_CHART_MANUAL_INPUT';
  /**
   * Manual operation flow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'flowChartManualOperation'
   */
  public const SHAPE_TYPE_FLOW_CHART_MANUAL_OPERATION = 'FLOW_CHART_MANUAL_OPERATION';
  /**
   * Merge flow shape. Corresponds to ECMA-376 ST_ShapeType 'flowChartMerge'
   */
  public const SHAPE_TYPE_FLOW_CHART_MERGE = 'FLOW_CHART_MERGE';
  /**
   * Multi-document flow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'flowChartMultidocument'
   */
  public const SHAPE_TYPE_FLOW_CHART_MULTIDOCUMENT = 'FLOW_CHART_MULTIDOCUMENT';
  /**
   * Offline storage flow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'flowChartOfflineStorage'
   */
  public const SHAPE_TYPE_FLOW_CHART_OFFLINE_STORAGE = 'FLOW_CHART_OFFLINE_STORAGE';
  /**
   * Off-page connector flow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'flowChartOffpageConnector'
   */
  public const SHAPE_TYPE_FLOW_CHART_OFFPAGE_CONNECTOR = 'FLOW_CHART_OFFPAGE_CONNECTOR';
  /**
   * Online storage flow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'flowChartOnlineStorage'
   */
  public const SHAPE_TYPE_FLOW_CHART_ONLINE_STORAGE = 'FLOW_CHART_ONLINE_STORAGE';
  /**
   * Or flow shape. Corresponds to ECMA-376 ST_ShapeType 'flowChartOr'
   */
  public const SHAPE_TYPE_FLOW_CHART_OR = 'FLOW_CHART_OR';
  /**
   * Predefined process flow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'flowChartPredefinedProcess'
   */
  public const SHAPE_TYPE_FLOW_CHART_PREDEFINED_PROCESS = 'FLOW_CHART_PREDEFINED_PROCESS';
  /**
   * Preparation flow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'flowChartPreparation'
   */
  public const SHAPE_TYPE_FLOW_CHART_PREPARATION = 'FLOW_CHART_PREPARATION';
  /**
   * Process flow shape. Corresponds to ECMA-376 ST_ShapeType 'flowChartProcess'
   */
  public const SHAPE_TYPE_FLOW_CHART_PROCESS = 'FLOW_CHART_PROCESS';
  /**
   * Punched card flow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'flowChartPunchedCard'
   */
  public const SHAPE_TYPE_FLOW_CHART_PUNCHED_CARD = 'FLOW_CHART_PUNCHED_CARD';
  /**
   * Punched tape flow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'flowChartPunchedTape'
   */
  public const SHAPE_TYPE_FLOW_CHART_PUNCHED_TAPE = 'FLOW_CHART_PUNCHED_TAPE';
  /**
   * Sort flow shape. Corresponds to ECMA-376 ST_ShapeType 'flowChartSort'
   */
  public const SHAPE_TYPE_FLOW_CHART_SORT = 'FLOW_CHART_SORT';
  /**
   * Summing junction flow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'flowChartSummingJunction'
   */
  public const SHAPE_TYPE_FLOW_CHART_SUMMING_JUNCTION = 'FLOW_CHART_SUMMING_JUNCTION';
  /**
   * Terminator flow shape. Corresponds to ECMA-376 ST_ShapeType
   * 'flowChartTerminator'
   */
  public const SHAPE_TYPE_FLOW_CHART_TERMINATOR = 'FLOW_CHART_TERMINATOR';
  /**
   * East arrow shape.
   */
  public const SHAPE_TYPE_ARROW_EAST = 'ARROW_EAST';
  /**
   * Northeast arrow shape.
   */
  public const SHAPE_TYPE_ARROW_NORTH_EAST = 'ARROW_NORTH_EAST';
  /**
   * North arrow shape.
   */
  public const SHAPE_TYPE_ARROW_NORTH = 'ARROW_NORTH';
  /**
   * Speech shape.
   */
  public const SHAPE_TYPE_SPEECH = 'SPEECH';
  /**
   * Star burst shape.
   */
  public const SHAPE_TYPE_STARBURST = 'STARBURST';
  /**
   * Teardrop shape. Corresponds to ECMA-376 ST_ShapeType 'teardrop'
   */
  public const SHAPE_TYPE_TEARDROP = 'TEARDROP';
  /**
   * Ellipse ribbon shape. Corresponds to ECMA-376 ST_ShapeType 'ellipseRibbon'
   */
  public const SHAPE_TYPE_ELLIPSE_RIBBON = 'ELLIPSE_RIBBON';
  /**
   * Ellipse ribbon 2 shape. Corresponds to ECMA-376 ST_ShapeType
   * 'ellipseRibbon2'
   */
  public const SHAPE_TYPE_ELLIPSE_RIBBON_2 = 'ELLIPSE_RIBBON_2';
  /**
   * Callout cloud shape. Corresponds to ECMA-376 ST_ShapeType 'cloudCallout'
   */
  public const SHAPE_TYPE_CLOUD_CALLOUT = 'CLOUD_CALLOUT';
  /**
   * Custom shape.
   */
  public const SHAPE_TYPE_CUSTOM = 'CUSTOM';
  protected $placeholderType = Placeholder::class;
  protected $placeholderDataType = '';
  protected $shapePropertiesType = ShapeProperties::class;
  protected $shapePropertiesDataType = '';
  /**
   * The type of the shape.
   *
   * @var string
   */
  public $shapeType;
  protected $textType = TextContent::class;
  protected $textDataType = '';

  /**
   * Placeholders are page elements that inherit from corresponding placeholders
   * on layouts and masters. If set, the shape is a placeholder shape and any
   * inherited properties can be resolved by looking at the parent placeholder
   * identified by the Placeholder.parent_object_id field.
   *
   * @param Placeholder $placeholder
   */
  public function setPlaceholder(Placeholder $placeholder)
  {
    $this->placeholder = $placeholder;
  }
  /**
   * @return Placeholder
   */
  public function getPlaceholder()
  {
    return $this->placeholder;
  }
  /**
   * The properties of the shape.
   *
   * @param ShapeProperties $shapeProperties
   */
  public function setShapeProperties(ShapeProperties $shapeProperties)
  {
    $this->shapeProperties = $shapeProperties;
  }
  /**
   * @return ShapeProperties
   */
  public function getShapeProperties()
  {
    return $this->shapeProperties;
  }
  /**
   * The type of the shape.
   *
   * Accepted values: TYPE_UNSPECIFIED, TEXT_BOX, RECTANGLE, ROUND_RECTANGLE,
   * ELLIPSE, ARC, BENT_ARROW, BENT_UP_ARROW, BEVEL, BLOCK_ARC, BRACE_PAIR,
   * BRACKET_PAIR, CAN, CHEVRON, CHORD, CLOUD, CORNER, CUBE, CURVED_DOWN_ARROW,
   * CURVED_LEFT_ARROW, CURVED_RIGHT_ARROW, CURVED_UP_ARROW, DECAGON,
   * DIAGONAL_STRIPE, DIAMOND, DODECAGON, DONUT, DOUBLE_WAVE, DOWN_ARROW,
   * DOWN_ARROW_CALLOUT, FOLDED_CORNER, FRAME, HALF_FRAME, HEART, HEPTAGON,
   * HEXAGON, HOME_PLATE, HORIZONTAL_SCROLL, IRREGULAR_SEAL_1, IRREGULAR_SEAL_2,
   * LEFT_ARROW, LEFT_ARROW_CALLOUT, LEFT_BRACE, LEFT_BRACKET, LEFT_RIGHT_ARROW,
   * LEFT_RIGHT_ARROW_CALLOUT, LEFT_RIGHT_UP_ARROW, LEFT_UP_ARROW,
   * LIGHTNING_BOLT, MATH_DIVIDE, MATH_EQUAL, MATH_MINUS, MATH_MULTIPLY,
   * MATH_NOT_EQUAL, MATH_PLUS, MOON, NO_SMOKING, NOTCHED_RIGHT_ARROW, OCTAGON,
   * PARALLELOGRAM, PENTAGON, PIE, PLAQUE, PLUS, QUAD_ARROW, QUAD_ARROW_CALLOUT,
   * RIBBON, RIBBON_2, RIGHT_ARROW, RIGHT_ARROW_CALLOUT, RIGHT_BRACE,
   * RIGHT_BRACKET, ROUND_1_RECTANGLE, ROUND_2_DIAGONAL_RECTANGLE,
   * ROUND_2_SAME_RECTANGLE, RIGHT_TRIANGLE, SMILEY_FACE, SNIP_1_RECTANGLE,
   * SNIP_2_DIAGONAL_RECTANGLE, SNIP_2_SAME_RECTANGLE, SNIP_ROUND_RECTANGLE,
   * STAR_10, STAR_12, STAR_16, STAR_24, STAR_32, STAR_4, STAR_5, STAR_6,
   * STAR_7, STAR_8, STRIPED_RIGHT_ARROW, SUN, TRAPEZOID, TRIANGLE, UP_ARROW,
   * UP_ARROW_CALLOUT, UP_DOWN_ARROW, UTURN_ARROW, VERTICAL_SCROLL, WAVE,
   * WEDGE_ELLIPSE_CALLOUT, WEDGE_RECTANGLE_CALLOUT,
   * WEDGE_ROUND_RECTANGLE_CALLOUT, FLOW_CHART_ALTERNATE_PROCESS,
   * FLOW_CHART_COLLATE, FLOW_CHART_CONNECTOR, FLOW_CHART_DECISION,
   * FLOW_CHART_DELAY, FLOW_CHART_DISPLAY, FLOW_CHART_DOCUMENT,
   * FLOW_CHART_EXTRACT, FLOW_CHART_INPUT_OUTPUT, FLOW_CHART_INTERNAL_STORAGE,
   * FLOW_CHART_MAGNETIC_DISK, FLOW_CHART_MAGNETIC_DRUM,
   * FLOW_CHART_MAGNETIC_TAPE, FLOW_CHART_MANUAL_INPUT,
   * FLOW_CHART_MANUAL_OPERATION, FLOW_CHART_MERGE, FLOW_CHART_MULTIDOCUMENT,
   * FLOW_CHART_OFFLINE_STORAGE, FLOW_CHART_OFFPAGE_CONNECTOR,
   * FLOW_CHART_ONLINE_STORAGE, FLOW_CHART_OR, FLOW_CHART_PREDEFINED_PROCESS,
   * FLOW_CHART_PREPARATION, FLOW_CHART_PROCESS, FLOW_CHART_PUNCHED_CARD,
   * FLOW_CHART_PUNCHED_TAPE, FLOW_CHART_SORT, FLOW_CHART_SUMMING_JUNCTION,
   * FLOW_CHART_TERMINATOR, ARROW_EAST, ARROW_NORTH_EAST, ARROW_NORTH, SPEECH,
   * STARBURST, TEARDROP, ELLIPSE_RIBBON, ELLIPSE_RIBBON_2, CLOUD_CALLOUT,
   * CUSTOM
   *
   * @param self::SHAPE_TYPE_* $shapeType
   */
  public function setShapeType($shapeType)
  {
    $this->shapeType = $shapeType;
  }
  /**
   * @return self::SHAPE_TYPE_*
   */
  public function getShapeType()
  {
    return $this->shapeType;
  }
  /**
   * The text content of the shape.
   *
   * @param TextContent $text
   */
  public function setText(TextContent $text)
  {
    $this->text = $text;
  }
  /**
   * @return TextContent
   */
  public function getText()
  {
    return $this->text;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Shape::class, 'Google_Service_Slides_Shape');
