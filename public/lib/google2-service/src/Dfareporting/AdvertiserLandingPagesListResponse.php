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

class AdvertiserLandingPagesListResponse extends \Google\Collection
{
  protected $collection_key = 'landingPages';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#advertiserLandingPagesListResponse".
   *
   * @var string
   */
  public $kind;
  protected $landingPagesType = LandingPage::class;
  protected $landingPagesDataType = 'array';
  /**
   * Pagination token to be used for the next list operation.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#advertiserLandingPagesListResponse".
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
   * Landing page collection
   *
   * @param LandingPage[] $landingPages
   */
  public function setLandingPages($landingPages)
  {
    $this->landingPages = $landingPages;
  }
  /**
   * @return LandingPage[]
   */
  public function getLandingPages()
  {
    return $this->landingPages;
  }
  /**
   * Pagination token to be used for the next list operation.
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
class_alias(AdvertiserLandingPagesListResponse::class, 'Google_Service_Dfareporting_AdvertiserLandingPagesListResponse');
