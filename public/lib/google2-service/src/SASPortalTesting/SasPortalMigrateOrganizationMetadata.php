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

namespace Google\Service\SASPortalTesting;

class SasPortalMigrateOrganizationMetadata extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const OPERATION_STATE_OPERATION_STATE_UNSPECIFIED = 'OPERATION_STATE_UNSPECIFIED';
  /**
   * Pending (Not started).
   */
  public const OPERATION_STATE_OPERATION_STATE_PENDING = 'OPERATION_STATE_PENDING';
  /**
   * In-progress.
   */
  public const OPERATION_STATE_OPERATION_STATE_RUNNING = 'OPERATION_STATE_RUNNING';
  /**
   * Done successfully.
   */
  public const OPERATION_STATE_OPERATION_STATE_SUCCEEDED = 'OPERATION_STATE_SUCCEEDED';
  /**
   * Done with errors.
   */
  public const OPERATION_STATE_OPERATION_STATE_FAILED = 'OPERATION_STATE_FAILED';
  /**
   * Output only. Current operation state
   *
   * @var string
   */
  public $operationState;

  /**
   * Output only. Current operation state
   *
   * Accepted values: OPERATION_STATE_UNSPECIFIED, OPERATION_STATE_PENDING,
   * OPERATION_STATE_RUNNING, OPERATION_STATE_SUCCEEDED, OPERATION_STATE_FAILED
   *
   * @param self::OPERATION_STATE_* $operationState
   */
  public function setOperationState($operationState)
  {
    $this->operationState = $operationState;
  }
  /**
   * @return self::OPERATION_STATE_*
   */
  public function getOperationState()
  {
    return $this->operationState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SasPortalMigrateOrganizationMetadata::class, 'Google_Service_SASPortalTesting_SasPortalMigrateOrganizationMetadata');
