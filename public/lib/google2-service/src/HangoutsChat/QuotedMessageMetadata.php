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

namespace Google\Service\HangoutsChat;

class QuotedMessageMetadata extends \Google\Model
{
  /**
   * Required. The timestamp when the quoted message was created or when the
   * quoted message was last updated. If the message was edited, use this field,
   * `last_update_time`. If the message was never edited, use `create_time`. If
   * `last_update_time` doesn't match the latest version of the quoted message,
   * the request fails.
   *
   * @var string
   */
  public $lastUpdateTime;
  /**
   * Required. Resource name of the message that is quoted. Format:
   * `spaces/{space}/messages/{message}`
   *
   * @var string
   */
  public $name;

  /**
   * Required. The timestamp when the quoted message was created or when the
   * quoted message was last updated. If the message was edited, use this field,
   * `last_update_time`. If the message was never edited, use `create_time`. If
   * `last_update_time` doesn't match the latest version of the quoted message,
   * the request fails.
   *
   * @param string $lastUpdateTime
   */
  public function setLastUpdateTime($lastUpdateTime)
  {
    $this->lastUpdateTime = $lastUpdateTime;
  }
  /**
   * @return string
   */
  public function getLastUpdateTime()
  {
    return $this->lastUpdateTime;
  }
  /**
   * Required. Resource name of the message that is quoted. Format:
   * `spaces/{space}/messages/{message}`
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QuotedMessageMetadata::class, 'Google_Service_HangoutsChat_QuotedMessageMetadata');
