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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1FlattenedApiVersionOperationDeploymentView extends \Google\Model
{
  protected $apiType = GoogleCloudApihubV1Api::class;
  protected $apiDataType = '';
  protected $apiOperationType = GoogleCloudApihubV1ApiOperation::class;
  protected $apiOperationDataType = '';
  protected $deploymentType = GoogleCloudApihubV1Deployment::class;
  protected $deploymentDataType = '';
  protected $versionType = GoogleCloudApihubV1Version::class;
  protected $versionDataType = '';

  /**
   * The API.
   *
   * @param GoogleCloudApihubV1Api $api
   */
  public function setApi(GoogleCloudApihubV1Api $api)
  {
    $this->api = $api;
  }
  /**
   * @return GoogleCloudApihubV1Api
   */
  public function getApi()
  {
    return $this->api;
  }
  /**
   * The API operation.
   *
   * @param GoogleCloudApihubV1ApiOperation $apiOperation
   */
  public function setApiOperation(GoogleCloudApihubV1ApiOperation $apiOperation)
  {
    $this->apiOperation = $apiOperation;
  }
  /**
   * @return GoogleCloudApihubV1ApiOperation
   */
  public function getApiOperation()
  {
    return $this->apiOperation;
  }
  /**
   * The deployment.
   *
   * @param GoogleCloudApihubV1Deployment $deployment
   */
  public function setDeployment(GoogleCloudApihubV1Deployment $deployment)
  {
    $this->deployment = $deployment;
  }
  /**
   * @return GoogleCloudApihubV1Deployment
   */
  public function getDeployment()
  {
    return $this->deployment;
  }
  /**
   * The version.
   *
   * @param GoogleCloudApihubV1Version $version
   */
  public function setVersion(GoogleCloudApihubV1Version $version)
  {
    $this->version = $version;
  }
  /**
   * @return GoogleCloudApihubV1Version
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1FlattenedApiVersionOperationDeploymentView::class, 'Google_Service_APIhub_GoogleCloudApihubV1FlattenedApiVersionOperationDeploymentView');
