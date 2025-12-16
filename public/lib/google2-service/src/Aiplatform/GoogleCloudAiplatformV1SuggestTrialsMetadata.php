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

class GoogleCloudAiplatformV1SuggestTrialsMetadata extends \Google\Model
{
  /**
   * The identifier of the client that is requesting the suggestion. If multiple
   * SuggestTrialsRequests have the same `client_id`, the service will return
   * the identical suggested Trial if the Trial is pending, and provide a new
   * Trial if the last suggested Trial was completed.
   *
   * @var string
   */
  public $clientId;
  protected $genericMetadataType = GoogleCloudAiplatformV1GenericOperationMetadata::class;
  protected $genericMetadataDataType = '';

  /**
   * The identifier of the client that is requesting the suggestion. If multiple
   * SuggestTrialsRequests have the same `client_id`, the service will return
   * the identical suggested Trial if the Trial is pending, and provide a new
   * Trial if the last suggested Trial was completed.
   *
   * @param string $clientId
   */
  public function setClientId($clientId)
  {
    $this->clientId = $clientId;
  }
  /**
   * @return string
   */
  public function getClientId()
  {
    return $this->clientId;
  }
  /**
   * Operation metadata for suggesting Trials.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SuggestTrialsMetadata::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SuggestTrialsMetadata');
