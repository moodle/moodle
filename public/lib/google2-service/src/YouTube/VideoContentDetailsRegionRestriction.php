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

namespace Google\Service\YouTube;

class VideoContentDetailsRegionRestriction extends \Google\Collection
{
  protected $collection_key = 'blocked';
  /**
   * A list of region codes that identify countries where the video is viewable.
   * If this property is present and a country is not listed in its value, then
   * the video is blocked from appearing in that country. If this property is
   * present and contains an empty list, the video is blocked in all countries.
   *
   * @var string[]
   */
  public $allowed;
  /**
   * A list of region codes that identify countries where the video is blocked.
   * If this property is present and a country is not listed in its value, then
   * the video is viewable in that country. If this property is present and
   * contains an empty list, the video is viewable in all countries.
   *
   * @var string[]
   */
  public $blocked;

  /**
   * A list of region codes that identify countries where the video is viewable.
   * If this property is present and a country is not listed in its value, then
   * the video is blocked from appearing in that country. If this property is
   * present and contains an empty list, the video is blocked in all countries.
   *
   * @param string[] $allowed
   */
  public function setAllowed($allowed)
  {
    $this->allowed = $allowed;
  }
  /**
   * @return string[]
   */
  public function getAllowed()
  {
    return $this->allowed;
  }
  /**
   * A list of region codes that identify countries where the video is blocked.
   * If this property is present and a country is not listed in its value, then
   * the video is viewable in that country. If this property is present and
   * contains an empty list, the video is viewable in all countries.
   *
   * @param string[] $blocked
   */
  public function setBlocked($blocked)
  {
    $this->blocked = $blocked;
  }
  /**
   * @return string[]
   */
  public function getBlocked()
  {
    return $this->blocked;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VideoContentDetailsRegionRestriction::class, 'Google_Service_YouTube_VideoContentDetailsRegionRestriction');
