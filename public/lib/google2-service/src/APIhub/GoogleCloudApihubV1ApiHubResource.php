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

class GoogleCloudApihubV1ApiHubResource extends \Google\Model
{
  protected $apiType = GoogleCloudApihubV1Api::class;
  protected $apiDataType = '';
  protected $definitionType = GoogleCloudApihubV1Definition::class;
  protected $definitionDataType = '';
  protected $deploymentType = GoogleCloudApihubV1Deployment::class;
  protected $deploymentDataType = '';
  protected $operationType = GoogleCloudApihubV1ApiOperation::class;
  protected $operationDataType = '';
  protected $specType = GoogleCloudApihubV1Spec::class;
  protected $specDataType = '';
  protected $versionType = GoogleCloudApihubV1Version::class;
  protected $versionDataType = '';

  /**
   * This represents Api resource in search results. Only name, display_name,
   * description and owner fields are populated in search results.
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
   * This represents Definition resource in search results. Only name field is
   * populated in search results.
   *
   * @param GoogleCloudApihubV1Definition $definition
   */
  public function setDefinition(GoogleCloudApihubV1Definition $definition)
  {
    $this->definition = $definition;
  }
  /**
   * @return GoogleCloudApihubV1Definition
   */
  public function getDefinition()
  {
    return $this->definition;
  }
  /**
   * This represents Deployment resource in search results. Only name,
   * display_name, description, deployment_type and api_versions fields are
   * populated in search results.
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
   * This represents ApiOperation resource in search results. Only name,
   * description, spec and details fields are populated in search results.
   *
   * @param GoogleCloudApihubV1ApiOperation $operation
   */
  public function setOperation(GoogleCloudApihubV1ApiOperation $operation)
  {
    $this->operation = $operation;
  }
  /**
   * @return GoogleCloudApihubV1ApiOperation
   */
  public function getOperation()
  {
    return $this->operation;
  }
  /**
   * This represents Spec resource in search results. Only name, display_name,
   * description, spec_type and documentation fields are populated in search
   * results.
   *
   * @param GoogleCloudApihubV1Spec $spec
   */
  public function setSpec(GoogleCloudApihubV1Spec $spec)
  {
    $this->spec = $spec;
  }
  /**
   * @return GoogleCloudApihubV1Spec
   */
  public function getSpec()
  {
    return $this->spec;
  }
  /**
   * This represents Version resource in search results. Only name,
   * display_name, description, lifecycle, compliance and accreditation fields
   * are populated in search results.
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
class_alias(GoogleCloudApihubV1ApiHubResource::class, 'Google_Service_APIhub_GoogleCloudApihubV1ApiHubResource');
