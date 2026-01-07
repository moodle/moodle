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

class NativeContentPositionAssignedTargetingOptionDetails extends \Google\Model
{
  /**
   * Native content position is not specified in this version. This enum is a
   * place holder for a default value and does not represent a real native
   * content position.
   */
  public const CONTENT_POSITION_NATIVE_CONTENT_POSITION_UNSPECIFIED = 'NATIVE_CONTENT_POSITION_UNSPECIFIED';
  /**
   * The native content position is unknown.
   */
  public const CONTENT_POSITION_NATIVE_CONTENT_POSITION_UNKNOWN = 'NATIVE_CONTENT_POSITION_UNKNOWN';
  /**
   * Native content position is in-article, i.e., ads appear between the
   * paragraphs of pages.
   */
  public const CONTENT_POSITION_NATIVE_CONTENT_POSITION_IN_ARTICLE = 'NATIVE_CONTENT_POSITION_IN_ARTICLE';
  /**
   * Native content position is in-feed, i.e., ads appear in a scrollable stream
   * of content. A feed is typically editorial (e.g. a list of articles or news)
   * or listings (e.g. a list of products or services).
   */
  public const CONTENT_POSITION_NATIVE_CONTENT_POSITION_IN_FEED = 'NATIVE_CONTENT_POSITION_IN_FEED';
  /**
   * Native content position is peripheral, i.e., ads appear outside of core
   * content on pages, such as the right- or left-hand side of the page.
   */
  public const CONTENT_POSITION_NATIVE_CONTENT_POSITION_PERIPHERAL = 'NATIVE_CONTENT_POSITION_PERIPHERAL';
  /**
   * Native content position is recommendation, i.e., ads appear in sections for
   * recommended content.
   */
  public const CONTENT_POSITION_NATIVE_CONTENT_POSITION_RECOMMENDATION = 'NATIVE_CONTENT_POSITION_RECOMMENDATION';
  /**
   * Required. The content position.
   *
   * @var string
   */
  public $contentPosition;

  /**
   * Required. The content position.
   *
   * Accepted values: NATIVE_CONTENT_POSITION_UNSPECIFIED,
   * NATIVE_CONTENT_POSITION_UNKNOWN, NATIVE_CONTENT_POSITION_IN_ARTICLE,
   * NATIVE_CONTENT_POSITION_IN_FEED, NATIVE_CONTENT_POSITION_PERIPHERAL,
   * NATIVE_CONTENT_POSITION_RECOMMENDATION
   *
   * @param self::CONTENT_POSITION_* $contentPosition
   */
  public function setContentPosition($contentPosition)
  {
    $this->contentPosition = $contentPosition;
  }
  /**
   * @return self::CONTENT_POSITION_*
   */
  public function getContentPosition()
  {
    return $this->contentPosition;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NativeContentPositionAssignedTargetingOptionDetails::class, 'Google_Service_DisplayVideo_NativeContentPositionAssignedTargetingOptionDetails');
