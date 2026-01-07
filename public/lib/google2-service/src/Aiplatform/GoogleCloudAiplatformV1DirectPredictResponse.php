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

class GoogleCloudAiplatformV1DirectPredictResponse extends \Google\Collection
{
  protected $collection_key = 'outputs';
  protected $outputsType = GoogleCloudAiplatformV1Tensor::class;
  protected $outputsDataType = 'array';
  protected $parametersType = GoogleCloudAiplatformV1Tensor::class;
  protected $parametersDataType = '';

  /**
   * The prediction output.
   *
   * @param GoogleCloudAiplatformV1Tensor[] $outputs
   */
  public function setOutputs($outputs)
  {
    $this->outputs = $outputs;
  }
  /**
   * @return GoogleCloudAiplatformV1Tensor[]
   */
  public function getOutputs()
  {
    return $this->outputs;
  }
  /**
   * The parameters that govern the prediction.
   *
   * @param GoogleCloudAiplatformV1Tensor $parameters
   */
  public function setParameters(GoogleCloudAiplatformV1Tensor $parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return GoogleCloudAiplatformV1Tensor
   */
  public function getParameters()
  {
    return $this->parameters;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1DirectPredictResponse::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1DirectPredictResponse');
