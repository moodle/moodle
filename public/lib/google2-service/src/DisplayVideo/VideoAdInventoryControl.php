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

class VideoAdInventoryControl extends \Google\Model
{
  /**
   * Optional. Whether ads can serve as in-feed format.
   *
   * @var bool
   */
  public $allowInFeed;
  /**
   * Optional. Whether ads can serve as in-stream format.
   *
   * @var bool
   */
  public $allowInStream;
  /**
   * Optional. Whether ads can serve as shorts format.
   *
   * @var bool
   */
  public $allowShorts;

  /**
   * Optional. Whether ads can serve as in-feed format.
   *
   * @param bool $allowInFeed
   */
  public function setAllowInFeed($allowInFeed)
  {
    $this->allowInFeed = $allowInFeed;
  }
  /**
   * @return bool
   */
  public function getAllowInFeed()
  {
    return $this->allowInFeed;
  }
  /**
   * Optional. Whether ads can serve as in-stream format.
   *
   * @param bool $allowInStream
   */
  public function setAllowInStream($allowInStream)
  {
    $this->allowInStream = $allowInStream;
  }
  /**
   * @return bool
   */
  public function getAllowInStream()
  {
    return $this->allowInStream;
  }
  /**
   * Optional. Whether ads can serve as shorts format.
   *
   * @param bool $allowShorts
   */
  public function setAllowShorts($allowShorts)
  {
    $this->allowShorts = $allowShorts;
  }
  /**
   * @return bool
   */
  public function getAllowShorts()
  {
    return $this->allowShorts;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VideoAdInventoryControl::class, 'Google_Service_DisplayVideo_VideoAdInventoryControl');
