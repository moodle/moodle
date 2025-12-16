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

class GoogleCloudAssetV1GovernedContainer extends \Google\Collection
{
  protected $collection_key = 'policyBundle';
  protected $consolidatedPolicyType = AnalyzerOrgPolicy::class;
  protected $consolidatedPolicyDataType = '';
  protected $effectiveTagsType = EffectiveTagDetails::class;
  protected $effectiveTagsDataType = 'array';
  /**
   * The folder(s) that this resource belongs to, in the format of
   * folders/{FOLDER_NUMBER}. This field is available when the resource belongs
   * (directly or cascadingly) to one or more folders.
   *
   * @var string[]
   */
  public $folders;
  /**
   * The [full resource name] (https://cloud.google.com/asset-
   * inventory/docs/resource-name-format) of an organization/folder/project
   * resource.
   *
   * @var string
   */
  public $fullResourceName;
  /**
   * The organization that this resource belongs to, in the format of
   * organizations/{ORGANIZATION_NUMBER}. This field is available when the
   * resource belongs (directly or cascadingly) to an organization.
   *
   * @var string
   */
  public $organization;
  /**
   * The [full resource name] (https://cloud.google.com/asset-
   * inventory/docs/resource-name-format) of the parent of AnalyzeOrgPolicyGover
   * nedContainersResponse.GovernedContainer.full_resource_name.
   *
   * @var string
   */
  public $parent;
  protected $policyBundleType = AnalyzerOrgPolicy::class;
  protected $policyBundleDataType = 'array';
  /**
   * The project that this resource belongs to, in the format of
   * projects/{PROJECT_NUMBER}. This field is available when the resource
   * belongs to a project.
   *
   * @var string
   */
  public $project;

  /**
   * The consolidated organization policy for the analyzed resource. The
   * consolidated organization policy is computed by merging and evaluating
   * AnalyzeOrgPolicyGovernedContainersResponse.GovernedContainer.policy_bundle.
   * The evaluation will respect the organization policy [hierarchy
   * rules](https://cloud.google.com/resource-manager/docs/organization-
   * policy/understanding-hierarchy).
   *
   * @param AnalyzerOrgPolicy $consolidatedPolicy
   */
  public function setConsolidatedPolicy(AnalyzerOrgPolicy $consolidatedPolicy)
  {
    $this->consolidatedPolicy = $consolidatedPolicy;
  }
  /**
   * @return AnalyzerOrgPolicy
   */
  public function getConsolidatedPolicy()
  {
    return $this->consolidatedPolicy;
  }
  /**
   * The effective tags on this resource.
   *
   * @param EffectiveTagDetails[] $effectiveTags
   */
  public function setEffectiveTags($effectiveTags)
  {
    $this->effectiveTags = $effectiveTags;
  }
  /**
   * @return EffectiveTagDetails[]
   */
  public function getEffectiveTags()
  {
    return $this->effectiveTags;
  }
  /**
   * The folder(s) that this resource belongs to, in the format of
   * folders/{FOLDER_NUMBER}. This field is available when the resource belongs
   * (directly or cascadingly) to one or more folders.
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
   * The [full resource name] (https://cloud.google.com/asset-
   * inventory/docs/resource-name-format) of an organization/folder/project
   * resource.
   *
   * @param string $fullResourceName
   */
  public function setFullResourceName($fullResourceName)
  {
    $this->fullResourceName = $fullResourceName;
  }
  /**
   * @return string
   */
  public function getFullResourceName()
  {
    return $this->fullResourceName;
  }
  /**
   * The organization that this resource belongs to, in the format of
   * organizations/{ORGANIZATION_NUMBER}. This field is available when the
   * resource belongs (directly or cascadingly) to an organization.
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
   * The [full resource name] (https://cloud.google.com/asset-
   * inventory/docs/resource-name-format) of the parent of AnalyzeOrgPolicyGover
   * nedContainersResponse.GovernedContainer.full_resource_name.
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
  /**
   * The ordered list of all organization policies from the
   * consolidated_policy.attached_resource. to the scope specified in the
   * request. If the constraint is defined with default policy, it will also
   * appear in the list.
   *
   * @param AnalyzerOrgPolicy[] $policyBundle
   */
  public function setPolicyBundle($policyBundle)
  {
    $this->policyBundle = $policyBundle;
  }
  /**
   * @return AnalyzerOrgPolicy[]
   */
  public function getPolicyBundle()
  {
    return $this->policyBundle;
  }
  /**
   * The project that this resource belongs to, in the format of
   * projects/{PROJECT_NUMBER}. This field is available when the resource
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
class_alias(GoogleCloudAssetV1GovernedContainer::class, 'Google_Service_CloudAsset_GoogleCloudAssetV1GovernedContainer');
