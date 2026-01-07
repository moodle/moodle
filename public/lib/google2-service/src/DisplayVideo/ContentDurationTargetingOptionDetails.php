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

class ContentDurationTargetingOptionDetails extends \Google\Model
{
  /**
   * Content duration is not specified in this version. This enum is a place
   * holder for a default value and does not represent a real content duration.
   */
  public const CONTENT_DURATION_CONTENT_DURATION_UNSPECIFIED = 'CONTENT_DURATION_UNSPECIFIED';
  /**
   * The content duration is unknown.
   */
  public const CONTENT_DURATION_CONTENT_DURATION_UNKNOWN = 'CONTENT_DURATION_UNKNOWN';
  /**
   * Content is 0-1 minute long.
   */
  public const CONTENT_DURATION_CONTENT_DURATION_0_TO_1_MIN = 'CONTENT_DURATION_0_TO_1_MIN';
  /**
   * Content is 1-5 minutes long.
   */
  public const CONTENT_DURATION_CONTENT_DURATION_1_TO_5_MIN = 'CONTENT_DURATION_1_TO_5_MIN';
  /**
   * Content is 5-15 minutes long.
   */
  public const CONTENT_DURATION_CONTENT_DURATION_5_TO_15_MIN = 'CONTENT_DURATION_5_TO_15_MIN';
  /**
   * Content is 15-30 minutes long.
   */
  public const CONTENT_DURATION_CONTENT_DURATION_15_TO_30_MIN = 'CONTENT_DURATION_15_TO_30_MIN';
  /**
   * Content is 30-60 minutes long.
   */
  public const CONTENT_DURATION_CONTENT_DURATION_30_TO_60_MIN = 'CONTENT_DURATION_30_TO_60_MIN';
  /**
   * Content is over 60 minutes long.
   */
  public const CONTENT_DURATION_CONTENT_DURATION_OVER_60_MIN = 'CONTENT_DURATION_OVER_60_MIN';
  /**
   * Output only. The content duration.
   *
   * @var string
   */
  public $contentDuration;

  /**
   * Output only. The content duration.
   *
   * Accepted values: CONTENT_DURATION_UNSPECIFIED, CONTENT_DURATION_UNKNOWN,
   * CONTENT_DURATION_0_TO_1_MIN, CONTENT_DURATION_1_TO_5_MIN,
   * CONTENT_DURATION_5_TO_15_MIN, CONTENT_DURATION_15_TO_30_MIN,
   * CONTENT_DURATION_30_TO_60_MIN, CONTENT_DURATION_OVER_60_MIN
   *
   * @param self::CONTENT_DURATION_* $contentDuration
   */
  public function setContentDuration($contentDuration)
  {
    $this->contentDuration = $contentDuration;
  }
  /**
   * @return self::CONTENT_DURATION_*
   */
  public function getContentDuration()
  {
    return $this->contentDuration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContentDurationTargetingOptionDetails::class, 'Google_Service_DisplayVideo_ContentDurationTargetingOptionDetails');
