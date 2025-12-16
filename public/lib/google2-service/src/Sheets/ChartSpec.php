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

class ChartSpec extends \Google\Collection
{
  /**
   * Default value, do not use.
   */
  public const HIDDEN_DIMENSION_STRATEGY_CHART_HIDDEN_DIMENSION_STRATEGY_UNSPECIFIED = 'CHART_HIDDEN_DIMENSION_STRATEGY_UNSPECIFIED';
  /**
   * Charts will skip hidden rows and columns.
   */
  public const HIDDEN_DIMENSION_STRATEGY_SKIP_HIDDEN_ROWS_AND_COLUMNS = 'SKIP_HIDDEN_ROWS_AND_COLUMNS';
  /**
   * Charts will skip hidden rows only.
   */
  public const HIDDEN_DIMENSION_STRATEGY_SKIP_HIDDEN_ROWS = 'SKIP_HIDDEN_ROWS';
  /**
   * Charts will skip hidden columns only.
   */
  public const HIDDEN_DIMENSION_STRATEGY_SKIP_HIDDEN_COLUMNS = 'SKIP_HIDDEN_COLUMNS';
  /**
   * Charts will not skip any hidden rows or columns.
   */
  public const HIDDEN_DIMENSION_STRATEGY_SHOW_ALL = 'SHOW_ALL';
  protected $collection_key = 'sortSpecs';
  /**
   * The alternative text that describes the chart. This is often used for
   * accessibility.
   *
   * @var string
   */
  public $altText;
  protected $backgroundColorType = Color::class;
  protected $backgroundColorDataType = '';
  protected $backgroundColorStyleType = ColorStyle::class;
  protected $backgroundColorStyleDataType = '';
  protected $basicChartType = BasicChartSpec::class;
  protected $basicChartDataType = '';
  protected $bubbleChartType = BubbleChartSpec::class;
  protected $bubbleChartDataType = '';
  protected $candlestickChartType = CandlestickChartSpec::class;
  protected $candlestickChartDataType = '';
  protected $dataSourceChartPropertiesType = DataSourceChartProperties::class;
  protected $dataSourceChartPropertiesDataType = '';
  protected $filterSpecsType = FilterSpec::class;
  protected $filterSpecsDataType = 'array';
  /**
   * The name of the font to use by default for all chart text (e.g. title, axis
   * labels, legend). If a font is specified for a specific part of the chart it
   * will override this font name.
   *
   * @var string
   */
  public $fontName;
  /**
   * Determines how the charts will use hidden rows or columns.
   *
   * @var string
   */
  public $hiddenDimensionStrategy;
  protected $histogramChartType = HistogramChartSpec::class;
  protected $histogramChartDataType = '';
  /**
   * True to make a chart fill the entire space in which it's rendered with
   * minimum padding. False to use the default padding. (Not applicable to Geo
   * and Org charts.)
   *
   * @var bool
   */
  public $maximized;
  protected $orgChartType = OrgChartSpec::class;
  protected $orgChartDataType = '';
  protected $pieChartType = PieChartSpec::class;
  protected $pieChartDataType = '';
  protected $scorecardChartType = ScorecardChartSpec::class;
  protected $scorecardChartDataType = '';
  protected $sortSpecsType = SortSpec::class;
  protected $sortSpecsDataType = 'array';
  /**
   * The subtitle of the chart.
   *
   * @var string
   */
  public $subtitle;
  protected $subtitleTextFormatType = TextFormat::class;
  protected $subtitleTextFormatDataType = '';
  protected $subtitleTextPositionType = TextPosition::class;
  protected $subtitleTextPositionDataType = '';
  /**
   * The title of the chart.
   *
   * @var string
   */
  public $title;
  protected $titleTextFormatType = TextFormat::class;
  protected $titleTextFormatDataType = '';
  protected $titleTextPositionType = TextPosition::class;
  protected $titleTextPositionDataType = '';
  protected $treemapChartType = TreemapChartSpec::class;
  protected $treemapChartDataType = '';
  protected $waterfallChartType = WaterfallChartSpec::class;
  protected $waterfallChartDataType = '';

