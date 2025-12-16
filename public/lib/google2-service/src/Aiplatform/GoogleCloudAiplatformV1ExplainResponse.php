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

class GoogleCloudAiplatformV1ExplainResponse extends \Google\Collection
{
  protected $collection_key = 'predictions';
  /**
   * ID of the Endpoint's DeployedModel that served this explanation.
   *
   * @var string
   */
  public $deployedModelId;
  protected $explanationsType = GoogleCloudAiplatformV1Explanation::class;
  protected $explanationsDataType = 'array';
  /**
   * The predictions that are the output of the predictions call. Same as
   * PredictResponse.predictions.
   *
   * @var array[]
   */
  public $predictions;

  /**
   * ID of the Endpoint's DeployedModel that served this explanation.
   *
   * @param string $deployedModelId
   */
  public function setDeployedModelId($deployedModelId)
  {
    $this->deployedModelId = $deployedModelId;
  }
  /**
   * @return string
   */
  public function getDeployedModelId()
  {
    return $this->deployedModelId;
  }
  /**
   * The explanations of the Model's PredictResponse.predictions. It has the
   * same number of elements as instances to be explained.
   *
   * @param GoogleCloudAiplatformV1Explanation[] $explanations
   */
  public function setExplanations($explanations)
  {
    $this->explanations = $explanations;
  }
  /**
   * @return GoogleCloudAiplatformV1Explanation[]
   */
  public function getExplanations()
  {
    return $this->explanations;
  }
  /**
   * The predictions that are the output of the predictions call. Same as
   * PredictResponse.predictions.
   *
   * @param array[] $predictions
   */
  public function setPredictions($predictions)
  {
    $this->predictions = $predictions;
  }
  /**
   * @return array[]
   */
  public function getPredictions()
  {
    return $this->predictions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ExplainResponse::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ExplainResponse');
