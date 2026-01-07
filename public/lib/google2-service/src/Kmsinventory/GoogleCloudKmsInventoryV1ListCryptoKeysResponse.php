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

namespace Google\Service\Kmsinventory;

class GoogleCloudKmsInventoryV1ListCryptoKeysResponse extends \Google\Collection
{
  protected $collection_key = 'cryptoKeys';
  protected $cryptoKeysType = GoogleCloudKmsV1CryptoKey::class;
  protected $cryptoKeysDataType = 'array';
  /**
   * The page token returned from the previous response if the next page is
   * desired.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The list of CryptoKeys.
   *
   * @param GoogleCloudKmsV1CryptoKey[] $cryptoKeys
   */
  public function setCryptoKeys($cryptoKeys)
  {
    $this->cryptoKeys = $cryptoKeys;
  }
  /**
   * @return GoogleCloudKmsV1CryptoKey[]
   */
  public function getCryptoKeys()
  {
    return $this->cryptoKeys;
  }
  /**
   * The page token returned from the previous response if the next page is
   * desired.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudKmsInventoryV1ListCryptoKeysResponse::class, 'Google_Service_Kmsinventory_GoogleCloudKmsInventoryV1ListCryptoKeysResponse');
