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

class GoogleCloudAiplatformV1DeployedIndexRef extends \Google\Model
{
  /**
   * Immutable. The ID of the DeployedIndex in the above IndexEndpoint.
   *
   * @var string
   */
  public $deployedIndexId;
  /**
   * Output only. The display name of the DeployedIndex.
   *
   * @var string
   */
  public $displayName;
  /**
   * Immutable. A resource name of the IndexEndpoint.
   *
   * @var string
   */
  public $indexEndpoint;

  /**
   * Immutable. The ID of the DeployedIndex in the above IndexEndpoint.
   *
   * @param string $deployedIndexId
   */
  public function setDeployedIndexId($deployedIndexId)
  {
    $this->deployedIndexId = $deployedIndexId;
  }
  /**
   * @return string
   */
  public function getDeployedIndexId()
  {
    return $this->deployedIndexId;
  }
  /**
   * Output only. The display name of the DeployedIndex.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Immutable. A resource name of the IndexEndpoint.
   *
   * @param string $indexEndpoint
   */
  public function setIndexEndpoint($indexEndpoint)
  {
    $this->indexEndpoint = $indexEndpoint;
  }
  /**
   * @return string
   */
  public function getIndexEndpoint()
  {
    return $this->indexEndpoint;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1DeployedIndexRef::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1DeployedIndexRef');
