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

class GoogleCloudAiplatformV1FeatureValueMetadata extends \Google\Model
{
  /**
   * Feature generation timestamp. Typically, it is provided by user at feature
   * ingestion time. If not, feature store will use the system timestamp when
   * the data is ingested into feature store. Legacy Feature Store: For
   * streaming ingestion, the time, aligned by days, must be no older than five
   * years (1825 days) and no later than one year (366 days) in the future.
   *
   * @var string
   */
  public $generateTime;

  /**
   * Feature generation timestamp. Typically, it is provided by user at feature
   * ingestion time. If not, feature store will use the system timestamp when
   * the data is ingested into feature store. Legacy Feature Store: For
   * streaming ingestion, the time, aligned by days, must be no older than five
   * years (1825 days) and no later than one year (366 days) in the future.
   *
   * @param string $generateTime
   */
  public function setGenerateTime($generateTime)
  {
    $this->generateTime = $generateTime;
  }
  /**
   * @return string
   */
  public function getGenerateTime()
  {
    return $this->generateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1FeatureValueMetadata::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FeatureValueMetadata');
