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

namespace Google\Service\SecurityCommandCenter;

class GoogleCloudSecuritycenterV2Resource extends \Google\Model
{
  /**
   * The cloud provider is unspecified.
   */
  public const CLOUD_PROVIDER_CLOUD_PROVIDER_UNSPECIFIED = 'CLOUD_PROVIDER_UNSPECIFIED';
  /**
   * The cloud provider is Google Cloud.
   */
  public const CLOUD_PROVIDER_GOOGLE_CLOUD_PLATFORM = 'GOOGLE_CLOUD_PLATFORM';
  /**
   * The cloud provider is Amazon Web Services.
   */
  public const CLOUD_PROVIDER_AMAZON_WEB_SERVICES = 'AMAZON_WEB_SERVICES';
  /**
   * The cloud provider is Microsoft Azure.
   */
  public const CLOUD_PROVIDER_MICROSOFT_AZURE = 'MICROSOFT_AZURE';
  protected $applicationType = GoogleCloudSecuritycenterV2ResourceApplication::class;
  protected $applicationDataType = '';
  protected $awsMetadataType = GoogleCloudSecuritycenterV2AwsMetadata::class;
  protected $awsMetadataDataType = '';
  protected $azureMetadataType = GoogleCloudSecuritycenterV2AzureMetadata::class;
  protected $azureMetadataDataType = '';
  /**
   * Indicates which cloud provider the finding is from.
   *
   * @var string
   */
  public $cloudProvider;
  /**
   * The human readable name of the resource.
   *
   * @var string
   */
  public $displayName;
  protected $gcpMetadataType = GcpMetadata::class;
  protected $gcpMetadataDataType = '';
  /**
   * The region or location of the service (if applicable).
   *
   * @var string
   */
  public $location;
  /**
   * The full resource name of the resource. See:
   * https://cloud.google.com/apis/design/resource_names#full_resource_name
   *
   * @var string
   */
  public $name;
  protected $resourcePathType = GoogleCloudSecuritycenterV2ResourcePath::class;
  protected $resourcePathDataType = '';
  /**
   * A string representation of the resource path. For Google Cloud, it has the
   * format of `organizations/{organization_id}/folders/{folder_id}/folders/{fol
   * der_id}/projects/{project_id}` where there can be any number of folders.
   * For AWS, it has the format of `org/{organization_id}/ou/{organizational_uni
   * t_id}/ou/{organizational_unit_id}/account/{account_id}` where there can be
   * any number of organizational units. For Azure, it has the format of `mg/{ma
   * nagement_group_id}/mg/{management_group_id}/subscription/{subscription_id}/
   * rg/{resource_group_name}` where there can be any number of management
   * groups.
   *
   * @var string
   */
  public $resourcePathString;
  /**
   * The service or resource provider associated with the resource.
   *
   * @var string
   */
  public $service;
  /**
   * The full resource type of the resource.
   *
   * @var string
   */
  public $type;

  /**
   * The App Hub application this resource belongs to.
   *
   * @param GoogleCloudSecuritycenterV2ResourceApplication $application
   */
  public function setApplication(GoogleCloudSecuritycenterV2ResourceApplication $application)
  {
    $this->application = $application;
  }
  /**
   * @return GoogleCloudSecuritycenterV2ResourceApplication
   */
  public function getApplication()
  {
    return $this->application;
  }
  /**
   * The AWS metadata associated with the finding.
   *
   * @param GoogleCloudSecuritycenterV2AwsMetadata $awsMetadata
   */
  public function setAwsMetadata(GoogleCloudSecuritycenterV2AwsMetadata $awsMetadata)
  {
    $this->awsMetadata = $awsMetadata;
  }
  /**
   * @return GoogleCloudSecuritycenterV2AwsMetadata
   */
  public function getAwsMetadata()
  {
    return $this->awsMetadata;
  }
  /**
   * The Azure metadata associated with the finding.
   *
   * @param GoogleCloudSecuritycenterV2AzureMetadata $azureMetadata
   */
  public function setAzureMetadata(GoogleCloudSecuritycenterV2AzureMetadata $azureMetadata)
  {
    $this->azureMetadata = $azureMetadata;
  }
  /**
   * @return GoogleCloudSecuritycenterV2AzureMetadata
   */
  public function getAzureMetadata()
  {
    return $this->azureMetadata;
  }
  /**
   * Indicates which cloud provider the finding is from.
   *
   * Accepted values: CLOUD_PROVIDER_UNSPECIFIED, GOOGLE_CLOUD_PLATFORM,
   * AMAZON_WEB_SERVICES, MICROSOFT_AZURE
   *
   * @param self::CLOUD_PROVIDER_* $cloudProvider
   */
  public function setCloudProvider($cloudProvider)
  {
    $this->cloudProvider = $cloudProvider;
  }
  /**
   * @return self::CLOUD_PROVIDER_*
   */
  public function getCloudProvider()
  {
    return $this->cloudProvider;
  }
  /**
   * The human readable name of the resource.
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
   * The Google Cloud metadata associated with the finding.
   *
   * @param GcpMetadata $gcpMetadata
   */
  public function setGcpMetadata(GcpMetadata $gcpMetadata)
  {
    $this->gcpMetadata = $gcpMetadata;
  }
  /**
   * @return GcpMetadata
   */
  public function getGcpMetadata()
  {
    return $this->gcpMetadata;
  }
  /**
   * The region or location of the service (if applicable).
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * The full resource name of the resource. See:
   * https://cloud.google.com/apis/design/resource_names#full_resource_name
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
   * Provides the path to the resource within the resource hierarchy.
   *
   * @param GoogleCloudSecuritycenterV2ResourcePath $resourcePath
   */
  public function setResourcePath(GoogleCloudSecuritycenterV2ResourcePath $resourcePath)
  {
    $this->resourcePath = $resourcePath;
  }
  /**
   * @return GoogleCloudSecuritycenterV2ResourcePath
   */
  public function getResourcePath()
  {
    return $this->resourcePath;
  }
  /**
   * A string representation of the resource path. For Google Cloud, it has the
   * format of `organizations/{organization_id}/folders/{folder_id}/folders/{fol
   * der_id}/projects/{project_id}` where there can be any number of folders.
   * For AWS, it has the format of `org/{organization_id}/ou/{organizational_uni
   * t_id}/ou/{organizational_unit_id}/account/{account_id}` where there can be
   * any number of organizational units. For Azure, it has the format of `mg/{ma
   * nagement_group_id}/mg/{management_group_id}/subscription/{subscription_id}/
   * rg/{resource_group_name}` where there can be any number of management
   * groups.
   *
   * @param string $resourcePathString
   */
  public function setResourcePathString($resourcePathString)
  {
    $this->resourcePathString = $resourcePathString;
  }
  /**
   * @return string
   */
  public function getResourcePathString()
  {
    return $this->resourcePathString;
  }
  /**
   * The service or resource provider associated with the resource.
   *
   * @param string $service
   */
  public function setService($service)
  {
    $this->service = $service;
  }
  /**
   * @return string
   */
  public function getService()
  {
    return $this->service;
  }
  /**
   * The full resource type of the resource.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV2Resource::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2Resource');
