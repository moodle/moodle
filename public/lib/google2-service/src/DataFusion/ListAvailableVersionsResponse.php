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

namespace Google\Service\DataFusion;

class ListAvailableVersionsResponse extends \Google\Collection
{
  protected $collection_key = 'versions';
  protected $availableVersionsType = Version::class;
  protected $availableVersionsDataType = 'array';
  /**
   * Token to retrieve the next page of results or empty if there are no more
   * results in the list.
   *
   * @var string
   */
  public $nextPageToken;
  protected $versionsType = Version::class;
  protected $versionsDataType = 'array';

  /**
   * Represents a list of versions that are supported. Deprecated: Use versions
   * field instead.
   *
   * @deprecated
   * @param Version[] $availableVersions
   */
  public function setAvailableVersions($availableVersions)
  {
    $this->availableVersions = $availableVersions;
  }
  /**
   * @deprecated
   * @return Version[]
   */
  public function getAvailableVersions()
  {
    return $this->availableVersions;
  }
  /**
   * Token to retrieve the next page of results or empty if there are no more
   * results in the list.
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
   * Represents a list of all versions.
   *
   * @param Version[] $versions
   */
  public function setVersions($versions)
  {
    $this->versions = $versions;
  }
  /**
   * @return Version[]
   */
  public function getVersions()
  {
    return $this->versions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListAvailableVersionsResponse::class, 'Google_Service_DataFusion_ListAvailableVersionsResponse');
