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

class OrgChartSpec extends \Google\Model
{
  /**
   * Default value, do not use.
   */
  public const NODE_SIZE_ORG_CHART_LABEL_SIZE_UNSPECIFIED = 'ORG_CHART_LABEL_SIZE_UNSPECIFIED';
  /**
   * The small org chart node size.
   */
  public const NODE_SIZE_SMALL = 'SMALL';
  /**
   * The medium org chart node size.
   */
  public const NODE_SIZE_MEDIUM = 'MEDIUM';
  /**
   * The large org chart node size.
   */
  public const NODE_SIZE_LARGE = 'LARGE';
  protected $labelsType = ChartData::class;
  protected $labelsDataType = '';
  protected $nodeColorType = Color::class;
  protected $nodeColorDataType = '';
  protected $nodeColorStyleType = ColorStyle::class;
  protected $nodeColorStyleDataType = '';
  /**
   * The size of the org chart nodes.
   *
   * @var string
   */
  public $nodeSize;
  protected $parentLabelsType = ChartData::class;
  protected $parentLabelsDataType = '';
  protected $selectedNodeColorType = Color::class;
  protected $selectedNodeColorDataType = '';
  protected $selectedNodeColorStyleType = ColorStyle::class;
  protected $selectedNodeColorStyleDataType = '';
  protected $tooltipsType = ChartData::class;
  protected $tooltipsDataType = '';

  /**
   * The data containing the labels for all the nodes in the chart. Labels must
   * be unique.
   *
   * @param ChartData $labels
   */
  public function setLabels(ChartData $labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return ChartData
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * The color of the org chart nodes. Deprecated: Use node_color_style.
   *
   * @deprecated
   * @param Color $nodeColor
   */
  public function setNodeColor(Color $nodeColor)
  {
    $this->nodeColor = $nodeColor;
  }
  /**
   * @deprecated
   * @return Color
   */
  public function getNodeColor()
  {
    return $this->nodeColor;
  }
  /**
   * The color of the org chart nodes. If node_color is also set, this field
   * takes precedence.
   *
   * @param ColorStyle $nodeColorStyle
   */
  public function setNodeColorStyle(ColorStyle $nodeColorStyle)
  {
    $this->nodeColorStyle = $nodeColorStyle;
  }
  /**
   * @return ColorStyle
   */
  public function getNodeColorStyle()
  {
    return $this->nodeColorStyle;
  }
  /**
   * The size of the org chart nodes.
   *
   * Accepted values: ORG_CHART_LABEL_SIZE_UNSPECIFIED, SMALL, MEDIUM, LARGE
   *
   * @param self::NODE_SIZE_* $nodeSize
   */
  public function setNodeSize($nodeSize)
  {
    $this->nodeSize = $nodeSize;
  }
  /**
   * @return self::NODE_SIZE_*
   */
  public function getNodeSize()
  {
    return $this->nodeSize;
  }
  /**
   * The data containing the label of the parent for the corresponding node. A
   * blank value indicates that the node has no parent and is a top-level node.
   * This field is optional.
   *
   * @param ChartData $parentLabels
   */
  public function setParentLabels(ChartData $parentLabels)
  {
    $this->parentLabels = $parentLabels;
  }
  /**
   * @return ChartData
   */
  public function getParentLabels()
  {
    return $this->parentLabels;
  }
  /**
   * The color of the selected org chart nodes. Deprecated: Use
   * selected_node_color_style.
   *
   * @deprecated
   * @param Color $selectedNodeColor
   */
  public function setSelectedNodeColor(Color $selectedNodeColor)
  {
    $this->selectedNodeColor = $selectedNodeColor;
  }
  /**
   * @deprecated
   * @return Color
   */
  public function getSelectedNodeColor()
  {
    return $this->selectedNodeColor;
  }
  /**
   * The color of the selected org chart nodes. If selected_node_color is also
   * set, this field takes precedence.
   *
   * @param ColorStyle $selectedNodeColorStyle
   */
  public function setSelectedNodeColorStyle(ColorStyle $selectedNodeColorStyle)
  {
    $this->selectedNodeColorStyle = $selectedNodeColorStyle;
  }
  /**
   * @return ColorStyle
   */
  public function getSelectedNodeColorStyle()
  {
    return $this->selectedNodeColorStyle;
  }
  /**
   * The data containing the tooltip for the corresponding node. A blank value
   * results in no tooltip being displayed for the node. This field is optional.
   *
   * @param ChartData $tooltips
   */
  public function setTooltips(ChartData $tooltips)
  {
    $this->tooltips = $tooltips;
  }
  /**
   * @return ChartData
   */
  public function getTooltips()
  {
    return $this->tooltips;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrgChartSpec::class, 'Google_Service_Sheets_OrgChartSpec');
