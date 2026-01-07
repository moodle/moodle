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

namespace Google\Service\Compute;

class InstanceGroupManagerResizeRequestStatus extends \Google\Model
{
  protected $errorType = InstanceGroupManagerResizeRequestStatusError::class;
  protected $errorDataType = '';
  protected $lastAttemptType = InstanceGroupManagerResizeRequestStatusLastAttempt::class;
  protected $lastAttemptDataType = '';

  /**
   * Output only. [Output only] Fatal errors encountered during the queueing or
   * provisioning phases of the ResizeRequest that caused the transition to the
   * FAILED state. Contrary to the last_attempt errors, this field is final and
   * errors are never removed from here, as the ResizeRequest is not going to
   * retry.
   *
   * @param InstanceGroupManagerResizeRequestStatusError $error
   */
  public function setError(InstanceGroupManagerResizeRequestStatusError $error)
  {
    $this->error = $error;
  }
  /**
   * @return InstanceGroupManagerResizeRequestStatusError
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Output only. [Output only] Information about the last attempt to fulfill
   * the request. The value is temporary since the ResizeRequest can retry, as
   * long as it's still active and the last attempt value can either be cleared
   * or replaced with a different error. Since ResizeRequest retries
   * infrequently, the value may be stale and no longer show an active problem.
   * The value is cleared when ResizeRequest transitions to the final state
   * (becomes inactive). If the final state is FAILED the error describing it
   * will be storred in the "error" field only.
   *
   * @param InstanceGroupManagerResizeRequestStatusLastAttempt $lastAttempt
   */
  public function setLastAttempt(InstanceGroupManagerResizeRequestStatusLastAttempt $lastAttempt)
  {
    $this->lastAttempt = $lastAttempt;
  }
  /**
   * @return InstanceGroupManagerResizeRequestStatusLastAttempt
   */
  public function getLastAttempt()
  {
    return $this->lastAttempt;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceGroupManagerResizeRequestStatus::class, 'Google_Service_Compute_InstanceGroupManagerResizeRequestStatus');
