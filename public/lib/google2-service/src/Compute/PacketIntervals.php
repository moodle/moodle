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

namespace Google\Service\Compute;

class PacketIntervals extends \Google\Model
{
  public const DURATION_DURATION_UNSPECIFIED = 'DURATION_UNSPECIFIED';
  public const DURATION_HOUR = 'HOUR';
  /**
   * From BfdSession object creation time.
   */
  public const DURATION_MAX = 'MAX';
  public const DURATION_MINUTE = 'MINUTE';
  /**
   * Only applies to Echo packets. This shows the intervals between sending and
   * receiving the same packet.
   */
  public const TYPE_LOOPBACK = 'LOOPBACK';
  /**
   * Intervals between received packets.
   */
  public const TYPE_RECEIVE = 'RECEIVE';
  /**
   * Intervals between transmitted packets.
   */
  public const TYPE_TRANSMIT = 'TRANSMIT';
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Average observed inter-packet interval in milliseconds.
   *
   * @var string
   */
  public $avgMs;
  /**
   * From how long ago in the past these intervals were observed.
   *
   * @var string
   */
  public $duration;
  /**
   * Maximum observed inter-packet interval in milliseconds.
   *
   * @var string
   */
  public $maxMs;
  /**
   * Minimum observed inter-packet interval in milliseconds.
   *
   * @var string
   */
  public $minMs;
  /**
   * Number of inter-packet intervals from which these statistics were derived.
   *
   * @var string
   */
  public $numIntervals;
  /**
   * The type of packets for which inter-packet intervals were computed.
   *
   * @var string
   */
  public $type;

  /**
   * Average observed inter-packet interval in milliseconds.
   *
   * @param string $avgMs
   */
  public function setAvgMs($avgMs)
  {
    $this->avgMs = $avgMs;
  }
  /**
   * @return string
   */
  public function getAvgMs()
  {
    return $this->avgMs;
  }
  /**
   * From how long ago in the past these intervals were observed.
   *
   * Accepted values: DURATION_UNSPECIFIED, HOUR, MAX, MINUTE
   *
   * @param self::DURATION_* $duration
   */
  public function setDuration($duration)
  {
    $this->duration = $duration;
  }
  /**
   * @return self::DURATION_*
   */
  public function getDuration()
  {
    return $this->duration;
  }
  /**
   * Maximum observed inter-packet interval in milliseconds.
   *
   * @param string $maxMs
   */
  public function setMaxMs($maxMs)
  {
    $this->maxMs = $maxMs;
  }
  /**
   * @return string
   */
  public function getMaxMs()
  {
    return $this->maxMs;
  }
  /**
   * Minimum observed inter-packet interval in milliseconds.
   *
   * @param string $minMs
   */
  public function setMinMs($minMs)
  {
    $this->minMs = $minMs;
  }
  /**
   * @return string
   */
  public function getMinMs()
  {
    return $this->minMs;
  }
  /**
   * Number of inter-packet intervals from which these statistics were derived.
   *
   * @param string $numIntervals
   */
  public function setNumIntervals($numIntervals)
  {
    $this->numIntervals = $numIntervals;
  }
  /**
   * @return string
   */
  public function getNumIntervals()
  {
    return $this->numIntervals;
  }
  /**
   * The type of packets for which inter-packet intervals were computed.
   *
   * Accepted values: LOOPBACK, RECEIVE, TRANSMIT, TYPE_UNSPECIFIED
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PacketIntervals::class, 'Google_Service_Compute_PacketIntervals');
