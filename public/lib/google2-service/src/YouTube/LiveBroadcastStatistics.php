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

class LiveBroadcastStatistics extends \Google\Model
{
  /**
   * The number of viewers currently watching the broadcast. The property and
   * its value will be present if the broadcast has current viewers and the
   * broadcast owner has not hidden the viewcount for the video. Note that
   * YouTube stops tracking the number of concurrent viewers for a broadcast
   * when the broadcast ends. So, this property would not identify the number of
   * viewers watching an archived video of a live broadcast that already ended.
   *
   * @var string
   */
  public $concurrentViewers;

  /**
   * The number of viewers currently watching the broadcast. The property and
   * its value will be present if the broadcast has current viewers and the
   * broadcast owner has not hidden the viewcount for the video. Note that
   * YouTube stops tracking the number of concurrent viewers for a broadcast
   * when the broadcast ends. So, this property would not identify the number of
   * viewers watching an archived video of a live broadcast that already ended.
   *
   * @param string $concurrentViewers
   */
  public function setConcurrentViewers($concurrentViewers)
  {
    $this->concurrentViewers = $concurrentViewers;
  }
  /**
   * @return string
   */
  public function getConcurrentViewers()
  {
    return $this->concurrentViewers;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LiveBroadcastStatistics::class, 'Google_Service_YouTube_LiveBroadcastStatistics');
