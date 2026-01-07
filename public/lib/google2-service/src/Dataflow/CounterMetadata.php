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

namespace Google\Service\Dataflow;

class CounterMetadata extends \Google\Model
{
  /**
   * Counter aggregation kind was not set.
   */
  public const KIND_INVALID = 'INVALID';
  /**
   * Aggregated value is the sum of all contributed values.
   */
  public const KIND_SUM = 'SUM';
  /**
   * Aggregated value is the max of all contributed values.
   */
  public const KIND_MAX = 'MAX';
  /**
   * Aggregated value is the min of all contributed values.
   */
  public const KIND_MIN = 'MIN';
  /**
   * Aggregated value is the mean of all contributed values.
   */
  public const KIND_MEAN = 'MEAN';
  /**
   * Aggregated value represents the logical 'or' of all contributed values.
   */
  public const KIND_OR = 'OR';
  /**
   * Aggregated value represents the logical 'and' of all contributed values.
   */
  public const KIND_AND = 'AND';
  /**
   * Aggregated value is a set of unique contributed values.
   */
  public const KIND_SET = 'SET';
  /**
   * Aggregated value captures statistics about a distribution.
   */
  public const KIND_DISTRIBUTION = 'DISTRIBUTION';
  /**
   * Aggregated value tracks the latest value of a variable.
   */
  public const KIND_LATEST_VALUE = 'LATEST_VALUE';
  /**
   * Counter returns a value in bytes.
   */
  public const STANDARD_UNITS_BYTES = 'BYTES';
  /**
   * Counter returns a value in bytes per second.
   */
  public const STANDARD_UNITS_BYTES_PER_SEC = 'BYTES_PER_SEC';
  /**
   * Counter returns a value in milliseconds.
   */
  public const STANDARD_UNITS_MILLISECONDS = 'MILLISECONDS';
  /**
   * Counter returns a value in microseconds.
   */
  public const STANDARD_UNITS_MICROSECONDS = 'MICROSECONDS';
  /**
   * Counter returns a value in nanoseconds.
   */
  public const STANDARD_UNITS_NANOSECONDS = 'NANOSECONDS';
  /**
   * Counter returns a timestamp in milliseconds.
   */
  public const STANDARD_UNITS_TIMESTAMP_MSEC = 'TIMESTAMP_MSEC';
  /**
   * Counter returns a timestamp in microseconds.
   */
  public const STANDARD_UNITS_TIMESTAMP_USEC = 'TIMESTAMP_USEC';
  /**
   * Counter returns a timestamp in nanoseconds.
   */
  public const STANDARD_UNITS_TIMESTAMP_NSEC = 'TIMESTAMP_NSEC';
  /**
   * Human-readable description of the counter semantics.
   *
   * @var string
   */
  public $description;
  /**
   * Counter aggregation kind.
   *
   * @var string
   */
  public $kind;
  /**
   * A string referring to the unit type.
   *
   * @var string
   */
  public $otherUnits;
  /**
   * System defined Units, see above enum.
   *
   * @var string
   */
  public $standardUnits;

  /**
   * Human-readable description of the counter semantics.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Counter aggregation kind.
   *
   * Accepted values: INVALID, SUM, MAX, MIN, MEAN, OR, AND, SET, DISTRIBUTION,
   * LATEST_VALUE
   *
   * @param self::KIND_* $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return self::KIND_*
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * A string referring to the unit type.
   *
   * @param string $otherUnits
   */
  public function setOtherUnits($otherUnits)
  {
    $this->otherUnits = $otherUnits;
  }
  /**
   * @return string
   */
  public function getOtherUnits()
  {
    return $this->otherUnits;
  }
  /**
   * System defined Units, see above enum.
   *
   * Accepted values: BYTES, BYTES_PER_SEC, MILLISECONDS, MICROSECONDS,
   * NANOSECONDS, TIMESTAMP_MSEC, TIMESTAMP_USEC, TIMESTAMP_NSEC
   *
   * @param self::STANDARD_UNITS_* $standardUnits
   */
  public function setStandardUnits($standardUnits)
  {
    $this->standardUnits = $standardUnits;
  }
  /**
   * @return self::STANDARD_UNITS_*
   */
  public function getStandardUnits()
  {
    return $this->standardUnits;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CounterMetadata::class, 'Google_Service_Dataflow_CounterMetadata');
