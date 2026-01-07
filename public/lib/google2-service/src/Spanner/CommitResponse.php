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

namespace Google\Service\Spanner;

class CommitResponse extends \Google\Model
{
  protected $commitStatsType = CommitStats::class;
  protected $commitStatsDataType = '';
  /**
   * The Cloud Spanner timestamp at which the transaction committed.
   *
   * @var string
   */
  public $commitTimestamp;
  protected $precommitTokenType = MultiplexedSessionPrecommitToken::class;
  protected $precommitTokenDataType = '';
  /**
   * If `TransactionOptions.isolation_level` is set to
   * `IsolationLevel.REPEATABLE_READ`, then the snapshot timestamp is the
   * timestamp at which all reads in the transaction ran. This timestamp is
   * never returned.
   *
   * @var string
   */
  public $snapshotTimestamp;

  /**
   * The statistics about this `Commit`. Not returned by default. For more
   * information, see CommitRequest.return_commit_stats.
   *
   * @param CommitStats $commitStats
   */
  public function setCommitStats(CommitStats $commitStats)
  {
    $this->commitStats = $commitStats;
  }
  /**
   * @return CommitStats
   */
  public function getCommitStats()
  {
    return $this->commitStats;
  }
  /**
   * The Cloud Spanner timestamp at which the transaction committed.
   *
   * @param string $commitTimestamp
   */
  public function setCommitTimestamp($commitTimestamp)
  {
    $this->commitTimestamp = $commitTimestamp;
  }
  /**
   * @return string
   */
  public function getCommitTimestamp()
  {
    return $this->commitTimestamp;
  }
  /**
   * If specified, transaction has not committed yet. You must retry the commit
   * with the new precommit token.
   *
   * @param MultiplexedSessionPrecommitToken $precommitToken
   */
  public function setPrecommitToken(MultiplexedSessionPrecommitToken $precommitToken)
  {
    $this->precommitToken = $precommitToken;
  }
  /**
   * @return MultiplexedSessionPrecommitToken
   */
  public function getPrecommitToken()
  {
    return $this->precommitToken;
  }
  /**
   * If `TransactionOptions.isolation_level` is set to
   * `IsolationLevel.REPEATABLE_READ`, then the snapshot timestamp is the
   * timestamp at which all reads in the transaction ran. This timestamp is
   * never returned.
   *
   * @param string $snapshotTimestamp
   */
  public function setSnapshotTimestamp($snapshotTimestamp)
  {
    $this->snapshotTimestamp = $snapshotTimestamp;
  }
  /**
   * @return string
   */
  public function getSnapshotTimestamp()
  {
    return $this->snapshotTimestamp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CommitResponse::class, 'Google_Service_Spanner_CommitResponse');
