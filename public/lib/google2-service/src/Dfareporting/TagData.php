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

class TagData extends \Google\Model
{
  public const FORMAT_PLACEMENT_TAG_STANDARD = 'PLACEMENT_TAG_STANDARD';
  public const FORMAT_PLACEMENT_TAG_IFRAME_JAVASCRIPT = 'PLACEMENT_TAG_IFRAME_JAVASCRIPT';
  public const FORMAT_PLACEMENT_TAG_IFRAME_ILAYER = 'PLACEMENT_TAG_IFRAME_ILAYER';
  public const FORMAT_PLACEMENT_TAG_INTERNAL_REDIRECT = 'PLACEMENT_TAG_INTERNAL_REDIRECT';
  public const FORMAT_PLACEMENT_TAG_JAVASCRIPT = 'PLACEMENT_TAG_JAVASCRIPT';
  public const FORMAT_PLACEMENT_TAG_INTERSTITIAL_IFRAME_JAVASCRIPT = 'PLACEMENT_TAG_INTERSTITIAL_IFRAME_JAVASCRIPT';
  public const FORMAT_PLACEMENT_TAG_INTERSTITIAL_INTERNAL_REDIRECT = 'PLACEMENT_TAG_INTERSTITIAL_INTERNAL_REDIRECT';
  public const FORMAT_PLACEMENT_TAG_INTERSTITIAL_JAVASCRIPT = 'PLACEMENT_TAG_INTERSTITIAL_JAVASCRIPT';
  public const FORMAT_PLACEMENT_TAG_CLICK_COMMANDS = 'PLACEMENT_TAG_CLICK_COMMANDS';
  public const FORMAT_PLACEMENT_TAG_INSTREAM_VIDEO_PREFETCH = 'PLACEMENT_TAG_INSTREAM_VIDEO_PREFETCH';
  public const FORMAT_PLACEMENT_TAG_TRACKING = 'PLACEMENT_TAG_TRACKING';
  public const FORMAT_PLACEMENT_TAG_TRACKING_IFRAME = 'PLACEMENT_TAG_TRACKING_IFRAME';
  public const FORMAT_PLACEMENT_TAG_TRACKING_JAVASCRIPT = 'PLACEMENT_TAG_TRACKING_JAVASCRIPT';
  public const FORMAT_PLACEMENT_TAG_INSTREAM_VIDEO_PREFETCH_VAST_3 = 'PLACEMENT_TAG_INSTREAM_VIDEO_PREFETCH_VAST_3';
  public const FORMAT_PLACEMENT_TAG_IFRAME_JAVASCRIPT_LEGACY = 'PLACEMENT_TAG_IFRAME_JAVASCRIPT_LEGACY';
  public const FORMAT_PLACEMENT_TAG_JAVASCRIPT_LEGACY = 'PLACEMENT_TAG_JAVASCRIPT_LEGACY';
  public const FORMAT_PLACEMENT_TAG_INTERSTITIAL_IFRAME_JAVASCRIPT_LEGACY = 'PLACEMENT_TAG_INTERSTITIAL_IFRAME_JAVASCRIPT_LEGACY';
  public const FORMAT_PLACEMENT_TAG_INTERSTITIAL_JAVASCRIPT_LEGACY = 'PLACEMENT_TAG_INTERSTITIAL_JAVASCRIPT_LEGACY';
  public const FORMAT_PLACEMENT_TAG_INSTREAM_VIDEO_PREFETCH_VAST_4 = 'PLACEMENT_TAG_INSTREAM_VIDEO_PREFETCH_VAST_4';
  public const FORMAT_PLACEMENT_TAG_TRACKING_THIRD_PARTY_MEASUREMENT = 'PLACEMENT_TAG_TRACKING_THIRD_PARTY_MEASUREMENT';
  /**
   * Ad associated with this placement tag. Applicable only when format is
   * PLACEMENT_TAG_TRACKING.
   *
   * @var string
   */
  public $adId;
  /**
   * Tag string to record a click.
   *
   * @var string
   */
  public $clickTag;
  /**
   * Creative associated with this placement tag. Applicable only when format is
   * PLACEMENT_TAG_TRACKING.
   *
   * @var string
   */
  public $creativeId;
  /**
   * TagData tag format of this tag.
   *
   * @var string
   */
  public $format;
  /**
   * Tag string for serving an ad.
   *
   * @var string
   */
  public $impressionTag;

  /**
   * Ad associated with this placement tag. Applicable only when format is
   * PLACEMENT_TAG_TRACKING.
   *
   * @param string $adId
   */
  public function setAdId($adId)
  {
    $this->adId = $adId;
  }
  /**
   * @return string
   */
  public function getAdId()
  {
    return $this->adId;
  }
  /**
   * Tag string to record a click.
   *
   * @param string $clickTag
   */
  public function setClickTag($clickTag)
  {
    $this->clickTag = $clickTag;
  }
  /**
   * @return string
   */
  public function getClickTag()
  {
    return $this->clickTag;
  }
  /**
   * Creative associated with this placement tag. Applicable only when format is
   * PLACEMENT_TAG_TRACKING.
   *
   * @param string $creativeId
   */
  public function setCreativeId($creativeId)
  {
    $this->creativeId = $creativeId;
  }
  /**
   * @return string
   */
  public function getCreativeId()
  {
    return $this->creativeId;
  }
  /**
   * TagData tag format of this tag.
   *
   * Accepted values: PLACEMENT_TAG_STANDARD, PLACEMENT_TAG_IFRAME_JAVASCRIPT,
   * PLACEMENT_TAG_IFRAME_ILAYER, PLACEMENT_TAG_INTERNAL_REDIRECT,
   * PLACEMENT_TAG_JAVASCRIPT, PLACEMENT_TAG_INTERSTITIAL_IFRAME_JAVASCRIPT,
   * PLACEMENT_TAG_INTERSTITIAL_INTERNAL_REDIRECT,
   * PLACEMENT_TAG_INTERSTITIAL_JAVASCRIPT, PLACEMENT_TAG_CLICK_COMMANDS,
   * PLACEMENT_TAG_INSTREAM_VIDEO_PREFETCH, PLACEMENT_TAG_TRACKING,
   * PLACEMENT_TAG_TRACKING_IFRAME, PLACEMENT_TAG_TRACKING_JAVASCRIPT,
   * PLACEMENT_TAG_INSTREAM_VIDEO_PREFETCH_VAST_3,
   * PLACEMENT_TAG_IFRAME_JAVASCRIPT_LEGACY, PLACEMENT_TAG_JAVASCRIPT_LEGACY,
   * PLACEMENT_TAG_INTERSTITIAL_IFRAME_JAVASCRIPT_LEGACY,
   * PLACEMENT_TAG_INTERSTITIAL_JAVASCRIPT_LEGACY,
   * PLACEMENT_TAG_INSTREAM_VIDEO_PREFETCH_VAST_4,
   * PLACEMENT_TAG_TRACKING_THIRD_PARTY_MEASUREMENT
   *
   * @param self::FORMAT_* $format
   */
  public function setFormat($format)
  {
    $this->format = $format;
  }
  /**
   * @return self::FORMAT_*
   */
  public function getFormat()
  {
    return $this->format;
  }
  /**
   * Tag string for serving an ad.
   *
   * @param string $impressionTag
   */
  public function setImpressionTag($impressionTag)
  {
    $this->impressionTag = $impressionTag;
  }
  /**
   * @return string
   */
  public function getImpressionTag()
  {
    return $this->impressionTag;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TagData::class, 'Google_Service_Dfareporting_TagData');
