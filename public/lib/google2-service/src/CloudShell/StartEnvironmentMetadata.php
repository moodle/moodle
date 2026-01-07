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

namespace Google\Service\CloudShell;

class StartEnvironmentMetadata extends \Google\Model
{
  /**
   * The environment's start state is unknown.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The environment is in the process of being started, but no additional
   * details are available.
   */
  public const STATE_STARTING = 'STARTING';
  /**
   * Startup is waiting for the user's disk to be unarchived. This can happen
   * when the user returns to Cloud Shell after not having used it for a while,
   * and suggests that startup will take longer than normal.
   */
  public const STATE_UNARCHIVING_DISK = 'UNARCHIVING_DISK';
  /**
   * Startup is waiting for compute resources to be assigned to the environment.
   * This should normally happen very quickly, but an environment might stay in
   * this state for an extended period of time if the system is experiencing
   * heavy load.
   */
  public const STATE_AWAITING_COMPUTE_RESOURCES = 'AWAITING_COMPUTE_RESOURCES';
  /**
   * Startup has completed. If the start operation was successful, the user
   * should be able to establish an SSH connection to their environment.
   * Otherwise, the operation will contain details of the failure.
   */
  public const STATE_FINISHED = 'FINISHED';
  /**
   * Current state of the environment being started.
   *
   * @var string
   */
  public $state;

  /**
   * Current state of the environment being started.
   *
   * Accepted values: STATE_UNSPECIFIED, STARTING, UNARCHIVING_DISK,
   * AWAITING_COMPUTE_RESOURCES, FINISHED
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
class_alias(StartEnvironmentMetadata::class, 'Google_Service_CloudShell_StartEnvironmentMetadata');
