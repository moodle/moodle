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

namespace Google\Service\SecretManager;

class ListSecretVersionsResponse extends \Google\Collection
{
  protected $collection_key = 'versions';
  /**
   * A token to retrieve the next page of results. Pass this value in
   * ListSecretVersionsRequest.page_token to retrieve the next page.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * The total number of SecretVersions but 0 when the ListSecretsRequest.filter
   * field is set.
   *
   * @var int
   */
  public $totalSize;
  protected $versionsType = SecretVersion::class;
  protected $versionsDataType = 'array';

  /**
   * A token to retrieve the next page of results. Pass this value in
   * ListSecretVersionsRequest.page_token to retrieve the next page.
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
   * The total number of SecretVersions but 0 when the ListSecretsRequest.filter
   * field is set.
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
  /**
   * The list of SecretVersions sorted in reverse by create_time (newest first).
   *
   * @param SecretVersion[] $versions
   */
  public function setVersions($versions)
  {
    $this->versions = $versions;
  }
  /**
   * @return SecretVersion[]
   */
  public function getVersions()
  {
    return $this->versions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListSecretVersionsResponse::class, 'Google_Service_SecretManager_ListSecretVersionsResponse');
