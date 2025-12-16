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

class SpannerReadOnly extends \Google\Model
{
  /**
   * Executes all reads at a timestamp that is `exact_staleness` old. The
   * timestamp is chosen soon after the read is started. Guarantees that all
   * writes that have committed more than the specified number of seconds ago
   * are visible. Because Cloud Spanner chooses the exact timestamp, this mode
   * works even if the client's local clock is substantially skewed from Cloud
   * Spanner commit timestamps. Useful for reading at nearby replicas without
   * the distributed timestamp negotiation overhead of `max_staleness`.
   *
   * @var string
   */
  public $exactStaleness;
  /**
   * Read data at a timestamp >= `NOW - max_staleness` seconds. Guarantees that
   * all writes that have committed more than the specified number of seconds
   * ago are visible. Because Cloud Spanner chooses the exact timestamp, this
   * mode works even if the client's local clock is substantially skewed from
   * Cloud Spanner commit timestamps. Useful for reading the freshest data
   * available at a nearby replica, while bounding the possible staleness if the
   * local replica has fallen behind. Note that this option can only be used in
   * single-use transactions.
   *
   * @var string
   */
  public $maxStaleness;
  /**
   * Executes all reads at a timestamp >= `min_read_timestamp`. This is useful
   * for requesting fresher data than some previous read, or data that is fresh
   * enough to observe the effects of some previously committed transaction
   * whose timestamp is known. Note that this option can only be used in single-
   * use transactions. A timestamp in RFC3339 UTC \"Zulu\" format, accurate to
   * nanoseconds. Example: `"2014-10-02T15:01:23.045123456Z"`.
   *
   * @var string
   */
  public $minReadTimestamp;
  /**
   * Executes all reads at the given timestamp. Unlike other modes, reads at a
   * specific timestamp are repeatable; the same read at the same timestamp
   * always returns the same data. If the timestamp is in the future, the read
   * is blocked until the specified timestamp, modulo the read's deadline.
   * Useful for large scale consistent reads such as mapreduces, or for
   * coordinating many reads against a consistent snapshot of the data. A
   * timestamp in RFC3339 UTC \"Zulu\" format, accurate to nanoseconds. Example:
   * `"2014-10-02T15:01:23.045123456Z"`.
   *
   * @var string
   */
  public $readTimestamp;
  /**
   * If true, the Cloud Spanner-selected read timestamp is included in the
   * Transaction message that describes the transaction.
   *
   * @var bool
   */
  public $returnReadTimestamp;
  /**
   * Read at a timestamp where all previously committed transactions are
   * visible.
   *
   * @var bool
   */
  public $strong;

  /**
   * Executes all reads at a timestamp that is `exact_staleness` old. The
   * timestamp is chosen soon after the read is started. Guarantees that all
   * writes that have committed more than the specified number of seconds ago
   * are visible. Because Cloud Spanner chooses the exact timestamp, this mode
   * works even if the client's local clock is substantially skewed from Cloud
   * Spanner commit timestamps. Useful for reading at nearby replicas without
   * the distributed timestamp negotiation overhead of `max_staleness`.
   *
   * @param string $exactStaleness
   */
  public function setExactStaleness($exactStaleness)
  {
    $this->exactStaleness = $exactStaleness;
  }
  /**
   * @return string
   */
  public function getExactStaleness()
  {
    return $this->exactStaleness;
  }
  /**
   * Read data at a timestamp >= `NOW - max_staleness` seconds. Guarantees that
   * all writes that have committed more than the specified number of seconds
   * ago are visible. Because Cloud Spanner chooses the exact timestamp, this
   * mode works even if the client's local clock is substantially skewed from
   * Cloud Spanner commit timestamps. Useful for reading the freshest data
   * available at a nearby replica, while bounding the possible staleness if the
   * local replica has fallen behind. Note that this option can only be used in
   * single-use transactions.
   *
   * @param string $maxStaleness
   */
  public function setMaxStaleness($maxStaleness)
  {
    $this->maxStaleness = $maxStaleness;
  }
  /**
   * @return string
   */
  public function getMaxStaleness()
  {
    return $this->maxStaleness;
  }
  /**
   * Executes all reads at a timestamp >= `min_read_timestamp`. This is useful
   * for requesting fresher data than some previous read, or data that is fresh
   * enough to observe the effects of some previously committed transaction
   * whose timestamp is known. Note that this option can only be used in single-
   * use transactions. A timestamp in RFC3339 UTC \"Zulu\" format, accurate to
   * nanoseconds. Example: `"2014-10-02T15:01:23.045123456Z"`.
   *
   * @param string $minReadTimestamp
   */
  public function setMinReadTimestamp($minReadTimestamp)
  {
    $this->minReadTimestamp = $minReadTimestamp;
  }
  /**
   * @return string
   */
  public function getMinReadTimestamp()
  {
    return $this->minReadTimestamp;
  }
  /**
   * Executes all reads at the given timestamp. Unlike other modes, reads at a
   * specific timestamp are repeatable; the same read at the same timestamp
   * always returns the same data. If the timestamp is in the future, the read
   * is blocked until the specified timestamp, modulo the read's deadline.
   * Useful for large scale consistent reads such as mapreduces, or for
   * coordinating many reads against a consistent snapshot of the data. A
   * timestamp in RFC3339 UTC \"Zulu\" format, accurate to nanoseconds. Example:
   * `"2014-10-02T15:01:23.045123456Z"`.
   *
   * @param string $readTimestamp
   */
  public function setReadTimestamp($readTimestamp)
  {
    $this->readTimestamp = $readTimestamp;
  }
  /**
   * @return string
   */
  public function getReadTimestamp()
  {
    return $this->readTimestamp;
  }
  /**
   * If true, the Cloud Spanner-selected read timestamp is included in the
   * Transaction message that describes the transaction.
   *
   * @param bool $returnReadTimestamp
   */
  public function setReturnReadTimestamp($returnReadTimestamp)
  {
    $this->returnReadTimestamp = $returnReadTimestamp;
  }
  /**
   * @return bool
   */
  public function getReturnReadTimestamp()
  {
    return $this->returnReadTimestamp;
  }
  /**
   * Read at a timestamp where all previously committed transactions are
   * visible.
   *
   * @param bool $strong
   */
  public function setStrong($strong)
  {
    $this->strong = $strong;
  }
  /**
   * @return bool
   */
  public function getStrong()
  {
    return $this->strong;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SpannerReadOnly::class, 'Google_Service_Spanner_SpannerReadOnly');
