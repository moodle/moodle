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

namespace Google\Service\Integrations;

class EnterpriseCrmFrontendsEventbusProtoEventExecutionInfoReplayInfo extends \Google\Collection
{
  public const REPLAY_MODE_REPLAY_MODE_UNSPECIFIED = 'REPLAY_MODE_UNSPECIFIED';
  /**
   * Replay the original execution from the beginning.
   */
  public const REPLAY_MODE_REPLAY_MODE_FROM_BEGINNING = 'REPLAY_MODE_FROM_BEGINNING';
  /**
   * Replay the execution from the first failed task.
   */
  public const REPLAY_MODE_REPLAY_MODE_POINT_OF_FAILURE = 'REPLAY_MODE_POINT_OF_FAILURE';
  protected $collection_key = 'replayedExecutionInfoIds';
  /**
   * If this execution is a replay of another execution, then this field
   * contains the original execution id.
   *
   * @var string
   */
  public $originalExecutionInfoId;
  /**
   * Replay mode for the execution
   *
   * @var string
   */
  public $replayMode;
  /**
   * reason for replay
   *
   * @var string
   */
  public $replayReason;
  /**
   * If this execution has been replayed, then this field contains the execution
   * ids of the replayed executions.
   *
   * @var string[]
   */
  public $replayedExecutionInfoIds;

  /**
   * If this execution is a replay of another execution, then this field
   * contains the original execution id.
   *
   * @param string $originalExecutionInfoId
   */
  public function setOriginalExecutionInfoId($originalExecutionInfoId)
  {
    $this->originalExecutionInfoId = $originalExecutionInfoId;
  }
  /**
   * @return string
   */
  public function getOriginalExecutionInfoId()
  {
    return $this->originalExecutionInfoId;
  }
  /**
   * Replay mode for the execution
   *
   * Accepted values: REPLAY_MODE_UNSPECIFIED, REPLAY_MODE_FROM_BEGINNING,
   * REPLAY_MODE_POINT_OF_FAILURE
   *
   * @param self::REPLAY_MODE_* $replayMode
   */
  public function setReplayMode($replayMode)
  {
    $this->replayMode = $replayMode;
  }
  /**
   * @return self::REPLAY_MODE_*
   */
  public function getReplayMode()
  {
    return $this->replayMode;
  }
  /**
   * reason for replay
   *
   * @param string $replayReason
   */
  public function setReplayReason($replayReason)
  {
    $this->replayReason = $replayReason;
  }
  /**
   * @return string
   */
  public function getReplayReason()
  {
    return $this->replayReason;
  }
  /**
   * If this execution has been replayed, then this field contains the execution
   * ids of the replayed executions.
   *
   * @param string[] $replayedExecutionInfoIds
   */
  public function setReplayedExecutionInfoIds($replayedExecutionInfoIds)
  {
    $this->replayedExecutionInfoIds = $replayedExecutionInfoIds;
  }
  /**
   * @return string[]
   */
  public function getReplayedExecutionInfoIds()
  {
    return $this->replayedExecutionInfoIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmFrontendsEventbusProtoEventExecutionInfoReplayInfo::class, 'Google_Service_Integrations_EnterpriseCrmFrontendsEventbusProtoEventExecutionInfoReplayInfo');
