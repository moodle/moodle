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

class GoogleCloudAiplatformV1FeatureViewSyncSyncSummary extends \Google\Model
{
  /**
   * Output only. Total number of rows synced.
   *
   * @var string
   */
  public $rowSynced;
  /**
   * Lower bound of the system time watermark for the sync job. This is only set
   * for continuously syncing feature views.
   *
   * @var string
   */
  public $systemWatermarkTime;
  /**
   * Output only. BigQuery slot milliseconds consumed for the sync job.
   *
   * @var string
   */
  public $totalSlot;

  /**
   * Output only. Total number of rows synced.
   *
   * @param string $rowSynced
   */
  public function setRowSynced($rowSynced)
  {
    $this->rowSynced = $rowSynced;
  }
  /**
   * @return string
   */
  public function getRowSynced()
  {
    return $this->rowSynced;
  }
  /**
   * Lower bound of the system time watermark for the sync job. This is only set
   * for continuously syncing feature views.
   *
   * @param string $systemWatermarkTime
   */
  public function setSystemWatermarkTime($systemWatermarkTime)
  {
    $this->systemWatermarkTime = $systemWatermarkTime;
  }
  /**
   * @return string
   */
  public function getSystemWatermarkTime()
  {
    return $this->systemWatermarkTime;
  }
  /**
   * Output only. BigQuery slot milliseconds consumed for the sync job.
   *
   * @param string $totalSlot
   */
  public function setTotalSlot($totalSlot)
  {
    $this->totalSlot = $totalSlot;
  }
  /**
   * @return string
   */
  public function getTotalSlot()
  {
    return $this->totalSlot;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1FeatureViewSyncSyncSummary::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FeatureViewSyncSyncSummary');
