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

class StateReason extends \Google\Model
{
  /**
   * No information available.
   */
  public const CODE_CODE_UNSPECIFIED = 'CODE_UNSPECIFIED';
  /**
   * The proposed spoke is pending review.
   */
  public const CODE_PENDING_REVIEW = 'PENDING_REVIEW';
  /**
   * The proposed spoke has been rejected by the hub administrator.
   */
  public const CODE_REJECTED = 'REJECTED';
  /**
   * The spoke has been deactivated internally.
   */
  public const CODE_PAUSED = 'PAUSED';
  /**
   * Network Connectivity Center encountered errors while accepting the spoke.
   */
  public const CODE_FAILED = 'FAILED';
  /**
   * The proposed spoke update is pending review.
   */
  public const CODE_UPDATE_PENDING_REVIEW = 'UPDATE_PENDING_REVIEW';
  /**
   * The proposed spoke update has been rejected by the hub administrator.
   */
  public const CODE_UPDATE_REJECTED = 'UPDATE_REJECTED';
  /**
   * Network Connectivity Center encountered errors while accepting the spoke
   * update.
   */
  public const CODE_UPDATE_FAILED = 'UPDATE_FAILED';
  /**
   * The code associated with this reason.
   *
   * @var string
   */
  public $code;
  /**
   * Human-readable details about this reason.
   *
   * @var string
   */
  public $message;
  /**
   * Additional information provided by the user in the RejectSpoke call.
   *
   * @var string
   */
  public $userDetails;

  /**
   * The code associated with this reason.
   *
   * Accepted values: CODE_UNSPECIFIED, PENDING_REVIEW, REJECTED, PAUSED,
   * FAILED, UPDATE_PENDING_REVIEW, UPDATE_REJECTED, UPDATE_FAILED
   *
   * @param self::CODE_* $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return self::CODE_*
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * Human-readable details about this reason.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * Additional information provided by the user in the RejectSpoke call.
   *
   * @param string $userDetails
   */
  public function setUserDetails($userDetails)
  {
    $this->userDetails = $userDetails;
  }
  /**
   * @return string
   */
  public function getUserDetails()
  {
    return $this->userDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StateReason::class, 'Google_Service_Networkconnectivity_StateReason');
