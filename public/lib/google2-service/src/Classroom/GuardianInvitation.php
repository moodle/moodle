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

namespace Google\Service\Classroom;

class GuardianInvitation extends \Google\Model
{
  /**
   * Should never be returned.
   */
  public const STATE_GUARDIAN_INVITATION_STATE_UNSPECIFIED = 'GUARDIAN_INVITATION_STATE_UNSPECIFIED';
  /**
   * The invitation is active and awaiting a response.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The invitation is no longer active. It may have been accepted, declined,
   * withdrawn or it may have expired.
   */
  public const STATE_COMPLETE = 'COMPLETE';
  /**
   * The time that this invitation was created. Read-only.
   *
   * @var string
   */
  public $creationTime;
  /**
   * Unique identifier for this invitation. Read-only.
   *
   * @var string
   */
  public $invitationId;
  /**
   * Email address that the invitation was sent to. This field is only visible
   * to domain administrators.
   *
   * @var string
   */
  public $invitedEmailAddress;
  /**
   * The state that this invitation is in.
   *
   * @var string
   */
  public $state;
  /**
   * ID of the student (in standard format)
   *
   * @var string
   */
  public $studentId;

  /**
   * The time that this invitation was created. Read-only.
   *
   * @param string $creationTime
   */
  public function setCreationTime($creationTime)
  {
    $this->creationTime = $creationTime;
  }
  /**
   * @return string
   */
  public function getCreationTime()
  {
    return $this->creationTime;
  }
  /**
   * Unique identifier for this invitation. Read-only.
   *
   * @param string $invitationId
   */
  public function setInvitationId($invitationId)
  {
    $this->invitationId = $invitationId;
  }
  /**
   * @return string
   */
  public function getInvitationId()
  {
    return $this->invitationId;
  }
  /**
   * Email address that the invitation was sent to. This field is only visible
   * to domain administrators.
   *
   * @param string $invitedEmailAddress
   */
  public function setInvitedEmailAddress($invitedEmailAddress)
  {
    $this->invitedEmailAddress = $invitedEmailAddress;
  }
  /**
   * @return string
   */
  public function getInvitedEmailAddress()
  {
    return $this->invitedEmailAddress;
  }
  /**
   * The state that this invitation is in.
   *
   * Accepted values: GUARDIAN_INVITATION_STATE_UNSPECIFIED, PENDING, COMPLETE
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
   * ID of the student (in standard format)
   *
   * @param string $studentId
   */
  public function setStudentId($studentId)
  {
    $this->studentId = $studentId;
  }
  /**
   * @return string
   */
  public function getStudentId()
  {
    return $this->studentId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GuardianInvitation::class, 'Google_Service_Classroom_GuardianInvitation');
