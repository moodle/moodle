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

class GoogleCloudAiplatformV1QueryDeployedModelsResponse extends \Google\Collection
{
  protected $collection_key = 'deployedModels';
  protected $deployedModelRefsType = GoogleCloudAiplatformV1DeployedModelRef::class;
  protected $deployedModelRefsDataType = 'array';
  protected $deployedModelsType = GoogleCloudAiplatformV1DeployedModel::class;
  protected $deployedModelsDataType = 'array';
  /**
   * A token, which can be sent as `page_token` to retrieve the next page. If
   * this field is omitted, there are no subsequent pages.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * The total number of DeployedModels on this DeploymentResourcePool.
   *
   * @var int
   */
  public $totalDeployedModelCount;
  /**
   * The total number of Endpoints that have DeployedModels on this
   * DeploymentResourcePool.
   *
   * @var int
   */
  public $totalEndpointCount;

  /**
   * References to the DeployedModels that share the specified
   * deploymentResourcePool.
   *
   * @param GoogleCloudAiplatformV1DeployedModelRef[] $deployedModelRefs
   */
  public function setDeployedModelRefs($deployedModelRefs)
  {
    $this->deployedModelRefs = $deployedModelRefs;
  }
  /**
   * @return GoogleCloudAiplatformV1DeployedModelRef[]
   */
  public function getDeployedModelRefs()
  {
    return $this->deployedModelRefs;
  }
  /**
   * DEPRECATED Use deployed_model_refs instead.
   *
   * @deprecated
   * @param GoogleCloudAiplatformV1DeployedModel[] $deployedModels
   */
  public function setDeployedModels($deployedModels)
  {
    $this->deployedModels = $deployedModels;
  }
  /**
   * @deprecated
   * @return GoogleCloudAiplatformV1DeployedModel[]
   */
  public function getDeployedModels()
  {
    return $this->deployedModels;
  }
  /**
   * A token, which can be sent as `page_token` to retrieve the next page. If
   * this field is omitted, there are no subsequent pages.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  /**
   * The total number of DeployedModels on this DeploymentResourcePool.
   *
   * @param int $totalDeployedModelCount
   */
  public function setTotalDeployedModelCount($totalDeployedModelCount)
  {
    $this->totalDeployedModelCount = $totalDeployedModelCount;
  }
  /**
   * @return int
   */
  public function getTotalDeployedModelCount()
  {
    return $this->totalDeployedModelCount;
  }
  /**
   * The total number of Endpoints that have DeployedModels on this
   * DeploymentResourcePool.
   *
   * @param int $totalEndpointCount
   */
  public function setTotalEndpointCount($totalEndpointCount)
  {
    $this->totalEndpointCount = $totalEndpointCount;
  }
  /**
   * @return int
   */
  public function getTotalEndpointCount()
  {
    return $this->totalEndpointCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1QueryDeployedModelsResponse::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1QueryDeployedModelsResponse');
