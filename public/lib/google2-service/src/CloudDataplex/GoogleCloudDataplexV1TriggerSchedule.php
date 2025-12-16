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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1TriggerSchedule extends \Google\Model
{
  /**
   * Required. Cron (https://en.wikipedia.org/wiki/Cron) schedule for running
   * scans periodically.To explicitly set a timezone in the cron tab, apply a
   * prefix in the cron tab: "CRON_TZ=${IANA_TIME_ZONE}" or
   * "TZ=${IANA_TIME_ZONE}". The ${IANA_TIME_ZONE} may only be a valid string
   * from IANA time zone database (wikipedia
   * (https://en.wikipedia.org/wiki/List_of_tz_database_time_zones#List)). For
   * example, CRON_TZ=America/New_York 1 * * * *, or TZ=America/New_York 1 * * *
   * *.This field is required for Schedule scans.
   *
   * @var string
   */
  public $cron;

  /**
   * Required. Cron (https://en.wikipedia.org/wiki/Cron) schedule for running
   * scans periodically.To explicitly set a timezone in the cron tab, apply a
   * prefix in the cron tab: "CRON_TZ=${IANA_TIME_ZONE}" or
   * "TZ=${IANA_TIME_ZONE}". The ${IANA_TIME_ZONE} may only be a valid string
   * from IANA time zone database (wikipedia
   * (https://en.wikipedia.org/wiki/List_of_tz_database_time_zones#List)). For
   * example, CRON_TZ=America/New_York 1 * * * *, or TZ=America/New_York 1 * * *
   * *.This field is required for Schedule scans.
   *
   * @param string $cron
   */
  public function setCron($cron)
  {
    $this->cron = $cron;
  }
  /**
   * @return string
   */
  public function getCron()
  {
    return $this->cron;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1TriggerSchedule::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1TriggerSchedule');
