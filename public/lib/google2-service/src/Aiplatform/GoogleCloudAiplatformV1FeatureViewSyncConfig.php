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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1FeatureViewSyncConfig extends \Google\Model
{
  /**
   * Optional. If true, syncs the FeatureView in a continuous manner to Online
   * Store.
   *
   * @var bool
   */
  public $continuous;
  /**
   * Cron schedule (https://en.wikipedia.org/wiki/Cron) to launch scheduled
   * runs. To explicitly set a timezone to the cron tab, apply a prefix in the
   * cron tab: "CRON_TZ=${IANA_TIME_ZONE}" or "TZ=${IANA_TIME_ZONE}". The
   * ${IANA_TIME_ZONE} may only be a valid string from IANA time zone database.
   * For example, "CRON_TZ=America/New_York 1 * * * *", or "TZ=America/New_York
   * 1 * * * *".
   *
   * @var string
   */
  public $cron;

  /**
   * Optional. If true, syncs the FeatureView in a continuous manner to Online
   * Store.
   *
   * @param bool $continuous
   */
  public function setContinuous($continuous)
  {
    $this->continuous = $continuous;
  }
  /**
   * @return bool
   */
  public function getContinuous()
  {
    return $this->continuous;
  }
  /**
   * Cron schedule (https://en.wikipedia.org/wiki/Cron) to launch scheduled
   * runs. To explicitly set a timezone to the cron tab, apply a prefix in the
   * cron tab: "CRON_TZ=${IANA_TIME_ZONE}" or "TZ=${IANA_TIME_ZONE}". The
   * ${IANA_TIME_ZONE} may only be a valid string from IANA time zone database.
   * For example, "CRON_TZ=America/New_York 1 * * * *", or "TZ=America/New_York
   * 1 * * * *".
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
class_alias(GoogleCloudAiplatformV1FeatureViewSyncConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FeatureViewSyncConfig');
