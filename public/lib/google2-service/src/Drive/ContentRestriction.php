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

namespace Google\Service\Drive;

class ContentRestriction extends \Google\Model
{
  /**
   * Whether the content restriction can only be modified or removed by a user
   * who owns the file. For files in shared drives, any user with `organizer`
   * capabilities can modify or remove this content restriction.
   *
   * @var bool
   */
  public $ownerRestricted;
  /**
   * Whether the content of the file is read-only. If a file is read-only, a new
   * revision of the file may not be added, comments may not be added or
   * modified, and the title of the file may not be modified.
   *
   * @var bool
   */
  public $readOnly;
  /**
   * Reason for why the content of the file is restricted. This is only mutable
   * on requests that also set `readOnly=true`.
   *
   * @var string
   */
  public $reason;
  protected $restrictingUserType = User::class;
  protected $restrictingUserDataType = '';
  /**
   * The time at which the content restriction was set (formatted RFC 3339
   * timestamp). Only populated if readOnly is true.
   *
   * @var string
   */
  public $restrictionTime;
  /**
   * Output only. Whether the content restriction was applied by the system, for
   * example due to an esignature. Users cannot modify or remove system
   * restricted content restrictions.
   *
   * @var bool
   */
  public $systemRestricted;
  /**
   * Output only. The type of the content restriction. Currently the only
   * possible value is `globalContentRestriction`.
   *
   * @var string
   */
  public $type;

  /**
   * Whether the content restriction can only be modified or removed by a user
   * who owns the file. For files in shared drives, any user with `organizer`
   * capabilities can modify or remove this content restriction.
   *
   * @param bool $ownerRestricted
   */
  public function setOwnerRestricted($ownerRestricted)
  {
    $this->ownerRestricted = $ownerRestricted;
  }
  /**
   * @return bool
   */
  public function getOwnerRestricted()
  {
    return $this->ownerRestricted;
  }
  /**
   * Whether the content of the file is read-only. If a file is read-only, a new
   * revision of the file may not be added, comments may not be added or
   * modified, and the title of the file may not be modified.
   *
   * @param bool $readOnly
   */
  public function setReadOnly($readOnly)
  {
    $this->readOnly = $readOnly;
  }
  /**
   * @return bool
   */
  public function getReadOnly()
  {
    return $this->readOnly;
  }
  /**
   * Reason for why the content of the file is restricted. This is only mutable
   * on requests that also set `readOnly=true`.
   *
   * @param string $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return string
   */
  public function getReason()
  {
    return $this->reason;
  }
  /**
   * Output only. The user who set the content restriction. Only populated if
   * `readOnly=true`.
   *
   * @param User $restrictingUser
   */
  public function setRestrictingUser(User $restrictingUser)
  {
    $this->restrictingUser = $restrictingUser;
  }
  /**
   * @return User
   */
  public function getRestrictingUser()
  {
    return $this->restrictingUser;
  }
  /**
   * The time at which the content restriction was set (formatted RFC 3339
   * timestamp). Only populated if readOnly is true.
   *
   * @param string $restrictionTime
   */
  public function setRestrictionTime($restrictionTime)
  {
    $this->restrictionTime = $restrictionTime;
  }
  /**
   * @return string
   */
  public function getRestrictionTime()
  {
    return $this->restrictionTime;
  }
  /**
   * Output only. Whether the content restriction was applied by the system, for
   * example due to an esignature. Users cannot modify or remove system
   * restricted content restrictions.
   *
   * @param bool $systemRestricted
   */
  public function setSystemRestricted($systemRestricted)
  {
    $this->systemRestricted = $systemRestricted;
  }
  /**
   * @return bool
   */
  public function getSystemRestricted()
  {
    return $this->systemRestricted;
  }
  /**
   * Output only. The type of the content restriction. Currently the only
   * possible value is `globalContentRestriction`.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContentRestriction::class, 'Google_Service_Drive_ContentRestriction');
