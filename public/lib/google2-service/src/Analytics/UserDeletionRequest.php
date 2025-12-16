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

namespace Google\Service\Analytics;

class UserDeletionRequest extends \Google\Model
{
  /**
   * This marks the point in time for which all user data before should be
   * deleted
   *
   * @var string
   */
  public $deletionRequestTime;
  /**
   * Firebase Project Id
   *
   * @var string
   */
  public $firebaseProjectId;
  protected $idType = UserDeletionRequestId::class;
  protected $idDataType = '';
  /**
   * Value is "analytics#userDeletionRequest".
   *
   * @var string
   */
  public $kind;
  /**
   * Property ID
   *
   * @var string
   */
  public $propertyId;
  /**
   * Web property ID of the form UA-XXXXX-YY.
   *
   * @var string
   */
  public $webPropertyId;

  /**
   * This marks the point in time for which all user data before should be
   * deleted
   *
   * @param string $deletionRequestTime
   */
  public function setDeletionRequestTime($deletionRequestTime)
  {
    $this->deletionRequestTime = $deletionRequestTime;
  }
  /**
   * @return string
   */
  public function getDeletionRequestTime()
  {
    return $this->deletionRequestTime;
  }
  /**
   * Firebase Project Id
   *
   * @param string $firebaseProjectId
   */
  public function setFirebaseProjectId($firebaseProjectId)
  {
    $this->firebaseProjectId = $firebaseProjectId;
  }
  /**
   * @return string
   */
  public function getFirebaseProjectId()
  {
    return $this->firebaseProjectId;
  }
  /**
   * User ID.
   *
   * @param UserDeletionRequestId $id
   */
  public function setId(UserDeletionRequestId $id)
  {
    $this->id = $id;
  }
  /**
   * @return UserDeletionRequestId
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Value is "analytics#userDeletionRequest".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Property ID
   *
   * @param string $propertyId
   */
  public function setPropertyId($propertyId)
  {
    $this->propertyId = $propertyId;
  }
  /**
   * @return string
   */
  public function getPropertyId()
  {
    return $this->propertyId;
  }
  /**
   * Web property ID of the form UA-XXXXX-YY.
   *
   * @param string $webPropertyId
   */
  public function setWebPropertyId($webPropertyId)
  {
    $this->webPropertyId = $webPropertyId;
  }
  /**
   * @return string
   */
  public function getWebPropertyId()
  {
    return $this->webPropertyId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserDeletionRequest::class, 'Google_Service_Analytics_UserDeletionRequest');
