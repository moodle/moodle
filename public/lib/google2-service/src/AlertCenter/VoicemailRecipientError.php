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

namespace Google\Service\AlertCenter;

class VoicemailRecipientError extends \Google\Model
{
  /**
   * Reason wasn't specified.
   */
  public const INVALID_REASON_EMAIL_INVALID_REASON_UNSPECIFIED = 'EMAIL_INVALID_REASON_UNSPECIFIED';
  /**
   * User can't receive emails due to insufficient quota.
   */
  public const INVALID_REASON_OUT_OF_QUOTA = 'OUT_OF_QUOTA';
  /**
   * All recipients were deleted.
   */
  public const INVALID_REASON_RECIPIENT_DELETED = 'RECIPIENT_DELETED';
  /**
   * Email address of the invalid recipient. This may be unavailable if the
   * recipient was deleted.
   *
   * @var string
   */
  public $email;
  /**
   * Reason for the error.
   *
   * @var string
   */
  public $invalidReason;

  /**
   * Email address of the invalid recipient. This may be unavailable if the
   * recipient was deleted.
   *
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }
  /**
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }
  /**
   * Reason for the error.
   *
   * Accepted values: EMAIL_INVALID_REASON_UNSPECIFIED, OUT_OF_QUOTA,
   * RECIPIENT_DELETED
   *
   * @param self::INVALID_REASON_* $invalidReason
   */
  public function setInvalidReason($invalidReason)
  {
    $this->invalidReason = $invalidReason;
  }
  /**
   * @return self::INVALID_REASON_*
   */
  public function getInvalidReason()
  {
    return $this->invalidReason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VoicemailRecipientError::class, 'Google_Service_AlertCenter_VoicemailRecipientError');