  /**
   * The alternative text that describes the chart. This is often used for
   * accessibility.
   *
   * @param string $altText
   */
  public function setAltText($altText)
  {
    $this->altText = $altText;
  }
  /**
   * @return string
   */
  public function getAltText()
  {
    return $this->altText;
  }
  /**
   * The background color of the entire chart. Not applicable to Org charts.
   * Deprecated: Use background_color_style.
   *
   * @deprecated
   * @param Color $backgroundColor
   */
  public function setBackgroundColor(Color $backgroundColor)
  {
    $this->backgroundColor = $backgroundColor;
  }
  /**
   * @deprecated
   * @return Color
   */
  public function getBackgroundColor()
  {
    return $this->backgroundColor;
  }
  /**
   * The background color of the entire chart. Not applicable to Org charts. If
   * background_color is also set, this field takes precedence.
   *
   * @param ColorStyle $backgroundColorStyle
   */
  public function setBackgroundColorStyle(ColorStyle $backgroundColorStyle)
  {
    $this->backgroundColorStyle = $backgroundColorStyle;
  }
  /**
   * @return ColorStyle
   */
  public function getBackgroundColorStyle()
  {
    return $this->backgroundColorStyle;
  }
  /**
   * A basic chart specification, can be one of many kinds of charts. See
   * BasicChartType for the list of all charts this supports.
   *
   * @param BasicChartSpec $basicChart
   */
  public function setBasicChart(BasicChartSpec $basicChart)
  {
    $this->basicChart = $basicChart;
  }
  /**
   * @return BasicChartSpec
   */
  public function getBasicChart()
  {
    return $this->basicChart;
  }
  /**
   * A bubble chart specification.
   *
   * @param BubbleChartSpec $bubbleChart
   */
  public function setBubbleChart(BubbleChartSpec $bubbleChart)
  {
    $this->bubbleChart = $bubbleChart;
  }
  /**
   * @return BubbleChartSpec
   */
  public function getBubbleChart()
  {
    return $this->bubbleChart;
  }
  /**
   * A candlestick chart specification.
   *
   * @param CandlestickChartSpec $candlestickChart
   */
  public function setCandlestickChart(CandlestickChartSpec $candlestickChart)
  {
    $this->candlestickChart = $candlestickChart;
  }
  /**
   * @return CandlestickChartSpec
   */
  public function getCandlestickChart()
  {
    return $this->candlestickChart;
  }
  /**
   * If present, the field contains data source chart specific properties.
   *
   * @param DataSourceChartProperties $dataSourceChartProperties
   */
  public function setDataSourceChartProperties(DataSourceChartProperties $dataSourceChartProperties)
  {
    $this->dataSourceChartProperties = $dataSourceChartProperties;
  }
  /**
   * @return DataSourceChartProperties
   */
  public function getDataSourceChartProperties()
  {
    return $this->dataSourceChartProperties;
  }
  /**
   * The filters applied to the source data of the chart. Only supported for
   * data source charts.
   *
   * @param FilterSpec[] $filterSpecs
   */
  public function setFilterSpecs($filterSpecs)
  {
    $this->filterSpecs = $filterSpecs;
  }
  /**
   * @return FilterSpec[]
   */
  public function getFilterSpecs()
  {
    return $this->filterSpecs;
  }
  /**
   * The name of the font to use by default for all chart text (e.g. title, axis
   * labels, legend). If a font is specified for a specific part of the chart it
   * will override this font name.
   *
   * @param string $fontName
   */
  public function setFontName($fontName)
  {
    $this->fontName = $fontName;
  }
  /**
   * @return string
   */
  public function getFontName()
  {
    return $this->fontName;
  }
  /**
   * Determines how the charts will use hidden rows or columns.
   *
   * Accepted values: CHART_HIDDEN_DIMENSION_STRATEGY_UNSPECIFIED,
   * SKIP_HIDDEN_ROWS_AND_COLUMNS, SKIP_HIDDEN_ROWS, SKIP_HIDDEN_COLUMNS,
   * SHOW_ALL
   *
   * @param self::HIDDEN_DIMENSION_STRATEGY_* $hiddenDimensionStrategy
   */
  public function setHiddenDimensionStrategy($hiddenDimensionStrategy)
  {
    $this->hiddenDimensionStrategy = $hiddenDimensionStrategy;
  }
  /**
   * @return self::HIDDEN_DIMENSION_STRATEGY_*
   */
  public function getHiddenDimensionStrategy()
  {
    return $this->hiddenDimensionStrategy;
  }
  /**
   * A histogram chart specification.
   *
   * @param HistogramChartSpec $histogramChart
   */
  public function setHistogramChart(HistogramChartSpec $histogramChart)
  {
    $this->histogramChart = $histogramChart;
  }
  /**
   * @return HistogramChartSpec
   */
  public function getHistogramChart()
  {
    return $this->histogramChart;
  }
  /**
   * True to make a chart fill the entire space in which it's rendered with
   * minimum padding. False to use the default padding. (Not applicable to Geo
   * and Org charts.)
   *
   * @param bool $maximized
   */
  public function setMaximized($maximized)
  {
    $this->maximized = $maximized;
  }
  /**
   * @return bool
   */
  public function getMaximized()
  {
    return $this->maximized;
  }
  /**
   * An org chart specification.
   *
   * @param OrgChartSpec $orgChart
   */
  public function setOrgChart(OrgChartSpec $orgChart)
  {
    $this->orgChart = $orgChart;
  }
  /**
   * @return OrgChartSpec
   */
  public function getOrgChart()
  {
    return $this->orgChart;
  }
  /**
   * A pie chart specification.
   *
   * @param PieChartSpec $pieChart
   */
  public function setPieChart(PieChartSpec $pieChart)
  {
    $this->pieChart = $pieChart;
  }
  /**
   * @return PieChartSpec
   */
  public function getPieChart()
  {
    return $this->pieChart;
  }
  /**
   * A scorecard chart specification.
   *
   * @param ScorecardChartSpec $scorecardChart
   */
  public function setScorecardChart(ScorecardChartSpec $scorecardChart)
  {
    $this->scorecardChart = $scorecardChart;
  }
  /**
   * @return ScorecardChartSpec
   */
  public function getScorecardChart()
  {
    return $this->scorecardChart;
  }
  /**
   * The order to sort the chart data by. Only a single sort spec is supported.
   * Only supported for data source charts.
   *
   * @param SortSpec[] $sortSpecs
   */
  public function setSortSpecs($sortSpecs)
  {
    $this->sortSpecs = $sortSpecs;
  }
  /**
   * @return SortSpec[]
   */
  public function getSortSpecs()
  {
    return $this->sortSpecs;
  }
  /**
   * The subtitle of the chart.
   *
   * @param string $subtitle
   */
  public function setSubtitle($subtitle)
  {
    $this->subtitle = $subtitle;
  }
  /**
   * @return string
   */
  public function getSubtitle()
  {
    return $this->subtitle;
  }
  /**
   * The subtitle text format. Strikethrough, underline, and link are not
   * supported.
   *
   * @param TextFormat $subtitleTextFormat
   */
  public function setSubtitleTextFormat(TextFormat $subtitleTextFormat)
  {
    $this->subtitleTextFormat = $subtitleTextFormat;
  }
  /**
   * @return TextFormat
   */
  public function getSubtitleTextFormat()
  {
    return $this->subtitleTextFormat;
  }
  /**
   * The subtitle text position. This field is optional.
   *
   * @param TextPosition $subtitleTextPosition
   */
  public function setSubtitleTextPosition(TextPosition $subtitleTextPosition)
  {
    $this->subtitleTextPosition = $subtitleTextPosition;
  }
  /**
   * @return TextPosition
   */
  public function getSubtitleTextPosition()
  {
    return $this->subtitleTextPosition;
  }
  /**
   * The title of the chart.
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
   * The title text format. Strikethrough, underline, and link are not
   * supported.
   *
   * @param TextFormat $titleTextFormat
   */
  public function setTitleTextFormat(TextFormat $titleTextFormat)
  {
    $this->titleTextFormat = $titleTextFormat;
  }
  /**
   * @return TextFormat
   */
  public function getTitleTextFormat()
  {
    return $this->titleTextFormat;
  }
  /**
   * The title text position. This field is optional.
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
   * A treemap chart specification.
   *
   * @param TreemapChartSpec $treemapChart
   */
  public function setTreemapChart(TreemapChartSpec $treemapChart)
  {
    $this->treemapChart = $treemapChart;
  }
  /**
   * @return TreemapChartSpec
   */
  public function getTreemapChart()
  {
    return $this->treemapChart;
  }
  /**
   * A waterfall chart specification.
   *
   * @param WaterfallChartSpec $waterfallChart
   */
  public function setWaterfallChart(WaterfallChartSpec $waterfallChart)
  {
    $this->waterfallChart = $waterfallChart;
  }
  /**
   * @return WaterfallChartSpec
   */
  public function getWaterfallChart()
  {
    return $this->waterfallChart;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChartSpec::class, 'Google_Service_Sheets_ChartSpec');
