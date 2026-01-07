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

class GoogleCloudAiplatformV1PublisherModelCallToActionDeployVertex extends \Google\Collection
{
  protected $collection_key = 'multiDeployVertex';
  protected $multiDeployVertexType = GoogleCloudAiplatformV1PublisherModelCallToActionDeploy::class;
  protected $multiDeployVertexDataType = 'array';

  /**
   * Optional. One click deployment configurations.
   *
   * @param GoogleCloudAiplatformV1PublisherModelCallToActionDeploy[] $multiDeployVertex
   */
  public function setMultiDeployVertex($multiDeployVertex)
  {
    $this->multiDeployVertex = $multiDeployVertex;
  }
  /**
   * @return GoogleCloudAiplatformV1PublisherModelCallToActionDeploy[]
   */
  public function getMultiDeployVertex()
  {
    return $this->multiDeployVertex;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1PublisherModelCallToActionDeployVertex::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PublisherModelCallToActionDeployVertex');
