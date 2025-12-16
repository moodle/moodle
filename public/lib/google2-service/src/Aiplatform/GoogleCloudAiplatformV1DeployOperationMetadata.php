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

class GoogleCloudAiplatformV1DeployOperationMetadata extends \Google\Model
{
  /**
   * Output only. The resource name of the Location to deploy the model in.
   * Format: `projects/{project}/locations/{location}`
   *
   * @var string
   */
  public $destination;
  protected $genericMetadataType = GoogleCloudAiplatformV1GenericOperationMetadata::class;
  protected $genericMetadataDataType = '';
  /**
   * Output only. The model id to be used at query time.
   *
   * @var string
   */
  public $modelId;
  /**
   * Output only. The project number where the deploy model request is sent.
   *
   * @var string
   */
  public $projectNumber;
  /**
   * Output only. The name of the model resource.
   *
   * @var string
   */
  public $publisherModel;

  /**
   * Output only. The resource name of the Location to deploy the model in.
   * Format: `projects/{project}/locations/{location}`
   *
   * @param string $destination
   */
  public function setDestination($destination)
  {
    $this->destination = $destination;
  }
  /**
   * @return string
   */
  public function getDestination()
  {
    return $this->destination;
  }
  /**
   * The operation generic information.
   *
   * @param GoogleCloudAiplatformV1GenericOperationMetadata $genericMetadata
   */
  public function setGenericMetadata(GoogleCloudAiplatformV1GenericOperationMetadata $genericMetadata)
  {
    $this->genericMetadata = $genericMetadata;
  }
  /**
   * @return GoogleCloudAiplatformV1GenericOperationMetadata
   */
  public function getGenericMetadata()
  {
    return $this->genericMetadata;
  }
  /**
   * Output only. The model id to be used at query time.
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
   * Output only. The project number where the deploy model request is sent.
   *
   * @param string $projectNumber
   */
  public function setProjectNumber($projectNumber)
  {
    $this->projectNumber = $projectNumber;
  }
  /**
   * @return string
   */
  public function getProjectNumber()
  {
    return $this->projectNumber;
  }
  /**
   * Output only. The name of the model resource.
   *
   * @param string $publisherModel
   */
  public function setPublisherModel($publisherModel)
  {
    $this->publisherModel = $publisherModel;
  }
  /**
   * @return string
   */
  public function getPublisherModel()
  {
    return $this->publisherModel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1DeployOperationMetadata::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1DeployOperationMetadata');
