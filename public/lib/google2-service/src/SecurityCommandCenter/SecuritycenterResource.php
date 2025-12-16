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

class SecuritycenterResource extends \Google\Collection
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
  protected $collection_key = 'folders';
  protected $applicationType = GoogleCloudSecuritycenterV1ResourceApplication::class;
  protected $applicationDataType = '';
  protected $awsMetadataType = AwsMetadata::class;
  protected $awsMetadataDataType = '';
  protected $azureMetadataType = AzureMetadata::class;
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
  protected $foldersType = Folder::class;
  protected $foldersDataType = 'array';
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
  /**
   * Indicates which organization / tenant the finding is for.
   *
   * @var string
   */
  public $organization;
  /**
   * The human readable name of resource's parent.
   *
   * @var string
   */
  public $parentDisplayName;
  /**
   * The full resource name of resource's parent.
   *
   * @var string
   */
  public $parentName;
  /**
   * The project ID that the resource belongs to.
   *
   * @var string
   */
  public $projectDisplayName;
  /**
   * The full resource name of project that the resource belongs to.
   *
   * @var string
   */
  public $projectName;
  protected $resourcePathType = ResourcePath::class;
  protected $resourcePathDataType = '';
  /**
   * A string representation of the resource path. For Google Cloud, it has the
   * format of `org/{organization_id}/folder/{folder_id}/folder/{folder_id}/proj
   * ect/{project_id}` where there can be any number of folders. For AWS, it has
   * the format of `org/{organization_id}/ou/{organizational_unit_id}/ou/{organi
   * zational_unit_id}/account/{account_id}` where there can be any number of
   * organizational units. For Azure, it has the format of `mg/{management_group
   * _id}/mg/{management_group_id}/subscription/{subscription_id}/rg/{resource_g
   * roup_name}` where there can be any number of management groups.
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
   * @param GoogleCloudSecuritycenterV1ResourceApplication $application
   */
  public function setApplication(GoogleCloudSecuritycenterV1ResourceApplication $application)
  {
    $this->application = $application;
  }
  /**
   * @return GoogleCloudSecuritycenterV1ResourceApplication
   */
  public function getApplication()
  {
    return $this->application;
  }
  /**
   * The AWS metadata associated with the finding.
   *
   * @param AwsMetadata $awsMetadata
   */
  public function setAwsMetadata(AwsMetadata $awsMetadata)
  {
    $this->awsMetadata = $awsMetadata;
  }
  /**
   * @return AwsMetadata
   */
  public function getAwsMetadata()
  {
    return $this->awsMetadata;
  }
  /**
   * The Azure metadata associated with the finding.
   *
   * @param AzureMetadata $azureMetadata
   */
  public function setAzureMetadata(AzureMetadata $azureMetadata)
  {
    $this->azureMetadata = $azureMetadata;
  }
  /**
   * @return AzureMetadata
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
   * Contains a Folder message for each folder in the assets ancestry. The first
   * folder is the deepest nested folder, and the last folder is the folder
   * directly under the Organization.
   *
   * @param Folder[] $folders
   */
  public function setFolders($folders)
  {
    $this->folders = $folders;
  }
  /**
   * @return Folder[]
   */
  public function getFolders()
  {
    return $this->folders;
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
   * Indicates which organization / tenant the finding is for.
   *
   * @param string $organization
   */
  public function setOrganization($organization)
  {
    $this->organization = $organization;
  }
  /**
   * @return string
   */
  public function getOrganization()
  {
    return $this->organization;
  }
  /**
   * The human readable name of resource's parent.
   *
   * @param string $parentDisplayName
   */
  public function setParentDisplayName($parentDisplayName)
  {
    $this->parentDisplayName = $parentDisplayName;
  }
  /**
   * @return string
   */
  public function getParentDisplayName()
  {
    return $this->parentDisplayName;
  }
  /**
   * The full resource name of resource's parent.
   *
   * @param string $parentName
   */
  public function setParentName($parentName)
  {
    $this->parentName = $parentName;
  }
  /**
   * @return string
   */
  public function getParentName()
  {
    return $this->parentName;
  }
  /**
   * The project ID that the resource belongs to.
   *
   * @param string $projectDisplayName
   */
  public function setProjectDisplayName($projectDisplayName)
  {
    $this->projectDisplayName = $projectDisplayName;
  }
  /**
   * @return string
   */
  public function getProjectDisplayName()
  {
    return $this->projectDisplayName;
  }
  /**
   * The full resource name of project that the resource belongs to.
   *
   * @param string $projectName
   */
  public function setProjectName($projectName)
  {
    $this->projectName = $projectName;
  }
  /**
   * @return string
   */
  public function getProjectName()
  {
    return $this->projectName;
  }
  /**
   * Provides the path to the resource within the resource hierarchy.
   *
   * @param ResourcePath $resourcePath
   */
  public function setResourcePath(ResourcePath $resourcePath)
  {
    $this->resourcePath = $resourcePath;
  }
  /**
   * @return ResourcePath
   */
  public function getResourcePath()
  {
    return $this->resourcePath;
  }
  /**
   * A string representation of the resource path. For Google Cloud, it has the
   * format of `org/{organization_id}/folder/{folder_id}/folder/{folder_id}/proj
   * ect/{project_id}` where there can be any number of folders. For AWS, it has
   * the format of `org/{organization_id}/ou/{organizational_unit_id}/ou/{organi
   * zational_unit_id}/account/{account_id}` where there can be any number of
   * organizational units. For Azure, it has the format of `mg/{management_group
   * _id}/mg/{management_group_id}/subscription/{subscription_id}/rg/{resource_g
   * roup_name}` where there can be any number of management groups.
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
class_alias(SecuritycenterResource::class, 'Google_Service_SecurityCommandCenter_SecuritycenterResource');
