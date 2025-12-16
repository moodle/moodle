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

namespace Google\Service\CloudIdentity;

class UserInvitation extends \Google\Model
{
  /**
   * The default value. This value is used if the state is omitted.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The `UserInvitation` has been created and is ready for sending as an email.
   */
  public const STATE_NOT_YET_SENT = 'NOT_YET_SENT';
  /**
   * The user has been invited by email.
   */
  public const STATE_INVITED = 'INVITED';
  /**
   * The user has accepted the invitation and is part of the organization.
   */
  public const STATE_ACCEPTED = 'ACCEPTED';
  /**
   * The user declined the invitation.
   */
  public const STATE_DECLINED = 'DECLINED';
  /**
   * Number of invitation emails sent to the user.
   *
   * @var string
   */
  public $mailsSentCount;
  /**
   * Shall be of the form
   * `customers/{customer}/userinvitations/{user_email_address}`.
   *
   * @var string
   */
  public $name;
  /**
   * State of the `UserInvitation`.
   *
   * @var string
   */
  public $state;
  /**
   * Time when the `UserInvitation` was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Number of invitation emails sent to the user.
   *
   * @param string $mailsSentCount
   */
  public function setMailsSentCount($mailsSentCount)
  {
    $this->mailsSentCount = $mailsSentCount;
  }
  /**
   * @return string
   */
  public function getMailsSentCount()
  {
    return $this->mailsSentCount;
  }
  /**
   * Shall be of the form
   * `customers/{customer}/userinvitations/{user_email_address}`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * State of the `UserInvitation`.
   *
   * Accepted values: STATE_UNSPECIFIED, NOT_YET_SENT, INVITED, ACCEPTED,
   * DECLINED
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
  /**
   * Time when the `UserInvitation` was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserInvitation::class, 'Google_Service_CloudIdentity_UserInvitation');
