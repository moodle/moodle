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

namespace Google\Service\AnalyticsData;

class CohortSpec extends \Google\Collection
{
  protected $collection_key = 'cohorts';
  protected $cohortReportSettingsType = CohortReportSettings::class;
  protected $cohortReportSettingsDataType = '';
  protected $cohortsType = Cohort::class;
  protected $cohortsDataType = 'array';
  protected $cohortsRangeType = CohortsRange::class;
  protected $cohortsRangeDataType = '';

  /**
   * Optional settings for a cohort report.
   *
   * @param CohortReportSettings $cohortReportSettings
   */
  public function setCohortReportSettings(CohortReportSettings $cohortReportSettings)
  {
    $this->cohortReportSettings = $cohortReportSettings;
  }
  /**
   * @return CohortReportSettings
   */
  public function getCohortReportSettings()
  {
    return $this->cohortReportSettings;
  }
  /**
   * Defines the selection criteria to group users into cohorts. Most cohort
   * reports define only a single cohort. If multiple cohorts are specified,
   * each cohort can be recognized in the report by their name.
   *
   * @param Cohort[] $cohorts
   */
  public function setCohorts($cohorts)
  {
    $this->cohorts = $cohorts;
  }
  /**
   * @return Cohort[]
   */
  public function getCohorts()
  {
    return $this->cohorts;
  }
  /**
   * Cohort reports follow cohorts over an extended reporting date range. This
   * range specifies an offset duration to follow the cohorts over.
   *
   * @param CohortsRange $cohortsRange
   */
  public function setCohortsRange(CohortsRange $cohortsRange)
  {
    $this->cohortsRange = $cohortsRange;
  }
  /**
   * @return CohortsRange
   */
  public function getCohortsRange()
  {
    return $this->cohortsRange;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CohortSpec::class, 'Google_Service_AnalyticsData_CohortSpec');
