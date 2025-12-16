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

namespace Google\Service\RealTimeBidding;

class AdvertiserAndBrand extends \Google\Model
{
  /**
   * See https://storage.googleapis.com/adx-rtb-dictionaries/advertisers.txt for
   * the list of possible values. Can be used to filter the response of the
   * creatives.list method.
   *
   * @var string
   */
  public $advertiserId;
  /**
   * Advertiser name. Can be used to filter the response of the creatives.list
   * method.
   *
   * @var string
   */
  public $advertiserName;
  /**
   * Detected brand ID or zero if no brand has been detected. See
   * https://storage.googleapis.com/adx-rtb-dictionaries/brands.txt for the list
   * of possible values. Can be used to filter the response of the
   * creatives.list method.
   *
   * @var string
   */
  public $brandId;
  /**
   * Brand name. Can be used to filter the response of the creatives.list
   * method.
   *
   * @var string
   */
  public $brandName;

  /**
   * See https://storage.googleapis.com/adx-rtb-dictionaries/advertisers.txt for
   * the list of possible values. Can be used to filter the response of the
   * creatives.list method.
   *
   * @param string $advertiserId
   */
  public function setAdvertiserId($advertiserId)
  {
    $this->advertiserId = $advertiserId;
  }
  /**
   * @return string
   */
  public function getAdvertiserId()
  {
    return $this->advertiserId;
  }
  /**
   * Advertiser name. Can be used to filter the response of the creatives.list
   * method.
   *
   * @param string $advertiserName
   */
  public function setAdvertiserName($advertiserName)
  {
    $this->advertiserName = $advertiserName;
  }
  /**
   * @return string
   */
  public function getAdvertiserName()
  {
    return $this->advertiserName;
  }
  /**
   * Detected brand ID or zero if no brand has been detected. See
   * https://storage.googleapis.com/adx-rtb-dictionaries/brands.txt for the list
   * of possible values. Can be used to filter the response of the
   * creatives.list method.
   *
   * @param string $brandId
   */
  public function setBrandId($brandId)
  {
    $this->brandId = $brandId;
  }
  /**
   * @return string
   */
  public function getBrandId()
  {
    return $this->brandId;
  }
  /**
   * Brand name. Can be used to filter the response of the creatives.list
   * method.
   *
   * @param string $brandName
   */
  public function setBrandName($brandName)
  {
    $this->brandName = $brandName;
  }
  /**
   * @return string
   */
  public function getBrandName()
  {
    return $this->brandName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdvertiserAndBrand::class, 'Google_Service_RealTimeBidding_AdvertiserAndBrand');
