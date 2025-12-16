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

namespace Google\Service\ManagedServiceforMicrosoftActiveDirectoryConsumerAPI;

class MaintenancePolicy extends \Google\Model
{
  /**
   * Unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Resource is ready to be used.
   */
  public const STATE_READY = 'READY';
  /**
   * Resource is being deleted. It can no longer be attached to instances.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Output only. The time when the resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Description of what this policy is for. Create/Update methods
   * return INVALID_ARGUMENT if the length is greater than 512.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Resource labels to represent user provided metadata. Each label
   * is a key-value pair, where both the key and the value are arbitrary strings
   * provided by the user.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Required. MaintenancePolicy name using the form: `projects/{project_id}/loc
   * ations/{location_id}/maintenancePolicies/{maintenance_policy_id}` where
   * {project_id} refers to a GCP consumer project ID, {location_id} refers to a
   * GCP region/zone, {maintenance_policy_id} must be 1-63 characters long and
   * match the regular expression `[a-z0-9]([-a-z0-9]*[a-z0-9])?`.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The state of the policy.
   *
   * @var string
   */
  public $state;
  protected $updatePolicyType = UpdatePolicy::class;
  protected $updatePolicyDataType = '';
  /**
   * Output only. The time when the resource was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time when the resource was created.
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
   * Optional. Description of what this policy is for. Create/Update methods
   * return INVALID_ARGUMENT if the length is greater than 512.
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
   * Optional. Resource labels to represent user provided metadata. Each label
   * is a key-value pair, where both the key and the value are arbitrary strings
   * provided by the user.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Required. MaintenancePolicy name using the form: `projects/{project_id}/loc
   * ations/{location_id}/maintenancePolicies/{maintenance_policy_id}` where
   * {project_id} refers to a GCP consumer project ID, {location_id} refers to a
   * GCP region/zone, {maintenance_policy_id} must be 1-63 characters long and
   * match the regular expression `[a-z0-9]([-a-z0-9]*[a-z0-9])?`.
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
   * Optional. The state of the policy.
   *
   * Accepted values: STATE_UNSPECIFIED, READY, DELETING
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Maintenance policy applicable to instance update.
   *
   * @param UpdatePolicy $updatePolicy
   */
  public function setUpdatePolicy(UpdatePolicy $updatePolicy)
  {
    $this->updatePolicy = $updatePolicy;
  }
  /**
   * @return UpdatePolicy
   */
  public function getUpdatePolicy()
  {
    return $this->updatePolicy;
  }
  /**
   * Output only. The time when the resource was updated.
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
class_alias(MaintenancePolicy::class, 'Google_Service_ManagedServiceforMicrosoftActiveDirectoryConsumerAPI_MaintenancePolicy');
