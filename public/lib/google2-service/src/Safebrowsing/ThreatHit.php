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

namespace Google\Service\Safebrowsing;

class ThreatHit extends \Google\Collection
{
  protected $collection_key = 'resources';
  protected $clientInfoType = ClientInfo::class;
  protected $clientInfoDataType = '';
  public $clientInfo;
  protected $entryType = ThreatEntry::class;
  protected $entryDataType = '';
  public $entry;
  /**
   * @var string
   */
  public $platformType;
  protected $resourcesType = ThreatSource::class;
  protected $resourcesDataType = 'array';
  public $resources;
  /**
   * @var string
   */
  public $threatType;
  protected $userInfoType = UserInfo::class;
  protected $userInfoDataType = '';
  public $userInfo;

  /**
   * @param ClientInfo
   */
  public function setClientInfo(ClientInfo $clientInfo)
  {
    $this->clientInfo = $clientInfo;
  }
  /**
   * @return ClientInfo
   */
  public function getClientInfo()
  {
    return $this->clientInfo;
  }
  /**
   * @param ThreatEntry
   */
  public function setEntry(ThreatEntry $entry)
  {
    $this->entry = $entry;
  }
  /**
   * @return ThreatEntry
   */
  public function getEntry()
  {
    return $this->entry;
  }
  /**
   * @param string
   */
  public function setPlatformType($platformType)
  {
    $this->platformType = $platformType;
  }
  /**
   * @return string
   */
  public function getPlatformType()
  {
    return $this->platformType;
  }
  /**
   * @param ThreatSource[]
   */
  public function setResources($resources)
  {
    $this->resources = $resources;
  }
  /**
   * @return ThreatSource[]
   */
  public function getResources()
  {
    return $this->resources;
  }
  /**
   * @param string
   */
  public function setThreatType($threatType)
  {
    $this->threatType = $threatType;
  }
  /**
   * @return string
   */
  public function getThreatType()
  {
    return $this->threatType;
  }
  /**
   * @param UserInfo
   */
  public function setUserInfo(UserInfo $userInfo)
  {
    $this->userInfo = $userInfo;
  }
  /**
   * @return UserInfo
   */
  public function getUserInfo()
  {
    return $this->userInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ThreatHit::class, 'Google_Service_Safebrowsing_ThreatHit');
