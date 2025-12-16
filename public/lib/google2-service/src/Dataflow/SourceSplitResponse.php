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

class SourceSplitResponse extends \Google\Collection
{
  /**
   * The source split outcome is unknown, or unspecified.
   */
  public const OUTCOME_SOURCE_SPLIT_OUTCOME_UNKNOWN = 'SOURCE_SPLIT_OUTCOME_UNKNOWN';
  /**
   * The current source should be processed "as is" without splitting.
   */
  public const OUTCOME_SOURCE_SPLIT_OUTCOME_USE_CURRENT = 'SOURCE_SPLIT_OUTCOME_USE_CURRENT';
  /**
   * Splitting produced a list of bundles.
   */
  public const OUTCOME_SOURCE_SPLIT_OUTCOME_SPLITTING_HAPPENED = 'SOURCE_SPLIT_OUTCOME_SPLITTING_HAPPENED';
  protected $collection_key = 'shards';
  protected $bundlesType = DerivedSource::class;
  protected $bundlesDataType = 'array';
  /**
   * Indicates whether splitting happened and produced a list of bundles. If
   * this is USE_CURRENT_SOURCE_AS_IS, the current source should be processed
   * "as is" without splitting. "bundles" is ignored in this case. If this is
   * SPLITTING_HAPPENED, then "bundles" contains a list of bundles into which
   * the source was split.
   *
   * @var string
   */
  public $outcome;
  protected $shardsType = SourceSplitShard::class;
  protected $shardsDataType = 'array';

  /**
   * If outcome is SPLITTING_HAPPENED, then this is a list of bundles into which
   * the source was split. Otherwise this field is ignored. This list can be
   * empty, which means the source represents an empty input.
   *
   * @param DerivedSource[] $bundles
   */
  public function setBundles($bundles)
  {
    $this->bundles = $bundles;
  }
  /**
   * @return DerivedSource[]
   */
  public function getBundles()
  {
    return $this->bundles;
  }
  /**
   * Indicates whether splitting happened and produced a list of bundles. If
   * this is USE_CURRENT_SOURCE_AS_IS, the current source should be processed
   * "as is" without splitting. "bundles" is ignored in this case. If this is
   * SPLITTING_HAPPENED, then "bundles" contains a list of bundles into which
   * the source was split.
   *
   * Accepted values: SOURCE_SPLIT_OUTCOME_UNKNOWN,
   * SOURCE_SPLIT_OUTCOME_USE_CURRENT, SOURCE_SPLIT_OUTCOME_SPLITTING_HAPPENED
   *
   * @param self::OUTCOME_* $outcome
   */
  public function setOutcome($outcome)
  {
    $this->outcome = $outcome;
  }
  /**
   * @return self::OUTCOME_*
   */
  public function getOutcome()
  {
    return $this->outcome;
  }
  /**
   * DEPRECATED in favor of bundles.
   *
   * @deprecated
   * @param SourceSplitShard[] $shards
   */
  public function setShards($shards)
  {
    $this->shards = $shards;
  }
  /**
   * @deprecated
   * @return SourceSplitShard[]
   */
  public function getShards()
  {
    return $this->shards;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SourceSplitResponse::class, 'Google_Service_Dataflow_SourceSplitResponse');
