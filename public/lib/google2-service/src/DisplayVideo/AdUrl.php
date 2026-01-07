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

namespace Google\Service\DisplayVideo;

class AdUrl extends \Google\Model
{
  /**
   * Unknown or unspecified.
   */
  public const TYPE_AD_URL_TYPE_UNSPECIFIED = 'AD_URL_TYPE_UNSPECIFIED';
  /**
   * A 1x1 tracking pixel to ping when an impression of a creative is delivered.
   */
  public const TYPE_AD_URL_TYPE_BEACON_IMPRESSION = 'AD_URL_TYPE_BEACON_IMPRESSION';
  /**
   * Expandable DCM impression beacon. At serving time, it is expanded to
   * several beacons.
   */
  public const TYPE_AD_URL_TYPE_BEACON_EXPANDABLE_DCM_IMPRESSION = 'AD_URL_TYPE_BEACON_EXPANDABLE_DCM_IMPRESSION';
  /**
   * Tracking URL to ping when the click event is triggered.
   */
  public const TYPE_AD_URL_TYPE_BEACON_CLICK = 'AD_URL_TYPE_BEACON_CLICK';
  /**
   * Tracking URL to ping when the skip event is triggered.
   */
  public const TYPE_AD_URL_TYPE_BEACON_SKIP = 'AD_URL_TYPE_BEACON_SKIP';
  /**
   * The type of the Ad URL.
   *
   * @var string
   */
  public $type;
  /**
   * The URL string value.
   *
   * @var string
   */
  public $url;

  /**
   * The type of the Ad URL.
   *
   * Accepted values: AD_URL_TYPE_UNSPECIFIED, AD_URL_TYPE_BEACON_IMPRESSION,
   * AD_URL_TYPE_BEACON_EXPANDABLE_DCM_IMPRESSION, AD_URL_TYPE_BEACON_CLICK,
   * AD_URL_TYPE_BEACON_SKIP
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * The URL string value.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdUrl::class, 'Google_Service_DisplayVideo_AdUrl');
