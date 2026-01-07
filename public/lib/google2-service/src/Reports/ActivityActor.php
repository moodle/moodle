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

namespace Google\Service\Reports;

class ActivityActor extends \Google\Model
{
  protected $applicationInfoType = ActivityActorApplicationInfo::class;
  protected $applicationInfoDataType = '';
  /**
   * The type of actor.
   *
   * @var string
   */
  public $callerType;
  /**
   * The primary email address of the actor. May be absent if there is no email
   * address associated with the actor.
   *
   * @var string
   */
  public $email;
  /**
   * Only present when `callerType` is `KEY`. Can be the `consumer_key` of the
   * requestor for OAuth 2LO API requests or an identifier for robot accounts.
   *
   * @var string
   */
  public $key;
  /**
   * The unique Google Workspace profile ID of the actor. This value might be
   * absent if the actor is not a Google Workspace user, or may be the number
   * 105250506097979753968 which acts as a placeholder ID.
   *
   * @var string
   */
  public $profileId;

  /**
   * Details of the application that was the actor for the activity.
   *
   * @param ActivityActorApplicationInfo $applicationInfo
   */
  public function setApplicationInfo(ActivityActorApplicationInfo $applicationInfo)
  {
    $this->applicationInfo = $applicationInfo;
  }
  /**
   * @return ActivityActorApplicationInfo
   */
  public function getApplicationInfo()
  {
    return $this->applicationInfo;
  }
  /**
   * The type of actor.
   *
   * @param string $callerType
   */
  public function setCallerType($callerType)
  {
    $this->callerType = $callerType;
  }
  /**
   * @return string
   */
  public function getCallerType()
  {
    return $this->callerType;
  }
  /**
   * The primary email address of the actor. May be absent if there is no email
   * address associated with the actor.
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
   * Only present when `callerType` is `KEY`. Can be the `consumer_key` of the
   * requestor for OAuth 2LO API requests or an identifier for robot accounts.
   *
   * @param string $key
   */
  public function setKey($key)
  {
    $this->key = $key;
  }
  /**
   * @return string
   */
  public function getKey()
  {
    return $this->key;
  }
  /**
   * The unique Google Workspace profile ID of the actor. This value might be
   * absent if the actor is not a Google Workspace user, or may be the number
   * 105250506097979753968 which acts as a placeholder ID.
   *
   * @param string $profileId
   */
  public function setProfileId($profileId)
  {
    $this->profileId = $profileId;
  }
  /**
   * @return string
   */
  public function getProfileId()
  {
    return $this->profileId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ActivityActor::class, 'Google_Service_Reports_ActivityActor');
