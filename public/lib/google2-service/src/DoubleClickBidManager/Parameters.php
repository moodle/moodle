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

namespace Google\Service\DoubleClickBidManager;

class Parameters extends \Google\Collection
{
  /**
   * Default value when report type is not specified or is unknown in this
   * version.
   */
  public const TYPE_REPORT_TYPE_UNSPECIFIED = 'REPORT_TYPE_UNSPECIFIED';
  /**
   * Standard report.
   */
  public const TYPE_STANDARD = 'STANDARD';
  /**
   * Inventory Availability report.
   */
  public const TYPE_INVENTORY_AVAILABILITY = 'INVENTORY_AVAILABILITY';
  /**
   * Audience Composition report.
   *
   * @deprecated
   */
  public const TYPE_AUDIENCE_COMPOSITION = 'AUDIENCE_COMPOSITION';
  /**
   * Floodlight report.
   */
  public const TYPE_FLOODLIGHT = 'FLOODLIGHT';
  /**
   * YouTube report.
   */
  public const TYPE_YOUTUBE = 'YOUTUBE';
  /**
   * GRP report.
   */
  public const TYPE_GRP = 'GRP';
  /**
   * YouTube Programmatic Guaranteed report.
   */
  public const TYPE_YOUTUBE_PROGRAMMATIC_GUARANTEED = 'YOUTUBE_PROGRAMMATIC_GUARANTEED';
  /**
   * Reach report.
   */
  public const TYPE_REACH = 'REACH';
  /**
   * Unique Reach Audience report.
   */
  public const TYPE_UNIQUE_REACH_AUDIENCE = 'UNIQUE_REACH_AUDIENCE';
  /**
   * Full Path report.
   *
   * @deprecated
   */
  public const TYPE_FULL_PATH = 'FULL_PATH';
  /**
   * Path Attribution report.
   *
   * @deprecated
   */
  public const TYPE_PATH_ATTRIBUTION = 'PATH_ATTRIBUTION';
  protected $collection_key = 'metrics';
  protected $filtersType = FilterPair::class;
  protected $filtersDataType = 'array';
  /**
   * Dimensions by which to segment and group the data. Defined by
   * [Filter](/bid-manager/reference/rest/v2/filters-metrics#filters) values.
   *
   * @var string[]
   */
  public $groupBys;
  /**
   * Metrics to define the data populating the report. Defined by [Metric](/bid-
   * manager/reference/rest/v2/filters-metrics#metrics) values.
   *
   * @var string[]
   */
  public $metrics;
  protected $optionsType = Options::class;
  protected $optionsDataType = '';
  /**
   * The type of the report. The type of the report determines the dimesions,
   * filters, and metrics that can be used.
   *
   * @var string
   */
  public $type;

  /**
   * Filters to limit the scope of reported data.
   *
   * @param FilterPair[] $filters
   */
  public function setFilters($filters)
  {
    $this->filters = $filters;
  }
  /**
   * @return FilterPair[]
   */
  public function getFilters()
  {
    return $this->filters;
  }
  /**
   * Dimensions by which to segment and group the data. Defined by
   * [Filter](/bid-manager/reference/rest/v2/filters-metrics#filters) values.
   *
   * @param string[] $groupBys
   */
  public function setGroupBys($groupBys)
  {
    $this->groupBys = $groupBys;
  }
  /**
   * @return string[]
   */
  public function getGroupBys()
  {
    return $this->groupBys;
  }
  /**
   * Metrics to define the data populating the report. Defined by [Metric](/bid-
   * manager/reference/rest/v2/filters-metrics#metrics) values.
   *
   * @param string[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return string[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * Additional report parameter options.
   *
   * @param Options $options
   */
  public function setOptions(Options $options)
  {
    $this->options = $options;
  }
  /**
   * @return Options
   */
  public function getOptions()
  {
    return $this->options;
  }
  /**
   * The type of the report. The type of the report determines the dimesions,
   * filters, and metrics that can be used.
   *
   * Accepted values: REPORT_TYPE_UNSPECIFIED, STANDARD, INVENTORY_AVAILABILITY,
   * AUDIENCE_COMPOSITION, FLOODLIGHT, YOUTUBE, GRP,
   * YOUTUBE_PROGRAMMATIC_GUARANTEED, REACH, UNIQUE_REACH_AUDIENCE, FULL_PATH,
   * PATH_ATTRIBUTION
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
class_alias(Parameters::class, 'Google_Service_DoubleClickBidManager_Parameters');
