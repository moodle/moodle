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

namespace Google\Service\Transcoder;

class DashConfig extends \Google\Model
{
  /**
   * The segment reference scheme is not specified.
   */
  public const SEGMENT_REFERENCE_SCHEME_SEGMENT_REFERENCE_SCHEME_UNSPECIFIED = 'SEGMENT_REFERENCE_SCHEME_UNSPECIFIED';
  /**
   * Explicitly lists the URLs of media files for each segment. For example, if
   * SegmentSettings.individual_segments is `true`, then the manifest contains
   * fields similar to the following: ```xml ... ```
   */
  public const SEGMENT_REFERENCE_SCHEME_SEGMENT_LIST = 'SEGMENT_LIST';
  /**
   * SegmentSettings.individual_segments must be set to `true` to use this
   * segment reference scheme. Uses the DASH specification `` tag to determine
   * the URLs of media files for each segment. For example: ```xml ... ```
   */
  public const SEGMENT_REFERENCE_SCHEME_SEGMENT_TEMPLATE_NUMBER = 'SEGMENT_TEMPLATE_NUMBER';
  /**
   * The segment reference scheme for a `DASH` manifest. The default is
   * `SEGMENT_LIST`.
   *
   * @var string
   */
  public $segmentReferenceScheme;

  /**
   * The segment reference scheme for a `DASH` manifest. The default is
   * `SEGMENT_LIST`.
   *
   * Accepted values: SEGMENT_REFERENCE_SCHEME_UNSPECIFIED, SEGMENT_LIST,
   * SEGMENT_TEMPLATE_NUMBER
   *
   * @param self::SEGMENT_REFERENCE_SCHEME_* $segmentReferenceScheme
   */
  public function setSegmentReferenceScheme($segmentReferenceScheme)
  {
    $this->segmentReferenceScheme = $segmentReferenceScheme;
  }
  /**
   * @return self::SEGMENT_REFERENCE_SCHEME_*
   */
  public function getSegmentReferenceScheme()
  {
    return $this->segmentReferenceScheme;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DashConfig::class, 'Google_Service_Transcoder_DashConfig');
