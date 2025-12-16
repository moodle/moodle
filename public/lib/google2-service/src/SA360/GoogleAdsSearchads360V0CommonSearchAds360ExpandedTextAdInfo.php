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

class GoogleAdsSearchads360V0CommonSearchAds360ExpandedTextAdInfo extends \Google\Model
{
  /**
   * The tracking id of the ad.
   *
   * @var string
   */
  public $adTrackingId;
  /**
   * The first line of the ad's description.
   *
   * @var string
   */
  public $description1;
  /**
   * The second line of the ad's description.
   *
   * @var string
   */
  public $description2;
  /**
   * The headline of the ad.
   *
   * @var string
   */
  public $headline;
  /**
   * The second headline of the ad.
   *
   * @var string
   */
  public $headline2;
  /**
   * The third headline of the ad.
   *
   * @var string
   */
  public $headline3;
  /**
   * Text appended to the auto-generated visible URL with a delimiter.
   *
   * @var string
   */
  public $path1;
  /**
   * Text appended to path1 with a delimiter.
   *
   * @var string
   */
  public $path2;

  /**
   * The tracking id of the ad.
   *
   * @param string $adTrackingId
   */
  public function setAdTrackingId($adTrackingId)
  {
    $this->adTrackingId = $adTrackingId;
  }
  /**
   * @return string
   */
  public function getAdTrackingId()
  {
    return $this->adTrackingId;
  }
  /**
   * The first line of the ad's description.
   *
   * @param string $description1
   */
  public function setDescription1($description1)
  {
    $this->description1 = $description1;
  }
  /**
   * @return string
   */
  public function getDescription1()
  {
    return $this->description1;
  }
  /**
   * The second line of the ad's description.
   *
   * @param string $description2
   */
  public function setDescription2($description2)
  {
    $this->description2 = $description2;
  }
  /**
   * @return string
   */
  public function getDescription2()
  {
    return $this->description2;
  }
  /**
   * The headline of the ad.
   *
   * @param string $headline
   */
  public function setHeadline($headline)
  {
    $this->headline = $headline;
  }
  /**
   * @return string
   */
  public function getHeadline()
  {
    return $this->headline;
  }
  /**
   * The second headline of the ad.
   *
   * @param string $headline2
   */
  public function setHeadline2($headline2)
  {
    $this->headline2 = $headline2;
  }
  /**
   * @return string
   */
  public function getHeadline2()
  {
    return $this->headline2;
  }
  /**
   * The third headline of the ad.
   *
   * @param string $headline3
   */
  public function setHeadline3($headline3)
  {
    $this->headline3 = $headline3;
  }
  /**
   * @return string
   */
  public function getHeadline3()
  {
    return $this->headline3;
  }
  /**
   * Text appended to the auto-generated visible URL with a delimiter.
   *
   * @param string $path1
   */
  public function setPath1($path1)
  {
    $this->path1 = $path1;
  }
  /**
   * @return string
   */
  public function getPath1()
  {
    return $this->path1;
  }
  /**
   * Text appended to path1 with a delimiter.
   *
   * @param string $path2
   */
  public function setPath2($path2)
  {
    $this->path2 = $path2;
  }
  /**
   * @return string
   */
  public function getPath2()
  {
    return $this->path2;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0CommonSearchAds360ExpandedTextAdInfo::class, 'Google_Service_SA360_GoogleAdsSearchads360V0CommonSearchAds360ExpandedTextAdInfo');
