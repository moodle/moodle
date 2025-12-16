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

class GoogleCloudAiplatformV1DeployRequestModelConfig extends \Google\Model
{
  /**
   * Optional. Whether the user accepts the End User License Agreement (EULA)
   * for the model.
   *
   * @var bool
   */
  public $acceptEula;
  protected $containerSpecType = GoogleCloudAiplatformV1ModelContainerSpec::class;
  protected $containerSpecDataType = '';
  /**
   * Optional. The Hugging Face read access token used to access the model
   * artifacts of gated models.
   *
   * @var string
   */
  public $huggingFaceAccessToken;
  /**
   * Optional. If true, the model will deploy with a cached version instead of
   * directly downloading the model artifacts from Hugging Face. This is
   * suitable for VPC-SC users with limited internet access.
   *
   * @var bool
   */
  public $huggingFaceCacheEnabled;
  /**
   * Optional. The user-specified display name of the uploaded model. If not
   * set, a default name will be used.
   *
   * @var string
   */
  public $modelDisplayName;
  /**
   * Optional. The ID to use for the uploaded Model, which will become the final
   * component of the model resource name. When not provided, Vertex AI will
   * generate a value for this ID. When Model Registry model is provided, this
   * field will be ignored. This value may be up to 63 characters, and valid
   * characters are `[a-z0-9_-]`. The first character cannot be a number or
   * hyphen.
   *
   * @var string
   */
  public $modelUserId;

  /**
   * Optional. Whether the user accepts the End User License Agreement (EULA)
   * for the model.
   *
   * @param bool $acceptEula
   */
  public function setAcceptEula($acceptEula)
  {
    $this->acceptEula = $acceptEula;
  }
  /**
   * @return bool
   */
  public function getAcceptEula()
  {
    return $this->acceptEula;
  }
  /**
   * Optional. The specification of the container that is to be used when
   * deploying. If not set, the default container spec will be used.
   *
   * @param GoogleCloudAiplatformV1ModelContainerSpec $containerSpec
   */
  public function setContainerSpec(GoogleCloudAiplatformV1ModelContainerSpec $containerSpec)
  {
    $this->containerSpec = $containerSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1ModelContainerSpec
   */
  public function getContainerSpec()
  {
    return $this->containerSpec;
  }
  /**
   * Optional. The Hugging Face read access token used to access the model
   * artifacts of gated models.
   *
   * @param string $huggingFaceAccessToken
   */
  public function setHuggingFaceAccessToken($huggingFaceAccessToken)
  {
    $this->huggingFaceAccessToken = $huggingFaceAccessToken;
  }
  /**
   * @return string
   */
  public function getHuggingFaceAccessToken()
  {
    return $this->huggingFaceAccessToken;
  }
  /**
   * Optional. If true, the model will deploy with a cached version instead of
   * directly downloading the model artifacts from Hugging Face. This is
   * suitable for VPC-SC users with limited internet access.
   *
   * @param bool $huggingFaceCacheEnabled
   */
  public function setHuggingFaceCacheEnabled($huggingFaceCacheEnabled)
  {
    $this->huggingFaceCacheEnabled = $huggingFaceCacheEnabled;
  }
  /**
   * @return bool
   */
  public function getHuggingFaceCacheEnabled()
  {
    return $this->huggingFaceCacheEnabled;
  }
  /**
   * Optional. The user-specified display name of the uploaded model. If not
   * set, a default name will be used.
   *
   * @param string $modelDisplayName
   */
  public function setModelDisplayName($modelDisplayName)
  {
    $this->modelDisplayName = $modelDisplayName;
  }
  /**
   * @return string
   */
  public function getModelDisplayName()
  {
    return $this->modelDisplayName;
  }
  /**
   * Optional. The ID to use for the uploaded Model, which will become the final
   * component of the model resource name. When not provided, Vertex AI will
   * generate a value for this ID. When Model Registry model is provided, this
   * field will be ignored. This value may be up to 63 characters, and valid
   * characters are `[a-z0-9_-]`. The first character cannot be a number or
   * hyphen.
   *
   * @param string $modelUserId
   */
  public function setModelUserId($modelUserId)
  {
    $this->modelUserId = $modelUserId;
  }
  /**
   * @return string
   */
  public function getModelUserId()
  {
    return $this->modelUserId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1DeployRequestModelConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1DeployRequestModelConfig');
