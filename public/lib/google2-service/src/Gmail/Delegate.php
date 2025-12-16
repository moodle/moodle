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

namespace Google\Service\Gmail;

class Delegate extends \Google\Model
{
  /**
   * Unspecified verification status.
   */
  public const VERIFICATION_STATUS_verificationStatusUnspecified = 'verificationStatusUnspecified';
  /**
   * The address can act a delegate for the account.
   */
  public const VERIFICATION_STATUS_accepted = 'accepted';
  /**
   * A verification request was mailed to the address, and the owner has not yet
   * accepted it.
   */
  public const VERIFICATION_STATUS_pending = 'pending';
  /**
   * A verification request was mailed to the address, and the owner rejected
   * it.
   */
  public const VERIFICATION_STATUS_rejected = 'rejected';
  /**
   * A verification request was mailed to the address, and it expired without
   * verification.
   */
  public const VERIFICATION_STATUS_expired = 'expired';
  /**
   * The email address of the delegate.
   *
   * @var string
   */
  public $delegateEmail;
  /**
   * Indicates whether this address has been verified and can act as a delegate
   * for the account. Read-only.
   *
   * @var string
   */
  public $verificationStatus;

  /**
   * The email address of the delegate.
   *
   * @param string $delegateEmail
   */
  public function setDelegateEmail($delegateEmail)
  {
    $this->delegateEmail = $delegateEmail;
  }
  /**
   * @return string
   */
  public function getDelegateEmail()
  {
    return $this->delegateEmail;
  }
  /**
   * Indicates whether this address has been verified and can act as a delegate
   * for the account. Read-only.
   *
   * Accepted values: verificationStatusUnspecified, accepted, pending,
   * rejected, expired
   *
   * @param self::VERIFICATION_STATUS_* $verificationStatus
   */
  public function setVerificationStatus($verificationStatus)
  {
    $this->verificationStatus = $verificationStatus;
  }
  /**
   * @return self::VERIFICATION_STATUS_*
   */
  public function getVerificationStatus()
  {
    return $this->verificationStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Delegate::class, 'Google_Service_Gmail_Delegate');
