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

namespace Google\Service\WorkloadManager;

class ServiceStates extends \Google\Collection
{
  /**
   * The state is unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The state means the service has config errors.
   */
  public const STATE_CONFIG_FAILURE = 'CONFIG_FAILURE';
  /**
   * The state means the service has IAM permission errors.
   */
  public const STATE_IAM_FAILURE = 'IAM_FAILURE';
  /**
   * The state means the service has functionality errors.
   */
  public const STATE_FUNCTIONALITY_FAILURE = 'FUNCTIONALITY_FAILURE';
  /**
   * The state means the service has no error.
   */
  public const STATE_ENABLED = 'ENABLED';
  /**
   * The state means the service disabled.
   */
  public const STATE_DISABLED = 'DISABLED';
  protected $collection_key = 'iamPermissions';
  protected $iamPermissionsType = IAMPermission::class;
  protected $iamPermissionsDataType = 'array';
  /**
   * Output only. The overall state of the service.
   *
   * @var string
   */
  public $state;

  /**
   * Optional. Output only. The IAM permissions for the service.
   *
   * @param IAMPermission[] $iamPermissions
   */
  public function setIamPermissions($iamPermissions)
  {
    $this->iamPermissions = $iamPermissions;
  }
  /**
   * @return IAMPermission[]
   */
  public function getIamPermissions()
  {
    return $this->iamPermissions;
  }
  /**
   * Output only. The overall state of the service.
   *
   * Accepted values: STATE_UNSPECIFIED, CONFIG_FAILURE, IAM_FAILURE,
   * FUNCTIONALITY_FAILURE, ENABLED, DISABLED
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServiceStates::class, 'Google_Service_WorkloadManager_ServiceStates');
