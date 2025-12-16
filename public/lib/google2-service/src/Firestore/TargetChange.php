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

namespace Google\Service\Firestore;

class TargetChange extends \Google\Collection
{
  /**
   * No change has occurred. Used only to send an updated `resume_token`.
   */
  public const TARGET_CHANGE_TYPE_NO_CHANGE = 'NO_CHANGE';
  /**
   * The targets have been added.
   */
  public const TARGET_CHANGE_TYPE_ADD = 'ADD';
  /**
   * The targets have been removed.
   */
  public const TARGET_CHANGE_TYPE_REMOVE = 'REMOVE';
  /**
   * The targets reflect all changes committed before the targets were added to
   * the stream. This will be sent after or with a `read_time` that is greater
   * than or equal to the time at which the targets were added. Listeners can
   * wait for this change if read-after-write semantics are desired.
   */
  public const TARGET_CHANGE_TYPE_CURRENT = 'CURRENT';
  /**
   * The targets have been reset, and a new initial state for the targets will
   * be returned in subsequent changes. After the initial state is complete,
   * `CURRENT` will be returned even if the target was previously indicated to
   * be `CURRENT`.
   */
  public const TARGET_CHANGE_TYPE_RESET = 'RESET';
  protected $collection_key = 'targetIds';
  protected $causeType = Status::class;
  protected $causeDataType = '';
  /**
   * The consistent `read_time` for the given `target_ids` (omitted when the
   * target_ids are not at a consistent snapshot). The stream is guaranteed to
   * send a `read_time` with `target_ids` empty whenever the entire stream
   * reaches a new consistent snapshot. ADD, CURRENT, and RESET messages are
   * guaranteed to (eventually) result in a new consistent snapshot (while
   * NO_CHANGE and REMOVE messages are not). For a given stream, `read_time` is
   * guaranteed to be monotonically increasing.
   *
   * @var string
   */
  public $readTime;
  /**
   * A token that can be used to resume the stream for the given `target_ids`,
   * or all targets if `target_ids` is empty. Not set on every target change.
   *
   * @var string
   */
  public $resumeToken;
  /**
   * The type of change that occurred.
   *
   * @var string
   */
  public $targetChangeType;
  /**
   * The target IDs of targets that have changed. If empty, the change applies
   * to all targets. The order of the target IDs is not defined.
   *
   * @var int[]
   */
  public $targetIds;

  /**
   * The error that resulted in this change, if applicable.
   *
   * @param Status $cause
   */
  public function setCause(Status $cause)
  {
    $this->cause = $cause;
  }
  /**
   * @return Status
   */
  public function getCause()
  {
    return $this->cause;
  }
  /**
   * The consistent `read_time` for the given `target_ids` (omitted when the
   * target_ids are not at a consistent snapshot). The stream is guaranteed to
   * send a `read_time` with `target_ids` empty whenever the entire stream
   * reaches a new consistent snapshot. ADD, CURRENT, and RESET messages are
   * guaranteed to (eventually) result in a new consistent snapshot (while
   * NO_CHANGE and REMOVE messages are not). For a given stream, `read_time` is
   * guaranteed to be monotonically increasing.
   *
   * @param string $readTime
   */
  public function setReadTime($readTime)
  {
    $this->readTime = $readTime;
  }
  /**
   * @return string
   */
  public function getReadTime()
  {
    return $this->readTime;
  }
  /**
   * A token that can be used to resume the stream for the given `target_ids`,
   * or all targets if `target_ids` is empty. Not set on every target change.
   *
   * @param string $resumeToken
   */
  public function setResumeToken($resumeToken)
  {
    $this->resumeToken = $resumeToken;
  }
  /**
   * @return string
   */
  public function getResumeToken()
  {
    return $this->resumeToken;
  }
  /**
   * The type of change that occurred.
   *
   * Accepted values: NO_CHANGE, ADD, REMOVE, CURRENT, RESET
   *
   * @param self::TARGET_CHANGE_TYPE_* $targetChangeType
   */
  public function setTargetChangeType($targetChangeType)
  {
    $this->targetChangeType = $targetChangeType;
  }
  /**
   * @return self::TARGET_CHANGE_TYPE_*
   */
  public function getTargetChangeType()
  {
    return $this->targetChangeType;
  }
  /**
   * The target IDs of targets that have changed. If empty, the change applies
   * to all targets. The order of the target IDs is not defined.
   *
   * @param int[] $targetIds
   */
  public function setTargetIds($targetIds)
  {
    $this->targetIds = $targetIds;
  }
  /**
   * @return int[]
   */
  public function getTargetIds()
  {
    return $this->targetIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TargetChange::class, 'Google_Service_Firestore_TargetChange');
