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

class ForwardingAddress extends \Google\Model
{
  /**
   * Unspecified verification status.
   */
  public const VERIFICATION_STATUS_verificationStatusUnspecified = 'verificationStatusUnspecified';
  /**
   * The address is ready to use for forwarding.
   */
  public const VERIFICATION_STATUS_accepted = 'accepted';
  /**
   * The address is awaiting verification by the owner.
   */
  public const VERIFICATION_STATUS_pending = 'pending';
  /**
   * An email address to which messages can be forwarded.
   *
   * @var string
   */
  public $forwardingEmail;
  /**
   * Indicates whether this address has been verified and is usable for
   * forwarding. Read-only.
   *
   * @var string
   */
  public $verificationStatus;

  /**
   * An email address to which messages can be forwarded.
   *
   * @param string $forwardingEmail
   */
  public function setForwardingEmail($forwardingEmail)
  {
    $this->forwardingEmail = $forwardingEmail;
  }
  /**
   * @return string
   */
  public function getForwardingEmail()
  {
    return $this->forwardingEmail;
  }
  /**
   * Indicates whether this address has been verified and is usable for
   * forwarding. Read-only.
   *
   * Accepted values: verificationStatusUnspecified, accepted, pending
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
class_alias(ForwardingAddress::class, 'Google_Service_Gmail_ForwardingAddress');
