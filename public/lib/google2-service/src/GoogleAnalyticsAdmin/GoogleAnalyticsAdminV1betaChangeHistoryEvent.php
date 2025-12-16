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

namespace Google\Service\GoogleAnalyticsAdmin;

class GoogleAnalyticsAdminV1betaChangeHistoryEvent extends \Google\Collection
{
  /**
   * Unknown or unspecified actor type.
   */
  public const ACTOR_TYPE_ACTOR_TYPE_UNSPECIFIED = 'ACTOR_TYPE_UNSPECIFIED';
  /**
   * Changes made by the user specified in actor_email.
   */
  public const ACTOR_TYPE_USER = 'USER';
  /**
   * Changes made by the Google Analytics system.
   */
  public const ACTOR_TYPE_SYSTEM = 'SYSTEM';
  /**
   * Changes made by Google Analytics support team staff.
   */
  public const ACTOR_TYPE_SUPPORT = 'SUPPORT';
  protected $collection_key = 'changes';
  /**
   * The type of actor that made this change.
   *
   * @var string
   */
  public $actorType;
  /**
   * Time when change was made.
   *
   * @var string
   */
  public $changeTime;
  protected $changesType = GoogleAnalyticsAdminV1betaChangeHistoryChange::class;
  protected $changesDataType = 'array';
  /**
   * If true, then the list of changes returned was filtered, and does not
   * represent all changes that occurred in this event.
   *
   * @var bool
   */
  public $changesFiltered;
  /**
   * ID of this change history event. This ID is unique across Google Analytics.
   *
   * @var string
   */
  public $id;
  /**
   * Email address of the Google account that made the change. This will be a
   * valid email address if the actor field is set to USER, and empty otherwise.
   * Google accounts that have been deleted will cause an error.
   *
   * @var string
   */
  public $userActorEmail;

  /**
   * The type of actor that made this change.
   *
   * Accepted values: ACTOR_TYPE_UNSPECIFIED, USER, SYSTEM, SUPPORT
   *
   * @param self::ACTOR_TYPE_* $actorType
   */
  public function setActorType($actorType)
  {
    $this->actorType = $actorType;
  }
  /**
   * @return self::ACTOR_TYPE_*
   */
  public function getActorType()
  {
    return $this->actorType;
  }
  /**
   * Time when change was made.
   *
   * @param string $changeTime
   */
  public function setChangeTime($changeTime)
  {
    $this->changeTime = $changeTime;
  }
  /**
   * @return string
   */
  public function getChangeTime()
  {
    return $this->changeTime;
  }
  /**
   * A list of changes made in this change history event that fit the filters
   * specified in SearchChangeHistoryEventsRequest.
   *
   * @param GoogleAnalyticsAdminV1betaChangeHistoryChange[] $changes
   */
  public function setChanges($changes)
  {
    $this->changes = $changes;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaChangeHistoryChange[]
   */
  public function getChanges()
  {
    return $this->changes;
  }
  /**
   * If true, then the list of changes returned was filtered, and does not
   * represent all changes that occurred in this event.
   *
   * @param bool $changesFiltered
   */
  public function setChangesFiltered($changesFiltered)
  {
    $this->changesFiltered = $changesFiltered;
  }
  /**
   * @return bool
   */
  public function getChangesFiltered()
  {
    return $this->changesFiltered;
  }
  /**
   * ID of this change history event. This ID is unique across Google Analytics.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Email address of the Google account that made the change. This will be a
   * valid email address if the actor field is set to USER, and empty otherwise.
   * Google accounts that have been deleted will cause an error.
   *
   * @param string $userActorEmail
   */
  public function setUserActorEmail($userActorEmail)
  {
    $this->userActorEmail = $userActorEmail;
  }
  /**
   * @return string
   */
  public function getUserActorEmail()
  {
    return $this->userActorEmail;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAnalyticsAdminV1betaChangeHistoryEvent::class, 'Google_Service_GoogleAnalyticsAdmin_GoogleAnalyticsAdminV1betaChangeHistoryEvent');
