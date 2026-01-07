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

class GoogleCloudAiplatformV1InvokeRequest extends \Google\Model
{
  /**
   * ID of the DeployedModel that serves the invoke request.
   *
   * @var string
   */
  public $deployedModelId;
  protected $httpBodyType = GoogleApiHttpBody::class;
  protected $httpBodyDataType = '';

  /**
   * ID of the DeployedModel that serves the invoke request.
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
   * The invoke method input. Supports HTTP headers and arbitrary data payload.
   *
   * @param GoogleApiHttpBody $httpBody
   */
  public function setHttpBody(GoogleApiHttpBody $httpBody)
  {
    $this->httpBody = $httpBody;
  }
  /**
   * @return GoogleApiHttpBody
   */
  public function getHttpBody()
  {
    return $this->httpBody;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1InvokeRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1InvokeRequest');
