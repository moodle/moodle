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

namespace Google\Service\CloudSearch;

class EnterpriseTopazSidekickPerson extends \Google\Model
{
  public const AFFINITY_LEVEL_UNKNOWN = 'UNKNOWN';
  public const AFFINITY_LEVEL_LOW = 'LOW';
  public const AFFINITY_LEVEL_MEDIUM = 'MEDIUM';
  public const AFFINITY_LEVEL_HIGH = 'HIGH';
  /**
   * Awaiting for the user to set the status.
   */
  public const ATTENDING_STATUS_AWAITING = 'AWAITING';
  /**
   * Attending.
   */
  public const ATTENDING_STATUS_YES = 'YES';
  /**
   * Not attending.
   */
  public const ATTENDING_STATUS_NO = 'NO';
  /**
   * Tentatively attending.
   */
  public const ATTENDING_STATUS_MAYBE = 'MAYBE';
  /**
   * The level of affinity this person has with the requesting user.
   *
   * @var string
   */
  public $affinityLevel;
  /**
   * Attendance status of the person when included in a meeting event.
   *
   * @var string
   */
  public $attendingStatus;
  /**
   * Email.
   *
   * @var string
   */
  public $email;
  /**
   * Gaia id.
   *
   * @deprecated
   * @var string
   */
  public $gaiaId;
  /**
   * Whether the invitee is a group.
   *
   * @var bool
   */
  public $isGroup;
  /**
   * Name.
   *
   * @var string
   */
  public $name;
  /**
   * Obfuscated Gaia id.
   *
   * @var string
   */
  public $obfuscatedGaiaId;
  /**
   * Absolute URL to the profile photo of the person.
   *
   * @var string
   */
  public $photoUrl;

  /**
   * The level of affinity this person has with the requesting user.
   *
   * Accepted values: UNKNOWN, LOW, MEDIUM, HIGH
   *
   * @param self::AFFINITY_LEVEL_* $affinityLevel
   */
  public function setAffinityLevel($affinityLevel)
  {
    $this->affinityLevel = $affinityLevel;
  }
  /**
   * @return self::AFFINITY_LEVEL_*
   */
  public function getAffinityLevel()
  {
    return $this->affinityLevel;
  }
  /**
   * Attendance status of the person when included in a meeting event.
   *
   * Accepted values: AWAITING, YES, NO, MAYBE
   *
   * @param self::ATTENDING_STATUS_* $attendingStatus
   */
  public function setAttendingStatus($attendingStatus)
  {
    $this->attendingStatus = $attendingStatus;
  }
  /**
   * @return self::ATTENDING_STATUS_*
   */
  public function getAttendingStatus()
  {
    return $this->attendingStatus;
  }
  /**
   * Email.
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
   * Gaia id.
   *
   * @deprecated
   * @param string $gaiaId
   */
  public function setGaiaId($gaiaId)
  {
    $this->gaiaId = $gaiaId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getGaiaId()
  {
    return $this->gaiaId;
  }
  /**
   * Whether the invitee is a group.
   *
   * @param bool $isGroup
   */
  public function setIsGroup($isGroup)
  {
    $this->isGroup = $isGroup;
  }
  /**
   * @return bool
   */
  public function getIsGroup()
  {
    return $this->isGroup;
  }
  /**
   * Name.
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
   * Obfuscated Gaia id.
   *
   * @param string $obfuscatedGaiaId
   */
  public function setObfuscatedGaiaId($obfuscatedGaiaId)
  {
    $this->obfuscatedGaiaId = $obfuscatedGaiaId;
  }
  /**
   * @return string
   */
  public function getObfuscatedGaiaId()
  {
    return $this->obfuscatedGaiaId;
  }
  /**
   * Absolute URL to the profile photo of the person.
   *
   * @param string $photoUrl
   */
  public function setPhotoUrl($photoUrl)
  {
    $this->photoUrl = $photoUrl;
  }
  /**
   * @return string
   */
  public function getPhotoUrl()
  {
    return $this->photoUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseTopazSidekickPerson::class, 'Google_Service_CloudSearch_EnterpriseTopazSidekickPerson');
