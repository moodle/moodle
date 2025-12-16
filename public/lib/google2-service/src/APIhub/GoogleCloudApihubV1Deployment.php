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

class GoogleCloudApihubV1Deployment extends \Google\Collection
{
  protected $collection_key = 'sourceMetadata';
  /**
   * Output only. The API versions linked to this deployment. Note: A particular
   * deployment could be linked to multiple different API versions (of same or
   * different APIs).
   *
   * @var string[]
   */
  public $apiVersions;
  protected $attributesType = GoogleCloudApihubV1AttributeValues::class;
  protected $attributesDataType = 'map';
  /**
   * Output only. The time at which the deployment was created.
   *
   * @var string
   */
  public $createTime;
  protected $deploymentTypeType = GoogleCloudApihubV1AttributeValues::class;
  protected $deploymentTypeDataType = '';
  /**
   * Optional. The description of the deployment.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The display name of the deployment.
   *
   * @var string
   */
  public $displayName;
  protected $documentationType = GoogleCloudApihubV1Documentation::class;
  protected $documentationDataType = '';
  /**
   * Required. The endpoints at which this deployment resource is listening for
   * API requests. This could be a list of complete URIs, hostnames or an IP
   * addresses.
   *
   * @var string[]
   */
  public $endpoints;
  protected $environmentType = GoogleCloudApihubV1AttributeValues::class;
  protected $environmentDataType = '';
  protected $managementUrlType = GoogleCloudApihubV1AttributeValues::class;
  protected $managementUrlDataType = '';
  /**
   * Identifier. The name of the deployment. Format:
   * `projects/{project}/locations/{location}/deployments/{deployment}`
   *
   * @var string
   */
  public $name;
  /**
   * Required. The resource URI identifies the deployment within its gateway.
   * For Apigee gateways, its recommended to use the format:
   * organizations/{org}/environments/{env}/apis/{api}. For ex: if a proxy with
   * name `orders` is deployed in `staging` environment of `cymbal`
   * organization, the resource URI would be:
   * `organizations/cymbal/environments/staging/apis/orders`.
   *
   * @var string
   */
  public $resourceUri;
  protected $sloType = GoogleCloudApihubV1AttributeValues::class;
  protected $sloDataType = '';
  /**
   * Optional. The environment at source for the deployment. For example: prod,
   * dev, staging, etc.
   *
   * @var string
   */
  public $sourceEnvironment;
  protected $sourceMetadataType = GoogleCloudApihubV1SourceMetadata::class;
  protected $sourceMetadataDataType = 'array';
  /**
   * Optional. The project to which the deployment belongs. For GCP gateways,
   * this will refer to the project identifier. For others like Edge/OPDK, this
   * will refer to the org identifier.
   *
   * @var string
   */
  public $sourceProject;
  protected $sourceUriType = GoogleCloudApihubV1AttributeValues::class;
  protected $sourceUriDataType = '';
  /**
   * Output only. The time at which the deployment was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The API versions linked to this deployment. Note: A particular
   * deployment could be linked to multiple different API versions (of same or
   * different APIs).
   *
   * @param string[] $apiVersions
   */
  public function setApiVersions($apiVersions)
  {
    $this->apiVersions = $apiVersions;
  }
  /**
   * @return string[]
   */
  public function getApiVersions()
  {
    return $this->apiVersions;
  }
  /**
   * Optional. The list of user defined attributes associated with the
   * deployment resource. The key is the attribute name. It will be of the
   * format: `projects/{project}/locations/{location}/attributes/{attribute}`.
   * The value is the attribute values associated with the resource.
   *
   * @param GoogleCloudApihubV1AttributeValues[] $attributes
   */
  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return GoogleCloudApihubV1AttributeValues[]
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * Output only. The time at which the deployment was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Required. The type of deployment. This maps to the following system defined
   * attribute: `projects/{project}/locations/{location}/attributes/system-
   * deployment-type` attribute. The number of values for this attribute will be
   * based on the cardinality of the attribute. The same can be retrieved via
   * GetAttribute API. All values should be from the list of allowed values
   * defined for the attribute.
   *
   * @param GoogleCloudApihubV1AttributeValues $deploymentType
   */
  public function setDeploymentType(GoogleCloudApihubV1AttributeValues $deploymentType)
  {
    $this->deploymentType = $deploymentType;
  }
  /**
   * @return GoogleCloudApihubV1AttributeValues
   */
  public function getDeploymentType()
  {
    return $this->deploymentType;
  }
  /**
   * Optional. The description of the deployment.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. The display name of the deployment.
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
   * Optional. The documentation of the deployment.
   *
   * @param GoogleCloudApihubV1Documentation $documentation
   */
  public function setDocumentation(GoogleCloudApihubV1Documentation $documentation)
  {
    $this->documentation = $documentation;
  }
  /**
   * @return GoogleCloudApihubV1Documentation
   */
  public function getDocumentation()
  {
    return $this->documentation;
  }
  /**
   * Required. The endpoints at which this deployment resource is listening for
   * API requests. This could be a list of complete URIs, hostnames or an IP
   * addresses.
   *
   * @param string[] $endpoints
   */
  public function setEndpoints($endpoints)
  {
    $this->endpoints = $endpoints;
  }
  /**
   * @return string[]
   */
  public function getEndpoints()
  {
    return $this->endpoints;
  }
  /**
   * Optional. The environment mapping to this deployment. This maps to the
   * following system defined attribute:
   * `projects/{project}/locations/{location}/attributes/system-environment`
   * attribute. The number of values for this attribute will be based on the
   * cardinality of the attribute. The same can be retrieved via GetAttribute
   * API. All values should be from the list of allowed values defined for the
   * attribute.
   *
   * @param GoogleCloudApihubV1AttributeValues $environment
   */
  public function setEnvironment(GoogleCloudApihubV1AttributeValues $environment)
  {
    $this->environment = $environment;
  }
  /**
   * @return GoogleCloudApihubV1AttributeValues
   */
  public function getEnvironment()
  {
    return $this->environment;
  }
  /**
   * Optional. The uri where users can navigate to for the management of the
   * deployment. This maps to the following system defined attribute:
   * `projects/{project}/locations/{location}/attributes/system-management-url`
   * The number of values for this attribute will be based on the cardinality of
   * the attribute. The same can be retrieved via GetAttribute API. The value of
   * the attribute should be a valid URL.
   *
   * @param GoogleCloudApihubV1AttributeValues $managementUrl
   */
  public function setManagementUrl(GoogleCloudApihubV1AttributeValues $managementUrl)
  {
    $this->managementUrl = $managementUrl;
  }
  /**
   * @return GoogleCloudApihubV1AttributeValues
   */
  public function getManagementUrl()
  {
    return $this->managementUrl;
  }
  /**
   * Identifier. The name of the deployment. Format:
   * `projects/{project}/locations/{location}/deployments/{deployment}`
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
   * Required. The resource URI identifies the deployment within its gateway.
   * For Apigee gateways, its recommended to use the format:
   * organizations/{org}/environments/{env}/apis/{api}. For ex: if a proxy with
   * name `orders` is deployed in `staging` environment of `cymbal`
   * organization, the resource URI would be:
   * `organizations/cymbal/environments/staging/apis/orders`.
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
   * Optional. The SLO for this deployment. This maps to the following system
   * defined attribute:
   * `projects/{project}/locations/{location}/attributes/system-slo` attribute.
   * The number of values for this attribute will be based on the cardinality of
   * the attribute. The same can be retrieved via GetAttribute API. All values
   * should be from the list of allowed values defined for the attribute.
   *
   * @param GoogleCloudApihubV1AttributeValues $slo
   */
  public function setSlo(GoogleCloudApihubV1AttributeValues $slo)
  {
    $this->slo = $slo;
  }
  /**
   * @return GoogleCloudApihubV1AttributeValues
   */
  public function getSlo()
  {
    return $this->slo;
  }
  /**
   * Optional. The environment at source for the deployment. For example: prod,
   * dev, staging, etc.
   *
   * @param string $sourceEnvironment
   */
  public function setSourceEnvironment($sourceEnvironment)
  {
    $this->sourceEnvironment = $sourceEnvironment;
  }
  /**
   * @return string
   */
  public function getSourceEnvironment()
  {
    return $this->sourceEnvironment;
  }
  /**
   * Output only. The list of sources and metadata from the sources of the
   * deployment.
   *
   * @param GoogleCloudApihubV1SourceMetadata[] $sourceMetadata
   */
  public function setSourceMetadata($sourceMetadata)
  {
    $this->sourceMetadata = $sourceMetadata;
  }
  /**
   * @return GoogleCloudApihubV1SourceMetadata[]
   */
  public function getSourceMetadata()
  {
    return $this->sourceMetadata;
  }
  /**
   * Optional. The project to which the deployment belongs. For GCP gateways,
   * this will refer to the project identifier. For others like Edge/OPDK, this
   * will refer to the org identifier.
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
  /**
   * Optional. The uri where additional source specific information for this
   * deployment can be found. This maps to the following system defined
   * attribute: `projects/{project}/locations/{location}/attributes/system-
   * source-uri` The number of values for this attribute will be based on the
   * cardinality of the attribute. The same can be retrieved via GetAttribute
   * API. The value of the attribute should be a valid URI, and in case of Cloud
   * Storage URI, it should point to a Cloud Storage object, not a directory.
   *
   * @param GoogleCloudApihubV1AttributeValues $sourceUri
   */
  public function setSourceUri(GoogleCloudApihubV1AttributeValues $sourceUri)
  {
    $this->sourceUri = $sourceUri;
  }
  /**
   * @return GoogleCloudApihubV1AttributeValues
   */
  public function getSourceUri()
  {
    return $this->sourceUri;
  }
  /**
   * Output only. The time at which the deployment was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1Deployment::class, 'Google_Service_APIhub_GoogleCloudApihubV1Deployment');
