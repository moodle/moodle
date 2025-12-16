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

namespace Google\Service\AdMob;

class MediationReportSpec extends \Google\Collection
{
  protected $collection_key = 'sortConditions';
  protected $dateRangeType = DateRange::class;
  protected $dateRangeDataType = '';
  protected $dimensionFiltersType = MediationReportSpecDimensionFilter::class;
  protected $dimensionFiltersDataType = 'array';
  /**
   * List of dimensions of the report. The value combination of these dimensions
   * determines the row of the report. If no dimensions are specified, the
   * report returns a single row of requested metrics for the entire account.
   *
   * @var string[]
   */
  public $dimensions;
  protected $localizationSettingsType = LocalizationSettings::class;
  protected $localizationSettingsDataType = '';
  /**
   * Maximum number of report data rows to return. If the value is not set, the
   * API returns as many rows as possible, up to 100000. Acceptable values are
   * 1-100000, inclusive. Values larger than 100000 return an error.
   *
   * @var int
   */
  public $maxReportRows;
  /**
   * List of metrics of the report. A report must specify at least one metric.
   *
   * @var string[]
   */
  public $metrics;
  protected $sortConditionsType = MediationReportSpecSortCondition::class;
  protected $sortConditionsDataType = 'array';
  /**
   * A report time zone. Accepts an IANA TZ name values, such as
   * "America/Los_Angeles." If no time zone is defined, the account default
   * takes effect. Check default value by the get account action. **Warning:**
   * The "America/Los_Angeles" is the only supported value at the moment.
   *
   * @var string
   */
  public $timeZone;

  /**
   * The date range for which the report is generated.
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
   * Describes which report rows to match based on their dimension values.
   *
   * @param MediationReportSpecDimensionFilter[] $dimensionFilters
   */
  public function setDimensionFilters($dimensionFilters)
  {
    $this->dimensionFilters = $dimensionFilters;
  }
  /**
   * @return MediationReportSpecDimensionFilter[]
   */
  public function getDimensionFilters()
  {
    return $this->dimensionFilters;
  }
  /**
   * List of dimensions of the report. The value combination of these dimensions
   * determines the row of the report. If no dimensions are specified, the
   * report returns a single row of requested metrics for the entire account.
   *
   * @param string[] $dimensions
   */
  public function setDimensions($dimensions)
  {
    $this->dimensions = $dimensions;
  }
  /**
   * @return string[]
   */
  public function getDimensions()
  {
    return $this->dimensions;
  }
  /**
   * Localization settings of the report.
   *
   * @param LocalizationSettings $localizationSettings
   */
  public function setLocalizationSettings(LocalizationSettings $localizationSettings)
  {
    $this->localizationSettings = $localizationSettings;
  }
  /**
   * @return LocalizationSettings
   */
  public function getLocalizationSettings()
  {
    return $this->localizationSettings;
  }
  /**
   * Maximum number of report data rows to return. If the value is not set, the
   * API returns as many rows as possible, up to 100000. Acceptable values are
   * 1-100000, inclusive. Values larger than 100000 return an error.
   *
   * @param int $maxReportRows
   */
  public function setMaxReportRows($maxReportRows)
  {
    $this->maxReportRows = $maxReportRows;
  }
  /**
   * @return int
   */
  public function getMaxReportRows()
  {
    return $this->maxReportRows;
  }
  /**
   * List of metrics of the report. A report must specify at least one metric.
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
   * Describes the sorting of report rows. The order of the condition in the
   * list defines its precedence; the earlier the condition, the higher its
   * precedence. If no sort conditions are specified, the row ordering is
   * undefined.
   *
   * @param MediationReportSpecSortCondition[] $sortConditions
   */
  public function setSortConditions($sortConditions)
  {
    $this->sortConditions = $sortConditions;
  }
  /**
   * @return MediationReportSpecSortCondition[]
   */
  public function getSortConditions()
  {
    return $this->sortConditions;
  }
  /**
   * A report time zone. Accepts an IANA TZ name values, such as
   * "America/Los_Angeles." If no time zone is defined, the account default
   * takes effect. Check default value by the get account action. **Warning:**
   * The "America/Los_Angeles" is the only supported value at the moment.
   *
   * @param string $timeZone
   */
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return string
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MediationReportSpec::class, 'Google_Service_AdMob_MediationReportSpec');
