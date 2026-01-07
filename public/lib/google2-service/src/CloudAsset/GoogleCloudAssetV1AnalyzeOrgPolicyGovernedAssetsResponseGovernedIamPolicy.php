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

class GoogleCloudAssetV1AnalyzeOrgPolicyGovernedAssetsResponseGovernedIamPolicy extends \Google\Collection
{
  protected $collection_key = 'folders';
  /**
   * The asset type of the
   * AnalyzeOrgPolicyGovernedAssetsResponse.GovernedIamPolicy.attached_resource.
   * Example: `cloudresourcemanager.googleapis.com/Project` See [Cloud Asset
   * Inventory Supported Asset Types](https://cloud.google.com/asset-
   * inventory/docs/supported-asset-types) for all supported asset types.
   *
   * @var string
   */
  public $assetType;
  /**
   * The full resource name of the resource on which this IAM policy is set.
   * Example: `//compute.googleapis.com/projects/my_project_123/zones/zone1/inst
   * ances/instance1`. See [Cloud Asset Inventory Resource Name
   * Format](https://cloud.google.com/asset-inventory/docs/resource-name-format)
   * for more information.
   *
   * @var string
   */
  public $attachedResource;
  /**
   * The folder(s) that this IAM policy belongs to, in the format of
   * folders/{FOLDER_NUMBER}. This field is available when the IAM policy
   * belongs (directly or cascadingly) to one or more folders.
   *
   * @var string[]
   */
  public $folders;
  /**
   * The organization that this IAM policy belongs to, in the format of
   * organizations/{ORGANIZATION_NUMBER}. This field is available when the IAM
   * policy belongs (directly or cascadingly) to an organization.
   *
   * @var string
   */
  public $organization;
  protected $policyType = Policy::class;
  protected $policyDataType = '';
  /**
   * The project that this IAM policy belongs to, in the format of
   * projects/{PROJECT_NUMBER}. This field is available when the IAM policy
   * belongs to a project.
   *
   * @var string
   */
  public $project;

  /**
   * The asset type of the
   * AnalyzeOrgPolicyGovernedAssetsResponse.GovernedIamPolicy.attached_resource.
   * Example: `cloudresourcemanager.googleapis.com/Project` See [Cloud Asset
   * Inventory Supported Asset Types](https://cloud.google.com/asset-
   * inventory/docs/supported-asset-types) for all supported asset types.
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
   * The full resource name of the resource on which this IAM policy is set.
   * Example: `//compute.googleapis.com/projects/my_project_123/zones/zone1/inst
   * ances/instance1`. See [Cloud Asset Inventory Resource Name
   * Format](https://cloud.google.com/asset-inventory/docs/resource-name-format)
   * for more information.
   *
   * @param string $attachedResource
   */
  public function setAttachedResource($attachedResource)
  {
    $this->attachedResource = $attachedResource;
  }
  /**
   * @return string
   */
  public function getAttachedResource()
  {
    return $this->attachedResource;
  }
  /**
   * The folder(s) that this IAM policy belongs to, in the format of
   * folders/{FOLDER_NUMBER}. This field is available when the IAM policy
   * belongs (directly or cascadingly) to one or more folders.
   *
   * @param string[] $folders
   */
  public function setFolders($folders)
  {
    $this->folders = $folders;
  }
  /**
   * @return string[]
   */
  public function getFolders()
  {
    return $this->folders;
  }
  /**
   * The organization that this IAM policy belongs to, in the format of
   * organizations/{ORGANIZATION_NUMBER}. This field is available when the IAM
   * policy belongs (directly or cascadingly) to an organization.
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
   * The IAM policy directly set on the given resource.
   *
   * @param Policy $policy
   */
  public function setPolicy(Policy $policy)
  {
    $this->policy = $policy;
  }
  /**
   * @return Policy
   */
  public function getPolicy()
  {
    return $this->policy;
  }
  /**
   * The project that this IAM policy belongs to, in the format of
   * projects/{PROJECT_NUMBER}. This field is available when the IAM policy
   * belongs to a project.
   *
   * @param string $project
   */
  public function setProject($project)
  {
    $this->project = $project;
  }
  /**
   * @return string
   */
  public function getProject()
  {
    return $this->project;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAssetV1AnalyzeOrgPolicyGovernedAssetsResponseGovernedIamPolicy::class, 'Google_Service_CloudAsset_GoogleCloudAssetV1AnalyzeOrgPolicyGovernedAssetsResponseGovernedIamPolicy');
