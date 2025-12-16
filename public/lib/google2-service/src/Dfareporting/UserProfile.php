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

namespace Google\Service\Dfareporting;

class UserProfile extends \Google\Model
{
  /**
   * The account ID to which this profile belongs.
   *
   * @var string
   */
  public $accountId;
  /**
   * The account name this profile belongs to.
   *
   * @var string
   */
  public $accountName;
  /**
   * Etag of this resource.
   *
   * @var string
   */
  public $etag;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#userProfile".
   *
   * @var string
   */
  public $kind;
  /**
   * The unique ID of the user profile.
   *
   * @var string
   */
  public $profileId;
  /**
   * The sub account ID this profile belongs to if applicable.
   *
   * @var string
   */
  public $subAccountId;
  /**
   * The sub account name this profile belongs to if applicable.
   *
   * @var string
   */
  public $subAccountName;
  /**
   * The user name.
   *
   * @var string
   */
  public $userName;

  /**
   * The account ID to which this profile belongs.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * The account name this profile belongs to.
   *
   * @param string $accountName
   */
  public function setAccountName($accountName)
  {
    $this->accountName = $accountName;
  }
  /**
   * @return string
   */
  public function getAccountName()
  {
    return $this->accountName;
  }
  /**
   * Etag of this resource.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#userProfile".
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
   * The unique ID of the user profile.
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
  /**
   * The sub account ID this profile belongs to if applicable.
   *
   * @param string $subAccountId
   */
  public function setSubAccountId($subAccountId)
  {
    $this->subAccountId = $subAccountId;
  }
  /**
   * @return string
   */
  public function getSubAccountId()
  {
    return $this->subAccountId;
  }
  /**
   * The sub account name this profile belongs to if applicable.
   *
   * @param string $subAccountName
   */
  public function setSubAccountName($subAccountName)
  {
    $this->subAccountName = $subAccountName;
  }
  /**
   * @return string
   */
  public function getSubAccountName()
  {
    return $this->subAccountName;
  }
  /**
   * The user name.
   *
   * @param string $userName
   */
  public function setUserName($userName)
  {
    $this->userName = $userName;
  }
  /**
   * @return string
   */
  public function getUserName()
  {
    return $this->userName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserProfile::class, 'Google_Service_Dfareporting_UserProfile');
