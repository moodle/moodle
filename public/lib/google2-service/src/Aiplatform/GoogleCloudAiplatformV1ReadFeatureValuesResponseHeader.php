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

class GoogleCloudAiplatformV1ReadFeatureValuesResponseHeader extends \Google\Collection
{
  protected $collection_key = 'featureDescriptors';
  /**
   * The resource name of the EntityType from the ReadFeatureValuesRequest.
   * Value format: `projects/{project}/locations/{location}/featurestores/{featu
   * restore}/entityTypes/{entityType}`.
   *
   * @var string
   */
  public $entityType;
  protected $featureDescriptorsType = GoogleCloudAiplatformV1ReadFeatureValuesResponseFeatureDescriptor::class;
  protected $featureDescriptorsDataType = 'array';

  /**
   * The resource name of the EntityType from the ReadFeatureValuesRequest.
   * Value format: `projects/{project}/locations/{location}/featurestores/{featu
   * restore}/entityTypes/{entityType}`.
   *
   * @param string $entityType
   */
  public function setEntityType($entityType)
  {
    $this->entityType = $entityType;
  }
  /**
   * @return string
   */
  public function getEntityType()
  {
    return $this->entityType;
  }
  /**
   * List of Feature metadata corresponding to each piece of
   * ReadFeatureValuesResponse.EntityView.data.
   *
   * @param GoogleCloudAiplatformV1ReadFeatureValuesResponseFeatureDescriptor[] $featureDescriptors
   */
  public function setFeatureDescriptors($featureDescriptors)
  {
    $this->featureDescriptors = $featureDescriptors;
  }
  /**
   * @return GoogleCloudAiplatformV1ReadFeatureValuesResponseFeatureDescriptor[]
   */
  public function getFeatureDescriptors()
  {
    return $this->featureDescriptors;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ReadFeatureValuesResponseHeader::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ReadFeatureValuesResponseHeader');
