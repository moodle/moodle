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

class GaDataProfileInfo extends \Google\Model
{
  /**
   * Account ID to which this view (profile) belongs.
   *
   * @var string
   */
  public $accountId;
  /**
   * Internal ID for the web property to which this view (profile) belongs.
   *
   * @var string
   */
  public $internalWebPropertyId;
  /**
   * View (Profile) ID.
   *
   * @var string
   */
  public $profileId;
  /**
   * View (Profile) name.
   *
   * @var string
   */
  public $profileName;
  /**
   * Table ID for view (profile).
   *
   * @var string
   */
  public $tableId;
  /**
   * Web Property ID to which this view (profile) belongs.
   *
   * @var string
   */
  public $webPropertyId;

  /**
   * Account ID to which this view (profile) belongs.
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
   * Internal ID for the web property to which this view (profile) belongs.
   *
   * @param string $internalWebPropertyId
   */
  public function setInternalWebPropertyId($internalWebPropertyId)
  {
    $this->internalWebPropertyId = $internalWebPropertyId;
  }
  /**
   * @return string
   */
  public function getInternalWebPropertyId()
  {
    return $this->internalWebPropertyId;
  }
  /**
   * View (Profile) ID.
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
   * View (Profile) name.
   *
   * @param string $profileName
   */
  public function setProfileName($profileName)
  {
    $this->profileName = $profileName;
  }
  /**
   * @return string
   */
  public function getProfileName()
  {
    return $this->profileName;
  }
  /**
   * Table ID for view (profile).
   *
   * @param string $tableId
   */
  public function setTableId($tableId)
  {
    $this->tableId = $tableId;
  }
  /**
   * @return string
   */
  public function getTableId()
  {
    return $this->tableId;
  }
  /**
   * Web Property ID to which this view (profile) belongs.
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
class_alias(GaDataProfileInfo::class, 'Google_Service_Analytics_GaDataProfileInfo');
