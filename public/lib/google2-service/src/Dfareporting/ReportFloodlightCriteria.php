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

namespace Google\Service\Dfareporting;

class ReportFloodlightCriteria extends \Google\Collection
{
  protected $collection_key = 'metricNames';
  protected $customRichMediaEventsType = DimensionValue::class;
  protected $customRichMediaEventsDataType = 'array';
  protected $dateRangeType = DateRange::class;
  protected $dateRangeDataType = '';
  protected $dimensionFiltersType = DimensionValue::class;
  protected $dimensionFiltersDataType = 'array';
  protected $dimensionsType = SortedDimension::class;
  protected $dimensionsDataType = 'array';
  protected $floodlightConfigIdType = DimensionValue::class;
  protected $floodlightConfigIdDataType = '';
  /**
   * The list of names of metrics the report should include.
   *
   * @var string[]
   */
  public $metricNames;
  protected $reportPropertiesType = ReportFloodlightCriteriaReportProperties::class;
  protected $reportPropertiesDataType = '';

  /**
   * The list of custom rich media events to include.
   *
   * @param DimensionValue[] $customRichMediaEvents
   */
  public function setCustomRichMediaEvents($customRichMediaEvents)
  {
    $this->customRichMediaEvents = $customRichMediaEvents;
  }
  /**
   * @return DimensionValue[]
   */
  public function getCustomRichMediaEvents()
  {
    return $this->customRichMediaEvents;
  }
  /**
   * The date range this report should be run for.
   *
   * @param DateRange $dateRange
   */
  public function setDateRange(DateRange $dateRange)
  {
    $this->dateRange = $dateRange;
  }
  /**
   * @return DateRange
   */
  public function getDateRange()
  {
    return $this->dateRange;
  }
  /**
   * The list of filters on which dimensions are filtered. Filters for different
   * dimensions are ANDed, filters for the same dimension are grouped together
   * and ORed.
   *
   * @param DimensionValue[] $dimensionFilters
   */
  public function setDimensionFilters($dimensionFilters)
  {
    $this->dimensionFilters = $dimensionFilters;
  }
  /**
   * @return DimensionValue[]
   */
  public function getDimensionFilters()
  {
    return $this->dimensionFilters;
  }
  /**
   * The list of dimensions the report should include.
   *
   * @param SortedDimension[] $dimensions
   */
  public function setDimensions($dimensions)
  {
    $this->dimensions = $dimensions;
  }
  /**
   * @return SortedDimension[]
   */
  public function getDimensions()
  {
    return $this->dimensions;
  }
  /**
   * The floodlight ID for which to show data in this report. All advertisers
   * associated with that ID will automatically be added. The dimension of the
   * value needs to be 'dfa:floodlightConfigId'.
   *
   * @param DimensionValue $floodlightConfigId
   */
  public function setFloodlightConfigId(DimensionValue $floodlightConfigId)
  {
    $this->floodlightConfigId = $floodlightConfigId;
  }
  /**
   * @return DimensionValue
   */
  public function getFloodlightConfigId()
  {
    return $this->floodlightConfigId;
  }
  /**
   * The list of names of metrics the report should include.
   *
   * @param string[] $metricNames
   */
  public function setMetricNames($metricNames)
  {
    $this->metricNames = $metricNames;
  }
  /**
   * @return string[]
   */
  public function getMetricNames()
  {
    return $this->metricNames;
  }
  /**
   * The properties of the report.
   *
   * @param ReportFloodlightCriteriaReportProperties $reportProperties
   */
  public function setReportProperties(ReportFloodlightCriteriaReportProperties $reportProperties)
  {
    $this->reportProperties = $reportProperties;
  }
  /**
   * @return ReportFloodlightCriteriaReportProperties
   */
  public function getReportProperties()
  {
    return $this->reportProperties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportFloodlightCriteria::class, 'Google_Service_Dfareporting_ReportFloodlightCriteria');
