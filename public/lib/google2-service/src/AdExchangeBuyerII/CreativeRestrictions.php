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

class CreativeRestrictions extends \Google\Collection
{
  /**
   * A placeholder for an undefined creative format.
   */
  public const CREATIVE_FORMAT_CREATIVE_FORMAT_UNSPECIFIED = 'CREATIVE_FORMAT_UNSPECIFIED';
  /**
   * A creative that will be displayed in environments such as a browser.
   */
  public const CREATIVE_FORMAT_DISPLAY = 'DISPLAY';
  /**
   * A video creative that will be displayed in environments such as a video
   * player.
   */
  public const CREATIVE_FORMAT_VIDEO = 'VIDEO';
  /**
   * A placeholder for an undefined skippable ad type.
   */
  public const SKIPPABLE_AD_TYPE_SKIPPABLE_AD_TYPE_UNSPECIFIED = 'SKIPPABLE_AD_TYPE_UNSPECIFIED';
  /**
   * This video ad can be skipped after 5 seconds.
   */
  public const SKIPPABLE_AD_TYPE_SKIPPABLE = 'SKIPPABLE';
  /**
   * This video ad can be skipped after 5 seconds, and is counted as engaged
   * view after 30 seconds. The creative is hosted on YouTube only, and
   * viewcount of the YouTube video increments after the engaged view.
   */
  public const SKIPPABLE_AD_TYPE_INSTREAM_SELECT = 'INSTREAM_SELECT';
  /**
   * This video ad is not skippable.
   */
  public const SKIPPABLE_AD_TYPE_NOT_SKIPPABLE = 'NOT_SKIPPABLE';
  protected $collection_key = 'creativeSpecifications';
  /**
   * The format of the environment that the creatives will be displayed in.
   *
   * @var string
   */
  public $creativeFormat;
  protected $creativeSpecificationsType = CreativeSpecification::class;
  protected $creativeSpecificationsDataType = 'array';
  /**
   * Skippable video ads allow viewers to skip ads after 5 seconds.
   *
   * @var string
   */
  public $skippableAdType;

  /**
   * The format of the environment that the creatives will be displayed in.
   *
   * Accepted values: CREATIVE_FORMAT_UNSPECIFIED, DISPLAY, VIDEO
   *
   * @param self::CREATIVE_FORMAT_* $creativeFormat
   */
  public function setCreativeFormat($creativeFormat)
  {
    $this->creativeFormat = $creativeFormat;
  }
  /**
   * @return self::CREATIVE_FORMAT_*
   */
  public function getCreativeFormat()
  {
    return $this->creativeFormat;
  }
  /**
   * @param CreativeSpecification[] $creativeSpecifications
   */
  public function setCreativeSpecifications($creativeSpecifications)
  {
    $this->creativeSpecifications = $creativeSpecifications;
  }
  /**
   * @return CreativeSpecification[]
   */
  public function getCreativeSpecifications()
  {
    return $this->creativeSpecifications;
  }
  /**
   * Skippable video ads allow viewers to skip ads after 5 seconds.
   *
   * Accepted values: SKIPPABLE_AD_TYPE_UNSPECIFIED, SKIPPABLE, INSTREAM_SELECT,
   * NOT_SKIPPABLE
   *
   * @param self::SKIPPABLE_AD_TYPE_* $skippableAdType
   */
  public function setSkippableAdType($skippableAdType)
  {
    $this->skippableAdType = $skippableAdType;
  }
  /**
   * @return self::SKIPPABLE_AD_TYPE_*
   */
  public function getSkippableAdType()
  {
    return $this->skippableAdType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreativeRestrictions::class, 'Google_Service_AdExchangeBuyerII_CreativeRestrictions');
