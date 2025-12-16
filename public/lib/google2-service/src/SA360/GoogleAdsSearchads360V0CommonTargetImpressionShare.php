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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0CommonTargetImpressionShare extends \Google\Model
{
  /**
   * Not specified.
   */
  public const LOCATION_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const LOCATION_UNKNOWN = 'UNKNOWN';
  /**
   * Any location on the web page.
   */
  public const LOCATION_ANYWHERE_ON_PAGE = 'ANYWHERE_ON_PAGE';
  /**
   * Top box of ads.
   */
  public const LOCATION_TOP_OF_PAGE = 'TOP_OF_PAGE';
  /**
   * Top slot in the top box of ads.
   */
  public const LOCATION_ABSOLUTE_TOP_OF_PAGE = 'ABSOLUTE_TOP_OF_PAGE';
  /**
   * The highest CPC bid the automated bidding system is permitted to specify.
   * This is a required field entered by the advertiser that sets the ceiling
   * and specified in local micros.
   *
   * @var string
   */
  public $cpcBidCeilingMicros;
  /**
   * The targeted location on the search results page.
   *
   * @var string
   */
  public $location;
  /**
   * The chosen fraction of ads to be shown in the targeted location in micros.
   * For example, 1% equals 10,000.
   *
   * @var string
   */
  public $locationFractionMicros;

  /**
   * The highest CPC bid the automated bidding system is permitted to specify.
   * This is a required field entered by the advertiser that sets the ceiling
   * and specified in local micros.
   *
   * @param string $cpcBidCeilingMicros
   */
  public function setCpcBidCeilingMicros($cpcBidCeilingMicros)
  {
    $this->cpcBidCeilingMicros = $cpcBidCeilingMicros;
  }
  /**
   * @return string
   */
  public function getCpcBidCeilingMicros()
  {
    return $this->cpcBidCeilingMicros;
  }
  /**
   * The targeted location on the search results page.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, ANYWHERE_ON_PAGE, TOP_OF_PAGE,
   * ABSOLUTE_TOP_OF_PAGE
   *
   * @param self::LOCATION_* $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return self::LOCATION_*
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * The chosen fraction of ads to be shown in the targeted location in micros.
   * For example, 1% equals 10,000.
   *
   * @param string $locationFractionMicros
   */
  public function setLocationFractionMicros($locationFractionMicros)
  {
    $this->locationFractionMicros = $locationFractionMicros;
  }
  /**
   * @return string
   */
  public function getLocationFractionMicros()
  {
    return $this->locationFractionMicros;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0CommonTargetImpressionShare::class, 'Google_Service_SA360_GoogleAdsSearchads360V0CommonTargetImpressionShare');
