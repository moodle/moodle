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

class NameAndKind extends \Google\Model
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
   * Counter aggregation kind.
   *
   * @var string
   */
  public $kind;
  /**
   * Name of the counter.
   *
   * @var string
   */
  public $name;

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
   * Name of the counter.
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
class_alias(NameAndKind::class, 'Google_Service_Dataflow_NameAndKind');
