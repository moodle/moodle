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

class GoogleCloudAiplatformV1WriteFeatureValuesPayload extends \Google\Model
{
  /**
   * Required. The ID of the entity.
   *
   * @var string
   */
  public $entityId;
  protected $featureValuesType = GoogleCloudAiplatformV1FeatureValue::class;
  protected $featureValuesDataType = 'map';

  /**
   * Required. The ID of the entity.
   *
   * @param string $entityId
   */
  public function setEntityId($entityId)
  {
    $this->entityId = $entityId;
  }
  /**
   * @return string
   */
  public function getEntityId()
  {
    return $this->entityId;
  }
  /**
   * Required. Feature values to be written, mapping from Feature ID to value.
   * Up to 100,000 `feature_values` entries may be written across all payloads.
   * The feature generation time, aligned by days, must be no older than five
   * years (1825 days) and no later than one year (366 days) in the future.
   *
   * @param GoogleCloudAiplatformV1FeatureValue[] $featureValues
   */
  public function setFeatureValues($featureValues)
  {
    $this->featureValues = $featureValues;
  }
  /**
   * @return GoogleCloudAiplatformV1FeatureValue[]
   */
  public function getFeatureValues()
  {
    return $this->featureValues;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1WriteFeatureValuesPayload::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1WriteFeatureValuesPayload');
