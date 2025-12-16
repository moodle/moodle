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

namespace Google\Service\Networkconnectivity;

class AllocationOptions extends \Google\Model
{
  /**
   * Unspecified is the only valid option when the range is specified explicitly
   * by ip_cidr_range field. Otherwise unspefified means using the default
   * strategy.
   */
  public const ALLOCATION_STRATEGY_ALLOCATION_STRATEGY_UNSPECIFIED = 'ALLOCATION_STRATEGY_UNSPECIFIED';
  /**
   * Random strategy, the legacy algorithm, used for backwards compatibility.
   * This allocation strategy remains efficient in the case of concurrent
   * allocation requests in the same peered network space and doesn't require
   * providing the level of concurrency in an explicit parameter, but it is
   * prone to fragmenting available address space.
   */
  public const ALLOCATION_STRATEGY_RANDOM = 'RANDOM';
  /**
   * Pick the first available address range. This strategy is deterministic and
   * the result is easy to predict.
   */
  public const ALLOCATION_STRATEGY_FIRST_AVAILABLE = 'FIRST_AVAILABLE';
  /**
   * Pick an arbitrary range out of the first N available ones. The N will be
   * set in the first_available_ranges_lookup_size field. This strategy should
   * be used when concurrent allocation requests are made in the same space of
   * peered networks while the fragmentation of the addrress space is reduced.
   */
  public const ALLOCATION_STRATEGY_RANDOM_FIRST_N_AVAILABLE = 'RANDOM_FIRST_N_AVAILABLE';
  /**
   * Pick the smallest but fitting available range. This deterministic strategy
   * minimizes fragmentation of the address space.
   */
  public const ALLOCATION_STRATEGY_FIRST_SMALLEST_FITTING = 'FIRST_SMALLEST_FITTING';
  /**
   * Optional. Allocation strategy Not setting this field when the allocation is
   * requested means an implementation defined strategy is used.
   *
   * @var string
   */
  public $allocationStrategy;
  /**
   * Optional. This field must be set only when allocation_strategy is set to
   * RANDOM_FIRST_N_AVAILABLE. The value should be the maximum expected
   * parallelism of range creation requests issued to the same space of peered
   * netwroks.
   *
   * @var int
   */
  public $firstAvailableRangesLookupSize;

  /**
   * Optional. Allocation strategy Not setting this field when the allocation is
   * requested means an implementation defined strategy is used.
   *
   * Accepted values: ALLOCATION_STRATEGY_UNSPECIFIED, RANDOM, FIRST_AVAILABLE,
   * RANDOM_FIRST_N_AVAILABLE, FIRST_SMALLEST_FITTING
   *
   * @param self::ALLOCATION_STRATEGY_* $allocationStrategy
   */
  public function setAllocationStrategy($allocationStrategy)
  {
    $this->allocationStrategy = $allocationStrategy;
  }
  /**
   * @return self::ALLOCATION_STRATEGY_*
   */
  public function getAllocationStrategy()
  {
    return $this->allocationStrategy;
  }
  /**
   * Optional. This field must be set only when allocation_strategy is set to
   * RANDOM_FIRST_N_AVAILABLE. The value should be the maximum expected
   * parallelism of range creation requests issued to the same space of peered
   * netwroks.
   *
   * @param int $firstAvailableRangesLookupSize
   */
  public function setFirstAvailableRangesLookupSize($firstAvailableRangesLookupSize)
  {
    $this->firstAvailableRangesLookupSize = $firstAvailableRangesLookupSize;
  }
  /**
   * @return int
   */
  public function getFirstAvailableRangesLookupSize()
  {
    return $this->firstAvailableRangesLookupSize;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AllocationOptions::class, 'Google_Service_Networkconnectivity_AllocationOptions');
