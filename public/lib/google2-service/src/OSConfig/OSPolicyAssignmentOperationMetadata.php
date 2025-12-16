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

class OSPolicyAssignmentOperationMetadata extends \Google\Model
{
  /**
   * Invalid value
   */
  public const API_METHOD_API_METHOD_UNSPECIFIED = 'API_METHOD_UNSPECIFIED';
  /**
   * Create OS policy assignment API method
   */
  public const API_METHOD_CREATE = 'CREATE';
  /**
   * Update OS policy assignment API method
   */
  public const API_METHOD_UPDATE = 'UPDATE';
  /**
   * Delete OS policy assignment API method
   */
  public const API_METHOD_DELETE = 'DELETE';
  /**
   * Invalid value
   */
  public const ROLLOUT_STATE_ROLLOUT_STATE_UNSPECIFIED = 'ROLLOUT_STATE_UNSPECIFIED';
  /**
   * The rollout is in progress.
   */
  public const ROLLOUT_STATE_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * The rollout is being cancelled.
   */
  public const ROLLOUT_STATE_CANCELLING = 'CANCELLING';
  /**
   * The rollout is cancelled.
   */
  public const ROLLOUT_STATE_CANCELLED = 'CANCELLED';
  /**
   * The rollout has completed successfully.
   */
  public const ROLLOUT_STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The OS policy assignment API method.
   *
   * @var string
   */
  public $apiMethod;
  /**
   * Reference to the `OSPolicyAssignment` API resource. Format: `projects/{proj
   * ect_number}/locations/{location}/osPolicyAssignments/{os_policy_assignment_
   * id@revision_id}`
   *
   * @var string
   */
  public $osPolicyAssignment;
  /**
   * Rollout start time
   *
   * @var string
   */
  public $rolloutStartTime;
  /**
   * State of the rollout
   *
   * @var string
   */
  public $rolloutState;
  /**
   * Rollout update time
   *
   * @var string
   */
  public $rolloutUpdateTime;

  /**
   * The OS policy assignment API method.
   *
   * Accepted values: API_METHOD_UNSPECIFIED, CREATE, UPDATE, DELETE
   *
   * @param self::API_METHOD_* $apiMethod
   */
  public function setApiMethod($apiMethod)
  {
    $this->apiMethod = $apiMethod;
  }
  /**
   * @return self::API_METHOD_*
   */
  public function getApiMethod()
  {
    return $this->apiMethod;
  }
  /**
   * Reference to the `OSPolicyAssignment` API resource. Format: `projects/{proj
   * ect_number}/locations/{location}/osPolicyAssignments/{os_policy_assignment_
   * id@revision_id}`
   *
   * @param string $osPolicyAssignment
   */
  public function setOsPolicyAssignment($osPolicyAssignment)
  {
    $this->osPolicyAssignment = $osPolicyAssignment;
  }
  /**
   * @return string
   */
  public function getOsPolicyAssignment()
  {
    return $this->osPolicyAssignment;
  }
  /**
   * Rollout start time
   *
   * @param string $rolloutStartTime
   */
  public function setRolloutStartTime($rolloutStartTime)
  {
    $this->rolloutStartTime = $rolloutStartTime;
  }
  /**
   * @return string
   */
  public function getRolloutStartTime()
  {
    return $this->rolloutStartTime;
  }
  /**
   * State of the rollout
   *
   * Accepted values: ROLLOUT_STATE_UNSPECIFIED, IN_PROGRESS, CANCELLING,
   * CANCELLED, SUCCEEDED
   *
   * @param self::ROLLOUT_STATE_* $rolloutState
   */
  public function setRolloutState($rolloutState)
  {
    $this->rolloutState = $rolloutState;
  }
  /**
   * @return self::ROLLOUT_STATE_*
   */
  public function getRolloutState()
  {
    return $this->rolloutState;
  }
  /**
   * Rollout update time
   *
   * @param string $rolloutUpdateTime
   */
  public function setRolloutUpdateTime($rolloutUpdateTime)
  {
    $this->rolloutUpdateTime = $rolloutUpdateTime;
  }
  /**
   * @return string
   */
  public function getRolloutUpdateTime()
  {
    return $this->rolloutUpdateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OSPolicyAssignmentOperationMetadata::class, 'Google_Service_OSConfig_OSPolicyAssignmentOperationMetadata');
