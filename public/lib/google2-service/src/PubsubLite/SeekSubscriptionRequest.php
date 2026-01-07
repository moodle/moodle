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

namespace Google\Service\PubsubLite;

class SeekSubscriptionRequest extends \Google\Model
{
  /**
   * Unspecified named target. Do not use.
   */
  public const NAMED_TARGET_NAMED_TARGET_UNSPECIFIED = 'NAMED_TARGET_UNSPECIFIED';
  /**
   * Seek to the oldest retained message.
   */
  public const NAMED_TARGET_TAIL = 'TAIL';
  /**
   * Seek past all recently published messages, skipping the entire message
   * backlog.
   */
  public const NAMED_TARGET_HEAD = 'HEAD';
  /**
   * Seek to a named position with respect to the message backlog.
   *
   * @var string
   */
  public $namedTarget;
  protected $timeTargetType = TimeTarget::class;
  protected $timeTargetDataType = '';

  /**
   * Seek to a named position with respect to the message backlog.
   *
   * Accepted values: NAMED_TARGET_UNSPECIFIED, TAIL, HEAD
   *
   * @param self::NAMED_TARGET_* $namedTarget
   */
  public function setNamedTarget($namedTarget)
  {
    $this->namedTarget = $namedTarget;
  }
  /**
   * @return self::NAMED_TARGET_*
   */
  public function getNamedTarget()
  {
    return $this->namedTarget;
  }
  /**
   * Seek to the first message whose publish or event time is greater than or
   * equal to the specified query time. If no such message can be located, will
   * seek to the end of the message backlog.
   *
   * @param TimeTarget $timeTarget
   */
  public function setTimeTarget(TimeTarget $timeTarget)
  {
    $this->timeTarget = $timeTarget;
  }
  /**
   * @return TimeTarget
   */
  public function getTimeTarget()
  {
    return $this->timeTarget;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SeekSubscriptionRequest::class, 'Google_Service_PubsubLite_SeekSubscriptionRequest');
