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

class ReportPathToConversionCriteria extends \Google\Collection
{
  protected $collection_key = 'perInteractionDimensions';
  protected $activityFiltersType = DimensionValue::class;
  protected $activityFiltersDataType = 'array';
  protected $conversionDimensionsType = SortedDimension::class;
  protected $conversionDimensionsDataType = 'array';
  protected $customFloodlightVariablesType = SortedDimension::class;
  protected $customFloodlightVariablesDataType = 'array';
  protected $customRichMediaEventsType = DimensionValue::class;
  protected $customRichMediaEventsDataType = 'array';
  protected $dateRangeType = DateRange::class;
  protected $dateRangeDataType = '';
  protected $floodlightConfigIdType = DimensionValue::class;
  protected $floodlightConfigIdDataType = '';
  /**
   * The list of names of metrics the report should include.
   *
   * @var string[]
   */
  public $metricNames;
  protected $perInteractionDimensionsType = SortedDimension::class;
  protected $perInteractionDimensionsDataType = 'array';
  protected $reportPropertiesType = ReportPathToConversionCriteriaReportProperties::class;
  protected $reportPropertiesDataType = '';

  /**
   * The list of 'dfa:activity' values to filter on.
   *
   * @param DimensionValue[] $activityFilters
   */
  public function setActivityFilters($activityFilters)
  {
    $this->activityFilters = $activityFilters;
  }
  /**
   * @return DimensionValue[]
   */
  public function getActivityFilters()
  {
    return $this->activityFilters;
  }
  /**
   * The list of conversion dimensions the report should include.
   *
   * @param SortedDimension[] $conversionDimensions
   */
  public function setConversionDimensions($conversionDimensions)
  {
    $this->conversionDimensions = $conversionDimensions;
  }
  /**
   * @return SortedDimension[]
   */
  public function getConversionDimensions()
  {
    return $this->conversionDimensions;
  }
  /**
   * The list of custom floodlight variables the report should include.
   *
   * @param SortedDimension[] $customFloodlightVariables
   */
  public function setCustomFloodlightVariables($customFloodlightVariables)
  {
    $this->customFloodlightVariables = $customFloodlightVariables;
  }
  /**
   * @return SortedDimension[]
   */
  public function getCustomFloodlightVariables()
  {
    return $this->customFloodlightVariables;
  }
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
   * The list of per interaction dimensions the report should include.
   *
   * @param SortedDimension[] $perInteractionDimensions
   */
  public function setPerInteractionDimensions($perInteractionDimensions)
  {
    $this->perInteractionDimensions = $perInteractionDimensions;
  }
  /**
   * @return SortedDimension[]
   */
  public function getPerInteractionDimensions()
  {
    return $this->perInteractionDimensions;
  }
  /**
   * The properties of the report.
   *
   * @param ReportPathToConversionCriteriaReportProperties $reportProperties
   */
  public function setReportProperties(ReportPathToConversionCriteriaReportProperties $reportProperties)
  {
    $this->reportProperties = $reportProperties;
  }
  /**
   * @return ReportPathToConversionCriteriaReportProperties
   */
  public function getReportProperties()
  {
    return $this->reportProperties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportPathToConversionCriteria::class, 'Google_Service_Dfareporting_ReportPathToConversionCriteria');
