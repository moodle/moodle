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

namespace Google\Service\CloudAsset;

class ListAssetsResponse extends \Google\Collection
{
  protected $collection_key = 'assets';
  protected $assetsType = Asset::class;
  protected $assetsDataType = 'array';
  /**
   * Token to retrieve the next page of results. It expires 72 hours after the
   * page token for the first page is generated. Set to empty if there are no
   * remaining results.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Time the snapshot was taken.
   *
   * @var string
   */
  public $readTime;

  /**
   * Assets.
   *
   * @param Asset[] $assets
   */
  public function setAssets($assets)
  {
    $this->assets = $assets;
  }
  /**
   * @return Asset[]
   */
  public function getAssets()
  {
    return $this->assets;
  }
  /**
   * Token to retrieve the next page of results. It expires 72 hours after the
   * page token for the first page is generated. Set to empty if there are no
   * remaining results.
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
   * Time the snapshot was taken.
   *
   * @param string $readTime
   */
  public function setReadTime($readTime)
  {
    $this->readTime = $readTime;
  }
  /**
   * @return string
   */
  public function getReadTime()
  {
    return $this->readTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListAssetsResponse::class, 'Google_Service_CloudAsset_ListAssetsResponse');
