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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1SecurityAssessmentResultResourceApiHubDeploymentDetails extends \Google\Model
{
  /**
   * Gateway type is not specified.
   */
  public const GATEWAY_TYPE_API_HUB_GATEWAY_TYPE_UNSPECIFIED = 'API_HUB_GATEWAY_TYPE_UNSPECIFIED';
  /**
   * Gateway is Apigee X for API Hub.
   */
  public const GATEWAY_TYPE_APIGEE_X = 'APIGEE_X';
  /**
   * Gateway is Apigee Hybrid for API Hub.
   */
  public const GATEWAY_TYPE_APIGEE_HYBRID = 'APIGEE_HYBRID';
  /**
   * Gateway is Apigee Edge for API Hub.
   */
  public const GATEWAY_TYPE_APIGEE_EDGE = 'APIGEE_EDGE';
  /**
   * Gateway is Apigee OPDK for API Hub.
   */
  public const GATEWAY_TYPE_APIGEE_OPDK = 'APIGEE_OPDK';
  /**
   * The display name of the API Hub deployment.
   *
   * @var string
   */
  public $displayName;
  /**
   * The gateway for the API Hub deployment. Format: `projects/{project}/locatio
   * ns/{location}/plugins/{plugin}/instances/{instance}`
   *
   * @var string
   */
  public $gateway;
  /**
   * The gateway type for the API Hub deployment.
   *
   * @var string
   */
  public $gatewayType;
  /**
   * The resource uri for the API Hub deployment.
   *
   * @var string
   */
  public $resourceUri;
  /**
   * The source project for the API Hub deployment.
   *
   * @var string
   */
  public $sourceProject;

  /**
   * The display name of the API Hub deployment.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The gateway for the API Hub deployment. Format: `projects/{project}/locatio
   * ns/{location}/plugins/{plugin}/instances/{instance}`
   *
   * @param string $gateway
   */
  public function setGateway($gateway)
  {
    $this->gateway = $gateway;
  }
  /**
   * @return string
   */
  public function getGateway()
  {
    return $this->gateway;
  }
  /**
   * The gateway type for the API Hub deployment.
   *
   * Accepted values: API_HUB_GATEWAY_TYPE_UNSPECIFIED, APIGEE_X, APIGEE_HYBRID,
   * APIGEE_EDGE, APIGEE_OPDK
   *
   * @param self::GATEWAY_TYPE_* $gatewayType
   */
  public function setGatewayType($gatewayType)
  {
    $this->gatewayType = $gatewayType;
  }
  /**
   * @return self::GATEWAY_TYPE_*
   */
  public function getGatewayType()
  {
    return $this->gatewayType;
  }
  /**
   * The resource uri for the API Hub deployment.
   *
   * @param string $resourceUri
   */
  public function setResourceUri($resourceUri)
  {
    $this->resourceUri = $resourceUri;
  }
  /**
   * @return string
   */
  public function getResourceUri()
  {
    return $this->resourceUri;
  }
  /**
   * The source project for the API Hub deployment.
   *
   * @param string $sourceProject
   */
  public function setSourceProject($sourceProject)
  {
    $this->sourceProject = $sourceProject;
  }
  /**
   * @return string
   */
  public function getSourceProject()
  {
    return $this->sourceProject;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1SecurityAssessmentResultResourceApiHubDeploymentDetails::class, 'Google_Service_Apigee_GoogleCloudApigeeV1SecurityAssessmentResultResourceApiHubDeploymentDetails');
