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

class ContentInstreamPositionTargetingOptionDetails extends \Google\Model
{
  /**
   * Content instream position is not specified in this version. This enum is a
   * place holder for a default value and does not represent a real in stream ad
   * position.
   */
  public const CONTENT_INSTREAM_POSITION_CONTENT_INSTREAM_POSITION_UNSPECIFIED = 'CONTENT_INSTREAM_POSITION_UNSPECIFIED';
  /**
   * Ads that play before streaming content.
   */
  public const CONTENT_INSTREAM_POSITION_CONTENT_INSTREAM_POSITION_PRE_ROLL = 'CONTENT_INSTREAM_POSITION_PRE_ROLL';
  /**
   * Ads that play between the beginning and end of streaming content.
   */
  public const CONTENT_INSTREAM_POSITION_CONTENT_INSTREAM_POSITION_MID_ROLL = 'CONTENT_INSTREAM_POSITION_MID_ROLL';
  /**
   * Ads that play at the end of streaming content.
   */
  public const CONTENT_INSTREAM_POSITION_CONTENT_INSTREAM_POSITION_POST_ROLL = 'CONTENT_INSTREAM_POSITION_POST_ROLL';
  /**
   * Ads instream position is unknown.
   */
  public const CONTENT_INSTREAM_POSITION_CONTENT_INSTREAM_POSITION_UNKNOWN = 'CONTENT_INSTREAM_POSITION_UNKNOWN';
  /**
   * Output only. The content instream position.
   *
   * @var string
   */
  public $contentInstreamPosition;

  /**
   * Output only. The content instream position.
   *
   * Accepted values: CONTENT_INSTREAM_POSITION_UNSPECIFIED,
   * CONTENT_INSTREAM_POSITION_PRE_ROLL, CONTENT_INSTREAM_POSITION_MID_ROLL,
   * CONTENT_INSTREAM_POSITION_POST_ROLL, CONTENT_INSTREAM_POSITION_UNKNOWN
   *
   * @param self::CONTENT_INSTREAM_POSITION_* $contentInstreamPosition
   */
  public function setContentInstreamPosition($contentInstreamPosition)
  {
    $this->contentInstreamPosition = $contentInstreamPosition;
  }
  /**
   * @return self::CONTENT_INSTREAM_POSITION_*
   */
  public function getContentInstreamPosition()
  {
    return $this->contentInstreamPosition;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContentInstreamPositionTargetingOptionDetails::class, 'Google_Service_DisplayVideo_ContentInstreamPositionTargetingOptionDetails');
