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

namespace Google\Service\AdExchangeBuyerII;

class ServingContext extends \Google\Model
{
  /**
   * A simple context.
   */
  public const ALL_SIMPLE_CONTEXT = 'SIMPLE_CONTEXT';
  /**
   * Matches all contexts.
   *
   * @var string
   */
  public $all;
  protected $appTypeType = AppContext::class;
  protected $appTypeDataType = '';
  protected $auctionTypeType = AuctionContext::class;
  protected $auctionTypeDataType = '';
  protected $locationType = LocationContext::class;
  protected $locationDataType = '';
  protected $platformType = PlatformContext::class;
  protected $platformDataType = '';
  protected $securityTypeType = SecurityContext::class;
  protected $securityTypeDataType = '';

  /**
   * Matches all contexts.
   *
   * Accepted values: SIMPLE_CONTEXT
   *
   * @param self::ALL_* $all
   */
  public function setAll($all)
  {
    $this->all = $all;
  }
  /**
   * @return self::ALL_*
   */
  public function getAll()
  {
    return $this->all;
  }
  /**
   * Matches impressions for a particular app type.
   *
   * @param AppContext $appType
   */
  public function setAppType(AppContext $appType)
  {
    $this->appType = $appType;
  }
  /**
   * @return AppContext
   */
  public function getAppType()
  {
    return $this->appType;
  }
  /**
   * Matches impressions for a particular auction type.
   *
   * @param AuctionContext $auctionType
   */
  public function setAuctionType(AuctionContext $auctionType)
  {
    $this->auctionType = $auctionType;
  }
  /**
   * @return AuctionContext
   */
  public function getAuctionType()
  {
    return $this->auctionType;
  }
  /**
   * Matches impressions coming from users *or* publishers in a specific
   * location.
   *
   * @param LocationContext $location
   */
  public function setLocation(LocationContext $location)
  {
    $this->location = $location;
  }
  /**
   * @return LocationContext
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Matches impressions coming from a particular platform.
   *
   * @param PlatformContext $platform
   */
  public function setPlatform(PlatformContext $platform)
  {
    $this->platform = $platform;
  }
  /**
   * @return PlatformContext
   */
  public function getPlatform()
  {
    return $this->platform;
  }
  /**
   * Matches impressions for a particular security type.
   *
   * @deprecated
   * @param SecurityContext $securityType
   */
  public function setSecurityType(SecurityContext $securityType)
  {
    $this->securityType = $securityType;
  }
  /**
   * @deprecated
   * @return SecurityContext
   */
  public function getSecurityType()
  {
    return $this->securityType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServingContext::class, 'Google_Service_AdExchangeBuyerII_ServingContext');
