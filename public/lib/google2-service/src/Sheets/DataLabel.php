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

class DataLabel extends \Google\Model
{
  /**
   * The positioning is determined automatically by the renderer.
   */
  public const PLACEMENT_DATA_LABEL_PLACEMENT_UNSPECIFIED = 'DATA_LABEL_PLACEMENT_UNSPECIFIED';
  /**
   * Center within a bar or column, both horizontally and vertically.
   */
  public const PLACEMENT_CENTER = 'CENTER';
  /**
   * To the left of a data point.
   */
  public const PLACEMENT_LEFT = 'LEFT';
  /**
   * To the right of a data point.
   */
  public const PLACEMENT_RIGHT = 'RIGHT';
  /**
   * Above a data point.
   */
  public const PLACEMENT_ABOVE = 'ABOVE';
  /**
   * Below a data point.
   */
  public const PLACEMENT_BELOW = 'BELOW';
  /**
   * Inside a bar or column at the end (top if positive, bottom if negative).
   */
  public const PLACEMENT_INSIDE_END = 'INSIDE_END';
  /**
   * Inside a bar or column at the base.
   */
  public const PLACEMENT_INSIDE_BASE = 'INSIDE_BASE';
  /**
   * Outside a bar or column at the end.
   */
  public const PLACEMENT_OUTSIDE_END = 'OUTSIDE_END';
  /**
   * The data label type is not specified and will be interpreted depending on
   * the context of the data label within the chart.
   */
  public const TYPE_DATA_LABEL_TYPE_UNSPECIFIED = 'DATA_LABEL_TYPE_UNSPECIFIED';
  /**
   * The data label is not displayed.
   */
  public const TYPE_NONE = 'NONE';
  /**
   * The data label is displayed using values from the series data.
   */
  public const TYPE_DATA = 'DATA';
  /**
   * The data label is displayed using values from a custom data source
   * indicated by customLabelData.
   */
  public const TYPE_CUSTOM = 'CUSTOM';
  protected $customLabelDataType = ChartData::class;
  protected $customLabelDataDataType = '';
  /**
   * The placement of the data label relative to the labeled data.
   *
   * @var string
   */
  public $placement;
  protected $textFormatType = TextFormat::class;
  protected $textFormatDataType = '';
  /**
   * The type of the data label.
   *
   * @var string
   */
  public $type;

  /**
   * Data to use for custom labels. Only used if type is set to CUSTOM. This
   * data must be the same length as the series or other element this data label
   * is applied to. In addition, if the series is split into multiple source
   * ranges, this source data must come from the next column in the source data.
   * For example, if the series is B2:B4,E6:E8 then this data must come from
   * C2:C4,F6:F8.
   *
   * @param ChartData $customLabelData
   */
  public function setCustomLabelData(ChartData $customLabelData)
  {
    $this->customLabelData = $customLabelData;
  }
  /**
   * @return ChartData
   */
  public function getCustomLabelData()
  {
    return $this->customLabelData;
  }
  /**
   * The placement of the data label relative to the labeled data.
   *
   * Accepted values: DATA_LABEL_PLACEMENT_UNSPECIFIED, CENTER, LEFT, RIGHT,
   * ABOVE, BELOW, INSIDE_END, INSIDE_BASE, OUTSIDE_END
   *
   * @param self::PLACEMENT_* $placement
   */
  public function setPlacement($placement)
  {
    $this->placement = $placement;
  }
  /**
   * @return self::PLACEMENT_*
   */
  public function getPlacement()
  {
    return $this->placement;
  }
  /**
   * The text format used for the data label. The link field is not supported.
   *
   * @param TextFormat $textFormat
   */
  public function setTextFormat(TextFormat $textFormat)
  {
    $this->textFormat = $textFormat;
  }
  /**
   * @return TextFormat
   */
  public function getTextFormat()
  {
    return $this->textFormat;
  }
  /**
   * The type of the data label.
   *
   * Accepted values: DATA_LABEL_TYPE_UNSPECIFIED, NONE, DATA, CUSTOM
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataLabel::class, 'Google_Service_Sheets_DataLabel');
