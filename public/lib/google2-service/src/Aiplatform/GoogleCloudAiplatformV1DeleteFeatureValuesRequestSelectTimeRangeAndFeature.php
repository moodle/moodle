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

class GoogleCloudAiplatformV1DeleteFeatureValuesRequestSelectTimeRangeAndFeature extends \Google\Model
{
  protected $featureSelectorType = GoogleCloudAiplatformV1FeatureSelector::class;
  protected $featureSelectorDataType = '';
  /**
   * If set, data will not be deleted from online storage. When time range is
   * older than the data in online storage, setting this to be true will make
   * the deletion have no impact on online serving.
   *
   * @var bool
   */
  public $skipOnlineStorageDelete;
  protected $timeRangeType = GoogleTypeInterval::class;
  protected $timeRangeDataType = '';

  /**
   * Required. Selectors choosing which feature values to be deleted from the
   * EntityType.
   *
   * @param GoogleCloudAiplatformV1FeatureSelector $featureSelector
   */
  public function setFeatureSelector(GoogleCloudAiplatformV1FeatureSelector $featureSelector)
  {
    $this->featureSelector = $featureSelector;
  }
  /**
   * @return GoogleCloudAiplatformV1FeatureSelector
   */
  public function getFeatureSelector()
  {
    return $this->featureSelector;
  }
  /**
   * If set, data will not be deleted from online storage. When time range is
   * older than the data in online storage, setting this to be true will make
   * the deletion have no impact on online serving.
   *
   * @param bool $skipOnlineStorageDelete
   */
  public function setSkipOnlineStorageDelete($skipOnlineStorageDelete)
  {
    $this->skipOnlineStorageDelete = $skipOnlineStorageDelete;
  }
  /**
   * @return bool
   */
  public function getSkipOnlineStorageDelete()
  {
    return $this->skipOnlineStorageDelete;
  }
  /**
   * Required. Select feature generated within a half-inclusive time range. The
   * time range is lower inclusive and upper exclusive.
   *
   * @param GoogleTypeInterval $timeRange
   */
  public function setTimeRange(GoogleTypeInterval $timeRange)
  {
    $this->timeRange = $timeRange;
  }
  /**
   * @return GoogleTypeInterval
   */
  public function getTimeRange()
  {
    return $this->timeRange;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1DeleteFeatureValuesRequestSelectTimeRangeAndFeature::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1DeleteFeatureValuesRequestSelectTimeRangeAndFeature');
