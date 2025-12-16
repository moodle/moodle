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

class GoogleCloudAiplatformV1FeatureViewFeatureRegistrySourceFeatureGroup extends \Google\Collection
{
  protected $collection_key = 'featureIds';
  /**
   * Required. Identifier of the feature group.
   *
   * @var string
   */
  public $featureGroupId;
  /**
   * Required. Identifiers of features under the feature group.
   *
   * @var string[]
   */
  public $featureIds;

  /**
   * Required. Identifier of the feature group.
   *
   * @param string $featureGroupId
   */
  public function setFeatureGroupId($featureGroupId)
  {
    $this->featureGroupId = $featureGroupId;
  }
  /**
   * @return string
   */
  public function getFeatureGroupId()
  {
    return $this->featureGroupId;
  }
  /**
   * Required. Identifiers of features under the feature group.
   *
   * @param string[] $featureIds
   */
  public function setFeatureIds($featureIds)
  {
    $this->featureIds = $featureIds;
  }
  /**
   * @return string[]
   */
  public function getFeatureIds()
  {
    return $this->featureIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1FeatureViewFeatureRegistrySourceFeatureGroup::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FeatureViewFeatureRegistrySourceFeatureGroup');
