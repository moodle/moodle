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

namespace Google\Service\TagManager;

class ListGtagConfigResponse extends \Google\Collection
{
  protected $collection_key = 'gtagConfig';
  protected $gtagConfigType = GtagConfig::class;
  protected $gtagConfigDataType = 'array';
  /**
   * Continuation token for fetching the next page of results.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * All Google tag configs in a Container.
   *
   * @param GtagConfig[] $gtagConfig
   */
  public function setGtagConfig($gtagConfig)
  {
    $this->gtagConfig = $gtagConfig;
  }
  /**
   * @return GtagConfig[]
   */
  public function getGtagConfig()
  {
    return $this->gtagConfig;
  }
  /**
   * Continuation token for fetching the next page of results.
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
class_alias(ListGtagConfigResponse::class, 'Google_Service_TagManager_ListGtagConfigResponse');
