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

class ContentStreamTypeAssignedTargetingOptionDetails extends \Google\Model
{
  /**
   * Content stream type is not specified in this version. This enum is a place
   * holder for a default value and does not represent a real content stream
   * type.
   */
  public const CONTENT_STREAM_TYPE_CONTENT_STREAM_TYPE_UNSPECIFIED = 'CONTENT_STREAM_TYPE_UNSPECIFIED';
  /**
   * The content is being live-streamed.
   */
  public const CONTENT_STREAM_TYPE_CONTENT_LIVE_STREAM = 'CONTENT_LIVE_STREAM';
  /**
   * The content is viewed on-demand.
   */
  public const CONTENT_STREAM_TYPE_CONTENT_ON_DEMAND = 'CONTENT_ON_DEMAND';
  /**
   * Output only. The content stream type.
   *
   * @var string
   */
  public $contentStreamType;
  /**
   * Required. The targeting_option_id field when targeting_type is
   * `TARGETING_TYPE_CONTENT_STREAM_TYPE`.
   *
   * @var string
   */
  public $targetingOptionId;

  /**
   * Output only. The content stream type.
   *
   * Accepted values: CONTENT_STREAM_TYPE_UNSPECIFIED, CONTENT_LIVE_STREAM,
   * CONTENT_ON_DEMAND
   *
   * @param self::CONTENT_STREAM_TYPE_* $contentStreamType
   */
  public function setContentStreamType($contentStreamType)
  {
    $this->contentStreamType = $contentStreamType;
  }
  /**
   * @return self::CONTENT_STREAM_TYPE_*
   */
  public function getContentStreamType()
  {
    return $this->contentStreamType;
  }
  /**
   * Required. The targeting_option_id field when targeting_type is
   * `TARGETING_TYPE_CONTENT_STREAM_TYPE`.
   *
   * @param string $targetingOptionId
   */
  public function setTargetingOptionId($targetingOptionId)
  {
    $this->targetingOptionId = $targetingOptionId;
  }
  /**
   * @return string
   */
  public function getTargetingOptionId()
  {
    return $this->targetingOptionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContentStreamTypeAssignedTargetingOptionDetails::class, 'Google_Service_DisplayVideo_ContentStreamTypeAssignedTargetingOptionDetails');
