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

namespace Google\Service\Gmail;

class WatchResponse extends \Google\Model
{
  /**
   * When Gmail will stop sending notifications for mailbox updates (epoch
   * millis). Call `watch` again before this time to renew the watch.
   *
   * @var string
   */
  public $expiration;
  /**
   * The ID of the mailbox's current history record.
   *
   * @var string
   */
  public $historyId;

  /**
   * When Gmail will stop sending notifications for mailbox updates (epoch
   * millis). Call `watch` again before this time to renew the watch.
   *
   * @param string $expiration
   */
  public function setExpiration($expiration)
  {
    $this->expiration = $expiration;
  }
  /**
   * @return string
   */
  public function getExpiration()
  {
    return $this->expiration;
  }
  /**
   * The ID of the mailbox's current history record.
   *
   * @param string $historyId
   */
  public function setHistoryId($historyId)
  {
    $this->historyId = $historyId;
  }
  /**
   * @return string
   */
  public function getHistoryId()
  {
    return $this->historyId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WatchResponse::class, 'Google_Service_Gmail_WatchResponse');
