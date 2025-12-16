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

class GoogleCloudAiplatformV1MutateDeployedIndexResponse extends \Google\Model
{
  protected $deployedIndexType = GoogleCloudAiplatformV1DeployedIndex::class;
  protected $deployedIndexDataType = '';

  /**
   * The DeployedIndex that had been updated in the IndexEndpoint.
   *
   * @param GoogleCloudAiplatformV1DeployedIndex $deployedIndex
   */
  public function setDeployedIndex(GoogleCloudAiplatformV1DeployedIndex $deployedIndex)
  {
    $this->deployedIndex = $deployedIndex;
  }
  /**
   * @return GoogleCloudAiplatformV1DeployedIndex
   */
  public function getDeployedIndex()
  {
    return $this->deployedIndex;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1MutateDeployedIndexResponse::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1MutateDeployedIndexResponse');
