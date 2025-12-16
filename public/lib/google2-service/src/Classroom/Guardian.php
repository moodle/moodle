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

class Guardian extends \Google\Model
{
  /**
   * Identifier for the guardian.
   *
   * @var string
   */
  public $guardianId;
  protected $guardianProfileType = UserProfile::class;
  protected $guardianProfileDataType = '';
  /**
   * The email address to which the initial guardian invitation was sent. This
   * field is only visible to domain administrators.
   *
   * @var string
   */
  public $invitedEmailAddress;
  /**
   * Identifier for the student to whom the guardian relationship applies.
   *
   * @var string
   */
  public $studentId;

  /**
   * Identifier for the guardian.
   *
   * @param string $guardianId
   */
  public function setGuardianId($guardianId)
  {
    $this->guardianId = $guardianId;
  }
  /**
   * @return string
   */
  public function getGuardianId()
  {
    return $this->guardianId;
  }
  /**
   * User profile for the guardian.
   *
   * @param UserProfile $guardianProfile
   */
  public function setGuardianProfile(UserProfile $guardianProfile)
  {
    $this->guardianProfile = $guardianProfile;
  }
  /**
   * @return UserProfile
   */
  public function getGuardianProfile()
  {
    return $this->guardianProfile;
  }
  /**
   * The email address to which the initial guardian invitation was sent. This
   * field is only visible to domain administrators.
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
   * Identifier for the student to whom the guardian relationship applies.
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
class_alias(Guardian::class, 'Google_Service_Classroom_Guardian');
