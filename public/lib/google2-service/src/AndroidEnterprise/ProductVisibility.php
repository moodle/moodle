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

namespace Google\Service\AndroidEnterprise;

class ProductVisibility extends \Google\Collection
{
  protected $collection_key = 'tracks';
  /**
   * The product ID to make visible to the user. Required for each item in the
   * productVisibility list.
   *
   * @var string
   */
  public $productId;
  /**
   * Grants the user visibility to the specified product track(s), identified by
   * trackIds.
   *
   * @var string[]
   */
  public $trackIds;
  /**
   * Deprecated. Use trackIds instead.
   *
   * @var string[]
   */
  public $tracks;

  /**
   * The product ID to make visible to the user. Required for each item in the
   * productVisibility list.
   *
   * @param string $productId
   */
  public function setProductId($productId)
  {
    $this->productId = $productId;
  }
  /**
   * @return string
   */
  public function getProductId()
  {
    return $this->productId;
  }
  /**
   * Grants the user visibility to the specified product track(s), identified by
   * trackIds.
   *
   * @param string[] $trackIds
   */
  public function setTrackIds($trackIds)
  {
    $this->trackIds = $trackIds;
  }
  /**
   * @return string[]
   */
  public function getTrackIds()
  {
    return $this->trackIds;
  }
  /**
   * Deprecated. Use trackIds instead.
   *
   * @param string[] $tracks
   */
  public function setTracks($tracks)
  {
    $this->tracks = $tracks;
  }
  /**
   * @return string[]
   */
  public function getTracks()
  {
    return $this->tracks;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductVisibility::class, 'Google_Service_AndroidEnterprise_ProductVisibility');
