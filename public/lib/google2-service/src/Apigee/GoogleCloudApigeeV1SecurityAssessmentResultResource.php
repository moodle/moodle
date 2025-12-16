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

class GoogleCloudApigeeV1SecurityAssessmentResultResource extends \Google\Model
{
  /**
   * ResourceType not specified.
   */
  public const TYPE_RESOURCE_TYPE_UNSPECIFIED = 'RESOURCE_TYPE_UNSPECIFIED';
  /**
   * Resource is an Apigee Proxy.
   */
  public const TYPE_API_PROXY = 'API_PROXY';
  /**
   * Resource is an API Hub deployment.
   */
  public const TYPE_API_HUB_DEPLOYMENT = 'API_HUB_DEPLOYMENT';
  protected $apiHubDeploymentDetailsType = GoogleCloudApigeeV1SecurityAssessmentResultResourceApiHubDeploymentDetails::class;
  protected $apiHubDeploymentDetailsDataType = '';
  /**
   * Required. Name of this resource. For an Apigee API Proxy, this should be
   * the id of the API proxy. For an API Hub Deployment, this should be the id
   * of the deployment.
   *
   * @var string
   */
  public $name;
  /**
   * The revision id for the resource. In case of Apigee, this is proxy revision
   * id.
   *
   * @var string
   */
  public $resourceRevisionId;
  /**
   * Required. Type of this resource.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. Additional details for the API Hub deployment.
   *
   * @param GoogleCloudApigeeV1SecurityAssessmentResultResourceApiHubDeploymentDetails $apiHubDeploymentDetails
   */
  public function setApiHubDeploymentDetails(GoogleCloudApigeeV1SecurityAssessmentResultResourceApiHubDeploymentDetails $apiHubDeploymentDetails)
  {
    $this->apiHubDeploymentDetails = $apiHubDeploymentDetails;
  }
  /**
   * @return GoogleCloudApigeeV1SecurityAssessmentResultResourceApiHubDeploymentDetails
   */
  public function getApiHubDeploymentDetails()
  {
    return $this->apiHubDeploymentDetails;
  }
  /**
   * Required. Name of this resource. For an Apigee API Proxy, this should be
   * the id of the API proxy. For an API Hub Deployment, this should be the id
   * of the deployment.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The revision id for the resource. In case of Apigee, this is proxy revision
   * id.
   *
   * @param string $resourceRevisionId
   */
  public function setResourceRevisionId($resourceRevisionId)
  {
    $this->resourceRevisionId = $resourceRevisionId;
  }
  /**
   * @return string
   */
  public function getResourceRevisionId()
  {
    return $this->resourceRevisionId;
  }
  /**
   * Required. Type of this resource.
   *
   * Accepted values: RESOURCE_TYPE_UNSPECIFIED, API_PROXY, API_HUB_DEPLOYMENT
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1SecurityAssessmentResultResource::class, 'Google_Service_Apigee_GoogleCloudApigeeV1SecurityAssessmentResultResource');
