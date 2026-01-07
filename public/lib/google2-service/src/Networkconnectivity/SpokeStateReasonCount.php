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

namespace Google\Service\Networkconnectivity;

class SpokeStateReasonCount extends \Google\Model
{
  /**
   * No information available.
   */
  public const STATE_REASON_CODE_CODE_UNSPECIFIED = 'CODE_UNSPECIFIED';
  /**
   * The proposed spoke is pending review.
   */
  public const STATE_REASON_CODE_PENDING_REVIEW = 'PENDING_REVIEW';
  /**
   * The proposed spoke has been rejected by the hub administrator.
   */
  public const STATE_REASON_CODE_REJECTED = 'REJECTED';
  /**
   * The spoke has been deactivated internally.
   */
  public const STATE_REASON_CODE_PAUSED = 'PAUSED';
  /**
   * Network Connectivity Center encountered errors while accepting the spoke.
   */
  public const STATE_REASON_CODE_FAILED = 'FAILED';
  /**
   * The proposed spoke update is pending review.
   */
  public const STATE_REASON_CODE_UPDATE_PENDING_REVIEW = 'UPDATE_PENDING_REVIEW';
  /**
   * The proposed spoke update has been rejected by the hub administrator.
   */
  public const STATE_REASON_CODE_UPDATE_REJECTED = 'UPDATE_REJECTED';
  /**
   * Network Connectivity Center encountered errors while accepting the spoke
   * update.
   */
  public const STATE_REASON_CODE_UPDATE_FAILED = 'UPDATE_FAILED';
  /**
   * Output only. The total number of spokes that are inactive for a particular
   * reason and associated with a given hub.
   *
   * @var string
   */
  public $count;
  /**
   * Output only. The reason that a spoke is inactive.
   *
   * @var string
   */
  public $stateReasonCode;

  /**
   * Output only. The total number of spokes that are inactive for a particular
   * reason and associated with a given hub.
   *
   * @param string $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return string
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * Output only. The reason that a spoke is inactive.
   *
   * Accepted values: CODE_UNSPECIFIED, PENDING_REVIEW, REJECTED, PAUSED,
   * FAILED, UPDATE_PENDING_REVIEW, UPDATE_REJECTED, UPDATE_FAILED
   *
   * @param self::STATE_REASON_CODE_* $stateReasonCode
   */
  public function setStateReasonCode($stateReasonCode)
  {
    $this->stateReasonCode = $stateReasonCode;
  }
  /**
   * @return self::STATE_REASON_CODE_*
   */
  public function getStateReasonCode()
  {
    return $this->stateReasonCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SpokeStateReasonCount::class, 'Google_Service_Networkconnectivity_SpokeStateReasonCount');
