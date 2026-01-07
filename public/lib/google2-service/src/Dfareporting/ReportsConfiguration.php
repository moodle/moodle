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

class ReportsConfiguration extends \Google\Model
{
  /**
   * Whether the exposure to conversion report is enabled. This report shows
   * detailed pathway information on up to 10 of the most recent ad exposures
   * seen by a user before converting.
   *
   * @var bool
   */
  public $exposureToConversionEnabled;
  protected $lookbackConfigurationType = LookbackConfiguration::class;
  protected $lookbackConfigurationDataType = '';
  /**
   * Report generation time zone ID of this account. This is a required field
   * that cannot be changed on update. Acceptable values are: - "1" for
   * "America/New_York" - "2" for "Europe/London" - "3" for "Europe/Paris" - "4"
   * for "Africa/Johannesburg" - "5" for "Asia/Jerusalem" - "6" for
   * "Asia/Shanghai" - "7" for "Asia/Hong_Kong" - "8" for "Asia/Tokyo" - "9" for
   * "Australia/Sydney" - "10" for "Asia/Dubai" - "11" for "America/Los_Angeles"
   * - "12" for "Pacific/Auckland" - "13" for "America/Sao_Paulo" - "16" for
   * "America/Asuncion" - "17" for "America/Chicago" - "18" for "America/Denver"
   * - "19" for "America/St_Johns" - "20" for "Asia/Dhaka" - "21" for
   * "Asia/Jakarta" - "22" for "Asia/Kabul" - "23" for "Asia/Karachi" - "24" for
   * "Asia/Calcutta" - "25" for "Asia/Pyongyang" - "26" for "Asia/Rangoon" -
   * "27" for "Atlantic/Cape_Verde" - "28" for "Atlantic/South_Georgia" - "29"
   * for "Australia/Adelaide" - "30" for "Australia/Lord_Howe" - "31" for
   * "Europe/Moscow" - "32" for "Pacific/Kiritimati" - "35" for
   * "Pacific/Norfolk" - "36" for "Pacific/Tongatapu"
   *
   * @var string
   */
  public $reportGenerationTimeZoneId;

  /**
   * Whether the exposure to conversion report is enabled. This report shows
   * detailed pathway information on up to 10 of the most recent ad exposures
   * seen by a user before converting.
   *
   * @param bool $exposureToConversionEnabled
   */
  public function setExposureToConversionEnabled($exposureToConversionEnabled)
  {
    $this->exposureToConversionEnabled = $exposureToConversionEnabled;
  }
  /**
   * @return bool
   */
  public function getExposureToConversionEnabled()
  {
    return $this->exposureToConversionEnabled;
  }
  /**
   * Default lookback windows for new advertisers in this account.
   *
   * @param LookbackConfiguration $lookbackConfiguration
   */
  public function setLookbackConfiguration(LookbackConfiguration $lookbackConfiguration)
  {
    $this->lookbackConfiguration = $lookbackConfiguration;
  }
  /**
   * @return LookbackConfiguration
   */
  public function getLookbackConfiguration()
  {
    return $this->lookbackConfiguration;
  }
  /**
   * Report generation time zone ID of this account. This is a required field
   * that cannot be changed on update. Acceptable values are: - "1" for
   * "America/New_York" - "2" for "Europe/London" - "3" for "Europe/Paris" - "4"
   * for "Africa/Johannesburg" - "5" for "Asia/Jerusalem" - "6" for
   * "Asia/Shanghai" - "7" for "Asia/Hong_Kong" - "8" for "Asia/Tokyo" - "9" for
   * "Australia/Sydney" - "10" for "Asia/Dubai" - "11" for "America/Los_Angeles"
   * - "12" for "Pacific/Auckland" - "13" for "America/Sao_Paulo" - "16" for
   * "America/Asuncion" - "17" for "America/Chicago" - "18" for "America/Denver"
   * - "19" for "America/St_Johns" - "20" for "Asia/Dhaka" - "21" for
   * "Asia/Jakarta" - "22" for "Asia/Kabul" - "23" for "Asia/Karachi" - "24" for
   * "Asia/Calcutta" - "25" for "Asia/Pyongyang" - "26" for "Asia/Rangoon" -
   * "27" for "Atlantic/Cape_Verde" - "28" for "Atlantic/South_Georgia" - "29"
   * for "Australia/Adelaide" - "30" for "Australia/Lord_Howe" - "31" for
   * "Europe/Moscow" - "32" for "Pacific/Kiritimati" - "35" for
   * "Pacific/Norfolk" - "36" for "Pacific/Tongatapu"
   *
   * @param string $reportGenerationTimeZoneId
   */
  public function setReportGenerationTimeZoneId($reportGenerationTimeZoneId)
  {
    $this->reportGenerationTimeZoneId = $reportGenerationTimeZoneId;
  }
  /**
   * @return string
   */
  public function getReportGenerationTimeZoneId()
  {
    return $this->reportGenerationTimeZoneId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportsConfiguration::class, 'Google_Service_Dfareporting_ReportsConfiguration');
