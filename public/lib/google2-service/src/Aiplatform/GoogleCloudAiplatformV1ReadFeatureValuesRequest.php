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

class GoogleCloudAiplatformV1ReadFeatureValuesRequest extends \Google\Model
{
  /**
   * Required. ID for a specific entity. For example, for a machine learning
   * model predicting user clicks on a website, an entity ID could be
   * `user_123`.
   *
   * @var string
   */
  public $entityId;
  protected $featureSelectorType = GoogleCloudAiplatformV1FeatureSelector::class;
  protected $featureSelectorDataType = '';

  /**
   * Required. ID for a specific entity. For example, for a machine learning
   * model predicting user clicks on a website, an entity ID could be
   * `user_123`.
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
   * Required. Selector choosing Features of the target EntityType.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ReadFeatureValuesRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ReadFeatureValuesRequest');
