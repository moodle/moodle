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

namespace Google\Service\DeploymentManager;

class SetCommonInstanceMetadataOperationMetadataPerLocationOperationInfo extends \Google\Model
{
  public const STATE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Operation is not yet confirmed to have been created in the location.
   */
  public const STATE_PROPAGATING = 'PROPAGATING';
  /**
   * Operation is confirmed to be in the location.
   */
  public const STATE_PROPAGATED = 'PROPAGATED';
  /**
   * Operation not tracked in this location e.g. zone is marked as DOWN.
   */
  public const STATE_ABANDONED = 'ABANDONED';
  /**
   * Operation is in an error state.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Operation has completed successfully.
   */
  public const STATE_DONE = 'DONE';
  protected $errorType = Status::class;
  protected $errorDataType = '';
  /**
   * [Output Only] Status of the action, which can be one of the following:
   * `PROPAGATING`, `PROPAGATED`, `ABANDONED`, `FAILED`, or `DONE`.
   *
   * @var string
   */
  public $state;

  /**
   * [Output Only] If state is `ABANDONED` or `FAILED`, this field is populated.
   *
   * @param Status $error
   */
  public function setError(Status $error)
  {
    $this->error = $error;
  }
  /**
   * @return Status
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * [Output Only] Status of the action, which can be one of the following:
   * `PROPAGATING`, `PROPAGATED`, `ABANDONED`, `FAILED`, or `DONE`.
   *
   * Accepted values: UNSPECIFIED, PROPAGATING, PROPAGATED, ABANDONED, FAILED,
   * DONE
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
class_alias(SetCommonInstanceMetadataOperationMetadataPerLocationOperationInfo::class, 'Google_Service_DeploymentManager_SetCommonInstanceMetadataOperationMetadataPerLocationOperationInfo');
