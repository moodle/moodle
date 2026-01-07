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

namespace Google\Service\CloudAsset;

class Asset extends \Google\Collection
{
  protected $collection_key = 'orgPolicy';
  protected $accessLevelType = GoogleIdentityAccesscontextmanagerV1AccessLevel::class;
  protected $accessLevelDataType = '';
  protected $accessPolicyType = GoogleIdentityAccesscontextmanagerV1AccessPolicy::class;
  protected $accessPolicyDataType = '';
  /**
   * The ancestry path of an asset in Google Cloud [resource
   * hierarchy](https://cloud.google.com/resource-manager/docs/cloud-platform-
   * resource-hierarchy), represented as a list of relative resource names. An
   * ancestry path starts with the closest ancestor in the hierarchy and ends at
   * root. If the asset is a project, folder, or organization, the ancestry path
   * starts from the asset itself. Example: `["projects/123456789",
   * "folders/5432", "organizations/1234"]`
   *
   * @var string[]
   */
  public $ancestors;
  protected $assetExceptionsType = AssetException::class;
  protected $assetExceptionsDataType = 'array';
  /**
   * The type of the asset. Example: `compute.googleapis.com/Disk` See
   * [Supported asset types](https://cloud.google.com/asset-
   * inventory/docs/supported-asset-types) for more information.
   *
   * @var string
   */
  public $assetType;
  protected $iamPolicyType = Policy::class;
  protected $iamPolicyDataType = '';
  /**
   * The full name of the asset. Example: `//compute.googleapis.com/projects/my_
   * project_123/zones/zone1/instances/instance1` See [Resource names](https://c
   * loud.google.com/apis/design/resource_names#full_resource_name) for more
   * information.
   *
   * @var string
   */
  public $name;
  protected $orgPolicyType = GoogleCloudOrgpolicyV1Policy::class;
  protected $orgPolicyDataType = 'array';
  protected $osInventoryType = Inventory::class;
  protected $osInventoryDataType = '';
  protected $relatedAssetType = RelatedAsset::class;
  protected $relatedAssetDataType = '';
  protected $relatedAssetsType = RelatedAssets::class;
  protected $relatedAssetsDataType = '';
  protected $resourceType = CloudassetResource::class;
  protected $resourceDataType = '';
  protected $servicePerimeterType = GoogleIdentityAccesscontextmanagerV1ServicePerimeter::class;
  protected $servicePerimeterDataType = '';
  /**
   * The last update timestamp of an asset. update_time is updated when
   * create/update/delete operation is performed.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Also refer to the [access level user
   * guide](https://cloud.google.com/access-context-
   * manager/docs/overview#access-levels).
   *
   * @param GoogleIdentityAccesscontextmanagerV1AccessLevel $accessLevel
   */
  public function setAccessLevel(GoogleIdentityAccesscontextmanagerV1AccessLevel $accessLevel)
  {
    $this->accessLevel = $accessLevel;
  }
  /**
   * @return GoogleIdentityAccesscontextmanagerV1AccessLevel
   */
  public function getAccessLevel()
  {
    return $this->accessLevel;
  }
  /**
   * Also refer to the [access policy user
   * guide](https://cloud.google.com/access-context-
   * manager/docs/overview#access-policies).
   *
   * @param GoogleIdentityAccesscontextmanagerV1AccessPolicy $accessPolicy
   */
  public function setAccessPolicy(GoogleIdentityAccesscontextmanagerV1AccessPolicy $accessPolicy)
  {
    $this->accessPolicy = $accessPolicy;
  }
  /**
   * @return GoogleIdentityAccesscontextmanagerV1AccessPolicy
   */
  public function getAccessPolicy()
  {
    return $this->accessPolicy;
  }
  /**
   * The ancestry path of an asset in Google Cloud [resource
   * hierarchy](https://cloud.google.com/resource-manager/docs/cloud-platform-
   * resource-hierarchy), represented as a list of relative resource names. An
   * ancestry path starts with the closest ancestor in the hierarchy and ends at
   * root. If the asset is a project, folder, or organization, the ancestry path
   * starts from the asset itself. Example: `["projects/123456789",
   * "folders/5432", "organizations/1234"]`
   *
   * @param string[] $ancestors
   */
  public function setAncestors($ancestors)
  {
    $this->ancestors = $ancestors;
  }
  /**
   * @return string[]
   */
  public function getAncestors()
  {
    return $this->ancestors;
  }
  /**
   * The exceptions of a resource.
   *
   * @param AssetException[] $assetExceptions
   */
  public function setAssetExceptions($assetExceptions)
  {
    $this->assetExceptions = $assetExceptions;
  }
  /**
   * @return AssetException[]
   */
  public function getAssetExceptions()
  {
    return $this->assetExceptions;
  }
  /**
   * The type of the asset. Example: `compute.googleapis.com/Disk` See
   * [Supported asset types](https://cloud.google.com/asset-
   * inventory/docs/supported-asset-types) for more information.
   *
   * @param string $assetType
   */
  public function setAssetType($assetType)
  {
    $this->assetType = $assetType;
  }
  /**
   * @return string
   */
  public function getAssetType()
  {
    return $this->assetType;
  }
  /**
   * A representation of the IAM policy set on a Google Cloud resource. There
   * can be a maximum of one IAM policy set on any given resource. In addition,
   * IAM policies inherit their granted access scope from any policies set on
   * parent resources in the resource hierarchy. Therefore, the effectively
   * policy is the union of both the policy set on this resource and each policy
   * set on all of the resource's ancestry resource levels in the hierarchy. See
   * [this topic](https://cloud.google.com/iam/help/allow-policies/inheritance)
   * for more information.
   *
   * @param Policy $iamPolicy
   */
  public function setIamPolicy(Policy $iamPolicy)
  {
    $this->iamPolicy = $iamPolicy;
  }
  /**
   * @return Policy
   */
  public function getIamPolicy()
  {
    return $this->iamPolicy;
  }
  /**
   * The full name of the asset. Example: `//compute.googleapis.com/projects/my_
   * project_123/zones/zone1/instances/instance1` See [Resource names](https://c
   * loud.google.com/apis/design/resource_names#full_resource_name) for more
   * information.
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
   * A representation of an [organization
   * policy](https://cloud.google.com/resource-manager/docs/organization-
   * policy/overview#organization_policy). There can be more than one
   * organization policy with different constraints set on a given resource.
   *
   * @param GoogleCloudOrgpolicyV1Policy[] $orgPolicy
   */
  public function setOrgPolicy($orgPolicy)
  {
    $this->orgPolicy = $orgPolicy;
  }
  /**
   * @return GoogleCloudOrgpolicyV1Policy[]
   */
  public function getOrgPolicy()
  {
    return $this->orgPolicy;
  }
  /**
   * A representation of runtime OS Inventory information. See [this
   * topic](https://cloud.google.com/compute/docs/instances/os-inventory-
   * management) for more information.
   *
   * @param Inventory $osInventory
   */
  public function setOsInventory(Inventory $osInventory)
  {
    $this->osInventory = $osInventory;
  }
  /**
   * @return Inventory
   */
  public function getOsInventory()
  {
    return $this->osInventory;
  }
  /**
   * One related asset of the current asset.
   *
   * @param RelatedAsset $relatedAsset
   */
  public function setRelatedAsset(RelatedAsset $relatedAsset)
  {
    $this->relatedAsset = $relatedAsset;
  }
  /**
   * @return RelatedAsset
   */
  public function getRelatedAsset()
  {
    return $this->relatedAsset;
  }
  /**
   * DEPRECATED. This field only presents for the purpose of backward-
   * compatibility. The server will never generate responses with this field.
   * The related assets of the asset of one relationship type. One asset only
   * represents one type of relationship.
   *
   * @deprecated
   * @param RelatedAssets $relatedAssets
   */
  public function setRelatedAssets(RelatedAssets $relatedAssets)
  {
    $this->relatedAssets = $relatedAssets;
  }
  /**
   * @deprecated
   * @return RelatedAssets
   */
  public function getRelatedAssets()
  {
    return $this->relatedAssets;
  }
  /**
   * A representation of the resource.
   *
   * @param CloudassetResource $resource
   */
  public function setResource(CloudassetResource $resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return CloudassetResource
   */
  public function getResource()
  {
    return $this->resource;
  }
  /**
   * Also refer to the [service perimeter user
   * guide](https://cloud.google.com/vpc-service-controls/docs/overview).
   *
   * @param GoogleIdentityAccesscontextmanagerV1ServicePerimeter $servicePerimeter
   */
  public function setServicePerimeter(GoogleIdentityAccesscontextmanagerV1ServicePerimeter $servicePerimeter)
  {
    $this->servicePerimeter = $servicePerimeter;
  }
  /**
   * @return GoogleIdentityAccesscontextmanagerV1ServicePerimeter
   */
  public function getServicePerimeter()
  {
    return $this->servicePerimeter;
  }
  /**
   * The last update timestamp of an asset. update_time is updated when
   * create/update/delete operation is performed.
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
class_alias(Asset::class, 'Google_Service_CloudAsset_Asset');
