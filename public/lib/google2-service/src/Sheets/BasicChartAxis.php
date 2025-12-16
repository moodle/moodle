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

namespace Google\Service\Sheets;

class BasicChartAxis extends \Google\Model
{
  /**
   * Default value, do not use.
   */
  public const POSITION_BASIC_CHART_AXIS_POSITION_UNSPECIFIED = 'BASIC_CHART_AXIS_POSITION_UNSPECIFIED';
  /**
   * The axis rendered at the bottom of a chart. For most charts, this is the
   * standard major axis. For bar charts, this is a minor axis.
   */
  public const POSITION_BOTTOM_AXIS = 'BOTTOM_AXIS';
  /**
   * The axis rendered at the left of a chart. For most charts, this is a minor
   * axis. For bar charts, this is the standard major axis.
   */
  public const POSITION_LEFT_AXIS = 'LEFT_AXIS';
  /**
   * The axis rendered at the right of a chart. For most charts, this is a minor
   * axis. For bar charts, this is an unusual major axis.
   */
  public const POSITION_RIGHT_AXIS = 'RIGHT_AXIS';
  protected $formatType = TextFormat::class;
  protected $formatDataType = '';
  /**
   * The position of this axis.
   *
   * @var string
   */
  public $position;
  /**
   * The title of this axis. If set, this overrides any title inferred from
   * headers of the data.
   *
   * @var string
   */
  public $title;
  protected $titleTextPositionType = TextPosition::class;
  protected $titleTextPositionDataType = '';
  protected $viewWindowOptionsType = ChartAxisViewWindowOptions::class;
  protected $viewWindowOptionsDataType = '';

  /**
   * The format of the title. Only valid if the axis is not associated with the
   * domain. The link field is not supported.
   *
   * @param TextFormat $format
   */
  public function setFormat(TextFormat $format)
  {
    $this->format = $format;
  }
  /**
   * @return TextFormat
   */
  public function getFormat()
  {
    return $this->format;
  }
  /**
   * The position of this axis.
   *
   * Accepted values: BASIC_CHART_AXIS_POSITION_UNSPECIFIED, BOTTOM_AXIS,
   * LEFT_AXIS, RIGHT_AXIS
   *
   * @param self::POSITION_* $position
   */
  public function setPosition($position)
  {
    $this->position = $position;
  }
  /**
   * @return self::POSITION_*
   */
  public function getPosition()
  {
    return $this->position;
  }
  /**
   * The title of this axis. If set, this overrides any title inferred from
   * headers of the data.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * The axis title text position.
   *
   * @param TextPosition $titleTextPosition
   */
  public function setTitleTextPosition(TextPosition $titleTextPosition)
  {
    $this->titleTextPosition = $titleTextPosition;
  }
  /**
   * @return TextPosition
   */
  public function getTitleTextPosition()
  {
    return $this->titleTextPosition;
  }
  /**
   * The view window options for this axis.
   *
   * @param ChartAxisViewWindowOptions $viewWindowOptions
   */
  public function setViewWindowOptions(ChartAxisViewWindowOptions $viewWindowOptions)
  {
    $this->viewWindowOptions = $viewWindowOptions;
  }
  /**
   * @return ChartAxisViewWindowOptions
   */
  public function getViewWindowOptions()
  {
    return $this->viewWindowOptions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BasicChartAxis::class, 'Google_Service_Sheets_BasicChartAxis');
