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

namespace Google\Service\Adsense;

class ContentAdsSettings extends \Google\Model
{
  /**
   * Unspecified ad unit type.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Display ad unit.
   */
  public const TYPE_DISPLAY = 'DISPLAY';
  /**
   * In-feed ad unit.
   */
  public const TYPE_FEED = 'FEED';
  /**
   * In-article ad unit.
   */
  public const TYPE_ARTICLE = 'ARTICLE';
  /**
   * Matched content unit.
   */
  public const TYPE_MATCHED_CONTENT = 'MATCHED_CONTENT';
  /**
   * Link ad unit. Note that link ad units have now been retired, see
   * https://support.google.com/adsense/answer/9987221.
   *
   * @deprecated
   */
  public const TYPE_LINK = 'LINK';
  /**
   * Required. Size of the ad unit. e.g. "728x90", "1x3" (for responsive ad
   * units).
   *
   * @var string
   */
  public $size;
  /**
   * Required. Type of the ad unit.
   *
   * @var string
   */
  public $type;

  /**
   * Required. Size of the ad unit. e.g. "728x90", "1x3" (for responsive ad
   * units).
   *
   * @param string $size
   */
  public function setSize($size)
  {
    $this->size = $size;
  }
  /**
   * @return string
   */
  public function getSize()
  {
    return $this->size;
  }
  /**
   * Required. Type of the ad unit.
   *
   * Accepted values: TYPE_UNSPECIFIED, DISPLAY, FEED, ARTICLE, MATCHED_CONTENT,
   * LINK
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContentAdsSettings::class, 'Google_Service_Adsense_ContentAdsSettings');
