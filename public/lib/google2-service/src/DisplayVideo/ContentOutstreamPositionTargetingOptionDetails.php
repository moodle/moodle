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

class ContentOutstreamPositionTargetingOptionDetails extends \Google\Model
{
  /**
   * Content outstream position is not specified in this version. This enum is a
   * place holder for a default value and does not represent a real content
   * outstream position.
   */
  public const CONTENT_OUTSTREAM_POSITION_CONTENT_OUTSTREAM_POSITION_UNSPECIFIED = 'CONTENT_OUTSTREAM_POSITION_UNSPECIFIED';
  /**
   * The ad position is unknown in the content outstream.
   */
  public const CONTENT_OUTSTREAM_POSITION_CONTENT_OUTSTREAM_POSITION_UNKNOWN = 'CONTENT_OUTSTREAM_POSITION_UNKNOWN';
  /**
   * Ads that appear between the paragraphs of your pages.
   */
  public const CONTENT_OUTSTREAM_POSITION_CONTENT_OUTSTREAM_POSITION_IN_ARTICLE = 'CONTENT_OUTSTREAM_POSITION_IN_ARTICLE';
  /**
   * Ads that display on the top and the sides of a page.
   */
  public const CONTENT_OUTSTREAM_POSITION_CONTENT_OUTSTREAM_POSITION_IN_BANNER = 'CONTENT_OUTSTREAM_POSITION_IN_BANNER';
  /**
   * Ads that appear in a scrollable stream of content. A feed is typically
   * editorial (e.g. a list of articles or news) or listings (e.g. a list of
   * products or services).
   */
  public const CONTENT_OUTSTREAM_POSITION_CONTENT_OUTSTREAM_POSITION_IN_FEED = 'CONTENT_OUTSTREAM_POSITION_IN_FEED';
  /**
   * Ads shown before or between content loads.
   */
  public const CONTENT_OUTSTREAM_POSITION_CONTENT_OUTSTREAM_POSITION_INTERSTITIAL = 'CONTENT_OUTSTREAM_POSITION_INTERSTITIAL';
  /**
   * Output only. The content outstream position.
   *
   * @var string
   */
  public $contentOutstreamPosition;

  /**
   * Output only. The content outstream position.
   *
   * Accepted values: CONTENT_OUTSTREAM_POSITION_UNSPECIFIED,
   * CONTENT_OUTSTREAM_POSITION_UNKNOWN, CONTENT_OUTSTREAM_POSITION_IN_ARTICLE,
   * CONTENT_OUTSTREAM_POSITION_IN_BANNER, CONTENT_OUTSTREAM_POSITION_IN_FEED,
   * CONTENT_OUTSTREAM_POSITION_INTERSTITIAL
   *
   * @param self::CONTENT_OUTSTREAM_POSITION_* $contentOutstreamPosition
   */
  public function setContentOutstreamPosition($contentOutstreamPosition)
  {
    $this->contentOutstreamPosition = $contentOutstreamPosition;
  }
  /**
   * @return self::CONTENT_OUTSTREAM_POSITION_*
   */
  public function getContentOutstreamPosition()
  {
    return $this->contentOutstreamPosition;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContentOutstreamPositionTargetingOptionDetails::class, 'Google_Service_DisplayVideo_ContentOutstreamPositionTargetingOptionDetails');
