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

namespace Google\Service\OSConfig;

class OSPolicy extends \Google\Collection
{
  /**
   * Invalid mode
   */
  public const MODE_MODE_UNSPECIFIED = 'MODE_UNSPECIFIED';
  /**
   * This mode checks if the configuration resources in the policy are in their
   * desired state. No actions are performed if they are not in the desired
   * state. This mode is used for reporting purposes.
   */
  public const MODE_VALIDATION = 'VALIDATION';
  /**
   * This mode checks if the configuration resources in the policy are in their
   * desired state, and if not, enforces the desired state.
   */
  public const MODE_ENFORCEMENT = 'ENFORCEMENT';
  protected $collection_key = 'resourceGroups';
  /**
   * This flag determines the OS policy compliance status when none of the
   * resource groups within the policy are applicable for a VM. Set this value
   * to `true` if the policy needs to be reported as compliant even if the
   * policy has nothing to validate or enforce.
   *
   * @var bool
   */
  public $allowNoResourceGroupMatch;
  /**
   * Policy description. Length of the description is limited to 1024
   * characters.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The id of the OS policy with the following restrictions: * Must
   * contain only lowercase letters, numbers, and hyphens. * Must start with a
   * letter. * Must be between 1-63 characters. * Must end with a number or a
   * letter. * Must be unique within the assignment.
   *
   * @var string
   */
  public $id;
  /**
   * Required. Policy mode
   *
   * @var string
   */
  public $mode;
  protected $resourceGroupsType = OSPolicyResourceGroup::class;
  protected $resourceGroupsDataType = 'array';

  /**
   * This flag determines the OS policy compliance status when none of the
   * resource groups within the policy are applicable for a VM. Set this value
   * to `true` if the policy needs to be reported as compliant even if the
   * policy has nothing to validate or enforce.
   *
   * @param bool $allowNoResourceGroupMatch
   */
  public function setAllowNoResourceGroupMatch($allowNoResourceGroupMatch)
  {
    $this->allowNoResourceGroupMatch = $allowNoResourceGroupMatch;
  }
  /**
   * @return bool
   */
  public function getAllowNoResourceGroupMatch()
  {
    return $this->allowNoResourceGroupMatch;
  }
  /**
   * Policy description. Length of the description is limited to 1024
   * characters.
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
   * Required. The id of the OS policy with the following restrictions: * Must
   * contain only lowercase letters, numbers, and hyphens. * Must start with a
   * letter. * Must be between 1-63 characters. * Must end with a number or a
   * letter. * Must be unique within the assignment.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Required. Policy mode
   *
   * Accepted values: MODE_UNSPECIFIED, VALIDATION, ENFORCEMENT
   *
   * @param self::MODE_* $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return self::MODE_*
   */
  public function getMode()
  {
    return $this->mode;
  }
  /**
   * Required. List of resource groups for the policy. For a particular VM,
   * resource groups are evaluated in the order specified and the first resource
   * group that is applicable is selected and the rest are ignored. If none of
   * the resource groups are applicable for a VM, the VM is considered to be
   * non-compliant w.r.t this policy. This behavior can be toggled by the flag
   * `allow_no_resource_group_match`
   *
   * @param OSPolicyResourceGroup[] $resourceGroups
   */
  public function setResourceGroups($resourceGroups)
  {
    $this->resourceGroups = $resourceGroups;
  }
  /**
   * @return OSPolicyResourceGroup[]
   */
  public function getResourceGroups()
  {
    return $this->resourceGroups;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OSPolicy::class, 'Google_Service_OSConfig_OSPolicy');
