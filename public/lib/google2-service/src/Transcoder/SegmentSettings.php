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

class SegmentSettings extends \Google\Model
{
  /**
   * Required. Create an individual segment file. The default is `false`.
   *
   * @var bool
   */
  public $individualSegments;
  /**
   * Duration of the segments in seconds. The default is `6.0s`. Note that
   * `segmentDuration` must be greater than or equal to
   * [`gopDuration`](#videostream), and `segmentDuration` must be divisible by
   * [`gopDuration`](#videostream).
   *
   * @var string
   */
  public $segmentDuration;

  /**
   * Required. Create an individual segment file. The default is `false`.
   *
   * @param bool $individualSegments
   */
  public function setIndividualSegments($individualSegments)
  {
    $this->individualSegments = $individualSegments;
  }
  /**
   * @return bool
   */
  public function getIndividualSegments()
  {
    return $this->individualSegments;
  }
  /**
   * Duration of the segments in seconds. The default is `6.0s`. Note that
   * `segmentDuration` must be greater than or equal to
   * [`gopDuration`](#videostream), and `segmentDuration` must be divisible by
   * [`gopDuration`](#videostream).
   *
   * @param string $segmentDuration
   */
  public function setSegmentDuration($segmentDuration)
  {
    $this->segmentDuration = $segmentDuration;
  }
  /**
   * @return string
   */
  public function getSegmentDuration()
  {
    return $this->segmentDuration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SegmentSettings::class, 'Google_Service_Transcoder_SegmentSettings');
