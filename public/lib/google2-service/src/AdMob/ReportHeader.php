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

class ReportHeader extends \Google\Model
{
  protected $dateRangeType = DateRange::class;
  protected $dateRangeDataType = '';
  protected $localizationSettingsType = LocalizationSettings::class;
  protected $localizationSettingsDataType = '';
  /**
   * The report time zone. The value is a time-zone ID as specified by the CLDR
   * project, for example, "America/Los_Angeles".
   *
   * @var string
   */
  public $reportingTimeZone;

  /**
   * The date range for which the report is generated. This is identical to the
   * range specified in the report request.
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
   * Localization settings of the report. This is identical to the settings in
   * the report request.
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
   * The report time zone. The value is a time-zone ID as specified by the CLDR
   * project, for example, "America/Los_Angeles".
   *
   * @param string $reportingTimeZone
   */
  public function setReportingTimeZone($reportingTimeZone)
  {
    $this->reportingTimeZone = $reportingTimeZone;
  }
  /**
   * @return string
   */
  public function getReportingTimeZone()
  {
    return $this->reportingTimeZone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportHeader::class, 'Google_Service_AdMob_ReportHeader');
