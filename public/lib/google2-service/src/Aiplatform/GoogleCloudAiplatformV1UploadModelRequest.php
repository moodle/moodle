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

class GoogleCloudAiplatformV1UploadModelRequest extends \Google\Model
{
  protected $modelType = GoogleCloudAiplatformV1Model::class;
  protected $modelDataType = '';
  /**
   * Optional. The ID to use for the uploaded Model, which will become the final
   * component of the model resource name. This value may be up to 63
   * characters, and valid characters are `[a-z0-9_-]`. The first character
   * cannot be a number or hyphen.
   *
   * @var string
   */
  public $modelId;
  /**
   * Optional. The resource name of the model into which to upload the version.
   * Only specify this field when uploading a new version.
   *
   * @var string
   */
  public $parentModel;
  /**
   * Optional. The user-provided custom service account to use to do the model
   * upload. If empty, [Vertex AI Service
   * Agent](https://cloud.google.com/vertex-ai/docs/general/access-
   * control#service-agents) will be used to access resources needed to upload
   * the model. This account must belong to the target project where the model
   * is uploaded to, i.e., the project specified in the `parent` field of this
   * request and have necessary read permissions (to Google Cloud Storage,
   * Artifact Registry, etc.).
   *
   * @var string
   */
  public $serviceAccount;

  /**
   * Required. The Model to create.
   *
   * @param GoogleCloudAiplatformV1Model $model
   */
  public function setModel(GoogleCloudAiplatformV1Model $model)
  {
    $this->model = $model;
  }
  /**
   * @return GoogleCloudAiplatformV1Model
   */
  public function getModel()
  {
    return $this->model;
  }
  /**
   * Optional. The ID to use for the uploaded Model, which will become the final
   * component of the model resource name. This value may be up to 63
   * characters, and valid characters are `[a-z0-9_-]`. The first character
   * cannot be a number or hyphen.
   *
   * @param string $modelId
   */
  public function setModelId($modelId)
  {
    $this->modelId = $modelId;
  }
  /**
   * @return string
   */
  public function getModelId()
  {
    return $this->modelId;
  }
  /**
   * Optional. The resource name of the model into which to upload the version.
   * Only specify this field when uploading a new version.
   *
   * @param string $parentModel
   */
  public function setParentModel($parentModel)
  {
    $this->parentModel = $parentModel;
  }
  /**
   * @return string
   */
  public function getParentModel()
  {
    return $this->parentModel;
  }
  /**
   * Optional. The user-provided custom service account to use to do the model
   * upload. If empty, [Vertex AI Service
   * Agent](https://cloud.google.com/vertex-ai/docs/general/access-
   * control#service-agents) will be used to access resources needed to upload
   * the model. This account must belong to the target project where the model
   * is uploaded to, i.e., the project specified in the `parent` field of this
   * request and have necessary read permissions (to Google Cloud Storage,
   * Artifact Registry, etc.).
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1UploadModelRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1UploadModelRequest');
