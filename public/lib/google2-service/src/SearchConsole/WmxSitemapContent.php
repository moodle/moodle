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

namespace Google\Service\SearchConsole;

class WmxSitemapContent extends \Google\Model
{
  public const TYPE_WEB = 'WEB';
  public const TYPE_IMAGE = 'IMAGE';
  public const TYPE_VIDEO = 'VIDEO';
  public const TYPE_NEWS = 'NEWS';
  public const TYPE_MOBILE = 'MOBILE';
  public const TYPE_ANDROID_APP = 'ANDROID_APP';
  /**
   * Unsupported content type.
   *
   * @deprecated
   */
  public const TYPE_PATTERN = 'PATTERN';
  public const TYPE_IOS_APP = 'IOS_APP';
  /**
   * Unsupported content type.
   *
   * @deprecated
   */
  public const TYPE_DATA_FEED_ELEMENT = 'DATA_FEED_ELEMENT';
  /**
   * *Deprecated; do not use.*
   *
   * @deprecated
   * @var string
   */
  public $indexed;
  /**
   * The number of URLs in the sitemap (of the content type).
   *
   * @var string
   */
  public $submitted;
  /**
   * The specific type of content in this sitemap. For example: `web`.
   *
   * @var string
   */
  public $type;

  /**
   * *Deprecated; do not use.*
   *
   * @deprecated
   * @param string $indexed
   */
  public function setIndexed($indexed)
  {
    $this->indexed = $indexed;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getIndexed()
  {
    return $this->indexed;
  }
  /**
   * The number of URLs in the sitemap (of the content type).
   *
   * @param string $submitted
   */
  public function setSubmitted($submitted)
  {
    $this->submitted = $submitted;
  }
  /**
   * @return string
   */
  public function getSubmitted()
  {
    return $this->submitted;
  }
  /**
   * The specific type of content in this sitemap. For example: `web`.
   *
   * Accepted values: WEB, IMAGE, VIDEO, NEWS, MOBILE, ANDROID_APP, PATTERN,
   * IOS_APP, DATA_FEED_ELEMENT
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
class_alias(WmxSitemapContent::class, 'Google_Service_SearchConsole_WmxSitemapContent');
