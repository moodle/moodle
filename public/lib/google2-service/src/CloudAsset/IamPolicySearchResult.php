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

class IamPolicySearchResult extends \Google\Collection
{
  protected $collection_key = 'folders';
  /**
   * The type of the resource associated with this IAM policy. Example:
   * `compute.googleapis.com/Disk`. To search against the `asset_type`: *
   * specify the `asset_types` field in your search request.
   *
   * @var string
   */
  public $assetType;
  protected $explanationType = Explanation::class;
  protected $explanationDataType = '';
  /**
   * The folder(s) that the IAM policy belongs to, in the form of
   * folders/{FOLDER_NUMBER}. This field is available when the IAM policy
   * belongs to one or more folders. To search against `folders`: * use a field
   * query. Example: `folders:(123 OR 456)` * use a free text query. Example:
   * `123` * specify the `scope` field as this folder in your search request.
   *
   * @var string[]
   */
  public $folders;
  /**
   * The organization that the IAM policy belongs to, in the form of
   * organizations/{ORGANIZATION_NUMBER}. This field is available when the IAM
   * policy belongs to an organization. To search against `organization`: * use
   * a field query. Example: `organization:123` * use a free text query.
   * Example: `123` * specify the `scope` field as this organization in your
   * search request.
   *
   * @var string
   */
  public $organization;
  protected $policyType = Policy::class;
  protected $policyDataType = '';
  /**
   * The project that the associated Google Cloud resource belongs to, in the
   * form of projects/{PROJECT_NUMBER}. If an IAM policy is set on a resource
   * (like VM instance, Cloud Storage bucket), the project field will indicate
   * the project that contains the resource. If an IAM policy is set on a folder
   * or organization, this field will be empty. To search against the `project`:
   * * specify the `scope` field as this project in your search request.
   *
   * @var string
   */
  public $project;
  /**
   * The full resource name of the resource associated with this IAM policy.
   * Example: `//compute.googleapis.com/projects/my_project_123/zones/zone1/inst
   * ances/instance1`. See [Cloud Asset Inventory Resource Name
   * Format](https://cloud.google.com/asset-inventory/docs/resource-name-format)
   * for more information. To search against the `resource`: * use a field
   * query. Example: `resource:organizations/123`
   *
   * @var string
   */
  public $resource;

  /**
   * The type of the resource associated with this IAM policy. Example:
   * `compute.googleapis.com/Disk`. To search against the `asset_type`: *
   * specify the `asset_types` field in your search request.
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
   * Explanation about the IAM policy search result. It contains additional
   * information to explain why the search result matches the query.
   *
   * @param Explanation $explanation
   */
  public function setExplanation(Explanation $explanation)
  {
    $this->explanation = $explanation;
  }
  /**
   * @return Explanation
   */
  public function getExplanation()
  {
    return $this->explanation;
  }
  /**
   * The folder(s) that the IAM policy belongs to, in the form of
   * folders/{FOLDER_NUMBER}. This field is available when the IAM policy
   * belongs to one or more folders. To search against `folders`: * use a field
   * query. Example: `folders:(123 OR 456)` * use a free text query. Example:
   * `123` * specify the `scope` field as this folder in your search request.
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
   * The organization that the IAM policy belongs to, in the form of
   * organizations/{ORGANIZATION_NUMBER}. This field is available when the IAM
   * policy belongs to an organization. To search against `organization`: * use
   * a field query. Example: `organization:123` * use a free text query.
   * Example: `123` * specify the `scope` field as this organization in your
   * search request.
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
   * The IAM policy directly set on the given resource. Note that the original
   * IAM policy can contain multiple bindings. This only contains the bindings
   * that match the given query. For queries that don't contain a constrain on
   * policies (e.g., an empty query), this contains all the bindings. To search
   * against the `policy` bindings: * use a field query: - query by the policy
   * contained members. Example: `policy:amy@gmail.com` - query by the policy
   * contained roles. Example: `policy:roles/compute.admin` - query by the
   * policy contained roles' included permissions. Example:
   * `policy.role.permissions:compute.instances.create`
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
   * The project that the associated Google Cloud resource belongs to, in the
   * form of projects/{PROJECT_NUMBER}. If an IAM policy is set on a resource
   * (like VM instance, Cloud Storage bucket), the project field will indicate
   * the project that contains the resource. If an IAM policy is set on a folder
   * or organization, this field will be empty. To search against the `project`:
   * * specify the `scope` field as this project in your search request.
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
  /**
   * The full resource name of the resource associated with this IAM policy.
   * Example: `//compute.googleapis.com/projects/my_project_123/zones/zone1/inst
   * ances/instance1`. See [Cloud Asset Inventory Resource Name
   * Format](https://cloud.google.com/asset-inventory/docs/resource-name-format)
   * for more information. To search against the `resource`: * use a field
   * query. Example: `resource:organizations/123`
   *
   * @param string $resource
   */
  public function setResource($resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return string
   */
  public function getResource()
  {
    return $this->resource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IamPolicySearchResult::class, 'Google_Service_CloudAsset_IamPolicySearchResult');
