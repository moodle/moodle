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

namespace Google\Service\CloudKMS;

class ListCryptoKeyVersionsResponse extends \Google\Collection
{
  protected $collection_key = 'cryptoKeyVersions';
  protected $cryptoKeyVersionsType = CryptoKeyVersion::class;
  protected $cryptoKeyVersionsDataType = 'array';
  /**
   * A token to retrieve next page of results. Pass this value in
   * ListCryptoKeyVersionsRequest.page_token to retrieve the next page of
   * results.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * The total number of CryptoKeyVersions that matched the query. This field is
   * not populated if ListCryptoKeyVersionsRequest.filter is applied.
   *
   * @var int
   */
  public $totalSize;

  /**
   * The list of CryptoKeyVersions.
   *
   * @param CryptoKeyVersion[] $cryptoKeyVersions
   */
  public function setCryptoKeyVersions($cryptoKeyVersions)
  {
    $this->cryptoKeyVersions = $cryptoKeyVersions;
  }
  /**
   * @return CryptoKeyVersion[]
   */
  public function getCryptoKeyVersions()
  {
    return $this->cryptoKeyVersions;
  }
  /**
   * A token to retrieve next page of results. Pass this value in
   * ListCryptoKeyVersionsRequest.page_token to retrieve the next page of
   * results.
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
  /**
   * The total number of CryptoKeyVersions that matched the query. This field is
   * not populated if ListCryptoKeyVersionsRequest.filter is applied.
   *
   * @param int $totalSize
   */
  public function setTotalSize($totalSize)
  {
    $this->totalSize = $totalSize;
  }
  /**
   * @return int
   */
  public function getTotalSize()
  {
    return $this->totalSize;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListCryptoKeyVersionsResponse::class, 'Google_Service_CloudKMS_ListCryptoKeyVersionsResponse');
