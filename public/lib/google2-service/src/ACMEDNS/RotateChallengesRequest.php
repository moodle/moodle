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

namespace Google\Service\ACMEDNS;

class RotateChallengesRequest extends \Google\Collection
{
  protected $collection_key = 'recordsToRemove';
  /**
   * @var string
   */
  public $accessToken;
  /**
   * @var bool
   */
  public $keepExpiredRecords;
  protected $recordsToAddType = AcmeTxtRecord::class;
  protected $recordsToAddDataType = 'array';
  protected $recordsToRemoveType = AcmeTxtRecord::class;
  protected $recordsToRemoveDataType = 'array';

  /**
   * @param string
   */
  public function setAccessToken($accessToken)
  {
    $this->accessToken = $accessToken;
  }
  /**
   * @return string
   */
  public function getAccessToken()
  {
    return $this->accessToken;
  }
  /**
   * @param bool
   */
  public function setKeepExpiredRecords($keepExpiredRecords)
  {
    $this->keepExpiredRecords = $keepExpiredRecords;
  }
  /**
   * @return bool
   */
  public function getKeepExpiredRecords()
  {
    return $this->keepExpiredRecords;
  }
  /**
   * @param AcmeTxtRecord[]
   */
  public function setRecordsToAdd($recordsToAdd)
  {
    $this->recordsToAdd = $recordsToAdd;
  }
  /**
   * @return AcmeTxtRecord[]
   */
  public function getRecordsToAdd()
  {
    return $this->recordsToAdd;
  }
  /**
   * @param AcmeTxtRecord[]
   */
  public function setRecordsToRemove($recordsToRemove)
  {
    $this->recordsToRemove = $recordsToRemove;
  }
  /**
   * @return AcmeTxtRecord[]
   */
  public function getRecordsToRemove()
  {
    return $this->recordsToRemove;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RotateChallengesRequest::class, 'Google_Service_ACMEDNS_RotateChallengesRequest');
